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
        $tasks = $this->taskService->getAllTasksForRole($role->name, 20, $date);

        // ── Tugas Sosmed: tampilkan semua status relevan per role ──────────
        $sosmedQuery = SosmedTask::with(['account', 'assignedUser'])
            ->whereDate('task_date', $date);

        // Status yang dianggap "selesai" — tidak perlu ditampilkan
        $doneStatuses = ['approved_hr'];

        $roleUserIds = User::where('role', $role->name)->where('is_active', true)->pluck('id');

        switch ($role->name) {
            case 'hr_staff':
                // HR Staff sebagai eksekutor (admin assign langsung), PLUS yang butuh final approval
                $staffExecutorTasks = (clone $sosmedQuery)
                    ->whereIn('assigned_to', $roleUserIds)
                    ->whereNotIn('status', $doneStatuses)
                    ->get();
                $pendingFinalApproval = (clone $sosmedQuery)
                    ->where('status', 'verified_by_pm')
                    ->get();
                $sosmedPending = $staffExecutorTasks->merge($pendingFinalApproval)->unique('id')->sortBy('task_date');
                break;

            case 'hr_assistant':
                // Asisten: akun sosmed yang diawasi asisten ini, semua status belum selesai
                $sosmedPending = (clone $sosmedQuery)
                    ->whereHas('account', fn($q) => $q->whereIn('assistant_id', $roleUserIds))
                    ->whereNotIn('status', $doneStatuses)
                    ->get();
                break;

            case 'pm':
                // PM sebagai eksekutor mandiri (pending/rejected) + akun yang diawasi PM (butuh verif)
                $pmExecutorTasks = (clone $sosmedQuery)
                    ->whereIn('assigned_to', $roleUserIds)
                    ->whereNotIn('status', $doneStatuses)
                    ->get();
                $pmVerifTasks = (clone $sosmedQuery)
                    ->whereHas('account', fn($q) => $q->whereIn('pm_id', $roleUserIds))
                    ->where('status', 'done_by_staff')
                    ->get();
                $sosmedPending = $pmExecutorTasks->merge($pmVerifTasks)->unique('id')->sortBy('task_date');
                break;

            case 'sosmed':
            case 'digital_marketing':
                // Eksekutor: tampilkan semua status belum selesai
                $sosmedPending = (clone $sosmedQuery)
                    ->whereIn('assigned_to', $roleUserIds)
                    ->whereNotIn('status', $doneStatuses)
                    ->get();
                break;

            default:
                // Role lain (cs, ob, programmer, dg, vg, dll.) tidak memiliki tugas sosmed
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
}