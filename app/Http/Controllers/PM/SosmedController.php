<?php

namespace App\Http\Controllers\PM;

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
        $tab = $request->query('tab', 'tasks');
        $currentUserId = Auth::id();

        // 1. KEAMANAN AKSES DATA: PM HANYA BISA MELIHAT AKUN YANG DI-ASSIGN KE DIRINYA (pm_id = Auth::id())
        $accounts = SosmedAccount::with(['staffUser', 'creator'])
            ->where('pm_id', $currentUserId)
            ->orderBy('platform')
            ->get();

        $accountIds = $accounts->pluck('id');

        // 2. TUGAS SOSMED YANG BERADA DALAM TANGGUNG JAWAB PM INI
        $sosmedTasks = SosmedTask::with(['account', 'assignedUser', 'verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $accountIds)
            ->orderBy('task_date', 'desc')
            ->get();

        // 3. TUGAS MENUNGGU VERIFIKASI PM (Level 1)
        $pendingVerification = SosmedTask::with(['account', 'assignedUser'])
            ->whereIn('sosmed_account_id', $accountIds)
            ->where('status', 'done_by_staff')
            ->orderBy('updated_at', 'desc')
            ->get();

        // 4. RIWAYAT APPROVAL
        $approvalHistory = SosmedTask::with(['account', 'assignedUser', 'verifiedBy', 'hrVerifiedBy', 'logs'])
            ->whereIn('sosmed_account_id', $accountIds)
            ->whereIn('status', ['verified_by_pm', 'approved_hr', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // 5. LIST STAFF SOSMED
        $sosmedStaff = User::join('roles', 'users.role', '=', 'roles.name')
            ->where('roles.base_type', 'member')
            ->where('users.is_active', true)
            ->select('users.*')
            ->orderBy('users.name')
            ->get();

        // 6. STATISTIK PM
        $stats = [
            'total_accounts' => $accounts->count(),
            'unassigned_staff' => $accounts->whereNull('staff_id')->count(),
            'total_tasks'    => $sosmedTasks->count(),
            'pending_tasks'  => $sosmedTasks->where('status', 'pending')->count(),
            'need_pm_verify' => $pendingVerification->count(),
            'waiting_hr'     => $sosmedTasks->where('status', 'verified_by_pm')->count(),
            'approved_final' => $sosmedTasks->where('status', 'approved_hr')->count(),
            'rejected'       => $sosmedTasks->where('status', 'rejected')->count(),
        ];

        return view('pm.sosmed.index', compact(
            'tab', 'accounts', 'sosmedTasks', 'pendingVerification', 'approvalHistory', 'sosmedStaff', 'stats'
        ));
    }

    // PM membuat tugas untuk akun tanggung jawabnya (Daily / Custom)
    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'sosmed_account_id' => ['required', 'exists:sosmed_accounts,id'],
            'assigned_to'       => ['required', 'exists:users,id'],
            'type'              => ['required', 'in:daily,custom'],
            'title'             => ['required', 'string', 'max:200'],
            'description'       => ['nullable', 'string'],
            'task_date'         => ['required', 'date'],
            'deadline'          => ['nullable', 'date'],
        ]);

        // Verifikasi PM adalah penanggung jawab akun ini
        $account = SosmedAccount::where('id', $validated['sosmed_account_id'])
            ->where('pm_id', Auth::id())
            ->firstOrFail();

        SosmedTask::create([
            ...$validated,
            'assigned_by' => Auth::id(),
            'status'      => 'pending',
        ]);

        return redirect()->route('pm.sosmed.index', ['tab' => 'tasks'])
            ->with('success', 'Tugas sosmed berhasil dibuat.');
    }

    // PM melakukan Approval Level 1
    public function verifyTask(Request $request, SosmedTask $task)
    {
        // Pastikan akun milik PM yang login
        if ($task->account->pm_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan PM penanggung jawab akun ini.');
        }

        $validated = $request->validate([
            'action'         => ['required', 'in:verify,reject'],
            'rejection_note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['action'] === 'verify') {
            $task->update([
                'status'      => 'verified_by_pm',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
            ]);

            SosmedApprovalLog::create([
                'sosmed_task_id' => $task->id,
                'user_id'        => Auth::id(),
                'user_name'      => Auth::user()->name,
                'role_name'      => 'PM (Project Manager)',
                'action'         => 'approved_pm',
                'notes'          => 'Diverifikasi oleh PM. Menunggu persetujuan final HR Staff.',
            ]);

            return redirect()->route('pm.sosmed.index', ['tab' => 'approvals'])
                ->with('success', 'Tugas berhasil diverifikasi dan diteruskan ke HR Staff untuk approval final.');
        } else {
            $task->update([
                'status'         => 'rejected',
                'verified_by'    => Auth::id(),
                'verified_at'    => now(),
                'rejection_note' => $validated['rejection_note'],
            ]);

            SosmedApprovalLog::create([
                'sosmed_task_id' => $task->id,
                'user_id'        => Auth::id(),
                'user_name'      => Auth::user()->name,
                'role_name'      => 'PM (Project Manager)',
                'action'         => 'rejected',
                'notes'          => $validated['rejection_note'] ?? 'Ditolak oleh PM',
            ]);

            return redirect()->route('pm.sosmed.index', ['tab' => 'approvals'])
                ->with('success', 'Tugas ditolak dan dikembalikan ke staff.');
        }
    }

    // PM menugaskan akun ke Staff Sosmed
    public function assignAccount(Request $request, SosmedAccount $account)
    {
        if ($account->pm_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'staff_id' => ['nullable', 'exists:users,id'],
        ]);

        $account->update(['staff_id' => $validated['staff_id']]);

        return redirect()->route('pm.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Penanggung jawab staff sosmed berhasil diperbarui.');
    }

    public function destroyTask(SosmedTask $task)
    {
        if ($task->account->pm_id !== Auth::id()) {
            abort(403);
        }

        if (in_array($task->status, ['done_by_staff', 'verified_by_pm', 'approved_hr'])) {
            return back()->with('error', 'Tugas yang sudah diproses tidak dapat dihapus.');
        }

        $task->delete();
        return redirect()->route('pm.sosmed.index', ['tab' => 'tasks'])
            ->with('success', 'Tugas berhasil dihapus.');
    }
}
