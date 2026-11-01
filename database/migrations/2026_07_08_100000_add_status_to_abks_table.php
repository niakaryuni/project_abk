<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom status (aktif/nonaktif) ke tabel abks
     * untuk fitur nonaktifkan/aktifkan siswa (soft-deactivation).
     *
     * Dibuat idempotent: kalau kolom 'status' sudah ada duluan
     * (misalnya ditambahkan manual sebelumnya), migration ini
     * tidak akan mencoba menambahkannya lagi dan tidak akan error.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('abks', 'status')) {
            Schema::table('abks', function (Blueprint $table) {
                $table->enum('status', ['aktif', 'nonaktif'])
                      ->default('aktif')
                      ->after('id_orang_tua');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('abks', 'status')) {
            Schema::table('abks', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};