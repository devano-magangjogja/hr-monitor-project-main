<?php

namespace App\Services;

use App\Models\DefaultTask;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Repositories\DefaultTaskRepository;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DefaultTaskService
{
    public function __construct(
        protected DefaultTaskRepository $defaultTaskRepository,
        protected TaskRepository        $taskRepository,
    ) {}

    public function getAll(): Collection
    {
        return $this->defaultTaskRepository->getAll();
    }

    public function create(array $data): DefaultTask
    {
        return $this->defaultTaskRepository->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'target_role' => $data['target_role'],
            'is_active'   => $data['is_active'] ?? 1,
            'created_by'  => Auth::id(),
        ]);
    }

    public function update(DefaultTask $defaultTask, array $data): bool
    {
        return $this->defaultTaskRepository->update($defaultTask, [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'target_role' => $data['target_role'],
            'is_active'   => $data['is_active'] ?? 1,
        ]);
    }

    public function delete(DefaultTask $defaultTask): bool
    {
        return $this->defaultTaskRepository->delete($defaultTask);
    }

    /**
     * Di-trigger oleh Scheduler setiap hari jam 08:00.
     * Generate instance task harian dari semua active default task.
     */
    public function generateDailyTasks(): void
    {
        $today         = Carbon::today()->toDateString();
        $defaultTasks  = $this->defaultTaskRepository->getAllActive();
        $generated     = 0;

        foreach ($defaultTasks as $defaultTask) {
            // Cegah duplikasi jika scheduler dijalankan lebih dari sekali
            if ($this->taskRepository->isDefaultTaskAlreadyGenerated($defaultTask->id, $today)) {
                continue;
            }

            // Ambil semua user aktif sesuai target_role
            $users = $this->taskRepository->getUsersByRole($defaultTask->target_role);

            if ($users->isEmpty()) {
                continue;
            }

            // Buat satu task per default task
            $task = $this->taskRepository->create([
                'title'           => $defaultTask->title,
                'description'     => $defaultTask->description,
                'task_date'       => $today,
                'type'            => 'default',
                'created_by'      => null, // dibuat sistem
                'default_task_id' => $defaultTask->id,
            ]);

            // Assign ke semua user dengan role yang sesuai
            foreach ($users as $user) {
                $this->taskRepository->createAssignment($task->id, $user->id);
            }

            $generated++;
        }

        Log::info("[Scheduler] Default task generated: {$generated} task(s) for {$today}");
    }
}