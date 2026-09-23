<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        Schema::table('sosmed_accounts', function (Blueprint $table) {
            $indexes = collect(DB::select("SHOW INDEX FROM sosmed_accounts"))
                ->pluck('Key_name')
                ->unique()
                ->all();

            if (!in_array('sosmed_accounts_brand_index', $indexes, true)) {
                $table->index('brand');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            $indexes = collect(DB::select("SHOW INDEX FROM sosmed_accounts"))
                ->pluck('Key_name')
                ->unique()
                ->all();

            if (in_array('sosmed_accounts_brand_index', $indexes, true)) {
                $table->dropIndex('sosmed_accounts_brand_index');
            }
        });

        Schema::dropIfExists('brands');
    }
};
