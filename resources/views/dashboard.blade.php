@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

@php $user = auth()->user(); @endphp

{{-- ═══════════════════════════════════════════════
     TAMPILAN ORANG TUA
     Hanya lihat data anak sendiri
═══════════════════════════════════════════════ --}}
@if($user->isOrangTua())

<div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-child"></i></div>
        <div class="stat-label">Anak Saya</div>
        <div class="stat-value">{{ $totalAbk }}</div>
        <div style="font-size:12px;color:var(--text-muted)">Terdaftar</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-star"></i></div>
        <div class="stat-label">Total Penilaian</div>
        <div class="stat-value">{{ $totalNilai }}</div>
        <div style="font-size:12px;color:var(--text-muted)">Entri penilaian</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="fas fa-file-alt"></i></div>
        <div class="stat-label">Laporan Final</div>
        <div class="stat-value">{{ $totalLaporan }}</div>
        <div style="font-size:12px;color:var(--text-muted)">Dapat diunduh</div>
    </div>
</div>

{{-- Rata-rata perkembangan anak --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar" style="color:var(--primary);margin-right:8px"></i>Perkembangan Anak Saya</h3>
        </div>
        <div class="card-body">
            <div style="margin-bottom:20px">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                    <span style="font-size:13px;font-weight:600">🏃 Motorik</span>
                    <span style="font-size:13px;font-weight:700;color:var(--primary)">{{ number_format($avgMotorik,1) }}/100</span>
                </div>
                <div class="progress"><div class="progress-bar blue" style="width:{{ $avgMotorik }}%"></div></div>
            </div>
            <div style="margin-bottom:20px">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                    <span style="font-size:13px;font-weight:600">🤲 Keterampilan</span>
                    <span style="font-size:13px;font-weight:700;color:var(--success)">{{ number_format($avgKeterampilan,1) }}/100</span>
                </div>
                <div class="progress"><div class="progress-bar green" style="width:{{ $avgKeterampilan }}%"></div></div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                    <span style="font-size:13px;font-weight:600">🤝 Sosial</span>
                    <span style="font-size:13px;font-weight:700;color:var(--accent-dark)">{{ number_format($avgSosial,1) }}/100</span>
                </div>
                <div class="progress"><div class="progress-bar orange" style="width:{{ $avgSosial }}%"></div></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-child" style="color:var(--primary);margin-right:8px"></i>Data Anak Saya</h3>
        </div>
        <div class="card-body" style="padding:0">
            @forelse($recentAbk as $abk)
            <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid var(--border)">
                <div style="width:42px;height:42px;background:var(--primary-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--primary);font-size:16px;flex-shrink:0">
                    {{ strtoupper(substr($abk->nama,0,1)) }}
                </div>
                <div style="flex:1">
                    <div style="font-size:13.5px;font-weight:600">{{ $abk->nama }}</div>
                    <div style="font-size:12px;color:var(--text-muted)">Kelas {{ $abk->kelas ?? '-' }} · {{ $abk->umur ?? '-' }} tahun · {{ $abk->keterangan ?? '-' }}</div>
                    <div style="font-size:12px;color:var(--text-muted)">Guru: {{ $abk->guru?->name ?? '-' }}</div>
                </div>
                <a href="{{ route('abk.show', $abk) }}" class="btn btn-sm btn-primary">Detail</a>
            </div>
            @empty
            <div style="padding:24px;text-align:center;color:var(--text-muted)">Belum ada data</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Penilaian terbaru anak --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-history" style="color:var(--primary);margin-right:8px"></i>Penilaian Terbaru Anak Saya</h3>
        <a href="{{ route('nilai.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Program / Mata Pelajaran</th>
                    <th>Aspek</th>
                    <th>Nilai</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentNilai as $n)
                <tr>
                    <td><div style="font-weight:600">{{ $n->abk->nama ?? '-' }}</div></td>
                    <td>{{ $n->mata_pelajaran }}</td>
                    <td><span class="badge badge-{{ $n->aspek }}">{{ ucfirst($n->aspek) }}</span></td>
                    <td>
                        <span style="font-weight:700;font-size:16px;color:{{ $n->nilai >= 75 ? 'var(--success)' : ($n->nilai >= 60 ? 'var(--warning)' : 'var(--danger)') }}">
                            {{ $n->nilai ?? '-' }}
                        </span>
                    </td>
                    <td style="color:var(--text-muted);font-size:13px">{{ \Carbon\Carbon::parse($n->tanggal)->isoFormat('D MMM Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--text-muted)">Belum ada data penilaian</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     TAMPILAN ADMIN, GURU, KEPALA SEKOLAH
═══════════════════════════════════════════════ --}}
@else

<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-child"></i></div>
        <div class="stat-label">Total Siswa ABK</div>
        <div class="stat-value">{{ $totalAbk }}</div>
        <div style="font-size:12px;color:var(--text-muted)">Terdaftar aktif</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-star"></i></div>
        <div class="stat-label">Total Data Nilai</div>
        <div class="stat-value">{{ $totalNilai }}</div>
        <div style="font-size:12px;color:var(--text-muted)">Entri penilaian</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="fas fa-running"></i></div>
        <div class="stat-label">Penilaian Motorik</div>
        <div class="stat-value">{{ $motorik }}</div>
        <div style="font-size:12px;color:var(--text-muted)">Rata-rata: {{ number_format($avgMotorik,1) }}</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon red"><i class="fas fa-hands"></i></div>
        <div class="stat-label">Penilaian Keterampilan</div>
        <div class="stat-value">{{ $keterampilan }}</div>
        <div style="font-size:12px;color:var(--text-muted)">Rata-rata: {{ number_format($avgKeterampilan,1) }}</div>
    </div>
</div>

{{-- Tambahan stat untuk Admin & Kepala Sekolah --}}
@if($user->isAdmin() || $user->isKepalaSekolah())
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-top:-10px">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-label">Total Guru</div>
        <div class="stat-value">{{ $totalGuru }}</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-users"></i></div>
        <div class="stat-label">Total Orang Tua</div>
        <div class="stat-value">{{ $totalOrtu }}</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="fas fa-file-alt"></i></div>
        <div class="stat-label">Total Laporan</div>
        <div class="stat-value">{{ $totalLaporan }}</div>
    </div>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar" style="color:var(--primary);margin-right:8px"></i>Rata-rata Nilai per Aspek</h3>
        </div>
        <div class="card-body">
            <div style="margin-bottom:20px">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                    <span style="font-size:13px;font-weight:600">🏃 Motorik</span>
                    <span style="font-size:13px;font-weight:700;color:var(--primary)">{{ number_format($avgMotorik,1) }}/100</span>
                </div>
                <div class="progress"><div class="progress-bar blue" style="width:{{ $avgMotorik }}%"></div></div>
            </div>
            <div style="margin-bottom:20px">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                    <span style="font-size:13px;font-weight:600">🤲 Keterampilan</span>
                    <span style="font-size:13px;font-weight:700;color:var(--success)">{{ number_format($avgKeterampilan,1) }}/100</span>
                </div>
                <div class="progress"><div class="progress-bar green" style="width:{{ $avgKeterampilan }}%"></div></div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                    <span style="font-size:13px;font-weight:600">🤝 Sosial</span>
                    <span style="font-size:13px;font-weight:700;color:var(--accent-dark)">{{ number_format($avgSosial,1) }}/100</span>
                </div>
                <div class="progress"><div class="progress-bar orange" style="width:{{ $avgSosial }}%"></div></div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-user-clock" style="color:var(--primary);margin-right:8px"></i>Siswa Terbaru</h3>
            <a href="{{ route('abk.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding:0">
            @forelse($recentAbk as $abk)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid var(--border)">
                <div style="width:38px;height:38px;background:var(--primary-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--primary);font-size:15px;flex-shrink:0">
                    {{ strtoupper(substr($abk->nama,0,1)) }}
                </div>
                <div style="flex:1">
                    <div style="font-size:13.5px;font-weight:600">{{ $abk->nama }}</div>
                    <div style="font-size:12px;color:var(--text-muted)">Kelas {{ $abk->kelas ?? '-' }} · {{ $abk->umur ?? '-' }} tahun</div>
                </div>
                <a href="{{ route('abk.show', $abk) }}" class="btn btn-sm btn-primary">Detail</a>
            </div>
            @empty
            <div style="padding:24px;text-align:center;color:var(--text-muted)">Belum ada data siswa</div>
            @endforelse
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-history" style="color:var(--primary);margin-right:8px"></i>Penilaian Terbaru</h3>
        <a href="{{ route('nilai.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Mata Pelajaran</th>
                    <th>Aspek</th>
                    <th>Nilai</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentNilai as $n)
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $n->abk->nama ?? '-' }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">Kelas {{ $n->abk->kelas ?? '-' }}</div>
                    </td>
                    <td>{{ $n->mata_pelajaran }}</td>
                    <td><span class="badge badge-{{ $n->aspek }}">{{ ucfirst($n->aspek) }}</span></td>
                    <td>
                        <span style="font-weight:700;font-size:16px;color:{{ $n->nilai >= 75 ? 'var(--success)' : ($n->nilai >= 60 ? 'var(--warning)' : 'var(--danger)') }}">
                            {{ $n->nilai ?? '-' }}
                        </span>
                    </td>
                    <td style="color:var(--text-muted);font-size:13px">{{ \Carbon\Carbon::parse($n->tanggal)->isoFormat('D MMM Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--text-muted)">Belum ada data penilaian</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endif
@endsection