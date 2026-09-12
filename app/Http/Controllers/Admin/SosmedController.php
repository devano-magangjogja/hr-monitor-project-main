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

        $executors = User::whereIn('role', ['sosmed', 'pm', 'hr_staff', 'hr_assistant', 'digital_marketing'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $staffs = $executors; // compatibility

        // Tugas Sosmed yang dikerjakan oleh HR Staff dan menunggu verifikasi langsung Admin
        $staffPendingTasks = SosmedTask::with(['account', 'assignedUser', 'assignedBy'])
            ->where('status', 'done_by_staff')
            ->whereHas('assignedUser', fn($u) => $u->where('role', 'hr_staff'))
            ->orderBy('updated_at', 'desc')
            ->get();

        $stats = [
            'total_accounts'    => $accounts->count(),
            'unassigned_pm'     => $accounts->whereNull('pm_id')->count(),
            'unassigned_staff'  => $accounts->whereNull('staff_id')->count(),
            'total_tasks'       => $tasks->count(),
            'pending_tasks'     => $tasks->where('status', 'pending')->count(),
            'need_pm_verify'    => $tasks->where('status', 'done_by_staff')->count(),
            'need_admin_verify' => $staffPendingTasks->count(),
            'need_hr_verify'    => $tasks->where('status', 'verified_by_pm')->count(),
            'completed'         => $tasks->where('status', 'approved_hr')->count(),
        ];

        // Akun yang tersedia / belum ditugaskan sama sekali
        $availableAccounts = SosmedAccount::whereNull('staff_id')
            ->select('id', 'name', 'platform', 'link')
            ->orderBy('platform')
            ->orderBy('name')
            ->get();

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

        // Link akun paten (dikelola melalui menu Manajemen Akun) dan tidak boleh diubah dari form ini
        unset($validated['link']);

        $account->update($validated);
        $this->logActivity('sosmed.updated', 'Sosmed', "Memperbarui akun sosial media '{$account->name}'", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Akun sosial media berhasil diperbarui.');
    }

    public function assignTask(Request $request)
    {
        $validated = $request->validate([
            'sosmed_account_id' => ['required', 'exists:sosmed_accounts,id'],
            'staff_id'          => ['required', 'exists:users,id'],
            'pm_id'             => ['nullable', 'exists:users,id'],
            'assistant_id'      => ['nullable', 'exists:users,id'],
            'notes'             => ['nullable', 'string', 'max:1000'],
        ]);

        $account = SosmedAccount::findOrFail($validated['sosmed_account_id']);

        if (!empty($validated['staff_id'])) {
            $staff = User::find($validated['staff_id']);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $oldStaffId = $account->staff_id;

        $account->update([
            'staff_id'     => $validated['staff_id'],
            'pm_id'        => $validated['pm_id'] ?? null,
            'assistant_id' => $validated['assistant_id'] ?? null,
            'notes'        => $validated['notes'] ?? $account->notes,
        ]);

        if ($validated['staff_id'] && $validated['staff_id'] !== $oldStaffId) {
            $exists = SosmedTask::where('sosmed_account_id', $account->id)
                ->whereDate('task_date', now()->toDateString())
                ->where('assigned_to', $validated['staff_id'])
                ->exists();

            if (!$exists) {
                SosmedTask::create([
                    'sosmed_account_id' => $account->id,
                    'assigned_to'       => $validated['staff_id'],
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

        $this->logActivity('sosmed.assigned', 'Sosmed', "Memberikan tugas pengelolaan akun '{$account->name}' ({$account->platform})", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', "Tugas pengelolaan akun '{$account->name}' berhasil diberikan.");
    }

    public function assignAccount(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'pm_id'        => ['nullable', 'exists:users,id'],
            'assistant_id' => ['nullable', 'exists:users,id'],
            'staff_id'     => ['nullable', 'exists:users,id'],
        ]);

        if (!empty($validated['staff_id'])) {
            $staff = User::find($validated['staff_id']);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $oldStaffId = $account->staff_id;

        $account->update($validated);

        if (!empty($validated['staff_id']) && $validated['staff_id'] !== $oldStaffId) {
            $exists = SosmedTask::where('sosmed_account_id', $account->id)
                ->whereDate('task_date', now()->toDateString())
                ->where('assigned_to', $validated['staff_id'])
                ->exists();

            if (!$exists) {
                SosmedTask::create([
                    'sosmed_account_id' => $account->id,
                    'assigned_to'       => $validated['staff_id'],
                    'assigned_by'       => Auth::id(),
                    'type'              => 'daily',
                    'title'             => 'Laporan Konten Harian - ' . $account->name,
                    'description'       => null,
                    'link_upload'       => null,
                    'task_date'         => now()->toDateString(),
                    'status'            => 'pending',
                ]);
            }
        }

        $this->logActivity('sosmed.assigned', 'Sosmed', "Menugaskan penanggung jawab untuk akun '{$account->name}'", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Penanggung jawab akun berhasil diperbarui.');
    }

    public function unassignAccount(SosmedAccount $account)
    {
        $name = $account->name;
        $platform = $account->platform;

        $account->update([
            'staff_id'     => null,
            'pm_id'        => null,
            'assistant_id' => null,
        ]);

        $this->logActivity('sosmed.unassigned', 'Sosmed', "Melepas penugasan akun '{$name}' ({$platform})", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', "Penugasan akun '{$name}' berhasil dilepas. Akun kini kembali tersedia di dropdown penugasan.");
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
                ->with('success', 'Tugas sosmed Staff berhasil disetujui.');
        } else {
            $task->update([
                'status'         => 'rejected',
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
                ->with('success', 'Tugas ditolak dan dikembalikan ke Staff untuk revisi.');
        }
    }
}
