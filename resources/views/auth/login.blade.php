<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SIMBA ABK - SKHN 01 Kab. Tangerang</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0D2137;
        }

        /* ── KIRI: Foto Sekolah ── */
        .login-left {
            flex: 1;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
        }

        /* Slideshow foto */
        .slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.2s ease-in-out;
        }
        .slide.active { opacity: 1; }
        .slide-1 { background-image: url('{{ asset("images/skhn01.jpeg") }}'); }
        .slide-2 { background-image: url('{{ asset("images/foto_skhn_01_kab_tangerang.jpeg") }}'); }

        /* Overlay gelap agar teks terbaca */
        .slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(13,33,55,0.3) 0%,
                rgba(13,33,55,0.7) 60%,
                rgba(13,33,55,0.92) 100%
            );
            z-index: 1;
        }

        /* Konten di atas foto */
        .left-content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
            padding: 40px;
            width: 100%;
        }
        .left-content h1 {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 1px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.4);
        }

        /* Dot indikator slideshow */
        .slide-dots {
            position: relative;
            z-index: 2;
            display: flex;
            gap: 8px;
            justify-content: center;
            padding: 16px 0 32px;
        }
        .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.4);
            cursor: pointer;
            transition: all 0.3s;
        }
        .dot.active { background: white; width: 22px; border-radius: 4px; }

        /* ── KANAN: Form Login ── */
        .login-right {
            width: 460px;
            background: #F0F4F8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .login-box { width: 100%; }

        /* Logo Pendidikan & Sekolah */
        .logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            margin-bottom: 28px;
        }
        .logo-row img {
            height: 56px;
            width: auto;
            object-fit: contain;
        }
        .logo-divider {
            width: 1px;
            height: 40px;
            background: #DDE5ED;
        }

        .login-box h2 { font-size: 26px; font-weight: 800; color: #1A2B3C; margin-bottom: 6px; text-align: center; }
        .login-box .subtitle { color: #6B7E90; font-size: 14px; margin-bottom: 32px; text-align: center; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 600; color: #1A2B3C; margin-bottom: 6px; }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%); color: #6B7E90; font-size: 15px;
        }
        .form-control {
            width: 100%; padding: 12px 14px 12px 42px;
            border: 1.5px solid #DDE5ED; border-radius: 10px;
            font-family: inherit; font-size: 14px; color: #1A2B3C;
            background: white; transition: all 0.2s;
        }
        .form-control:focus { outline: none; border-color: #1E6F9F; box-shadow: 0 0 0 3px rgba(30,111,159,0.1); }
        .is-invalid { border-color: #E74C3C !important; }
        .invalid-feedback { color: #E74C3C; font-size: 12px; margin-top: 4px; }
        .remember-row { display: flex; align-items: center; margin-bottom: 24px; }
        .remember-label { display: flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer; }
        .btn-login {
            width: 100%; padding: 13px;
            background: #1E6F9F; color: white;
            border: none; border-radius: 10px;
            font-family: inherit; font-size: 15px; font-weight: 700;
            cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover { background: #155a82; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(30,111,159,0.3); }

        @media (max-width: 900px) {
            .login-left { display: none; }
            .login-right { width: 100%; }
        }
    </style>
</head>
<body>

    {{-- ═══ KIRI: Foto Sekolah ═══ --}}
    <div class="login-left">
        <div class="slide slide-1 active"></div>
        <div class="slide slide-2"></div>
        <div class="slide-overlay"></div>

        <div class="left-content">
            <h1>SIMBA ABK</h1>
            <p class="sekolah-name">SEKOLAH KHUSUS NEGERI 01 KAB. TANGERANG</p>
        </div>

        <div class="slide-dots">
            <div class="dot active" onclick="goSlide(0)"></div>
            <div class="dot" onclick="goSlide(1)"></div>
        </div>
    </div>

    {{-- ═══ KANAN: Form Login ═══ --}}
    <div class="login-right">
        <div class="login-box">

            {{-- Logo Pendidikan & Sekolah --}}
            <div class="logo-row">
                <img src="{{ asset('images/logo-kemendikbud.png') }}" alt="Logo Kemendikbud">
                <div class="logo-divider"></div>
                <img src="{{ asset('images/logo-skhn01.png') }}" alt="Logo SKHN 01 Kab. Tangerang">
            </div>

            <h2>Selamat Datang di SIMBA ABK</h2>
            <p class="subtitle">Sistem Informasi Monitoring Perkembangan Belajar Anak Berkebutuhan Khusus</p>

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <div class="input-wrap">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="contoh@skhn01.sch.id"
                               value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember">
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Masuk ke Sistem
                </button>
            </form>
        </div>
    </div>

    <script>
        const slides = document.querySelectorAll('.slide');
        const dots   = document.querySelectorAll('.dot');
        let current  = 0;
        let timer;

        function goSlide(n) {
            slides[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = n;
            slides[current].classList.add('active');
            dots[current].classList.add('active');
            clearInterval(timer);
            timer = setInterval(nextSlide, 5000);
        }

        function nextSlide() {
            goSlide((current + 1) % slides.length);
        }

        timer = setInterval(nextSlide, 5000);
    </script>
</body>
</html>