<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_sosmed_oversight', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pm_id')
                  ->constrained('users')->cascadeOnDelete();
            $table->foreignId('sosmed_id')          // the Sosmed user being overseen
                  ->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')
                  ->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // One Sosmed user → at most one PM
            $table->unique('sosmed_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_sosmed_oversight');
    }
};
