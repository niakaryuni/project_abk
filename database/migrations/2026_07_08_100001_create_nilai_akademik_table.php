<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel nilai_akademik (sebelumnya tabel ini sudah ada
     * di database tapi belum punya file migration resmi).
     *
     * Dibuat idempotent: kalau tabel sudah ada duluan, migration ini
     * tidak akan mencoba membuatnya lagi dan tidak akan error.
     */
    public function up(): void
    {
        if (! Schema::hasTable('nilai_akademik')) {
            Schema::create('nilai_akademik', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('abk_id');
                $table->foreign('abk_id')
                      ->references('id')->on('abks')
                      ->onDelete('cascade');

                $table->unsignedBigInteger('id_guru');
                $table->foreign('id_guru')
                      ->references('id')->on('users')
                      ->onDelete('cascade');

                $table->string('mata_pelajaran');
                $table->decimal('nilai_akhir', 5, 2);
                $table->enum('predikat', ['A', 'B', 'C', 'D']);
                $table->enum('semester', ['1', '2']);
                $table->string('tahun_ajaran', 10);
                $table->text('catatan')->nullable();
                $table->date('tanggal_input');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_akademik');
    }
};