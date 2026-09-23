<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultTask extends Model
{
    protected $table = 'default_tasks';

    // Tidak pakai AUTO_INCREMENT di DB, jadi kita handle manual
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'title',
        'description',
        'target_role',
        'is_active',
        'proof_requirement',
        'created_by',
        'assigned_user_ids'
    ];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'target_role' => 'string',
            'proof_requirement' => 'string',
            'assigned_user_ids' => 'array',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'default_task_id');
    }
}