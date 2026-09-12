<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'label',
        'base_type',
        'badge_class',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    public static function getBaseTypeOptions(): array
    {
        return [
            'member' => [
                'label' => 'Anggota / Mandiri (seperti CS, VG, Programmer, OB)',
                'description' => 'Dashboard tugas pribadi, tugas harian default, tugas dari admin, dan riwayat.',
                'icon' => 'user',
            ],
            'assistant' => [
                'label' => 'Asisten (seperti HR Assistant)',
                'description' => 'Tugas rutin harian, tugas dari staff/admin, serta kelola presensi pemagang.',
                'icon' => 'clipboard',
            ],
            'staff' => [
                'label' => 'Staff / Koordinator (seperti HR Staff)',
                'description' => 'Bagi tugas ke bawahan (assign tasks), pantau progres tim, kelola tim, dan presensi.',
                'icon' => 'users',
            ],
            'admin' => [
                'label' => 'Administrator (seperti Admin)',
                'description' => 'Akses penuh ke seluruh modul sistem, laporan, ranking, pengaturan, dan manajemen role.',
                'icon' => 'shield',
            ],
        ];
    }

    public function getBaseTypeLabelAttribute(): string
    {
        return match ($this->base_type) {
            'admin'     => 'Administrator',
            'staff'     => 'Staff / Koordinator',
            'assistant' => 'Asisten',
            'member'    => 'Anggota / Mandiri',
            default     => ucfirst($this->base_type),
        };
    }
}
