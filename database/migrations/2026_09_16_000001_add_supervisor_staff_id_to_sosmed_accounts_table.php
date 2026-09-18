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
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('sosmed_accounts', 'supervisor_staff_id')) {
                $table->foreignId('supervisor_staff_id')
                    ->nullable()
                    ->after('assistant_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('sosmed_accounts', 'supervisor_staff_id')) {
                $table->dropForeign(['supervisor_staff_id']);
                $table->dropColumn('supervisor_staff_id');
            }
        });
    }
};
