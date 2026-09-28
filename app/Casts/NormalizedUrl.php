<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class NormalizedUrl implements CastsAttributes
{
    /**
     * Simpan & kembalikan URL dalam bentuk absolut (skema https:// bila user menulis host saja).
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return is_string($value) ? normalize_url($value) : $value;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return is_string($value) ? normalize_url($value) : $value;
    }
}
