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
    ) {}

    public function index()
    {
        $defaultTasks = $this->defaultTaskService->getAll();
        $roles = \App\Models\Role::where('name', '!=', 'admin')->orderBy('id')->get();
        return view('admin.default-tasks.index', compact('defaultTasks', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'target_role' => ['required', 'exists:roles,name'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $task = $this->defaultTaskService->create($validated);
        $this->logActivity('task.created', 'Tugas', "Menambahkan tugas default '{$validated['title']}' untuk role {$validated['target_role']}", $task);

        return redirect()->route('admin.default-tasks.index')
            ->with('success', 'Default task berhasil ditambahkan.');
    }

    public function update(Request $request, DefaultTask $defaultTask)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'target_role' => ['required', 'exists:roles,name'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

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