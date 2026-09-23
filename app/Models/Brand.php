<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';

    protected $fillable = ['name', 'logo_path'];

    public function accounts()
    {
        return $this->hasMany(SosmedAccount::class, 'brand', 'name');
    }
}
