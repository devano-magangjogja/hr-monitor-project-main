<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemagang extends Model
{
    use HasFactory;

    protected $table = 'pemagang';

    public $timestamps = false;

    protected $fillable = [
        'nama_lengkap',
        'no_hp',
        'kampus',
        'divisi',
    ];

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class, 'pemagang_id');
    }

    /**
     * Mendapatkan nomor WhatsApp berformat 628...
     */
    public function getWaNumberAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', (string)$this->no_hp);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }

    /**
     * URL direct chat WhatsApp konfirmasi ketidakhadiran
     */
    public function getWaUrlAttribute(): string
    {
        $defaultTemplate = "Halo {nama}, kami dari tim HR ingin menanyakan konfirmasi kehadiran Anda untuk kegiatan magang ({divisi} - {kampus}) hari ini. Mohon informasikan keterangan atau kendala Anda. Terima kasih.";
        $template = AppSetting::get('wa_template_tidak_hadir', $defaultTemplate);

        $message = str_replace(
            ['{nama}', '{divisi}', '{kampus}'],
            [$this->nama_lengkap ?? '', $this->divisi ?? '', $this->kampus ?? ''],
            $template
        );

        return "https://wa.me/{$this->wa_number}?text=" . urlencode($message);
    }
}
