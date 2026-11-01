<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan foreign key id_guru ke tabel nilais
     * agar diketahui guru mana yang menginput perkembangan
     */
    public function up(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            // Tambah kolom id_guru setelah abk_id
            $table->unsignedBigInteger('id_guru')->nullable()->after('abk_id');

            // Foreign key ke tabel users
            $table->foreign('id_guru')
                  ->references('id')->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropForeign(['id_guru']);
            $table->dropColumn('id_guru');
        });
    }
};