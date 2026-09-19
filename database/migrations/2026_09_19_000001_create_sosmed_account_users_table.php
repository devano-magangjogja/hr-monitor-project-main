<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel pivot many-to-many
        if (!Schema::hasTable('sosmed_account_users')) {
            Schema::create('sosmed_account_users', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sosmed_account_id')
                      ->constrained('sosmed_accounts')
                      ->cascadeOnDelete();
                $table->foreignId('user_id')
                      ->constrained('users')
                      ->cascadeOnDelete();
                $table->foreignId('assigned_by')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete();
                $table->timestamp('assigned_at')->useCurrent();
                $table->unique(['sosmed_account_id', 'user_id']);
            });
        }

        // 2. Migrasi data staff_id existing ke tabel pivot
        if (Schema::hasColumn('sosmed_accounts', 'staff_id')) {
            $rows = DB::table('sosmed_accounts')
                ->whereNotNull('staff_id')
                ->select('id', 'staff_id', 'created_by')
                ->get();

            foreach ($rows as $row) {
                // Hindari duplicate jika migration sudah pernah dijalankan sebagian
                $exists = DB::table('sosmed_account_users')
                    ->where('sosmed_account_id', $row->id)
                    ->where('user_id', $row->staff_id)
                    ->exists();

                if (!$exists) {
                    DB::table('sosmed_account_users')->insert([
                        'sosmed_account_id' => $row->id,
                        'user_id'           => $row->staff_id,
                        'assigned_by'       => $row->created_by,
                        'assigned_at'       => now(),
                    ]);
                }
            }

            // 3. Drop kolom staff_id setelah data dimigrasikan
            Schema::table('sosmed_accounts', function (Blueprint $table) {
                $table->dropForeign(['staff_id']);
                $table->dropColumn('staff_id');
            });
        }
    }

    public function down(): void
    {
        // Kembalikan kolom staff_id
        if (!Schema::hasColumn('sosmed_accounts', 'staff_id')) {
            Schema::table('sosmed_accounts', function (Blueprint $table) {
                $table->foreignId('staff_id')
                      ->nullable()
                      ->after('pm_id')
                      ->constrained('users')
                      ->nullOnDelete();
            });

            // Restore data: ambil baris pertama dari pivot per akun
            $pivotRows = DB::table('sosmed_account_users')
                ->orderBy('assigned_at')
                ->get()
                ->groupBy('sosmed_account_id');

            foreach ($pivotRows as $accountId => $users) {
                $first = $users->first();
                DB::table('sosmed_accounts')
                    ->where('id', $accountId)
                    ->update(['staff_id' => $first->user_id]);
            }
        }

        Schema::dropIfExists('sosmed_account_users');
    }
};
