<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function assignIndex()
    {
        $tasks           = $this->taskService->getAssignedTasksForStaff(Auth::id());
        $assignableUsers = $this->taskService->getAssignableUsers();
        return view('staff.assign-tasks.index', compact('tasks', 'assignableUsers'));
    }

    public function assignStore(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'user_ids'    => ['required', 'array', 'min:1'],
            'user_ids.*'  => ['integer', 'exists:users,id'],
        ]);

        try {
            $this->taskService->createAssignedTask($validated);
            return redirect()->route('staff.assign.index')
                ->with('success', 'Tugas berhasil dikirim ke HR Assistant.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function assignUpdate(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'user_ids'    => ['required', 'array', 'min:1'],
            'user_ids.*'  => ['integer', 'exists:users,id'],
        ]);

        try {
            $this->taskService->updateTask($task, $validated);
            return redirect()->route('staff.assign.index')
                ->with('success', 'Tugas berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal memperbarui tugas.');
        }
    }

    public function assignDestroy(Task $task)
    {
        try {
            $this->taskService->deleteTask($task);
            return redirect()->route('staff.assign.index')
                ->with('success', 'Tugas berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menghapus tugas.');
        }
    }

   // ── Self Task (Tugas Mandiri) ────────────────────────
   public function index()
   {
       $tasks = $this->taskService->getSelfTasksToday(Auth::id());
       $tasks = $this->taskService->sortTasksByStatus($tasks, Auth::id());
       return view('staff.tasks.index', compact('tasks'));
   }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $this->taskService->createSelfTask($validated);
            return redirect()->route('staff.tasks.index')
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
            return redirect()->route('staff.tasks.index')
                ->with('success', 'Tugas mandiri berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal memperbarui tugas.');
        }
    }
    public function destroy(Task $task)
    {
        try {
            $this->taskService->deleteSelfTask($task);
            return redirect()->route('staff.tasks.index')
                ->with('success', 'Tugas mandiri berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menghapus tugas.');
        }
    }
    public function complete(Request $request, Task $task)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);
        
        try {
            $this->taskService->completeTask($task, $request->note);
            return redirect()->route('staff.tasks.index')
                ->with('task_completed', 'Terima kasih sudah menyelesaikan tugas ini dengan baik. Tetap semangat!');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menyelesaikan tugas.');
        }
    }
    public function history(Request $request)
    {
        $date   = $request->query('date');
        $search = $request->query('search');
        $tasks  = $this->taskService->getHistoryForUser(Auth::id(), $date, $search);
        return view('staff.history.index', compact('tasks', 'date', 'search'));
    }

    public function dailyIndex()
    {
        $tasks = $this->taskService->getDefaultTasksForUser(Auth::id());
        $tasks = $this->taskService->sortTasksByStatus($tasks, Auth::id());
        return view('staff.tasks.daily', compact('tasks'));
    }

    public function dailyComplete(Request $request, Task $task)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->taskService->completeTask($task, $request->note);
            return redirect()->route('staff.tasks.daily')
                ->with('task_completed', 'Terima kasih sudah menyelesaikan tugas ini dengan baik. Tetap semangat!');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menyelesaikan tugas.');
        }
    }

    public function assignedIndex()
    {
        $tasks = $this->taskService->getAssignedTasksFromAdmin(Auth::id());
        $tasks = $this->taskService->sortTasksByStatus($tasks, Auth::id());
        return view('staff.tasks.assigned', compact('tasks'));
    }

    public function assignedComplete(Request $request, Task $task)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->taskService->completeTask($task, $request->note);
            return redirect()->route('staff.tasks.assigned')
                ->with('task_completed', 'Terima kasih sudah menyelesaikan tugas ini dengan baik. Tetap semangat!');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menyelesaikan tugas.');
        }
    }
            
    public function allIndex()
    {
        $tasks = $this->taskService->getAllTasksForUserToday(Auth::id());
        $tasks = $this->taskService->sortTasksByStatus($tasks, Auth::id());
        return view('staff.tasks.all', compact('tasks'));
    }

    public function allComplete(Request $request, Task $task)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->taskService->completeTask($task, $request->note);
            return redirect()->route('staff.tasks.all')
                ->with('task_completed', 'Terima kasih sudah menyelesaikan tugas ini dengan baik. Tetap semangat!');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menyelesaikan tugas.');
        }
    }

    public function assistantProgress(Request $request)
    {
        $today    = \Carbon\Carbon::today()->toDateString();
        $dateFrom = $request->query('date_from', $today);
        $dateTo   = $request->query('date_to',   $today);

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $assistants = $this->taskService->getAssistantProgressByRange($dateFrom, $dateTo);

        $scoreData = [];
        foreach ($assistants as $assistant) {
            $scoreData[$assistant->id] = [
                'week'  => $this->taskService->getUserScore($assistant->id, 'week'),
                'month' => $this->taskService->getUserScore($assistant->id, 'month'),
            ];
        }

        return view('staff.assistant-progress', compact(
            'assistants', 'scoreData', 'dateFrom', 'dateTo', 'today'
        ));
    }

    public function assistantProgressDetail(Request $request, \App\Models\User $user)
    {
        abort_if($user->role !== 'hr_assistant', 403);

        $today    = \Carbon\Carbon::today()->toDateString();
        $dateFrom = $request->query('date_from', $today);
        $dateTo   = $request->query('date_to',   $today);

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $tasks = $this->taskService->getProductivityDetailForUser($user->id, $dateFrom, $dateTo);

        $scoreWeek  = $this->taskService->getUserScore($user->id, 'week');
        $scoreMonth = $this->taskService->getUserScore($user->id, 'month');

        // Hitung ringkasan
        $total = $tasks->count();
        $completed = $notDone = $pending = 0;
        foreach ($tasks as $task) {
            $s = $task->assignments->first()?->is_completed ?? 'pending';
            if ($s === 'completed')    $completed++;
            elseif ($s === 'not_done') $notDone++;
            else                       $pending++;
        }
        $pct = $total > 0 ? round(($completed / $total) * 100) : 0;

        // Kelompokkan per hari
        $tasksByDate = $tasks->groupBy(fn($t) => $t->task_date->toDateString());

        return view('staff.assistant-progress-detail', compact(
            'user', 'tasks', 'tasksByDate',
            'dateFrom', 'dateTo',
            'total', 'completed', 'notDone', 'pending', 'pct',
            'scoreWeek', 'scoreMonth'
        ));
    }
}