<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $laporan->labelJenis() }} - {{ $abk->nama }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1a2b3c; }

        .header { text-align: center; border-bottom: 3px solid #1E6F9F; padding-bottom: 14px; margin-bottom: 20px; }
        .header h1 { font-size: 15px; font-weight: 700; color: #1E6F9F; text-transform: uppercase; letter-spacing: 1px; }
        .header h2 { font-size: 13px; font-weight: 600; margin: 4px 0; }
        .header p  { font-size: 11px; color: #6b7e90; }

        .info-box { background: #f0f8ff; border: 1px solid #bee3f8; border-radius: 4px; padding: 12px 16px; margin-bottom: 18px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 6px; font-size: 11.5px; }
        .info-table td:first-child { font-weight: 700; width: 140px; color: #4a5568; }
        .info-table td:nth-child(2) { width: 10px; }

        .section { margin-bottom: 20px; }
        .section-title { font-size: 12px; font-weight: 700; color: white; background: #1E6F9F; padding: 7px 12px; text-transform: uppercase; letter-spacing: 0.5px; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #e8f4fb; color: #1E6F9F; font-size: 11px; font-weight: 700; padding: 7px 10px; text-align: left; border: 1px solid #bee3f8; text-transform: uppercase; }
        td { padding: 7px 10px; border: 1px solid #dde5ed; font-size: 11px; vertical-align: middle; }
        tr:nth-child(even) td { background: #f8fbff; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; }
        .badge-motorik      { background: #d5e8fb; color: #1a6fb0; }
        .badge-keterampilan { background: #d5f5e3; color: #1a7a47; }
        .badge-sosial       { background: #fde8d5; color: #b05a1a; }

        .avg-wrap { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .avg-wrap td { text-align: center; padding: 12px; border: 1px solid #dde5ed; }
        .avg-num  { font-size: 28px; font-weight: 800; }
        .avg-label{ font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 3px; }

        .catatan-box { background: #fffbeb; border: 1px solid #fcd34d; padding: 12px; font-size: 11.5px; line-height: 1.6; white-space: pre-line; }

        .ttd-wrap { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .ttd-wrap td { text-align: center; width: 33%; padding: 10px; vertical-align: top; border: none; }
        .ttd-line { border-top: 1px solid #333; margin-top: 50px; margin-bottom: 4px; }

        .text-success { color: #27AE60; }
        .text-warning { color: #F39C12; }
        .text-danger  { color: #E74C3C; }
        .text-primary { color: #1E6F9F; }
        .text-muted   { color: #6b7e90; }
        .font-bold    { font-weight: 700; }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <h1>{{ $laporan->labelJenis() }} Siswa</h1>
    <h2>SKHN 01 Kabupaten Tangerang</h2>
    <p>Sistem Informasi Monitoring Perkembangan Belajar Anak Berkebutuhan Khusus (SIMBA ABK)</p>
</div>

{{-- INFO SISWA --}}
<div class="info-box">
    <table class="info-table">
        <tr><td>Nama Siswa</td><td>:</td><td><strong>{{ $abk->nama }}</strong></td></tr>
        <tr><td>Kelas</td><td>:</td><td>{{ $abk->kelas ?? '-' }}</td></tr>
        <tr><td>Umur</td><td>:</td><td>{{ $abk->umur ?? '-' }} tahun</td></tr>
        <tr><td>Jenis Kebutuhan</td><td>:</td><td>{{ $abk->keterangan ?? '-' }}</td></tr>
        <tr><td>Guru</td><td>:</td><td>{{ $abk->guru?->name ?? '-' }}</td></tr>
        <tr><td>Orang Tua / Wali</td><td>:</td><td>{{ $abk->orangTua?->name ?? '-' }}</td></tr>
        <tr><td>Jenis Laporan</td><td>:</td><td>{{ $laporan->labelJenis() }}</td></tr>
        <tr><td>Periode</td><td>:</td><td>{{ $laporan->periode }}</td></tr>
        <tr><td>Semester / T.A.</td><td>:</td><td>Semester {{ $laporan->semester }} / {{ $laporan->tahun_ajaran }}</td></tr>
        <tr><td>Dibuat Oleh</td><td>:</td><td>{{ $laporan->guru?->name ?? '-' }}</td></tr>
    </table>
</div>

{{-- ===== BAGIAN PERKEMBANGAN — hanya tampil jika jenis: perkembangan / gabungan ===== --}}
{{-- PERBAIKAN: 'lengkap' -> 'gabungan' agar sesuai dengan enum jenis_laporan di database --}}
@if(in_array($laporan->jenis_laporan, ['perkembangan', 'gabungan']))

{{-- RATA-RATA PERKEMBANGAN --}}
<div class="section">
    <div class="section-title">Rata-rata Perkembangan</div>
    <table class="avg-wrap">
        <tr>
            <td style="border:1px solid #bee3f8">
                <div class="avg-num text-primary">{{ number_format($avgMotorik, 1) }}</div>
                <div class="avg-label text-primary">Motorik</div>
            </td>
            <td style="border:1px solid #86efac">
                <div class="avg-num text-success">{{ number_format($avgKeterampilan, 1) }}</div>
                <div class="avg-label text-success">Keterampilan</div>
            </td>
            <td style="border:1px solid #fdba74">
                <div class="avg-num" style="color:#b05a1a;font-size:28px;font-weight:800">{{ number_format($avgSosial, 1) }}</div>
                <div class="avg-label" style="color:#b05a1a">Sosial</div>
            </td>
        </tr>
    </table>
</div>

{{-- NILAI PERKEMBANGAN --}}
<div class="section">
    <div class="section-title">Riwayat Nilai Perkembangan</div>
    <table>
        <thead>
            <tr>
                <th style="width:30px">#</th>
                <th>Program / Mata Pelajaran</th>
                <th style="width:100px">Aspek</th>
                <th style="width:50px">Nilai</th>
                <th>Catatan</th>
                <th style="width:90px">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($nilaiPerkembangan->flatten() as $n)
            <tr>
                <td>{{ $no++ }}</td>
                <td class="font-bold">{{ $n->mata_pelajaran }}</td>
                <td><span class="badge badge-{{ $n->aspek }}">{{ ucfirst($n->aspek) }}</span></td>
                <td class="font-bold @if($n->nilai >= 75) text-success @elseif($n->nilai >= 60) text-warning @else text-danger @endif">
                    {{ $n->nilai }}
                </td>
                <td class="text-muted">{{ $n->catatan ?? '-' }}</td>
                <td class="text-muted">{{ \Carbon\Carbon::parse($n->tanggal)->isoFormat('D MMM Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:16px;color:#6b7e90">
                    Belum ada data perkembangan
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endif

{{-- ===== BAGIAN AKADEMIK — hanya tampil jika jenis: akademik / gabungan ===== --}}
{{-- PERBAIKAN: 'lengkap' -> 'gabungan' agar sesuai dengan enum jenis_laporan di database --}}
@if(in_array($laporan->jenis_laporan, ['akademik', 'gabungan']) && $nilaiAkademik->count() > 0)
<div class="section">
    <div class="section-title">Nilai Akademik Semester {{ $laporan->semester }}</div>
    <table>
        <thead>
            <tr>
                <th style="width:30px">#</th>
                <th>Mata Pelajaran</th>
                <th style="width:80px">Nilai Akhir</th>
                <th style="width:70px">Predikat</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($nilaiAkademik as $i => $na)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="font-bold">{{ $na->mata_pelajaran }}</td>
                <td class="font-bold @if($na->nilai_akhir >= 75) text-success @elseif($na->nilai_akhir >= 60) text-warning @else text-danger @endif">
                    {{ $na->nilai_akhir }}
                </td>
                <td class="font-bold">{{ $na->predikat ?? '-' }}</td>
                <td class="text-muted">{{ $na->catatan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@elseif($laporan->jenis_laporan === 'akademik' && $nilaiAkademik->count() === 0)
<div class="section">
    <div class="section-title">Nilai Akademik Semester {{ $laporan->semester }}</div>
    <table>
        <tbody>
            <tr>
                <td style="text-align:center;padding:16px;color:#6b7e90">
                    Belum ada data nilai akademik untuk semester ini
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endif

{{-- CATATAN UMUM --}}
@if($laporan->ringkasan)
<div class="section">
    <div class="section-title">Catatan Umum Guru</div>
    <div class="catatan-box">{{ $laporan->ringkasan }}</div>
</div>
@endif

{{-- TANDA TANGAN --}}
<table class="ttd-wrap">
    <tr>
        <td>
            <p>Orang Tua / Wali</p>
            <div class="ttd-line"></div>
            <p class="font-bold">{{ $abk->orangTua?->name ?? '...........................' }}</p>
        </td>
        <td>
            <p>Tangerang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
            <p>Guru Kelas</p>
            <div class="ttd-line"></div>
            <p class="font-bold">{{ $abk->guru?->name ?? '...........................' }}</p>
        </td>
        <td>
            <p>Mengetahui,</p>
            <p>Kepala Sekolah</p>
            <div class="ttd-line"></div>
            <p class="font-bold">...........................</p>
        </td>
    </tr>
</table>

</body>
</html>