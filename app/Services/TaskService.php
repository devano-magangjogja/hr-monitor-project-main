<?php

namespace App\Services;

use App\Models\SosmedAccount;
use App\Models\SosmedTask;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Services\DefaultTaskService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function __construct(
        protected TaskRepository $taskRepository,
        protected UserRepository $userRepository,
    ) {}

    // ── Getter ───────────────────────────────────────────

    public function getAllForAdmin(): Collection
    {
        return $this->taskRepository->getAllForAdmin();
    }

    public function getAssignedTasksForStaff(int $staffId): Collection
    {
        return $this->taskRepository->getAssignedTasksForStaff($staffId);
    }

    public function getAssignableUsers(): Collection
    {
        $user = Auth::user();

        /** @var Builder $query */
        $query = User::query();

        if ($user->isAdmin()) {
            // Admin bisa assign ke semua role kecuali admin itu sendiri
            return $query
                ->where('role', '!=', 'admin')
                ->where('is_active', 1)
                ->orderBy('role')
                ->orderBy('name')
                ->get();
        }

        // hr_staff bisa assign ke hr_assistant / user dengan base_type assistant
        return $query
            ->where(function ($q) {
                $q->where('role', 'hr_assistant')
                  ->orWhereHas('roleModel', fn($r) => $r->where('base_type', 'assistant'));
            })
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }
    public function createAssignedTask(array $data): Task
    {
        $this->validateAssignees($data['user_ids']);

        $task = $this->taskRepository->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'task_date'   => Carbon::today(),
            'type'        => 'assigned',
            'kantor'      => $data['kantor'] ?? null,
            'created_by'  => Auth::id(),
        ]);

        $this->attachAssigneesAndNotify($task, $data['user_ids']);

        return $task;
    }
    public function updateTask(Task $task, array $data): bool
    {
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat diedit karena sudah ada penerima yang menyelesaikannya.',
            ]);
        }

        $this->validateAssignees($data['user_ids']);

        $updated = $this->taskRepository->update($task, [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'kantor'      => $data['kantor'] ?? null,
        ]);

        $this->taskRepository->deleteAssignments($task->id);
        $this->attachAssigneesAndNotify($task, $data['user_ids']);

        return $updated;
    }
    public function deleteTask(Task $task): bool
    {
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat dihapus karena sudah ada penerima yang menyelesaikannya.',
            ]);
        }

        return $this->taskRepository->delete($task);
    }

    public function createSelfTask(array $data): Task
    {
        $task = $this->taskRepository->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'task_date'   => Carbon::today(),
            'type'        => 'self',
            'created_by'  => Auth::id(),
        ]);
        $this->taskRepository->createAssignment($task->id, Auth::id());

        return $task;
    }

    public function updateSelfTask(Task $task, array $data): bool
    {
        // Pastikan task milik sendiri
        if ($task->created_by !== Auth::id()) {
            throw ValidationException::withMessages([
                'task' => 'Anda tidak memiliki akses untuk mengedit tugas ini.',
            ]);
        }

        // Cegah edit jika sudah selesai
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat diedit karena sudah diselesaikan.',
            ]);
        }

        return $this->taskRepository->update($task, [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
        ]);
    }
    public function deleteSelfTask(Task $task): bool
    {
        // Pastikan task milik sendiri
        if ($task->created_by !== Auth::id()) {
            throw ValidationException::withMessages([
                'task' => 'Anda tidak memiliki akses untuk menghapus tugas ini.',
            ]);
        }
        // Cegah hapus jika sudah selesai
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat dihapus karena sudah diselesaikan.',
            ]);
        }

        return $this->taskRepository->delete($task);
    }

    public function getSelfTasksToday(int $userId): Collection
    {
        return $this->taskRepository->getSelfTasksToday($userId);
    }

    public function completeTask(Task $task, ?string $note): bool
    {
        $assignment = $this->taskRepository->findAssignment($task->id, Auth::id());
    
        if (! $assignment) {
            throw ValidationException::withMessages([
                'task' => 'Anda tidak memiliki akses untuk menyelesaikan tugas ini.',
            ]);
        }
    
        // Tidak bisa complete jika sudah not_done
        if ($assignment->isNotDone()) {
            throw ValidationException::withMessages([
                'task' => 'Tugas ini sudah ditandai tidak dikerjakan karena melewati batas waktu.',
            ]);
        }
    
        if ($assignment->isCompleted()) {
            throw ValidationException::withMessages([
                'task' => 'Tugas ini sudah ditandai selesai.',
            ]);
        }
        return $this->taskRepository->completeAssignment($assignment, $note);
    }

    public function markAllPendingAsNotDone(): int
    {
        $today = Carbon::today()->toDateString();
        return $this->taskRepository->markAllPendingAsNotDone($today);
    }

    public function getAllTasksForUserToday(int $userId): \Illuminate\Support\Collection
    {
        $today = Carbon::today()->toDateString();

        // 1. Pastikan tugas rutin default hari ini sudah ter-generate (termasuk tugas Presensi)
        try {
            app(DefaultTaskService::class)->generateDailyTasks();
        } catch (\Throwable $e) {
            // Abaikan jika terjadi kendala saat auto-generate
        }

        // 2. Ambil tugas reguler dari repository
        $regularTasks = $this->taskRepository->getAllTasksForUserToday($userId);

        // 3. Ambil tugas pengelolaan akun sosmed untuk user ini
        $sosmedTasks = $this->getSosmedTasksForUser($userId, $today);

        // 4. Gabungkan seluruh tugas
        return $regularTasks->concat($sosmedTasks);
    }

    /**
     * Ambil seluruh tugas pengelolaan akun sosmed untuk user tertentu.
     * Jika akun belum disetujui final (approved_hr) dari hari sebelumnya, tugas TIDAK AKAN HILANG.
     */
    public function getSosmedTasksForUser(int $userId, string $today): \Illuminate\Support\Collection
    {
        $user = User::find($userId);
        if (!$user) {
            return collect();
        }

        $items = collect();

        // A. Akun di mana user ini menjadi penanggung jawab langsung (staff_id)
        $accounts = SosmedAccount::with(['pmUser', 'creator'])
            ->where('staff_id', $userId)
            ->orderBy('platform')
            ->get();

        foreach ($accounts as $account) {
            // 1. Tugas masa lalu yang BELUM disetujui final (status != 'approved_hr')
            $unapprovedTasks = SosmedTask::with(['verifiedBy', 'hrVerifiedBy'])
                ->where('sosmed_account_id', $account->id)
                ->whereDate('task_date', '<', $today)
                ->where('status', '!=', 'approved_hr')
                ->orderByDesc('task_date')
                ->get();

            foreach ($unapprovedTasks as $pt) {
                $items->push($this->formatSosmedTaskItem($pt, $account, $userId, true));
            }

            // 2. Tugas hari ini untuk akun tersebut
            $todayTask = SosmedTask::with(['verifiedBy', 'hrVerifiedBy'])
                ->where('sosmed_account_id', $account->id)
                ->whereDate('task_date', $today)
                ->first();

            if ($todayTask) {
                $items->push($this->formatSosmedTaskItem($todayTask, $account, $userId, false));
            } else {
                // Hari ini belum submit bukti -> Muncul otomatis sebagai tugas pending
                $items->push($this->createPendingSosmedTaskItem($account, $userId, $today));
            }
        }

        // B. Jika user adalah PM, ambil juga tugas verifikasi konten staff yang menunggu verifikasi PM
        if ($user->role === 'pm') {
            $supervisedAccountIds = SosmedAccount::where('pm_id', $userId)
                ->where('staff_id', '!=', $userId)
                ->whereNotNull('staff_id')
                ->pluck('id');

            if ($supervisedAccountIds->isNotEmpty()) {
                $tasksNeedPmVerify = SosmedTask::with(['account', 'assignedToUser'])
                    ->whereIn('sosmed_account_id', $supervisedAccountIds)
                    ->where('status', 'done_by_staff')
                    ->orderByDesc('task_date')
                    ->get();

                foreach ($tasksNeedPmVerify as $tv) {
                    $items->push($this->formatPmVerificationTaskItem($tv, $userId));
                }
            }
        }

        return $items;
    }

    private function formatSosmedTaskItem(SosmedTask $task, SosmedAccount $account, int $userId, bool $isPastUnapproved = false): Task
    {
        $dateFormatted = Carbon::parse($task->task_date)->translatedFormat('d M Y');
        $prefix = $isPastUnapproved ? "[Belum Disetujui] " : "";

        $item = new Task([
            'title'       => $prefix . ($task->title ?: ('Laporan Konten - ' . $account->name)),
            'kantor'      => null,
            'description' => $task->description ?: ($account->notes ?: 'Submit bukti konten harian.'),
            'type'        => 'sosmed',
        ]);
        $item->id = $task->id;
        $item->task_date = Carbon::parse($task->task_date);

        $creatorName = $account->pmUser?->name ?? $account->creator?->name ?? 'Admin';
        $item->setRelation('creator', (object)['name' => $creatorName]);

        $item->setRelation('assignments', collect([
            (object)[
                'id'           => $task->id,
                'task_id'      => $task->id,
                'user_id'      => $userId,
                'is_completed' => $task->status,
                'completed_at' => $task->status === 'approved_hr' ? ($task->hr_verified_at ?? $task->updated_at) : null,
                'note'         => $task->description ?? '',
            ]
        ]));

        $user = User::find($userId);
        $actionRoute = ($user && $user->role === 'pm') ? route('pm.sosmed.index') : route('sosmed.sosmed.index');

        $item->is_sosmed = true;
        $item->sosmed_status = $task->status;
        $item->is_past_unapproved = $isPastUnapproved;
        $item->platform = $account->platform;
        $item->platform_color = $account->platform_color;
        $item->action_url = $actionRoute;
        $item->links = is_array($task->link_upload) ? $task->link_upload : ($task->link_upload ? json_decode($task->link_upload, true) : []);
        $item->rejection_note = $task->rejection_note;
        $item->account = $account;

        return $item;
    }

    private function createPendingSosmedTaskItem(SosmedAccount $account, int $userId, string $today): Task
    {
        $item = new Task([
            'title'       => 'Kelola Akun: ' . $account->name,
            'kantor'      => null,
            'description' => 'Wajib kelola dan submit bukti konten harian akun ' . $account->name . ($account->notes ? ' - Catatan: ' . $account->notes : ''),
            'type'        => 'sosmed',
        ]);
        $item->id = 'sosmed_acc_' . $account->id;
        $item->task_date = Carbon::parse($today);

        $creatorName = $account->pmUser?->name ?? $account->creator?->name ?? 'Admin';
        $item->setRelation('creator', (object)['name' => $creatorName]);

        $item->setRelation('assignments', collect([
            (object)[
                'id'           => 0,
                'task_id'      => $item->id,
                'user_id'      => $userId,
                'is_completed' => 'pending',
                'completed_at' => null,
                'note'         => '',
            ]
        ]));

        $user = User::find($userId);
        $actionRoute = ($user && $user->role === 'pm') ? route('pm.sosmed.index') : route('sosmed.sosmed.index');

        $item->is_sosmed = true;
        $item->sosmed_status = 'pending';
        $item->is_past_unapproved = false;
        $item->platform = $account->platform;
        $item->platform_color = $account->platform_color;
        $item->action_url = $actionRoute;
        $item->links = [];
        $item->rejection_note = null;
        $item->account = $account;

        return $item;
    }

    private function formatPmVerificationTaskItem(SosmedTask $tv, int $userId): Task
    {
        $acc = $tv->account;
        $staffName = $tv->assignedToUser?->name ?? 'Staff Sosmed';

        $item = new Task([
            'title'       => 'Verifikasi Konten: ' . ($acc?->name ?? 'Akun Sosmed') . " ({$staffName})",
            'kantor'      => null,
            'description' => "Bukti konten telah disubmit oleh {$staffName}. Menunggu verifikasi Anda sebagai PM.",
            'type'        => 'sosmed',
        ]);
        $item->id = $tv->id;
        $item->task_date = Carbon::parse($tv->task_date);

        $item->setRelation('creator', (object)['name' => $staffName]);

        $item->setRelation('assignments', collect([
            (object)[
                'id'           => $tv->id,
                'task_id'      => $tv->id,
                'user_id'      => $userId,
                'is_completed' => 'done_by_staff',
                'completed_at' => null,
                'note'         => $tv->description ?? '',
            ]
        ]));

        $item->is_sosmed = true;
        $item->sosmed_status = 'done_by_staff';
        $item->is_pm_verification = true;
        $item->platform = $acc?->platform;
        $item->platform_color = $acc?->platform_color;
        $item->action_url = route('pm.sosmed.index', ['tab' => 'oversight']);
        $item->links = is_array($tv->link_upload) ? $tv->link_upload : ($tv->link_upload ? json_decode($tv->link_upload, true) : []);
        $item->rejection_note = $tv->rejection_note;
        $item->account = $acc;

        return $item;
    }

    // ── Private Helpers ──────────────────────────────────
    private function validateAssignees(array $userIds): void
    {
        if (empty($userIds)) {
            throw ValidationException::withMessages([
                'user_ids' => 'Pilih minimal satu penerima tugas.',
            ]);
        }

        $user = Auth::user();

        /** @var Builder $query */
        $query = User::query()->whereIn('id', $userIds);

        if ($user->isAdmin()) {
            // Admin tidak boleh assign ke sesama admin
            $invalid = (clone $query)->where('role', 'admin')->exists();
        } else {
            // Staff hanya boleh assign ke assistant
            $invalid = (clone $query)
                ->where('role', '!=', 'hr_assistant')
                ->whereDoesntHave('roleModel', fn($r) => $r->where('base_type', 'assistant'))
                ->exists();
        }

        if ($invalid) {
            throw ValidationException::withMessages([
                'user_ids' => 'Terdapat penerima yang tidak sesuai dengan hierarki role.',
            ]);
        }
    }

    public function forceDeleteTask(Task $task): bool
    {
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat dihapus karena sudah ada penerima yang menyelesaikannya.',
            ]);
        }

        return $this->taskRepository->delete($task);
    }

    private function attachAssigneesAndNotify(Task $task, array $userIds): void
    {
        $assignerName = Auth::user()->name;

        foreach ($userIds as $userId) {
            $this->taskRepository->createAssignment($task->id, (int) $userId);

            $user = $this->userRepository->findById((int) $userId);
            if ($user) {
                $user->notify(new TaskAssigned($task, $assignerName));
            }
        }
    }

    public function getTopRankings(string $period = 'week'): \Illuminate\Support\Collection
    {
        $users = app(\App\Repositories\UserRepository::class)->getAllExceptAdmin();

        return $users->map(function ($user) use ($period) {
            return [
                'user'  => $user,
                'score' => $this->taskRepository->getUserScore($user->id, $period),
            ];
        })
        ->sortByDesc('score')
        ->take(3)
        ->values();
    }
    public function getHistoryForUser(int $userId, ?string $date = null, ?string $search = null): Collection
    {
        return $this->taskRepository->getHistoryForUser($userId, $date, $search);
    }

    public function getHistoryForAdmin(?int $userId = null, ?string $date = null, ?string $search = null): Collection
    {
        return $this->taskRepository->getHistoryForAdmin($userId, $date, $search);
    }

    public function getTasksByStaff(): Collection
    {
        return $this->taskRepository->getTasksByStaff();
    }

    public function getTasksCreatedByAdmin(): Collection
    {
        return $this->taskRepository->getTasksCreatedByAdmin();
    }

    public function getAllTasksForAssistant(): Collection
    {
        return $this->taskRepository->getAllTasksForAssistant();
    }

    public function getAllTasksForRole(string $role): Collection
    {
        return $this->taskRepository->getAllTasksForRole($role);
    }

    public function getDailyStats(): array
    {
        return $this->taskRepository->getDailyStats();
    }

    public function getDailyStatsPerUser(): Collection
    {
        return $this->taskRepository->getDailyStatsPerUser();
    }

    public function getUserScore(int $userId, string $period): int
    {
        return $this->taskRepository->getUserScore($userId, $period);
    }

    public function getAssistantProgressForStaff(): Collection
    {
        return $this->taskRepository->getDailyStatsPerUser()
            ->where('role', 'hr_assistant');
    }

    public function getDefaultTasksForUser(int $userId): Collection
    {
        return $this->taskRepository->getDefaultTasksForUser($userId);
    }

    public function getAssignedTasksFromAdmin(int $userId): Collection
    {
        return $this->taskRepository->getAssignedTasksFromAdmin($userId);
    }

    public function getDefaultTasksForAssistant(int $userId): Collection
    {
        return $this->taskRepository->getDefaultTasksForAssistant($userId);
    }
    
    public function getAllAssignedTasksForAssistant(int $userId): Collection
    {
        return $this->taskRepository->getAllAssignedTasksForAssistant($userId);
    }

    public function getDailyStatsForAssistants(): Collection
    {
        return $this->taskRepository->getDailyStatsForAssistants();
    }

    // ── Admin: kelola assignment milik bawahan ───────────

    public function adminCompleteAssignment(int $assignmentId): bool
    {
        $assignment = $this->taskRepository->findAssignmentById($assignmentId);

        if (! $assignment) {
            throw ValidationException::withMessages([
                'assignment' => 'Data assignment tidak ditemukan.',
            ]);
        }

        if ($assignment->isCompleted()) {
            throw ValidationException::withMessages([
                'assignment' => 'Tugas ini sudah selesai.',
            ]);
        }

        return $this->taskRepository->adminCompleteAssignment($assignment);
    }

    public function adminDeleteAssignment(int $assignmentId): bool
    {
        $assignment = $this->taskRepository->findAssignmentById($assignmentId);

        if (! $assignment) {
            throw ValidationException::withMessages([
                'assignment' => 'Data assignment tidak ditemukan.',
            ]);
        }

        return $this->taskRepository->deleteAssignmentById($assignmentId);
    }

    public function getAssistantProgressByRange(string $dateFrom, string $dateTo): \Illuminate\Support\Collection
    {
        $assistants = app(\App\Repositories\UserRepository::class)
            ->getAllByRole('hr_assistant');

        return $assistants->map(function ($assistant) use ($dateFrom, $dateTo) {
            $assignments = \App\Models\TaskAssignment::query()
                ->where('user_id', $assistant->id)
                ->whereHas('task', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('task_date', [$dateFrom, $dateTo]);
                })
                ->get();

            $total     = $assignments->count();
            $completed = $assignments->where('is_completed', 'completed')->count();
            $notDone   = $assignments->where('is_completed', 'not_done')->count();
            $pending   = $total - $completed - $notDone;

            // Inject properties agar view bisa pakai notasi objek
            $assistant->total_tasks     = $total;
            $assistant->completed_tasks = $completed;
            $assistant->not_done_tasks  = $notDone;
            $assistant->pending_tasks   = $pending;

            return $assistant;
        });
    }

    /**
     * Urutkan koleksi tugas berdasarkan status:
     * pending/rejected → done_by_staff/verified_by_pm → not_done → completed/approved_hr
     */
    public function sortTasksByStatus(\Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $tasks, int $userId = 0): \Illuminate\Support\Collection
    {
        $order = [
            'pending'        => 0,
            'rejected'       => 0,
            'done_by_staff'  => 1,
            'verified_by_pm' => 1,
            'not_done'       => 2,
            'approved_hr'    => 3,
            'completed'      => 3,
        ];

        return $tasks->sortBy(function ($task) use ($order, $userId) {
            $assignment = $userId
                ? $task->assignments->firstWhere('user_id', $userId)
                : $task->assignments->first();
            $status = $assignment?->is_completed ?? 'pending';
            return $order[$status] ?? 0;
        })->values();
    }

    // ── Laporan Produktivitas: rentang tanggal ───────────

    public function getProductivityByRange(string $dateFrom, string $dateTo): \Illuminate\Support\Collection
    {
        return $this->taskRepository->getProductivityByRange($dateFrom, $dateTo);
    }

    public function getProductivityDetailForUser(int $userId, string $dateFrom, string $dateTo): \Illuminate\Database\Eloquent\Collection
    {
        return $this->taskRepository->getProductivityDetailForUser($userId, $dateFrom, $dateTo);
    }
}