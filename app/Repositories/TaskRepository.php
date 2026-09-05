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

        // Tampilkan tugas yang dibuat staff ini (type assigned) HANYA hari ini
        // ATAU tugas default yang ditugaskan ke HR Assistant (carry-over pending tetap untuk default)
        return $query
            ->with(['assignments', 'assignedUsers:id,name,role'])
            ->where(function ($q) use ($staffId) {
                $q->where(function ($q1) use ($staffId) {
                    $q1->where('created_by', $staffId)
                       ->where('type', 'assigned');
                })
                ->orWhereHas('assignments.user', function ($q2) {
                    $q2->where('role', 'hr_assistant');
                });
            })
            // assigned: hanya hari ini | default: hanya hari ini
            ->whereDate('task_date', Carbon::today())
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
        $today = Carbon::today()->toDateString();
        /** @var Builder $query */
        $query = $this->model->newQuery();

        // Tampilkan tugas mandiri hari ini ATAU tugas mandiri lama yang masih pending ATAU diselesaikan hari ini
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->where('created_by', $userId)
            ->where('type', 'self')
            ->where(function ($q) use ($userId, $today) {
                $q->whereDate('task_date', $today)
                  ->orWhereHas('assignments', function ($q2) use ($userId, $today) {
                      $q2->where('user_id', $userId)
                         ->where(function ($q3) use ($today) {
                             $q3->where('is_completed', 'pending')
                                ->orWhere(function ($q4) use ($today) {
                                    $q4->where('is_completed', 'completed')
                                       ->whereDate('completed_at', $today);
                                });
                         });
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
        $today = Carbon::today()->toDateString();
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
            // assigned: hanya hari ini (tidak carry-over ke hari berikutnya)
            // self: hari ini ATAU masih pending ATAU diselesaikan hari ini
            ->where(function ($q) use ($userId, $today) {
                $q->where(function ($q2) use ($today) {
                      // default task: hanya hari ini
                      $q2->where('type', 'default')
                         ->whereDate('task_date', $today);
                  })
                  ->orWhere(function ($q2) use ($today) {
                      // assigned: hanya hari ini
                      $q2->where('type', 'assigned')
                         ->whereDate('task_date', $today);
                  })
                  ->orWhere(function ($q2) use ($userId, $today) {
                      // self: hari ini ATAU masih pending ATAU diselesaikan hari ini
                      $q2->where('type', 'self')
                         ->whereDate('task_date', '<=', $today)
                         ->where(function ($q3) use ($userId, $today) {
                             $q3->whereDate('task_date', $today)
                                ->orWhereHas('assignments', function ($q4) use ($userId, $today) {
                                    $q4->where('user_id', $userId)
                                       ->where(function ($q5) use ($today) {
                                           $q5->where('is_completed', 'pending')
                                              ->orWhere(function ($q6) use ($today) {
                                                  $q6->where('is_completed', 'completed')
                                                     ->whereDate('completed_at', $today);
                                              });
                                       });
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
        $today = Carbon::today()->toDateString();
        /** @var Builder $query */
        $query = $this->model->newQuery();

        // default: hanya hari ini
        // assigned: hanya hari ini (tidak carry-over)
        // self: hari ini + pending lama + selesai hari ini
        return $query
            ->with(['creator:id,name', 'assignments', 'assignedUsers:id,name,role'])
            ->whereHas('assignments', function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('role', 'hr_assistant');
                });
            })
            ->where(function ($q) use ($today) {
                $q->where(function ($q2) use ($today) {
                      // default: hanya hari ini
                      $q2->where('type', 'default')
                         ->whereDate('task_date', $today);
                  })
                  ->orWhere(function ($q2) use ($today) {
                      // assigned: hanya hari ini
                      $q2->where('type', 'assigned')
                         ->whereDate('task_date', $today);
                  })
                  ->orWhere(function ($q2) use ($today) {
                      // self: hari ini + pending lama + selesai hari ini
                      $q2->where('type', 'self')
                         ->whereDate('task_date', '<=', $today)
                         ->where(function ($q3) use ($today) {
                             $q3->whereDate('task_date', $today)
                                ->orWhereHas('assignments', function ($q4) use ($today) {
                                    $q4->whereHas('user', function ($q5) {
                                        $q5->where('role', 'hr_assistant');
                                    })
                                    ->where(function ($q6) use ($today) {
                                        $q6->where('is_completed', 'pending')
                                           ->orWhere(function ($q7) use ($today) {
                                               $q7->where('is_completed', 'completed')
                                                  ->whereDate('completed_at', $today);
                                           });
                                    });
                                });
                         });
                  });
            })
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Ambil semua tugas yang masuk ke role tertentu (cs, ob, programmer, vg, dg, pm, dst.)
     * dengan pola yang sama seperti getAllTasksForAssistant.
     */
    public function getAllTasksForRole(string $role): Collection
    {
        $today = Carbon::today()->toDateString();
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['creator:id,name', 'assignments', 'assignedUsers:id,name,role'])
            ->whereHas('assignments', function ($q) use ($role) {
                $q->whereHas('user', function ($q) use ($role) {
                    $q->where('role', $role);
                });
            })
            ->where(function ($q) use ($role, $today) {
                $q->where(function ($q2) use ($today) {
                      // default: hanya hari ini
                      $q2->where('type', 'default')
                         ->whereDate('task_date', $today);
                  })
                  ->orWhere(function ($q2) use ($today) {
                      // assigned: hanya hari ini (tidak carry-over)
                      $q2->where('type', 'assigned')
                         ->whereDate('task_date', $today);
                  })
                  ->orWhere(function ($q2) use ($role, $today) {
                      // self: hari ini + pending lama + selesai hari ini
                      $q2->where('type', 'self')
                         ->whereDate('task_date', '<=', $today)
                         ->where(function ($q3) use ($role, $today) {
                             $q3->whereDate('task_date', $today)
                                ->orWhereHas('assignments', function ($q4) use ($role, $today) {
                                    $q4->whereHas('user', function ($q5) use ($role) {
                                        $q5->where('role', $role);
                                    })
                                    ->where(function ($q6) use ($today) {
                                        $q6->where('is_completed', 'pending')
                                           ->orWhere(function ($q7) use ($today) {
                                               $q7->where('is_completed', 'completed')
                                                  ->whereDate('completed_at', $today);
                                           });
                                    });
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
        $today = Carbon::today()->toDateString();

        // Assignment yang aktif/berlaku hari ini:
        // 1. Task hari ini (task_date == today)
        // 2. Task lama yang masih pending (task_date < today and is_completed == pending)
        // 3. Task lama yang diselesaikan HARI INI (is_completed == completed and DATE(completed_at) == today)
        $activeAssignmentFilter = function ($q) use ($today) {
            $q->where(function ($q2) use ($today) {
                $q2->whereHas('task', function ($t) use ($today) {
                    $t->whereDate('task_date', $today);
                });
            })->orWhere(function ($q2) use ($today) {
                $q2->whereHas('task', function ($t) use ($today) {
                    $t->whereDate('task_date', '<', $today);
                })->where(function ($q3) use ($today) {
                    $q3->where('is_completed', 'pending')
                       ->orWhere(function ($q4) use ($today) {
                           $q4->where('is_completed', 'completed')
                              ->whereDate('completed_at', $today);
                       });
                });
            });
        };

        $total = TaskAssignment::query()->whereHas('task')->where($activeAssignmentFilter)->count();

        $completed = TaskAssignment::query()->whereHas('task')->where('is_completed', 'completed')
            ->where(function ($q) use ($today) {
                $q->whereDate('completed_at', $today)
                  ->orWhere(function ($q2) use ($today) {
                      $q2->whereNull('completed_at')
                         ->whereHas('task', fn($t) => $t->whereDate('task_date', $today));
                  });
            })->count();

        $notDone = TaskAssignment::query()->whereHas('task', fn($t) => $t->whereDate('task_date', $today))
            ->where('is_completed', 'not_done')
            ->count();

        $pending = max(0, $total - $completed - $notDone);

        return [
            'total'     => $total,
            'completed' => $completed,
            'pending'   => $pending,
            'not_done'  => $notDone,
        ];
    }

    public function getDailyStatsPerUser(): Collection
    {
        $today = Carbon::today()->toDateString();

        return \App\Models\User::query()
            ->where('role', '!=', 'admin')
            ->where('is_active', 1)
            ->withCount([
                'taskAssignments as total_tasks' => function ($q) use ($today) {
                    $q->where(function ($q2) use ($today) {
                        $q2->whereHas('task', function ($t) use ($today) {
                            $t->whereDate('task_date', $today);
                        });
                    })->orWhere(function ($q2) use ($today) {
                        $q2->whereHas('task', function ($t) use ($today) {
                            $t->whereDate('task_date', '<', $today);
                        })->where(function ($q3) use ($today) {
                            $q3->where('is_completed', 'pending')
                               ->orWhere(function ($q4) use ($today) {
                                   $q4->where('is_completed', 'completed')
                                      ->whereDate('completed_at', $today);
                               });
                        });
                    });
                },
                'taskAssignments as completed_tasks' => function ($q) use ($today) {
                    $q->where('is_completed', 'completed')
                      ->where(function ($q2) use ($today) {
                          $q2->whereDate('completed_at', $today)
                             ->orWhere(function ($q3) use ($today) {
                                 $q3->whereNull('completed_at')
                                    ->whereHas('task', fn($t) => $t->whereDate('task_date', $today));
                             });
                      });
                },
                'taskAssignments as not_done_tasks' => function ($q) use ($today) {
                    $q->where('is_completed', 'not_done')
                      ->whereHas('task', function ($t) use ($today) {
                          $t->whereDate('task_date', $today);
                      });
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
            $start = Carbon::now()->startOfWeek();
            $end   = Carbon::now()->endOfWeek();
            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('completed_at', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->whereNull('completed_at')
                         ->whereHas('task', function ($t) use ($start, $end) {
                             $t->whereBetween('task_date', [$start, $end]);
                         });
                  });
            });
        } elseif ($period === 'month') {
            $month = Carbon::now()->month;
            $year  = Carbon::now()->year;
            $query->where(function ($q) use ($month, $year) {
                $q->where(function ($q2) use ($month, $year) {
                    $q2->whereMonth('completed_at', $month)
                       ->whereYear('completed_at', $year);
                })->orWhere(function ($q2) use ($month, $year) {
                    $q2->whereNull('completed_at')
                       ->whereHas('task', function ($t) use ($month, $year) {
                           $t->whereMonth('task_date', $month)
                             ->whereYear('task_date', $year);
                       });
                });
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
        $today = Carbon::today()->toDateString();
        /** @var Builder $query */
        $query = $this->model->newQuery();

        // Tampilkan tugas dari admin: hari ini ATAU masih pending dari hari sebelumnya ATAU diselesaikan hari ini
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
            ->where(function ($q) use ($userId, $today) {
                $q->whereDate('task_date', $today)
                  ->orWhereHas('assignments', function ($q2) use ($userId, $today) {
                      $q2->where('user_id', $userId)
                         ->where(function ($q3) use ($today) {
                             $q3->where('is_completed', 'pending')
                                ->orWhere(function ($q4) use ($today) {
                                    $q4->where('is_completed', 'completed')
                                       ->whereDate('completed_at', $today);
                                });
                         });
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
        $today = Carbon::today()->toDateString();
        /** @var Builder $query */
        $query = $this->model->newQuery();

        // Tampilkan tugas dari admin/staff: HANYA hari ini (tidak carry-over ke hari berikutnya)
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }, 'creator:id,name,role'])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('type', 'assigned')
            ->whereDate('task_date', $today)
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getDailyStatsForAssistants(): Collection
    {
        $today = Carbon::today()->toDateString();

        return \App\Models\User::query()
            ->where('role', 'hr_assistant')
            ->where('is_active', 1)
            ->withCount([
                'taskAssignments as total_tasks' => function ($q) use ($today) {
                    $q->where(function ($q2) use ($today) {
                        $q2->whereHas('task', function ($t) use ($today) {
                            $t->whereDate('task_date', $today);
                        });
                    })->orWhere(function ($q2) use ($today) {
                        $q2->whereHas('task', function ($t) use ($today) {
                            $t->whereDate('task_date', '<', $today);
                        })->where(function ($q3) use ($today) {
                            $q3->where('is_completed', 'pending')
                               ->orWhere(function ($q4) use ($today) {
                                   $q4->where('is_completed', 'completed')
                                      ->whereDate('completed_at', $today);
                               });
                        });
                    });
                },
                'taskAssignments as completed_tasks' => function ($q) use ($today) {
                    $q->where('is_completed', 'completed')
                      ->where(function ($q2) use ($today) {
                          $q2->whereDate('completed_at', $today)
                             ->orWhere(function ($q3) use ($today) {
                                 $q3->whereNull('completed_at')
                                    ->whereHas('task', fn($t) => $t->whereDate('task_date', $today));
                             });
                      });
                },
                'taskAssignments as not_done_tasks' => function ($q) use ($today) {
                    $q->where('is_completed', 'not_done')
                      ->whereHas('task', function ($t) use ($today) {
                          $t->whereDate('task_date', $today);
                      });
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
            ->where('role', '!=', 'admin')
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
