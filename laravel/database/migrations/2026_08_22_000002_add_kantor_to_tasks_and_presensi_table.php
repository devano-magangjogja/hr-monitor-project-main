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
        if (Schema::hasTable('tasks') && !Schema::hasColumn('tasks', 'kantor')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('kantor')->nullable()->after('type');
            });
        }

        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) {
                if (!Schema::hasColumn('presensi', 'kantor')) {
                    $table->string('kantor')->nullable()->after('shift');
                }
                if (!Schema::hasColumn('presensi', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'kantor')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('kantor');
            });
        }

        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) {
                if (Schema::hasColumn('presensi', 'created_by')) {
                    $table->dropConstrainedForeignId('created_by');
                }
                if (Schema::hasColumn('presensi', 'kantor')) {
                    $table->dropColumn('kantor');
                }
            });
        }
    }
};
