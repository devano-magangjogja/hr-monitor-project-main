<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    public $timestamps = false;

    protected $fillable = [
        'pemagang_id',
        'tanggal',
        'session',
        'shift',
        'kantor',
        'waktu_masuk',
        'keterangan',
        'notes',
        'created_by',
    ];

    public function isBreakReturn(): bool
    {
        return $this->session === 'break_return';
    }

    public function pemagang(): BelongsTo
    {
        return $this->belongsTo(Pemagang::class, 'pemagang_id');
    }

    public function pemagangs(): BelongsTo
    {
        return $this->pemagang();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
