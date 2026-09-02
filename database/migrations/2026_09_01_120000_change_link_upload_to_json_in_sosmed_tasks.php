<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom sementara (JSON)
        Schema::table('sosmed_tasks', function (Blueprint $table) {
            $table->json('link_upload_new')->nullable()->after('link_upload');
        });

        // 2. Migrate data lama: bungkus string URL lama jadi array JSON satu elemen
        DB::statement("
            UPDATE sosmed_tasks
            SET link_upload_new = JSON_ARRAY(link_upload)
            WHERE link_upload IS NOT NULL AND link_upload != ''
        ");

        // 3. Hapus kolom lama
        Schema::table('sosmed_tasks', function (Blueprint $table) {
            $table->dropColumn('link_upload');
        });

        // 4. Rename kolom baru
        Schema::table('sosmed_tasks', function (Blueprint $table) {
            $table->renameColumn('link_upload_new', 'link_upload');
        });
    }

    public function down(): void
    {
        // Balik ke varchar — ambil elemen pertama dari JSON array
        Schema::table('sosmed_tasks', function (Blueprint $table) {
            $table->string('link_upload_old', 500)->nullable()->after('link_upload');
        });

        DB::statement("
            UPDATE sosmed_tasks
            SET link_upload_old = JSON_UNQUOTE(JSON_EXTRACT(link_upload, '$[0]'))
            WHERE link_upload IS NOT NULL
        ");

        Schema::table('sosmed_tasks', function (Blueprint $table) {
            $table->dropColumn('link_upload');
        });

        Schema::table('sosmed_tasks', function (Blueprint $table) {
            $table->renameColumn('link_upload_old', 'link_upload');
        });
    }
};
