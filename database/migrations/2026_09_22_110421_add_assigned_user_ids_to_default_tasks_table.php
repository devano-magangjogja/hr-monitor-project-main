<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('default_tasks', function (Blueprint $table) {
            $table->json('assigned_user_ids')->nullable()->after('target_role');
        });
    }

    public function down(): void
    {
        Schema::table('default_tasks', function (Blueprint $table) {
            $table->dropColumn('assigned_user_ids');
        });
    }
};