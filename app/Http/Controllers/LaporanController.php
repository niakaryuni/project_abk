<?php

namespace App\Http\Controllers;

use App\Models\Abk;
use App\Models\Laporan;
use App\Models\Nilai;
use App\Models\NilaiAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Laporan::with(['abk', 'guru']);

        if ($user->isAdmin() || $user->isKepalaSekolah()) {
            // Admin & Kepala Sekolah bisa melihat semua laporan
        } elseif ($user->isGuru()) {
            $query->where('id_guru', $user->id);
        } elseif ($user->isOrangTua()) {
            $abkIds = Abk::where('id_orang_tua', $user->id)->pluck('id');
            $query->whereIn('abk_id', $abkIds)->where('status', 'final');
        } else {
            $query->whereRaw('1 = 0'); // role tidak dikenali, tidak ada akses
        }

        // ===== Pencarian berdasarkan nama siswa =====
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('abk', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        // ===== Filter Jenis Laporan =====
        if ($request->filled('jenis_laporan')) {
            $query->where('jenis_laporan', $request->jenis_laporan);
        }

        // ===== Filter Status (Orang Tua hanya boleh lihat status final, jadi filter ini disembunyikan untuk mereka) =====
        if ($request->filled('status') && !$user->isOrangTua()) {
            $query->where('status', $request->status);
        }

        $laporans = $query->latest()->paginate(10)->withQueryString();

        // Data untuk modal "Tambah Laporan" (hanya dibutuhkan oleh Guru)
        $abks = $user->isGuru()
            ? Abk::where('id_guru', $user->id)->where('status', 'aktif')->get()
            : collect();

        return view('laporan.index', compact('laporans', 'abks'));
    }

    public function create()
    {
        $user = Auth::user();
        $abks = $user->isGuru()
            ? Abk::where('id_guru', $user->id)->get()
            : Abk::all();
        return view('laporan.create', compact('abks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'abk_id'        => 'required|exists:abks,id',
            // PERBAIKAN: enum di database adalah 'akademik','perkembangan','gabungan'
            // sebelumnya kode memakai 'lengkap' yang tidak ada di enum -> data truncated
            'jenis_laporan' => 'required|in:perkembangan,akademik,gabungan',
            'periode'       => 'required|string|max:255',
            'semester'      => 'required|in:1,2',
            'tahun_ajaran'  => 'required|string|max:10',
            'status'        => 'required|in:draft,final',
        ]);

        // Ringkasan dibuat otomatis dari data yang sudah ada (tidak diinput manual)
        $ringkasan = $this->generateRingkasan(
            $request->abk_id,
            $request->semester,
            $request->tahun_ajaran,
            $request->jenis_laporan
        );

        Laporan::create([
            'abk_id'        => $request->abk_id,
            'jenis_laporan' => $request->jenis_laporan,
            'id_guru'       => Auth::id(),
            'periode'       => $request->periode,
            'semester'      => $request->semester,
            'tahun_ajaran'  => $request->tahun_ajaran,
            'ringkasan'     => $ringkasan,
            'status'        => $request->status,
        ]);

        return redirect()->route('laporan.index')
                         ->with('success', 'Laporan berhasil dibuat otomatis dari data nilai siswa.');
    }

    public function show(Laporan $laporan)
    {
        $laporan->load(['abk', 'guru']);
        return view('laporan.show', compact('laporan'));
    }

    public function edit(Laporan $laporan)
    {
        $user = Auth::user();
        $abks = $user->isGuru()
            ? Abk::where('id_guru', $user->id)->get()
            : Abk::all();
        return view('laporan.edit', compact('laporan', 'abks'));
    }

    public function update(Request $request, Laporan $laporan)
    {
        $request->validate([
            'abk_id'        => 'required|exists:abks,id',
            'jenis_laporan' => 'required|in:perkembangan,akademik,gabungan',
            'periode'       => 'required|string|max:255',
            'semester'      => 'required|in:1,2',
            'tahun_ajaran'  => 'required|string|max:10',
            'status'        => 'required|in:draft,final',
        ]);

        // Generate ulang ringkasan jika jenis/semester/tahun ajaran berubah
        $ringkasan = $this->generateRingkasan(
            $request->abk_id,
            $request->semester,
            $request->tahun_ajaran,
            $request->jenis_laporan
        );

        $laporan->update([
            'abk_id'        => $request->abk_id,
            'jenis_laporan' => $request->jenis_laporan,
            'periode'       => $request->periode,
            'semester'      => $request->semester,
            'tahun_ajaran'  => $request->tahun_ajaran,
            'ringkasan'     => $ringkasan,
            'status'        => $request->status,
        ]);

        return redirect()->route('laporan.index')
                         ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function destroy(Laporan $laporan)
    {
        $laporan->delete();
        return redirect()->route('laporan.index')
                         ->with('success', 'Laporan berhasil dihapus.');
    }

    // Generate & download PDF otomatis dari data siswa
    public function download(Laporan $laporan)
    {
        // Use Case Unduh Laporan, Alt Step 6: laporan berstatus draft
        // yang diakses Orang Tua harus ditolak.
        $user = Auth::user();
        abort_if(
            $user->isOrangTua() && $laporan->status !== 'final',
            403,
            'Laporan ini masih berstatus draft dan belum dapat diunduh.'
        );

        $laporan->load(['abk.guru', 'abk.orangTua', 'guru']);

        $abk = $laporan->abk;

        $nilaiPerkembangan = Nilai::where('abk_id', $abk->id)
            ->orderBy('tanggal')
            ->get()
            ->groupBy('aspek');

        $nilaiAkademik = NilaiAkademik::where('abk_id', $abk->id)
            ->where('semester', $laporan->semester)
            ->where('tahun_ajaran', $laporan->tahun_ajaran)
            ->orderBy('mata_pelajaran')
            ->get();

        $avgMotorik      = Nilai::where('abk_id', $abk->id)->where('aspek', 'motorik')->avg('nilai') ?? 0;
        $avgKeterampilan = Nilai::where('abk_id', $abk->id)->where('aspek', 'keterampilan')->avg('nilai') ?? 0;
        $avgSosial       = Nilai::where('abk_id', $abk->id)->where('aspek', 'sosial')->avg('nilai') ?? 0;

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'laporan', 'abk',
            'nilaiPerkembangan', 'nilaiAkademik',
            'avgMotorik', 'avgKeterampilan', 'avgSosial'
        ))->setPaper('a4', 'portrait');

        $filename = 'Laporan_' . $abk->nama
            . '_Semester' . $laporan->semester
            . '_' . $laporan->tahun_ajaran . '.pdf';
        $filename = str_replace([' ', '/'], ['_', '-'], $filename);

        return $pdf->download($filename);
    }

    /**
     * Generate ringkasan laporan secara otomatis berdasarkan
     * data nilai perkembangan & nilai akademik yang sudah ada.
     */
    private function generateRingkasan($abkId, $semester, $tahunAjaran, $jenisLaporan): string
    {
        $abk = Abk::find($abkId);
        $namaSiswa = $abk->nama ?? 'Siswa';

        $teksPerkembangan = '';
        $teksAkademik = '';

        // PERBAIKAN: 'lengkap' -> 'gabungan' agar sesuai dengan enum di database
        if (in_array($jenisLaporan, ['perkembangan', 'gabungan'])) {
            $aspekList = ['motorik' => 'Motorik', 'keterampilan' => 'Keterampilan', 'sosial' => 'Sosial'];
            $baris = [];

            foreach ($aspekList as $key => $label) {
                $nilaiAspek = Nilai::where('abk_id', $abkId)->where('aspek', $key);
                $rata = $nilaiAspek->avg('nilai');

                if ($rata !== null) {
                    $catatanTerbaru = Nilai::where('abk_id', $abkId)
                        ->where('aspek', $key)
                        ->latest('tanggal')
                        ->value('catatan');

                    $baris[] = "- {$label}: rata-rata nilai " . round($rata, 1)
                        . ($catatanTerbaru ? ". Catatan terbaru: {$catatanTerbaru}" : '');
                } else {
                    $baris[] = "- {$label}: belum ada data penilaian.";
                }
            }

            $teksPerkembangan = "Perkembangan Non-Akademik:\n" . implode("\n", $baris);
        }

        // PERBAIKAN: 'lengkap' -> 'gabungan' agar sesuai dengan enum di database
        if (in_array($jenisLaporan, ['akademik', 'gabungan'])) {
            $nilaiAkademik = NilaiAkademik::where('abk_id', $abkId)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->orderBy('mata_pelajaran')
                ->get();

            if ($nilaiAkademik->isEmpty()) {
                $teksAkademik = "Nilai Akademik Semester {$semester} ({$tahunAjaran}):\nBelum ada data nilai akademik untuk periode ini.";
            } else {
                $baris = $nilaiAkademik->map(function ($n) {
                    return "- {$n->mata_pelajaran}: {$n->nilai}";
                })->implode("\n");

                $rataAkademik = round($nilaiAkademik->avg('nilai'), 1);
                $teksAkademik = "Nilai Akademik Semester {$semester} ({$tahunAjaran}):\n{$baris}\n\nRata-rata nilai akademik: {$rataAkademik}";
            }
        }

        $ringkasan = "Ringkasan Laporan {$namaSiswa}\n\n";
        $ringkasan .= trim($teksPerkembangan . "\n\n" . $teksAkademik);

        return trim($ringkasan);
    }
}