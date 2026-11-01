<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilais';

    protected $fillable = [
        'abk_id',
        'id_guru',
        'mata_pelajaran',
        'aspek',
        'nilai',
        'catatan',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Konstanta aspek penilaian
    const ASPEK = ['motorik', 'keterampilan', 'sosial'];

    // Relasi: nilai dimiliki oleh satu siswa ABK
    public function abk()
    {
        return $this->belongsTo(Abk::class, 'abk_id');
    }

    // Relasi: nilai diinput oleh satu guru
    public function guru()
    {
        return $this->belongsTo(User::class, 'id_guru');
    }

    // Helper: label warna berdasarkan nilai
    public function badgeWarna(): string
    {
        return match(true) {
            $this->nilai >= 80 => 'success',
            $this->nilai >= 60 => 'warning',
            default            => 'danger',
        };
    }

    // Helper: label nilai
    public function labelNilai(): string
    {
        return match(true) {
            $this->nilai >= 80 => 'Baik',
            $this->nilai >= 60 => 'Cukup',
            default            => 'Perlu Perhatian',
        };
    }
}