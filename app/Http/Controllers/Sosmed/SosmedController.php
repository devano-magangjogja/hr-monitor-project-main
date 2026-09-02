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
        $currentUserId = Auth::id();

        // Akun yang didelegasikan ke user ini oleh PM
        $accounts = SosmedAccount::with(['pmUser', 'creator'])
            ->where('staff_id', $currentUserId)
            ->orderBy('platform')
            ->get();

        $accountIds = $accounts->pluck('id');

        // Tugas hari ini per akun
        $todayTasks = SosmedTask::with(['verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $accountIds)
            ->whereDate('task_date', now()->toDateString())
            ->get()
            ->keyBy('sosmed_account_id');

        // Statistik hari ini
        $stats = [
            'total_accounts' => $accounts->count(),
            'pending_tasks'  => $accounts->filter(function ($acc) use ($todayTasks) {
                if (!isset($todayTasks[$acc->id])) return true;
                return in_array($todayTasks[$acc->id]->status, ['pending', 'rejected']);
            })->count(),
            'waiting_pm'     => $todayTasks->where('status', 'done_by_staff')->count(),
            'waiting_hr'     => $todayTasks->where('status', 'verified_by_pm')->count(),
            'approved_final' => $todayTasks->where('status', 'approved_hr')->count(),
        ];

        return view('sosmed.sosmed.index', compact('accounts', 'todayTasks', 'stats'));
    }

    /**
     * Sosmed staff submit bukti untuk akun yang ia pegang hari ini.
     * Menerima multi-link (array of URLs).
     */
    public function submitAccountTask(Request $request, SosmedAccount $account)
    {
        if ($account->staff_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan penanggung jawab akun ini.');
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

        // Cari atau buat tugas hari ini
        $task = SosmedTask::firstOrNew([
            'sosmed_account_id' => $account->id,
            'task_date'         => now()->toDateString(),
        ]);

        $task->fill([
            'assigned_to' => Auth::id(),
            'assigned_by' => $account->pm_id ?? $account->created_by ?? Auth::id(),
            'type'        => 'daily',
            'title'       => 'Laporan Konten - ' . $account->name,
            'link_upload' => $links,
            'description' => $validated['description'] ?? null,
            'status'      => 'done_by_staff',
        ]);
        $task->save();

        SosmedApprovalLog::create([
            'sosmed_task_id' => $task->id,
            'user_id'        => Auth::id(),
            'user_name'      => Auth::user()->name,
            'role_name'      => Auth::user()->role_label,
            'action'         => 'submitted',
            'notes'          => 'Bukti disubmit (' . count($links) . ' link). Menunggu verifikasi PM.',
        ]);

        return redirect()->route('sosmed.sosmed.index')
            ->with('success', $account->name . ' berhasil ditandai selesai dan diteruskan ke PM untuk verifikasi.');
    }
}
