<?php

namespace App\Http\Controllers\PM;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(protected TaskService $taskService) {}

    public function index()
    {
        $userId = Auth::id();
        $tasks  = $this->taskService->getAllTasksForUserToday($userId);

        $stats = [
            'total'     => $tasks->count(),
            'completed' => $tasks->filter(fn($t) =>
                $t->assignments->first()?->is_completed === 'completed'
            )->count(),
            'pending'   => $tasks->filter(fn($t) =>
                $t->assignments->first()?->is_completed === 'pending'
            )->count(),
            'not_done'  => $tasks->filter(fn($t) =>
                $t->assignments->first()?->is_completed === 'not_done'
            )->count(),
        ];

        $statusOrder = ['pending' => 0, 'not_done' => 1, 'completed' => 2];
        $tasks = $tasks->sortBy(fn($t) =>
            $statusOrder[$t->assignments->first()?->is_completed ?? 'pending'] ?? 0
        )->values();

        $scoreWeek  = $this->taskService->getUserScore($userId, 'week');
        $scoreMonth = $this->taskService->getUserScore($userId, 'month');

        return view('pm.dashboard', compact('tasks', 'stats', 'scoreWeek', 'scoreMonth'));
    }
}
