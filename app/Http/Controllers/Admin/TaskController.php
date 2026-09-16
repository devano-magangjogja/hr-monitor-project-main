<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use App\Models\SosmedTask;
use App\Models\SosmedAccount;
use App\Models\User;

class TaskController extends Controller
{
    use LogsActivity;

    public function __construct(
        protected TaskService $taskService
    ) {
    }

    // ── Halaman 1: Tugas dari Admin ──────────────────────

    public function index()
    {
        $tasks = $this->taskService->getTasksCreatedByAdmin();
        $assignableUsers = $this->taskService->getAssignableUsers();
        return view('admin.tasks.index', compact('tasks', 'assignableUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'kantor' => ['nullable', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4,Kantor 5,Kantor 6,Kantor 7,Kantor 8,Kantor 9,Kantor 10'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        try {
            $task = $this->taskService->createAssignedTask($validated);
            $this->logActivity('task.created', 'Tugas', "Membuat tugas '{$validated['title']}' dan dikirim ke penerima", $task);
            return redirect()->route('admin.tasks.index')
                ->with('success', 'Tugas berhasil dibuat dan dikirim ke penerima.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'kantor' => ['nullable', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4,Kantor 5,Kantor 6,Kantor 7,Kantor 8,Kantor 9,Kantor 10'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        try {
            $this->taskService->updateTask($task, $validated);
            $this->logActivity('task.updated', 'Tugas', "Memperbarui tugas '{$task->title}'", $task);
            return back()->with('success', 'Tugas berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal memperbarui tugas.');
        }
    }

    public function destroy(Task $task)
    {
        try {
            $title = $task->title;
            $this->taskService->deleteTask($task);
            $this->logActivity('task.deleted', 'Tugas', "Menghapus tugas '{$title}'");
            return back()->with('success', 'Tugas berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menghapus tugas.');
        }
    }

    // ── Halaman 2: Pantau HR Staff ───────────────────────

    public function staffTasks()
    {
        $tasks = $this->taskService->getTasksByStaff();
        return view('admin.tasks.staff', compact('tasks'));
    }

    // ── Halaman 3: Pantau HR Assistant ───────────────────

    public function assistantTasks()
    {
        $tasks = $this->taskService->getAllTasksForAssistant();
        $assignableUsers = \App\Models\User::where('role', 'hr_assistant')->where('is_active', 1)->get();
        $kantorList = ['Kantor 1', 'Kantor 2', 'Kantor 3', 'Kantor 4'];
        return view('admin.tasks.assistant', compact('tasks', 'assignableUsers', 'kantorList'));
    }

    // ── Halaman 4: Pantau CS ─────────────────────────────

    public function csTasks()
    {
        $tasks = $this->taskService->getAllTasksForRole('cs');
        return view('admin.tasks.cs', compact('tasks'));
    }

    // ── Halaman 9: Pantau PM ─────────────────────
    public function pmTasks()
    {
        $tasks = $this->taskService->getAllTasksForRole('pm');
        return view('admin.tasks.pm', compact('tasks'));
    }

    // ── Halaman 5: Pantau OB ─────────────────────────────

    public function obTasks()
    {
        $tasks = $this->taskService->getAllTasksForRole('ob');
        return view('admin.tasks.ob', compact('tasks'));
    }

    // ── Halaman 6: Pantau Programmer ─────────────────────
    public function programmerTasks()
    {
        $tasks = $this->taskService->getAllTasksForRole('programmer');
        return view('admin.tasks.programmer', compact('tasks'));
    }

    // ── Halaman 7: Pantau DG ─────────────────────
    public function dgTasks()
    {
        $tasks = $this->taskService->getAllTasksForRole('dg');
        return view('admin.tasks.dg', compact('tasks'));
    }

    // ── Halaman 8: Pantau VG ─────────────────────
    public function vgTasks()
    {
        $tasks = $this->taskService->getAllTasksForRole('vg');
        return view('admin.tasks.vg', compact('tasks'));
    }

    // ── Halaman Dinamis: Pantau Role Kustom / Apapun ─────
    public function roleTasks(Request $request, \App\Models\Role $role)
    {
        $date = $request->query('date', Carbon::today()->toDateString());

        // Pastikan tugas default harian sudah ter-generate untuk tanggal yang dipantau
        if ($date === Carbon::today()->toDateString()) {
            try {
                app(\App\Services\DefaultTaskService::class)->generateDailyTasks();
            } catch (\Throwable) {}
        }
        $tasks = $this->taskService->getAllTasksForRole($role->name, 20, $date);

        // ── Tugas Sosmed: gunakan SosmedAccount sebagai sumber utama ────────
        $roleUserIds = User::where('role', $role->name)->where('is_active', true)->pluck('id');

        // Helper: bangun baris sosmed per akun (dengan atau tanpa record SosmedTask)
        $buildSosmedRows = function (\Illuminate\Support\Collection $accounts, string $forDate) {
            $rows = collect();
            foreach ($accounts as $acc) {
                $task = SosmedTask::with(['assignedUser', 'verifiedBy', 'hrVerifiedBy'])
                    ->where('sosmed_account_id', $acc->id)
                    ->whereDate('task_date', $forDate)
                    ->first();
                // Pastikan relasi account ter-load agar accessor status_label bisa membaca pm_id/assistant_id
                if ($task) {
                    $task->setRelation('account', $acc);
                }

                $rows->push((object)[
                    'account'        => $acc,
                    'task'           => $task,
                    'executor_name'  => $acc->staffUser?->name ?? '—',
                    'executor_role'  => $acc->staffUser?->role_label ?? '',
                    'task_date'      => $forDate,
                    'status'         => $task?->status ?? 'no_task',
                    'status_label'   => $task ? $task->status_label : 'Belum Ada Tugas',
                    'status_class'   => $task ? $task->status_badge_class : 'bg-gray-100 text-gray-500 border border-gray-200',
                    'rejection_note' => $task?->rejection_note,
                    'is_verif_row'   => false,
                ]);
            }
            return $rows;
        };

        switch ($role->name) {
            case 'hr_staff':
                // 1. Akun yang di-assign langsung ke hr_staff sebagai eksekutor
                $executorAccounts = SosmedAccount::with(['staffUser', 'pmUser'])
                    ->whereIn('staff_id', $roleUserIds)->get();
                $sosmedExecRows = $buildSosmedRows($executorAccounts, $date);

                // 2. Semua tugas yang sudah lolos PM dan butuh final approval HR
                $finalApprovalTasks = SosmedTask::with(['account.staffUser', 'assignedUser'])
                    ->where('status', 'verified_by_pm')
                    ->whereDate('task_date', $date)
                    ->get()
                    ->map(fn($t) => (object)[
                        'account'        => $t->account,
                        'task'           => $t,
                        'executor_name'  => $t->assignedUser?->name ?? '—',
                        'executor_role'  => $t->assignedUser?->role_label ?? '',
                        'task_date'      => $date,
                        'status'         => $t->status,
                        'status_label'   => $t->status_label,
                        'status_class'   => $t->status_badge_class,
                        'rejection_note' => null,
                        'is_verif_row'   => true,
                    ]);
                $sosmedPending = $sosmedExecRows->merge($finalApprovalTasks)
                    ->sortBy('task_date');
                break;

            case 'hr_assistant':
                // Semua tugas sosmed di akun yang diawasi asisten ini (butuh verifikasi asisten)
                $assistantAccounts = SosmedAccount::with(['staffUser', 'pmUser'])
                    ->whereIn('assistant_id', $roleUserIds)->get();
                $sosmedPending = $buildSosmedRows($assistantAccounts, $date);
                break;

            case 'pm':
                // Akun yang dikelola PM sebagai eksekutor mandiri
                $pmExecAccounts = SosmedAccount::with(['staffUser', 'pmUser'])
                    ->whereIn('staff_id', $roleUserIds)->get();
                $pmExecRows = $buildSosmedRows($pmExecAccounts, $date);

                // Akun yang diawasi PM (butuh verifikasi PM)
                $pmVerifTasks = SosmedTask::with(['account.staffUser', 'assignedUser'])
                    ->whereHas('account', fn($q) => $q->whereIn('pm_id', $roleUserIds))
                    ->where('status', 'done_by_staff')
                    ->whereDate('task_date', $date)
                    ->get()
                    ->map(fn($t) => (object)[
                        'account'        => $t->account,
                        'task'           => $t,
                        'executor_name'  => $t->assignedUser?->name ?? '—',
                        'executor_role'  => $t->assignedUser?->role_label ?? '',
                        'task_date'      => $date,
                        'status'         => $t->status,
                        'status_label'   => $t->status_label,
                        'status_class'   => 'bg-blue-50 text-blue-700 border border-blue-200',
                        'rejection_note' => null,
                        'is_verif_row'   => true,
                    ]);
                $sosmedPending = $pmExecRows->merge($pmVerifTasks)->sortBy('task_date');
                break;

            case 'sosmed':
            case 'digital_marketing':
                // Akun yang dikelola oleh user role ini
                $execAccounts = SosmedAccount::with(['staffUser', 'pmUser'])
                    ->whereIn('staff_id', $roleUserIds)->get();
                $sosmedPending = $buildSosmedRows($execAccounts, $date);
                break;

            default:
                $sosmedPending = collect();
                break;
        }

        return view('admin.tasks.role-tasks', compact('tasks', 'role', 'date', 'sosmedPending'));
    }

    // ── Force Destroy (Admin hapus task siapapun) ────────

    public function forceDestroy(Task $task)
    {
        $redirect = url()->previous();

        try {
            $title = $task->title;
            $this->taskService->forceDeleteTask($task);
            $this->logActivity('task.deleted', 'Tugas', "Force-hapus tugas '{$title}'");
            return redirect($redirect)->with('success', 'Tugas berhasil dihapus.');
        } catch (ValidationException $e) {
            return redirect($redirect)->with('error', $e->errors()['task'][0] ?? 'Gagal menghapus tugas.');
        }
    }

    // ── Force Update (Admin edit task siapapun) ──────────

    public function forceUpdate(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'kantor'      => ['nullable', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4,Kantor 5,Kantor 6,Kantor 7,Kantor 8,Kantor 9,Kantor 10'],
        ]);

        $redirect = url()->previous();

        try {
            $task->update([
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'kantor'      => $validated['kantor'] ?? null,
            ]);
            $this->logActivity('task.updated', 'Tugas', "Force-edit tugas '{$task->title}'", $task);
            return redirect($redirect)->with('success', 'Tugas berhasil diperbarui.');
        } catch (\Throwable $e) {
            return redirect($redirect)->with('error', 'Gagal memperbarui tugas.');
        }
    }
}