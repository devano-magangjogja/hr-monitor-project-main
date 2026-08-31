<?php

namespace App\Http\Controllers\Sosmed;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function __construct(protected TaskService $taskService) {}

    // ── Tugas Mandiri ────────────────────────────────────

    public function index()
    {
        $tasks = $this->taskService->getSelfTasksToday(Auth::id());
        $tasks = $this->taskService->sortTasksByStatus($tasks, Auth::id());
        return view('sosmed.tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ]);
        try {
            $this->taskService->createSelfTask($validated);
            return redirect()->route('sosmed.tasks.index')
                ->with('success', 'Tugas mandiri berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ]);
        try {
            $this->taskService->updateSelfTask($task, $validated);
            return redirect()->route('sosmed.tasks.index')
                ->with('success', 'Tugas mandiri berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal memperbarui tugas.');
        }
    }

    public function destroy(Task $task)
    {
        try {
            $this->taskService->deleteSelfTask($task);
            return redirect()->route('sosmed.tasks.index')
                ->with('success', 'Tugas mandiri berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menghapus tugas.');
        }
    }

    public function complete(Request $request, Task $task)
    {
        $request->validate(['note' => ['nullable', 'string', 'max:500']]);
        try {
            $this->taskService->completeTask($task, $request->note);
            return redirect()->route('sosmed.tasks.index')
                ->with('task_completed', 'Terima kasih sudah menyelesaikan tugas ini dengan baik. Tetap semangat!');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menyelesaikan tugas.');
        }
    }

    // ── Tugas Harian (Default) ───────────────────────────

    public function dailyIndex()
    {
        $tasks = $this->taskService->getDefaultTasksForUser(Auth::id());
        $tasks = $this->taskService->sortTasksByStatus($tasks, Auth::id());
        return view('sosmed.tasks.daily', compact('tasks'));
    }

    public function dailyComplete(Request $request, Task $task)
    {
        $request->validate(['note' => ['nullable', 'string', 'max:500']]);
        try {
            $this->taskService->completeTask($task, $request->note);
            return redirect()->route('sosmed.tasks.daily')
                ->with('task_completed', 'Terima kasih sudah menyelesaikan tugas ini dengan baik. Tetap semangat!');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menyelesaikan tugas.');
        }
    }

    // ── Tugas dari Admin ─────────────────────────────────

    public function assignedIndex()
    {
        $tasks = $this->taskService->getAssignedTasksFromAdmin(Auth::id());
        $tasks = $this->taskService->sortTasksByStatus($tasks, Auth::id());
        return view('sosmed.tasks.assigned', compact('tasks'));
    }

    public function assignedComplete(Request $request, Task $task)
    {
        $request->validate(['note' => ['nullable', 'string', 'max:500']]);
        try {
            $this->taskService->completeTask($task, $request->note);
            return redirect()->route('sosmed.tasks.assigned')
                ->with('task_completed', 'Terima kasih sudah menyelesaikan tugas ini dengan baik. Tetap semangat!');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menyelesaikan tugas.');
        }
    }

    // ── Semua Tugas ──────────────────────────────────────

    public function allIndex()
    {
        $tasks = $this->taskService->getAllTasksForUserToday(Auth::id());
        $tasks = $this->taskService->sortTasksByStatus($tasks, Auth::id());
        return view('sosmed.tasks.all', compact('tasks'));
    }

    public function allComplete(Request $request, Task $task)
    {
        $request->validate(['note' => ['nullable', 'string', 'max:500']]);
        try {
            $this->taskService->completeTask($task, $request->note);
            return redirect()->route('sosmed.tasks.all')
                ->with('task_completed', 'Terima kasih sudah menyelesaikan tugas ini dengan baik. Tetap semangat!');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menyelesaikan tugas.');
        }
    }

    // ── Riwayat ──────────────────────────────────────────

    public function history(Request $request)
    {
        $date   = $request->query('date');
        $search = $request->query('search');
        $tasks  = $this->taskService->getHistoryForUser(Auth::id(), $date, $search);
        return view('sosmed.history.index', compact('tasks', 'date', 'search'));
    }
}
