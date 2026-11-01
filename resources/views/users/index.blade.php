@extends('layouts.app')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')

@if(session('success'))
<div class="alert-info" style="background:#e6f7ee;border:1px solid #34c77b;color:#1b7a4b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px">
    <i class="fas fa-check-circle" style="margin-right:6px"></i>{{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert-info" style="background:#fde8e8;border:1px solid var(--danger);color:#a33;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px">
    <i class="fas fa-exclamation-circle" style="margin-right:6px"></i>{{ session('error') }}
</div>
@endif

{{-- Stat Cards --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-user-shield"></i></div>
        <div class="stat-label">Admin</div>
        <div class="stat-value">{{ $totalAdmin }}</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-label">Guru</div>
        <div class="stat-value">{{ $totalGuru }}</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="fas fa-user-tie"></i></div>
        <div class="stat-label">Kepala Sekolah</div>
        <div class="stat-value">{{ $totalKepsek }}</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon red"><i class="fas fa-users"></i></div>
        <div class="stat-label">Orang Tua</div>
        <div class="stat-value">{{ $totalOrangTua }}</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-users-cog" style="color:var(--primary);margin-right:8px"></i>Daftar Pengguna Sistem</h3>
        <button type="button" class="btn btn-primary" onclick="bukaModalUser()">
            <i class="fas fa-plus"></i> Tambah User
        </button>
    </div>

    {{-- Tab Filter Status --}}
    <div style="padding:0 20px;border-bottom:1px solid var(--border);display:flex;gap:4px">
        @foreach(['aktif' => 'Aktif ('.$totalAktif.')', 'nonaktif' => 'Nonaktif ('.$totalNonaktif.')', 'semua' => 'Semua ('.$totalSemua.')'] as $val => $label)
        <a href="{{ route('users.index', array_merge(request()->except('status','page'), ['status' => $val])) }}"
           style="padding:12px 18px;font-size:13px;font-weight:600;text-decoration:none;border-bottom:2px solid {{ $statusFilter==$val ? 'var(--primary)' : 'transparent' }};color:{{ $statusFilter==$val ? 'var(--primary)' : 'var(--text-muted)' }};transition:all 0.2s">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Filter & Search --}}
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;gap:10px;flex-wrap:wrap">
        <form method="GET" action="{{ route('users.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;width:100%">
            <input type="hidden" name="status" value="{{ $statusFilter }}">
            <input type="text" name="search" class="form-control" style="width:220px"
                   placeholder="Cari nama atau email..." value="{{ request('search') }}">
            <select name="role" class="form-control" style="width:180px">
                <option value="">Semua Role</option>
                <option value="admin"          {{ request('role')=='admin' ? 'selected':'' }}>Admin</option>
                <option value="guru"           {{ request('role')=='guru' ? 'selected':'' }}>Guru</option>
                <option value="kepala_sekolah" {{ request('role')=='kepala_sekolah' ? 'selected':'' }}>Kepala Sekolah</option>
                <option value="orangtua"       {{ request('role')=='orangtua' ? 'selected':'' }}>Orang Tua</option>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Reset</a>
        </form>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width:50px">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Info Tambahan</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                    $roleLabel = ['admin'=>'Admin','guru'=>'Guru','kepala_sekolah'=>'Kepala Sekolah','orangtua'=>'Orang Tua'];
                    $roleColor = ['admin'=>'badge-motorik','guru'=>'badge-keterampilan','kepala_sekolah'=>'badge-primary','orangtua'=>'badge-sosial'];
                    $anak = $user->role === 'orangtua' ? $user->anakAbk->first() : null;
                    $infoTambahan = $user->role === 'guru'
                        ? $user->abkDikelola->count().' siswa'
                        : ($user->role === 'orangtua'
                            ? ($anak ? $anak->nama : 'Belum ada anak')
                            : '-');
                @endphp
                <tr style="{{ $user->status === 'nonaktif' ? 'opacity:0.55' : '' }}">
                    <td style="color:var(--text-muted);font-weight:600">{{ $loop->iteration }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;
                                background:{{ $user->role=='admin' ? 'var(--primary-light)' : ($user->role=='guru' ? '#d4edda' : ($user->role=='kepala_sekolah' ? '#fff3cd' : '#fde8d5')) }};
                                color:{{ $user->role=='admin' ? 'var(--primary)' : ($user->role=='guru' ? 'var(--success)' : ($user->role=='kepala_sekolah' ? 'var(--warning)' : 'var(--accent-dark)')) }}">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                    <div style="font-size:11px;color:var(--primary)">(Akun Anda)</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--text-muted)">{{ $user->email }}</td>
                    <td>
                        <span class="badge {{ $roleColor[$user->role] ?? 'badge-primary' }}">
                            {{ $roleLabel[$user->role] ?? $user->role }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:var(--text-muted)">
                        @if($user->role === 'guru')
                            <i class="fas fa-child" style="color:var(--primary)"></i> {{ $infoTambahan }}
                        @elseif($user->role === 'orangtua')
                            @if($anak)
                                <i class="fas fa-user-graduate" style="color:var(--success)"></i> {{ $infoTambahan }}
                            @else
                                <span style="color:#ccc">{{ $infoTambahan }}</span>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($user->status === 'aktif')
                            <span class="badge badge-keterampilan">Aktif</span>
                        @else
                            <span class="badge badge-sosial">Nonaktif</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:var(--text-muted)">
                        {{ $user->created_at->isoFormat('D MMM Y') }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <button type="button" class="btn btn-sm btn-primary" title="Lihat Detail"
                                onclick="bukaModalDetail(this)"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-role="{{ $roleLabel[$user->role] ?? $user->role }}"
                                data-info="{{ $infoTambahan }}"
                                data-created="{{ $user->created_at->isoFormat('D MMMM Y') }}">
                                <i class="fas fa-eye"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-warning" title="Edit"
                                onclick="bukaModalEdit(this)"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-role="{{ $user->role }}"
                                data-abkid="{{ $anak->id ?? '' }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            @if($user->id !== auth()->id())
                                @if($user->status === 'aktif')
                                <form action="{{ route('users.nonaktifkan', $user) }}" method="POST"
                                      onsubmit="return confirm('Nonaktifkan akun {{ $user->name }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-secondary" title="Nonaktifkan"
                                            style="color:var(--danger);border-color:var(--danger)">
                                        <i class="fas fa-user-slash"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('users.aktifkan', $user) }}" method="POST"
                                      onsubmit="return confirm('Aktifkan kembali akun {{ $user->name }}?')">
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
                    <td colspan="8" style="text-align:center;padding:48px;color:var(--text-muted)">
                        <i class="fas fa-users" style="font-size:36px;margin-bottom:12px;display:block;opacity:0.4"></i>
                        @if($statusFilter === 'nonaktif')
                            Tidak ada pengguna yang dinonaktifkan.
                        @else
                            Belum ada data pengguna.<br>
                            <button type="button" class="btn btn-primary" style="margin-top:12px" onclick="bukaModalUser()">
                                <i class="fas fa-plus"></i> Tambah User
                            </button>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--border)">
        {{ $users->links() }}
    </div>
    @endif
</div>

<!-- ===== MODAL TAMBAH PENGGUNA ===== -->
<div id="modalUserOverlay" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="fas fa-user-plus" style="color:var(--primary);margin-right:8px"></i>Tambah Pengguna</h3>
            <button type="button" class="modal-close" onclick="tutupModalUser()">&times;</button>
        </div>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_form_type" value="tambah">
            <div class="modal-body">

                @if($errors->any() && old('_form_type') === 'tambah')
                <div style="background:#fde8e8;border:1px solid var(--danger);border-radius:8px;padding:12px 16px;margin-bottom:16px">
                    <ul style="margin:0;padding-left:20px">
                        @foreach($errors->all() as $error)
                            <li style="font-size:13px">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="form-group">
                    <label>Nama Lengkap <span style="color:#e25555">*</span></label>
                    <input type="text" name="name" class="form-control"
                           placeholder="Masukkan nama lengkap"
                           value="{{ old('_form_type') === 'tambah' ? old('name') : '' }}" required>
                </div>

                <div class="form-group">
                    <label>Email <span style="color:#e25555">*</span></label>
                    <input type="email" name="email" class="form-control"
                           placeholder="contoh@email.com"
                           value="{{ old('_form_type') === 'tambah' ? old('email') : '' }}" required>
                </div>

                <div class="form-group">
                    <label>Role <span style="color:#e25555">*</span></label>
                    <select name="role" id="modalSelectRole" class="form-control" required onchange="toggleAbkModal()">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin"          {{ old('role')=='admin' ? 'selected':'' }}>Admin</option>
                        <option value="guru"           {{ old('role')=='guru' ? 'selected':'' }}>Guru</option>
                        <option value="kepala_sekolah" {{ old('role')=='kepala_sekolah' ? 'selected':'' }}>Kepala Sekolah</option>
                        <option value="orangtua"       {{ old('role')=='orangtua' ? 'selected':'' }}>Orang Tua</option>
                    </select>
                </div>

                {{-- Field khusus Orang Tua --}}
                <div id="modalSectionAbk" class="form-group" style="display:none">
                    <label>Siswa ABK yang Diasuh</label>
                    <select name="abk_id" class="form-control">
                        <option value="">-- Pilih Siswa (opsional) --</option>
                        @foreach($abkList ?? [] as $abk)
                            <option value="{{ $abk->id }}" {{ old('abk_id') == $abk->id ? 'selected':'' }}>
                                {{ $abk->nama }} — Kelas {{ $abk->kelas ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row" style="display:flex;gap:14px">
                    <div class="form-group" style="flex:1">
                        <label>Password <span style="color:#e25555">*</span></label>
                        <input type="password" name="password" class="form-control"
                               placeholder="Min. 6 karakter" required>
                    </div>
                    <div class="form-group" style="flex:1">
                        <label>Konfirmasi Password <span style="color:#e25555">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control"
                               placeholder="Ulangi password" required>
                    </div>
                </div>

            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-secondary" onclick="tutupModalUser()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== MODAL EDIT PENGGUNA ===== -->
<div id="modalEditOverlay" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-head">
            <h3><i class="fas fa-user-edit" style="color:var(--warning);margin-right:8px"></i>Edit Pengguna</h3>
            <button type="button" class="modal-close" onclick="tutupModalEdit()">&times;</button>
        </div>

        <form action="#" method="POST" id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="_form_type" value="edit">
            <input type="hidden" name="_editing_id" id="edit-editing-id" value="{{ old('_editing_id') }}">
            <div class="modal-body">

                @if($errors->any() && old('_form_type') === 'edit')
                <div style="background:#fde8e8;border:1px solid var(--danger);border-radius:8px;padding:12px 16px;margin-bottom:16px">
                    <ul style="margin:0;padding-left:20px">
                        @foreach($errors->all() as $error)
                            <li style="font-size:13px">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="form-group">
                    <label>Nama Lengkap <span style="color:#e25555">*</span></label>
                    <input type="text" name="name" id="edit-name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Email <span style="color:#e25555">*</span></label>
                    <input type="email" name="email" id="edit-email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Role <span style="color:#e25555">*</span></label>
                    <select name="role" id="edit-role" class="form-control" required onchange="toggleAbkEdit()">
                        <option value="admin">Admin</option>
                        <option value="guru">Guru</option>
                        <option value="kepala_sekolah">Kepala Sekolah</option>
                        <option value="orangtua">Orang Tua</option>
                    </select>
                </div>

                {{-- Field khusus Orang Tua --}}
                <div id="editSectionAbk" class="form-group" style="display:none">
                    <label>Siswa ABK yang Diasuh</label>
                    <select name="abk_id" id="edit-abkid" class="form-control">
                        <option value="">-- Tidak ada / Lepas relasi --</option>
                        @foreach($abkList ?? [] as $abk)
                            <option value="{{ $abk->id }}">{{ $abk->nama }} — Kelas {{ $abk->kelas ?? '-' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label style="font-weight:600;color:var(--text-muted);font-size:12.5px">
                        🔒 Kosongkan password jika tidak ingin mengubahnya
                    </label>
                </div>
                <div class="form-row" style="display:flex;gap:14px">
                    <div class="form-group" style="flex:1">
                        <label>Password Baru</label>
                        <input type="password" name="password" id="edit-password" class="form-control"
                               placeholder="Min. 6 karakter (opsional)">
                    </div>
                    <div class="form-group" style="flex:1">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" id="edit-password-confirmation" class="form-control"
                               placeholder="Ulangi password baru">
                    </div>
                </div>

            </div>
            <div class="modal-foot">
                <button type="button" class="btn btn-secondary" onclick="tutupModalEdit()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Perbarui Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== MODAL DETAIL PENGGUNA ===== -->
<div id="modalDetailOverlay" class="modal-overlay">
    <div class="modal-box" style="width:440px">
        <div class="modal-head">
            <h3><i class="fas fa-id-card" style="color:var(--primary);margin-right:8px"></i>Detail Pengguna</h3>
            <button type="button" class="modal-close" onclick="tutupModalDetail()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-row">
                <span class="detail-label">Nama Lengkap</span>
                <span class="detail-value" id="detail-name">-</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email</span>
                <span class="detail-value" id="detail-email">-</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Role</span>
                <span class="detail-value" id="detail-role">-</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Info Tambahan</span>
                <span class="detail-value" id="detail-info">-</span>
            </div>
            <div class="detail-row" style="border-bottom:none">
                <span class="detail-label">Dibuat Sejak</span>
                <span class="detail-value" id="detail-created">-</span>
            </div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-secondary" onclick="tutupModalDetail()">Tutup</button>
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
.form-group{ margin-bottom:16px; }
.form-group label{ display:block;font-weight:600;font-size:13.5px;margin-bottom:6px; }
.form-control{
    width:100%;padding:9px 12px;border:1px solid var(--border);
    border-radius:8px;font-size:14px;
}
.detail-row{
    display:flex;justify-content:space-between;align-items:flex-start;
    gap:12px;padding:12px 0;border-bottom:1px solid var(--border);
}
.detail-label{ font-size:12.5px;color:var(--text-muted);font-weight:600;flex-shrink:0 }
.detail-value{ font-size:13.5px;font-weight:600;text-align:right }
</style>

<script>
const editUrlTemplate = "{{ route('users.update', ['user' => '__ID__']) }}";

// ===== Modal Tambah =====
function bukaModalUser() {
    document.getElementById('modalUserOverlay').classList.add('active');
}
function tutupModalUser() {
    document.getElementById('modalUserOverlay').classList.remove('active');
}
function toggleAbkModal() {
    const role = document.getElementById('modalSelectRole').value;
    document.getElementById('modalSectionAbk').style.display = role === 'orangtua' ? 'block' : 'none';
}
document.getElementById('modalUserOverlay').addEventListener('click', function (e) {
    if (e.target === this) tutupModalUser();
});

// ===== Modal Edit =====
function bukaModalEdit(btn) {
    const id = btn.dataset.id;
    document.getElementById('editForm').action = editUrlTemplate.replace('__ID__', id);
    document.getElementById('edit-editing-id').value = id;
    document.getElementById('edit-name').value = btn.dataset.name || '';
    document.getElementById('edit-email').value = btn.dataset.email || '';
    document.getElementById('edit-role').value = btn.dataset.role || '';
    document.getElementById('edit-abkid').value = btn.dataset.abkid || '';
    document.getElementById('edit-password').value = '';
    document.getElementById('edit-password-confirmation').value = '';
    toggleAbkEdit();
    document.getElementById('modalEditOverlay').classList.add('active');
}
function tutupModalEdit() {
    document.getElementById('modalEditOverlay').classList.remove('active');
}
function toggleAbkEdit() {
    const role = document.getElementById('edit-role').value;
    document.getElementById('editSectionAbk').style.display = role === 'orangtua' ? 'block' : 'none';
}
document.getElementById('modalEditOverlay').addEventListener('click', function (e) {
    if (e.target === this) tutupModalEdit();
});

// ===== Modal Detail =====
function bukaModalDetail(btn) {
    document.getElementById('detail-name').textContent = btn.dataset.name || '-';
    document.getElementById('detail-email').textContent = btn.dataset.email || '-';
    document.getElementById('detail-role').textContent = btn.dataset.role || '-';
    document.getElementById('detail-info').textContent = btn.dataset.info || '-';
    document.getElementById('detail-created').textContent = btn.dataset.created || '-';
    document.getElementById('modalDetailOverlay').classList.add('active');
}
function tutupModalDetail() {
    document.getElementById('modalDetailOverlay').classList.remove('active');
}
document.getElementById('modalDetailOverlay').addEventListener('click', function (e) {
    if (e.target === this) tutupModalDetail();
});

// ===== Reopen modal otomatis jika validasi gagal =====
@if($errors->any())
    @if(old('_form_type') === 'edit')
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('editForm').action = editUrlTemplate.replace('__ID__', @json(old('_editing_id', '')));
            document.getElementById('edit-editing-id').value = @json(old('_editing_id', ''));
            document.getElementById('edit-name').value = @json(old('name'));
            document.getElementById('edit-email').value = @json(old('email'));
            document.getElementById('edit-role').value = @json(old('role'));
            document.getElementById('edit-abkid').value = @json(old('abk_id'));
            toggleAbkEdit();
            document.getElementById('modalEditOverlay').classList.add('active');
        });
    @else
        document.addEventListener('DOMContentLoaded', bukaModalUser);
    @endif
@endif
</script>

@endsection