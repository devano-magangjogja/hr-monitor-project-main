<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SosmedAccount;
use App\Models\SosmedApprovalLog;
use App\Models\SosmedTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SosmedController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'accounts');

        // Seluruh Akun Sosmed di Sistem
        $accounts = SosmedAccount::with(['pmUser', 'staffUser', 'creator'])
            ->orderBy('platform')
            ->get();

        // Seluruh Tugas Sosmed
        $tasks = SosmedTask::with(['account', 'assignedUser', 'assignedBy', 'verifiedBy', 'hrVerifiedBy'])
            ->orderBy('task_date', 'desc')
            ->get();

        // Seluruh Approval Logs (Audit Trail Lengkap)
        $logs = SosmedApprovalLog::with(['task.account', 'user'])
            ->latest()
            ->paginate(30);

        // List user untuk penugasan
        $pms = User::join('roles', 'users.role', '=', 'roles.name')
            ->where('roles.name', 'pm')
            ->where('users.is_active', true)
            ->select('users.*')
            ->orderBy('users.name')
            ->get();

        $staffs = User::join('roles', 'users.role', '=', 'roles.name')
            ->where('roles.base_type', 'member')
            ->where('users.is_active', true)
            ->select('users.*')
            ->orderBy('users.name')
            ->get();

        $stats = [
            'total_accounts' => $accounts->count(),
            'unassigned_pm'  => $accounts->whereNull('pm_id')->count(),
            'unassigned_staff' => $accounts->whereNull('staff_id')->count(),
            'total_tasks'    => $tasks->count(),
            'pending_tasks'  => $tasks->where('status', 'pending')->count(),
            'need_pm_verify' => $tasks->where('status', 'done_by_staff')->count(),
            'need_hr_verify' => $tasks->where('status', 'verified_by_pm')->count(),
            'completed'      => $tasks->where('status', 'approved_hr')->count(),
        ];

        return view('admin.sosmed.index', compact(
            'tab', 'accounts', 'tasks', 'logs', 'pms', 'staffs', 'stats'
        ));
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:200'],
            'platform' => ['required', 'string', 'max:50'],
            'link'     => ['nullable', 'url', 'max:500'],
            'pm_id'    => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes'    => ['nullable', 'string'],
        ]);

        SosmedAccount::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Akun sosial media baru berhasil ditambahkan.');
    }

    public function updateAccount(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:200'],
            'platform' => ['required', 'string', 'max:50'],
            'link'     => ['nullable', 'url', 'max:500'],
            'pm_id'    => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes'    => ['nullable', 'string'],
        ]);

        $account->update($validated);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Akun sosial media berhasil diperbarui.');
    }

    public function assignAccount(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'pm_id'    => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
        ]);

        $account->update($validated);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Penanggung jawab akun berhasil diperbarui.');
    }

    public function destroyAccount(SosmedAccount $account)
    {
        $account->delete();
        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Akun sosial media berhasil dihapus.');
    }
}
