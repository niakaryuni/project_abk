<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Role helpers ──────────────────────────────────────────────────────
    public function isAdmin(): bool        { return $this->role === 'admin'; }
    public function isGuru(): bool         { return $this->role === 'guru'; }
    public function isKepalaSekolah(): bool{ return $this->role === 'kepala_sekolah'; }
    public function isOrangTua(): bool     { return $this->role === 'orangtua'; }
    public function isAktif(): bool        { return $this->status === 'aktif'; }

    // ── Relasi ────────────────────────────────────────────────────────────
    public function abkDikelola()
    {
        return $this->hasMany(Abk::class, 'id_guru');
    }

    public function anakAbk()
    {
        return $this->hasMany(Abk::class, 'id_orang_tua');
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'id_guru');
    }

    public function nilaiAkademik()
    {
        return $this->hasMany(NilaiAkademik::class, 'id_guru');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'id_guru');
    }
}