<?php

namespace App\Repositories;

use App\Models\AppSetting;

class AppSettingRepository
{
    public function get(string $key, mixed $default = null): mixed
    {
        return AppSetting::get($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        AppSetting::set($key, $value);
    }

    public function all(): array
    {
        return AppSetting::all()->pluck('value', 'key')->toArray();
    }
}
