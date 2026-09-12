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
        $myAccounts = SosmedAccount::with(['pmUser', 'creator'])
            ->where('staff_id', $userId)
            ->orderBy('platform')
            ->get();

        $accountIds = $myAccounts->pluck('id');

        $todaySosmedTasks = SosmedTask::with(['verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $accountIds)
            ->whereDate('task_date', now()->toDateString())
            ->get()
            ->keyBy('sosmed_account_id');

        // Akun tugas sosmed yang perlu dikelola hari ini
        $sosmedAccountTasks = $myAccounts->map(function ($acc) use ($todaySosmedTasks) {
            $todayTask = $todaySosmedTasks[$acc->id] ?? null;
            $status = $todayTask?->status ?? 'pending';
            return [
                'account'        => $acc,
                'task'           => $todayTask,
                'status'         => $status,
                'title'          => 'Kelola Konten - ' . $acc->name,
                'platform'       => $acc->platform,
                'platform_icon'  => $acc->platform_icon,
                'platform_color' => $acc->platform_color,
                'is_done'        => $status === 'approved_hr',
                'is_waiting'     => in_array($status, ['done_by_staff', 'verified_by_pm']),
                'is_rejected'    => $status === 'rejected',
                'is_pending'     => $status === 'pending',
            ];
        });

        $statusWeight = [
            'rejected'       => 0,
            'pending'        => 1,
            'done_by_staff'  => 2,
            'verified_by_pm' => 3,
            'approved_hr'    => 4,
        ];
        $sosmedAccountTasks = $sosmedAccountTasks->sortBy(fn($item) => $statusWeight[$item['status']] ?? 99)->values();

        $sosmedStats = [
            'accounts_count' => $myAccounts->count(),
            'pending_upload' => $myAccounts->filter(function ($acc) use ($todaySosmedTasks) {
                if (!isset($todaySosmedTasks[$acc->id])) return true;
                return in_array($todaySosmedTasks[$acc->id]->status, ['pending', 'rejected']);
            })->count(),
            'waiting_pm'     => $todaySosmedTasks->where('status', 'done_by_staff')->count(),
            'waiting_hr'     => $todaySosmedTasks->where('status', 'verified_by_pm')->count(),
            'approved_final' => $todaySosmedTasks->where('status', 'approved_hr')->count(),
        ];

        return view('sosmed.dashboard', compact(
            'tasks', 'stats', 'scoreWeek', 'scoreMonth', 'myAccounts', 'sosmedStats',
            'sosmedAccountTasks', 'todaySosmedTasks'
        ));
    }
}
