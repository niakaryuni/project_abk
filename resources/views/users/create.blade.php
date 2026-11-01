@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah Pengguna')

@section('content')
<div class="card" style="max-width:600px;margin:0 auto">
    <div class="card-header">
        <h3><i class="fas fa-user-plus" style="color:var(--primary);margin-right:8px"></i>Form Tambah Pengguna</h3>
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

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nama Lengkap <span style="color:var(--danger)">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Email <span style="color:var(--danger)">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       placeholder="contoh@email.com" value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Role <span style="color:var(--danger)">*</span></label>
                <select name="role" id="select-role" class="form-control @error('role') is-invalid @enderror"
                        required onchange="toggleAbk()">
                    <option value="">-- Pilih Role --</option>
                    <option value="admin"          {{ old('role')=='admin' ? 'selected':'' }}>Admin</option>
                    <option value="guru"           {{ old('role')=='guru' ? 'selected':'' }}>Guru</option>
                    <option value="kepala_sekolah" {{ old('role')=='kepala_sekolah' ? 'selected':'' }}>Kepala Sekolah</option>
                    <option value="orangtua"       {{ old('role')=='orangtua' ? 'selected':'' }}>Orang Tua</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Field khusus Orang Tua --}}
            <div id="section-abk" style="display:none">
                <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#15803d;font-weight:600">
                    👨‍👩‍👧 Pilih siswa ABK yang diasuh oleh orang tua ini
                </div>
                <div class="form-group">
                    <label>Siswa ABK yang Diasuh</label>
                    <select name="abk_id" class="form-control @error('abk_id') is-invalid @enderror">
                        <option value="">-- Pilih Siswa (opsional) --</option>
                        @foreach($abkList as $abk)
                            <option value="{{ $abk->id }}" {{ old('abk_id') == $abk->id ? 'selected':'' }}>
                                {{ $abk->nama }} — Kelas {{ $abk->kelas ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('abk_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Password <span style="color:var(--danger)">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Min. 6 karakter" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password <span style="color:var(--danger)">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control"
                           placeholder="Ulangi password" required>
                </div>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Pengguna
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