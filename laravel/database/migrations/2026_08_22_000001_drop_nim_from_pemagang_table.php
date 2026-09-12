<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('pemagang', 'nim')) {
            Schema::table('pemagang', function (Blueprint $table) {
                $table->dropColumn('nim');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('pemagang', 'nim')) {
            Schema::table('pemagang', function (Blueprint $table) {
                $table->string('nim')->nullable()->after('nama_lengkap');
            });
        }
    }
};
