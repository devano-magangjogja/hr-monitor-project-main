<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TaskService;

class DashboardController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index()
    {
        $stats    = $this->taskService->getDailyStats();
        $perUser  = $this->taskService->getDailyStatsPerUser();
        $rankings = $this->taskService->getTopRankings('week');

        return view('admin.dashboard', compact('stats', 'perUser', 'rankings'));
    }

    public function teamProgressDetail(User $user)
    {
        // Admin dapat melihat detail progres seluruh anggota tim (selain admin sendiri)
        abort_if($user->role === 'admin', 404);

        $tasks      = $this->taskService->getAllTasksForUserToday($user->id);
        $scoreWeek  = $this->taskService->getUserScore($user->id, 'week');
        $scoreMonth = $this->taskService->getUserScore($user->id, 'month');

        return view('admin.dashboard.team-progress-detail', compact('user', 'tasks', 'scoreWeek', 'scoreMonth'));
    }
}