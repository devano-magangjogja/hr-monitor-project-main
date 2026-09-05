<?php

namespace App\Console\Commands;

use App\Models\Presensi;
use App\Models\SosmedApprovalLog;
use App\Models\SosmedTask;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Services\DefaultTaskService;
use App\Services\TaskService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SimulateNextDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'day:simulate 
                            {--days=1 : Jumlah hari untuk dimundurkan (default 1)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulasi pergantian hari terpadu: memundurkan tanggal presensi, tugas harian, dan tugas sosmed, serta menjalankan penanda tugas belum selesai dan generate tugas harian baru';

    public function handle(DefaultTaskService $defaultTaskService, TaskService $taskService): int
    {
        $today = Carbon::today()->toDateString();
        $days = (int) $this->option('days');
        if ($days < 1) {
            $days = 1;
        }
        $yesterday = Carbon::today()->subDays($days)->toDateString();

        $this->info("=================================================");
        $this->info("  SIMULASI PERGANTIAN HARI TERPADU");
        $this->info("  Dari Hari Ini ({$today}) -> Kemarin ({$yesterday})");
        $this->info("=================================================\n");

        // ── 1. PRESENSI PEMAGANG ─────────────────────────────
        $this->comment("[1/4] Memproses Presensi Pemagang...");
        $presensiCount = Presensi::whereDate('tanggal', $today)->count();
        if ($presensiCount > 0) {
            Presensi::whereDate('tanggal', $today)->update(['tanggal' => $yesterday]);
            $this->line("  ✔ {$presensiCount} data presensi hari ini dimundurkan ke {$yesterday}.");
            $this->line("  ✔ Presensi untuk hari ini ({$today}) sekarang kosong & siap diinput kembali.");
        } else {
            $this->line("  ℹ Tidak ada data presensi pemagang pada tanggal {$today}.");
        }

        // ── 2. TUGAS REGULER & RUTIN ─────────────────────────
        $this->comment("\n[2/4] Memproses Tugas Reguler & Rutin...");
        $tasksToday = Task::whereDate('task_date', $today)->get();
        $taskCount = $tasksToday->count();
        if ($taskCount > 0) {
            Task::whereDate('task_date', $today)->update(['task_date' => $yesterday]);
            $this->line("  ✔ {$taskCount} tugas hari ini dimundurkan ke {$yesterday}.");
        } else {
            $this->line("  ℹ Tidak ada tugas pada tanggal {$today}.");
        }

        // Mundurkan completed_at assignment hari ini
        $assignmentsToday = TaskAssignment::whereDate('completed_at', $today)->get();
        $assignmentCount = $assignmentsToday->count();
        if ($assignmentCount > 0) {
            foreach ($assignmentsToday as $assign) {
                $assign->completed_at = Carbon::parse($assign->completed_at)->subDays($days);
                $assign->save();
            }
            $this->line("  ✔ {$assignmentCount} catatan penyelesaian tugas (completed_at) dimundurkan.");
        }

        // Jalankan artisan tasks:mark-not-done
        $this->line("  -> Menjalankan tasks:mark-not-done...");
        $markedNotDone = $taskService->markAllPendingAsNotDone();
        $this->line("  ✔ {$markedNotDone} tugas kemarin yang belum selesai ditandai sebagai 'Tidak Dikerjakan'.");

        // Jalankan artisan tasks:generate-daily
        $this->line("  -> Menjalankan tasks:generate-daily...");
        $defaultTaskService->generateDailyTasks();
        $this->line("  ✔ Tugas rutin harian (termasuk tugas Presensi default) berhasil digenerate untuk hari ini ({$today}).");

        // ── 3. PENGELOLAAN SOSMED ────────────────────────────
        $this->comment("\n[3/4] Memproses Tugas Pengelolaan Sosmed...");
        $sosmedTasksToday = SosmedTask::whereDate('task_date', $today)->get();
        $sosmedCount = $sosmedTasksToday->count();
        if ($sosmedCount > 0) {
            foreach ($sosmedTasksToday as $st) {
                $st->task_date = Carbon::parse($st->task_date)->subDays($days);
                if ($st->verified_at && Carbon::parse($st->verified_at)->isToday()) {
                    $st->verified_at = Carbon::parse($st->verified_at)->subDays($days);
                }
                if ($st->hr_verified_at && Carbon::parse($st->hr_verified_at)->isToday()) {
                    $st->hr_verified_at = Carbon::parse($st->hr_verified_at)->subDays($days);
                }
                $st->save();
            }
            $this->line("  ✔ {$sosmedCount} tugas pengelolaan sosmed hari ini dimundurkan ke {$yesterday}.");
        } else {
            $this->line("  ℹ Tidak ada tugas sosmed hari ini ({$today}).");
        }

        // Mundurkan log persetujuan sosmed hari ini (jika ada)
        $approvalLogsToday = SosmedApprovalLog::whereDate('created_at', $today)->get();
        foreach ($approvalLogsToday as $log) {
            $log->created_at = Carbon::parse($log->created_at)->subDays($days);
            $log->save();
        }

        $this->line("  ✔ Akun sosmed aktif otomatis memerlukan submit bukti konten ulang untuk hari ini ({$today}).");
        $this->line("  ✔ Tugas sosmed kemarin yang belum disetujui final (approved_hr) tetap aman & tidak hilang dari 'Semua Tugas'.");

        // ── SELESAI ──────────────────────────────────────────
        $this->comment("\n[4/4] Selesai!");
        $this->info("Simulasi pergantian hari selesai sukses. Silakan refresh browser Anda!");
        $this->info("=================================================\n");

        return Command::SUCCESS;
    }
}
