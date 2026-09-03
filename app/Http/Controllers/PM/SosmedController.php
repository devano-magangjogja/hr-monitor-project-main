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

        // ── Tab 1: direct accounts (pm_id = me) ──────────────────────────
        $accounts = SosmedAccount::with(['staffUser', 'creator'])
            ->where('pm_id', $currentUserId)
            ->orderBy('platform')
            ->get();

        $accountIds = $accounts->pluck('id');

        $todayTasks = SosmedTask::with(['verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $accountIds)
            ->whereDate('task_date', now()->toDateString())
            ->get()
            ->keyBy('sosmed_account_id');

        // ── Tab 2: oversight — Sosmed users assigned to me via pivot ──────
        $oversightLinks = PmSosmedOversight::with(['sosmedUser'])
            ->where('pm_id', $currentUserId)
            ->get();

        // For each overseen Sosmed user, load their accounts + today's tasks
        $oversightData = $oversightLinks->map(function ($link) {
            $sosmedUser = $link->sosmedUser;
            if (!$sosmedUser) return null;

            $userAccounts = SosmedAccount::with(['pmUser'])
                ->where('staff_id', $sosmedUser->id)
                ->orderBy('platform')
                ->get();

            $userAccountIds = $userAccounts->pluck('id');

            $userTodayTasks = SosmedTask::whereIn('sosmed_account_id', $userAccountIds)
                ->whereDate('task_date', now()->toDateString())
                ->get()
                ->keyBy('sosmed_account_id');

            return [
                'sosmed_user'   => $sosmedUser,
                'accounts'      => $userAccounts,
                'today_tasks'   => $userTodayTasks,
                'total'         => $userAccounts->count(),
                'done'          => $userTodayTasks->whereIn('status', ['done_by_staff', 'verified_by_pm', 'approved_hr'])->count(),
                'pending'       => $userAccounts->filter(function ($acc) use ($userTodayTasks) {
                    return !isset($userTodayTasks[$acc->id])
                        || in_array($userTodayTasks[$acc->id]->status, ['pending', 'rejected']);
                })->count(),
            ];
        })->filter()->values();

        // ── Tab 3: verification — tasks from ALL accounts PM can verify ───
        // = direct accounts + accounts managed by overseen Sosmed users
        $oversightAccountIds = $oversightData->flatMap(fn($d) => $d['accounts']->pluck('id'));
        $allVerifiableIds    = $accountIds->merge($oversightAccountIds)->unique();

        $pendingVerification = SosmedTask::with(['account', 'assignedUser'])
            ->whereIn('sosmed_account_id', $allVerifiableIds)
            ->where('status', 'done_by_staff')
            ->orderBy('updated_at', 'desc')
            ->get();

        $approvalHistory = SosmedTask::with(['account', 'assignedUser', 'verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $allVerifiableIds)
            ->whereIn('status', ['verified_by_pm', 'approved_hr', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // ── Stats ─────────────────────────────────────────────────────────
        $allTasks = SosmedTask::whereIn('sosmed_account_id', $allVerifiableIds)->get();

        $stats = [
            'total_accounts'    => $accounts->count(),
            'need_pm_verify'    => $pendingVerification->count(),
            'waiting_hr'        => $allTasks->where('status', 'verified_by_pm')->count(),
            'approved_final'    => $allTasks->where('status', 'approved_hr')->count(),
            'rejected'          => $allTasks->where('status', 'rejected')->count(),
            'pending_today'     => $accounts->filter(function ($acc) use ($todayTasks) {
                if (!isset($todayTasks[$acc->id])) return true;
                return in_array($todayTasks[$acc->id]->status, ['pending', 'rejected']);
            })->count(),
            'oversight_count'   => $oversightLinks->count(),
        ];

        return view('pm.sosmed.index', compact(
            'tab', 'accounts', 'todayTasks',
            'oversightData', 'oversightLinks',
            'pendingVerification', 'approvalHistory', 'stats'
        ));
    }

    /**
     * PM submits proof for a direct account (pm_id = me).
     * Bypasses level-1 (PM is the verifier), goes straight to verified_by_pm.
     */
    public function submitAccountTask(Request $request, SosmedAccount $account)
    {
        if ($account->pm_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan PM penanggung jawab akun ini.');
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
