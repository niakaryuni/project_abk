<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom jenis_laporan, semester, dan tahun_ajaran
     * ke tabel laporan.
     *
     * Dibuat idempotent: tiap kolom dicek satu per satu, jadi kalau
     * sebagian sudah ada duluan, migration ini tidak akan error dan
     * hanya menambahkan kolom yang benar-benar belum ada.
     */
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            if (! Schema::hasColumn('laporan', 'jenis_laporan')) {
                $table->enum('jenis_laporan', ['akademik', 'perkembangan', 'gabungan'])
                      ->default('gabungan')
                      ->after('id_guru');
            }

            if (! Schema::hasColumn('laporan', 'semester')) {
                $table->enum('semester', ['1', '2'])
                      ->nullable()
                      ->after('periode');
            }

            if (! Schema::hasColumn('laporan', 'tahun_ajaran')) {
                $table->string('tahun_ajaran', 10)
                      ->nullable()
                      ->after('semester');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            foreach (['jenis_laporan', 'semester', 'tahun_ajaran'] as $column) {
                if (Schema::hasColumn('laporan', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};