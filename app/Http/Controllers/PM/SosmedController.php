<?php

namespace App\Http\Controllers\PM;

use App\Http\Controllers\Controller;
use App\Models\PmSosmedOversight;
use App\Models\SosmedAccount;
use App\Models\SosmedApprovalLog;
use App\Models\SosmedTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SosmedController extends Controller
{
    public function index(Request $request)
    {
        $tab           = $request->query('tab', 'accounts');
        $currentUserId = Auth::id();

        // ── Kapasitas 1: Akun Mandiri yang Dikelola Sendiri oleh PM (Eksekutor) ────
        $myAccounts = SosmedAccount::with(['creator'])
            ->where('staff_id', $currentUserId)
            ->orderBy('platform')
            ->get();

        $myAccountIds = $myAccounts->pluck('id');

        $todayTasks = SosmedTask::with(['verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $myAccountIds)
            ->whereDate('task_date', now()->toDateString())
            ->get()
            ->keyBy('sosmed_account_id');

        $accounts = $myAccounts; // Tab 1: Akun yang dikerjakan sendiri

        // ── Kapasitas 2: Akun & Staff yang Disupervisi oleh PM (Supervisor/Approver) ─
        // 1. Akun langsung di mana pm_id = me tapi dikerjakan staff lain
        $directSupervisedAccounts = SosmedAccount::with(['staffUser', 'creator'])
            ->where('pm_id', $currentUserId)
            ->where('staff_id', '!=', $currentUserId)
            ->whereNotNull('staff_id')
            ->get();

        // 2. Akun via pivot oversight (PM -> Sosmed staff)
        $oversightLinks = PmSosmedOversight::with(['sosmedUser'])
            ->where('pm_id', $currentUserId)
            ->get();

        $oversightStaffIds = $oversightLinks->pluck('sosmed_id')->filter();

        // Semua akun staff yang berada di bawah pengawasan PM ini
        $allSupervisedAccounts = SosmedAccount::with(['staffUser', 'creator'])
            ->where(function ($q) use ($currentUserId, $oversightStaffIds) {
                $q->where(function ($q2) use ($currentUserId) {
                    $q2->where('pm_id', $currentUserId)
                       ->where('staff_id', '!=', $currentUserId)
                       ->whereNotNull('staff_id');
                })->orWhere(function ($q2) use ($oversightStaffIds, $currentUserId) {
                    $q2->whereIn('staff_id', $oversightStaffIds)
                       ->where('staff_id', '!=', $currentUserId);
                });
            })
            ->orderBy('platform')
            ->get();

        $supervisedAccountIds = $allSupervisedAccounts->pluck('id')->unique();

        $supervisedTodayTasks = SosmedTask::whereIn('sosmed_account_id', $supervisedAccountIds)
            ->whereDate('task_date', now()->toDateString())
            ->get()
            ->keyBy('sosmed_account_id');

        // Oversight data grouping per staff user
        $oversightStaffUsers = $allSupervisedAccounts->pluck('staffUser')->filter()->unique('id');
        $oversightData = $oversightStaffUsers->map(function ($staffUser) use ($supervisedTodayTasks) {
            $userAccounts = SosmedAccount::where('staff_id', $staffUser->id)
                ->orderBy('platform')
                ->get();
            $userAccountIds = $userAccounts->pluck('id');

            $userTodayTasks = $supervisedTodayTasks->whereIn('sosmed_account_id', $userAccountIds);

            return [
                'sosmed_user'   => $staffUser,
                'accounts'      => $userAccounts,
                'today_tasks'   => $userTodayTasks->keyBy('sosmed_account_id'),
                'total'         => $userAccounts->count(),
                'done'          => $userTodayTasks->whereIn('status', ['done_by_staff', 'verified_by_pm', 'approved_hr'])->count(),
                'pending'       => $userAccounts->filter(function ($acc) use ($userTodayTasks) {
                    $t = $userTodayTasks->firstWhere('sosmed_account_id', $acc->id);
                    return !$t || in_array($t->status, ['pending', 'rejected']);
                })->count(),
            ];
        })->values();

        // ── Tab 3: Verifikasi Tugas Tim (Approval Level 1) ─────────────────
        // Tugas dari akun yang diawasi yang berstatus 'done_by_staff' dan bukan milik PM sendiri
        $pendingVerification = SosmedTask::with(['account.staffUser', 'assignedUser'])
            ->whereIn('sosmed_account_id', $supervisedAccountIds)
            ->where('assigned_to', '!=', $currentUserId)
            ->where('status', 'done_by_staff')
            ->orderBy('updated_at', 'desc')
            ->get();

        $approvalHistory = SosmedTask::with(['account.staffUser', 'assignedUser', 'verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $supervisedAccountIds)
            ->where('assigned_to', '!=', $currentUserId)
            ->whereIn('status', ['verified_by_pm', 'approved_hr', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // ── Stats ─────────────────────────────────────────────────────────
        $allSupervisedTasks = SosmedTask::whereIn('sosmed_account_id', $supervisedAccountIds)->get();

        $stats = [
            'total_accounts'    => $accounts->count(), // Akun mandiri yang dipegang sendiri
            'need_pm_verify'    => $pendingVerification->count(),
            'waiting_hr'        => $allSupervisedTasks->where('status', 'verified_by_pm')->count(),
            'approved_final'    => $allSupervisedTasks->where('status', 'approved_hr')->count(),
            'rejected'          => $allSupervisedTasks->where('status', 'rejected')->count(),
            'pending_today'     => $accounts->filter(function ($acc) use ($todayTasks) {
                if (!isset($todayTasks[$acc->id])) return true;
                return in_array($todayTasks[$acc->id]->status, ['pending', 'rejected']);
            })->count(),
            'oversight_count'   => $allSupervisedAccounts->count(),
        ];

        return view('pm.sosmed.index', compact(
            'tab', 'accounts', 'todayTasks',
            'oversightData', 'oversightLinks', 'allSupervisedAccounts',
            'pendingVerification', 'approvalHistory', 'stats'
        ));
    }

    /**
     * PM submits proof for an account they manage directly as Executor.
     * Goes straight to verified_by_pm (level-1 passed) and awaits final HR approval.
     */
    public function submitAccountTask(Request $request, SosmedAccount $account)
    {
        if ($account->staff_id !== Auth::id() && $account->pm_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan eksekutor akun ini.');
        }

        $validated = $request->validate([
            'links'       => ['required', 'array', 'min:1'],
            'links.*'     => ['required', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $links = array_values(array_filter($validated['links'], fn($l) => !empty(trim($l))));
        if (empty($links)) {
            return back()->withErrors(['links' => 'Minimal satu link bukti harus diisi.'])->withInput();
        }

        $task = SosmedTask::firstOrNew([
            'sosmed_account_id' => $account->id,
            'task_date'         => now()->toDateString(),
        ]);

        $task->fill([
            'assigned_to' => Auth::id(),
            'assigned_by' => Auth::id(),
            'type'        => 'daily',
            'title'       => 'Laporan Konten - ' . $account->name,
            'link_upload' => $links,
            'description' => $validated['description'] ?? null,
            'status'      => 'verified_by_pm',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);
        $task->save();

        SosmedApprovalLog::create([
            'sosmed_task_id' => $task->id,
            'user_id'        => Auth::id(),
            'user_name'      => Auth::user()->name,
            'role_name'      => 'PM (Project Manager)',
            'action'         => 'submitted',
            'notes'          => 'PM submit bukti langsung (' . count($links) . ' link). Langsung menunggu approval final HR Staff.',
        ]);

        return redirect()->route('pm.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Bukti konten untuk ' . $account->name . ' berhasil dikirim. Menunggu approval final HR Staff.');
    }

    /**
     * PM verifies a task submitted by a Sosmed user.
     * Allowed if PM is the direct account owner (pm_id) OR
     * the oversight PM for the Sosmed user who submitted.
     */
    public function verifyTask(Request $request, SosmedTask $task)
    {
        $currentUserId = Auth::id();

        // Direct account owner check
        $isDirect = $task->account->pm_id === $currentUserId;

        // Oversight PM check: is this PM assigned to oversee the sosmed user?
        $isOversight = false;
        if ($task->assigned_to) {
            $isOversight = PmSosmedOversight::where('pm_id', $currentUserId)
                ->where('sosmed_id', $task->assigned_to)
                ->exists();
        }

        if (!$isDirect && !$isOversight) {
            abort(403, 'Akses ditolak. Anda tidak memiliki wewenang untuk memverifikasi tugas ini.');
        }

        $validated = $request->validate([
            'action'         => ['required', 'in:verify,reject'],
            'rejection_note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['action'] === 'verify') {
            $task->update([
                'status'      => 'verified_by_pm',
                'verified_by' => $currentUserId,
                'verified_at' => now(),
            ]);

            SosmedApprovalLog::create([
                'sosmed_task_id' => $task->id,
                'user_id'        => $currentUserId,
                'user_name'      => Auth::user()->name,
                'role_name'      => 'PM (Project Manager)',
                'action'         => 'approved_pm',
                'notes'          => 'Diverifikasi oleh PM. Menunggu persetujuan final HR Staff.',
            ]);

            return redirect()->route('pm.sosmed.index', ['tab' => 'approvals'])
                ->with('success', 'Tugas berhasil diverifikasi dan diteruskan ke HR Staff.');
        } else {
            $task->update([
                'status'         => 'rejected',
                'verified_by'    => $currentUserId,
                'verified_at'    => now(),
                'rejection_note' => $validated['rejection_note'],
            ]);

            SosmedApprovalLog::create([
                'sosmed_task_id' => $task->id,
                'user_id'        => $currentUserId,
                'user_name'      => Auth::user()->name,
                'role_name'      => 'PM (Project Manager)',
                'action'         => 'rejected',
                'notes'          => $validated['rejection_note'] ?? 'Ditolak oleh PM',
            ]);

            return redirect()->route('pm.sosmed.index', ['tab' => 'approvals'])
                ->with('success', 'Tugas ditolak dan dikembalikan ke staff.');
        }
    }
}
