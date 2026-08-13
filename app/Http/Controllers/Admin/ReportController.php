<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    public function __construct(
        protected TaskService $taskService,
        protected UserService $userService,
    ) {}

    public function history(Request $request)
    {
        $date   = $request->query('date');
        $search = $request->query('search');
        $userId = $request->query('user_id');
        $users  = $this->userService->getAllUsers();
        $tasks  = $this->taskService->getHistoryForAdmin(
            $userId ? (int) $userId : null,
            $date,
            $search
        );
        return view('admin.reports.history', compact('tasks', 'users', 'date', 'userId', 'search'));
    }

    public function productivity(Request $request)
    {
        $today    = Carbon::today()->toDateString();
        $dateFrom = $request->query('date_from', $today);
        $dateTo   = $request->query('date_to',   $today);

        // Normalise: pastikan dateFrom <= dateTo
        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $report = $this->taskService->getProductivityByRange($dateFrom, $dateTo);

        return view('admin.reports.productivity', compact('report', 'dateFrom', 'dateTo', 'today'));
    }

    public function productivityDetail(Request $request, \App\Models\User $user)
    {
        abort_if(! in_array($user->role, ['hr_staff', 'hr_assistant']), 403);

        $today    = Carbon::today()->toDateString();
        $dateFrom = $request->query('date_from', $today);
        $dateTo   = $request->query('date_to',   $today);

        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $tasks = $this->taskService->getProductivityDetailForUser($user->id, $dateFrom, $dateTo);

        // Hitung ringkasan
        $total     = $tasks->count();
        $completed = 0; $notDone = 0; $pending = 0;
        foreach ($tasks as $task) {
            $s = $task->assignments->first()?->is_completed ?? 'pending';
            if ($s === 'completed')      $completed++;
            elseif ($s === 'not_done')   $notDone++;
            else                         $pending++;
        }
        $pct = $total > 0 ? round(($completed / $total) * 100) : 0;

        // Kelompokkan per hari untuk tampilan yang lebih rapi
        $tasksByDate = $tasks->groupBy(fn($t) => $t->task_date->toDateString());

        return view('admin.reports.productivity-detail',
            compact('user', 'tasks', 'tasksByDate', 'dateFrom', 'dateTo',
                    'total', 'completed', 'notDone', 'pending', 'pct'));
    }

    public function ranking(Request $request)
    {
        $period = $request->query('period', 'week');
        $users  = $this->userService->getAllUsers();
    
        $rankings = $users->map(function ($user) use ($period) {
            return [
                'user'  => $user,
                'score' => $this->taskService->getUserScore($user->id, $period),
            ];
        })
        ->sortByDesc('score')
        ->values();
    
        return view('admin.reports.ranking', compact('rankings', 'period'));
    }

    /**
     * Admin tandai selesai sebuah assignment milik bawahan.
     * Route: PATCH /admin/reports/assignments/{assignment}/complete
     */
    public function completeAssignment(int $assignmentId)
    {
        try {
            $this->taskService->adminCompleteAssignment($assignmentId);
            return redirect()->back()
                ->with('task_completed', 'Tugas berhasil ditandai selesai.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->with('error', array_values($e->errors())[0][0] ?? 'Gagal menandai selesai.');
        }
    }

    /**
     * Admin hapus sebuah assignment dari riwayat bawahan.
     * Route: DELETE /admin/reports/assignments/{assignment}
     */
    public function destroyAssignment(int $assignmentId)
    {
        try {
            $this->taskService->adminDeleteAssignment($assignmentId);
            return redirect()->back()
                ->with('success', 'Riwayat tugas berhasil dihapus.');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->with('error', array_values($e->errors())[0][0] ?? 'Gagal menghapus riwayat.');
        }
    }
}