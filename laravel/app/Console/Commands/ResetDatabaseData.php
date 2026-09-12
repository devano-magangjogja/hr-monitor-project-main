<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDatabaseData extends Command
{
    protected $signature = 'db:reset-data {--keep-email=dep@hrdwms.com : Email admin yang dipertahankan} {--force : Jalankan tanpa konfirmasi}';
    protected $description = 'Bersihkan semua data transaksi, tugas, dan akun di database kecuali akun admin tertentu';

    public function handle(): int
    {
        $keepEmail = $this->option('keep-email') ?: 'dep@hrdwms.com';

        if (!$this->option('force')) {
            if (!$this->confirm("Apakah Anda yakin ingin menghapus SEMUA data database kecuali akun [{$keepEmail}]?", true)) {
                $this->warn('Operasi dibatalkan.');
                return 0;
            }
        }

        $this->info("Memulai pembersihan database (menjaga akun: {$keepEmail})...");

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tablesToTruncate = [
            'pemagang',
            'presensi',
            'tasks',
            'task_assignments',
            'default_tasks',
            'sosmed_accounts',
            'sosmed_tasks',
            'sosmed_approval_logs',
            'pm_sosmed_oversight',
            'notifications',
            'sessions',
            'password_reset_tokens',
            'wa_groups',
        ];

        foreach ($tablesToTruncate as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line("  ✔ Truncated: <comment>{$table}</comment>");
            }
        }

        // Hapus semua user kecuali email yang dipertahankan
        $deletedUsers = DB::table('users')->where('email', '!=', $keepEmail)->delete();
        $this->line("  ✔ Dihapus {$deletedUsers} user lain (akun <info>{$keepEmail}</info> tetap tersimpan).");

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Bersihkan cache & view
        $this->callSilently('cache:clear');
        $this->callSilently('view:clear');

        $this->newLine();
        $this->info("✔ Pembersihan database selesai!");

        $user = DB::table('users')->where('email', $keepEmail)->first(['id', 'name', 'email', 'role', 'is_active']);
        if ($user) {
            $this->table(
                ['ID', 'Name', 'Email', 'Role', 'Status Aktif'],
                [[(array)$user]['0']]
            );
        } else {
            $this->warn("Perhatian: Akun {$keepEmail} tidak ditemukan di database.");
        }

        return 0;
    }
}
