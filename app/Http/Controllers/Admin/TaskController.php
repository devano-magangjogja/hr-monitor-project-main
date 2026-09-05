<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
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
            'kantor' => ['nullable', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        try {
            $this->taskService->createAssignedTask($validated);
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
            'kantor' => ['nullable', 'string', 'in:Kantor 1,Kantor 2,Kantor 3,Kantor 4'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        try {
            $this->taskService->updateTask($task, $validated);
            return back()->with('success', 'Tugas berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal memperbarui tugas.');
        }
    }

    public function destroy(Task $task)
    {
        try {
            $this->taskService->deleteTask($task);
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
    public function roleTasks(\App\Models\Role $role)
    {
        if ($role->name === 'hr_staff') {
            $tasks = $this->taskService->getTasksByStaff();
        } elseif ($role->name === 'hr_assistant') {
            $tasks = $this->taskService->getAllTasksForAssistant();
        } else {
            $tasks = $this->taskService->getAllTasksForRole($role->name);
        }

        return view('admin.tasks.role-tasks', compact('tasks', 'role'));
    }

    // ── Force Destroy (Admin hapus task siapapun) ────────

    public function forceDestroy(Task $task)
    {
        $redirect = url()->previous();

        try {
            $this->taskService->forceDeleteTask($task);
            return redirect($redirect)->with('success', 'Tugas berhasil dihapus.');
        } catch (ValidationException $e) {
            return redirect($redirect)->with('error', $e->errors()['task'][0] ?? 'Gagal menghapus tugas.');
        }
    }
}