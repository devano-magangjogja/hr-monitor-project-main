<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'task_date',
        'type',
        'kantor',
        'proof_requirement',
        'created_by',
        'default_task_id',
    ];

    protected function casts(): array
    {
        return [
            'task_date'         => 'date',
            'type'              => 'string',
            'proof_requirement' => 'string',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function defaultTask()
    {
        return $this->belongsTo(DefaultTask::class, 'default_task_id');
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class, 'task_id');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_assignments', 'task_id', 'user_id')
                    ->withPivot(['is_completed', 'completed_at'])
                    ->withTimestamps();
    }
}