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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemagang_id')->constrained('pemagang');
            $table->enum('shift', [
                'Pagi',
                'Middle',
                'Siang'
            ]);
            $table->time('waktu_masuk');
            $table->enum('keterangan', [
                'Datang Lebih Awal',
                'Tepat Waktu',
                'Terlambat',
                'Tidak Hadir',
            ]);
            $table->text('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
