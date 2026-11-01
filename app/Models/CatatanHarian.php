<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanHarian extends Model
{
    use HasFactory;

    protected $table = 'catatan_harian';

    protected $fillable = [
        'abk_id',
        'id_guru',
        'tanggal',
        'isi_catatan',
        'kondisi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    const KONDISI = ['baik', 'cukup', 'kurang'];

    public function abk()
    {
        return $this->belongsTo(Abk::class, 'abk_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'id_guru');
    }

    public function badgeKondisi(): string
    {
        return match($this->kondisi) {
            'baik'   => 'success',
            'cukup'  => 'warning',
            'kurang' => 'danger',
            default  => 'secondary',
        };
    }
}