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
        $tab = $request->query('tab', 'accounts');
        $currentUserId = Auth::id();

        // Akun yang di-assign ke PM ini
        $accounts = SosmedAccount::with(['staffUser', 'creator'])
            ->where('pm_id', $currentUserId)
            ->orderBy('platform')
            ->get();

        $accountIds = $accounts->pluck('id');

        // Tugas hari ini per akun (untuk tampilan submit di tab accounts)
        $todayTasks = SosmedTask::with(['verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $accountIds)
            ->whereDate('task_date', now()->toDateString())
            ->get()
            ->keyBy('sosmed_account_id');

        // Tugas menunggu verifikasi PM (done_by_staff = dikirim oleh role Sosmed)
        $pendingVerification = SosmedTask::with(['account', 'assignedUser'])
            ->whereIn('sosmed_account_id', $accountIds)
            ->where('status', 'done_by_staff')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Riwayat approval
        $approvalHistory = SosmedTask::with(['account', 'assignedUser', 'verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $accountIds)
            ->whereIn('status', ['verified_by_pm', 'approved_hr', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // List staff sosmed untuk assign
        $sosmedStaff = User::whereIn('role', ['sosmed', 'digital_marketing'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Statistik
        $allTasks = SosmedTask::whereIn('sosmed_account_id', $accountIds)->get();
        $stats = [
            'total_accounts'   => $accounts->count(),
            'unassigned_staff' => $accounts->whereNull('staff_id')->count(),
            'need_pm_verify'   => $pendingVerification->count(),
            'waiting_hr'       => $allTasks->where('status', 'verified_by_pm')->count(),
            'approved_final'   => $allTasks->where('status', 'approved_hr')->count(),
            'rejected'         => $allTasks->where('status', 'rejected')->count(),
            // Akun yang belum diurus hari ini oleh PM sendiri
            'pending_today'    => $accounts->filter(function ($acc) use ($todayTasks) {
                if (!isset($todayTasks[$acc->id])) return true;
                return in_array($todayTasks[$acc->id]->status, ['pending', 'rejected']);
            })->count(),
        ];

        return view('pm.sosmed.index', compact(
            'tab', 'accounts', 'todayTasks', 'pendingVerification',
            'approvalHistory', 'sosmedStaff', 'stats'
        ));
    }

    /**
     * PM submit bukti untuk akun yang ia kerjakan sendiri.
     * Karena PM adalah pemilik akun, tugas langsung masuk ke verified_by_pm
     * (skip level-1 PM verify, langsung antri ke HR Staff).
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

        // Hapus entri kosong
        $links = array_values(array_filter($validated['links'], fn($l) => !empty(trim($l))));
        if (empty($links)) {
            return back()->withErrors(['links' => 'Minimal satu link bukti harus diisi.'])->withInput();
        }

        $task = SosmedTask::firstOrNew([
            'sosmed_account_id' => $account->id,
            'task_date'         => now()->toDateString(),
        ]);

        $task->fill([
            'assigned_to' => Auth::id(),   // PM sendiri yang mengerjakan
            'assigned_by' => Auth::id(),
            'type'        => 'daily',
            'title'       => 'Laporan Konten - ' . $account->name,
            'link_upload' => $links,
            'description' => $validated['description'] ?? null,
            // PM langsung ke verified_by_pm karena dia sendiri yang approve level 1
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
     * PM assign role Sosmed sebagai penanggung jawab akun.
     * Sekaligus generate/update tugas harian otomatis untuk hari ini
     * jika akun ini belum ada tugasnya hari ini untuk staff yang dipilih.
     */
    public function assignAccount(Request $request, SosmedAccount $account)
    {
        if ($account->pm_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'staff_id' => ['nullable', 'exists:users,id'],
        ]);

        $oldStaffId = $account->staff_id;
        $newStaffId = $validated['staff_id'] ?: null;

        $account->update(['staff_id' => $newStaffId]);

        // Auto-generate tugas harian untuk staff baru jika belum ada hari ini
        if ($newStaffId && $newStaffId !== $oldStaffId) {
            $exists = SosmedTask::where('sosmed_account_id', $account->id)
                ->whereDate('task_date', now()->toDateString())
                ->where('assigned_to', $newStaffId)
                ->exists();

            if (!$exists) {
                SosmedTask::create([
                    'sosmed_account_id' => $account->id,
                    'assigned_to'       => $newStaffId,
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

        return redirect()->route('pm.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Penanggung jawab staff sosmed berhasil diperbarui' . ($newStaffId ? ' dan tugas harian otomatis dibuat.' : '.'));
    }

    /**
     * PM verifikasi level-1 tugas yang dikirim oleh role Sosmed.
     */
    public function verifyTask(Request $request, SosmedTask $task)
    {
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
}
