<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan foreign key id_guru dan id_orang_tua ke tabel abks
     */
    public function up(): void
    {
        Schema::table('abks', function (Blueprint $table) {
            // Tambah kolom id_guru (guru yang mengelola siswa)
            $table->unsignedBigInteger('id_guru')->nullable()->after('keterangan');
            // Tambah kolom id_orang_tua (orang tua yang memantau siswa)
            $table->unsignedBigInteger('id_orang_tua')->nullable()->after('id_guru');

            // Foreign key constraints
            $table->foreign('id_guru')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            $table->foreign('id_orang_tua')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abks', function (Blueprint $table) {
            $table->dropForeign(['id_guru']);
            $table->dropForeign(['id_orang_tua']);
            $table->dropColumn(['id_guru', 'id_orang_tua']);
        });
    }
};