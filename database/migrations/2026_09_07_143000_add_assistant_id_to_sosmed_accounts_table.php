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
            if (!Schema::hasColumn('sosmed_accounts', 'assistant_id')) {
                $table->foreignId('assistant_id')
                    ->nullable()
                    ->after('pm_id')
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
            if (Schema::hasColumn('sosmed_accounts', 'assistant_id')) {
                $table->dropForeign(['assistant_id']);
                $table->dropColumn('assistant_id');
            }
        });
    }
};
