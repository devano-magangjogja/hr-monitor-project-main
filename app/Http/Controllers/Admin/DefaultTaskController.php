<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\DefaultTask;
use App\Services\DefaultTaskService;
use Illuminate\Http\Request;

class DefaultTaskController extends Controller
{
    use LogsActivity;

    public function __construct(
        protected DefaultTaskService $defaultTaskService
    ) {
    }

    public function index(Request $request)
    {
        $defaultTasks = $this->defaultTaskService->getAll();
        $roles = \App\Models\Role::where('name', '!=', 'admin')->orderBy('id')->get();

        // Ambil semua user non-admin, dikelompokkan per role
        $usersByRole = \App\Models\User::with('roleModel')
            ->whereHas('roleModel', fn($q) => $q->where('name', '!=', 'admin'))
            ->orderBy('name')
            ->get()
            ->groupBy(fn($u) => $u->roleModel?->name ?? 'unknown');
        $usersById = $usersByRole->flatten()->keyBy('id');

        // Pencarian: judul, deskripsi, target role, atau nama user terpilih
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $needle = mb_strtolower($search);
            $defaultTasks = $defaultTasks->filter(function ($task) use ($needle, $roles, $usersById) {
                $roleLabel = $roles->firstWhere('name', $task->target_role)?->label ?? $task->target_role;
                $haystacks = [$task->title, $task->description, $roleLabel];
                foreach ((array) ($task->assigned_user_ids ?? []) as $uid) {
                    $haystacks[] = $usersById->get($uid)?->name;
                }
                foreach ($haystacks as $v) {
                    if ($v !== null && str_contains(mb_strtolower((string) $v), $needle)) {
                        return true;
                    }
                }
                return false;
            })->values();
        }

        return view('admin.default-tasks.index', compact('defaultTasks', 'roles', 'usersByRole', 'usersById', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'target_role' => ['required', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
            'proof_requirement' => ['nullable', 'string', 'in:required,optional,none'],
            'assign_type' => ['required', 'in:all,specific'],
            'assigned_user_ids' => ['nullable', 'required_if:assign_type,specific', 'array', 'min:1'],
            'assigned_user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $validated['proof_requirement'] = $validated['proof_requirement'] ?? 'none';

        // Pastikan user yang dipilih memang milik role yang dipilih
        if (!empty($validated['assigned_user_ids'])) {
            $validUserIds = \App\Models\User::whereIn('id', $validated['assigned_user_ids'])
                ->whereHas('roleModel', fn($q) => $q->where('name', $validated['target_role']))
                ->pluck('id')
                ->toArray();
            $validated['assigned_user_ids'] = $validUserIds ?: null;
        } else {
            $validated['assigned_user_ids'] = null; // artinya semua anggota role
        }

        $task = $this->defaultTaskService->create($validated);
        $this->logActivity('task.created', 'Tugas', "Menambahkan tugas default '{$validated['title']}' untuk role {$validated['target_role']}", $task);

        return redirect()->route('admin.default-tasks.index')
            ->with('success', 'Default task berhasil ditambahkan.');
    }

    public function update(Request $request, DefaultTask $defaultTask)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'target_role' => ['required', 'exists:roles,name'],
            'is_active' => ['nullable', 'boolean'],
            'proof_requirement' => ['nullable', 'string', 'in:required,optional,none'],
            'assign_type' => ['required', 'in:all,specific'],
            'assigned_user_ids' => ['nullable', 'required_if:assign_type,specific', 'array', 'min:1'],
            'assigned_user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $validated['proof_requirement'] = $validated['proof_requirement'] ?? 'none';

        if (!empty($validated['assigned_user_ids'])) {
            $validUserIds = \App\Models\User::whereIn('id', $validated['assigned_user_ids'])
                ->whereHas('roleModel', fn($q) => $q->where('name', $validated['target_role']))
                ->pluck('id')
                ->toArray();
            $validated['assigned_user_ids'] = $validUserIds ?: null;
        } else {
            $validated['assigned_user_ids'] = null;
        }

        $this->defaultTaskService->update($defaultTask, $validated);
        $this->logActivity('task.updated', 'Tugas', "Memperbarui tugas default '{$defaultTask->title}'", $defaultTask);

        return redirect()->route('admin.default-tasks.index')
            ->with('success', 'Default task berhasil diperbarui.');
    }

    public function destroy(DefaultTask $defaultTask)
    {
        $title = $defaultTask->title;
        $this->defaultTaskService->delete($defaultTask);
        $this->logActivity('task.deleted', 'Tugas', "Menghapus tugas default '{$title}'");

        return redirect()->route('admin.default-tasks.index')
            ->with('success', 'Default task berhasil dihapus.');
    }
}