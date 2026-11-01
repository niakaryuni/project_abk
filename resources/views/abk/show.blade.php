@extends('layouts.app')

@section('title', 'Detail Siswa - '.$abk->nama)
@section('page-title', 'Detail Siswa ABK')

@section('content')

@php
    // Hanya Admin & Guru yang boleh mengubah data (Edit/Hapus/Input).
    // Orang Tua & Kepala Sekolah hanya bisa melihat (read-only).
    $isPengelola = auth()->user()->isAdmin() || auth()->user()->isGuru();
@endphp

{{-- Profile + Statistik --}}
<div style="display:grid;grid-template-columns:280px 1fr;gap:20px;margin-bottom:20px;align-items:start">

    {{-- Kartu Profil --}}
    <div class="card">
        <div class="card-body" style="text-align:center">
            <div style="width:80px;height:80px;background:var(--primary-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;color:var(--primary);font-size:32px;margin:0 auto 14px">
                {{ strtoupper(substr($abk->nama,0,1)) }}
            </div>
            <h3 style="font-size:17px;font-weight:700;margin-bottom:4px">{{ $abk->nama }}</h3>
            <span class="badge badge-primary" style="margin-bottom:10px;display:inline-block">
                {{ $abk->keterangan ?? 'Belum ada keterangan' }}
            </span>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px">
                <div style="background:var(--bg);border-radius:8px;padding:10px">
                    <div style="font-size:10px;color:var(--text-muted);font-weight:700;text-transform:uppercase">Umur</div>
                    <div style="font-size:22px;font-weight:800">{{ $abk->umur ?? '-' }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">tahun</div>
                </div>
                <div style="background:var(--bg);border-radius:8px;padding:10px">
                    <div style="font-size:10px;color:var(--text-muted);font-weight:700;text-transform:uppercase">Kelas</div>
                    <div style="font-size:22px;font-weight:800">{{ $abk->kelas ?? '-' }}</div>
                </div>
            </div>

            <div style="text-align:left;font-size:13px;color:var(--text-muted);margin-bottom:16px;padding:10px;background:var(--bg);border-radius:8px">
                <div><i class="fas fa-chalkboard-teacher" style="color:var(--primary);width:18px"></i> <strong>Guru:</strong> {{ $abk->guru?->name ?? '-' }}</div>
                <div style="margin-top:6px"><i class="fas fa-user-friends" style="color:var(--success);width:18px"></i> <strong>Orang Tua:</strong> {{ $abk->orangTua?->name ?? '-' }}</div>
            </div>

            @if($isPengelola)
            <div style="display:flex;gap:8px">
                <a href="{{ route('abk.edit', $abk) }}" class="btn btn-warning btn-sm" style="flex:1;justify-content:center">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('nilai.create') }}?abk_id={{ $abk->id }}" class="btn btn-success btn-sm" style="flex:1;justify-content:center">
                    <i class="fas fa-plus"></i> Input Nilai
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Rata-rata Perkembangan --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar" style="color:var(--primary);margin-right:8px"></i>Rata-rata Perkembangan</h3>
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
</div>

{{-- Grafik Perkembangan --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3><i class="fas fa-chart-line" style="color:var(--primary);margin-right:8px"></i>Grafik Perkembangan (90 Hari Terakhir)</h3>
    </div>
    <div class="card-body">
        <canvas id="grafikPerkembangan" height="100"></canvas>
    </div>
</div>

{{-- Nilai Akademik --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3><i class="fas fa-graduation-cap" style="color:var(--primary);margin-right:8px"></i>Nilai Akademik Per Semester</h3>
        @if($isPengelola)
        <a href="{{ route('nilai-akademik.create') }}?abk_id={{ $abk->id }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Input Nilai Akademik
        </a>
        @endif
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mata Pelajaran</th>
                    <th>Nilai Akhir</th>
                    <th>Predikat</th>
                    <th>Semester</th>
                    <th>Tahun Ajaran</th>
                    @if($isPengelola)
                    <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($nilaiAkademik->flatten() as $na)
                <tr>
                    <td style="color:var(--text-muted)">{{ $loop->iteration }}</td>
                    <td style="font-weight:600">{{ $na->mata_pelajaran }}</td>
                    <td>
                        <span style="font-size:18px;font-weight:800;color:{{ $na->nilai_akhir >= 75 ? 'var(--success)' : ($na->nilai_akhir >= 60 ? 'var(--warning)' : 'var(--danger)') }}">
                            {{ $na->nilai_akhir }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:700;font-size:16px;color:{{ $na->predikat=='A' ? 'var(--success)' : ($na->predikat=='B' ? 'var(--primary)' : ($na->predikat=='C' ? 'var(--warning)' : 'var(--danger)')) }}">
                            {{ $na->predikat ?? '-' }}
                        </span>
                    </td>
                    <td>Semester {{ $na->semester }}</td>
                    <td style="color:var(--text-muted)">{{ $na->tahun_ajaran }}</td>
                    @if($isPengelola)
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('nilai-akademik.edit', $na) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('nilai-akademik.destroy', $na) }}" method="POST" onsubmit="return confirm('Hapus nilai ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $isPengelola ? 7 : 6 }}" style="text-align:center;padding:32px;color:var(--text-muted)">
                        Belum ada nilai akademik
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Riwayat Perkembangan --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-star" style="color:var(--primary);margin-right:8px"></i>Riwayat Perkembangan ({{ $nilais->count() }} entri)</h3>
        @if($isPengelola)
        <a href="{{ route('nilai.create') }}?abk_id={{ $abk->id }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Input Perkembangan
        </a>
        @endif
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Program / Mata Pelajaran</th>
                    <th>Aspek</th>
                    <th>Nilai</th>
                    <th>Catatan</th>
                    <th>Tanggal</th>
                    @if($isPengelola)
                    <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($nilais as $n)
                <tr>
                    <td style="color:var(--text-muted)">{{ $loop->iteration }}</td>
                    <td style="font-weight:600;font-size:13px">{{ $n->mata_pelajaran }}</td>
                    <td><span class="badge badge-{{ $n->aspek }}">{{ ucfirst($n->aspek) }}</span></td>
                    <td>
                        <span style="font-size:18px;font-weight:800;color:{{ $n->nilai >= 75 ? 'var(--success)' : ($n->nilai >= 60 ? 'var(--warning)' : 'var(--danger)') }}">
                            {{ $n->nilai ?? '-' }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:var(--text-muted);max-width:180px">{{ Str::limit($n->catatan, 45) ?? '-' }}</td>
                    <td style="font-size:13px;color:var(--text-muted)">{{ \Carbon\Carbon::parse($n->tanggal)->isoFormat('D MMM Y') }}</td>
                    @if($isPengelola)
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('nilai.edit', $n) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('nilai.destroy', $n) }}" method="POST" onsubmit="return confirm('Hapus nilai ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $isPengelola ? 7 : 6 }}" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="fas fa-star" style="font-size:28px;display:block;margin-bottom:10px;opacity:0.3"></i>
                        Belum ada data perkembangan untuk siswa ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const grafikData = @json($grafikData);

// Kumpulkan semua tanggal unik
const semuaTanggal = [...new Set(
    Object.values(grafikData).flatMap(arr => arr.map(d => d.tanggal))
)].sort();

// Helper ambil nilai per tanggal per aspek
function nilaiPerTanggal(aspek, tanggal) {
    const arr = grafikData[aspek] || [];
    const found = arr.find(d => d.tanggal === tanggal);
    return found ? found.nilai : null;
}

const ctx = document.getElementById('grafikPerkembangan').getContext('2d');
new Chart(ctx, {
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
@endsection