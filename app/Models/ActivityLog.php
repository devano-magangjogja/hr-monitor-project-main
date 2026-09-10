<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    // ── Relasi ───────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Static Helper ────────────────────────────────────

    /**
     * Rekam satu aksi ke log.
     *
     * @param string      $action      Kode aksi, e.g. 'task.created'
     * @param string      $module      Modul, e.g. 'Tugas'
     * @param string      $description Kalimat deskriptif
     * @param mixed|null  $subject     Model terkait (opsional)
     * @param array       $properties  Data tambahan (opsional)
     */
    public static function record(
        string $action,
        string $module,
        string $description,
        mixed $subject = null,
        array $properties = []
    ): self {
        $user = Auth::user();

        return static::create([
            'user_id'      => $user?->id,
            'user_name'    => $user?->name ?? 'System',
            'user_role'    => $user?->role ?? '-',
            'action'       => $action,
            'module'       => $module,
            'description'  => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'properties'   => empty($properties) ? null : $properties,
        ]);
    }

    // ── Scopes ───────────────────────────────────────────

    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where('user_role', $role);
    }

    // ── Helpers ──────────────────────────────────────────

    /**
     * Label aksi yang lebih manusiawi
     */
    public function getActionLabelAttribute(): string
    {
        return match(true) {
            str_ends_with($this->action, '.created')  => 'Dibuat',
            str_ends_with($this->action, '.updated')  => 'Diperbarui',
            str_ends_with($this->action, '.deleted')  => 'Dihapus',
            str_ends_with($this->action, '.completed') => 'Diselesaikan',
            str_ends_with($this->action, '.verified')  => 'Diverifikasi',
            str_ends_with($this->action, '.submitted')  => 'Dikirim',
            str_ends_with($this->action, '.assigned')  => 'Ditugaskan',
            default => $this->action,
        };
    }

    /**
     * Warna badge per modul
     */
    public function getModuleColorAttribute(): string
    {
        return match($this->module) {
            'Tugas'     => 'blue',
            'Presensi'  => 'green',
            'Pemagang'  => 'purple',
            'Sosmed'    => 'pink',
            'Pengguna'  => 'orange',
            'Role'      => 'red',
            'Notifikasi' => 'yellow',
            'Pengaturan' => 'gray',
            default     => 'gray',
        };
    }

    /**
     * Warna badge per action suffix
     */
    public function getActionColorAttribute(): string
    {
        return match(true) {
            str_ends_with($this->action, '.created')   => 'green',
            str_ends_with($this->action, '.updated')   => 'blue',
            str_ends_with($this->action, '.deleted')   => 'red',
            str_ends_with($this->action, '.completed') => 'indigo',
            str_ends_with($this->action, '.verified')  => 'teal',
            str_ends_with($this->action, '.submitted') => 'purple',
            str_ends_with($this->action, '.assigned')  => 'amber',
            default => 'gray',
        };
    }
}
