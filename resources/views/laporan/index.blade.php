@extends('layouts.app')

@section('title', 'Data Laporan')
@section('page-title', 'Data Laporan Siswa')

@section('content')

@if(session('success'))
<div class="alert-info" style="background:#e6f7ee;border:1px solid #34c77b;color:#1b7a4b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px">
    <i class="fas fa-check-circle" style="margin-right:6px"></i>{{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-alt" style="color:var(--primary);margin-right:8px"></i>Daftar Laporan</h3>
        @if(auth()->user()->isGuru())
        <button type="button" class="btn btn-primary" onclick="bukaModalLaporan()">
            <i class="fas fa-plus"></i> Buat Laporan
        </button>
        @endif
    </div>

    {{-- Filter & Pencarian --}}
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;gap:10px;flex-wrap:wrap">
        <form method="GET" action="{{ route('laporan.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;width:100%">
            <input type="text" name="search" class="form-control" style="width:220px"
                   placeholder="Cari nama siswa..." value="{{ request('search') }}">

            <select name="jenis_laporan" class="form-control" style="width:200px">
                <option value="">Semua Jenis Laporan</option>
                <option value="perkembangan" {{ request('jenis_laporan')=='perkembangan' ? 'selected':'' }}>Perkembangan</option>
                <option value="akademik"     {{ request('jenis_laporan')=='akademik' ? 'selected':'' }}>Akademik</option>
                <option value="gabungan"     {{ request('jenis_laporan')=='gabungan' ? 'selected':'' }}>Gabungan</option>
            </select>

            @if(!auth()->user()->isOrangTua())
            <select name="status" class="form-control" style="width:160px">
                <option value="">Semua Status</option>
                <option value="draft" {{ request('status')=='draft' ? 'selected':'' }}>Draft</option>
                <option value="final" {{ request('status')=='final' ? 'selected':'' }}>Final</option>
            </select>
            @endif

            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Nama Siswa</th>
                    <th>Jenis</th>
                    <th>Periode</th>
                    <th>Semester</th>
                    <th>Tahun Ajaran</th>
                    <th>Dibuat Oleh</th>
                    <th>Status</th>
                    <th style="width:130px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $lap)
                <tr>
                    <td style="color:var(--text-muted);font-weight:600">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight:600">{{ $lap->abk->nama ?? '-' }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">Kelas {{ $lap->abk->kelas ?? '-' }}</div>
                    </td>
                    <td style="font-size:13px">{{ $lap->labelJenis() }}</td>
                    <td style="font-size:13px">{{ $lap->periode }}</td>
                    <td style="font-size:13px">Semester {{ $lap->semester }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">{{ $lap->tahun_ajaran }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">{{ $lap->guru->name ?? '-' }}</td>
                    <td>
                        @if($lap->status === 'final')
                            <span class="badge badge-keterampilan">Final</span>
                        @else
                            <span class="badge badge-sosial">Draft</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <button type="button" class="btn btn-sm btn-primary" title="Lihat Detail"
                                onclick="bukaModalDetailLaporan(this)"
                                data-nama="{{ $lap->abk->nama ?? '-' }}"
                                data-kelas="{{ $lap->abk->kelas ?? '-' }}"
                                data-umur="{{ $lap->abk->umur ?? '-' }}"
                                data-keterangan="{{ $lap->abk->keterangan ?? '-' }}"
                                data-jenis="{{ $lap->labelJenis() }}"
                                data-periode="{{ $lap->periode }}"
                                data-semester="{{ $lap->semester }}"
                                data-tahunajaran="{{ $lap->tahun_ajaran }}"
                                data-guru="{{ $lap->guru->name ?? '-' }}"
                                data-status="{{ $lap->status }}"
                                data-ringkasan="{{ $lap->ringkasan }}"
                                data-download="{{ route('laporan.download', $lap) }}"
                                @if(auth()->user()->isAdmin() || auth()->user()->isGuru())
                                data-edit="{{ route('laporan.edit', $lap) }}"
                                @endif
                            >
                                <i class="fas fa-eye"></i>
                            </button>

                            @if(auth()->user()->isGuru())
                                <a href="{{ route('laporan.edit', $lap) }}"
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('laporan.destroy', $lap) }}" method="POST"
                                      onsubmit="return confirm('Yakin hapus laporan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="fas fa-file-alt" style="font-size:36px;margin-bottom:12px;display:block;opacity:0.4"></i>
                        Belum ada laporan.
                        @if(auth()->user()->isGuru())
                        <br>
                        <button type="button" class="btn btn-primary" style="margin-top:12px" onclick="bukaModalLaporan()">
                            <i class="fas fa-plus"></i> Buat Laporan
                        </button>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($laporans, 'hasPages') && $laporans->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--border)">
        {{ $laporans->links() }}
    </div>
    @endif
</div>

@if(auth()->user()->isGuru())
<!-- ===== MODAL TAMBAH LAPORAN ===== -->
<div id="modalLaporanOverlay" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="fas fa-plus-circle" style="color:var(--primary);margin-right:8px"></i>Tambah Laporan</h3>
            <button type="button" class="modal-close" onclick="tutupModalLaporan()">&times;</button>
        </div>

        <form action="{{ route('laporan.store') }}" method="POST">
            @csrf
            <div class="modal-body">

                <div class="alert-info">
                    <i class="fas fa-magic" style="margin-right:6px"></i>
                    Ringkasan laporan akan <strong>dibuat otomatis</strong> dari data Nilai Perkembangan &amp; Nilai Akademik siswa yang sudah ada — tidak perlu input ulang.
                </div>

                <div class="form-group">
                    <label>Nama Siswa <span style="color:#e25555">*</span></label>
                    <select name="abk_id" class="form-control" required>
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($abks as $abk)
                            <option value="{{ $abk->id }}">{{ $abk->nama }} (Kelas {{ $abk->kelas }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Jenis Laporan <span style="color:#e25555">*</span></label>
                    <select name="jenis_laporan" id="jenisLaporanSelect" class="form-control" required onchange="updateInfoJenis()">
                        <option value="">-- Pilih Jenis Laporan --</option>
                        <option value="perkembangan">Perkembangan (Motorik, Keterampilan, Sosial)</option>
                        <option value="akademik">Akademik (Nilai Mata Pelajaran)</option>
                        <option value="gabungan">Gabungan (Perkembangan + Akademik)</option>
                    </select>
                    <small id="infoJenisLaporan" style="display:block;margin-top:6px;color:var(--text-muted);font-size:12.5px"></small>
                </div>

                <div class="form-row" style="display:flex;gap:14px">
                    <div class="form-group" style="flex:1">
                        <label>Periode <span style="color:#e25555">*</span></label>
                        <input type="text" name="periode" class="form-control" placeholder="Contoh: Januari - Juni 2026" required>
                    </div>
                    <div class="form-group" style="flex:1">
                        <label>Semester <span style="color:#e25555">*</span></label>
                        <select name="semester" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" style="display:flex;gap:14px">
                    <div class="form-group" style="flex:1">
                        <label>Tahun Ajaran <span style="color:#e25555">*</span></label>
                        <input type="text" name="tahun_ajaran" class="form-control" placeholder="Contoh: 2025/2026" required>
                    </div>
                    <div class="form-group" style="flex:1">
                        <label>Status <span style="color:#e25555">*</span></label>
                        <select name="status" class="form-control" required>
                            <option value="draft">Draft</option>
                            <option value="final">Final</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-secondary" onclick="tutupModalLaporan()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Laporan</button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- ===== MODAL DETAIL LAPORAN ===== -->
<div id="modalDetailLaporanOverlay" class="modal-overlay">
    <div class="modal-box" style="width:560px">
        <div class="modal-head">
            <h3><i class="fas fa-file-alt" style="color:var(--primary);margin-right:8px"></i>Detail Laporan</h3>
            <button type="button" class="modal-close" onclick="tutupModalDetailLaporan()">&times;</button>
        </div>
        <div class="modal-body">

            {{-- Info Siswa --}}
            <div style="display:flex;align-items:center;gap:14px;padding:14px 16px;background:var(--primary-light);border-radius:10px;margin-bottom:18px">
                <div id="dl-avatar" style="width:46px;height:46px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;color:white;font-size:18px;flex-shrink:0">S</div>
                <div>
                    <div id="dl-nama" style="font-size:15px;font-weight:700">-</div>
                    <div style="font-size:12.5px;color:var(--text-muted)">
                        Kelas <span id="dl-kelas">-</span> ·
                        <span id="dl-umur">-</span> tahun ·
                        <span id="dl-keterangan">-</span>
                    </div>
                </div>
            </div>

            {{-- Detail Field --}}
            <div class="detail-row">
                <span class="detail-label">Jenis Laporan</span>
                <span class="detail-value" id="dl-jenis">-</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Periode</span>
                <span class="detail-value" id="dl-periode">-</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Semester</span>
                <span class="detail-value" id="dl-semester">-</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tahun Ajaran</span>
                <span class="detail-value" id="dl-tahunajaran">-</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Dibuat Oleh</span>
                <span class="detail-value" id="dl-guru">-</span>
            </div>
            <div class="detail-row" style="border-bottom:none">
                <span class="detail-label">Status</span>
                <span class="detail-value" id="dl-status">-</span>
            </div>

            {{-- Ringkasan --}}
            <div id="dl-ringkasan-wrap" style="margin-top:18px">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px">
                    <span style="font-size:13px;font-weight:600;color:var(--text-muted)">Ringkasan Laporan</span>
                    <span style="font-size:11px;background:#eaf7ee;color:#1b7a4b;padding:2px 8px;border-radius:20px;font-weight:600">
                        <i class="fas fa-magic" style="margin-right:3px"></i>Auto-generate
                    </span>
                </div>
                <div id="dl-ringkasan" style="background:var(--bg);border-radius:8px;padding:14px;font-size:13px;line-height:1.8;white-space:pre-line;max-height:240px;overflow-y:auto">-</div>
            </div>

        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-secondary" onclick="tutupModalDetailLaporan()">Tutup</button>
            <a id="dl-download" href="#" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Unduh PDF
            </a>
            <a id="dl-edit" href="#" class="btn btn-warning" style="display:none">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
</div>

<style>
.modal-overlay{
    display:none;position:fixed;inset:0;background:rgba(15,23,42,0.55);
    align-items:center;justify-content:center;z-index:1000;
}
.modal-overlay.active{ display:flex; }
.modal-box{
    background:#fff;border-radius:12px;width:520px;max-width:92%;
    max-height:90vh;overflow-y:auto;box-shadow:0 10px 40px rgba(0,0,0,0.2);
}
.modal-head{
    display:flex;align-items:center;justify-content:space-between;
    padding:18px 22px;border-bottom:1px solid var(--border);
}
.modal-head h3{ margin:0;font-size:17px; }
.modal-close{
    background:none;border:none;font-size:22px;line-height:1;cursor:pointer;
    color:var(--text-muted);
}
.modal-body{ padding:20px 22px; }
.modal-foot{
    display:flex;justify-content:flex-end;gap:10px;
    padding:16px 22px;border-top:1px solid var(--border);
}
.alert-info{
    background:#eaf7ee;border:1px solid #34c77b;color:#1b7a4b;
    padding:12px 14px;border-radius:8px;margin-bottom:18px;font-size:13.5px;
}
.form-group{ margin-bottom:16px; }
.form-group label{ display:block;font-weight:600;font-size:13.5px;margin-bottom:6px; }
.form-control{
    width:100%;padding:9px 12px;border:1px solid var(--border);
    border-radius:8px;font-size:14px;
}
.detail-row{
    display:flex;justify-content:space-between;align-items:flex-start;
    gap:12px;padding:10px 0;border-bottom:1px solid var(--border);
}
.detail-label{ font-size:12.5px;color:var(--text-muted);font-weight:600;flex-shrink:0 }
.detail-value{ font-size:13.5px;font-weight:600;text-align:right }
</style>

<script>
// ===== Modal Tambah Laporan =====
function bukaModalLaporan() {
    document.getElementById('modalLaporanOverlay').classList.add('active');
}
function tutupModalLaporan() {
    document.getElementById('modalLaporanOverlay').classList.remove('active');
}
function updateInfoJenis() {
    const val = document.getElementById('jenisLaporanSelect').value;
    const info = document.getElementById('infoJenisLaporan');
    const teks = {
        perkembangan: 'Akan menarik rata-rata & catatan terbaru dari Nilai Perkembangan (Motorik, Keterampilan, Sosial).',
        akademik: 'Akan menarik seluruh nilai mata pelajaran sesuai semester & tahun ajaran yang dipilih.',
        gabungan: 'Akan menggabungkan data Nilai Perkembangan dan Nilai Akademik dalam satu ringkasan.'
    };
    info.textContent = teks[val] || '';
}
@if(auth()->user()->isGuru())
document.getElementById('modalLaporanOverlay').addEventListener('click', function (e) {
    if (e.target === this) tutupModalLaporan();
});
@endif

// ===== Modal Detail Laporan =====
function bukaModalDetailLaporan(btn) {
    document.getElementById('dl-nama').textContent = btn.dataset.nama || '-';
    document.getElementById('dl-avatar').textContent = (btn.dataset.nama || 'S').charAt(0).toUpperCase();
    document.getElementById('dl-kelas').textContent = btn.dataset.kelas || '-';
    document.getElementById('dl-umur').textContent = btn.dataset.umur || '-';
    document.getElementById('dl-keterangan').textContent = btn.dataset.keterangan || '-';
    document.getElementById('dl-jenis').textContent = btn.dataset.jenis || '-';
    document.getElementById('dl-periode').textContent = btn.dataset.periode || '-';
    document.getElementById('dl-semester').textContent = 'Semester ' + (btn.dataset.semester || '-');
    document.getElementById('dl-tahunajaran').textContent = btn.dataset.tahunajaran || '-';
    document.getElementById('dl-guru').textContent = btn.dataset.guru || '-';

    const statusEl = document.getElementById('dl-status');
    if (btn.dataset.status === 'final') {
        statusEl.innerHTML = '<span class="badge badge-keterampilan">Final</span>';
    } else {
        statusEl.innerHTML = '<span class="badge badge-sosial">Draft</span>';
    }

    const ringkasan = btn.dataset.ringkasan || '';
    document.getElementById('dl-ringkasan-wrap').style.display = ringkasan ? 'block' : 'none';
    document.getElementById('dl-ringkasan').textContent = ringkasan;

    document.getElementById('dl-download').href = btn.dataset.download || '#';

    const editBtn = document.getElementById('dl-edit');
    if (btn.dataset.edit) {
        editBtn.href = btn.dataset.edit;
        editBtn.style.display = 'inline-block';
    } else {
        editBtn.style.display = 'none';
    }

    document.getElementById('modalDetailLaporanOverlay').classList.add('active');
}
function tutupModalDetailLaporan() {
    document.getElementById('modalDetailLaporanOverlay').classList.remove('active');
}
document.getElementById('modalDetailLaporanOverlay').addEventListener('click', function (e) {
    if (e.target === this) tutupModalDetailLaporan();
});

// Buka otomatis modal Tambah Laporan kalau ada error validasi dari server (redirect back)
@if($errors->any() && auth()->user()->isGuru())
    document.addEventListener('DOMContentLoaded', bukaModalLaporan);
@endif
</script>

@endsection