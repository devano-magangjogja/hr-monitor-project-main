<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('presensi', 'session')) {
            Schema::table('presensi', function (Blueprint $table) {
                $table->string('session', 30)->default('entry')->after('tanggal');
                $table->index(['tanggal', 'session', 'pemagang_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('presensi', 'session')) {
            Schema::table('presensi', function (Blueprint $table) {
                $table->dropIndex(['tanggal', 'session', 'pemagang_id']);
                $table->dropColumn('session');
            });
        }
    }
};
