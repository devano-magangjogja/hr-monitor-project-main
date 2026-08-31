<?php

namespace App\Http\Controllers\Sosmed;

use App\Http\Controllers\Controller;
use App\Models\SosmedAccount;
use App\Models\SosmedTask;
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

        // Statistik khusus akun sosmed & tugas konten
        $myAccounts = SosmedAccount::where('staff_id', $userId)->get();
        $sosmedTasks = SosmedTask::where('assigned_to', $userId)->get();

        $sosmedStats = [
            'accounts_count' => $myAccounts->count(),
            'pending_upload' => $sosmedTasks->where('status', 'pending')->count(),
            'waiting_pm'     => $sosmedTasks->where('status', 'done_by_staff')->count(),
            'waiting_hr'     => $sosmedTasks->where('status', 'verified_by_pm')->count(),
            'approved_final' => $sosmedTasks->where('status', 'approved_hr')->count(),
        ];

        return view('sosmed.dashboard', compact(
            'tasks', 'stats', 'scoreWeek', 'scoreMonth', 'myAccounts', 'sosmedTasks', 'sosmedStats'
        ));
    }
}
