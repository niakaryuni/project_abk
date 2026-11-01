@extends('layouts.app')

@section('title', 'Data Siswa ABK')
@section('page-title', 'Data Siswa ABK')

@section('content')
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-child" style="color:var(--primary);margin-right:8px"></i>Daftar Siswa ABK</h3>
        @if(auth()->user()->isAdmin() || auth()->user()->isGuru())
        <button type="button" class="btn btn-primary" onclick="openModal('modalTambahSiswa')">
            <i class="fas fa-plus"></i> Tambah Siswa
        </button>
        @endif
    </div>

    {{-- Tab Filter Status --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isGuru())
    <div style="padding:0 20px;border-bottom:1px solid var(--border);display:flex;gap:4px">
        @foreach(['aktif' => 'Aktif ('.$totalAktif.')', 'nonaktif' => 'Nonaktif ('.$totalNonaktif.')', 'semua' => 'Semua ('.$totalSemua.')'] as $val => $label)
        <a href="{{ route('abk.index', array_merge(request()->except('status','page'), ['status' => $val])) }}"
           style="padding:12px 18px;font-size:13px;font-weight:600;text-decoration:none;border-bottom:2px solid {{ $statusFilter==$val ? 'var(--primary)' : 'transparent' }};color:{{ $statusFilter==$val ? 'var(--primary)' : 'var(--text-muted)' }};transition:all 0.2s">
            {{ $label }}
        </a>
        @endforeach
    </div>
    @endif

    {{-- Search & Filter --}}
    <div style="padding:14px 20px;border-bottom:1px solid var(--border)">
        <form method="GET" action="{{ route('abk.index') }}" style="display:flex;gap:10px;flex-wrap:wrap">
            <input type="hidden" name="status" value="{{ $statusFilter }}">
            <input type="text" name="search" class="form-control" style="width:220px"
                   placeholder="Cari nama siswa..." value="{{ request('search') }}">
            <select name="kelas" class="form-control" style="width:140px">
                <option value="">Semua Kelas</option>
                @foreach(['1A','1B','2A','2B','3A','3B','4A','4B','5A','5B','6A','6B'] as $kls)
                    <option value="{{ $kls }}" {{ request('kelas')==$kls ? 'selected':'' }}>Kelas {{ $kls }}</option>
                @endforeach
            </select>
            <select name="keterangan" class="form-control" style="width:160px">
                <option value="">Semua Kebutuhan</option>
                @foreach(['Tunanetra','Tunarungu','Tunagrahita','Tunalaras','Tunadaksa','Autis','Tunawicara','ADHD','Down Syndrome','Lambat Belajar'] as $ket)
                    <option value="{{ $ket }}" {{ request('keterangan')==$ket ? 'selected':'' }}>{{ $ket }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
            <a href="{{ route('abk.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Nama Siswa</th>
                    <th>Umur</th>
                    <th>Kelas</th>
                    <th>Jenis Kebutuhan</th>
                    <th>Guru</th>
                    <th>Status</th>
                    <th>Jumlah Nilai</th>
                    <th style="width:140px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($abks as $abk)
                <tr style="{{ $abk->status === 'nonaktif' ? 'opacity:0.55' : '' }}">
                    <td style="color:var(--text-muted);font-weight:600">{{ $loop->iteration }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:36px;height:36px;background:{{ $abk->status==='nonaktif' ? '#e5e7eb' : 'var(--primary-light)' }};border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:{{ $abk->status==='nonaktif' ? '#9ca3af' : 'var(--primary)' }};font-size:14px;flex-shrink:0">
                                {{ strtoupper(substr($abk->nama,0,1)) }}
                            </div>
                            <span style="font-weight:600">{{ $abk->nama }}</span>
                        </div>
                    </td>
                    <td>{{ $abk->umur ? $abk->umur.' tahun' : '-' }}</td>
                    <td>
                        @if($abk->kelas)
                            <span class="badge badge-primary">{{ $abk->kelas }}</span>
                        @else
                            <span style="color:var(--text-muted)">-</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:var(--text-muted)">{{ $abk->keterangan ?? '-' }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">{{ $abk->guru?->name ?? '-' }}</td>
                    <td>
                        @if($abk->status === 'aktif')
                            <span class="badge badge-keterampilan">Aktif</span>
                        @else
                            <span class="badge badge-sosial">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:700;color:var(--primary)">{{ $abk->nilais_count }}</span>
                        <span style="font-size:12px;color:var(--text-muted)"> entri</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap">
                            <a href="{{ route('abk.show', $abk) }}" class="btn btn-sm btn-primary" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isGuru())
                            @if($abk->status === 'aktif')
                            <button type="button" class="btn btn-sm btn-warning" title="Edit"
                                    onclick="openModal('modalEditSiswa{{ $abk->id }}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('abk.nonaktifkan', $abk) }}" method="POST"
                                  onsubmit="return confirm('Nonaktifkan siswa {{ $abk->nama }}?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-secondary" title="Nonaktifkan"
                                        style="color:var(--danger);border-color:var(--danger)">
                                    <i class="fas fa-user-slash"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('abk.aktifkan', $abk) }}" method="POST"
                                  onsubmit="return confirm('Aktifkan kembali siswa {{ $abk->nama }}?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-success" title="Aktifkan Kembali">
                                    <i class="fas fa-user-check"></i>
                                </button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="fas fa-inbox" style="font-size:36px;margin-bottom:12px;display:block;opacity:0.4"></i>
                        @if($statusFilter === 'nonaktif')
                            Tidak ada siswa yang dinonaktifkan.
                        @else
                            Belum ada data siswa ABK.
                            @if(auth()->user()->isAdmin() || auth()->user()->isGuru())
                            <br>
                            <button type="button" class="btn btn-primary" style="margin-top:12px" onclick="openModal('modalTambahSiswa')">
                                <i class="fas fa-plus"></i> Tambah Sekarang
                            </button>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($abks->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--border)">
        {{ $abks->links() }}
    </div>
    @endif
</div>

{{-- ═══════════ MODAL: TAMBAH SISWA ═══════════ --}}
<div class="modal-overlay" id="modalTambahSiswa">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3><i class="fas fa-user-plus" style="color:var(--primary);margin-right:8px"></i>Form Tambah Siswa ABK</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalTambahSiswa')">&times;</button>
        </div>
        <div class="modal-box-body">

            @if($errors->any() && old('form_context') == 'tambah')
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
                <input type="hidden" name="form_context" value="tambah">

                <div class="form-group">
                    <label>Nama Lengkap Siswa <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nama" class="form-control"
                           placeholder="Masukkan nama lengkap siswa"
                           value="{{ old('form_context')=='tambah' ? old('nama') : '' }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Umur <span style="color:var(--danger)">*</span></label>
                        <input type="number" name="umur" class="form-control"
                               placeholder="Contoh: 9" min="1" max="30"
                               value="{{ old('form_context')=='tambah' ? old('umur') : '' }}" required>
                    </div>
                    <div class="form-group">
                        <label>Kelas <span style="color:var(--danger)">*</span></label>
                        <select name="kelas" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach(['1A','1B','2A','2B','3A','3B','4A','4B','5A','5B','6A','6B'] as $kls)
                                <option value="{{ $kls }}" {{ (old('form_context')=='tambah' && old('kelas')==$kls) ? 'selected':'' }}>
                                    Kelas {{ $kls }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Jenis Kebutuhan Khusus <span style="color:var(--danger)">*</span></label>
                    <select name="keterangan" class="form-control" required>
                        <option value="">-- Pilih Jenis Kebutuhan Khusus --</option>
                        @foreach(['Tunanetra','Tunarungu','Tunagrahita','Tunalaras','Tunadaksa','Autis','Tunawicara','ADHD','Down Syndrome','Lambat Belajar'] as $ket)
                            <option value="{{ $ket }}" {{ (old('form_context')=='tambah' && old('keterangan')==$ket) ? 'selected':'' }}>{{ $ket }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Guru Pengampu</label>
                    <select name="id_guru" class="form-control">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $guru)
                            <option value="{{ $guru->id }}" {{ (old('form_context')=='tambah' && old('id_guru')==$guru->id) ? 'selected':'' }}>
                                {{ $guru->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Orang Tua / Wali</label>
                    <select name="id_orang_tua" class="form-control">
                        <option value="">-- Pilih Orang Tua --</option>
                        @foreach($orangTuas as $ot)
                            <option value="{{ $ot->id }}" {{ (old('form_context')=='tambah' && old('id_orang_tua')==$ot->id) ? 'selected':'' }}>
                                {{ $ot->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahSiswa')">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Siswa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════ MODAL: EDIT SISWA (satu per baris) ═══════════ --}}
@foreach($abks as $abk)
<div class="modal-overlay" id="modalEditSiswa{{ $abk->id }}">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3><i class="fas fa-edit" style="color:var(--primary);margin-right:8px"></i>Edit Data: {{ $abk->nama }}</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('modalEditSiswa{{ $abk->id }}')">&times;</button>
        </div>
        <div class="modal-box-body">

            @if($errors->any() && old('form_context') == 'edit_'.$abk->id)
            <div style="background:#fde8e8;border:1px solid var(--danger);border-radius:8px;padding:12px 16px;margin-bottom:20px">
                <ul style="margin:0;padding-left:20px">
                    @foreach($errors->all() as $error)
                        <li style="font-size:13px">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('abk.update', $abk) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="form_context" value="edit_{{ $abk->id }}">

                <div class="form-group">
                    <label>Nama Lengkap Siswa <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="nama" class="form-control"
                           value="{{ old('form_context')=='edit_'.$abk->id ? old('nama') : $abk->nama }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Umur <span style="color:var(--danger)">*</span></label>
                        <input type="number" name="umur" class="form-control" min="1" max="30"
                               value="{{ old('form_context')=='edit_'.$abk->id ? old('umur') : $abk->umur }}" required>
                    </div>
                    <div class="form-group">
                        <label>Kelas <span style="color:var(--danger)">*</span></label>
                        <select name="kelas" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach(['1A','1B','2A','2B','3A','3B','4A','4B','5A','5B','6A','6B'] as $kls)
                                @php $selectedKelas = old('form_context')=='edit_'.$abk->id ? old('kelas') : $abk->kelas; @endphp
                                <option value="{{ $kls }}" {{ $selectedKelas==$kls ? 'selected':'' }}>Kelas {{ $kls }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Jenis Kebutuhan Khusus <span style="color:var(--danger)">*</span></label>
                    <select name="keterangan" class="form-control" required>
                        <option value="">-- Pilih Jenis Kebutuhan Khusus --</option>
                        @foreach(['Tunanetra','Tunarungu','Tunagrahita','Tunalaras','Tunadaksa','Autis','Tunawicara','ADHD','Down Syndrome','Lambat Belajar'] as $ket)
                            @php $selectedKet = old('form_context')=='edit_'.$abk->id ? old('keterangan') : $abk->keterangan; @endphp
                            <option value="{{ $ket }}" {{ $selectedKet==$ket ? 'selected':'' }}>{{ $ket }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Guru Pengampu</label>
                    <select name="id_guru" class="form-control">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($gurus as $guru)
                            @php $selectedGuru = old('form_context')=='edit_'.$abk->id ? old('id_guru') : $abk->id_guru; @endphp
                            <option value="{{ $guru->id }}" {{ $selectedGuru==$guru->id ? 'selected':'' }}>{{ $guru->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Orang Tua / Wali</label>
                    <select name="id_orang_tua" class="form-control">
                        <option value="">-- Pilih Orang Tua --</option>
                        @foreach($orangTuas as $ot)
                            @php $selectedOt = old('form_context')=='edit_'.$abk->id ? old('id_orang_tua') : $abk->id_orang_tua; @endphp
                            <option value="{{ $ot->id }}" {{ $selectedOt==$ot->id ? 'selected':'' }}>{{ $ot->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditSiswa{{ $abk->id }}')">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('styles')
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
</style>
@endpush

@push('scripts')
<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
    document.body.style.overflow = '';
}
// Klik di luar kotak modal akan menutup modal
document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal(overlay.id);
    });
});
// Tombol Esc menutup modal yang aktif
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(function(m) {
            closeModal(m.id);
        });
    }
});

@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    const context = "{{ old('form_context') }}";
    if (context === 'tambah') {
        openModal('modalTambahSiswa');
    } else if (context.startsWith('edit_')) {
        openModal('modalEditSiswa' + context.replace('edit_', ''));
    }
});
@endif
</script>
@endpush
@endsection