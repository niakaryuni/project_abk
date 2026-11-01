<?php

namespace App\Http\Controllers;

use App\Models\Abk;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Nilai::with(['abk', 'guru']);

        // Filter berdasarkan role
        if ($user->isGuru()) {
            $query->where('id_guru', $user->id);
        } elseif ($user->isOrangTua()) {
            $abkIds = Abk::where('id_orang_tua', $user->id)->pluck('id');
            $query->whereIn('abk_id', $abkIds);
        }

        // Search & filter
        if ($request->filled('search')) {
            $query->whereHas('abk', function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('aspek')) {
            $query->where('aspek', $request->aspek);
        }
        if ($request->filled('dari')) {
            $query->where('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('tanggal', '<=', $request->sampai);
        }
        if ($request->filled('abk_id')) {
            $query->where('abk_id', $request->abk_id);
        }

        $nilais = $query->latest('tanggal')->paginate(10)->withQueryString();
        $abks   = Abk::all();

        // Jika ada siswa yang dipilih di filter, siapkan rata-rata & grafik
        // perkembangan untuk siswa itu (sama seperti di halaman Detail Siswa)
        $selectedAbk     = null;
        $avgMotorik      = 0;
        $avgKeterampilan = 0;
        $avgSosial       = 0;
        $grafikData      = collect();

        if ($request->filled('abk_id')) {
            $selectedAbk = Abk::find($request->abk_id);

            if ($selectedAbk) {
                $semuaNilaiSiswa = Nilai::where('abk_id', $selectedAbk->id)->get();

                $avgMotorik      = $semuaNilaiSiswa->where('aspek', 'motorik')->avg('nilai') ?? 0;
                $avgKeterampilan = $semuaNilaiSiswa->where('aspek', 'keterampilan')->avg('nilai') ?? 0;
                $avgSosial       = $semuaNilaiSiswa->where('aspek', 'sosial')->avg('nilai') ?? 0;

                $grafikData = Nilai::where('abk_id', $selectedAbk->id)
                    ->where('tanggal', '>=', now()->subDays(90))
                    ->orderBy('tanggal')
                    ->get()
                    ->groupBy('aspek');
            }
        }

        return view('nilai.index', compact(
            'nilais', 'abks',
            'selectedAbk', 'avgMotorik', 'avgKeterampilan', 'avgSosial', 'grafikData'
        ));
    }

    public function create()
    {
        $abks   = Abk::all();
        $aspeks = Nilai::ASPEK;
        return view('nilai.create', compact('abks', 'aspeks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'abk_id'               => 'required|exists:abks,id',
            'mata_pelajaran_program'=> 'required|string|max:255',
            'aspek_program'        => 'required|in:motorik,keterampilan,sosial',
            'nilai'                => 'required|integer|min:0|max:100',
            'catatan'              => 'nullable|string',
            'tanggal'              => 'required|date',
        ]);

        Nilai::create([
            'abk_id'         => $request->abk_id,
            'id_guru'        => Auth::id(),
            'mata_pelajaran' => $request->mata_pelajaran_program,
            'aspek'          => $request->aspek_program,
            'nilai'          => $request->nilai,
            'catatan'        => $request->catatan,
            'tanggal'        => $request->tanggal,
        ]);

        return redirect()->route('nilai.index')
                         ->with('success', 'Data perkembangan berhasil disimpan.');
    }

    public function show(Nilai $nilai)
    {
        $nilai->load(['abk', 'guru']);
        return view('nilai.show', compact('nilai'));
    }

    public function edit(Nilai $nilai)
    {
        $abks   = Abk::all();
        $aspeks = Nilai::ASPEK;
        return view('nilai.edit', compact('nilai', 'abks', 'aspeks'));
    }

    public function update(Request $request, Nilai $nilai)
    {
        $request->validate([
            'abk_id'         => 'required|exists:abks,id',
            'mata_pelajaran' => 'required|string|max:255',
            'aspek'          => 'required|in:motorik,keterampilan,sosial',
            'nilai'          => 'required|integer|min:0|max:100',
            'catatan'        => 'nullable|string',
            'tanggal'        => 'required|date',
        ]);

        $nilai->update($request->only([
            'abk_id', 'mata_pelajaran', 'aspek', 'nilai', 'catatan', 'tanggal'
        ]));

        return redirect()->route('nilai.index')
                         ->with('success', 'Data perkembangan berhasil diperbarui.');
    }

    public function destroy(Nilai $nilai)
    {
        $nilai->delete();

        return redirect()->route('nilai.index')
                         ->with('success', 'Data perkembangan berhasil dihapus.');
    }
}