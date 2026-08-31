<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosmedAccount extends Model
{
    protected $table = 'sosmed_accounts';

    protected $fillable = [
        'name',
        'platform',
        'link',
        'pm_id',
        'staff_id',
        'assigned_to', // fallback compatibility
        'created_by',
        'notes',
    ];

    // ── Relasi ──────────────────────────────────────────

    public function pmUser()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    public function staffUser()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function assignedUser()
    {
        return $this->staffUser();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sosmedTasks()
    {
        return $this->hasMany(SosmedTask::class, 'sosmed_account_id');
    }

    // ── Helper ──────────────────────────────────────────

    public function isUnassigned(): bool
    {
        return is_null($this->pm_id) && is_null($this->staff_id);
    }

    public function isStaffUnassigned(): bool
    {
        return is_null($this->staff_id);
    }

    public function isPmUnassigned(): bool
    {
        return is_null($this->pm_id);
    }
}
