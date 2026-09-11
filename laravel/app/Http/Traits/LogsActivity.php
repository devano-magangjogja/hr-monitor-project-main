<?php

namespace App\Http\Traits;

use App\Models\ActivityLog;

/**
 * Trait LogsActivity
 *
 * Use this trait in any controller to get the logActivity() helper.
 * It delegates to ActivityLog::record() and automatically picks up
 * the authenticated user and request metadata.
 */
trait LogsActivity
{
    /**
     * Record an activity log entry.
     *
     * @param string     $action      Kode aksi, e.g. 'task.created'
     * @param string     $module      Modul, e.g. 'Tugas'
     * @param string     $description Kalimat deskriptif dalam bahasa Indonesia
     * @param mixed|null $subject     Model terkait (opsional)
     * @param array      $properties  Data tambahan opsional
     */
    protected function logActivity(
        string $action,
        string $module,
        string $description,
        mixed $subject = null,
        array $properties = []
    ): void {
        try {
            ActivityLog::record($action, $module, $description, $subject, $properties);
        } catch (\Throwable $e) {
            // Logging failure must never crash the application
            \Illuminate\Support\Facades\Log::warning('ActivityLog::record failed: ' . $e->getMessage());
        }
    }
}
