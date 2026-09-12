<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmSosmedOversight extends Model
{
    protected $table = 'pm_sosmed_oversight';

    protected $fillable = ['pm_id', 'sosmed_id', 'created_by'];

    // The PM who oversees
    public function pm()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    // The Sosmed user being overseen
    public function sosmedUser()
    {
        return $this->belongsTo(User::class, 'sosmed_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
