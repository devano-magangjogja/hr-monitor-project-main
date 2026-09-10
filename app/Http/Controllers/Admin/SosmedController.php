<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\SosmedAccount;
use App\Models\SosmedApprovalLog;
use App\Models\SosmedTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SosmedController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'accounts');

        // Seluruh Akun Sosmed di Sistem
        $accountSearch = $request->query('account_search');
        $accountsQuery = SosmedAccount::with(['pmUser', 'staffUser', 'assistantUser', 'creator'])
            ->orderBy('platform');

        if ($accountSearch) {
            $accountsQuery->where('name', 'like', '%' . $accountSearch . '%');
        }

        $accounts = $accountsQuery->get();

        // Filter date for tasks
        $taskDateFilter = $request->query('task_date');
        if (!$taskDateFilter) {
            $taskDateFilter = now()->toDateString(); // By default, current date
        }

        // Seluruh Tugas Sosmed
        $tasks = SosmedTask::with(['account', 'assignedUser', 'assignedBy', 'verifiedBy', 'hrVerifiedBy'])
            ->when($taskDateFilter, function ($q) use ($taskDateFilter) {
                $q->whereDate('task_date', $taskDateFilter);
            })
            ->orderBy('task_date', 'desc')
            ->get();

        // Date filter and time ranges for audit logs
        $logDateFilter = $request->query('log_date');
        $logRangeFilter = $request->query('log_range'); // 'weekly', 'monthly', 'yearly'
        $logSearch = $request->query('log_search');
        $logActionFilter = $request->query('log_action');

        // Seluruh Approval Logs (Audit Trail Lengkap)
        $logsQuery = SosmedApprovalLog::with(['task.account', 'user'])->latest();

        if ($logDateFilter) {
            $logsQuery->whereDate('created_at', $logDateFilter);
        } elseif ($logRangeFilter) {
            if ($logRangeFilter === 'weekly') {
                $logsQuery->where('created_at', '>=', now()->startOfWeek());
            } elseif ($logRangeFilter === 'monthly') {
                $logsQuery->where('created_at', '>=', now()->startOfMonth());
            } elseif ($logRangeFilter === 'yearly') {
                $logsQuery->where('created_at', '>=', now()->startOfYear());
            }
        }

        if ($logSearch) {
            $logsQuery->where(function ($q) use ($logSearch) {
                $q->where('user_name', 'like', '%' . $logSearch . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $logSearch . '%'));
            });
        }

        if ($logActionFilter) {
            $logsQuery->where('action', $logActionFilter);
        }

        $logs = $logsQuery->paginate(30)->appends($request->all());

        // List user untuk penugasan
        $pms = User::join('roles', 'users.role', '=', 'roles.name')
            ->where('roles.name', 'pm')
            ->where('users.is_active', true)
            ->select('users.*')
            ->orderBy('users.name')
            ->get();

        $assistants = User::where(function ($q) {
                $q->where('role', 'hr_assistant')
                  ->orWhereHas('roleModel', fn($r) => $r->where('base_type', 'assistant'));
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $executors = User::whereIn('role', ['sosmed', 'pm'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $staffs = $executors; // compatibility

        $stats = [
            'total_accounts' => $accounts->count(),
            'unassigned_pm' => $accounts->whereNull('pm_id')->count(),
            'unassigned_staff' => $accounts->whereNull('staff_id')->count(),
            'total_tasks' => $tasks->count(),
            'pending_tasks' => $tasks->where('status', 'pending')->count(),
            'need_pm_verify' => $tasks->where('status', 'done_by_staff')->count(),
            'need_hr_verify' => $tasks->where('status', 'verified_by_pm')->count(),
            'completed' => $tasks->where('status', 'approved_hr')->count(),
        ];

        return view('admin.sosmed.index', compact(
            'tab',
            'accounts',
            'accountSearch',
            'tasks',
            'taskDateFilter',
            'logs',
            'logDateFilter',
            'logRangeFilter',
            'pms',
            'assistants',
            'staffs',
            'executors',
            'stats'
        ));
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'platform' => ['required', 'string', 'max:50'],
            'link' => ['nullable', 'url', 'max:500'],
            'pm_id' => ['nullable', 'exists:users,id'],
            'assistant_id' => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        if (!empty($validated['staff_id'])) {
            $staff = User::find($validated['staff_id']);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $account = SosmedAccount::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);
        $this->logActivity('sosmed.created', 'Sosmed', "Menambahkan akun sosial media '{$validated['name']}' ({$validated['platform']})", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Akun sosial media baru berhasil ditambahkan.');
    }

    public function updateAccount(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'platform' => ['required', 'string', 'max:50'],
            'link' => ['nullable', 'url', 'max:500'],
            'pm_id' => ['nullable', 'exists:users,id'],
            'assistant_id' => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        if (!empty($validated['staff_id'])) {
            $staff = User::find($validated['staff_id']);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $account->update($validated);
        $this->logActivity('sosmed.updated', 'Sosmed', "Memperbarui akun sosial media '{$account->name}'", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Akun sosial media berhasil diperbarui.');
    }

    public function assignAccount(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'pm_id' => ['nullable', 'exists:users,id'],
            'assistant_id' => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
        ]);

        if (!empty($validated['staff_id'])) {
            $staff = User::find($validated['staff_id']);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $account->update($validated);
        $this->logActivity('sosmed.assigned', 'Sosmed', "Menugaskan penanggung jawab untuk akun '{$account->name}'", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Penanggung jawab akun berhasil diperbarui.');
    }

    public function destroyAccount(SosmedAccount $account)
    {
        $name = $account->name;
        $account->delete();
        $this->logActivity('sosmed.deleted', 'Sosmed', "Menghapus akun sosial media '{$name}'");
        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Akun sosial media berhasil dihapus.');
    }

    public function purgeTasks(Request $request)
    {
        $range = $request->input('range'); // 'weekly', 'monthly', 'yearly'

        $query = SosmedTask::query();
        if ($range === 'weekly') {
            $query->where('task_date', '<', now()->startOfWeek());
        } elseif ($range === 'monthly') {
            $query->where('task_date', '<', now()->startOfMonth());
        } elseif ($range === 'yearly') {
            $query->where('task_date', '<', now()->startOfYear());
        } else {
            return redirect()->back()->with('error', 'Rentang waktu tidak valid.');
        }

        $count = $query->count();
        $query->delete();
        $this->logActivity('sosmed.deleted', 'Sosmed', "Menghapus {$count} tugas sosmed lama (filter: {$range})");

        return redirect()->route('admin.sosmed.index', ['tab' => 'tasks'])
            ->with('success', "Berhasil menghapus {$count} tugas lama.");
    }

    public function purgeLogs(Request $request)
    {
        $range = $request->input('range'); // 'weekly', 'monthly', 'yearly'

        $query = SosmedApprovalLog::query();
        if ($range === 'weekly') {
            $query->where('created_at', '<', now()->startOfWeek());
        } elseif ($range === 'monthly') {
            $query->where('created_at', '<', now()->startOfMonth());
        } elseif ($range === 'yearly') {
            $query->where('created_at', '<', now()->startOfYear());
        } else {
            return redirect()->back()->with('error', 'Rentang waktu tidak valid.');
        }

        $count = $query->count();
        $query->delete();
        $this->logActivity('sosmed.deleted', 'Sosmed', "Menghapus {$count} log persetujuan lama (filter: {$range})");

        return redirect()->route('admin.sosmed.index', ['tab' => 'logs'])
            ->with('success', "Berhasil menghapus {$count} log persetujuan lama.");
    }
}
