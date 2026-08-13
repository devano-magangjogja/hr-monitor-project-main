<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class TaskRepository
{
    public function __construct(protected Task $model) {}

    public function getAllForAdmin(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['creator:id,name', 'assignedUsers:id,name,role'])
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAssignedTasksForStaff(int $staffId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        // Tampilkan tugas yang dibuat hari ini ATAU tugas lama yang masih ada
        // penerima dengan status pending (belum diselesaikan)
        return $query
            ->with(['assignments', 'assignedUsers:id,name,role'])
            ->where('created_by', $staffId)
            ->where('type', 'assigned')
            ->where(function ($q) {
                $q->whereDate('task_date', Carbon::today())
                  ->orWhereHas('assignments', function ($q2) {
                      $q2->where('is_completed', 'pending');
                  });
            })
            ->whereDate('task_date', '<=', Carbon::today())
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

   public function findById(int $id): ?Task
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query->with(['assignments', 'assignedUsers'])->find($id);
    }

    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): bool
    {
        /** @var Task $task */
        return $task->delete();
    }

    public function createAssignment(int $taskId, int $userId): TaskAssignment
    {
        return TaskAssignment::create([
            'task_id'      => $taskId,
            'user_id'      => $userId,
            'is_completed' => 'pending', 
        ]);
    }

    public function deleteAssignments(int $taskId): void
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();
        $query->where('task_id', $taskId)->delete();
    }
    public function hasAnyCompleted(int $taskId): bool
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();
    
        return $query
            ->where('task_id', $taskId)
            ->where('is_completed', 'completed')
            ->exists();
    }

    public function markAllPendingAsNotDone(string $today): int
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();
    
        return $query
            ->whereHas('task', function ($q) use ($today) {
                // Hanya default task hingga hari ini yang masih pending
                // Tugas assigned dan self TIDAK ditandai not_done
                $q->whereDate('task_date', '<=', $today)
                  ->where('type', 'default');
            })
            ->where('is_completed', 'pending')
            ->update(['is_completed' => 'not_done']);
    }

    public function getSelfTasksToday(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        // Tampilkan tugas mandiri hari ini ATAU tugas mandiri lama yang masih pending
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->where('created_by', $userId)
            ->where('type', 'self')
            ->where(function ($q) use ($userId) {
                $q->whereDate('task_date', Carbon::today())
                  ->orWhereHas('assignments', function ($q2) use ($userId) {
                      $q2->where('user_id', $userId)
                         ->where('is_completed', 'pending');
                  });
            })
            ->whereDate('task_date', '<=', Carbon::today())
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function findAssignment(int $taskId, int $userId): ?TaskAssignment
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();

        return $query
            ->where('task_id', $taskId)
            ->where('user_id', $userId)
            ->first();
    }

    public function completeAssignment(TaskAssignment $assignment, ?string $note): bool
    {
        return (bool) $assignment->update([
            'is_completed' => 'completed',
            'completed_at' => now(),
            'note'         => $note,
        ]);
    }

    public function getAllTasksForUserToday(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }, 'creator:id,name'])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            // default: hanya hari ini
            // assigned + self: hari ini ATAU masih pending dari hari sebelumnya
            ->where(function ($q) use ($userId) {
                $q->where(function ($q2) {
                      // default task: hanya hari ini
                      $q2->where('type', 'default')
                         ->whereDate('task_date', Carbon::today());
                  })
                  ->orWhere(function ($q2) use ($userId) {
                      // assigned / self: tampilkan selama masih pending atau hari ini
                      $q2->whereIn('type', ['assigned', 'self'])
                         ->whereDate('task_date', '<=', Carbon::today())
                         ->where(function ($q3) use ($userId) {
                             $q3->whereDate('task_date', Carbon::today())
                                ->orWhereHas('assignments', function ($q4) use ($userId) {
                                    $q4->where('user_id', $userId)
                                       ->where('is_completed', 'pending');
                                });
                         });
                  });
            })
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getHistoryForUser(int $userId, ?string $date = null, ?string $search = null): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }, 'creator:id,name'])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->when($date, function ($q) use ($date) {
                $q->whereDate('task_date', $date);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('title', 'like', '%' . $search . '%')
                       ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }
    public function getHistoryForAdmin(?int $userId = null, ?string $date = null, ?string $search = null): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with([
                'assignments' => function ($q) use ($userId) {
                    if ($userId) {
                        $q->where('user_id', $userId);
                    }
                },
                'assignedUsers:id,name,role',
                'creator:id,name',
            ])
            ->when($userId, function ($q) use ($userId) {
                $q->whereHas('assignments', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            })
            ->when($date, function ($q) use ($date) {
                $q->whereDate('task_date', $date);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('title', 'like', '%' . $search . '%')
                       ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getTasksCreatedByAdmin(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['creator:id,name', 'assignments', 'assignedUsers:id,name,role'])
            ->whereHas('creator', function ($q) {
                $q->where('role', 'admin');
            })
            ->where('type', 'assigned')
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }
    public function getTasksByStaff(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        // Tasks created by any standalone role (hr_staff, cs, ob) — self + assigned
        $staffCreatedTasks = $query
            ->with(['creator:id,name', 'assignments', 'assignedUsers:id,name,role'])
            ->whereHas('creator', function ($q) {
                $q->whereIn('role', ['hr_staff', 'cs', 'ob']);
            })
            ->whereIn('type', ['self', 'assigned'])
            ->whereDate('task_date', Carbon::today());

        // Union with tasks assigned by admin to ANY staff-level member
        return $staffCreatedTasks->union(
            Task::query()
                ->with(['creator:id,name', 'assignments', 'assignedUsers:id,name,role'])
                ->whereHas('assignments', function ($q) {
                    $q->whereHas('user', function ($q) {
                        $q->whereIn('role', ['hr_staff', 'cs', 'ob']);
                    });
                })
                ->whereHas('creator', function ($q) {
                    $q->where('role', 'admin');
                })
                ->where('type', 'assigned')
                ->whereDate('task_date', Carbon::today())
        )
        ->orderByDesc('created_at')
        ->get();
    }

    public function getAllTasksForAssistant(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        // default: hanya hari ini | assigned: hari ini + pending lama
        return $query
            ->with(['creator:id,name', 'assignments', 'assignedUsers:id,name,role'])
            ->whereHas('assignments', function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('role', 'hr_assistant');
                });
            })
            ->where(function ($q) {
                $q->where(function ($q2) {
                      $q2->where('type', 'default')
                         ->whereDate('task_date', Carbon::today());
                  })
                  ->orWhere(function ($q2) {
                      $q2->whereIn('type', ['assigned', 'self'])
                         ->whereDate('task_date', '<=', Carbon::today())
                         ->where(function ($q3) {
                             $q3->whereDate('task_date', Carbon::today())
                                ->orWhereHas('assignments', function ($q4) {
                                    $q4->whereHas('user', function ($q5) {
                                        $q5->where('role', 'hr_assistant');
                                    })
                                    ->where('is_completed', 'pending');
                                });
                         });
                  });
            })
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Ambil semua tugas yang masuk ke role tertentu (cs, ob, dst.)
     * dengan pola yang sama seperti getAllTasksForAssistant.
     */
    public function getAllTasksForRole(string $role): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['creator:id,name', 'assignments', 'assignedUsers:id,name,role'])
            ->whereHas('assignments', function ($q) use ($role) {
                $q->whereHas('user', function ($q) use ($role) {
                    $q->where('role', $role);
                });
            })
            ->where(function ($q) use ($role) {
                $q->where(function ($q2) {
                      $q2->where('type', 'default')
                         ->whereDate('task_date', Carbon::today());
                  })
                  ->orWhere(function ($q2) use ($role) {
                      $q2->whereIn('type', ['assigned', 'self'])
                         ->whereDate('task_date', '<=', Carbon::today())
                         ->where(function ($q3) use ($role) {
                             $q3->whereDate('task_date', Carbon::today())
                                ->orWhereHas('assignments', function ($q4) use ($role) {
                                    $q4->whereHas('user', function ($q5) use ($role) {
                                        $q5->where('role', $role);
                                    })
                                    ->where('is_completed', 'pending');
                                });
                         });
                  });
            })
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function isDefaultTaskAlreadyGenerated(int $defaultTaskId, string $date): bool
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->where('default_task_id', $defaultTaskId)
            ->whereDate('task_date', $date)
            ->exists();
    }
    public function getUsersByRole(string $role): Collection
    {
        return \App\Models\User::query()
            ->where('role', $role)
            ->where('is_active', 1)
            ->get();
    }

    public function getDailyStats(): array
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();
    
        $total = $query->whereHas('task', function ($q) {
            $q->whereDate('task_date', Carbon::today());
        })->count();
    
        $completed = TaskAssignment::query()
            ->whereHas('task', function ($q) {
                $q->whereDate('task_date', Carbon::today());
            })
            ->where('is_completed', 'completed')
            ->count();
    
        $notDone = TaskAssignment::query()
            ->whereHas('task', function ($q) {
                $q->whereDate('task_date', Carbon::today());
            })
            ->where('is_completed', 'not_done')
            ->count();
    
        return [
            'total'     => $total,
            'completed' => $completed,
            'pending'   => $total - $completed - $notDone,
            'not_done'  => $notDone,
        ];
    }

    public function getDailyStatsPerUser(): Collection
    {
        return \App\Models\User::query()
            ->whereIn('role', ['hr_staff', 'hr_assistant', 'cs', 'ob'])
            ->where('is_active', 1)
            ->withCount([
                'taskAssignments as total_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    });
                },
                'taskAssignments as completed_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    })->where('is_completed', 'completed');
                },
                'taskAssignments as not_done_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    })->where('is_completed', 'not_done');
                },
            ])
            ->orderBy('role')
            ->orderBy('name')
            ->get();
    }

    public function getUserScore(int $userId, string $period): int
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();

        $query->where('user_id', $userId)
            ->where('is_completed', 'completed');

        if ($period === 'week') {
            $query->whereHas('task', function ($q) {
                $q->whereBetween('task_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
            });
        } elseif ($period === 'month') {
            $query->whereHas('task', function ($q) {
                $q->whereMonth('task_date', Carbon::now()->month)
                ->whereYear('task_date', Carbon::now()->year);
            });
        }
        return $query->count();
    }
    
    public function getDefaultTasksForUser(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();
    
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('type', 'default')
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }
    
    public function getAssignedTasksFromAdmin(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        // Tampilkan tugas dari admin: hari ini ATAU masih pending dari hari sebelumnya
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }, 'creator:id,name'])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereHas('creator', function ($q) {
                $q->where('role', 'admin');
            })
            ->where('type', 'assigned')
            ->whereDate('task_date', '<=', Carbon::today())
            ->where(function ($q) use ($userId) {
                $q->whereDate('task_date', Carbon::today())
                  ->orWhereHas('assignments', function ($q2) use ($userId) {
                      $q2->where('user_id', $userId)
                         ->where('is_completed', 'pending');
                  });
            })
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getDefaultTasksForAssistant(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();
    
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('type', 'default')
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }
    
    public function getAllAssignedTasksForAssistant(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        // Tampilkan tugas dari admin/staff: hari ini ATAU masih pending dari hari sebelumnya
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }, 'creator:id,name,role'])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('type', 'assigned')
            ->whereDate('task_date', '<=', Carbon::today())
            ->where(function ($q) use ($userId) {
                $q->whereDate('task_date', Carbon::today())
                  ->orWhereHas('assignments', function ($q2) use ($userId) {
                      $q2->where('user_id', $userId)
                         ->where('is_completed', 'pending');
                  });
            })
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getDailyStatsForAssistants(): Collection
    {
        return \App\Models\User::query()
            ->where('role', 'hr_assistant')
            ->where('is_active', 1)
            ->withCount([
                'taskAssignments as total_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    });
                },
                'taskAssignments as completed_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    })->where('is_completed', 'completed');
                },
                'taskAssignments as not_done_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    })->where('is_completed', 'not_done');
                },
            ])
            ->orderBy('name')
            ->get();
    }

    // ── Admin: kelola assignment milik bawahan ───────────

    public function findAssignmentById(int $assignmentId): ?TaskAssignment
    {
        return TaskAssignment::with('task')->find($assignmentId);
    }

    public function adminCompleteAssignment(TaskAssignment $assignment): bool
    {
        return (bool) $assignment->update([
            'is_completed' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function deleteAssignmentById(int $assignmentId): bool
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();
        return (bool) $query->where('id', $assignmentId)->delete();
    }

    // ── Laporan Produktivitas: rentang tanggal ───────────

    /**
     * Ringkasan produktivitas semua user (hr_staff + hr_assistant)
     * dalam rentang tanggal dateFrom – dateTo.
     *
     * @return \Illuminate\Support\Collection<int, array{user: \App\Models\User, total: int, completed: int, pending: int, not_done: int, pct: int}>
     */
    public function getProductivityByRange(string $dateFrom, string $dateTo): \Illuminate\Support\Collection
    {
        $users = \App\Models\User::query()
            ->whereIn('role', ['hr_staff', 'hr_assistant', 'cs', 'ob'])
            ->where('is_active', 1)
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return $users->map(function ($user) use ($dateFrom, $dateTo) {
            $assignments = TaskAssignment::query()
                ->where('user_id', $user->id)
                ->whereHas('task', function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('task_date', [$dateFrom, $dateTo]);
                })
                ->get();

            $total     = $assignments->count();
            $completed = $assignments->where('is_completed', 'completed')->count();
            $notDone   = $assignments->where('is_completed', 'not_done')->count();
            $pending   = $total - $completed - $notDone;
            $pct       = $total > 0 ? round(($completed / $total) * 100) : 0;

            return compact('user', 'total', 'completed', 'pending', 'notDone', 'pct');
        });
    }

    /**
     * Detail tugas satu user dalam rentang tanggal dateFrom – dateTo,
     * dikelompokkan per hari (task_date), diurutkan terbaru dahulu.
     */
    public function getProductivityDetailForUser(int $userId, string $dateFrom, string $dateTo): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with([
                'assignments' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                },
                'creator:id,name,role',
            ])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereBetween('task_date', [$dateFrom, $dateTo])
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }
}
