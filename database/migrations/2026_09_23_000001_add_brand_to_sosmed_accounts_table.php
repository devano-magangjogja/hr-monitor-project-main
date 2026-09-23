<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('sosmed_accounts', 'brand')) {
                $table->string('brand')->nullable()->after('platform');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('sosmed_accounts', 'brand')) {
                $table->dropColumn('brand');
            }
        });
    }
};
