<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use LogsActivity;

    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index(Request $request)
    {
        $stats    = $this->taskService->getDailyStats();
        $rankings = $this->taskService->getTopRankings('week');

        $progressSearch = trim((string) $request->query('progress_search', ''));
        $progressStatus = (string) $request->query('progress_status', '');
        if (!in_array($progressStatus, ['belum', 'sudah'], true)) {
            $progressStatus = '';
        }

        $progress = $this->taskService->filterTeamProgress(
            $this->taskService->getTeamProgressOverview(),
            $progressSearch,
            $progressStatus
        );

        // Semua yang belum beres (lintas halaman) untuk tombol pengingat massal
        $progressUnfinished = $progress->filter(fn($u) => $u->belum_hari_ini > 0)->values();

        $perUser = $this->taskService->paginateTeamProgress(
            $progress,
            (int) $request->query('page', 1),
            10,
            route('admin.dashboard'),
            array_filter($request->only('progress_search', 'progress_status'))
        );

        return view('admin.dashboard', compact(
            'stats', 'perUser', 'rankings', 'progressSearch', 'progressStatus', 'progressUnfinished'
        ));
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

    /**
     * Kirim pengingat tugas. Berguna untuk satu user (user_ids berisi satu id)
     * maupun banyak user sekaligus dari tabel progres tim.
     */
    public function sendReminder(Request $request)
    {
        $validated = $request->validate([
            'user_ids'   => ['required', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $overview = $this->taskService->getTeamProgressOverview()->keyBy('id');

        $targets = User::query()
            ->whereIn('id', $validated['user_ids'])
            ->where('role', '!=', 'admin')
            ->where('is_active', 1)
            ->get();

        $unfinished = [];
        foreach ($targets as $user) {
            $unfinished[$user->id] = (int) ($overview->get($user->id)?->belum_hari_ini ?? 0);
        }

        $count = NotificationService::sendTaskReminders($targets, Auth::user(), $unfinished);

        if ($count === 0) {
            return back()->with('error', 'Tidak ada pengguna yang bisa dikirim pengingat.');
        }

        $this->logActivity(
            'notification.reminder',
            'Progres Tim',
            "Mengirim pengingat tugas kepada {$count} pengguna"
        );

        return back()->with('success', "Pengingat berhasil dikirim ke {$count} pengguna.");
    }
}
