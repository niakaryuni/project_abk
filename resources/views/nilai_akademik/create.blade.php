@extends('layouts.app')

@section('title', 'Input Nilai Akademik')
@section('page-title', 'Input Nilai Akademik')

@section('content')
<div class="card" style="max-width:700px;margin:0 auto">
    <div class="card-header">
        <h3><i class="fas fa-graduation-cap" style="color:var(--primary);margin-right:8px"></i>Form Input Nilai Akademik</h3>
        <a href="{{ route('nilai-akademik.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;color:#dc2626">
            <ul style="margin:0;padding-left:16px">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('nilai-akademik.store') }}" method="POST">
            @csrf

            <div style="margin-bottom:16px">
                <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">Siswa ABK <span style="color:var(--danger)">*</span></label>
                <select name="abk_id" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswaDaftar as $s)
                        <option value="{{ $s->id }}" {{ old('abk_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nama }} — Kelas {{ $s->kelas ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px">
                <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">Mata Pelajaran <span style="color:var(--danger)">*</span></label>
                <select name="mata_pelajaran" class="form-control" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($mataDaftar as $mp)
                        <option value="{{ $mp }}" {{ old('mata_pelajaran') == $mp ? 'selected' : '' }}>{{ $mp }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div>
                    <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">Semester <span style="color:var(--danger)">*</span></label>
                    <select name="semester" class="form-control" required>
                        <option value="">-- Pilih Semester --</option>
                        <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                        <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">Tahun Ajaran <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="tahun_ajaran" class="form-control" placeholder="Contoh: 2025/2026"
                           value="{{ old('tahun_ajaran') }}" required maxlength="10">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div>
                    <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">Nilai Akhir (0-100) <span style="color:var(--danger)">*</span></label>
                    <input type="number" name="nilai_akhir" class="form-control" placeholder="0 - 100"
                           value="{{ old('nilai_akhir') }}" min="0" max="100" step="0.01" required
                           oninput="updatePredikat(this.value)">
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">Predikat</label>
                    <div id="predikat-display" style="height:42px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:22px;font-weight:800;background:var(--bg-secondary);color:var(--text-muted)">
                        —
                    </div>
                </div>
            </div>

            <div style="margin-bottom:16px">
                <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">Tanggal Input <span style="color:var(--danger)">*</span></label>
                <input type="date" name="tanggal_input" class="form-control"
                       value="{{ old('tanggal_input', date('Y-m-d')) }}" required>
            </div>

            <div style="margin-bottom:24px">
                <label style="font-size:13px;font-weight:600;margin-bottom:6px;display:block">Catatan <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
                <textarea name="catatan" class="form-control" rows="3"
                          placeholder="Catatan tambahan mengenai perkembangan akademik siswa...">{{ old('catatan') }}</textarea>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end">
                <a href="{{ route('nilai-akademik.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updatePredikat(nilai) {
    const el = document.getElementById('predikat-display');
    nilai = parseFloat(nilai);
    if (isNaN(nilai)) {
        el.textContent = '—';
        el.style.color = 'var(--text-muted)';
        el.style.background = 'var(--bg-secondary)';
        return;
    }
    let predikat, color, bg;
    if (nilai >= 90)      { predikat = 'A'; color = 'var(--success)'; bg = '#f0fdf4'; }
    else if (nilai >= 75) { predikat = 'B'; color = 'var(--primary)'; bg = '#eff6ff'; }
    else if (nilai >= 60) { predikat = 'C'; color = 'var(--warning)'; bg = '#fffbeb'; }
    else                  { predikat = 'D'; color = 'var(--danger)';  bg = '#fef2f2'; }
    el.textContent = predikat;
    el.style.color = color;
    el.style.background = bg;
}
</script>
@endsection