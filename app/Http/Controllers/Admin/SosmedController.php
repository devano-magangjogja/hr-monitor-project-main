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

        // Seluruh Akun Sosmed di Sistem Pemantauan Sosmed
        $accountSearch = $request->query('account_search');
        $accountsQuery = SosmedAccount::inSosmed()
            ->with(['pmUser', 'staffUsers', 'assistantUser', 'supervisorStaff', 'creator'])
            ->orderBy('platform');

        if ($accountSearch) {
            $accountsQuery->where('name', 'like', '%' . $accountSearch . '%');
        }

        // Get counts before pagination
        $totalAccountsCount = $accountsQuery->count();
        $accountsStats = [
            'total' => $totalAccountsCount,
            'unassigned_pm' => (clone $accountsQuery)->whereNull('pm_id')->count(),
            'unassigned_staff' => (clone $accountsQuery)->whereDoesntHave('staffUsers')->count(),
        ];

        $accounts = $accountsQuery->paginate(15)->appends($request->all());

        // Filter date for tasks
        $taskDateFilter = $request->query('task_date');
        if (!$taskDateFilter) {
            $taskDateFilter = now()->toDateString(); // By default, current date
        }

        // Seluruh Tugas Sosmed
        $tasksQuery = SosmedTask::with(['account', 'assignedUser', 'assignedBy', 'verifiedBy', 'hrVerifiedBy'])
            ->when($taskDateFilter, function ($q) use ($taskDateFilter) {
                $q->whereDate('task_date', $taskDateFilter);
            })
            ->orderBy('task_date', 'desc');

        // Get counts before pagination
        $totalTasksCount = $tasksQuery->count();
        $tasksStats = [
            'total' => $totalTasksCount,
            'pending' => (clone $tasksQuery)->where('status', 'pending')->count(),
            'done_by_staff' => (clone $tasksQuery)->where('status', 'done_by_staff')->count(),
            'verified_by_pm' => (clone $tasksQuery)->where('status', 'verified_by_pm')->count(),
            'approved_hr' => (clone $tasksQuery)->where('status', 'approved_hr')->count(),
        ];

        $tasks = $tasksQuery->paginate(15)->appends($request->all());

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

        // Staff Pengawas (Role HR Staff)
        $supervisors = User::where(function ($q) {
                $q->where('role', 'hr_staff')
                  ->orWhereHas('roleModel', fn($r) => $r->where('base_type', 'staff'));
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $executors = User::whereIn('role', ['sosmed', 'pm', 'hr_staff', 'hr_assistant', 'digital_marketing'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $staffs = $executors; // compatibility

        // Tugas Sosmed yang menunggu verifikasi langsung Admin (tugas yang dikerjakan Staff atau menunggu verifikasi pengawas/admin)
        $staffPendingTasks = SosmedTask::with(['account.supervisorStaff', 'account.pmUser', 'account.assistantUser', 'assignedUser', 'assignedBy'])
            ->where(function ($q) {
                $q->where('status', 'done_by_staff')
                    ->whereHas('account', fn($acc) => $acc->whereNull('pm_id')->whereNull('assistant_id')->whereNull('supervisor_staff_id'));
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        $stats = [
            'total_accounts'    => $accountsStats['total'],
            'unassigned_pm'     => $accountsStats['unassigned_pm'],
            'unassigned_staff'  => $accountsStats['unassigned_staff'],
            'total_tasks'       => $tasksStats['total'],
            'pending_tasks'     => $tasksStats['pending'],
            'need_pm_verify'    => $tasksStats['done_by_staff'],
            'need_admin_verify' => $staffPendingTasks->count(),
            'need_hr_verify'    => $tasksStats['verified_by_pm'],
            'completed'         => $tasksStats['approved_hr'],
        ];

        // Akun yang sudah disetujui (approved) untuk penugasan sosmed beserta info pengelola saat ini
        $availableAccounts = SosmedAccount::where('verification_status', 'approved')
            ->with(['staffUsers:id,name'])
            ->select('id', 'name', 'platform', 'link', 'is_in_sosmed')
            ->orderBy('platform')
            ->orderBy('name')
            ->get()
            ->map(function ($acc) {
                return [
                    'id' => $acc->id,
                    'name' => $acc->name,
                    'platform' => $acc->platform,
                    'link' => $acc->link,
                    'is_in_sosmed' => (bool)$acc->is_in_sosmed,
                    'managers_count' => $acc->staffUsers->count(),
                    'assigned_user_ids' => $acc->staffUsers->pluck('id')->values()->all(),
                ];
            });

        return view('admin.sosmed.index', compact(
            'tab',
            'accounts',
            'availableAccounts',
            'accountSearch',
            'tasks',
            'staffPendingTasks',
            'taskDateFilter',
            'logs',
            'logDateFilter',
            'logRangeFilter',
            'pms',
            'assistants',
            'supervisors',
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
            'supervisor_staff_id' => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $staffId = $validated['staff_id'] ?? null;
        unset($validated['staff_id']);

        if (!empty($staffId)) {
            $staff = User::find($staffId);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $account = SosmedAccount::create([
            ...$validated,
            'is_in_sosmed' => true,
            'created_by' => Auth::id(),
        ]);

        if (!empty($staffId)) {
            $account->staffUsers()->attach($staffId, [
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);
        }

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
            'supervisor_staff_id' => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $staffId = $validated['staff_id'] ?? null;
        unset($validated['staff_id']);
        unset($validated['link']);

        if (!empty($staffId)) {
            $staff = User::find($staffId);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $account->update($validated);

        if (!empty($staffId)) {
            if (!$account->staffUsers()->where('users.id', $staffId)->exists()) {
                $account->staffUsers()->attach($staffId, [
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                ]);
            }
        }

        $this->logActivity('sosmed.updated', 'Sosmed', "Memperbarui akun sosial media '{$account->name}'", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Akun sosial media berhasil diperbarui.');
    }

    public function assignTask(Request $request)
    {
        $validated = $request->validate([
            'sosmed_account_id'   => ['required', 'exists:sosmed_accounts,id'],
            'staff_id'            => ['nullable', 'exists:users,id'],
            'pm_id'               => ['nullable', 'exists:users,id'],
            'assistant_id'        => ['nullable', 'exists:users,id'],
            'supervisor_staff_id' => ['nullable', 'exists:users,id'],
            'notes'               => ['nullable', 'string', 'max:1000'],
        ]);

        $account = SosmedAccount::findOrFail($validated['sosmed_account_id']);

        if (!empty($validated['staff_id'])) {
            $staff = User::find($validated['staff_id']);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $accountData = [
            'is_in_sosmed' => true,
            'notes'        => $validated['notes'] ?? $account->notes,
        ];

        if (!empty($validated['pm_id'])) {
            $accountData['pm_id'] = $validated['pm_id'];
        }
        if (!empty($validated['assistant_id'])) {
            $accountData['assistant_id'] = $validated['assistant_id'];
        }
        if (!empty($validated['supervisor_staff_id'])) {
            $accountData['supervisor_staff_id'] = $validated['supervisor_staff_id'];
        }

        $account->update($accountData);

        if (!empty($validated['staff_id'])) {
            $staffId = $validated['staff_id'];
            if (!$account->staffUsers()->where('users.id', $staffId)->exists()) {
                $account->staffUsers()->attach($staffId, [
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                ]);
            }

            $exists = SosmedTask::where('sosmed_account_id', $account->id)
                ->whereDate('task_date', now()->toDateString())
                ->where('assigned_to', $staffId)
                ->exists();

            if (!$exists) {
                SosmedTask::create([
                    'sosmed_account_id' => $account->id,
                    'assigned_to'       => $staffId,
                    'assigned_by'       => Auth::id(),
                    'type'              => 'daily',
                    'title'             => 'Laporan Konten Harian - ' . $account->name,
                    'description'       => $validated['notes'] ?? null,
                    'link_upload'       => null,
                    'task_date'         => now()->toDateString(),
                    'status'            => 'pending',
                ]);
            }
        }

        $this->logActivity('sosmed.assigned', 'Sosmed', "Menambahkan akun '{$account->name}' ({$account->platform}) ke daftar pengelolaan sosmed", $account);

        $msg = !empty($validated['staff_id'])
            ? "Tugas pengelolaan akun '{$account->name}' berhasil diberikan."
            : "Akun '{$account->name}' berhasil ditambahkan ke daftar pengelolaan sosmed.";

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', $msg);
    }

    public function assignAccount(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'sosmed_account_id'   => ['nullable', 'exists:sosmed_accounts,id'],
            'pm_id'               => ['nullable', 'exists:users,id'],
            'assistant_id'        => ['nullable', 'exists:users,id'],
            'supervisor_staff_id' => ['nullable', 'exists:users,id'],
            'staff_id'            => ['nullable', 'exists:users,id'],
            'notes'               => ['nullable', 'string', 'max:1000'],
        ]);

        if (!empty($validated['staff_id'])) {
            $staff = User::find($validated['staff_id']);
            if ($staff && in_array($staff->role, ['pm', 'hr_staff'])) {
                $validated['pm_id'] = null;
                $validated['assistant_id'] = null;
            }
        }

        $targetAccount = $account;
        $isSwitched = false;

        // Jika user memilih akun lain dari dropdown di form edit
        if (!empty($validated['sosmed_account_id']) && (int)$validated['sosmed_account_id'] !== (int)$account->id) {
            $targetAccount = SosmedAccount::findOrFail($validated['sosmed_account_id']);
            $isSwitched = true;
        }

        $targetAccount->update([
            'pm_id'               => $validated['pm_id'] ?? null,
            'assistant_id'        => $validated['assistant_id'] ?? null,
            'supervisor_staff_id' => $validated['supervisor_staff_id'] ?? null,
            'notes'               => array_key_exists('notes', $validated) ? $validated['notes'] : $targetAccount->notes,
        ]);

        if (!empty($validated['staff_id'])) {
            $newStaffId = $validated['staff_id'];
            if (!$targetAccount->staffUsers()->where('users.id', $newStaffId)->exists()) {
                $targetAccount->staffUsers()->attach($newStaffId, [
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                ]);
            }

            $exists = SosmedTask::where('sosmed_account_id', $targetAccount->id)
                ->whereDate('task_date', now()->toDateString())
                ->where('assigned_to', $newStaffId)
                ->exists();

            if (!$exists) {
                SosmedTask::create([
                    'sosmed_account_id' => $targetAccount->id,
                    'assigned_to'       => $newStaffId,
                    'assigned_by'       => Auth::id(),
                    'type'              => 'daily',
                    'title'             => 'Laporan Konten Harian - ' . $targetAccount->name,
                    'description'       => $targetAccount->notes,
                    'link_upload'       => null,
                    'task_date'         => now()->toDateString(),
                    'status'            => 'pending',
                ]);
            }
        }

        $this->logActivity('sosmed.assigned', 'Sosmed', "Menugaskan penanggung jawab untuk akun '{$targetAccount->name}'", $targetAccount);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Penanggung jawab akun berhasil diperbarui.' . ($isSwitched ? ' Akun telah dialihkan ke ' . $targetAccount->name . '.' : ''));
    }

    public function unassignAccount(Request $request, SosmedAccount $account)
    {
        $name = $account->name;
        $platform = $account->platform;
        $userId = $request->input('user_id');

        if ($userId) {
            $account->staffUsers()->detach($userId);
            SosmedTask::where('sosmed_account_id', $account->id)
                ->where('assigned_to', $userId)
                ->where('status', 'pending')
                ->delete();

            $targetUser = User::find($userId);
            $userName = $targetUser ? $targetUser->name : "User #{$userId}";
            $this->logActivity('sosmed.unassigned', 'Sosmed', "Melepas akses user '{$userName}' dari akun '{$name}' ({$platform})", $account);

            return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
                ->with('success', "Akses user '{$userName}' untuk akun '{$name}' berhasil dilepas.");
        }

        $account->staffUsers()->detach();
        $account->update([
            'pm_id'               => null,
            'assistant_id'        => null,
            'supervisor_staff_id' => null,
        ]);

        SosmedTask::where('sosmed_account_id', $account->id)
            ->where('status', 'pending')
            ->delete();

        $this->logActivity('sosmed.unassigned', 'Sosmed', "Melepas seluruh penugasan akun '{$name}' ({$platform})", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', "Seluruh penugasan akun '{$name}' berhasil dilepas. Akun tetap berada di daftar kelola sosmed dengan status belum ditugaskan.");
    }

    public function destroyAccount(SosmedAccount $account)
    {
        $name = $account->name;
        $account->staffUsers()->detach();
        $account->update([
            'is_in_sosmed'        => false,
            'pm_id'               => null,
            'assistant_id'        => null,
            'supervisor_staff_id' => null,
        ]);

        SosmedTask::where('sosmed_account_id', $account->id)
            ->where('status', 'pending')
            ->delete();

        $this->logActivity('sosmed.deleted', 'Sosmed', "Menghapus akun '{$name}' dari daftar kelola sosmed");
        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', "Akun '{$name}' berhasil dihapus dari daftar pengelolaan sosmed. Data akun tetap tersimpan di Manajemen Akun.");
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

    /**
     * Admin verifikasi tugas sosmed yang dikerjakan oleh HR Staff.
     */
    public function verifyTask(Request $request, SosmedTask $task)
    {
        $validated = $request->validate([
            'action'         => ['required', 'in:verify,reject'],
            'rejection_note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['action'] === 'verify') {
            $task->update([
                'status'         => 'approved_hr',
                'verified_by'    => $task->verified_by ?? Auth::id(),
                'verified_at'    => $task->verified_at ?? now(),
                'hr_verified_by' => Auth::id(),
                'hr_verified_at' => now(),
            ]);

            SosmedApprovalLog::create([
                'sosmed_task_id' => $task->id,
                'user_id'        => Auth::id(),
                'user_name'      => Auth::user()->name,
                'role_name'      => 'Administrator',
                'action'         => 'approved_hr',
                'notes'          => 'Disetujui langsung oleh Administrator.',
            ]);

            $this->logActivity('sosmed.verified', 'Sosmed', "Administrator menyetujui tugas sosmed '{$task->title}' milik {$task->assignedUser?->name}", $task);

            return redirect()->route('admin.sosmed.index', ['tab' => 'staff_approvals'])
                ->with('success', 'Tugas sosmed berhasil disetujui oleh Administrator.');
        } else {
            $task->update([
                'status'         => 'rejected',
                'verified_by'    => $task->verified_by ?? Auth::id(),
                'verified_at'    => $task->verified_at ?? now(),
                'hr_verified_by' => Auth::id(),
                'hr_verified_at' => now(),
                'rejection_note' => $validated['rejection_note'],
            ]);

            SosmedApprovalLog::create([
                'sosmed_task_id' => $task->id,
                'user_id'        => Auth::id(),
                'user_name'      => Auth::user()->name,
                'role_name'      => 'Administrator',
                'action'         => 'rejected',
                'notes'          => $validated['rejection_note'] ?? 'Ditolak oleh Administrator.',
            ]);

            $this->logActivity('sosmed.verified', 'Sosmed', "Administrator menolak tugas sosmed '{$task->title}' milik {$task->assignedUser?->name}", $task);

            return redirect()->route('admin.sosmed.index', ['tab' => 'staff_approvals'])
                ->with('success', 'Tugas ditolak dan dikembalikan untuk revisi.');
        }
    }
}
