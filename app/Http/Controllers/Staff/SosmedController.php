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
        $accountSearch = $request->query('account_search');
        $searchType = $request->query('search_type', 'all');
        // Akun yang bisa dikelola Staff: exclude akun yang eksekutornya HR Staff
        // (akun tersebut sudah ditetapkan langsung oleh Admin, tersimpan di tab "Tugas Sosmed Saya")
        $accountsQuery = SosmedAccount::inSosmed()
            ->with(['pmUser', 'staffUsers', 'assistantUser', 'supervisorStaff', 'creator'])
            ->where(function ($q) {
                $q->whereDoesntHave('staffUsers')
                  ->orWhereHas('staffUsers', fn($u) => $u->where('role', '!=', 'hr_staff'));
            });

        if ($accountSearch) {
            $term = trim($accountSearch);
            $accountsQuery->where(function ($q) use ($term, $searchType) {
                if ($searchType === 'account') {
                    $q->where('name', 'like', '%' . $term . '%')
                      ->orWhere('username', 'like', '%' . $term . '%');
                } elseif ($searchType === 'manager') {
                    $q->whereHas('staffUsers', fn($sq) => $sq->where('users.name', 'like', '%' . $term . '%'));
                } elseif ($searchType === 'pm') {
                    $q->whereHas('pmUser', fn($pq) => $pq->where('users.name', 'like', '%' . $term . '%'));
                } elseif ($searchType === 'assistant') {
                    $q->whereHas('assistantUser', fn($aq) => $aq->where('users.name', 'like', '%' . $term . '%'));
                } elseif ($searchType === 'staff') {
                    $q->whereHas('supervisorStaff', fn($sq) => $sq->where('users.name', 'like', '%' . $term . '%'));
                } else {
                    // 'all': Cari di nama akun, username, pengelola/eksekutor, PM, asisten, maupun staff pengawas
                    $q->where('name', 'like', '%' . $term . '%')
                      ->orWhere('username', 'like', '%' . $term . '%')
                      ->orWhereHas('staffUsers', fn($sq) => $sq->where('users.name', 'like', '%' . $term . '%'))
                      ->orWhereHas('pmUser', fn($pq) => $pq->where('users.name', 'like', '%' . $term . '%'))
                      ->orWhereHas('assistantUser', fn($aq) => $aq->where('users.name', 'like', '%' . $term . '%'))
                      ->orWhereHas('supervisorStaff', fn($sq) => $sq->where('users.name', 'like', '%' . $term . '%'));
                }
            });
        }

        $accounts = $accountsQuery->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->all());

        // Tasks needing HR approval:
        // 1. Tugas level 2: sudah diverifikasi PM / Asisten (verified_by_pm)
        // 2. ATAU: Tugas level 1 (done_by_staff) pada akun di mana Staff ini adalah Staff Pengawas (supervisor_staff_id = Auth::id())
        $needHrApproval = SosmedTask::with(['account.supervisorStaff', 'account.pmUser', 'account.assistantUser', 'assignedUser', 'assignedBy', 'verifiedBy'])
            ->where(function ($q) {
                $q->where('status', 'verified_by_pm')
                  ->orWhere(function ($sub) {
                    $sub->where('status', 'done_by_staff')
                        ->whereHas('account', function ($acc) {
                            $acc->whereNull('pm_id')
                                ->whereNull('assistant_id')
                                ->where(function ($verifier) {
                                    $verifier->whereNull('supervisor_staff_id')
                                        ->orWhere('supervisor_staff_id', Auth::id());
                                });
                        });
                });
            })
            ->where('assigned_to', '!=', Auth::id());

        $verifySearch = trim((string) $request->query('verify_search', ''));
        if ($verifySearch !== '') {
            $needHrApproval->where(function ($q) use ($verifySearch) {
                $q->where('title', 'like', '%' . $verifySearch . '%')
                  ->orWhereHas('account', fn($a) => $a->where('name', 'like', '%' . $verifySearch . '%')
                      ->orWhere('platform', 'like', '%' . $verifySearch . '%')
                      ->orWhere('brand', 'like', '%' . $verifySearch . '%'))
                  ->orWhereHas('assignedUser', fn($u) => $u->where('name', 'like', '%' . $verifySearch . '%'));
            });
        }

        $needHrApproval = $needHrApproval
            ->orderByRaw("CASE WHEN status = 'done_by_staff' THEN 0 ELSE 1 END")
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        // All tasks for monitoring
        $taskSearch = trim((string) $request->query('task_search', ''));
        $allTasks = SosmedTask::with(['account', 'assignedUser', 'assignedBy', 'verifiedBy', 'hrVerifiedBy'])
            ->when($taskSearch !== '', function ($q) use ($taskSearch) {
                $q->where(function ($qq) use ($taskSearch) {
                    $qq->where('title', 'like', '%' . $taskSearch . '%')
                      ->orWhereHas('account', fn($a) => $a->where('name', 'like', '%' . $taskSearch . '%')
                          ->orWhere('platform', 'like', '%' . $taskSearch . '%')
                          ->orWhere('brand', 'like', '%' . $taskSearch . '%'))
                      ->orWhereHas('assignedUser', fn($u) => $u->where('name', 'like', '%' . $taskSearch . '%'))
                      ->orWhereHas('verifiedBy', fn($u) => $u->where('name', 'like', '%' . $taskSearch . '%'));
                });
            })
            ->orderBy('task_date', 'desc')
            ->paginate(15)
            ->appends($request->all());

        // Akun Mandiri yang Dikelola oleh Staff yang sedang login
        $myAccountSearch = trim((string) $request->query('my_account_search', ''));
        $myAccounts = SosmedAccount::with(['creator'])
            ->whereHas('staffUsers', fn($q) => $q->where('users.id', Auth::id()))
            ->when($myAccountSearch !== '', function ($q) use ($myAccountSearch) {
                $q->where(function ($qq) use ($myAccountSearch) {
                    $qq->where('name', 'like', '%' . $myAccountSearch . '%')
                      ->orWhere('username', 'like', '%' . $myAccountSearch . '%')
                      ->orWhere('platform', 'like', '%' . $myAccountSearch . '%')
                      ->orWhere('brand', 'like', '%' . $myAccountSearch . '%');
                });
            })
            ->orderBy('platform')
            ->paginate(15)
            ->appends($request->all());
        $myAccountIds = $myAccounts->pluck('id');

        $todayTasks = SosmedTask::with(['verifiedBy', 'hrVerifiedBy'])
            ->whereIn('sosmed_account_id', $myAccountIds)
            ->where('assigned_to', Auth::id())
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

        // List Staff Pengawas (Semua user role HR Staff aktif)
        $supervisors = User::where(function ($q) {
                $q->where('role', 'hr_staff')
                  ->orWhereHas('roleModel', fn($r) => $r->where('base_type', 'staff'));
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
            'my_accounts'      => $myAccounts->total(),
            'unassigned_pm'    => $accounts->whereNull('pm_id')->count(),
            'need_hr_verify'   => $needHrApproval->total(),
            'total_tasks'      => $allTasks->total(),
            'completed'        => $allTasks->where('status', 'approved_hr')->count(),
            'my_pending_today' => $myAccounts->filter(function ($acc) use ($todayTasks) {
                if (!isset($todayTasks[$acc->id])) return true;
                return in_array($todayTasks[$acc->id]->status, ['pending', 'rejected']);
            })->count(),
        ];

        // Akun yang sudah disetujui (approved) untuk penugasan sosmed beserta info pengelola saat ini
        $availableAccounts = SosmedAccount::where('verification_status', 'approved')
            ->with(['staffUsers:id,name'])
            ->select('id', 'name', 'platform', 'link', 'is_in_sosmed')
            ->orderBy('platform')
            ->orderBy('name')
            ->get()
            ->map(function ($acc) {
                return [
                    'id' => $acc->id,
                    'name' => $acc->name,
                    'platform' => $acc->platform,
                    'link' => $acc->link,
                    'is_in_sosmed' => (bool)$acc->is_in_sosmed,
                    'managers_count' => $acc->staffUsers->count(),
                    'assigned_user_ids' => $acc->staffUsers->pluck('id')->values()->all(),
                ];
            });

        return view('staff.sosmed.index', compact(
            'tab',
            'accounts',
            'accountSearch',
            'searchType',
            'availableAccounts',
            'myAccounts',
            'taskSearch',
            'myAccountSearch',
            'todayTasks',
            'needHrApproval',
            'allTasks',
            'approvalLogs',
            'pms',
            'assistants',
            'supervisors',
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
        if (!$account->staffUsers()->where('users.id', Auth::id())->exists()) {
            abort(403, 'Akses ditolak. Anda bukan eksekutor akun ini.');
        }

        $validated = $request->validate([
            'links'       => ['required', 'array', 'min:1'],
            'links.*'     => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $links = array_values(array_filter($validated['links'], fn($l) => !empty(trim($l))));
        if (empty($links)) {
            return back()->withErrors(['links' => 'Minimal satu link bukti harus diisi.'])->withInput();
        }

        $task = SosmedTask::firstOrNew([
            'sosmed_account_id' => $account->id,
            'assigned_to'       => Auth::id(),
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
            'notes'          => 'HR Staff submit bukti laporan sosmed (' . count($links) . ' item). Menunggu verifikasi ' . $account->finalVerifierLabel() . '.',
        ]);

        $this->logActivity('sosmed.submitted', 'Sosmed', "Submit bukti laporan sosmed untuk akun '{$account->name}'", $task);

        return redirect()->route('staff.sosmed.index', ['tab' => 'my_accounts'])
            ->with('success', 'Bukti konten untuk ' . $account->name . ' berhasil dikirim. Menunggu verifikasi ' . $account->finalVerifierLabel() . '.');
    }

    /**
     * HR Staff memberikan tugas pengelolaan akun sosmed baru dari daftar akun yang tersedia.
     */
    public function assignTask(Request $request)
    {
        $validated = $request->validate([
            'sosmed_account_id'   => ['required', 'exists:sosmed_accounts,id'],
            'staff_id'            => ['nullable', 'exists:users,id'],
            'pm_id'               => ['nullable', 'exists:users,id'],
            'assistant_id'        => ['nullable', 'exists:users,id'],
            'supervisor_staff_id' => ['nullable', 'exists:users,id'],
            'notes'               => ['nullable', 'string', 'max:1000'],
        ]);

        $account = SosmedAccount::findOrFail($validated['sosmed_account_id']);

        $newStaffId = $validated['staff_id'] ?? null;
        $newPmId = $validated['pm_id'] ?? null;
        $newAssistantId = $validated['assistant_id'] ?? null;
        $newSupervisorStaffId = $validated['supervisor_staff_id'] ?? null;

        if (!$newSupervisorStaffId && ($newPmId || $newAssistantId)) {
            $newSupervisorStaffId = Auth::id();
        }

        if ($newStaffId) {
            $oversight = PmSosmedOversight::where('sosmed_id', $newStaffId)->first();
            if ($oversight) {
                $newPmId = $oversight->pm_id;
            }
        }

        $accountData = [
            'is_in_sosmed' => true,
            'notes'        => $validated['notes'] ?? $account->notes,
        ];

        if (!empty($newPmId)) {
            $accountData['pm_id'] = $newPmId;
        }
        if (!empty($newAssistantId)) {
            $accountData['assistant_id'] = $newAssistantId;
        }
        if (!empty($newSupervisorStaffId)) {
            $accountData['supervisor_staff_id'] = $newSupervisorStaffId;
        }

        $account->update($accountData);

        if ($newStaffId) {
            if (!$account->staffUsers()->where('users.id', $newStaffId)->exists()) {
                $account->staffUsers()->attach($newStaffId, [
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                ]);
            }

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
                    'description'       => $validated['notes'] ?? null,
                    'link_upload'       => null,
                    'task_date'         => now()->toDateString(),
                    'status'            => 'pending',
                ]);
            }
        }

        $this->logActivity('sosmed.assigned', 'Sosmed', "Menambahkan akun '{$account->name}' ({$account->platform}) ke daftar pengelolaan sosmed", $account);

        $msg = $newStaffId
            ? "Tugas pengelolaan akun '{$account->name}' berhasil diberikan."
            : "Akun '{$account->name}' berhasil ditambahkan ke daftar pengelolaan sosmed.";

        return redirect()->route('staff.sosmed.index', ['tab' => 'accounts'])
            ->with('success', $msg);
    }

    /**
     * HR Staff assign pm_id, assistant_id, AND staff_id on a SosmedAccount.
     */
    public function assignAccount(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'sosmed_account_id'   => ['nullable', 'exists:sosmed_accounts,id'],
            'pm_id'               => ['nullable', 'exists:users,id'],
            'assistant_id'        => ['nullable', 'exists:users,id'],
            'supervisor_staff_id' => ['nullable', 'exists:users,id'],
            'staff_id'            => ['nullable', 'exists:users,id'],
            'old_staff_id'        => ['nullable', 'exists:users,id'],
            'notes'               => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStaffId = !empty($validated['old_staff_id']) ? (int)$validated['old_staff_id'] : null;
        $newStaffId = !empty($validated['staff_id']) ? (int)$validated['staff_id'] : null;

        $oldUser = $oldStaffId ? User::find($oldStaffId) : null;
        $newUser = $newStaffId ? User::find($newStaffId) : null;

        // Guard: Staff cannot reassign slots managed by HR Staff (assigned by Admin)
        if ($oldUser && $oldUser->role === 'hr_staff') {
            return back()->withErrors(['staff_id' => 'Akun ini ditugaskan langsung kepada HR Staff oleh Admin dan tidak dapat diubah oleh Staff.']);
        }

        $newPmId = $validated['pm_id'] ?? null;
        $newAssistantId = $validated['assistant_id'] ?? null;
        $newSupervisorStaffId = $validated['supervisor_staff_id'] ?? null;

        if (!$newSupervisorStaffId && ($newPmId || $newAssistantId)) {
            $newSupervisorStaffId = Auth::id();
        }

        // Enforce role restriction: Staff cannot assign to HR Staff or Admin
        if ($newStaffId) {
            $staff = User::find($newStaffId);
            if ($staff && in_array($staff->role, ['admin', 'hr_staff'])) {
                return back()->withErrors(['staff_id' => 'HR Staff hanya dapat mendelegasikan akun kepada role di bawahnya (Sosmed, PM, Assistant, Digital Marketing).']);
            }

            if ($staff && $staff->role === 'pm') {
                $newPmId = null;
                $newAssistantId = null;
            } else {
                $oversight = PmSosmedOversight::where('sosmed_id', $newStaffId)->first();
                if ($oversight) {
                    $newPmId = $oversight->pm_id; // auto-correct to the oversight PM
                }
            }
        }

        $targetAccount = $account;
        $isSwitched = false;

        // Jika user memilih akun lain dari dropdown di form edit
        if (!empty($validated['sosmed_account_id']) && (int)$validated['sosmed_account_id'] !== (int)$account->id) {
            $targetAccount = SosmedAccount::findOrFail($validated['sosmed_account_id']);
            $isSwitched = true;
        }

        $targetAccount->update([
            'pm_id'               => $newPmId,
            'assistant_id'        => $newAssistantId,
            'supervisor_staff_id' => $newSupervisorStaffId,
            'notes'               => array_key_exists('notes', $validated) ? $validated['notes'] : $targetAccount->notes,
        ]);

        // 1. Jika akun dialihkan (isSwitched) dan ada pengelola
        if ($isSwitched && $oldStaffId) {
            $account->staffUsers()->detach($oldStaffId);
            if ($newStaffId) {
                if (!$targetAccount->staffUsers()->where('users.id', $newStaffId)->exists()) {
                    $targetAccount->staffUsers()->attach($newStaffId, [
                        'assigned_by' => Auth::id(),
                        'assigned_at' => now(),
                    ]);
                }
            }
        }
        // 2. Jika mengedit pengelola yang sudah ada sebelumnya
        elseif ($oldStaffId) {
            if ($newStaffId && $newStaffId !== $oldStaffId) {
                // Lepas pengelola lama yang sedang diedit
                $targetAccount->staffUsers()->detach($oldStaffId);

                // Update / alihkan tugas harian pending hari ini ke pengelola baru jika belum ada
                $hasNewTask = SosmedTask::where('sosmed_account_id', $targetAccount->id)
                    ->where('assigned_to', $newStaffId)
                    ->whereDate('task_date', now()->toDateString())
                    ->exists();

                if ($hasNewTask) {
                    SosmedTask::where('sosmed_account_id', $targetAccount->id)
                        ->where('assigned_to', $oldStaffId)
                        ->where('status', 'pending')
                        ->whereDate('task_date', now()->toDateString())
                        ->delete();
                } else {
                    SosmedTask::where('sosmed_account_id', $targetAccount->id)
                        ->where('assigned_to', $oldStaffId)
                        ->where('status', 'pending')
                        ->whereDate('task_date', now()->toDateString())
                        ->update(['assigned_to' => $newStaffId]);
                }

                // Pasang pengelola baru jika belum terpasang
                if (!$targetAccount->staffUsers()->where('users.id', $newStaffId)->exists()) {
                    $targetAccount->staffUsers()->attach($newStaffId, [
                        'assigned_by' => Auth::id(),
                        'assigned_at' => now(),
                    ]);
                }

                // Pastikan tugas harian hari ini tersedia untuk pengelola baru
                $exists = SosmedTask::where('sosmed_account_id', $targetAccount->id)
                    ->whereDate('task_date', now()->toDateString())
                    ->where('assigned_to', $newStaffId)
                    ->exists();

                if (!$exists) {
                    SosmedTask::create([
                        'sosmed_account_id' => $targetAccount->id,
                        'assigned_to'       => $newStaffId,
                        'assigned_by'       => Auth::id(),
                        'type'              => 'daily',
                        'title'             => 'Laporan Konten Harian - ' . $targetAccount->name,
                        'description'       => $targetAccount->notes,
                        'link_upload'       => null,
                        'task_date'         => now()->toDateString(),
                        'status'            => 'pending',
                    ]);
                }
            } elseif (!$newStaffId) {
                // User memilih untuk mengosongkan pengelola pada baris ini
                $targetAccount->staffUsers()->detach($oldStaffId);
                SosmedTask::where('sosmed_account_id', $targetAccount->id)
                    ->where('assigned_to', $oldStaffId)
                    ->where('status', 'pending')
                    ->whereDate('task_date', now()->toDateString())
                    ->delete();
            }
        }
        // 3. Jika baris sebelumnya belum memiliki pengelola ("Belum Ditugaskan")
        elseif ($newStaffId) {
            if (!$targetAccount->staffUsers()->where('users.id', $newStaffId)->exists()) {
                $targetAccount->staffUsers()->attach($newStaffId, [
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                ]);
            }

            $exists = SosmedTask::where('sosmed_account_id', $targetAccount->id)
                ->whereDate('task_date', now()->toDateString())
                ->where('assigned_to', $newStaffId)
                ->exists();

            if (!$exists) {
                SosmedTask::create([
                    'sosmed_account_id' => $targetAccount->id,
                    'assigned_to'       => $newStaffId,
                    'assigned_by'       => Auth::id(),
                    'type'              => 'daily',
                    'title'             => 'Laporan Konten Harian - ' . $targetAccount->name,
                    'description'       => $targetAccount->notes,
                    'link_upload'       => null,
                    'task_date'         => now()->toDateString(),
                    'status'            => 'pending',
                ]);
            }
        }

        if ($oldUser && $newUser && $oldStaffId !== $newStaffId) {
            $logMsg = "Mengubah pengelola akun '{$targetAccount->name}' dari '{$oldUser->name}' ke '{$newUser->name}'";
            $flashMsg = "Pengelola akun '{$targetAccount->name}' berhasil diubah ke {$newUser->name}.";
        } elseif ($oldUser && !$newUser) {
            $logMsg = "Melepas pengelola '{$oldUser->name}' dari akun '{$targetAccount->name}'";
            $flashMsg = "Pengelola '{$oldUser->name}' berhasil dilepas dari akun '{$targetAccount->name}'.";
        } elseif (!$oldUser && $newUser) {
            $logMsg = "Menugaskan '{$newUser->name}' untuk mengelola akun '{$targetAccount->name}'";
            $flashMsg = "Pengelola '{$newUser->name}' berhasil ditugaskan untuk akun '{$targetAccount->name}'.";
        } else {
            $logMsg = "Memperbarui penugasan akun '{$targetAccount->name}'";
            $flashMsg = "Penugasan akun '{$targetAccount->name}' berhasil diperbarui.";
        }

        $this->logActivity('sosmed.assigned', 'Sosmed', $logMsg, $targetAccount);

        return redirect()->route('staff.sosmed.index', ['tab' => 'accounts'])
            ->with('success', $flashMsg . ($isSwitched ? ' Akun telah dialihkan ke ' . $targetAccount->name . '.' : ''));
    }

    public function unassignAccount(Request $request, SosmedAccount $account)
    {
        $name = $account->name;
        $platform = $account->platform;
        $userId = $request->input('user_id');

        if ($userId) {
            $account->staffUsers()->detach($userId);
            SosmedTask::where('sosmed_account_id', $account->id)
                ->where('assigned_to', $userId)
                ->where('status', 'pending')
                ->delete();

            $targetUser = User::find($userId);
            $userName = $targetUser ? $targetUser->name : "User #{$userId}";

            // Lepas otomatis dari daftar kelola sosmed jika sudah tidak ada yang mengelola sama sekali
            $removed = !$account->staffUsers()->exists()
                && !$account->pm_id && !$account->assistant_id && !$account->supervisor_staff_id;
            if ($removed) {
                $account->update(['is_in_sosmed' => false]);
            }

            $this->logActivity('sosmed.unassigned', 'Sosmed', "Melepas akses user '{$userName}' dari akun '{$name}' ({$platform})", $account);

            return redirect()->route('staff.sosmed.index', ['tab' => 'accounts'])
                ->with('success', $removed
                    ? "Akses user '{$userName}' dilepas. Akun '{$name}' ikut hilang dari daftar kelola sosmed karena sudah tidak ada yang mengelola."
                    : "Akses user '{$userName}' untuk akun '{$name}' berhasil dilepas.");
        }

        $account->staffUsers()->detach();
        $account->update([
            'is_in_sosmed'        => false,
            'pm_id'               => null,
            'assistant_id'        => null,
            'supervisor_staff_id' => null,
        ]);

        SosmedTask::where('sosmed_account_id', $account->id)
            ->where('status', 'pending')
            ->delete();

        $this->logActivity('sosmed.unassigned', 'Sosmed', "Melepas seluruh penugasan akun '{$name}' ({$platform})", $account);

        return redirect()->route('staff.sosmed.index', ['tab' => 'accounts'])
            ->with('success', "Seluruh penugasan akun '{$name}' berhasil dilepas. Akun dihapus dari daftar kelola sosmed karena sudah tidak ada yang mengelola.");
    }

    public function destroyAccount(SosmedAccount $account)
    {
        if ($account->staffUsers()->where('role', 'hr_staff')->exists()) {
            return back()->withErrors(['staff_id' => 'Akun ini ditugaskan langsung kepada HR Staff oleh Admin dan tidak dapat dihapus oleh Staff.']);
        }

        $name = $account->name;
        $account->staffUsers()->detach();
        $account->update([
            'is_in_sosmed'        => false,
            'pm_id'               => null,
            'assistant_id'        => null,
            'supervisor_staff_id' => null,
        ]);

        SosmedTask::where('sosmed_account_id', $account->id)
            ->where('status', 'pending')
            ->delete();

        $this->logActivity('sosmed.deleted', 'Sosmed', "Menghapus akun '{$name}' dari daftar kelola sosmed");
        return redirect()->route('staff.sosmed.index', ['tab' => 'accounts'])
            ->with('success', "Akun '{$name}' berhasil dihapus dari daftar pengelolaan sosmed.");
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
            SosmedAccount::whereHas('staffUsers', fn($q) => $q->where('users.id', $sosmedId))
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
     * Approval oleh HR Staff:
     * - Level 2 (verified_by_pm) dari PM / Asisten
     * - ATAU Verifikasi Langsung oleh Staff Pengawas yang dipilih (status done_by_staff)
     */
    public function verifyTask(Request $request, SosmedTask $task)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:verify,reject'],
            'rejection_note' => ['nullable', 'string', 'max:500'],
        ]);

        $isSupervisorDirect = ($task->status === 'done_by_staff');

        if ($isSupervisorDirect && $task->account?->supervisor_staff_id && $task->account->supervisor_staff_id !== Auth::id()) {
            abort(403, 'Akses ditolak. Anda bukan Staff Pengawas yang ditugaskan untuk akun ini.');
        }

        if ($validated['action'] === 'verify') {
            $task->update([
                'status'         => 'approved_hr',
                'verified_by'    => $task->verified_by ?? Auth::id(),
                'verified_at'    => $task->verified_at ?? now(),
                'hr_verified_by' => Auth::id(),
                'hr_verified_at' => now(),
            ]);

            SosmedApprovalLog::create([
                'sosmed_task_id' => $task->id,
                'user_id'        => Auth::id(),
                'user_name'      => Auth::user()->name,
                'role_name'      => $isSupervisorDirect ? 'Staff Pengawas (HR Staff)' : 'HR Staff',
                'action'         => 'approved_hr',
                'notes'          => $isSupervisorDirect
                    ? 'Diverifikasi & disetujui langsung oleh Staff Pengawas (' . Auth::user()->name . ') sebagai pengganti PM/Asisten.'
                    : 'Disetujui secara final oleh HR Staff.',
            ]);

            $this->logActivity('sosmed.verified', 'Sosmed', "Menyetujui tugas sosmed '{$task->title}'", $task);

            return redirect()->route('staff.sosmed.index', ['tab' => 'approvals'])
                ->with('success', $isSupervisorDirect
                    ? 'Tugas berhasil diverifikasi & disetujui langsung oleh Staff Pengawas.'
                    : 'Tugas berhasil disetujui secara final oleh HR Staff.');
        } else {
            $task->update([
                'status'         => 'rejected',
                'verified_by'    => $task->verified_by ?? Auth::id(),
                'verified_at'    => $task->verified_at ?? now(),
                'hr_verified_by' => Auth::id(),
                'hr_verified_at' => now(),
                'rejection_note' => $validated['rejection_note'],
            ]);

            SosmedApprovalLog::create([
                'sosmed_task_id' => $task->id,
                'user_id'        => Auth::id(),
                'user_name'      => Auth::user()->name,
                'role_name'      => $isSupervisorDirect ? 'Staff Pengawas (HR Staff)' : 'HR Staff',
                'action'         => 'rejected',
                'notes'          => $validated['rejection_note'] ?? ($isSupervisorDirect ? 'Ditolak oleh Staff Pengawas' : 'Ditolak oleh HR Staff'),
            ]);

            $this->logActivity('sosmed.verified', 'Sosmed', "Menolak tugas sosmed '{$task->title}' dengan catatan: {$validated['rejection_note']}", $task);

            return redirect()->route('staff.sosmed.index', ['tab' => 'approvals'])
                ->with('success', 'Tugas ditolak dan dikembalikan untuk perbaikan.');
        }
    }
}
