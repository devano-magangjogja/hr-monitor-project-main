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

        // Statistik khusus akun di bawah wewenang asisten ini
        $stats = [
            'pending'   => $pendingVerification->count(),
            'approved'  => SosmedTask::whereIn('sosmed_account_id', $assignedAccountIds)->where('status', 'verified_by_pm')->count(),
            'final_ok'  => SosmedTask::whereIn('sosmed_account_id', $assignedAccountIds)->where('status', 'approved_hr')->count(),
            'rejected'  => SosmedTask::whereIn('sosmed_account_id', $assignedAccountIds)->where('status', 'rejected')->count(),
        ];

        return view('assistant.sosmed.index', compact(
            'tab', 'pendingVerification', 'approvalHistory', 'stats'
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
}
