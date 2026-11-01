<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';

    protected $fillable = [
        'abk_id',
        'jenis_laporan',
        'id_guru',
        'periode',
        'semester',
        'tahun_ajaran',
        'ringkasan',
        'file_path',
        'status',
    ];

    // Relasi: laporan dimiliki oleh satu siswa ABK
    public function abk()
    {
        return $this->belongsTo(Abk::class, 'abk_id');
    }

    // Relasi: laporan dibuat oleh satu guru
    public function guru()
    {
        return $this->belongsTo(User::class, 'id_guru');
    }

    // Helper: cek apakah laporan sudah final
    public function isFinal(): bool
    {
        return $this->status === 'final';
    }

    // Helper: cek apakah file PDF tersedia
    public function adaFile(): bool
    {
        return !is_null($this->file_path);
    }

    // Helper: badge status
    public function badgeStatus(): string
    {
        return $this->status === 'final' ? 'success' : 'secondary';
    }

    // Helper: label jenis laporan untuk ditampilkan di tabel
    public function labelJenis(): string
    {
        return match ($this->jenis_laporan) {
            'perkembangan' => 'Perkembangan',
            'akademik'     => 'Akademik',
            'lengkap'      => 'Lengkap (Gabungan)',
            default        => '-',
        };
    }
}