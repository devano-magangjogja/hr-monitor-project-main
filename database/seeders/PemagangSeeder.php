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
              ['nama_lengkap' => 'Ahmad Fauzi', 'no_hp' => '081234567801', 'kampus' => 'Universitas Indonesia', 'divisi' => 'Programmer'],
              ['nama_lengkap' => 'Siti Nurhaliza', 'no_hp' => '081234567802', 'kampus' => 'Universitas Gadjah Mada', 'divisi' => 'UI/UX Designer'],
              ['nama_lengkap' => 'Budi Santoso', 'no_hp' => '081234567803', 'kampus' => 'Institut Teknologi Bandung', 'divisi' => 'Machine Learning'],
              ['nama_lengkap' => 'Dewi Lestari', 'no_hp' => '081234567804', 'kampus' => 'Universitas Diponegoro', 'divisi' => 'Human Resource'],
              ['nama_lengkap' => 'Rizky Pratama', 'no_hp' => '081234567805', 'kampus' => 'Universitas Brawijaya', 'divisi' => 'Social Media Specialist'],
              ['nama_lengkap' => 'Anisa Putri', 'no_hp' => '081234567806', 'kampus' => 'Universitas Airlangga', 'divisi' => 'Administrasi'],
              ['nama_lengkap' => 'Fajar Hidayat', 'no_hp' => '081234567807', 'kampus' => 'Universitas Sebelas Maret', 'divisi' => 'Content Writer'],
              ['nama_lengkap' => 'Maya Sari', 'no_hp' => '081234567808', 'kampus' => 'Universitas Padjadjaran', 'divisi' => 'Marketing & Sales'],
              ['nama_lengkap' => 'Raka Wijaya', 'no_hp' => '081234567809', 'kampus' => 'Institut Teknologi Sepuluh Nopember', 'divisi' => 'Digital Marketing'],
              ['nama_lengkap' => 'Nabila Zahra', 'no_hp' => '081234567810', 'kampus' => 'Politeknik Negeri Jakarta', 'divisi' => 'Project Manager'],
              ['nama_lengkap' => 'Yoga Saputra', 'no_hp' => '081234567811', 'kampus' => 'Politeknik Negeri Bandung', 'divisi' => 'Photographer / Videographer'],
              ['nama_lengkap' => 'Citra Maharani', 'no_hp' => '081234567812', 'kampus' => 'Universitas Telkom', 'divisi' => 'Content Creative (Desain Grafis)'],
              ['nama_lengkap' => 'Dimas Ramadhan', 'no_hp' => '081234567813', 'kampus' => 'Universitas Bina Nusantara', 'divisi' => 'SEO'],
              ['nama_lengkap' => 'Lina Oktaviani', 'no_hp' => '081234567814', 'kampus' => 'Universitas Indonesia', 'divisi' => 'Content Planner'],
              ['nama_lengkap' => 'Arif Kurniawan', 'no_hp' => '081234567815', 'kampus' => 'Universitas Gadjah Mada', 'divisi' => 'TikTok Creator'],
              ['nama_lengkap' => 'Intan Permata', 'no_hp' => '081234567816', 'kampus' => 'Institut Teknologi Bandung', 'divisi' => 'Marcom / Public Relations.'],
              ['nama_lengkap' => 'Bagas Maulana', 'no_hp' => '081234567817', 'kampus' => 'Universitas Diponegoro', 'divisi' => 'Las'],
              ['nama_lengkap' => 'Putri Amelia', 'no_hp' => '081234567818', 'kampus' => 'Universitas Brawijaya', 'divisi' => 'Animasi'],
              ['nama_lengkap' => 'Galih Prakoso', 'no_hp' => '081234567819', 'kampus' => 'Universitas Airlangga', 'divisi' => 'Human Resource'],
              ['nama_lengkap' => 'Wulan Anggraini', 'no_hp' => '081234567820', 'kampus' => 'Universitas Sebelas Maret', 'divisi' => 'Administrasi'],
        ];

        foreach ($samplePemagang as $data) {
            Pemagang::firstOrCreate(['no_hp' => $data['no_hp']], $data);
        }

    }
}
