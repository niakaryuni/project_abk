@extends('layouts.app')

@section('title', 'Edit Nilai')
@section('page-title', 'Edit Data Nilai')

@section('content')
<div style="max-width:700px">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-edit" style="color:var(--warning);margin-right:8px"></i>Edit Nilai: {{ $nilai->abk->nama ?? '' }}</h3>
            <a href="{{ route('nilai.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('nilai.update', $nilai) }}" method="POST">
                @csrf @method('PUT')

                <div class="form-group">
                    <label>Nama Siswa <span style="color:var(--danger)">*</span></label>
                    <select name="abk_id" class="form-control @error('abk_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($abks as $abk)
                            <option value="{{ $abk->id }}" {{ old('abk_id', $nilai->abk_id) == $abk->id ? 'selected' : '' }}>
                                {{ $abk->nama }} - Kelas {{ $abk->kelas ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                    @error('abk_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Mata Pelajaran <span style="color:var(--danger)">*</span></label>
                        <select name="mata_pelajaran" class="form-control @error('mata_pelajaran') is-invalid @enderror" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach(['Matematika','Bahasa Indonesia','IPA','IPS','Seni Budaya','Olahraga','Agama','PKn','Keterampilan Vokasional'] as $mp)
                                <option value="{{ $mp }}" {{ old('mata_pelajaran', $nilai->mata_pelajaran)==$mp ? 'selected' : '' }}>
                                    {{ $mp }}
                                </option>
                            @endforeach
                        </select>
                        @error('mata_pelajaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Aspek Penilaian <span style="color:var(--danger)">*</span></label>
                        <select name="aspek" class="form-control @error('aspek') is-invalid @enderror" required>
                            <option value="">-- Pilih Aspek --</option>
                            <option value="motorik"      {{ old('aspek',$nilai->aspek)=='motorik'      ? 'selected' : '' }}>🏃 Motorik</option>
                            <option value="keterampilan" {{ old('aspek',$nilai->aspek)=='keterampilan' ? 'selected' : '' }}>🤲 Keterampilan</option>
                            <option value="sosial"       {{ old('aspek',$nilai->aspek)=='sosial'       ? 'selected' : '' }}>🤝 Sosial</option>
                        </select>
                        @error('aspek')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nilai <span style="color:var(--text-muted);font-weight:400">(0–100)</span></label>
                        <input type="number" name="nilai"
                               class="form-control @error('nilai') is-invalid @enderror"
                               placeholder="Contoh: 80" min="0" max="100"
                               value="{{ old('nilai', $nilai->nilai) }}">
                        @error('nilai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Tanggal Penilaian <span style="color:var(--danger)">*</span></label>
                        <input type="date" name="tanggal"
                               class="form-control @error('tanggal') is-invalid @enderror"
                               value="{{ old('tanggal', $nilai->tanggal?->format('Y-m-d')) }}" required>
                        @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Catatan Perkembangan</label>
                    <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror"
                              placeholder="Tuliskan catatan perkembangan siswa...">{{ old('catatan', $nilai->catatan) }}</textarea>
                    @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div style="display:flex;gap:10px;margin-top:8px">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update Nilai
                    </button>
                    <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection