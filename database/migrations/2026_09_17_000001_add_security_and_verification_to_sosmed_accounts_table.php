<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('sosmed_accounts', 'email_recovery')) {
                $table->string('email_recovery')->nullable()->after('email');
            }
            if (!Schema::hasColumn('sosmed_accounts', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('email_recovery');
            }
            if (!Schema::hasColumn('sosmed_accounts', 'phone')) {
                $table->string('phone', 30)->nullable()->after('two_factor_enabled');
            }
            if (!Schema::hasColumn('sosmed_accounts', 'verification_status')) {
                $table->string('verification_status')->default('approved')->after('is_in_sosmed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            foreach (['email_recovery', 'two_factor_enabled', 'phone', 'verification_status'] as $column) {
                if (Schema::hasColumn('sosmed_accounts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
