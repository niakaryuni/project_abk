<?php

namespace App\Http\Controllers;

use App\Models\Abk;
use App\Models\Nilai;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ── Data disesuaikan per role ──────────────────────────
        if ($user->isOrangTua()) {
            // Orang tua hanya lihat data anaknya sendiri
            $abkIds = Abk::where('id_orang_tua', $user->id)->pluck('id');

            $totalAbk        = $abkIds->count();
            $totalNilai      = Nilai::whereIn('abk_id', $abkIds)->count();
            $motorik         = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'motorik')->count();
            $keterampilan    = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'keterampilan')->count();
            $sosial          = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'sosial')->count();
            $avgMotorik      = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'motorik')->avg('nilai') ?? 0;
            $avgKeterampilan = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'keterampilan')->avg('nilai') ?? 0;
            $avgSosial       = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'sosial')->avg('nilai') ?? 0;
            $totalLaporan    = Laporan::whereIn('abk_id', $abkIds)->where('status', 'final')->count();
            $totalGuru       = 0;
            $totalOrtu       = 0;
            $recentAbk       = Abk::with('guru')->whereIn('id', $abkIds)->latest()->limit(5)->get();
            $recentNilai     = Nilai::with(['abk', 'guru'])->whereIn('abk_id', $abkIds)->latest('tanggal')->limit(5)->get();

        } elseif ($user->isGuru()) {
            // Guru hanya lihat data siswa yang diajarnya
            $abkIds = Abk::where('id_guru', $user->id)->pluck('id');

            $totalAbk        = $abkIds->count();
            $totalNilai      = Nilai::whereIn('abk_id', $abkIds)->count();
            $motorik         = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'motorik')->count();
            $keterampilan    = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'keterampilan')->count();
            $sosial          = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'sosial')->count();
            $avgMotorik      = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'motorik')->avg('nilai') ?? 0;
            $avgKeterampilan = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'keterampilan')->avg('nilai') ?? 0;
            $avgSosial       = Nilai::whereIn('abk_id', $abkIds)->where('aspek', 'sosial')->avg('nilai') ?? 0;
            $totalLaporan    = Laporan::where('id_guru', $user->id)->count();
            $totalGuru       = 0;
            $totalOrtu       = 0;
            $recentAbk       = Abk::with('guru')->whereIn('id', $abkIds)->latest()->limit(5)->get();
            $recentNilai     = Nilai::with(['abk', 'guru'])->whereIn('abk_id', $abkIds)->latest('tanggal')->limit(5)->get();

        } else {
            // Admin & Kepala Sekolah lihat semua data
            $totalAbk        = Abk::count();
            $totalNilai      = Nilai::count();
            $motorik         = Nilai::where('aspek', 'motorik')->count();
            $keterampilan    = Nilai::where('aspek', 'keterampilan')->count();
            $sosial          = Nilai::where('aspek', 'sosial')->count();
            $avgMotorik      = Nilai::where('aspek', 'motorik')->avg('nilai') ?? 0;
            $avgKeterampilan = Nilai::where('aspek', 'keterampilan')->avg('nilai') ?? 0;
            $avgSosial       = Nilai::where('aspek', 'sosial')->avg('nilai') ?? 0;
            $totalLaporan    = Laporan::count();
            $totalGuru       = User::where('role', 'guru')->count();
            $totalOrtu       = User::where('role', 'orangtua')->count();
            $recentAbk       = Abk::with('guru')->latest()->limit(5)->get();
            $recentNilai     = Nilai::with(['abk', 'guru'])->latest('tanggal')->limit(5)->get();
        }

        $siswaList   = $recentAbk;
        $nilaiTerbaru = $recentNilai;

        return view('dashboard', compact(
            'totalAbk', 'totalNilai',
            'motorik', 'keterampilan', 'sosial',
            'avgMotorik', 'avgKeterampilan', 'avgSosial',
            'totalGuru', 'totalOrtu', 'totalLaporan',
            'siswaList', 'nilaiTerbaru', 'recentAbk', 'recentNilai'
        ));
    }
}