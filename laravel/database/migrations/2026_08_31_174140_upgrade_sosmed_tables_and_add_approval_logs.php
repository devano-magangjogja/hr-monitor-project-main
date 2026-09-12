<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Upgrade sosmed_accounts
        Schema::table('sosmed_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('sosmed_accounts', 'pm_id')) {
                $table->foreignId('pm_id')->nullable()->after('link')
                      ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('sosmed_accounts', 'staff_id')) {
                $table->foreignId('staff_id')->nullable()->after('pm_id')
                      ->constrained('users')->nullOnDelete();
            }
        });

        // 2. Upgrade sosmed_tasks
        Schema::table('sosmed_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('sosmed_tasks', 'type')) {
                $table->enum('type', ['daily', 'custom'])->default('daily')->after('assigned_by');
            }
            if (!Schema::hasColumn('sosmed_tasks', 'deadline')) {
                $table->datetime('deadline')->nullable()->after('task_date');
            }
            if (!Schema::hasColumn('sosmed_tasks', 'hr_verified_by')) {
                $table->foreignId('hr_verified_by')->nullable()->after('verified_at')
                      ->constrained('users')->nullOnDelete();
                $table->timestamp('hr_verified_at')->nullable()->after('hr_verified_by');
            }
        });

        // 3. Create sosmed_approval_logs
        if (!Schema::hasTable('sosmed_approval_logs')) {
            Schema::create('sosmed_approval_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sosmed_task_id')->constrained('sosmed_tasks')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('user_name')->nullable();
                $table->string('role_name')->nullable();
                $table->string('action'); // submitted, approved_pm, approved_hr, rejected
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sosmed_approval_logs');

        Schema::table('sosmed_tasks', function (Blueprint $table) {
            $table->dropForeign(['hr_verified_by']);
            $table->dropColumn(['type', 'deadline', 'hr_verified_by', 'hr_verified_at']);
        });

        Schema::table('sosmed_accounts', function (Blueprint $table) {
            $table->dropForeign(['pm_id']);
            $table->dropForeign(['staff_id']);
            $table->dropColumn(['pm_id', 'staff_id']);
        });
    }
};
