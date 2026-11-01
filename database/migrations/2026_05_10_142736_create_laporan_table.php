<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel laporan untuk fitur Lihat Laporan & Unduh Laporan
     */
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();

            // Relasi ke siswa ABK
            $table->unsignedBigInteger('abk_id');
            $table->foreign('abk_id')
                  ->references('id')->on('abks')
                  ->onDelete('cascade');

            // Guru yang membuat laporan
            $table->unsignedBigInteger('id_guru');
            $table->foreign('id_guru')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            // Periode laporan (contoh: "Januari 2026", "Semester 1 2025/2026")
            $table->string('periode');

            // Ringkasan perkembangan
            $table->text('ringkasan')->nullable();

            // Path file PDF laporan (untuk fitur Unduh Laporan)
            $table->string('file_path')->nullable();

            // Status laporan
            $table->enum('status', ['draft', 'final'])->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};