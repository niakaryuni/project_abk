<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel catatan_harian untuk fitur Input Catatan Harian oleh Guru
     */
    public function up(): void
    {
        Schema::create('catatan_harian', function (Blueprint $table) {
            $table->id();

            // Relasi ke siswa ABK
            $table->unsignedBigInteger('abk_id');
            $table->foreign('abk_id')
                  ->references('id')->on('abks')
                  ->onDelete('cascade');

            // Relasi ke guru yang menulis catatan
            $table->unsignedBigInteger('id_guru');
            $table->foreign('id_guru')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            // Isi catatan harian
            $table->date('tanggal');
            $table->text('isi_catatan');

            // Status/kondisi anak hari itu (opsional)
            $table->enum('kondisi', ['baik', 'cukup', 'kurang'])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_harian');
    }
};