<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\PmSosmedOversight;
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

        // All accounts (Staff sees everything)
        $accounts = SosmedAccount::with(['pmUser', 'staffUser', 'assistantUser', 'creator'])
            ->orderBy('platform')
            ->get();

        // Tasks needing final HR approval (tugas yang diverifikasi HR Staff, bukan tugas staff sendiri)
        $needHrApproval = SosmedTask::with(['account', 'assignedUser', 'assignedBy', 'verifiedBy'])
            ->where('status', 'verified_by_pm')
            ->where('assigned_to', '!=', Auth::id())
            ->orderBy('verified_at', 'desc')
            ->get();

        // All tasks for monitoring
        $allTasks = SosmedTask::with(['account', 'assignedUser', 'assignedBy', 'verifiedBy', 'hrVerifiedBy'])
            ->orderBy('task_date', 'desc')
            ->get();

        // Akun Mandiri yang Dikelola oleh Staff yang sedang login
        $myAccounts = SosmedAccount::with(['creator'])
            ->where('staff_id', Auth::id())
            ->orderBy('platform')
            ->get();
        $myAccountIds = $myAccounts->pluck('id');

        $todayTasks = SosmedTask::with(['verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $myAccountIds)
            ->whereDate('task_date', now()->toDateString())
            ->get()
            ->keyBy('sosmed_account_id');

        // Approval logs
        $approvalLogs = SosmedApprovalLog::with(['task.account', 'user'])
            ->latest()
            ->paginate(25);

        // PM list for account assignment
        $pms = User::join('roles', 'users.role', '=', 'roles.name')
            ->where('roles.name', 'pm')
            ->where('users.is_active', true)
            ->select('users.*')
            ->orderBy('users.name')
            ->get();

        // Asisten HR list for account assignment & approval authority
        $assistants = User::where(function ($q) {
                $q->where('role', 'hr_assistant')
                  ->orWhereHas('roleModel', fn($r) => $r->where('base_type', 'assistant'));
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Eksekutor Akun: Staff Sosmed, Digital Marketing, PM, dan HR Assistant
        // HR Staff tidak termasuk — Staff tidak bisa mendelegasikan ke sesama Staff
        $executors = User::whereIn('role', ['sosmed', 'pm', 'hr_assistant', 'digital_marketing'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $staffs = $executors;

        $stats = [
            'total_accounts'   => $accounts->count(),
            'my_accounts'      => $myAccounts->count(),
            'unassigned_pm'    => $accounts->whereNull('pm_id')->count(),
            'need_hr_verify'   => $needHrApproval->count(),
            'total_tasks'      => $allTasks->count(),
            'completed'        => $allTasks->where('status', 'approved_hr')->count(),
            'my_pending_today' => $myAccounts->filter(function ($acc) use ($todayTasks) {
                if (!isset($todayTasks[$acc->id])) return true;
                return in_array($todayTasks[$acc->id]->status, ['pending', 'rejected']);
            })->count(),
        ];

        return view('staff.sosmed.index', compact(
            'tab',
            'accounts',
            'myAccounts',
            'todayTasks',
            'needHrApproval',
            'allTasks',
            'approvalLogs',
            'pms',
            'assistants',
            'staffs',
            'executors',
            'stats'
        ));
    }

    /**
     * Staff submit bukti pengerjaan konten sosmed mandiri.
     * Status menjadi 'done_by_staff', dan diverifikasi langsung oleh Admin.
     */
    public function submitAccountTask(Request $request, SosmedAccount $account)
    {
        if ($account->staff_id !== Auth::id()) {
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
            'status'      => 'done_by_staff', // Menunggu verifikasi langsung oleh Admin
            'verified_by' => null,
            'verified_at' => null,
        ]);
        $task->save();

        SosmedApprovalLog::create([
            'sosmed_task_id' => $task->id,
            'user_id'        => Auth::id(),
            'user_name'      => Auth::user()->name,
            'role_name'      => 'HR Staff',
            'action'         => 'submitted',
            'notes'          => 'HR Staff submit bukti laporan sosmed (' . count($links) . ' link). Menunggu verifikasi langsung oleh Admin.',
        ]);

        $this->logActivity('sosmed.submitted', 'Sosmed', "Submit bukti laporan sosmed untuk akun '{$account->name}'", $task);

        return redirect()->route('staff.sosmed.index', ['tab' => 'my_accounts'])
            ->with('success', 'Bukti konten untuk ' . $account->name . ' berhasil dikirim. Menunggu verifikasi langsung oleh Admin.');
    }

    /**
     * HR Staff assign pm_id, assistant_id, AND staff_id on a SosmedAccount.
     *
     * Enforcement rule: if a staff_id is selected, the pm_id MUST match
     * whoever oversees that sosmed user in pm_sosmed_oversight.
     * If the chosen sosmed user has no oversight PM yet, pm_id is set freely.
     */
    public function assignAccount(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'pm_id' => ['nullable', 'exists:users,id'],
            'assistant_id' => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
        ]);

        $newStaffId = $validated['staff_id'] ?? null;
        $newPmId = $validated['pm_id'] ?? null;
        $newAssistantId = $validated['assistant_id'] ?? null;

        // Enforce: if a sosmed user is selected and they have an oversight PM,
        // the pm_id on this account must match that oversight PM.
        if ($newStaffId) {
            $oversight = PmSosmedOversight::where('sosmed_id', $newStaffId)->first();
            if ($oversight) {
                $newPmId = $oversight->pm_id; // auto-correct to the oversight PM
            }
        }

        $oldStaffId = $account->staff_id;

        $account->update([
            'pm_id' => $newPmId,
            'assistant_id' => $newAssistantId,
            'staff_id' => $newStaffId,
        ]);

        // Auto-generate today's daily task for the newly assigned sosmed user
        if ($newStaffId && $newStaffId !== $oldStaffId) {
            $exists = SosmedTask::where('sosmed_account_id', $account->id)
                ->whereDate('task_date', now()->toDateString())
                ->where('assigned_to', $newStaffId)
                ->exists();

            if (!$exists) {
                SosmedTask::create([
                    'sosmed_account_id' => $account->id,
                    'assigned_to' => $newStaffId,
                    'assigned_by' => Auth::id(),
                    'type' => 'daily',
                    'title' => 'Laporan Konten Harian - ' . $account->name,
                    'description' => null,
                    'link_upload' => null,
                    'task_date' => now()->toDateString(),
                    'status' => 'pending',
                ]);
            }
        }

        $this->logActivity('sosmed.assigned', 'Sosmed', "Mengatur penugasan akun '{$account->name}'", $account);

        return redirect()->route('staff.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Delegasi akun berhasil diperbarui.' . ($newStaffId && $newStaffId !== $oldStaffId ? ' Tugas harian otomatis dibuat.' : ''));
    }

    /**
     * Create or update the PM → Sosmed user oversight link.
     * Accepts: pm_id (nullable) and sosmed_id.
     * Passing pm_id = null removes the oversight for that sosmed user.
     */
    public function assignOversight(Request $request)
    {
        $validated = $request->validate([
            'sosmed_id' => ['required', 'exists:users,id'],
            'pm_id' => ['nullable', 'exists:users,id'],
        ]);

        $sosmedId = $validated['sosmed_id'];
        $pmId = $validated['pm_id'] ?? null;

        if ($pmId) {
            // Upsert: one sosmed user → one PM
            PmSosmedOversight::updateOrCreate(
                ['sosmed_id' => $sosmedId],
                ['pm_id' => $pmId, 'created_by' => Auth::id()]
            );

            // Also update pm_id on all SosmedAccounts managed by this sosmed user
            // to keep account-level pm_id in sync with the oversight PM.
            SosmedAccount::where('staff_id', $sosmedId)
                ->update(['pm_id' => $pmId]);
        } else {
            // Remove oversight link
            PmSosmedOversight::where('sosmed_id', $sosmedId)->delete();
        }

        $sosmedUser = User::find($sosmedId);
        $pmUser = $pmId ? User::find($pmId) : null;
        $this->logActivity('sosmed.updated', 'Sosmed', 
            $pmId ? "Menetapkan PM '{$pmUser?->name}' untuk oversight sosmed '{$sosmedUser?->name}'" 
                  : "Menghapus oversight PM untuk sosmed '{$sosmedUser?->name}'"
        );

        return redirect()->route('staff.sosmed.index', ['tab' => 'oversight'])
            ->with('success', 'Pengaturan oversight PM berhasil disimpan.');
    }

    /**
     * Final approval (Level 2) by HR Staff.
     */
    public function verifyTask(Request $request, SosmedTask $task)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:verify,reject'],
            'rejection_note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['action'] === 'verify') {
            $task->update([
                'status' => 'approved_hr',
                'hr_verified_by' => Auth::id(),
                'hr_verified_at' => now(),
            ]);

            SosmedApprovalLog::create([
                'sosmed_task_id' => $task->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'role_name' => 'HR Staff',
                'action' => 'approved_hr',
                'notes' => 'Disetujui secara final oleh HR Staff.',
            ]);

            $this->logActivity('sosmed.verified', 'Sosmed', "Menyetujui tugas sosmed '{$task->title}'", $task);

            return redirect()->route('staff.sosmed.index', ['tab' => 'approvals'])
                ->with('success', 'Tugas berhasil disetujui secara final oleh HR Staff.');
        } else {
            $task->update([
                'status' => 'rejected',
                'hr_verified_by' => Auth::id(),
                'hr_verified_at' => now(),
                'rejection_note' => $validated['rejection_note'],
            ]);

            SosmedApprovalLog::create([
                'sosmed_task_id' => $task->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'role_name' => 'HR Staff',
                'action' => 'rejected',
                'notes' => $validated['rejection_note'] ?? 'Ditolak oleh HR Staff',
            ]);

            $this->logActivity('sosmed.verified', 'Sosmed', "Menolak tugas sosmed '{$task->title}' dengan catatan: {$validated['rejection_note']}", $task);

            return redirect()->route('staff.sosmed.index', ['tab' => 'approvals'])
                ->with('success', 'Tugas ditolak dan dikembalikan untuk perbaikan.');
        }
    }
}
