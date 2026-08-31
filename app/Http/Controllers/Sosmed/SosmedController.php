<?php

namespace App\Http\Controllers\Sosmed;

use App\Http\Controllers\Controller;
use App\Models\SosmedAccount;
use App\Models\SosmedApprovalLog;
use App\Models\SosmedTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SosmedController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'tasks');
        $currentUserId = Auth::id();

        // 1. KEAMANAN AKSES: SOSMED HANYA MELIHAT AKUN YANG MENJADI TANGGUNG JAWABNYA (staff_id = Auth::id())
        $accounts = SosmedAccount::with(['pmUser', 'creator'])
            ->where('staff_id', $currentUserId)
            ->orderBy('platform')
            ->get();

        // 2. TUGAS SOSMED MILIK USER INI (Baik Harian maupun Custom)
        $myTasks = SosmedTask::with(['account.pmUser', 'assignedBy', 'verifiedBy', 'hrVerifiedBy'])
            ->where('assigned_to', $currentUserId)
            ->orderBy('task_date', 'desc')
            ->get();

        // 3. RIWAYAT PENGERJAAN & APPROVAL
        $historyTasks = SosmedTask::with(['account.pmUser', 'verifiedBy', 'hrVerifiedBy', 'logs'])
            ->where('assigned_to', $currentUserId)
            ->whereIn('status', ['done_by_staff', 'verified_by_pm', 'approved_hr', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $stats = [
            'total_accounts' => $accounts->count(),
            'pending_tasks'  => $myTasks->where('status', 'pending')->count(),
            'waiting_pm'     => $myTasks->where('status', 'done_by_staff')->count(),
            'waiting_hr'     => $myTasks->where('status', 'verified_by_pm')->count(),
            'approved_final' => $myTasks->where('status', 'approved_hr')->count(),
            'rejected'       => $myTasks->where('status', 'rejected')->count(),
        ];

        return view('sosmed.sosmed.index', compact(
            'tab', 'accounts', 'myTasks', 'historyTasks', 'stats'
        ));
    }

    // Sosmed menyelesaikan tugas dan submit link konten
    public function submitTask(Request $request, SosmedTask $task)
    {
        if ($task->assigned_to !== Auth::id()) {
            abort(403, 'Akses ditolak. Ini bukan tugas Anda.');
        }

        $validated = $request->validate([
            'link_upload' => ['required', 'url', 'max:500'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $task->update([
            'link_upload' => $validated['link_upload'],
            'status'      => 'done_by_staff',
        ]);

        SosmedApprovalLog::create([
            'sosmed_task_id' => $task->id,
            'user_id'        => Auth::id(),
            'user_name'      => Auth::user()->name,
            'role_name'      => Auth::user()->role_label,
            'action'         => 'submitted',
            'notes'          => 'Tugas selesai dikerjakan. Bukti URL: ' . $validated['link_upload'],
        ]);

        return redirect()->route('sosmed.sosmed.index', ['tab' => 'tasks'])
            ->with('success', 'Tugas berhasil diselesaikan dan diteruskan ke PM (' . ($task->account?->pmUser?->name ?? 'PM') . ') untuk verifikasi.');
    }
}
