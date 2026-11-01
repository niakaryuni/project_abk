@extends('layouts.app')

@section('title', 'Tambah Siswa ABK')
@section('page-title', 'Tambah Siswa ABK')

@section('content')
<div class="card" style="max-width:640px;margin:0 auto">
    <div class="card-header">
        <h3><i class="fas fa-user-plus" style="color:var(--primary);margin-right:8px"></i>Form Tambah Siswa ABK</h3>
        <a href="{{ route('abk.index') }}" class="btn btn-sm btn-secondary">
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

        <form action="{{ route('abk.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nama Lengkap Siswa <span style="color:var(--danger)">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                       placeholder="Masukkan nama lengkap siswa"
                       value="{{ old('nama') }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Umur <span style="color:var(--danger)">*</span></label>
                    <input type="number" name="umur" class="form-control @error('umur') is-invalid @enderror"
                           placeholder="Contoh: 9" min="1" max="30"
                           value="{{ old('umur') }}" required>
                    @error('umur')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Kelas <span style="color:var(--danger)">*</span></label>
                    <select name="kelas" class="form-control @error('kelas') is-invalid @enderror" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach(['1A','1B','2A','2B','3A','3B','4A','4B','5A','5B','6A','6B'] as $kls)
                            <option value="{{ $kls }}" {{ old('kelas')==$kls ? 'selected':'' }}>
                                Kelas {{ $kls }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Jenis Kebutuhan Khusus <span style="color:var(--danger)">*</span></label>
                <select name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" required>
                    <option value="">-- Pilih Jenis Kebutuhan Khusus --</option>
                    <option value="Tunanetra"   {{ old('keterangan')=='Tunanetra'   ? 'selected':'' }}>Tunanetra</option>
                    <option value="Tunarungu"   {{ old('keterangan')=='Tunarungu'   ? 'selected':'' }}>Tunarungu</option>
                    <option value="Tunagrahita" {{ old('keterangan')=='Tunagrahita' ? 'selected':'' }}>Tunagrahita</option>
                    <option value="Tunadaksa"   {{ old('keterangan')=='Tunadaksa'   ? 'selected':'' }}>Tunadaksa</option>
                    <option value="Autis"       {{ old('keterangan')=='Autis'       ? 'selected':'' }}>Autis</option>
                </select>
                @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Guru Pengampu</label>
                <select name="id_guru" class="form-control @error('id_guru') is-invalid @enderror">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($gurus as $guru)
                        <option value="{{ $guru->id }}" {{ old('id_guru')==$guru->id ? 'selected':'' }}>
                            {{ $guru->name }}
                        </option>
                    @endforeach
                </select>
                @error('id_guru')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Orang Tua / Wali</label>
                <select name="id_orang_tua" class="form-control @error('id_orang_tua') is-invalid @enderror">
                    <option value="">-- Pilih Orang Tua --</option>
                    @foreach($orangTuas as $ot)
                        <option value="{{ $ot->id }}" {{ old('id_orang_tua')==$ot->id ? 'selected':'' }}>
                            {{ $ot->name }}
                        </option>
                    @endforeach
                </select>
                @error('id_orang_tua')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                <a href="{{ route('abk.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Siswa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection