<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class NormalizedUrlList implements CastsAttributes
{
    /**
     * Kolom JSON berisi daftar link bukti. Setiap unsur dilengkapi skema https://
     * bila ditulis tanpa skema, supaya bisa diklik di tab baru.
     *
     * @return array<int, string>|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return $this->normalize(is_array($decoded) ? $decoded : [$decoded]);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $items = is_array($decoded) ? $decoded : [$value];
        } else {
            $items = is_array($value) ? $value : [$value];
        }

        return json_encode($this->normalize($items), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param  array<mixed>  $items
     * @return array<int, string>
     */
    private function normalize(array $items): array
    {
        $clean = [];
        foreach ($items as $item) {
            if (! is_string($item)) {
                continue;
            }
            $url = normalize_url($item);
            if ($url !== null && $url !== '') {
                $clean[] = $url;
            }
        }

        return $clean;
    }
}
