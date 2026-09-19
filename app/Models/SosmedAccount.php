<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosmedAccount extends Model
{
    protected $table = 'sosmed_accounts';

    protected $fillable = [
        'name',
        'username',
        'platform',
        'link',
        'email',
        'email_recovery',
        'password',
        'two_factor_enabled',
        'phone',
        'pm_id',
        'assistant_id',
        'supervisor_staff_id',
        'created_by',
        'notes',
        'is_in_sosmed',
        'verification_status',
        'rejection_note',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
            'is_in_sosmed' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function pmUser()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    public function assistantUser()
    {
        return $this->belongsTo(User::class, 'assistant_id');
    }

    public function supervisorStaff()
    {
        return $this->belongsTo(User::class, 'supervisor_staff_id');
    }

    public function supervisorUser()
    {
        return $this->supervisorStaff();
    }

    public function staffUsers()
    {
        return $this->belongsToMany(User::class, 'sosmed_account_users', 'sosmed_account_id', 'user_id')
            ->withPivot('assigned_by', 'assigned_at');
    }

    /**
     * Backward-compatibility accessor for singular staffUser
     */
    public function getStaffUserAttribute(): ?User
    {
        return $this->staffUsers->first();
    }

    public function getAssignedUserAttribute(): ?User
    {
        return $this->staffUser;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function finalVerifier(): ?User
    {
        if ($this->supervisorStaff) {
            return $this->supervisorStaff;
        }

        $creator = $this->creator;
        if ($creator && $creator->role === 'hr_staff') {
            return $creator;
        }

        return null;
    }

    public function levelOneVerifier(): ?User
    {
        return $this->pmUser ?: $this->assistantUser;
    }

    public function finalVerifierLabel(): string
    {
        $verifier = $this->finalVerifier();

        return $verifier ? 'HR Staff (' . $verifier->name . ')' : 'Admin';
    }

    public function sosmedTasks()
    {
        return $this->hasMany(SosmedTask::class, 'sosmed_account_id');
    }

    // ── Helper ──────────────────────────────────────────

    public function isUnassigned(): bool
    {
        return is_null($this->pm_id) && $this->isStaffUnassigned();
    }

    public function isStaffUnassigned(): bool
    {
        return $this->relationLoaded('staffUsers')
            ? $this->staffUsers->isEmpty()
            : !$this->staffUsers()->exists();
    }

    public function isPmUnassigned(): bool
    {
        return is_null($this->pm_id);
    }

    public function getManagersCountAttribute(): int
    {
        return $this->relationLoaded('staffUsers')
            ? $this->staffUsers->count()
            : $this->staffUsers()->count();
    }

    public function scopeUnassigned($query)
    {
        return $query->whereDoesntHave('staffUsers');
    }

    public function scopeInSosmed($query)
    {
        return $query->where('is_in_sosmed', true);
    }

    public function scopeNotInSosmed($query)
    {
        return $query->where('is_in_sosmed', false);
    }

    public function getPlatformColorAttribute(): string
    {
        return match (strtolower($this->platform ?? '')) {
            'instagram' => 'bg-pink-50 text-pink-700 border-pink-200',
            'tiktok' => 'bg-neutral-900 border-neutral-800',
            'youtube' => 'bg-red-50 text-red-700 border-red-200',
            'facebook' => 'bg-blue-50 text-blue-700 border-blue-200',
            'twitter', 'x', 'twitter/x' => 'bg-slate-50 text-slate-800 border-slate-200',
            'linkedin' => 'bg-sky-50 text-sky-700 border-sky-200',
            'threads' => 'bg-zinc-100 text-zinc-900 border-zinc-300',
            'website' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            default => 'bg-purple-50 text-purple-700 border-purple-200',
        };
    }
}
