<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sosmed_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Nama akun / username
            $table->string('platform');                     // Instagram, TikTok, YouTube, dll
            $table->string('link')->nullable();             // Link profil
            $table->foreignId('assigned_to')->nullable()   // Staff sosmed penanggung jawab
                  ->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')                // PM/HR yang memasukkan akun ini
                  ->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sosmed_accounts');
    }
};
