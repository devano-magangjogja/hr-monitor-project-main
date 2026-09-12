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
        $today        = Carbon::today()->toDateString();
        $dateFrom     = $request->query('date_from', $today);
        $dateTo       = $request->query('date_to',   $today);
        $selectedRole = $request->query('role', 'all');

        // Normalise: pastikan dateFrom <= dateTo
        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $report = $this->taskService->getProductivityByRange($dateFrom, $dateTo, $selectedRole);

        // Daftar role selain admin untuk dropdown filter
        $availableRoles = \App\Models\Role::where('name', '!=', 'admin')->orderBy('label')->get();

        return view('admin.reports.productivity', compact('report', 'dateFrom', 'dateTo', 'today', 'selectedRole', 'availableRoles'));
    }

    public function productivityDetail(Request $request, \App\Models\User $user)
    {
        abort_if($user->role === 'admin', 403);

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

    /**
     * Admin bersihkan / hapus riwayat aktivitas massal berdasarkan periode & kategori.
     * Route: POST /admin/reports/history/purge
     */
    public function purgeHistory(Request $request)
    {
        $validated = $request->validate([
            'period_type'    => ['required', 'in:older_than_year,older_than_months,by_year,by_month,before_date,all'],
            'year'           => ['nullable', 'integer', 'min:2020', 'max:2099'],
            'month'          => ['nullable', 'integer', 'min:1', 'max:12'],
            'before_date'    => ['nullable', 'date'],
            'targets'        => ['required', 'array', 'min:1'],
            'targets.*'      => ['in:tasks,sosmed,presensi'],
            'role'           => ['nullable', 'string'],
            'confirm_phrase' => ['nullable', 'string'],
        ]);

        $periodType = $validated['period_type'];
        $today = Carbon::today()->toDateString();
        $dateStart = null;
        $dateEnd = null;

        if ($periodType === 'older_than_year') {
            $dateEnd = Carbon::now()->subYear()->toDateString();
        } elseif ($periodType === 'older_than_months') {
            $dateEnd = Carbon::now()->subMonths(6)->toDateString();
        } elseif ($periodType === 'by_year') {
            if (empty($validated['year'])) {
                return back()->with('error', 'Silakan pilih tahun yang ingin dihapus.');
            }
            $dateStart = Carbon::createFromDate($validated['year'], 1, 1)->startOfYear()->toDateString();
            $dateEnd   = Carbon::createFromDate($validated['year'], 1, 1)->endOfYear()->toDateString();
            if ($dateEnd >= $today) {
                $dateEnd = Carbon::yesterday()->toDateString();
            }
        } elseif ($periodType === 'by_month') {
            if (empty($validated['year']) || empty($validated['month'])) {
                return back()->with('error', 'Silakan pilih bulan dan tahun yang ingin dihapus.');
            }
            $dt = Carbon::createFromDate($validated['year'], $validated['month'], 1);
            $dateStart = $dt->copy()->startOfMonth()->toDateString();
            $dateEnd   = $dt->copy()->endOfMonth()->toDateString();
            if ($dateEnd >= $today) {
                $dateEnd = Carbon::yesterday()->toDateString();
            }
        } elseif ($periodType === 'before_date') {
            if (empty($validated['before_date'])) {
                return back()->with('error', 'Silakan tentukan batas tanggal penghapusan.');
            }
            $dateEnd = min($validated['before_date'], Carbon::yesterday()->toDateString());
        } elseif ($periodType === 'all') {
            if (trim(strtoupper($request->input('confirm_phrase', ''))) !== 'HAPUS') {
                return back()->with('error', 'Konfirmasi keamanan gagal. Ketik kata "HAPUS" untuk menghapus seluruh riwayat.');
            }
            $dateEnd = Carbon::yesterday()->toDateString();
        }

        // Jangan pernah hapus data hari ini / masa depan
        if ($dateEnd && $dateEnd >= $today) {
            $dateEnd = Carbon::yesterday()->toDateString();
        }

        $targets = $validated['targets'];
        $role = $validated['role'] ?? null;
        $deletedSummary = [];

        // 1. Tasks & Task Assignments
        if (in_array('tasks', $targets)) {
            $taskQuery = \App\Models\Task::query();

            // Filter date
            if ($dateStart && $dateEnd) {
                $taskQuery->whereBetween('task_date', [$dateStart, $dateEnd]);
            } elseif ($dateEnd) {
                $taskQuery->where('task_date', '<=', $dateEnd);
            }

            // Filter role
            if (!empty($role) && $role !== 'all') {
                $taskQuery->whereHas('assignments.user', function ($q) use ($role) {
                    $q->where('role', $role);
                });
            }

            $countTasks = $taskQuery->count();
            $taskQuery->delete();
            $deletedSummary[] = "{$countTasks} riwayat tugas";
        }

        // 2. Sosmed Tasks & Logs
        if (in_array('sosmed', $targets)) {
            $sosmedQuery = \App\Models\SosmedTask::query();

            if ($dateStart && $dateEnd) {
                $sosmedQuery->whereBetween('task_date', [$dateStart, $dateEnd]);
            } elseif ($dateEnd) {
                $sosmedQuery->where('task_date', '<=', $dateEnd);
            }

            $countSosmed = $sosmedQuery->count();
            $sosmedQuery->delete();
            $deletedSummary[] = "{$countSosmed} riwayat sosmed";
        }

        // 3. Presensi Pemagang
        if (in_array('presensi', $targets)) {
            $presensiQuery = \App\Models\Presensi::query();

            if ($dateStart && $dateEnd) {
                $presensiQuery->whereBetween('tanggal', [$dateStart, $dateEnd]);
            } elseif ($dateEnd) {
                $presensiQuery->where('tanggal', '<=', $dateEnd);
            }

            $countPresensi = $presensiQuery->count();
            $presensiQuery->delete();
            $deletedSummary[] = "{$countPresensi} log presensi";
        }

        $summaryText = !empty($deletedSummary) ? implode(', ', $deletedSummary) : '0 data';

        return redirect()->route('admin.reports.history')
            ->with('success', "Pembersihan riwayat berhasil! Berhasil menghapus {$summaryText}.");
    }
}