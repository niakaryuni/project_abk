<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAkademik extends Model
{
    use HasFactory;

    protected $table = 'nilai_akademik';

    protected $fillable = [
        'abk_id',
        'id_guru',
        'mata_pelajaran',
        'nilai_akhir',
        'predikat',
        'semester',
        'tahun_ajaran',
        'catatan',
        'tanggal_input',
    ];

    protected $casts = [
        'tanggal_input' => 'date',
        'nilai_akhir'   => 'float',
    ];

    // Daftar mata pelajaran (sesuaikan dengan kurikulum SKHN)
    const MATA_PELAJARAN = [
        'Matematika',
        'Bahasa Indonesia',
        'IPA',
        'IPS',
        'Pendidikan Agama',
        'PJOK',
        'Seni Budaya',
        'Keterampilan',
    ];

    // Relasi: nilai akademik dimiliki oleh satu siswa ABK
    public function abk()
    {
        return $this->belongsTo(Abk::class, 'abk_id');
    }

    // Relasi: nilai akademik diinput oleh satu guru
    public function guru()
    {
        return $this->belongsTo(User::class, 'id_guru');
    }

    // Auto-set predikat berdasarkan nilai_akhir
    public static function hitungPredikat(float $nilai): string
    {
        return match(true) {
            $nilai >= 90 => 'A',
            $nilai >= 75 => 'B',
            $nilai >= 60 => 'C',
            default      => 'D',
        };
    }

    // Helper: badge warna berdasarkan nilai
    public function badgeWarna(): string
    {
        return match(true) {
            $this->nilai_akhir >= 75 => 'success',
            $this->nilai_akhir >= 60 => 'warning',
            default                  => 'danger',
        };
    }

    // Helper: label nilai
    public function labelNilai(): string
    {
        return match(true) {
            $this->nilai_akhir >= 90 => 'Sangat Baik',
            $this->nilai_akhir >= 75 => 'Baik',
            $this->nilai_akhir >= 60 => 'Cukup',
            default                  => 'Perlu Perhatian',
        };
    }
}