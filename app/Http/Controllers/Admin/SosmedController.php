<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\LogsActivity;
use App\Models\Brand;
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

        // Seluruh Akun Sosmed di Sistem Pemantauan Sosmed
        $accountSearch = $request->query('account_search');
        $searchType = $request->query('search_type', 'all');
        $brand = $request->query('brand');
        $accFilter = $request->query('acc_filter');
        $accountsQuery = SosmedAccount::inSosmed()
            ->with(['pmUser', 'staffUsers', 'assistantUser', 'supervisorStaff', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($brand) {
            $accountsQuery->where('brand', $brand);
        }

        if ($accountSearch) {
            $term = trim($accountSearch);
            $accountsQuery->where(function ($q) use ($term, $searchType) {
                if ($searchType === 'account') {
                    $q->where('name', 'like', '%' . $term . '%')
                      ->orWhere('username', 'like', '%' . $term . '%');
                } elseif ($searchType === 'brand') {
                    $q->where('brand', 'like', '%' . $term . '%');
                } elseif ($searchType === 'manager') {
                    $q->whereHas('staffUsers', fn($sq) => $sq->where('users.name', 'like', '%' . $term . '%'));
                } elseif ($searchType === 'pm') {
                    $q->whereHas('pmUser', fn($pq) => $pq->where('users.name', 'like', '%' . $term . '%'));
                } elseif ($searchType === 'assistant') {
                    $q->whereHas('assistantUser', fn($aq) => $aq->where('users.name', 'like', '%' . $term . '%'));
                } elseif ($searchType === 'staff') {
                    $q->whereHas('supervisorStaff', fn($sq) => $sq->where('users.name', 'like', '%' . $term . '%'));
                } else {
                    // 'all': Cari di nama akun, username, brand, pengelola/eksekutor, PM, asisten, maupun staff pengawas
                    $q->where('name', 'like', '%' . $term . '%')
                      ->orWhere('username', 'like', '%' . $term . '%')
                      ->orWhere('brand', 'like', '%' . $term . '%')
                      ->orWhereHas('staffUsers', fn($sq) => $sq->where('users.name', 'like', '%' . $term . '%'))
                      ->orWhereHas('pmUser', fn($pq) => $pq->where('users.name', 'like', '%' . $term . '%'))
                      ->orWhereHas('assistantUser', fn($aq) => $aq->where('users.name', 'like', '%' . $term . '%'))
                      ->orWhereHas('supervisorStaff', fn($sq) => $sq->where('users.name', 'like', '%' . $term . '%'));
                }
            });
        }

        // Get counts before pagination
        $totalAccountsCount = $accountsQuery->count();
        $accountsStats = [
            'total' => $totalAccountsCount,
            'unassigned_pm' => (clone $accountsQuery)->whereNull('pm_id')->count(),
            'unassigned_staff' => (clone $accountsQuery)->whereDoesntHave('staffUsers')->count(),
        ];

        // Filter cepat dari klik kartu statistik (diterapkan setelah angka kartu dihitung)
        if ($accFilter === 'unassigned_pm') {
            $accountsQuery->whereNull('pm_id');
        } elseif ($accFilter === 'unassigned_staff') {
            $accountsQuery->whereDoesntHave('staffUsers');
        } else {
            $accFilter = null;
        }

        $accounts = $accountsQuery->paginate(15)->appends($request->all());

        // Filter date for tasks
        $taskDateFilter = $request->query('task_date');
        if (!$taskDateFilter) {
            $taskDateFilter = now()->toDateString(); // By default, current date
        }

        // Seluruh Tugas Sosmed
        $tasksQuery = SosmedTask::with(['account', 'assignedUser', 'assignedBy', 'verifiedBy', 'hrVerifiedBy'])
            ->when($taskDateFilter, function ($q) use ($taskDateFilter) {
                $q->whereDate('task_date', $taskDateFilter);
            })
            ->orderBy('task_date', 'desc');

        // Get counts before pagination
        $totalTasksCount = $tasksQuery->count();
        $tasksStats = [
            'total' => $totalTasksCount,
            'pending' => (clone $tasksQuery)->where('status', 'pending')->count(),
            'done_by_staff' => (clone $tasksQuery)->where('status', 'done_by_staff')->count(),
            'verified_by_pm' => (clone $tasksQuery)->where('status', 'verified_by_pm')->count(),
            'approved_hr' => (clone $tasksQuery)->where('status', 'approved_hr')->count(),
        ];

        // Filter cepat status tugas dari klik kartu statistik
        $taskStatus = $request->query('task_status');
        if (! in_array($taskStatus, ['pending', 'done_by_staff', 'verified_by_pm', 'approved_hr', 'rejected'], true)) {
            $taskStatus = null;
        } else {
            $tasksQuery->where('status', $taskStatus);
        }

        // Pencarian tugas (diterapkan setelah angka kartu dihitung agar statistik stabil)
        $taskSearch = trim((string) $request->query('task_search', ''));
        if ($taskSearch !== '') {
            $tasksQuery->where(function ($q) use ($taskSearch) {
                $q->where('title', 'like', '%' . $taskSearch . '%')
                    ->orWhere('description', 'like', '%' . $taskSearch . '%')
                    ->orWhereHas('account', fn ($a) => $a->where('name', 'like', '%' . $taskSearch . '%')
                        ->orWhere('platform', 'like', '%' . $taskSearch . '%')
                        ->orWhere('brand', 'like', '%' . $taskSearch . '%'))
                    ->orWhereHas('assignedUser', fn ($u) => $u->where('name', 'like', '%' . $taskSearch . '%'));
            });
        }

        $tasks = $tasksQuery->paginate(15)->appends($request->all());

        // Date filter and time ranges for audit logs
        $logDateFilter = $request->query('log_date');
        $logRangeFilter = $request->query('log_range'); // 'weekly', 'monthly', 'yearly'
        $logSearch = $request->query('log_search');
        $logActionFilter = $request->query('log_action');

        // Seluruh Approval Logs (Audit Trail Lengkap)
        $logsQuery = SosmedApprovalLog::with(['task.account', 'user'])->latest();

        if ($logDateFilter) {
            $logsQuery->whereDate('created_at', $logDateFilter);
        } elseif ($logRangeFilter) {
            if ($logRangeFilter === 'weekly') {
                $logsQuery->where('created_at', '>=', now()->startOfWeek());
            } elseif ($logRangeFilter === 'monthly') {
                $logsQuery->where('created_at', '>=', now()->startOfMonth());
            } elseif ($logRangeFilter === 'yearly') {
                $logsQuery->where('created_at', '>=', now()->startOfYear());
            }
        }

        if ($logSearch) {
            $logsQuery->where(function ($q) use ($logSearch) {
                $q->where('user_name', 'like', '%' . $logSearch . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $logSearch . '%'));
            });
        }

        if ($logActionFilter) {
            $logsQuery->where('action', $logActionFilter);
        }

        $logs = $logsQuery->paginate(30)->appends($request->all());

        // List user untuk penugasan
        $pms = User::join('roles', 'users.role', '=', 'roles.name')
            ->where('roles.name', 'pm')
            ->where('users.is_active', true)
            ->select('users.*')
            ->orderBy('users.name')
            ->get();

        $assistants = User::where(function ($q) {
                $q->where('role', 'hr_assistant')
                  ->orWhereHas('roleModel', fn($r) => $r->where('base_type', 'assistant'));
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Staff Pengawas (Role HR Staff)
        $supervisors = User::where(function ($q) {
                $q->where('role', 'hr_staff')
                  ->orWhereHas('roleModel', fn($r) => $r->where('base_type', 'staff'));
            })
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $executors = User::whereIn('role', ['sosmed', 'pm', 'hr_staff', 'hr_assistant', 'digital_marketing'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $staffs = $executors; // compatibility

        // Admin memiliki wewenang untuk meng-acc semua tugas sosmed tanpa terkecuali
        // Menampilkan seluruh tugas yang menunggu verifikasi (baik selesai dikerjakan staff maupun telah diverifikasi PM)
        $verifySearch = trim((string) $request->query('verify_search', ''));
        $staffPendingQuery = SosmedTask::with(['account.supervisorStaff', 'account.pmUser', 'account.assistantUser', 'assignedUser', 'assignedBy'])
            ->whereIn('status', ['done_by_staff', 'verified_by_pm'])
            ->orderBy('updated_at', 'desc');

        if ($verifySearch !== '') {
            $staffPendingQuery->where(function ($q) use ($verifySearch) {
                $q->where('title', 'like', '%' . $verifySearch . '%')
                  ->orWhereHas('account', fn($a) => $a->where('name', 'like', '%' . $verifySearch . '%')
                      ->orWhere('platform', 'like', '%' . $verifySearch . '%')
                      ->orWhere('brand', 'like', '%' . $verifySearch . '%'))
                  ->orWhereHas('assignedUser', fn($u) => $u->where('name', 'like', '%' . $verifySearch . '%'));
            });
        }

        $staffPendingTasks = $staffPendingQuery->get();

        $stats = [
            'total_accounts'    => $accountsStats['total'],
            'unassigned_pm'     => $accountsStats['unassigned_pm'],
            'unassigned_staff'  => $accountsStats['unassigned_staff'],
            'total_tasks'       => $tasksStats['total'],
            'pending_tasks'     => $tasksStats['pending'],
            'need_pm_verify'    => $tasksStats['done_by_staff'],
            'need_admin_verify' => SosmedTask::whereIn('status', ['done_by_staff', 'verified_by_pm'])->count(),
            'need_hr_verify'    => $tasksStats['verified_by_pm'],
            'completed'         => $tasksStats['approved_hr'],
        ];

        // Daftar brand yang ada di sistem sosmed
        $brands = SosmedAccount::inSosmed()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->pluck('brand')
            ->sort()
            ->values();

        // Logo brand (nama => path) untuk penanda visual
        $brandLogos = Brand::whereNotNull('logo_path')->pluck('logo_path', 'name');

        // Akun yang sudah disetujui (approved) untuk penugasan sosmed beserta info pengelola saat ini
        $availableAccounts = SosmedAccount::where('verification_status', 'approved')
            ->with(['staffUsers:id,name'])
            ->select('id', 'name', 'brand', 'platform', 'link', 'is_in_sosmed')
            ->orderBy('platform')
            ->orderBy('name')
            ->get()
            ->map(function ($acc) {
                return [
                    'id' => $acc->id,
                    'name' => $acc->name,
                    'brand' => $acc->brand,
                    'platform' => $acc->platform,
                    'link' => $acc->link,
                    'is_in_sosmed' => (bool)$acc->is_in_sosmed,
                    'managers_count' => $acc->staffUsers->count(),
                    'assigned_user_ids' => $acc->staffUsers->pluck('id')->values()->all(),
                ];
            });

        return view('admin.sosmed.index', compact(
            'tab',
            'accounts',
            'availableAccounts',
            'accountSearch',
            'searchType',
            'brand',
            'brands',
            'brandLogos',
            'tasks',
            'staffPendingTasks',
            'taskDateFilter',
            'taskStatus',
            'taskSearch',
            'accFilter',
            'verifySearch',
            'logs',
            'logDateFilter',
            'logRangeFilter',
            'pms',
            'assistants',
            'supervisors',
            'staffs',
            'executors',
            'stats'
        ));
    }

    public function storeAccount(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'brand' => ['nullable', 'string', 'max:100'],
            'platform' => ['required', 'string', 'max:50'],
            'link' => ['nullable', 'url', 'max:500'],
            'pm_id' => ['nullable', 'exists:users,id'],
            'assistant_id' => ['nullable', 'exists:users,id'],
            'supervisor_staff_id' => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $staffId = $validated['staff_id'] ?? null;
        unset($validated['staff_id']);

        if (!empty($staffId)) {
            $staff = User::find($staffId);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $account = SosmedAccount::create([
            ...$validated,
            'is_in_sosmed' => true,
            'created_by' => Auth::id(),
        ]);

        if (!empty($staffId)) {
            $account->staffUsers()->attach($staffId, [
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
            ]);
        }

        $this->logActivity('sosmed.created', 'Sosmed', "Menambahkan akun sosial media '{$validated['name']}' ({$validated['platform']})" . (!empty($validated['brand']) ? " [Brand: {$validated['brand']}]" : ''), $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Akun sosial media baru berhasil ditambahkan.');
    }

    public function updateAccount(Request $request, SosmedAccount $account)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'brand' => ['nullable', 'string', 'max:100'],
            'platform' => ['required', 'string', 'max:50'],
            'link' => ['nullable', 'url', 'max:500'],
            'pm_id' => ['nullable', 'exists:users,id'],
            'assistant_id' => ['nullable', 'exists:users,id'],
            'supervisor_staff_id' => ['nullable', 'exists:users,id'],
            'staff_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $staffId = $validated['staff_id'] ?? null;
        unset($validated['staff_id']);
        unset($validated['link']);

        if (!empty($staffId)) {
            $staff = User::find($staffId);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $account->update($validated);

        if (!empty($staffId)) {
            if (!$account->staffUsers()->where('users.id', $staffId)->exists()) {
                $account->staffUsers()->attach($staffId, [
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                ]);
            }
        }

        $this->logActivity('sosmed.updated', 'Sosmed', "Memperbarui akun sosial media '{$account->name}'", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', 'Akun sosial media berhasil diperbarui.');
    }

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

        if (!empty($validated['staff_id'])) {
            $staff = User::find($validated['staff_id']);
            if ($staff && $staff->role === 'pm') {
                $validated['pm_id'] = null;
            }
        }

        $accountData = [
            'is_in_sosmed' => true,
            'notes'        => $validated['notes'] ?? $account->notes,
        ];

        if (!empty($validated['pm_id'])) {
            $accountData['pm_id'] = $validated['pm_id'];
        }
        if (!empty($validated['assistant_id'])) {
            $accountData['assistant_id'] = $validated['assistant_id'];
        }
        if (!empty($validated['supervisor_staff_id'])) {
            $accountData['supervisor_staff_id'] = $validated['supervisor_staff_id'];
        }

        $account->update($accountData);

        if (!empty($validated['staff_id'])) {
            $staffId = $validated['staff_id'];
            if (!$account->staffUsers()->where('users.id', $staffId)->exists()) {
                $account->staffUsers()->attach($staffId, [
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                ]);
            }

            $exists = SosmedTask::where('sosmed_account_id', $account->id)
                ->whereDate('task_date', now()->toDateString())
                ->where('assigned_to', $staffId)
                ->exists();

            if (!$exists) {
                SosmedTask::create([
                    'sosmed_account_id' => $account->id,
                    'assigned_to'       => $staffId,
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

        $msg = !empty($validated['staff_id'])
            ? "Tugas pengelolaan akun '{$account->name}' berhasil diberikan."
            : "Akun '{$account->name}' berhasil ditambahkan ke daftar pengelolaan sosmed.";

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', $msg);
    }

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

        if (!empty($validated['staff_id'])) {
            $staff = User::find($validated['staff_id']);
            if ($staff && in_array($staff->role, ['pm', 'hr_staff'])) {
                $validated['pm_id'] = null;
                $validated['assistant_id'] = null;
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
            'pm_id'               => $validated['pm_id'] ?? null,
            'assistant_id'        => $validated['assistant_id'] ?? null,
            'supervisor_staff_id' => $validated['supervisor_staff_id'] ?? null,
            'notes'               => array_key_exists('notes', $validated) ? $validated['notes'] : $targetAccount->notes,
        ]);

        $oldStaffId = !empty($validated['old_staff_id']) ? (int)$validated['old_staff_id'] : null;
        $newStaffId = !empty($validated['staff_id']) ? (int)$validated['staff_id'] : null;

        $oldUser = $oldStaffId ? User::find($oldStaffId) : null;
        $newUser = $newStaffId ? User::find($newStaffId) : null;

        // 1. Jika akun dipindahkan (isSwitched) dan ada pengelola
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

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
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
            $this->logActivity('sosmed.unassigned', 'Sosmed', "Melepas akses user '{$userName}' dari akun '{$name}' ({$platform})", $account);

            return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
                ->with('success', "Akses user '{$userName}' untuk akun '{$name}' berhasil dilepas.");
        }

        $account->staffUsers()->detach();
        $account->update([
            'pm_id'               => null,
            'assistant_id'        => null,
            'supervisor_staff_id' => null,
        ]);

        SosmedTask::where('sosmed_account_id', $account->id)
            ->where('status', 'pending')
            ->delete();

        $this->logActivity('sosmed.unassigned', 'Sosmed', "Melepas seluruh penugasan akun '{$name}' ({$platform})", $account);

        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', "Seluruh penugasan akun '{$name}' berhasil dilepas. Akun tetap berada di daftar kelola sosmed dengan status belum ditugaskan.");
    }

    public function destroyAccount(SosmedAccount $account)
    {
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
        return redirect()->route('admin.sosmed.index', ['tab' => 'accounts'])
            ->with('success', "Akun '{$name}' berhasil dihapus dari daftar pengelolaan sosmed. Data akun tetap tersimpan di Manajemen Akun.");
    }

    public function purgeTasks(Request $request)
    {
        $range = $request->input('range'); // 'weekly', 'monthly', 'yearly'

        $query = SosmedTask::query();
        if ($range === 'weekly') {
            $query->where('task_date', '<', now()->startOfWeek());
        } elseif ($range === 'monthly') {
            $query->where('task_date', '<', now()->startOfMonth());
        } elseif ($range === 'yearly') {
            $query->where('task_date', '<', now()->startOfYear());
        } else {
            return redirect()->back()->with('error', 'Rentang waktu tidak valid.');
        }

        $count = $query->count();
        $query->delete();
        $this->logActivity('sosmed.deleted', 'Sosmed', "Menghapus {$count} tugas sosmed lama (filter: {$range})");

        return redirect()->route('admin.sosmed.index', ['tab' => 'tasks'])
            ->with('success', "Berhasil menghapus {$count} tugas lama.");
    }

    public function purgeLogs(Request $request)
    {
        $range = $request->input('range'); // 'weekly', 'monthly', 'yearly'

        $query = SosmedApprovalLog::query();
        if ($range === 'weekly') {
            $query->where('created_at', '<', now()->startOfWeek());
        } elseif ($range === 'monthly') {
            $query->where('created_at', '<', now()->startOfMonth());
        } elseif ($range === 'yearly') {
            $query->where('created_at', '<', now()->startOfYear());
        } else {
            return redirect()->back()->with('error', 'Rentang waktu tidak valid.');
        }

        $count = $query->count();
        $query->delete();
        $this->logActivity('sosmed.deleted', 'Sosmed', "Menghapus {$count} log persetujuan lama (filter: {$range})");

        return redirect()->route('admin.sosmed.index', ['tab' => 'logs'])
            ->with('success', "Berhasil menghapus {$count} log persetujuan lama.");
    }

    /**
     * Admin verifikasi tugas sosmed yang dikerjakan oleh HR Staff.
     */
    public function verifyTask(Request $request, SosmedTask $task)
    {
        $validated = $request->validate([
            'action'         => ['required', 'in:verify,reject'],
            'rejection_note' => ['nullable', 'string', 'max:500'],
        ]);

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
                'role_name'      => 'Administrator',
                'action'         => 'approved_hr',
                'notes'          => 'Disetujui langsung oleh Administrator.',
            ]);

            $this->logActivity('sosmed.verified', 'Sosmed', "Administrator menyetujui tugas sosmed '{$task->title}' milik {$task->assignedUser?->name}", $task);

            return redirect()->back()->with('success', 'Tugas sosmed berhasil disetujui (ACC) oleh Administrator.');
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
                'role_name'      => 'Administrator',
                'action'         => 'rejected',
                'notes'          => $validated['rejection_note'] ?? 'Ditolak oleh Administrator.',
            ]);

            $this->logActivity('sosmed.verified', 'Sosmed', "Administrator menolak tugas sosmed '{$task->title}' milik {$task->assignedUser?->name}", $task);

            return redirect()->back()->with('success', 'Tugas ditolak dan dikembalikan untuk revisi.');
        }
    }
}
