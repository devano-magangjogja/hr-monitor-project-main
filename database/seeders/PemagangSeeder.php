<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pemagang;

class PemagangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $samplePemagang = [
            [
                'nama_lengkap' => 'Ahmad Fauzi',
                'nim' => '230101001',
                'no_hp' => '081234567801',
                'kampus' => 'Universitas Indonesia',
                'divisi' => 'Programmer',
            ],
            [
                'nama_lengkap' => 'Siti Nurhaliza',
                'nim' => '230101002',
                'no_hp' => '081234567802',
                'kampus' => 'Universitas Gadjah Mada',
                'divisi' => 'UI/UX Designer',
            ],
            [
                'nama_lengkap' => 'Budi Santoso',
                'nim' => '230101003',
                'no_hp' => '081234567803',
                'kampus' => 'Institut Teknologi Bandung',
                'divisi' => 'Machine Learning',
            ],
            [
                'nama_lengkap' => 'Dewi Lestari',
                'nim' => '230101004',
                'no_hp' => '081234567804',
                'kampus' => 'Universitas Diponegoro',
                'divisi' => 'Human Resource',
            ],
            [
                'nama_lengkap' => 'Rizky Pratama',
                'nim' => '230101005',
                'no_hp' => '081234567805',
                'kampus' => 'Universitas Brawijaya',
                'divisi' => 'Social Media Specialist',
            ],
        ];

        foreach ($samplePemagang as $data) {
            Pemagang::firstOrCreate(['nim' => $data['nim']], $data);
        }

        // Tambah pemagang acak via factory jika belum mencapai minimal 15
        $currentCount = Pemagang::count();
        if ($currentCount < 15) {
            Pemagang::factory(15 - $currentCount)->create();
        }
    }
}
