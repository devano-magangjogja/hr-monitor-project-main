<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    use LogsActivity;

    /**
     * Modul yang diizinkan untuk diakses oleh role Staff.
     */
    protected array $allowedModules = ['Manajemen Akun', 'Sosmed'];

    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Batasi hanya untuk modul Manajemen Akun dan Sosmed
        if ($request->filled('module') && in_array($request->input('module'), $this->allowedModules)) {
            $query->where('module', $request->input('module'));
        } else {
            $query->whereIn('module', $this->allowedModules);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('role')) {
            $query->where('user_role', $request->input('role'));
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
        $statsToday = ActivityLog::whereIn('module', $this->allowedModules)
            ->whereDate('created_at', $today)
            ->count();
        $statsTotal = ActivityLog::whereIn('module', $this->allowedModules)->count();
        $topModule  = ActivityLog::whereIn('module', $this->allowedModules)
            ->selectRaw('module, count(*) as cnt')
            ->groupBy('module')->orderByDesc('cnt')->value('module') ?? '-';
        $topUser    = ActivityLog::whereIn('module', $this->allowedModules)
            ->selectRaw('user_name, count(*) as cnt')
            ->groupBy('user_name')->orderByDesc('cnt')->value('user_name') ?? '-';

        $users   = User::orderBy('name')->get(['id', 'name', 'role']);
        $modules = collect($this->allowedModules);
        $roles   = User::distinct()->pluck('role')->sort()->values();

        return view('staff.activity-log.index', compact(
            'logs', 'users', 'modules', 'roles',
            'statsToday', 'statsTotal', 'topModule', 'topUser'
        ));
    }

    public function destroy(ActivityLog $activityLog)
    {
        if (!in_array($activityLog->module, $this->allowedModules)) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus log aktivitas di luar modul yang diizinkan.');
        }

        $desc = "Menghapus log: [{$activityLog->module}] {$activityLog->description} ({$activityLog->user_name})";
        $activityLog->delete();

        $this->logActivity('setting.deleted', 'Log Aktivitas', $desc);

        return back()->with('success', 'Log aktivitas berhasil dihapus.');
    }
}
