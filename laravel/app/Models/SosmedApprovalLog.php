<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosmedApprovalLog extends Model
{
    protected $table = 'sosmed_approval_logs';

    protected $fillable = [
        'sosmed_task_id',
        'user_id',
        'user_name',
        'role_name',
        'action',
        'notes',
    ];

    public function task()
    {
        return $this->belongsTo(SosmedTask::class, 'sosmed_task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'submitted'   => 'Selesai Dikerjakan (Submitted)',
            'approved_pm' => 'Diverifikasi PM (Level 1)',
            'approved_hr' => 'Disetujui HR Staff (Final)',
            'rejected'    => 'Ditolak / Perlu Revisi',
            default       => ucfirst($this->action),
        };
    }

    public function getActionBadgeClassAttribute(): string
    {
        return match ($this->action) {
            'submitted'   => 'bg-blue-50 text-blue-700 border-blue-200',
            'approved_pm' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'approved_hr' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'rejected'    => 'bg-rose-50 text-rose-700 border-rose-200',
            default       => 'bg-gray-50 text-gray-700 border-gray-200',
        };
    }
}
