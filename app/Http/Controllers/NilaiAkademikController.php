<?php

namespace App\Http\Controllers;

use App\Models\Abk;
use App\Models\NilaiAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiAkademikController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = NilaiAkademik::with(['abk', 'guru']);

        if ($user->isOrangTua()) {
            $abkIds = Abk::where('id_orang_tua', $user->id)->pluck('id');
            $query->whereIn('abk_id', $abkIds);
        } elseif ($user->isGuru()) {
            $query->where('id_guru', $user->id);
        }

        if ($request->filled('abk_id'))        $query->where('abk_id', $request->abk_id);
        if ($request->filled('semester'))       $query->where('semester', $request->semester);
        if ($request->filled('tahun_ajaran'))   $query->where('tahun_ajaran', $request->tahun_ajaran);
        if ($request->filled('mata_pelajaran')) $query->where('mata_pelajaran', $request->mata_pelajaran);

        $nilaiAkademik = $query->latest('tanggal_input')->paginate(10);
        $siswaDaftar   = Abk::orderBy('nama')->get();
        $mataDaftar    = NilaiAkademik::MATA_PELAJARAN;
        $tahunDaftar   = NilaiAkademik::distinct()->pluck('tahun_ajaran')->filter()->sort()->values();

        return view('nilai_akademik.index', compact(
            'nilaiAkademik', 'siswaDaftar', 'mataDaftar', 'tahunDaftar'
        ));
    }

    public function create()
    {
        $siswaDaftar = Abk::orderBy('nama')->get();
        $mataDaftar  = NilaiAkademik::MATA_PELAJARAN;
        return view('nilai_akademik.create', compact('siswaDaftar', 'mataDaftar'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'abk_id'         => 'required|exists:abks,id',
            'mata_pelajaran' => 'required|string',
            'nilai_akhir'    => 'required|numeric|min:0|max:100',
            'semester'       => 'required|in:1,2',
            'tahun_ajaran'   => 'required|string|max:10',
            'catatan'        => 'nullable|string',
            'tanggal_input'  => 'required|date',
        ]);

        NilaiAkademik::create([
            'abk_id'         => $request->abk_id,
            'id_guru'        => Auth::id(),
            'mata_pelajaran' => $request->mata_pelajaran,
            'nilai_akhir'    => $request->nilai_akhir,
            'predikat'       => NilaiAkademik::hitungPredikat($request->nilai_akhir),
            'semester'       => $request->semester,
            'tahun_ajaran'   => $request->tahun_ajaran,
            'catatan'        => $request->catatan,
            'tanggal_input'  => $request->tanggal_input,
        ]);

        return redirect()->route('nilai-akademik.index')
            ->with('success', 'Nilai akademik berhasil disimpan.');
    }

    public function edit(NilaiAkademik $nilaiAkademik)
    {
        $siswaDaftar = Abk::orderBy('nama')->get();
        $mataDaftar  = NilaiAkademik::MATA_PELAJARAN;
        return view('nilai_akademik.edit', compact('nilaiAkademik', 'siswaDaftar', 'mataDaftar'));
    }

    public function update(Request $request, NilaiAkademik $nilaiAkademik)
    {
        $request->validate([
            'abk_id'         => 'required|exists:abks,id',
            'mata_pelajaran' => 'required|string',
            'nilai_akhir'    => 'required|numeric|min:0|max:100',
            'semester'       => 'required|in:1,2',
            'tahun_ajaran'   => 'required|string|max:10',
            'catatan'        => 'nullable|string',
            'tanggal_input'  => 'required|date',
        ]);

        $nilaiAkademik->update([
            'abk_id'         => $request->abk_id,
            'mata_pelajaran' => $request->mata_pelajaran,
            'nilai_akhir'    => $request->nilai_akhir,
            'predikat'       => NilaiAkademik::hitungPredikat($request->nilai_akhir),
            'semester'       => $request->semester,
            'tahun_ajaran'   => $request->tahun_ajaran,
            'catatan'        => $request->catatan,
            'tanggal_input'  => $request->tanggal_input,
        ]);

        return redirect()->route('nilai-akademik.index')
            ->with('success', 'Nilai akademik berhasil diperbarui.');
    }

    public function destroy(NilaiAkademik $nilaiAkademik)
    {
        $nilaiAkademik->delete();
        return redirect()->route('nilai-akademik.index')
            ->with('success', 'Nilai akademik berhasil dihapus.');
    }
}