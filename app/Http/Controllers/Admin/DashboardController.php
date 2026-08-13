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
        // Hanya boleh lihat detail hr_staff dan hr_assistant
        abort_if(! in_array($user->role, ['hr_staff', 'hr_assistant']), 403);

        $tasks      = $this->taskService->getAllTasksForUserToday($user->id);
        $scoreWeek  = $this->taskService->getUserScore($user->id, 'week');
        $scoreMonth = $this->taskService->getUserScore($user->id, 'month');

        return view('admin.dashboard.team-progress-detail', compact('user', 'tasks', 'scoreWeek', 'scoreMonth'));
    }
}