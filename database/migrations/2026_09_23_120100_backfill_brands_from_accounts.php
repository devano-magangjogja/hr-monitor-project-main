<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $existing = DB::table('brands')->pluck('name')->all();

        $brands = DB::table('sosmed_accounts')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->pluck('brand')
            ->all();

        $now = now();

        foreach ($brands as $name) {
            if (!in_array($name, $existing, true)) {
                DB::table('brands')->insert([
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $existing[] = $name;
            }
        }
    }

    public function down(): void
    {
        // Data backfill tidak dibatalkan.
    }
};
