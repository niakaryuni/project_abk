@extends('layouts.app')

@section('title', 'Nilai Perkembangan')
@section('page-title', 'Data Nilai Perkembangan Siswa')

@section('content')

@if($selectedAbk)
{{-- Rata-rata Perkembangan --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3><i class="fas fa-chart-bar" style="color:var(--primary);margin-right:8px"></i>Rata-rata Perkembangan — {{ $selectedAbk->nama }}</h3>
        <a href="{{ route('nilai.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-times"></i> Tutup Grafik
        </a>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px">
            <div style="text-align:center;padding:18px;background:var(--primary-light);border-radius:12px">
                <div style="font-size:36px;font-weight:800;color:var(--primary)">{{ number_format($avgMotorik,0) }}</div>
                <div style="font-size:12px;font-weight:600;color:var(--primary);margin-top:4px">🏃 Motorik</div>
            </div>
            <div style="text-align:center;padding:18px;background:#d5f5e3;border-radius:12px">
                <div style="font-size:36px;font-weight:800;color:var(--success)">{{ number_format($avgKeterampilan,0) }}</div>
                <div style="font-size:12px;font-weight:600;color:var(--success);margin-top:4px">🤲 Keterampilan</div>
            </div>
            <div style="text-align:center;padding:18px;background:#fde8d5;border-radius:12px">
                <div style="font-size:36px;font-weight:800;color:#b05a1a">{{ number_format($avgSosial,0) }}</div>
                <div style="font-size:12px;font-weight:600;color:#b05a1a;margin-top:4px">🤝 Sosial</div>
            </div>
        </div>
        <div style="margin-bottom:12px">
            <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                <span style="font-size:13px;font-weight:600">Motorik</span>
                <span style="font-size:13px;color:var(--primary);font-weight:700">{{ number_format($avgMotorik,1) }}/100</span>
            </div>
            <div class="progress"><div class="progress-bar blue" style="width:{{ $avgMotorik }}%"></div></div>
        </div>
        <div style="margin-bottom:12px">
            <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                <span style="font-size:13px;font-weight:600">Keterampilan</span>
                <span style="font-size:13px;color:var(--success);font-weight:700">{{ number_format($avgKeterampilan,1) }}/100</span>
            </div>
            <div class="progress"><div class="progress-bar green" style="width:{{ $avgKeterampilan }}%"></div></div>
        </div>
        <div>
            <div style="display:flex;justify-content:space-between;margin-bottom:4px">
                <span style="font-size:13px;font-weight:600">Sosial</span>
                <span style="font-size:13px;color:var(--accent-dark);font-weight:700">{{ number_format($avgSosial,1) }}/100</span>
            </div>
            <div class="progress"><div class="progress-bar orange" style="width:{{ $avgSosial }}%"></div></div>
        </div>
    </div>
</div>

{{-- Grafik Perkembangan --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3><i class="fas fa-chart-line" style="color:var(--primary);margin-right:8px"></i>Grafik Perkembangan (90 Hari Terakhir) — {{ $selectedAbk->nama }}</h3>
    </div>
    <div class="card-body">
        <canvas id="grafikPerkembangan" height="100"></canvas>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-star" style="color:var(--primary);margin-right:8px"></i>Daftar Penilaian Perkembangan</h3>
        @if(auth()->user()->isGuru())
        <button type="button" class="btn btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Tambah Data
        </button>
        @endif
    </div>

    {{-- Search & Filter --}}
    <div style="padding:14px 20px;border-bottom:1px solid var(--border)">
        <form method="GET" action="{{ route('nilai.index') }}" style="display:flex;gap:10px;flex-wrap:wrap">
            <input type="text" name="search" class="form-control" style="width:200px"
                   placeholder="Cari nama siswa..." value="{{ request('search') }}">
            <select name="aspek" class="form-control" style="width:150px">
                <option value="">Semua Aspek</option>
                <option value="motorik"      {{ request('aspek')=='motorik'      ? 'selected':'' }}>Motorik</option>
                <option value="keterampilan" {{ request('aspek')=='keterampilan' ? 'selected':'' }}>Keterampilan</option>
                <option value="sosial"       {{ request('aspek')=='sosial'       ? 'selected':'' }}>Sosial</option>
            </select>
            <input type="date" name="dari" class="form-control" style="width:150px"
                   value="{{ request('dari') }}" title="Tanggal">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
            <a href="{{ route('nilai.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Siswa</th>
                    <th>Mata Pelajaran / Program</th>
                    <th>Aspek</th>
                    <th>Nilai</th>
                    <th>Catatan</th>
                    <th>Tanggal</th>
                    <th style="width:140px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nilais as $n)
                <tr>
                    <td style="color:var(--text-muted)">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:600">{{ $n->abk->nama ?? '-' }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">Kelas {{ $n->abk->kelas ?? '-' }}</div>
                    </td>
                    <td style="font-size:13px">{{ $n->mata_pelajaran }}</td>
                    <td><span class="badge badge-{{ $n->aspek }}">{{ ucfirst($n->aspek) }}</span></td>
                    <td>
                        <span style="font-size:18px;font-weight:800;color:{{ $n->nilai >= 75 ? 'var(--success)' : ($n->nilai >= 60 ? 'var(--warning)' : 'var(--danger)') }}">
                            {{ $n->nilai ?? '-' }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:var(--text-muted);max-width:180px">
                        {{ $n->catatan ? Str::limit($n->catatan, 45) : '-' }}
                    </td>
                    <td style="font-size:13px;color:var(--text-muted)">
                        {{ \Carbon\Carbon::parse($n->tanggal)->isoFormat('D MMM Y') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <button type="button" class="btn btn-sm btn-primary" title="Detail"
                                    onclick="openRow({{ $n->id }}, 'view')">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if(auth()->user()->isGuru())
                            <button type="button" class="btn btn-sm btn-warning" title="Edit"
                                    onclick="openRow({{ $n->id }}, 'edit')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                    onclick="deleteRow({{ $n->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="fas fa-inbox" style="font-size:36px;margin-bottom:12px;display:block;opacity:0.4"></i>
                        Belum ada data penilaian.
                        @if(auth()->user()->isGuru())
                        <br>
                        <button type="button" class="btn btn-primary" style="margin-top:12px" onclick="openAddModal()">
                            <i class="fas fa-plus"></i> Input Sekarang
                        </button>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($nilais->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--border)">
        {{ $nilais->links() }}
    </div>
    @endif
</div>

{{-- ============ MODAL: TAMBAH DATA ============ --}}
<div class="modal-overlay" id="modal-tambah">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3><i class="fas fa-plus-circle" style="color:var(--primary);margin-right:8px"></i>Tambah Nilai Perkembangan</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modal-tambah')">&times;</button>
        </div>
        <div class="modal-box-body">

            @if($errors->any() && old('_form') === 'tambah')
            <div style="background:#fde8e8;border:1px solid var(--danger);border-radius:8px;padding:12px 16px;margin-bottom:16px">
                <ul style="margin:0;padding-left:20px">
                    @foreach($errors->all() as $error)
                        <li style="font-size:13px">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#15803d;font-weight:600">
                🌟 Penilaian Perkembangan — Motorik, Keterampilan, Sosial (input setiap minggu)
            </div>

            <form action="{{ route('nilai.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_form" value="tambah">

                <div class="form-group">
                    <label>Nama Siswa <span style="color:var(--danger)">*</span></label>
                    <select name="abk_id" id="select-siswa-add" class="form-control" required onchange="loadProgramKhusus('add')">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($abks as $abk)
                            <option value="{{ $abk->id }}" data-kebutuhan="{{ $abk->keterangan }}">
                                {{ $abk->nama }} — Kelas {{ $abk->kelas ?? '-' }} ({{ $abk->keterangan ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="info-kebutuhan-add" style="background:#fef9e7;border:1px solid #f0c060;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;display:none"></div>

                <div class="form-group">
                    <label>Program Khusus <span style="color:var(--danger)">*</span></label>
                    <select name="mata_pelajaran_program" id="select-program-add" class="form-control" required onchange="autoAspek('add')">
                        <option value="">-- Pilih siswa terlebih dahulu --</option>
                    </select>
                </div>

                <input type="hidden" name="aspek_program" id="input-aspek-program-add" value="">

                <div id="aspek-info-add" style="display:none;background:#e8f5e9;border:1px solid #81c784;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;font-weight:600;color:#2e7d32">
                    ✅ Aspek: <span id="aspek-label-add">-</span>
                    <span style="font-weight:400;color:#555">(otomatis berdasarkan program)</span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nilai (0-100) <span style="color:var(--danger)">*</span></label>
                        <input type="number" name="nilai" class="form-control" placeholder="Contoh: 85" min="0" max="100" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Penilaian <span style="color:var(--danger)">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Catatan <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal-tambah')">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perkembangan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============ MODAL: DETAIL / EDIT ============ --}}
<div class="modal-overlay" id="modal-detail">
    <div class="modal-box">

        {{-- ---- View mode ---- --}}
        <div id="detail-view">
            <div class="modal-box-header">
                <h3><i class="fas fa-star" style="color:var(--primary);margin-right:8px"></i>Detail Nilai Perkembangan</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modal-detail')">&times;</button>
            </div>
            <div class="modal-box-body">

                @if(auth()->user()->isGuru())
                <div style="display:flex;gap:10px;margin-bottom:18px">
                    <button type="button" class="btn btn-warning" onclick="showEditMode()">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-danger" onclick="confirmDeleteFromModal()">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
                @endif

                <div class="detail-row"><div class="label">Nama Siswa</div><div id="d-nama">-</div></div>
                <div class="detail-row"><div class="label">Mata Pelajaran/Program</div><div id="d-program">-</div></div>
                <div class="detail-row"><div class="label">Aspek</div><div id="d-aspek">-</div></div>
                <div class="detail-row"><div class="label">Nilai</div><div id="d-nilai">-</div></div>
                <div class="detail-row"><div class="label">Catatan</div><div id="d-catatan">-</div></div>
                <div class="detail-row"><div class="label">Tanggal</div><div id="d-tanggal">-</div></div>
            </div>
        </div>

        {{-- ---- Edit mode ---- --}}
        <div id="detail-edit" style="display:none">
            <div class="modal-box-header">
                <h3><i class="fas fa-edit" style="color:var(--warning);margin-right:8px"></i>Edit Nilai Perkembangan</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modal-detail')">&times;</button>
            </div>
            <div class="modal-box-body">

                <form id="edit-form" action="" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama Siswa <span style="color:var(--danger)">*</span></label>
                        <select name="abk_id" id="select-siswa-edit" class="form-control" required onchange="loadProgramKhusus('edit')">
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($abks as $abk)
                                <option value="{{ $abk->id }}" data-kebutuhan="{{ $abk->keterangan }}">
                                    {{ $abk->nama }} — Kelas {{ $abk->kelas ?? '-' }} ({{ $abk->keterangan ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="info-kebutuhan-edit" style="background:#fef9e7;border:1px solid #f0c060;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;display:none"></div>

                    <div class="form-group">
                        <label>Program Khusus <span style="color:var(--danger)">*</span></label>
                        <select name="mata_pelajaran" id="select-program-edit" class="form-control" required onchange="autoAspek('edit')">
                            <option value="">-- Pilih Program Khusus --</option>
                        </select>
                    </div>

                    <input type="hidden" name="aspek" id="input-aspek-program-edit" value="">

                    <div id="aspek-info-edit" style="display:none;background:#e8f5e9;border:1px solid #81c784;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:13px;font-weight:600;color:#2e7d32">
                        ✅ Aspek: <span id="aspek-label-edit">-</span>
                        <span style="font-weight:400;color:#555">(otomatis berdasarkan program)</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nilai (0-100) <span style="color:var(--danger)">*</span></label>
                            <input type="number" name="nilai" id="nilai-edit" class="form-control" min="0" max="100" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Penilaian <span style="color:var(--danger)">*</span></label>
                            <input type="date" name="tanggal" id="tanggal-edit" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Catatan <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
                        <textarea name="catatan" id="catatan-edit" class="form-control" rows="3"></textarea>
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                        <button type="button" class="btn btn-secondary" onclick="showViewMode()">Batal</button>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Nilai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Hidden delete form, action set dynamically per row --}}
<form id="delete-form" action="" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

<style>
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(13,33,55,0.55);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal-overlay.active { display: flex; }
.modal-box {
    background: #fff;
    border-radius: 12px;
    max-width: 640px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 12px 40px rgba(0,0,0,0.25);
}
.modal-box-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    background: #fff;
}
.modal-box-header h3 { font-size: 16px; font-weight: 700; margin: 0; }
.modal-close-btn {
    background: none;
    border: none;
    font-size: 22px;
    color: var(--text-muted);
    cursor: pointer;
    line-height: 1;
    padding: 0 4px;
}
.modal-close-btn:hover { color: var(--danger); }
.modal-box-body { padding: 24px; }

.detail-row { display:flex; padding:10px 0; border-bottom:1px solid var(--border); font-size:14px }
.detail-row .label { width:190px; flex-shrink:0; color:var(--text-muted); font-weight:600 }
</style>

<script>
// All row data, looked up by id — avoids ever embedding JSON inside an
// HTML attribute (which previously could break if a "catatan" contained
// a quote character, making the Detail button silently do nothing).
@php
    $nilaiRowsForJs = $nilais->getCollection()->map(function ($n) {
        return [
            'id'             => $n->id,
            'abk_id'         => $n->abk_id,
            'nama'           => $n->abk->nama ?? '-',
            'kelas'          => $n->abk->kelas ?? '-',
            'mata_pelajaran' => $n->mata_pelajaran,
            'aspek'          => $n->aspek,
            'nilai'          => $n->nilai,
            'catatan'        => $n->catatan,
            'tanggal'        => optional($n->tanggal)->format('Y-m-d'),
            'tanggal_label'  => \Carbon\Carbon::parse($n->tanggal)->isoFormat('D MMM Y'),
            'update_url'     => route('nilai.update', $n->id),
            'destroy_url'    => route('nilai.destroy', $n->id),
        ];
    })->values();
@endphp
window.nilaiRows = @json($nilaiRowsForJs);

let currentRowId = null;

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

function autoAspek(suffix) {
    const prog = document.getElementById('select-program-' + suffix).value;
    let aspek = 'keterampilan';
    for (const key in aspekOtomatis) {
        if (prog.toLowerCase().includes(key.toLowerCase())) { aspek = aspekOtomatis[key]; break; }
    }
    document.getElementById('input-aspek-program-' + suffix).value = aspek;
    document.getElementById('aspek-label-' + suffix).textContent = aspekLabelMap[aspek];
    document.getElementById('aspek-info-' + suffix).style.display = prog ? 'block' : 'none';
}

function loadProgramKhusus(suffix) {
    const sel        = document.getElementById('select-siswa-' + suffix);
    const kebutuhan  = sel.options[sel.selectedIndex]?.getAttribute('data-kebutuhan') || '';
    const selectProg = document.getElementById('select-program-' + suffix);
    const infoBox    = document.getElementById('info-kebutuhan-' + suffix);
    selectProg.innerHTML = '<option value="">-- Pilih Program Khusus --</option>';
    document.getElementById('aspek-info-' + suffix).style.display = 'none';
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

function openAddModal() {
    document.getElementById('modal-tambah').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
}

function showViewMode() {
    document.getElementById('detail-view').style.display = 'block';
    document.getElementById('detail-edit').style.display = 'none';
}

function showEditMode() {
    document.getElementById('detail-view').style.display = 'none';
    document.getElementById('detail-edit').style.display = 'block';
}

function findRow(id) {
    return window.nilaiRows.find(r => r.id === id);
}

function fillViewFields(d) {
    document.getElementById('d-nama').textContent     = d.nama + ' — Kelas ' + d.kelas;
    document.getElementById('d-program').textContent  = d.mata_pelajaran || '-';
    document.getElementById('d-aspek').textContent    = aspekLabelMap[d.aspek] || d.aspek || '-';
    document.getElementById('d-nilai').textContent    = d.nilai ?? '-';
    document.getElementById('d-catatan').textContent  = d.catatan || '-';
    document.getElementById('d-tanggal').textContent  = d.tanggal_label || d.tanggal || '-';
}

function fillEditForm(d) {
    document.getElementById('edit-form').action = d.update_url;

    document.getElementById('select-siswa-edit').value = d.abk_id;
    loadProgramKhusus('edit');

    const selProg = document.getElementById('select-program-edit');
    let found = false;
    for (const opt of selProg.options) {
        if (opt.value === d.mata_pelajaran) { found = true; break; }
    }
    if (!found && d.mata_pelajaran) {
        const o = document.createElement('option');
        o.value = d.mata_pelajaran;
        o.textContent = d.mata_pelajaran;
        selProg.appendChild(o);
    }
    selProg.value = d.mata_pelajaran || '';
    autoAspek('edit');
    if (d.aspek) {
        document.getElementById('input-aspek-program-edit').value = d.aspek;
        document.getElementById('aspek-label-edit').textContent = aspekLabelMap[d.aspek] || d.aspek;
        document.getElementById('aspek-info-edit').style.display = 'block';
    }

    document.getElementById('nilai-edit').value   = d.nilai ?? '';
    document.getElementById('tanggal-edit').value = d.tanggal ?? '';
    document.getElementById('catatan-edit').value = d.catatan ?? '';
}

function openRow(id, mode) {
    const d = findRow(id);
    if (!d) { return; }
    currentRowId = id;

    fillViewFields(d);
    fillEditForm(d);

    if (mode === 'edit') {
        showEditMode();
    } else {
        showViewMode();
    }
    document.getElementById('modal-detail').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function deleteByUrl(url) {
    if (confirm('Hapus data penilaian ini? Tindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('delete-form').action = url;
        document.getElementById('delete-form').submit();
    }
}

function deleteRow(id) {
    const d = findRow(id);
    if (!d) { return; }
    deleteByUrl(d.destroy_url);
}

function confirmDeleteFromModal() {
    const d = findRow(currentRowId);
    if (!d) { return; }
    deleteByUrl(d.destroy_url);
}

// Close modal when clicking the dark overlay (outside the box)
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal(overlay.id);
    });
});

// Tombol Esc menutup modal yang aktif
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(function (m) {
            closeModal(m.id);
        });
    }
});

// Re-open Tambah modal automatically if validation failed on that form
@if($errors->any() && old('_form') === 'tambah')
document.addEventListener('DOMContentLoaded', () => openAddModal());
@endif
</script>

@if($selectedAbk)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const grafikData = @json($grafikData);

const semuaTanggal = [...new Set(
    Object.values(grafikData).flatMap(arr => arr.map(d => d.tanggal))
)].sort();

function nilaiPerTanggal(aspek, tanggal) {
    const arr = grafikData[aspek] || [];
    const found = arr.find(d => d.tanggal === tanggal);
    return found ? found.nilai : null;
}

const ctxGrafikPerkembangan = document.getElementById('grafikPerkembangan').getContext('2d');
new Chart(ctxGrafikPerkembangan, {
    type: 'line',
    data: {
        labels: semuaTanggal.map(t => {
            const d = new Date(t);
            return d.toLocaleDateString('id-ID', { day:'numeric', month:'short' });
        }),
        datasets: [
            {
                label: 'Motorik',
                data: semuaTanggal.map(t => nilaiPerTanggal('motorik', t)),
                borderColor: '#1E6F9F',
                backgroundColor: 'rgba(30,111,159,0.08)',
                tension: 0.4, fill: true,
                pointBackgroundColor: '#1E6F9F',
                pointRadius: 4, spanGaps: true,
            },
            {
                label: 'Keterampilan',
                data: semuaTanggal.map(t => nilaiPerTanggal('keterampilan', t)),
                borderColor: '#27AE60',
                backgroundColor: 'rgba(39,174,96,0.08)',
                tension: 0.4, fill: true,
                pointBackgroundColor: '#27AE60',
                pointRadius: 4, spanGaps: true,
            },
            {
                label: 'Sosial',
                data: semuaTanggal.map(t => nilaiPerTanggal('sosial', t)),
                borderColor: '#F4A83A',
                backgroundColor: 'rgba(244,168,58,0.08)',
                tension: 0.4, fill: true,
                pointBackgroundColor: '#F4A83A',
                pointRadius: 4, spanGaps: true,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': ' + (ctx.raw ?? '-')
                }
            }
        },
        scales: {
            y: {
                min: 0, max: 100,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: { stepSize: 20 }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
@endif
@endsection