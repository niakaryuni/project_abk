<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMBA ABK') | SKHN 01 Kab. Tangerang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #1E6F9F;
            --primary-dark: #155a82;
            --primary-light: #e8f4fb;
            --accent: #F4A83A;
            --accent-dark: #d4882a;
            --success: #27AE60;
            --danger: #E74C3C;
            --warning: #F39C12;
            --info: #2980B9;
            --bg: #F0F4F8;
            --sidebar: #0D2137;
            --sidebar-text: #A8BFCF;
            --card: #FFFFFF;
            --text: #1A2B3C;
            --text-muted: #6B7E90;
            --border: #DDE5ED;
            --radius: 12px;
            --shadow: 0 2px 12px rgba(30,111,159,0.08);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh; }

        /* ── SIDEBAR ── */
        .sidebar { width: 260px; min-height: 100vh; background: var(--sidebar); position: fixed; top: 0; left: 0; z-index: 100; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 24px 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.07); }
        .sidebar-brand .logo-icon { width: 44px; height: 44px; background: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: white; margin-bottom: 10px; }
        .sidebar-brand h2 { color: white; font-size: 15px; font-weight: 700; line-height: 1.2; }
        .sidebar-brand p { color: var(--sidebar-text); font-size: 11px; margin-top: 3px; }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section { margin-bottom: 20px; }
        .nav-section-label { color: var(--sidebar-text); font-size: 10px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; padding: 0 8px; margin-bottom: 6px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; color: var(--sidebar-text); text-decoration: none; font-size: 13.5px; font-weight: 500; transition: all 0.2s; margin-bottom: 2px; }
        .nav-item i { width: 18px; text-align: center; font-size: 14px; }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: white; }
        .nav-item.active { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(30,111,159,0.4); }
        .nav-item-sub { display: flex; align-items: center; gap: 10px; padding: 8px 12px 8px 32px; border-radius: 8px; color: var(--sidebar-text); text-decoration: none; font-size: 12.5px; font-weight: 500; transition: all 0.2s; margin-bottom: 2px; }
        .nav-item-sub:hover { background: rgba(255,255,255,0.07); color: white; }
        .nav-item-sub.active { background: rgba(30,111,159,0.5); color: white; }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,0.07); }
        .user-info { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; background: rgba(255,255,255,0.05); }
        .user-avatar { width: 36px; height: 36px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; font-size: 14px; flex-shrink: 0; }
        .user-info .name { color: white; font-size: 13px; font-weight: 600; }
        .user-info .role { color: var(--sidebar-text); font-size: 11px; }

        /* ── MAIN ── */
        .main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; }
        .topbar { background: var(--card); padding: 0 28px; height: 64px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 50; box-shadow: var(--shadow); }
        .topbar-title { font-size: 18px; font-weight: 700; color: var(--text); }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .btn-logout { padding: 7px 16px; border-radius: 8px; background: var(--danger); color: white; border: none; font-family: inherit; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
        .btn-logout:hover { background: #c0392b; }
        .page-content { padding: 28px; flex: 1; }

        /* ── ALERTS ── */
        .alert { padding: 14px 18px; border-radius: var(--radius); margin-bottom: 20px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid var(--success); }
        .alert-danger  { background: #f8d7da; color: #721c24; border-left: 4px solid var(--danger); }

        /* ── CARD ── */
        .card { background: var(--card); border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border); overflow: hidden; }
        .card-header { padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: var(--card); }
        .card-header h3 { font-size: 15px; font-weight: 700; color: var(--text); }
        .card-body { padding: 24px; }

        /* ── TABLE ── */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 12px 16px; background: var(--bg); color: var(--text-muted); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; text-align: left; border-bottom: 1px solid var(--border); }
        td { padding: 13px 16px; font-size: 14px; color: var(--text); border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: var(--primary-light); }

        /* ── BADGES ── */
        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11.5px; font-weight: 600; }
        .badge-motorik      { background: #d5e8fb; color: #1a6fb0; }
        .badge-keterampilan { background: #d5f5e3; color: #1a7a47; }
        .badge-sosial       { background: #fde8d5; color: #b05a1a; }
        .badge-primary      { background: var(--primary-light); color: var(--primary); }

        /* ── BUTTONS ── */
        .btn { padding: 9px 20px; border-radius: 8px; font-family: inherit; font-size: 13.5px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 7px; transition: all 0.2s; text-decoration: none; }
        .btn-primary   { background: var(--primary); color: white; }
        .btn-primary:hover   { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30,111,159,0.3); }
        .btn-warning   { background: var(--warning); color: white; }
        .btn-warning:hover   { background: #d68910; }
        .btn-danger    { background: var(--danger); color: white; }
        .btn-danger:hover    { background: #c0392b; }
        .btn-success   { background: var(--success); color: white; }
        .btn-success:hover   { background: #1e8449; }
        .btn-secondary { background: var(--bg); color: var(--text); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--border); }
        .btn-sm { padding: 6px 13px; font-size: 12.5px; }

        /* ── FORMS ── */
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1.5px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 14px; color: var(--text); background: white; transition: border-color 0.2s; }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,111,159,0.1); }
        .form-control.is-invalid { border-color: var(--danger); }
        .invalid-feedback { color: var(--danger); font-size: 12px; margin-top: 4px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        textarea.form-control { resize: vertical; min-height: 100px; }

        /* ── STAT CARDS ── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 24px; }
        .stat-card { background: var(--card); border-radius: var(--radius); padding: 20px 22px; border: 1px solid var(--border); box-shadow: var(--shadow); position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
        .stat-card.blue::before   { background: var(--primary); }
        .stat-card.green::before  { background: var(--success); }
        .stat-card.orange::before { background: var(--accent); }
        .stat-card.red::before    { background: var(--danger); }
        .stat-label { font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 32px; font-weight: 800; color: var(--text); margin: 6px 0 4px; line-height: 1; }
        .stat-icon { position: absolute; top: 16px; right: 18px; width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .stat-icon.blue   { background: var(--primary-light); color: var(--primary); }
        .stat-icon.green  { background: #d4edda; color: var(--success); }
        .stat-icon.orange { background: #fff3cd; color: var(--accent-dark); }
        .stat-icon.red    { background: #f8d7da; color: var(--danger); }

        /* ── PROGRESS ── */
        .progress { background: var(--bg); border-radius: 6px; height: 8px; overflow: hidden; }
        .progress-bar { height: 100%; border-radius: 6px; transition: width 0.5s; }
        .progress-bar.blue   { background: var(--primary); }
        .progress-bar.green  { background: var(--success); }
        .progress-bar.orange { background: var(--accent); }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-260px); }
            .main-content { margin-left: 0; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════════════ SIDEBAR ═══════════════════════ --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-icon"><i class="fas fa-graduation-cap"></i></div>
        <h2>SIMBA ABK</h2>
        <p>SKHN 01 Kab. Tangerang</p>
    </div>

    <nav class="sidebar-nav">

        {{-- MENU UTAMA --}}
        <div class="nav-section">
            <div class="nav-section-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </div>

        {{-- DATA SISWA --}}
        <div class="nav-section">
            <div class="nav-section-label">Data Siswa</div>
            <a href="{{ route('abk.index') }}"
               class="nav-item {{ request()->routeIs('abk.index','abk.show','abk.create','abk.edit') ? 'active' : '' }}">
                <i class="fas fa-child"></i> Data Siswa ABK
            </a>
        </div>

        {{-- PENILAIAN: Admin & Guru --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isGuru())
        <div class="nav-section">
            <div class="nav-section-label">Penilaian</div>
            <a href="{{ route('nilai.index') }}"
               class="nav-item {{ request()->routeIs('nilai.index','nilai.create','nilai.edit') ? 'active' : '' }}">
                <i class="fas fa-star"></i> Nilai Perkembangan
            </a>
            <a href="{{ route('nilai-akademik.index') }}"
               class="nav-item {{ request()->routeIs('nilai-akademik.index','nilai-akademik.create','nilai-akademik.edit') ? 'active' : '' }}"
               style="margin-top:4px">
                <i class="fas fa-graduation-cap"></i> Nilai Akademik
            </a>
        </div>
        @endif

        {{-- MONITORING: Kepala Sekolah & Orang Tua --}}
        @if(auth()->user()->isKepalaSekolah() || auth()->user()->isOrangTua())
        <div class="nav-section">
            <div class="nav-section-label">Monitoring</div>
            <a href="{{ route('nilai.index') }}"
               class="nav-item {{ request()->routeIs('nilai.index') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Lihat Perkembangan
            </a>
            <a href="{{ route('nilai-akademik.index') }}"
               class="nav-item {{ request()->routeIs('nilai-akademik.index') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap"></i> Lihat Nilai Akademik
            </a>
        </div>
        @endif

        {{-- LAPORAN: semua role --}}
        <div class="nav-section">
            <div class="nav-section-label">Laporan</div>
            <a href="{{ route('laporan.index') }}"
               class="nav-item {{ request()->routeIs('laporan.index','laporan.create','laporan.edit') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Data Laporan
            </a>
        </div>

        {{-- MANAJEMEN: hanya Admin --}}
        @if(auth()->user()->isAdmin())
        <div class="nav-section">
            <div class="nav-section-label">Manajemen</div>
            <a href="{{ route('users.index') }}"
               class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> Kelola User
            </a>
        </div>
        @endif

    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role">
                    @php
                        $roleLabel = [
                            'admin'          => 'Administrator',
                            'guru'           => 'Guru',
                            'kepala_sekolah' => 'Kepala Sekolah',
                            'orangtua'       => 'Orang Tua',
                        ];
                    @endphp
                    {{ $roleLabel[auth()->user()->role] ?? ucfirst(auth()->user()->role) }}
                </div>
            </div>
        </div>
    </div>
</aside>

{{-- ═══════════════════════ MAIN CONTENT ═══════════════════════ --}}
<div class="main-content">

    {{-- TOPBAR --}}
    <header class="topbar">
        <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
        <div class="topbar-right">
            <span style="font-size:13px;color:var(--text-muted)">
                {{ now()->isoFormat('dddd, D MMMM Y') }}
            </span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </form>
        </div>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="page-content">
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </main>

</div>

@stack('scripts')
</body>
</html>