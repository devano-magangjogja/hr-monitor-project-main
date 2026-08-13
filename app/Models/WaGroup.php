<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaGroup extends Model
{
    protected $table    = 'wa_groups';
    protected $fillable = ['label', 'url'];

    public function scopeActive($query)
    {
        return $query->orderBy('id');
    }
}
