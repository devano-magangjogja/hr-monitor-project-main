<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique(); // slug/key (e.g. 'admin', 'hr_staff', 'marketing')
                $table->string('label'); // Display name (e.g. 'Administrator', 'Digital Marketing')
                $table->string('base_type')->default('member'); // 'admin', 'staff', 'assistant', 'member'
                $table->string('badge_class')->default('bg-gray-100 text-gray-700');
                $table->boolean('is_system')->default(false); // protected system roles
                $table->timestamps();
            });
        }

        // Seed default roles if empty
        if (DB::table('roles')->count() === 0) {
            $now = now();
            $defaultRoles = [
                [
                    'name' => 'admin',
                    'label' => 'Administrator',
                    'base_type' => 'admin',
                    'badge_class' => 'bg-red-50 text-red-700',
                    'is_system' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'hr_staff',
                    'label' => 'HR Staff',
                    'base_type' => 'staff',
                    'badge_class' => 'bg-blue-50 text-blue-700',
                    'is_system' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'hr_assistant',
                    'label' => 'HR Assistant',
                    'base_type' => 'assistant',
                    'badge_class' => 'bg-purple-50 text-purple-700',
                    'is_system' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'cs',
                    'label' => 'CS (Customer Service)',
                    'base_type' => 'member',
                    'badge_class' => 'bg-teal-50 text-teal-700',
                    'is_system' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'ob',
                    'label' => 'OB (Office Boy)',
                    'base_type' => 'member',
                    'badge_class' => 'bg-orange-50 text-orange-700',
                    'is_system' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'programmer',
                    'label' => 'Programmer',
                    'base_type' => 'member',
                    'badge_class' => 'bg-cyan-50 text-cyan-700',
                    'is_system' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'dg',
                    'label' => 'DG (Design Graphics)',
                    'base_type' => 'member',
                    'badge_class' => 'bg-rose-50 text-rose-700',
                    'is_system' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'vg',
                    'label' => 'VG (Videografer)',
                    'base_type' => 'member',
                    'badge_class' => 'bg-amber-50 text-amber-700',
                    'is_system' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'name' => 'pm',
                    'label' => 'PM (Project Manager)',
                    'base_type' => 'member',
                    'badge_class' => 'bg-indigo-50 text-indigo-700',
                    'is_system' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ];

            DB::table('roles')->insert($defaultRoles);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
