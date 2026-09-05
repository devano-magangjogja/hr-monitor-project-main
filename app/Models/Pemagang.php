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

    public const DIVISI_LIST = [
        'Administrasi',
        'UI/UX Designer',
        'Programmer (Front end / Back end)',
        'Human Resource',
        'Social Media Specialist',
        'Photographer / Videographer',
        'Content Writer',
        'Marketing & Sales',
        'Content Creative (Desain Grafis)',
        'Digital Marketing',
        'Marcom / Public Relations.',
        'TikTok Creator',
        'Content Planner',
        'Project Manager',
        'Las',
        'Animasi',
        'SEO',
        'Machine Learning',
    ];

    public static function getAllDivisi(): array
    {
        $fromDb = static::select('divisi')->distinct()->whereNotNull('divisi')->pluck('divisi')->toArray();
        return array_values(array_unique(array_merge(self::DIVISI_LIST, $fromDb)));
    }

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
        $phone = preg_replace('/[^0-9]/', '', (string) $this->no_hp);
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
