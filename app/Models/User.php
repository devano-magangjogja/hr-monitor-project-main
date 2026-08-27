<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

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

    // ── Helper Role & Base Type ──────────────────────────

    public function getBaseTypeAttribute(): string
    {
        if ($this->relationLoaded('roleModel') && $this->roleModel) {
            return $this->roleModel->base_type;
        }

        // Cache lookup or fallback
        $role = Role::where('name', $this->role)->first();
        if ($role) {
            return $role->base_type;
        }

        return match ($this->role) {
            'admin'        => 'admin',
            'hr_staff'     => 'staff',
            'hr_assistant' => 'assistant',
            default        => 'member',
        };
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->base_type === 'admin';
    }

    public function isHrStaff(): bool
    {
        return $this->role === 'hr_staff' || $this->base_type === 'staff';
    }

    public function isHrAssistant(): bool
    {
        return $this->role === 'hr_assistant' || $this->base_type === 'assistant';
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
        if ($this->relationLoaded('roleModel') && $this->roleModel) {
            return $this->roleModel->label;
        }

        $role = Role::where('name', $this->role)->first();
        if ($role) {
            return $role->label;
        }

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
        if ($this->relationLoaded('roleModel') && $this->roleModel) {
            return $this->roleModel->badge_class;
        }

        $role = Role::where('name', $this->role)->first();
        if ($role) {
            return $role->badge_class;
        }

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
     * cs, ob, programmer, dg, vg, pm atau role kustom dengan base_type 'member'
     */
    public function isStandalone(): bool
    {
        return $this->base_type === 'member';
    }

    public function isMember(): bool
    {
        return $this->base_type === 'member';
    }
}