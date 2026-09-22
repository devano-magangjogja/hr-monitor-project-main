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
        if (Schema::hasTable('default_tasks') && !Schema::hasColumn('default_tasks', 'proof_requirement')) {
            Schema::table('default_tasks', function (Blueprint $table) {
                $table->string('proof_requirement')->default('none')->after('is_active');
            });
        }

        if (Schema::hasTable('tasks') && !Schema::hasColumn('tasks', 'proof_requirement')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->string('proof_requirement')->default('none')->after('type');
            });
        }

        if (Schema::hasTable('task_assignments') && !Schema::hasColumn('task_assignments', 'attachment')) {
            Schema::table('task_assignments', function (Blueprint $table) {
                $table->string('attachment')->nullable()->after('note');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('default_tasks') && Schema::hasColumn('default_tasks', 'proof_requirement')) {
            Schema::table('default_tasks', function (Blueprint $table) {
                $table->dropColumn('proof_requirement');
            });
        }

        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'proof_requirement')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('proof_requirement');
            });
        }

        if (Schema::hasTable('task_assignments') && Schema::hasColumn('task_assignments', 'attachment')) {
            Schema::table('task_assignments', function (Blueprint $table) {
                $table->dropColumn('attachment');
            });
        }
    }
};
