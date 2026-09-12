<?php

namespace Database\Seeders;

use App\Models\Pemagang;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PresensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pemagangs = Pemagang::all();
        if ($pemagangs->isEmpty()) {
            return;
        }

        $today = Carbon::today()->format('Y-m-d');
        $shifts = ['Pagi', 'Middle', 'Siang'];

        $statuses = [
            'Lebih Awal',
            'Tepat Waktu',
            'Tepat Waktu',
            'Lebih Awal',
            'Terlambat',
            'Tepat Waktu',
            'Terlambat',
            'Tidak Hadir',
            'Tidak Hadir',
            'Tepat Waktu',
        ];

        foreach ($pemagangs as $index => $pemagang) {
            if (! Presensi::where('pemagang_id', $pemagang->id)->where('tanggal', $today)->exists()) {
                $keterangan = $statuses[$index % count($statuses)];
                $shift = $shifts[$index % count($shifts)];

                $waktuMasuk = match($keterangan) {
                    'Lebih Awal'  => '07:35:00',
                    'Tepat Waktu' => '07:55:00',
                    'Terlambat'   => '08:25:00',
                    'Tidak Hadir' => '00:00:00',
                };

                $notes = match($keterangan) {
                    'Lebih Awal'  => 'Datang lebih awal untuk persiapan tugas',
                    'Tepat Waktu' => 'Hadir tepat waktu dan langsung standby',
                    'Terlambat'   => 'Terlambat 25 menit karena kendala transportasi',
                    'Tidak Hadir' => 'Belum ada kabar / belum konfirmasi kehadiran',
                };

                Presensi::create([
                    'pemagang_id' => $pemagang->id,
                    'tanggal'     => $today,
                    'shift'       => $shift,
                    'waktu_masuk' => $waktuMasuk,
                    'keterangan'  => $keterangan,
                    'notes'       => $notes,
                ]);
            }
        }
    }
}
