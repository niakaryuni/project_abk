@extends('layouts.app')

@section('title', 'Detail Laporan')
@section('page-title', 'Detail Laporan')

@section('content')
<div class="card" style="max-width:700px;margin:0 auto">
    <div class="card-header">
        <h3><i class="fas fa-file-alt" style="color:var(--primary);margin-right:8px"></i>Detail Laporan</h3>
        <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-body">

        {{-- Info Siswa --}}
        <div style="display:flex;align-items:center;gap:14px;padding:16px;background:var(--primary-light);border-radius:10px;margin-bottom:20px">
            <div style="width:50px;height:50px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;color:white;font-size:20px;flex-shrink:0">
                {{ strtoupper(substr($laporan->abk->nama ?? 'S', 0, 1)) }}
            </div>
            <div>
                <div style="font-size:16px;font-weight:700">{{ $laporan->abk->nama ?? '-' }}</div>
                <div style="font-size:13px;color:var(--text-muted)">
                    Kelas {{ $laporan->abk->kelas ?? '-' }} ·
                    {{ $laporan->abk->umur ?? '-' }} tahun ·
                    {{ $laporan->abk->keterangan ?? '-' }}
                </div>
            </div>
        </div>

        {{-- Detail Laporan --}}
        <table style="width:100%;border-collapse:collapse;margin-bottom:20px">
            @foreach([
                'Jenis Laporan' => $laporan->labelJenis(),
                'Periode'       => $laporan->periode,
                'Semester'      => 'Semester '.$laporan->semester,
                'Tahun Ajaran'  => $laporan->tahun_ajaran,
                'Dibuat Oleh'   => $laporan->guru->name ?? '-',
                'Status'        => $laporan->status,
            ] as $label => $value)
            <tr style="border-bottom:1px solid var(--border)">
                <td style="padding:10px 0;font-size:13px;font-weight:600;color:var(--text-muted);width:140px">{{ $label }}</td>
                <td style="padding:10px 0;font-size:13px">
                    @if($label === 'Status')
                        @if($value === 'final')
                            <span class="badge badge-keterampilan">Final</span>
                        @else
                            <span class="badge badge-sosial">Draft</span>
                        @endif
                    @elseif($label === 'Jenis Laporan')
                        <span class="badge badge-keterampilan" style="background:var(--primary-light);color:var(--primary)">{{ $value }}</span>
                    @else
                        {{ $value }}
                    @endif
                </td>
            </tr>
            @endforeach
        </table>

        {{-- Ringkasan Laporan (Auto-generate dari data nilai) --}}
        @if($laporan->ringkasan)
        <div style="margin-bottom:20px">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px">
                <span style="font-size:13px;font-weight:600;color:var(--text-muted)">Ringkasan Laporan</span>
                <span style="font-size:11px;background:#eaf7ee;color:#1b7a4b;padding:2px 8px;border-radius:20px;font-weight:600">
                    <i class="fas fa-magic" style="margin-right:3px"></i>Auto-generate
                </span>
            </div>
            <div style="background:var(--bg);border-radius:8px;padding:16px;font-size:13.5px;line-height:1.8;white-space:pre-line">{{ $laporan->ringkasan }}</div>
        </div>
        @endif

        {{-- Tombol Aksi --}}
        <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:16px;border-top:1px solid var(--border)">
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            {{-- Unduh PDF: semua role bisa dari halaman show --}}
            <a href="{{ route('laporan.download', $laporan) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Unduh Laporan PDF
            </a>

            {{-- Edit: hanya Admin & Guru --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isGuru())
            <a href="{{ route('laporan.edit', $laporan) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endif
        </div>
    </div>
</div>
@endsection