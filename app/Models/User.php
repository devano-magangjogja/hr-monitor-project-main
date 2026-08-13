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

    public function isProgrammer(): bool
    {
        return $this->role === 'programmer';
    }

    public function isDg(): bool
    {
        return $this->role === 'dg';
    }

    public function isVg(): bool
    {
        return $this->role === 'vg';
    }

    public function isPm(): bool
    {
        return $this->role === 'pm';
    }

    /**
     * Label resmi role lengkap dengan kepanjangan singkatan
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'        => 'Administrator',
            'hr_staff'     => 'HR Staff',
            'hr_assistant' => 'HR Assistant',
            'cs'           => 'CS (Customer Service)',
            'ob'           => 'OB (Office Boy)',
            'programmer'   => 'Programmer',
            'dg'           => 'DG (Design Graphics)',
            'vg'           => 'VG (Videografer)',
            'pm'           => 'PM (Project Manager)',
            default        => strtoupper($this->role),
        };
    }

    /**
     * Kelas CSS warna badge untuk role
     */
    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'admin'        => 'bg-red-50 text-red-700',
            'hr_staff'     => 'bg-blue-50 text-blue-700',
            'hr_assistant' => 'bg-purple-50 text-purple-700',
            'cs'           => 'bg-teal-50 text-teal-700',
            'ob'           => 'bg-orange-50 text-orange-700',
            'programmer'   => 'bg-cyan-50 text-cyan-700',
            'dg'           => 'bg-rose-50 text-rose-700',
            'vg'           => 'bg-amber-50 text-amber-700',
            'pm'           => 'bg-indigo-50 text-indigo-700',
            default        => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Apakah role ini setara "staff mandiri" (tidak punya bawahan)?
     * cs, ob, programmer, dg, vg, pm memakai pola dashboard/tugas mandiri
     * tetapi tanpa fitur kelola assistant.
     */
    public function isStandalone(): bool
    {
        return in_array($this->role, ['cs', 'ob', 'programmer', 'dg', 'vg', 'pm']);
    }
}