<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('role')) {
            $query->where('user_role', $request->input('role'));
        }
        if ($request->filled('module')) {
            $query->where('module', $request->input('module'));
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', '%.' . $request->input('action'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $today      = now()->toDateString();
        $statsToday = ActivityLog::whereDate('created_at', $today)->count();
        $statsTotal = ActivityLog::count();
        $topModule  = ActivityLog::selectRaw('module, count(*) as cnt')
            ->groupBy('module')->orderByDesc('cnt')->value('module') ?? '-';
        $topUser    = ActivityLog::selectRaw('user_name, count(*) as cnt')
            ->groupBy('user_name')->orderByDesc('cnt')->value('user_name') ?? '-';

        $users   = User::orderBy('name')->get(['id', 'name', 'role']);
        $modules = ActivityLog::distinct()->pluck('module')->sort()->values();
        $roles   = User::distinct()->pluck('role')->sort()->values();

        return view('admin.activity-log.index', compact(
            'logs', 'users', 'modules', 'roles',
            'statsToday', 'statsTotal', 'topModule', 'topUser'
        ));
    }

    public function purge(Request $request)
    {
        $validated = $request->validate([
            'period' => ['required', 'in:all,1_week,1_month,3_months,6_months'],
        ]);

        $query = ActivityLog::query();

        if ($validated['period'] === '1_week') {
            $query->where('created_at', '<', now()->subWeek());
        } elseif ($validated['period'] === '1_month') {
            $query->where('created_at', '<', now()->subMonth());
        } elseif ($validated['period'] === '3_months') {
            $query->where('created_at', '<', now()->subMonths(3));
        } elseif ($validated['period'] === '6_months') {
            $query->where('created_at', '<', now()->subMonths(6));
        }

        $count = $query->count();
        $query->delete();

        if ($count > 0) {
            $this->logActivity('setting.deleted', 'Log Aktivitas', "Membersihkan {$count} riwayat log aktivitas (Periode: {$validated['period']})");
        }

        return back()->with('success', "Berhasil membersihkan {$count} data log aktivitas.");
    }

    public function destroy(ActivityLog $activityLog)
    {
        $desc = "Menghapus log: [{$activityLog->module}] {$activityLog->description} ({$activityLog->user_name})";
        $activityLog->delete();

        $this->logActivity('setting.deleted', 'Log Aktivitas', $desc);

        return back()->with('success', 'Log aktivitas berhasil dihapus.');
    }
}
