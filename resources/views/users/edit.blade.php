@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit Pengguna')

@section('content')
<div class="card" style="max-width:620px;margin:0 auto">
    <div class="card-header">
        <h3><i class="fas fa-user-edit" style="color:var(--primary);margin-right:8px"></i>Edit Pengguna</h3>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">

        @if($errors->any())
        <div style="background:#fde8e8;border:1px solid var(--danger);border-radius:8px;padding:12px 16px;margin-bottom:20px">
            <ul style="margin:0;padding-left:20px">
                @foreach($errors->all() as $error)
                    <li style="font-size:13px">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="alert-info" style="background:#eaf7ee;border:1px solid #34c77b;color:#1b7a4b;padding:12px 14px;border-radius:8px;margin-bottom:20px;font-size:13.5px">
            <i class="fas fa-user-circle" style="margin-right:6px"></i>
            Mengedit akun <strong>{{ $user->name }}</strong> &mdash; dibuat sejak {{ $user->created_at->isoFormat('D MMM Y') }}.
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama Lengkap <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Email <span style="color:var(--danger)">*</span></label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Role <span style="color:var(--danger)">*</span></label>
                <select name="role" id="select-role"
                        class="form-control @error('role') is-invalid @enderror"
                        required onchange="toggleAbk()">
                    <option value="admin"          {{ old('role',$user->role)=='admin' ? 'selected':'' }}>Admin</option>
                    <option value="guru"           {{ old('role',$user->role)=='guru' ? 'selected':'' }}>Guru</option>
                    <option value="kepala_sekolah" {{ old('role',$user->role)=='kepala_sekolah' ? 'selected':'' }}>Kepala Sekolah</option>
                    <option value="orangtua"       {{ old('role',$user->role)=='orangtua' ? 'selected':'' }}>Orang Tua</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Field khusus Orang Tua --}}
            <div id="section-abk" style="display:none">
                <div class="form-group">
                    <label>Siswa ABK yang Diasuh</label>
                    <select name="abk_id" class="form-control @error('abk_id') is-invalid @enderror">
                        <option value="">-- Tidak ada / Lepas relasi --</option>
                        @foreach($abkList as $abk)
                            <option value="{{ $abk->id }}"
                                {{ old('abk_id', $abkTerkait?->id) == $abk->id ? 'selected':'' }}>
                                {{ $abk->nama }} — Kelas {{ $abk->kelas ?? '-' }}
                                @if($abk->id_orang_tua && $abk->id_orang_tua != $user->id)
                                    (sudah ada ortu lain)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('abk_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Password Baru <span style="color:var(--text-muted);font-weight:400">(opsional, kosongkan jika tidak diubah)</span></label>
                <div class="form-row">
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Min. 6 karakter" style="margin-bottom:10px">
                    <input type="password" name="password_confirmation"
                           class="form-control" placeholder="Ulangi password baru">
                </div>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Perbarui Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleAbk() {
    const role = document.getElementById('select-role').value;
    document.getElementById('section-abk').style.display = role === 'orangtua' ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', toggleAbk);
</script>
@endsection