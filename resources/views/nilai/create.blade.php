@extends('layouts.app')

@section('title', 'Input Nilai Perkembangan')
@section('page-title', 'Input Nilai Perkembangan')

@section('content')
<div class="card" style="max-width:700px;margin:0 auto">
    <div class="card-header">
        <h3><i class="fas fa-star" style="color:var(--primary);margin-right:8px"></i>Input Nilai Perkembangan
            <span style="font-size:12px;font-weight:400;color:var(--text-muted);margin-left:8px">— Input setiap minggu</span>
        </h3>
        <a href="{{ route('nilai.index') }}" class="btn btn-sm btn-secondary">
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

        <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#15803d;font-weight:600">
            🌟 Penilaian Perkembangan — Motorik, Keterampilan, Sosial (input setiap minggu)
        </div>

        <form action="{{ route('nilai.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nama Siswa <span style="color:var(--danger)">*</span></label>
                <select name="abk_id" id="select-siswa" class="form-control @error('abk_id') is-invalid @enderror"
                        required onchange="loadProgramKhusus()">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($abks as $abk)
                        <option value="{{ $abk->id }}"
                                data-kebutuhan="{{ $abk->keterangan }}"
                                {{ old('abk_id') == $abk->id ? 'selected' : '' }}>
                            {{ $abk->nama }} — Kelas {{ $abk->kelas ?? '-' }} ({{ $abk->keterangan ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('abk_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div id="info-kebutuhan" style="background:#fef9e7;border:1px solid #f0c060;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;display:none"></div>

            <div class="form-group">
                <label>Program Khusus <span style="color:var(--danger)">*</span></label>
                <select name="mata_pelajaran_program" id="select-program" class="form-control @error('mata_pelajaran_program') is-invalid @enderror"
                        required onchange="autoAspek()">
                    <option value="">-- Pilih siswa terlebih dahulu --</option>
                </select>
                @error('mata_pelajaran_program')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <input type="hidden" name="aspek_program" id="input-aspek-program" value="{{ old('aspek_program') }}">

            <div id="aspek-info" style="display:none;background:#e8f5e9;border:1px solid #81c784;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;font-weight:600;color:#2e7d32">
                ✅ Aspek: <span id="aspek-label">-</span>
                <span style="font-weight:400;color:#555">(otomatis berdasarkan program)</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nilai (0-100) <span style="color:var(--danger)">*</span></label>
                    <input type="number" name="nilai" class="form-control @error('nilai') is-invalid @enderror"
                           placeholder="Contoh: 85" min="0" max="100"
                           value="{{ old('nilai') }}" required>
                    @error('nilai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Tanggal Penilaian <span style="color:var(--danger)">*</span></label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                           value="{{ old('tanggal', date('Y-m-d')) }}" required>
                    @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>Catatan <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
                <textarea name="catatan" class="form-control" rows="3"
                          placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end">
                <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perkembangan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const aspekOtomatis = {
    'Bina Gerak': 'motorik', 'Fisioterapi': 'motorik', 'Orientasi Mobilitas': 'motorik',
    'Sensori Integrasi': 'motorik', 'Bina Persepsi Bunyi': 'motorik',
    'Bina Komunikasi': 'keterampilan', 'Bina Komunikasi Wicara': 'keterampilan',
    'Braille': 'keterampilan', 'Bahasa Isyarat': 'keterampilan',
    'ADL': 'keterampilan', 'Keterampilan Vokasional': 'keterampilan',
    'Bina Sosial': 'sosial', 'Terapi Perilaku': 'sosial', 'Bina Diri': 'sosial',
};
const aspekLabelMap = { motorik: '🏃 Motorik', keterampilan: '🤲 Keterampilan', sosial: '🤝 Sosial' };
const programKhusus = {
    'Autis':       ['Bina Komunikasi - Komunikasi Verbal','Bina Komunikasi - Komunikasi Non Verbal','Bina Sosial - Interaksi Sosial','Bina Sosial - Bermain Bersama','Terapi Perilaku - ABA','Terapi Perilaku - Manajemen Emosi','Sensori Integrasi - Taktil','Sensori Integrasi - Vestibular'],
    'Tunagrahita': ['Bina Diri - Merawat Diri','Bina Diri - Mengurus Diri','Bina Diri - Makan dan Minum','Bina Sosial - Interaksi Sosial','Keterampilan Vokasional - Pertanian','Keterampilan Vokasional - Kerajinan Tangan','Keterampilan Vokasional - Memasak','Keterampilan Vokasional - Komputer'],
    'Tunarungu':   ['Bina Komunikasi Wicara - Artikulasi','Bina Komunikasi Wicara - Membaca Ujaran','Bina Persepsi Bunyi - Deteksi Bunyi','Bina Persepsi Bunyi - Diskriminasi Bunyi','Bahasa Isyarat - SIBI','Bahasa Isyarat - Bisindo'],
    'Tunanetra':   ['Orientasi Mobilitas - Teknik Tongkat','Orientasi Mobilitas - Mobilitas Mandiri','Braille - Membaca Braille','Braille - Menulis Braille','ADL - Aktivitas Sehari-hari','ADL - Merawat Diri'],
    'Tunadaksa':   ['Bina Gerak - Motorik Kasar','Bina Gerak - Motorik Halus','Bina Gerak - Koordinasi','Fisioterapi - Latihan Fisik','Fisioterapi - Mobilisasi Sendi','Bina Diri - Merawat Diri','Bina Diri - Mengurus Diri'],
};
const infoKebutuhan = {
    'Autis':       '🧩 Autis — Program: Bina Komunikasi, Bina Sosial, Terapi Perilaku, Sensori Integrasi',
    'Tunagrahita': '🧠 Tunagrahita — Program: Bina Diri, Bina Sosial, Keterampilan Vokasional',
    'Tunarungu':   '👂 Tunarungu — Program: Bina Komunikasi Wicara, Bina Persepsi Bunyi, Bahasa Isyarat',
    'Tunanetra':   '👁️ Tunanetra — Program: Orientasi Mobilitas, Braille, ADL',
    'Tunadaksa':   '♿ Tunadaksa — Program: Bina Gerak, Fisioterapi, Bina Diri',
};

function autoAspek() {
    const prog = document.getElementById('select-program').value;
    let aspek = 'keterampilan';
    for (const key in aspekOtomatis) {
        if (prog.toLowerCase().includes(key.toLowerCase())) { aspek = aspekOtomatis[key]; break; }
    }
    document.getElementById('input-aspek-program').value = aspek;
    document.getElementById('aspek-label').textContent = aspekLabelMap[aspek];
    document.getElementById('aspek-info').style.display = prog ? 'block' : 'none';
}

function loadProgramKhusus() {
    const sel      = document.getElementById('select-siswa');
    const kebutuhan = sel.options[sel.selectedIndex]?.getAttribute('data-kebutuhan') || '';
    const selectProg = document.getElementById('select-program');
    const infoBox  = document.getElementById('info-kebutuhan');
    selectProg.innerHTML = '<option value="">-- Pilih Program Khusus --</option>';
    document.getElementById('aspek-info').style.display = 'none';
    let list = [];
    for (const key in programKhusus) {
        if (kebutuhan.toLowerCase().includes(key.toLowerCase())) {
            list = programKhusus[key];
            infoBox.style.display = 'block';
            infoBox.innerHTML = '<i class="fas fa-info-circle"></i> ' + (infoKebutuhan[key] || '');
            break;
        }
    }
    if (!list.length) { infoBox.style.display = 'none'; return; }
    list.forEach(p => {
        const o = document.createElement('option');
        o.value = p; o.textContent = p;
        selectProg.appendChild(o);
    });
}
</script>
@endsection