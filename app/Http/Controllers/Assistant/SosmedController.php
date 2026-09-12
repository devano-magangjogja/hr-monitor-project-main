<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Models\SosmedApprovalLog;
use App\Models\SosmedTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SosmedController extends Controller
{
    /**
     * Tampilkan semua tugas Sosmed yang menunggu verifikasi Level-1 (done_by_staff).
     * Asisten bisa approve sebagai pengganti PM jika PM tidak tersedia.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'pending');
        $currentUserId = Auth::id();

        // Ambil ID akun sosmed yang didelegasikan ke asisten ini oleh Staff/Admin
        $assignedAccountIds = \App\Models\SosmedAccount::where('assistant_id', $currentUserId)->pluck('id');

        // Tugas yang perlu diverifikasi: status done_by_staff HANYA dari akun yang wewenangnya diberikan ke asisten ini
        $pendingVerification = SosmedTask::with(['account.staffUser', 'account.pmUser', 'assignedUser', 'assignedBy'])
            ->whereIn('sosmed_account_id', $assignedAccountIds)
            ->where('status', 'done_by_staff')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Riwayat yang pernah di-approve oleh asisten ini
        $approvalHistory = SosmedApprovalLog::with(['task.account', 'user'])
            ->where('user_id', $currentUserId)
            ->latest()
            ->take(50)
            ->get();

        // Akun Mandiri yang Dikelola oleh HR Assistant (seperti PM)
        $myAccounts = \App\Models\SosmedAccount::with(['creator'])
            ->where('staff_id', $currentUserId)
            ->orderBy('platform')
            ->get();
        $myAccountIds = $myAccounts->pluck('id');

        $todayTasks = SosmedTask::with(['verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $myAccountIds)
            ->whereDate('task_date', now()->toDateString())
            ->get()
            ->keyBy('sosmed_account_id');

        // Statistik
        $stats = [
            'my_accounts'      => $myAccounts->count(),
            'my_pending_today' => $myAccounts->filter(function ($acc) use ($todayTasks) {
                if (!isset($todayTasks[$acc->id])) return true;
                return in_array($todayTasks[$acc->id]->status, ['pending', 'rejected']);
            })->count(),
            'pending'          => $pendingVerification->count(),
            'approved'         => SosmedTask::whereIn('sosmed_account_id', $assignedAccountIds)->where('status', 'verified_by_pm')->count(),
            'final_ok'         => SosmedTask::whereIn('sosmed_account_id', $assignedAccountIds)->where('status', 'approved_hr')->count(),
            'rejected'         => SosmedTask::whereIn('sosmed_account_id', $assignedAccountIds)->where('status', 'rejected')->count(),
        ];

        return view('assistant.sosmed.index', compact(
            'tab', 'myAccounts', 'todayTasks', 'pendingVerification', 'approvalHistory', 'stats'
        ));
    }

    /**
     * Asisten melakukan verifikasi Level-1 (pengganti PM).
     * Status berubah ke verified_by_pm, lalu diteruskan ke HR Staff untuk persetujuan final.
     */
    public function verifyTask(Request $request, SosmedTask $task)
    {
        // Pastikan akun ini berada di bawah wewenang asisten yang login
        if (!$task->account || $task->account->assistant_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak memiliki wewenang untuk memverifikasi tugas akun ini.']);
        }

        // Hanya boleh verifikasi tugas yang masih done_by_staff
        if ($task->status !== 'done_by_staff') {
            return back()->withErrors(['error' => 'Tugas ini tidak dalam status yang bisa diverifikasi.']);
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
                'role_name'      => 'HR Assistant (Pengganti PM)',
                'action'         => 'approved_pm',
                'notes'          => 'Diverifikasi oleh HR Assistant sebagai pengganti PM. Menunggu persetujuan final HR Staff.',
            ]);

            return redirect()->route('assistant.sosmed.index', ['tab' => 'pending'])
                ->with('success', 'Tugas berhasil diverifikasi dan diteruskan ke HR Staff untuk persetujuan final.');
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
                'role_name'      => 'HR Assistant (Pengganti PM)',
                'action'         => 'rejected',
                'notes'          => $validated['rejection_note'] ?? 'Ditolak oleh HR Assistant (Pengganti PM)',
            ]);

            return redirect()->route('assistant.sosmed.index', ['tab' => 'pending'])
                ->with('success', 'Tugas ditolak dan dikembalikan ke staff untuk diperbaiki.');
        }
    }

    /**
     * HR Assistant submit bukti pengerjaan konten sosmed mandiri (sama seperti PM).
     * Status langsung menjadi 'verified_by_pm' agar diverifikasi oleh HR Staff.
     */
    public function submitAccountTask(Request $request, \App\Models\SosmedAccount $account)
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
            'status'      => 'verified_by_pm', // Masuk ke antrean verifikasi HR Staff (seperti PM)
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);
        $task->save();

        SosmedApprovalLog::create([
            'sosmed_task_id' => $task->id,
            'user_id'        => Auth::id(),
            'user_name'      => Auth::user()->name,
            'role_name'      => 'HR Assistant',
            'action'         => 'submitted',
            'notes'          => 'HR Assistant submit bukti laporan sosmed (' . count($links) . ' link). Menunggu verifikasi oleh HR Staff.',
        ]);

        return redirect()->route('assistant.sosmed.index', ['tab' => 'my_accounts'])
            ->with('success', 'Bukti konten untuk ' . $account->name . ' berhasil dikirim. Menunggu verifikasi oleh HR Staff.');
    }
}
