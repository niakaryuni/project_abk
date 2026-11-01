@extends('layouts.app')

@section('title', 'Buat Laporan')
@section('page-title', 'Buat Laporan Baru')

@section('content')
<div class="card" style="max-width:620px;margin:0 auto">
    <div class="card-header">
        <h3><i class="fas fa-file-plus" style="color:var(--primary);margin-right:8px"></i>Form Buat Laporan</h3>
        <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">

        @if($errors->any())
        <div style="background:#fde8e8;border:1px solid var(--danger);border-radius:8px;padding:12px 16px;margin-bottom:20px">
            <ul style="margin:0;padding-left:20px">
                @foreach($errors->all() as $e)
                    <li style="font-size:13px">{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div style="background:var(--primary-light);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:var(--primary)">
            <i class="fas fa-info-circle"></i>
            Laporan akan otomatis mengambil data nilai perkembangan dan akademik siswa sesuai semester dan tahun ajaran yang dipilih saat download PDF.
        </div>

        <form action="{{ route('laporan.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Siswa ABK <span style="color:var(--danger)">*</span></label>
                <select name="abk_id" class="form-control @error('abk_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($abks as $abk)
                        <option value="{{ $abk->id }}" {{ old('abk_id') == $abk->id ? 'selected':'' }}>
                            {{ $abk->nama }} — Kelas {{ $abk->kelas ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('abk_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- TAMBAHAN: dropdown Jenis Laporan --}}
            {{-- PENTING: value harus sesuai enum di database: 'perkembangan', 'akademik', 'gabungan' --}}
            <div class="form-group">
                <label>Jenis Laporan <span style="color:var(--danger)">*</span></label>
                <select name="jenis_laporan" class="form-control @error('jenis_laporan') is-invalid @enderror" required>
                    <option value="">-- Pilih Jenis Laporan --</option>
                    <option value="perkembangan" {{ old('jenis_laporan')=='perkembangan' ? 'selected':'' }}>
                        Perkembangan — hanya data non-akademik (motorik, keterampilan, sosial)
                    </option>
                    <option value="akademik" {{ old('jenis_laporan')=='akademik' ? 'selected':'' }}>
                        Akademik — hanya nilai akademik per semester
                    </option>
                    <option value="gabungan" {{ old('jenis_laporan', 'gabungan')=='gabungan' ? 'selected':'' }}>
                        Gabungan — perkembangan & akademik sekaligus
                    </option>
                </select>
                @error('jenis_laporan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Semester <span style="color:var(--danger)">*</span></label>
                    <select name="semester" class="form-control @error('semester') is-invalid @enderror" required>
                        <option value="">-- Pilih --</option>
                        <option value="1" {{ old('semester')=='1' ? 'selected':'' }}>Semester 1</option>
                        <option value="2" {{ old('semester')=='2' ? 'selected':'' }}>Semester 2</option>
                    </select>
                    @error('semester')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Tahun Ajaran <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="tahun_ajaran"
                           class="form-control @error('tahun_ajaran') is-invalid @enderror"
                           placeholder="Contoh: 2025/2026"
                           value="{{ old('tahun_ajaran') }}" required maxlength="10">
                    @error('tahun_ajaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Periode <span style="color:var(--danger)">*</span></label>
                <input type="text" name="periode"
                       class="form-control @error('periode') is-invalid @enderror"
                       placeholder="Contoh: Januari - Juni 2026"
                       value="{{ old('periode') }}" required>
                @error('periode')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>Catatan Umum <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
                <textarea name="ringkasan" class="form-control" rows="4"
                          placeholder="Tuliskan catatan umum perkembangan siswa selama periode ini...">{{ old('ringkasan') }}</textarea>
            </div>

            <div class="form-group">
                <label>Status <span style="color:var(--danger)">*</span></label>
                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="draft"  {{ old('status','draft')=='draft'  ? 'selected':'' }}>
                        Draft — belum dapat diakses orang tua
                    </option>
                    <option value="final"  {{ old('status')=='final' ? 'selected':'' }}>
                        Final — orang tua dapat melihat dan mengunduh
                    </option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end">
                <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection