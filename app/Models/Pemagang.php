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
        'nim',
        'no_hp',
        'kampus',
        'divisi',
    ];

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class, 'pemagang_id');
    }
}
