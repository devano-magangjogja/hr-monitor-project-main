<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name');           // snapshot nama saat aksi
            $table->string('user_role');           // snapshot role saat aksi
            $table->string('action');              // kode aksi: task.created, presensi.deleted, dll
            $table->string('module');              // modul: Tugas, Presensi, Pemagang, Sosmed, Pengguna, dll
            $table->text('description');           // kalimat deskriptif aksi
            $table->string('subject_type')->nullable(); // class model terkait
            $table->unsignedBigInteger('subject_id')->nullable(); // id model terkait
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('properties')->nullable(); // data tambahan opsional
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
