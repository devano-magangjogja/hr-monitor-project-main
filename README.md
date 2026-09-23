<div align="center">

<img src="resources/images/logo_banner.jpg" alt="Task — HR Monitor" width="480"/>

# Task — Sistem Monitoring HR & Tugas

**Website internal untuk memantau tugas harian, presensi, dan konten sosial media antar divisi.**

</div>

---

## 📌 Tentang Proyek

**Task** adalah aplikasi web sistem monitoring SDM (HR) yang dibangun untuk kebutuhan operasional kantor. Aplikasi ini menjadi satu tempat bagi setiap karyawan — dari staff pelaksana hingga HR dan Admin — untuk mencatat, mengerjakan, memverifikasi, dan menyetujui pekerjaan harian secara terstruktur dan transparan.

Alur kerja utamanya:

1. **Tugas dibuat** — oleh Admin/HR Staff, atau otomatis (tugas rutin/harian), atau dari pengajuan mandiri.
2. **Kerja & bukti** — pelaksana mengerjakan tugas, menandai selesai, dan mengunggah bukti (foto lampiran atau link konten).
3. **Verifikasi berjenjang** — konten sosmed diverifikasi dua level: **PM/Asisten (Level 1)** lalu **HR Staff/Admin (Level 2/final)**.
4. **Pantau & audit** — dashboard per role, statistik, riwayat, log aktivitas, dan laporan siap cetak PDF.

## ✨ Fitur Utama

### 🗂️ Manajemen Tugas Multi-Role
- Dashboard khusus untuk setiap role: **Admin, HR Staff, Asisten HR, PM, Sosmed, Programmer, CS, OB, DG, dan Member**.
- Kategori tugas: **Harian/Rutin** (default), **Ditugaskan** (assigned), **Mandiri** (self), dan **Semua Tugas** dalam satu tabel gabungan.
- Bukti penyelesaian fleksibel: foto lampiran (wajib/opsional per tugas) atau catatan.
- Filter tanggal, pencarian, dan status per halaman tugas.

### 📱 Manajemen Sosial Media
- Kelola akun sosmed (nama, platform, brand, link profil, kredensial, 2FA) lengkap dengan pengajuan akun baru yang harus di-ACC Admin/HR.
- Master data **Brand** dengan upload logo (otomatis dikonversi ke WebP).
- Alur tugas sosmed: `Pending → Submit Bukti (link konten) → Verifikasi PM/Asisten → Approval Final HR/Staff → Disetujui / Ditolak (revisi)`.
- Pantau progress tim sosmed per pelaksana (oversight PM), statistik harian, dan kartu filter interaktif.

### ⏰ Presensi & Aktivitas
- Pencatatan presensi harian termasuk sesi istirahat.
- **Activity Log** (audit trail) untuk aksi penting dan **Approval Log** khusus alur verifikasi sosmed.
- Notifikasi in-app untuk tugas baru, verifikasi, persetujuan, dan penolakan.

### 🛠️ Administratif
- Pengaturan aplikasi oleh Admin: nama aplikasi, logo sidebar, dan logo banner login (custom upload).
- Cetak laporan ke **PDF** langsung dari halaman tugas.
- Hapus data tugas lama (per minggu/bulan/tahun) untuk merapikan arsip.
- Tampilan **responsif** — tabel di desktop, kartu di mobile.

## 🧰 Teknologi

| Kategori | Teknologi |
|---|---|
| Backend | [PHP 8.2+](https://www.php.net/) · [Laravel 12](https://laravel.com/) |
| Frontend | [Blade](https://laravel.com/docs/blade) · [Tailwind CSS](https://tailwindcss.com/) · [Alpine.js](https://alpinejs.dev/) · [Flowbite](https://flowbite.com/) |
| Build Tool | [Vite](https://vitejs.dev/) (laravel-vite-plugin) |
| Database | MySQL / SQLite (Eloquent ORM) |
| Font | [Inter (Fontsource)](https://fontsource.org/fonts/inter) |
| Testing & QA | [PHPUnit](https://phpunit.de/) · [Laravel Pint](https://laravel.com/docs/pint) · [Laravel Sail](https://laravel.com/docs/sail) |

## 🚀 Cara Menjalankan

### Prasyarat
- PHP >= 8.2 (dengan ekstensi `pdo_mysql`/`pdo_sqlite`)
- Composer & Node.js >= 20

### Instalasi

```bash
# 1. Clone dan install dependencies
git clone <repo-url> hr-monitor-project
cd hr-monitor-project
composer install
npm install

# 2. Konfigurasi environment
cp .env.example .env
php artisan key:generate
# sesuaikan DB_CONNECTION dan kredensial database di .env

# 3. Migrasi & seed database
php artisan migrate --seed

# 4. Link storage (untuk upload logo/bukti)
php artisan storage:link

# 5. Jalankan
php artisan serve
npm run dev
```

Aplikasi tersedia di `http://127.0.0.1:8000`.

> Scheduler digunakan untuk pembuatan tugas harian otomatis — jalankan `php artisan schedule:work` saat development, atau daftarkan cron saat production.

## 📁 Struktur Singkat

```
app/
├── Http/Controllers/
│   ├── Admin/        # Panel admin (akun, brand, sosmed, settings, logs)
│   ├── Assistant/    # Asisten HR (verifikasi Level 1)
│   ├── Staff/        # HR Staff (approval Level 2, manajemen)
│   ├── PM/           # Project Manager (verifikasi & oversight)
│   ├── Sosmed/       # Tim sosmed (submit bukti konten)
│   └── Cs|Ob|DG|VG|Programmer|Member/  # Role divisi lain
├── Models/           # Task, TaskAssignment, SosmedAccount, SosmedTask, Brand, Presensi, ActivityLog, ...
└── Services/         # TaskService, SettingService, logika verifikasi & notifikasi
resources/views/      # Blade per role + komponen bersama (tabel tugas, modal, badge)
routes/web.php        # Seluruh route per role
```

## 👥 Role & Hak Akses

| Role | Tanggung Jawab |
|---|---|
| **Admin** | Penuh: kelola akun & brand sosmed, ACC pengajuan akun, approval final semua tugas, pengaturan aplikasi |
| **HR Staff** | Approval final (Level 2), verifikasi langsung, manajemen tugas & akun sosmed |
| **Asisten HR** | Verifikasi Level 1 untuk akun yang didelegasikan Staff/Admin |
| **PM** | Verifikasi Level 1, pantau progress tim sosmed di bawah pengawasannya |
| **Sosmed** | Submit bukti konten harian per akun yang di-assign |
| **Programmer / CS / OB / DG / VG / Member** | Mengerjakan & melapor tugas divisi masing-masing |

## 📄 Lisensi

Proyek internal — semua hak cipta milik perusahaan.
