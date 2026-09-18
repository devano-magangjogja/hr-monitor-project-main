<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('sosmed_accounts', 'rejection_note')) {
                $table->text('rejection_note')->nullable()->after('verification_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('sosmed_accounts', 'rejection_note')) {
                $table->dropColumn('rejection_note');
            }
        });
    }
};
