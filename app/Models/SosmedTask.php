<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosmedTask extends Model
{
    protected $table = 'sosmed_tasks';

    protected $fillable = [
        'sosmed_account_id',
        'assigned_to',
        'assigned_by',
        'type',          // daily | custom
        'title',
        'description',
        'link_upload',   // JSON array of URLs
        'task_date',
        'deadline',
        'status',        // pending, done_by_staff, verified_by_pm, approved_hr, rejected
        'verified_by',
        'verified_at',
        'hr_verified_by',
        'hr_verified_at',
        'rejection_note',
    ];

    protected function casts(): array
    {
        return [
            'task_date'      => 'date',
            'deadline'       => 'datetime',
            'verified_at'    => 'datetime',
            'hr_verified_at' => 'datetime',
            'link_upload'    => 'array',   // JSON array of URL strings
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function account()
    {
        return $this->belongsTo(SosmedAccount::class, 'sosmed_account_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function hrVerifiedBy()
    {
        return $this->belongsTo(User::class, 'hr_verified_by');
    }

    public function logs()
    {
        return $this->hasMany(SosmedApprovalLog::class, 'sosmed_task_id')->latest();
    }

    // ── Helpers ─────────────────────────────────────────

    /**
     * Ambil link pertama (untuk backward-compat tampilan ringkas).
     */
    public function getFirstLinkAttribute(): ?string
    {
        $links = $this->link_upload;
        if (is_array($links) && count($links) > 0) {
            return $links[0];
        }
        return null;
    }

    /**
     * Jumlah link bukti yang dikirim.
     */
    public function getLinkCountAttribute(): int
    {
        $links = $this->link_upload;
        return is_array($links) ? count($links) : 0;
    }

    public function hasLinks(): bool
    {
        return $this->link_count > 0;
    }

    // ── Status Helpers ──────────────────────────────────

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isDoneByStaff(): bool { return $this->status === 'done_by_staff'; }
    public function isVerifiedByPm(): bool { return $this->status === 'verified_by_pm'; }
    public function isApprovedHr(): bool { return $this->status === 'approved_hr'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'        => 'Menunggu Pengerjaan',
            'done_by_staff'  => 'Menunggu Verif PM',
            'verified_by_pm' => 'Menunggu HR Staff',
            'approved_hr'    => 'Disetujui Final',
            'rejected'       => 'Ditolak / Revisi',
            default          => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending'        => 'bg-amber-50 text-amber-700 border border-amber-200',
            'done_by_staff'  => 'bg-blue-50 text-blue-700 border border-blue-200',
            'verified_by_pm' => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
            'approved_hr'    => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            'rejected'       => 'bg-rose-50 text-rose-700 border border-rose-200',
            default          => 'bg-gray-50 text-gray-700 border border-gray-200',
        };
    }
}
