<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index(Request $request)
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

        // Urutkan: pending di atas, not_done di tengah, completed di bawah
        $statusOrder = ['pending' => 0, 'not_done' => 1, 'completed' => 2];
        $tasks = $tasks->sortBy(fn($t) =>
            $statusOrder[$t->assignments->first()?->is_completed ?? 'pending'] ?? 0
        )->values();

        $scoreWeek  = $this->taskService->getUserScore($userId, 'week');
        $scoreMonth = $this->taskService->getUserScore($userId, 'month');

        // Progres tim: hanya user dengan role di bawah staff (selain admin & sesamanya)
        $progressSearch = trim((string) $request->query('progress_search', ''));
        $progressStatus = (string) $request->query('progress_status', '');
        if (!in_array($progressStatus, ['belum', 'sudah'], true)) {
            $progressStatus = '';
        }

        $progress = $this->taskService->filterTeamProgress(
            $this->taskService->getTeamProgressOverview(true),
            $progressSearch,
            $progressStatus
        );

        // Semua yang belum beres (lintas halaman) untuk tombol pengingat massal
        $progressUnfinished = $progress->filter(fn($u) => $u->belum_hari_ini > 0)->values();

        $perUser = $this->taskService->paginateTeamProgress(
            $progress,
            (int) $request->query('page', 1),
            10,
            route('staff.dashboard'),
            array_filter($request->only('progress_search', 'progress_status'))
        );

        return view('staff.dashboard', compact(
            'tasks', 'stats', 'scoreWeek', 'scoreMonth',
            'perUser', 'progressSearch', 'progressStatus', 'progressUnfinished'
        ));
    }

    /**
     * Pengingat tugas untuk bawahan staff saja.
     */
    public function sendReminder(Request $request)
    {
        $validated = $request->validate([
            'user_ids'   => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $overview = $this->taskService->getTeamProgressOverview(true)->keyBy('id');

        $targets = User::query()
            ->whereIn('id', $validated['user_ids'])
            ->whereNotIn('role', ['admin', 'hr_staff'])
            ->where('is_active', 1)
            ->get();

        $unfinished = [];
        foreach ($targets as $user) {
            $unfinished[$user->id] = (int) ($overview->get($user->id)?->belum_hari_ini ?? 0);
        }

        $count = NotificationService::sendTaskReminders($targets, Auth::user(), $unfinished);

        if ($count === 0) {
            return back()->with('error', 'Tidak ada bawahan yang bisa dikirim pengingat.');
        }

        return back()->with('success', "Pengingat berhasil dikirim ke {$count} pengguna.");
    }
}
