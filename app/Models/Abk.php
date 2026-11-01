<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abk extends Model
{
    use HasFactory;

    protected $table = 'abks';

    protected $fillable = [
        'nama',
        'umur',
        'kelas',
        'keterangan',
        'id_guru',
        'id_orang_tua',
        'status',
    ];

    // Relasi: siswa ABK dimiliki oleh satu guru
    public function guru()
    {
        return $this->belongsTo(User::class, 'id_guru');
    }

    // Relasi: siswa ABK dimiliki oleh satu orang tua
    public function orangTua()
    {
        return $this->belongsTo(User::class, 'id_orang_tua');
    }

    // Relasi: nilai perkembangan mingguan
    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'abk_id');
    }

    // Relasi: nilai akademik per semester
    public function nilaiAkademik()
    {
        return $this->hasMany(NilaiAkademik::class, 'abk_id');
    }

    // Relasi: laporan
    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'abk_id');
    }

    // Helper: 5 perkembangan terbaru
    public function perkembanganTerbaru()
    {
        return $this->hasMany(Nilai::class, 'abk_id')->latest('tanggal')->limit(5);
    }

    // Helper: rata-rata per aspek
    public function avgAspek(string $aspek): float
    {
        return $this->nilais()->where('aspek', $aspek)->avg('nilai') ?? 0;
    }
}