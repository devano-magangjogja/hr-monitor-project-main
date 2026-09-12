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
            if (!Schema::hasColumn('sosmed_accounts', 'email')) {
                $table->string('email')->nullable()->after('platform');
            }
            if (!Schema::hasColumn('sosmed_accounts', 'password')) {
                $table->text('password')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('sosmed_accounts', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('sosmed_accounts', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
