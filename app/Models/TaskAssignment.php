<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    protected $table = 'task_assignments';

    protected $fillable = [
        'task_id',
        'user_id',
        'is_completed',
        'completed_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
            'is_completed' => 'string',
        ];
    }

    // ── Helper Status ────────────────────────────────────
    public function isPending(): bool
    {
        return $this->is_completed === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->is_completed === 'completed';
    }

    public function isNotDone(): bool
    {
        return $this->is_completed === 'not_done';
    }

    // ── Relasi ──────────────────────────────────────────
    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}