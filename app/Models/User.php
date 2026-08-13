<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'image',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'role'      => 'string',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignment::class, 'user_id');
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function createdDefaultTasks()
    {
        return $this->hasMany(DefaultTask::class, 'created_by');
    }

    // ── Helper Role ──────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHrStaff(): bool
    {
        return $this->role === 'hr_staff';
    }

    public function isHrAssistant(): bool
    {
        return $this->role === 'hr_assistant';
    }

    public function isCs(): bool
    {
        return $this->role === 'cs';
    }

    public function isOb(): bool
    {
        return $this->role === 'ob';
    }

    /**
     * Apakah role ini setara "staff mandiri" (tidak punya bawahan)?
     * cs dan ob memakai pola dashboard/tugas yang sama dengan staff
     * tetapi tanpa fitur kelola assistant.
     */
    public function isStandalone(): bool
    {
        return in_array($this->role, ['cs', 'ob']);
    }
}