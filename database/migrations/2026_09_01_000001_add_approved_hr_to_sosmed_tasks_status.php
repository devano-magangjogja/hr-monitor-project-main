<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand the status enum to include 'approved_hr'
        DB::statement("ALTER TABLE `sosmed_tasks` MODIFY `status` ENUM('pending','done_by_staff','verified_by_pm','approved_hr','rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert — note: any rows with 'approved_hr' must be updated first in production
        DB::statement("ALTER TABLE `sosmed_tasks` MODIFY `status` ENUM('pending','done_by_staff','verified_by_pm','rejected') NOT NULL DEFAULT 'pending'");
    }
};
