@extends('layouts.app')

@section('title', 'Nilai Akademik')
@section('page-title', 'Data Nilai Akademik Siswa')

@section('content')

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-graduation-cap" style="color:var(--primary);margin-right:8px"></i>Daftar Nilai Akademik</h3>
        @if(auth()->user()->isGuru())
        <button type="button" class="btn btn-primary" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Input Nilai Akademik
        </button>
        @endif
    </div>

    {{-- Filter --}}
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;gap:10px;flex-wrap:wrap">
        <form method="GET" action="{{ route('nilai-akademik.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;width:100%">
            <select name="abk_id" class="form-control" style="width:180px">
                <option value="">Semua Siswa</option>
                @foreach($siswaDaftar as $s)
                    <option value="{{ $s->id }}" {{ request('abk_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->nama }}
                    </option>
                @endforeach
            </select>
            <select name="mata_pelajaran" class="form-control" style="width:180px">
                <option value="">Semua Mata Pelajaran</option>
                @foreach($mataDaftar as $mp)
                    <option value="{{ $mp }}" {{ request('mata_pelajaran') == $mp ? 'selected' : '' }}>{{ $mp }}</option>
                @endforeach
            </select>
            <select name="semester" class="form-control" style="width:130px">
                <option value="">Semua Semester</option>
                <option value="1" {{ request('semester') == '1' ? 'selected' : '' }}>Semester 1</option>
                <option value="2" {{ request('semester') == '2' ? 'selected' : '' }}>Semester 2</option>
            </select>
            <select name="tahun_ajaran" class="form-control" style="width:140px">
                <option value="">Semua Tahun</option>
                @foreach($tahunDaftar as $ta)
                    <option value="{{ $ta }}" {{ request('tahun_ajaran') == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
            <a href="{{ route('nilai-akademik.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Siswa</th>
                    <th>Mata Pelajaran</th>
                    <th>Nilai Akhir</th>
                    <th>Predikat</th>
                    <th>Semester</th>
                    <th>Tahun Ajaran</th>
                    <th>Tanggal Input</th>
                    @if(auth()->user()->isGuru())
                    <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($nilaiAkademik as $n)
                <tr>
                    <td style="color:var(--text-muted)">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:600">{{ $n->abk->nama ?? '-' }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">Kelas {{ $n->abk->kelas ?? '-' }}</div>
                    </td>
                    <td>{{ $n->mata_pelajaran }}</td>
                    <td>
                        <span style="font-size:18px;font-weight:800;color:{{ $n->nilai_akhir >= 75 ? 'var(--success)' : ($n->nilai_akhir >= 60 ? 'var(--warning)' : 'var(--danger)') }}">
                            {{ $n->nilai_akhir }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:700;font-size:16px;color:{{ $n->predikat=='A' ? 'var(--success)' : ($n->predikat=='B' ? 'var(--primary)' : ($n->predikat=='C' ? 'var(--warning)' : 'var(--danger)')) }}">
                            {{ $n->predikat ?? '-' }}
                        </span>
                    </td>
                    <td>Semester {{ $n->semester }}</td>
                    <td style="color:var(--text-muted)">{{ $n->tahun_ajaran }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">
                        {{ \Carbon\Carbon::parse($n->tanggal_input)->isoFormat('D MMM Y') }}
                    </td>
                    @if(auth()->user()->isGuru())
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <button type="button" class="btn btn-sm btn-primary" title="Detail"
                                    onclick="openRow({{ $n->id }}, 'view')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" title="Edit"
                                    onclick="openRow({{ $n->id }}, 'edit')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" title="Hapus"
                                    onclick="deleteRow({{ $n->id }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->isGuru() ? 9 : 8 }}"
                        style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="fas fa-inbox" style="font-size:36px;margin-bottom:12px;display:block;opacity:0.4"></i>
                        Belum ada data nilai akademik.
                        @if(auth()->user()->isGuru())
                        <br>
                        <button type="button" class="btn btn-primary" style="margin-top:12px" onclick="openAddModal()">
                            <i class="fas fa-plus"></i> Input Nilai Pertama
                        </button>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($nilaiAkademik->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--border)">
        {{ $nilaiAkademik->links() }}
    </div>
    @endif
</div>

{{-- ============ MODAL: TAMBAH DATA ============ --}}
<div class="modal-overlay" id="modal-tambah">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3><i class="fas fa-plus-circle" style="color:var(--primary);margin-right:8px"></i>Tambah Nilai Akademik</h3>
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

            <div style="background:#eff6ff;border:1px solid #93c5fd;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#1d4ed8;font-weight:600">
                🎓 Penilaian Akademik — per Mata Pelajaran, per Semester
            </div>

            <form action="{{ route('nilai-akademik.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_form" value="tambah">

                <div class="form-group">
                    <label>Nama Siswa <span style="color:var(--danger)">*</span></label>
                    <select name="abk_id" class="form-control" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($siswaDaftar as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }} — Kelas {{ $s->kelas ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Mata Pelajaran <span style="color:var(--danger)">*</span></label>
                    <select name="mata_pelajaran" class="form-control" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mataDaftar as $mp)
                            <option value="{{ $mp }}">{{ $mp }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Semester <span style="color:var(--danger)">*</span></label>
                        <select name="semester" class="form-control" required>
                            <option value="">-- Pilih Semester --</option>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahun Ajaran <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="tahun_ajaran" class="form-control" placeholder="Contoh: 2025/2026" maxlength="10" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nilai Akhir (0-100) <span style="color:var(--danger)">*</span></label>
                        <input type="number" name="nilai_akhir" class="form-control" placeholder="Contoh: 85"
                               min="0" max="100" step="0.01" required
                               oninput="updatePredikat(this.value, 'add')">
                    </div>
                    <div class="form-group">
                        <label>Predikat</label>
                        <div id="predikat-display-add" class="predikat-box">—</div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tanggal Input <span style="color:var(--danger)">*</span></label>
                    <input type="date" name="tanggal_input" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label>Catatan <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan mengenai perkembangan akademik siswa..."></textarea>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal-tambah')">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Nilai</button>
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
                <h3><i class="fas fa-graduation-cap" style="color:var(--primary);margin-right:8px"></i>Detail Nilai Akademik</h3>
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
                <div class="detail-row"><div class="label">Mata Pelajaran</div><div id="d-mapel">-</div></div>
                <div class="detail-row"><div class="label">Nilai Akhir</div><div id="d-nilai">-</div></div>
                <div class="detail-row"><div class="label">Predikat</div><div id="d-predikat">-</div></div>
                <div class="detail-row"><div class="label">Semester</div><div id="d-semester">-</div></div>
                <div class="detail-row"><div class="label">Tahun Ajaran</div><div id="d-tahun">-</div></div>
                <div class="detail-row"><div class="label">Catatan</div><div id="d-catatan">-</div></div>
                <div class="detail-row"><div class="label">Tanggal Input</div><div id="d-tanggal">-</div></div>
            </div>
        </div>

        {{-- ---- Edit mode ---- --}}
        <div id="detail-edit" style="display:none">
            <div class="modal-box-header">
                <h3><i class="fas fa-edit" style="color:var(--warning);margin-right:8px"></i>Edit Nilai Akademik</h3>
                <button type="button" class="modal-close-btn" onclick="closeModal('modal-detail')">&times;</button>
            </div>
            <div class="modal-box-body">

                <form id="edit-form" action="" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama Siswa <span style="color:var(--danger)">*</span></label>
                        <select name="abk_id" id="select-siswa-edit" class="form-control" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($siswaDaftar as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }} — Kelas {{ $s->kelas ?? '-' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Mata Pelajaran <span style="color:var(--danger)">*</span></label>
                        <select name="mata_pelajaran" id="select-mapel-edit" class="form-control" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mataDaftar as $mp)
                                <option value="{{ $mp }}">{{ $mp }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Semester <span style="color:var(--danger)">*</span></label>
                            <select name="semester" id="select-semester-edit" class="form-control" required>
                                <option value="">-- Pilih Semester --</option>
                                <option value="1">Semester 1</option>
                                <option value="2">Semester 2</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tahun Ajaran <span style="color:var(--danger)">*</span></label>
                            <input type="text" name="tahun_ajaran" id="tahun-edit" class="form-control" maxlength="10" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nilai Akhir (0-100) <span style="color:var(--danger)">*</span></label>
                            <input type="number" name="nilai_akhir" id="nilai-edit" class="form-control"
                                   min="0" max="100" step="0.01" required
                                   oninput="updatePredikat(this.value, 'edit')">
                        </div>
                        <div class="form-group">
                            <label>Predikat</label>
                            <div id="predikat-display-edit" class="predikat-box">—</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Input <span style="color:var(--danger)">*</span></label>
                        <input type="date" name="tanggal_input" id="tanggal-edit" class="form-control" required>
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

.predikat-box {
    height:42px;display:flex;align-items:center;justify-content:center;
    border-radius:8px;font-size:22px;font-weight:800;
    background:var(--bg-secondary);color:var(--text-muted);
}
</style>

<script>
// Lookup data per baris, dipakai untuk Detail/Edit modal — hindari embed JSON
// di atribut HTML supaya aman dari karakter kutip di "catatan".
@php
    $akademikRowsForJs = $nilaiAkademik->getCollection()->map(function ($n) {
        return [
            'id'             => $n->id,
            'abk_id'         => $n->abk_id,
            'nama'           => $n->abk->nama ?? '-',
            'kelas'          => $n->abk->kelas ?? '-',
            'mata_pelajaran' => $n->mata_pelajaran,
            'nilai_akhir'    => $n->nilai_akhir,
            'predikat'       => $n->predikat,
            'semester'       => $n->semester,
            'tahun_ajaran'   => $n->tahun_ajaran,
            'catatan'        => $n->catatan,
            'tanggal_input'  => optional($n->tanggal_input)->format('Y-m-d'),
            'tanggal_label'  => \Carbon\Carbon::parse($n->tanggal_input)->isoFormat('D MMM Y'),
            'update_url'     => route('nilai-akademik.update', $n->id),
            'destroy_url'    => route('nilai-akademik.destroy', $n->id),
        ];
    })->values();
@endphp
window.akademikRows = @json($akademikRowsForJs);

let currentRowId = null;

function hitungPredikat(nilai) {
    nilai = parseFloat(nilai);
    if (isNaN(nilai)) return null;
    if (nilai >= 90) return 'A';
    if (nilai >= 75) return 'B';
    if (nilai >= 60) return 'C';
    return 'D';
}

function updatePredikat(nilai, suffix) {
    const el = document.getElementById('predikat-display-' + suffix);
    const predikat = hitungPredikat(nilai);
    if (!predikat) {
        el.textContent = '—';
        el.style.color = 'var(--text-muted)';
        el.style.background = 'var(--bg-secondary)';
        return;
    }
    const map = {
        A: { color: 'var(--success)', bg: '#f0fdf4' },
        B: { color: 'var(--primary)', bg: '#eff6ff' },
        C: { color: 'var(--warning)', bg: '#fffbeb' },
        D: { color: 'var(--danger)',  bg: '#fef2f2' },
    };
    el.textContent = predikat;
    el.style.color = map[predikat].color;
    el.style.background = map[predikat].bg;
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
    return window.akademikRows.find(r => r.id === id);
}

function fillViewFields(d) {
    document.getElementById('d-nama').textContent     = d.nama + ' — Kelas ' + d.kelas;
    document.getElementById('d-mapel').textContent    = d.mata_pelajaran || '-';
    document.getElementById('d-nilai').textContent    = d.nilai_akhir ?? '-';
    document.getElementById('d-predikat').textContent = d.predikat || '-';
    document.getElementById('d-semester').textContent = 'Semester ' + (d.semester ?? '-');
    document.getElementById('d-tahun').textContent     = d.tahun_ajaran || '-';
    document.getElementById('d-catatan').textContent  = d.catatan || '-';
    document.getElementById('d-tanggal').textContent  = d.tanggal_label || d.tanggal_input || '-';
}

function fillEditForm(d) {
    document.getElementById('edit-form').action = d.update_url;

    document.getElementById('select-siswa-edit').value   = d.abk_id;
    document.getElementById('select-semester-edit').value = d.semester ?? '';
    document.getElementById('tahun-edit').value           = d.tahun_ajaran ?? '';
    document.getElementById('nilai-edit').value            = d.nilai_akhir ?? '';
    document.getElementById('tanggal-edit').value          = d.tanggal_input ?? '';
    document.getElementById('catatan-edit').value          = d.catatan ?? '';

    const selMapel = document.getElementById('select-mapel-edit');
    let found = false;
    for (const opt of selMapel.options) {
        if (opt.value === d.mata_pelajaran) { found = true; break; }
    }
    if (!found && d.mata_pelajaran) {
        const o = document.createElement('option');
        o.value = d.mata_pelajaran;
        o.textContent = d.mata_pelajaran;
        selMapel.appendChild(o);
    }
    selMapel.value = d.mata_pelajaran || '';

    updatePredikat(d.nilai_akhir, 'edit');
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
    if (confirm('Hapus data nilai akademik ini? Tindakan ini tidak dapat dibatalkan.')) {
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

// Tutup modal saat klik area gelap di luar box
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

// Buka kembali modal Tambah otomatis jika validasi gagal pada form ini
@if($errors->any() && old('_form') === 'tambah')
document.addEventListener('DOMContentLoaded', () => openAddModal());
@endif
</script>
@endsection