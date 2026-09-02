<?php

namespace App\Http\Controllers\Staff;

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

        // HR Staff dapat melihat SELURUH akun yang dibuat Admin
        $accounts = SosmedAccount::with(['pmUser', 'staffUser', 'creator'])
            ->orderBy('platform')
            ->get();

        // Tugas Sosmed yang perlu Final Approval oleh HR Staff (sudah di-approve PM)
        $needHrApproval = SosmedTask::with(['account', 'assignedUser', 'assignedBy', 'verifiedBy'])
            ->where('status', 'verified_by_pm')
            ->orderBy('verified_at', 'desc')
            ->get();

        // Seluruh Tugas
        $allTasks = SosmedTask::with(['account', 'assignedUser', 'assignedBy', 'verifiedBy', 'hrVerifiedBy'])
            ->orderBy('task_date', 'desc')
            ->get();

        // Riwayat approval
        $approvalLogs = SosmedApprovalLog::with(['task.account', 'user'])
            ->latest()
            ->paginate(25);

        // List PM & Staff untuk distribusi
        $pms = User::join('roles', 'users.role', '=', 'roles.name')
            ->where('roles.name', 'pm')
            ->where('users.is_active', true)
            ->select('users.*')
            ->orderBy('users.name')
            ->get();

        $staffs = User::whereIn('role', ['sosmed', 'digital_marketing'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $stats = [
            'total_accounts' => $accounts->count(),
            'unassigned_pm'  => $accounts->whereNull('pm_id')->count(),
            'need_hr_verify' => $needHrApproval->count(),
            'total_tasks'    => $allTasks->count(),
            'completed'      => $allTasks->where('status', 'approved_hr')->count(),
        ];

        return view('staff.sosmed.index', compact(
            'tab', 'accounts', 'needHrApproval', 'allTasks', 'approvalLogs', 'pms', 'staffs', 'stats'
        ));
    }

    // HR Staff assign akun ke PM / Sosmed
    public function assignAccount(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'pm_id'    => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
        ]);

        $account->update($validated);

        return redirect()->route('staff.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Delegasi akun berhasil diperbarui.');
    }

    // Approval Level 2 (Final) oleh HR Staff
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
                'role_name'      => 'HR Staff',
                'action'         => 'approved_hr',
                'notes'          => 'Disetujui secara final oleh HR Staff.',
            ]);

            return redirect()->route('staff.sosmed.index', ['tab' => 'approvals'])
                ->with('success', 'Tugas berhasil disetujui secara final oleh HR Staff.');
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
                'role_name'      => 'HR Staff',
                'action'         => 'rejected',
                'notes'          => $validated['rejection_note'] ?? 'Ditolak oleh HR Staff',
            ]);

            return redirect()->route('staff.sosmed.index', ['tab' => 'approvals'])
                ->with('success', 'Tugas ditolak dan dikembalikan untuk perbaikan.');
        }
    }
}
