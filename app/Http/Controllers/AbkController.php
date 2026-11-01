<?php

namespace App\Http\Controllers;

use App\Models\Abk;
use App\Models\User;
use App\Models\Nilai;
use App\Models\NilaiAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbkController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $query  = Abk::withCount('nilais')->with(['guru', 'orangTua']);

        // Filter berdasarkan role
        if ($user->isGuru()) {
            $query->where('id_guru', $user->id);
        } elseif ($user->isOrangTua()) {
            $query->where('id_orang_tua', $user->id);
        }

        // Filter status (default: aktif)
        $statusFilter = $request->get('status', 'aktif');
        if ($statusFilter === 'semua') {
            // tampilkan semua
        } elseif ($statusFilter === 'nonaktif') {
            $query->where('status', 'nonaktif');
        } else {
            $query->where('status', 'aktif');
        }

        // Search & filter lainnya
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }
        if ($request->filled('keterangan')) {
            $query->where('keterangan', 'like', '%'.$request->keterangan.'%');
        }

        $abks = $query->latest()->paginate(10)->withQueryString();

        // Hitung jumlah per status untuk tab
        $baseQuery = Abk::query();
        if ($user->isGuru()) $baseQuery->where('id_guru', $user->id);
        if ($user->isOrangTua()) $baseQuery->where('id_orang_tua', $user->id);

        $totalAktif    = (clone $baseQuery)->where('status', 'aktif')->count();
        $totalNonaktif = (clone $baseQuery)->where('status', 'nonaktif')->count();
        $totalSemua    = $totalAktif + $totalNonaktif;

        // Data untuk dropdown Guru & Orang Tua di modal Tambah/Edit
        $gurus     = User::where('role', 'guru')->get();
        $orangTuas = User::where('role', 'orangtua')->get();

        return view('abk.index', compact(
            'abks', 'statusFilter',
            'totalAktif', 'totalNonaktif', 'totalSemua',
            'gurus', 'orangTuas'
        ));
    }

    public function create()
    {
        $gurus     = User::where('role', 'guru')->get();
        $orangTuas = User::where('role', 'orangtua')->get();
        return view('abk.create', compact('gurus', 'orangTuas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:255',
            'umur'         => 'required|integer|min:1|max:30',
            'kelas'        => 'required|string|max:255',
            'keterangan'   => 'nullable|string',
            'id_guru'      => 'nullable|exists:users,id',
            'id_orang_tua' => 'nullable|exists:users,id',
        ]);

        Abk::create(array_merge($request->all(), ['status' => 'aktif']));

        return redirect()->route('abk.index')
                         ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Abk $abk)
    {
        $abk->load(['guru', 'orangTua', 'nilais', 'nilaiAkademik', 'laporan']);

        $avgMotorik      = $abk->nilais->where('aspek', 'motorik')->avg('nilai') ?? 0;
        $avgKeterampilan = $abk->nilais->where('aspek', 'keterampilan')->avg('nilai') ?? 0;
        $avgSosial       = $abk->nilais->where('aspek', 'sosial')->avg('nilai') ?? 0;

        $grafikData = Nilai::where('abk_id', $abk->id)
            ->where('tanggal', '>=', now()->subDays(90))
            ->orderBy('tanggal')
            ->get()
            ->groupBy('aspek');

        $nilaiAkademik = $abk->nilaiAkademik
            ->sortByDesc('tanggal_input')
            ->groupBy('semester');

        $nilais = $abk->nilais->sortByDesc('tanggal');

        return view('abk.show', compact(
            'abk', 'nilais', 'nilaiAkademik',
            'avgMotorik', 'avgKeterampilan', 'avgSosial',
            'grafikData'
        ));
    }

    public function edit(Abk $abk)
    {
        $gurus     = User::where('role', 'guru')->get();
        $orangTuas = User::where('role', 'orangtua')->get();
        return view('abk.edit', compact('abk', 'gurus', 'orangTuas'));
    }

    public function update(Request $request, Abk $abk)
    {
        $request->validate([
            'nama'         => 'required|string|max:255',
            'umur'         => 'required|integer|min:1|max:30',
            'kelas'        => 'required|string|max:255',
            'keterangan'   => 'nullable|string',
            'id_guru'      => 'nullable|exists:users,id',
            'id_orang_tua' => 'nullable|exists:users,id',
        ]);

        $abk->update($request->all());

        return redirect()->route('abk.index')
                         ->with('success', 'Data siswa berhasil diperbarui.');
    }

    // Nonaktifkan siswa (menggantikan destroy)
    public function nonaktifkan(Abk $abk)
    {
        $abk->update(['status' => 'nonaktif']);

        return redirect()->route('abk.index')
                         ->with('success', $abk->nama.' berhasil dinonaktifkan.');
    }

    // Aktifkan kembali siswa
    public function aktifkan(Abk $abk)
    {
        $abk->update(['status' => 'aktif']);

        return redirect()->route('abk.index', ['status' => 'nonaktif'])
                         ->with('success', $abk->nama.' berhasil diaktifkan kembali.');
    }

    // Hapus permanen (hanya admin, opsional)
    public function destroy(Abk $abk)
    {
        $abk->delete();
        return redirect()->route('abk.index')
                         ->with('success', 'Data siswa berhasil dihapus.');
    }
}