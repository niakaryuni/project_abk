<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Abk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Use Case Kelola User (Tabel 4.8): Primary Business Actor = Admin
    private function pastikanAdmin()
    {
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya Admin yang dapat mengelola pengguna.');
    }

    public function index(Request $request)
    {
        $this->pastikanAdmin();

        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter status (default: aktif) — sama seperti pola Data Siswa ABK
        $statusFilter = $request->get('status', 'aktif');
        if ($statusFilter === 'semua') {
            // tampilkan semua
        } elseif ($statusFilter === 'nonaktif') {
            $query->where('status', 'nonaktif');
        } else {
            $query->where('status', 'aktif');
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        $totalAdmin      = User::where('role', 'admin')->count();
        $totalGuru       = User::where('role', 'guru')->count();
        $totalKepsek     = User::where('role', 'kepala_sekolah')->count();
        $totalOrangTua   = User::where('role', 'orangtua')->count();

        $totalAktif    = User::where('status', 'aktif')->count();
        $totalNonaktif = User::where('status', 'nonaktif')->count();
        $totalSemua    = $totalAktif + $totalNonaktif;

        return view('users.index', compact(
            'users', 'totalAdmin', 'totalGuru', 'totalKepsek', 'totalOrangTua',
            'statusFilter', 'totalAktif', 'totalNonaktif', 'totalSemua'
        ));
    }

    public function create()
    {
        $this->pastikanAdmin();
        $abkList = Abk::orderBy('nama')->get(); // untuk assign orang tua ke siswa
        return view('users.create', compact('abkList'));
    }

    public function store(Request $request)
    {
        $this->pastikanAdmin();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,guru,kepala_sekolah,orangtua',
            'abk_id'   => 'nullable|exists:abks,id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'status'   => 'aktif',
        ]);

        // Jika role orang tua, assign ke siswa ABK
        if ($validated['role'] === 'orangtua' && !empty($validated['abk_id'])) {
            Abk::where('id', $validated['abk_id'])
               ->update(['id_orang_tua' => $user->id]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Akun '.$validated['name'].' berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $this->pastikanAdmin();
        $abkList   = Abk::orderBy('nama')->get();
        $abkTerkait = Abk::where('id_orang_tua', $user->id)->first();
        return view('users.edit', compact('user', 'abkList', 'abkTerkait'));
    }

    public function update(Request $request, User $user)
    {
        $this->pastikanAdmin();

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6|confirmed',
            'role'     => 'required|in:admin,guru,kepala_sekolah,orangtua',
            'abk_id'   => 'nullable|exists:abks,id',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
            ...(!empty($validated['password'])
                ? ['password' => Hash::make($validated['password'])]
                : []),
        ]);

        // Update relasi orang tua ke siswa
        if ($validated['role'] === 'orangtua') {
            // Lepas relasi lama
            Abk::where('id_orang_tua', $user->id)->update(['id_orang_tua' => null]);
            // Set relasi baru
            if (!empty($validated['abk_id'])) {
                Abk::where('id', $validated['abk_id'])
                   ->update(['id_orang_tua' => $user->id]);
            }
        }

        return redirect()->route('users.index')
            ->with('success', 'Akun '.$user->name.' berhasil diperbarui.');
    }

    // Non Aktif Pengguna (Gambar 4.8 Activity Diagram, Gambar 4.30 Sequence Diagram)
    // Menggantikan hapus permanen: akun dinonaktifkan sehingga tidak bisa login lagi,
    // namun data historis (nilai, laporan) yang pernah dibuat guru tsb tetap aman.
    public function nonaktifkan(User $user)
    {
        $this->pastikanAdmin();

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menonaktifkan akun yang sedang digunakan.');
        }

        $user->update(['status' => 'nonaktif']);

        return redirect()->route('users.index')
            ->with('success', 'Akun '.$user->name.' berhasil dinonaktifkan.');
    }

    // Aktifkan kembali akun pengguna
    public function aktifkan(User $user)
    {
        $this->pastikanAdmin();

        $user->update(['status' => 'aktif']);

        return redirect()->route('users.index', ['status' => 'nonaktif'])
            ->with('success', 'Akun '.$user->name.' berhasil diaktifkan kembali.');
    }

    // Hapus permanen (opsional, tetap tersedia untuk Admin bila benar-benar diperlukan)
    public function destroy(User $user)
    {
        $this->pastikanAdmin();

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
        }

        // Lepas relasi orang tua jika ada
        Abk::where('id_orang_tua', $user->id)->update(['id_orang_tua' => null]);

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Akun berhasil dihapus.');
    }
}