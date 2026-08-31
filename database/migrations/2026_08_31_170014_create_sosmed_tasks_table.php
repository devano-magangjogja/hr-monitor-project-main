<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sosmed_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sosmed_account_id')          // Akun sosmed yang ditugaskan
                  ->constrained('sosmed_accounts')->cascadeOnDelete();
            $table->foreignId('assigned_to')                // Staff sosmed pelaksana
                  ->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')                // PM yang memberi tugas
                  ->constrained('users')->cascadeOnDelete();
            $table->string('title');                        // Judul tugas
            $table->text('description')->nullable();
            $table->string('link_upload')->nullable();      // Link hasil upload konten
            $table->date('task_date');
            // Status: pending → done_by_staff → verified_by_pm / rejected
            $table->enum('status', ['pending', 'done_by_staff', 'verified_by_pm', 'rejected'])
                  ->default('pending');
            $table->foreignId('verified_by')->nullable()    // PM/Asisten yang memverifikasi
                  ->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sosmed_tasks');
    }
};
