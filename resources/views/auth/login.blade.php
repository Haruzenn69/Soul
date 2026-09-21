<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In Akun - SOULERS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * , *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body { height: 100%; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #0f172a; }

        /* ===================== PAGE BACKGROUND ===================== */
        .login-page {
            min-height: 100vh;
            min-height: 100svh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            position: relative;
            overflow: hidden;
        }

        /* Background images - only one loads per viewport */
        .login-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .login-bg--desktop {
            display: block;
            background-image: url('{{ asset('images/firefly.jpg') }}');
        }
        .login-bg--mobile {
            display: none;
            background-image: url('{{ asset('images/firefly-mobile.jpeg') }}');
        }
        @media (max-width: 640px) {
            .login-bg--desktop { display: none; }
            .login-bg--mobile { display: block; }
        }

        /* Overlay gradient */
        .login-overlay {
            position: fixed;
            inset: 0;
            z-index: 1;
            background: linear-gradient(180deg, rgba(11, 26, 46, 0.55) 0%, rgba(11, 26, 46, 0.75) 50%, rgba(11, 26, 46, 0.9) 100%);
            pointer-events: none;
        }

        /* ===================== CARD WRAPPER ===================== */
        .login-card {
            display: flex;
            width: 100%;
            max-width: 980px;
            min-height: 580px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px -12px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        @media (max-width: 900px) {
            .login-card { min-height: auto; border-radius: 12px; }
        }

        /* ===================== KOLOM KIRI: INFORMASI/BRANDING ===================== */
        .login-left {
            flex: 0 0 40%;
            width: 40%;
            background: #DCEEFB;
            padding: 56px 44px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
        }

        @media (max-width: 900px) {
            .login-left { display: none; }
        }

        .login-left-brand {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #1E5AA8;
            margin-bottom: 40px;
        }

        .login-left-title {
            font-size: clamp(1.6rem, 2.4vw, 2.1rem);
            font-weight: 800;
            line-height: 1.15;
            color: #10243e;
            margin-bottom: 14px;
        }

        .login-left-desc {
            font-size: 14px;
            line-height: 1.6;
            color: #5a6b7f;
            margin-bottom: 34px;
        }

        .platform-list { display: flex; flex-direction: column; gap: 18px; }

        .platform-item {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .platform-icon {
            flex: 0 0 auto;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 14px -6px rgba(30, 90, 168, 0.25);
        }
        .platform-icon svg { color: #1E5AA8; }
        .platform-text h4 {
            font-size: 14px;
            font-weight: 700;
            color: #10243e;
            line-height: 1.3;
        }
        .platform-text p {
            font-size: 12px;
            color: #5a6b7f;
            margin-top: 2px;
        }

        /* Tombol accessibility pojok kiri bawah */
        .accessibility-btn {
            position: fixed;
            bottom: 24px;
            left: 24px;
            z-index: 50;
            width: 46px;
            height: 46px;
            border: none;
            border-radius: 50%;
            background: #1E5AA8;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 8px 18px -6px rgba(30, 90, 168, 0.55);
            transition: background 0.15s ease, transform 0.1s ease;
        }
        .accessibility-btn:hover { background: #16457f; }
        .accessibility-btn:active { transform: scale(0.95); }
        .accessibility-btn svg { color: #ffffff; }

        /* ===================== KOLOM KANAN: FORM LOGIN ===================== */
        .login-right {
            flex: 1 1 60%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px 32px;
            position: relative;
            background: #ffffff;
        }

        .login-form-wrap {
            width: 100%;
            max-width: 420px;
        }

        .beranda-link {
            position: absolute;
            top: 28px;
            left: 36px;
            font-size: 14px;
            font-weight: 600;
            color: #1E5AA8;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .beranda-link:hover { color: #16457f; }

        .login-title {
            font-size: clamp(1.7rem, 3vw, 2.1rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #10243e;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #5a6b7f;
            margin-bottom: 30px;
        }
        .login-subtitle .hashtag {
            color: #1E5AA8;
            font-weight: 700;
        }

        /* ===================== FORM ===================== */
        .form-group { margin-bottom: 18px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #10243e;
            margin-bottom: 8px;
        }
        .form-label .required { color: #e11d48; margin-left: 2px; }

        .input-wrap { position: relative; }

        .form-control {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            border: 1px solid transparent;
            border-radius: 10px;
            background: #F0F3F7;
            color: #0f172a;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control::placeholder { color: #94a3b8; }
        .form-control:focus {
            border-color: #1E5AA8;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(30, 90, 168, 0.12);
        }

        .password-input { padding-right: 48px; }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 8px;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 8px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
        }
        .password-toggle:hover { background: #e2e8f0; color: #0f172a; }

        .login-alert {
            margin-bottom: 16px;
            padding: 10px 14px;
            border: 1px solid #fecdd3;
            border-radius: 10px;
            background: #fff1f2;
            color: #e11d48;
            font-size: 13px;
            font-weight: 500;
        }

        /* Checkbox + lupa password */
        .form-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 4px 0 18px;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #475569;
            cursor: pointer;
        }
        .remember input { accent-color: #1E5AA8; width: 16px; height: 16px; cursor: pointer; }

        /* ===================== HINT: DEFAULT PASSWORD (eye-catching) ===================== */
        .form-hint {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            padding: 6px 10px;
            border-radius: 8px;
            background: #FFF7E6;
            border: 1px dashed #F0B429;
        }
        .form-hint svg {
            flex: 0 0 auto;
            color: #B45309;
        }
        .form-hint-text {
            font-size: 12px;
            color: #92400e;
        }
        .form-hint-text code {
            background: #FDE8B0;
            padding: 2px 7px;
            border-radius: 5px;
            color: #7c3e00;
            font-weight: 700;
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            letter-spacing: 0.02em;
        }

        /* ===================== TOMBOL LOGIN ===================== */
        .login-button {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 9999px;
            background: #1E5AA8;
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.06em;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            box-shadow: 0 12px 22px -10px rgba(30, 90, 168, 0.6);
            transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
        }
        .login-button:hover { background: #16457f; box-shadow: 0 16px 26px -10px rgba(30, 90, 168, 0.7); }
        .login-button:active { transform: scale(0.99); }

        /* ===================== RESPONSIVE ===================== */
        @media (max-width: 900px) {
            .login-right { flex: 1 1 100%; }
            .beranda-link { left: 20px; top: 20px; }
        }
        @media (max-width: 480px) {
            .login-page { padding: 16px; }
            .login-card { border-radius: 12px; }
            .login-right { padding: 40px 18px; }
            .accessibility-btn { width: 40px; height: 40px; bottom: 16px; left: 16px; }
        }
    </style>
</head>
<body>

<!-- Background layers -->
<div class="login-bg login-bg--desktop" aria-hidden="true"></div>
<div class="login-bg login-bg--mobile" aria-hidden="true"></div>
<div class="login-overlay" aria-hidden="true"></div>

<div class="login-page">

    <div class="login-card">

        <!-- ===================== KOLOM KIRI: INFORMASI ===================== -->
        <aside class="login-left">
            <div class="login-left-brand">SOUL</div>

            <h1 class="login-left-title">Satu Akun, Semua Ekskul</h1>
            <p class="login-left-desc">Hi Soulers! Sekarang akunmu bisa dipakai untuk seluruh ekstrakurikuler SMKN 11 Bandung. Daftar ekskul, isi presensi, dan pantau laporan hanya dalam satu akun.</p>

            <div class="platform-list">
                <div class="platform-item">
                    <span class="platform-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    </span>
                    <div class="platform-text">
                        <h4>Katalog Ekskul</h4>
                        <p>Jelajahi dan daftar ekskul impianmu</p>
                    </div>
                </div>

                <div class="platform-item">
                    <span class="platform-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                    </span>
                    <div class="platform-text">
                        <h4>Dashboard Ketua</h4>
                        <p>Kelola anggota dan kegiatan ekskul</p>
                    </div>
                </div>

                <div class="platform-item">
                    <span class="platform-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 14l4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
                    </span>
                    <div class="platform-text">
                        <h4>Presensi Online</h4>
                        <p>Isi kehadiran setiap kegiatan</p>
                    </div>
                </div>

                <div class="platform-item">
                    <span class="platform-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <div class="platform-text">
                        <h4>Pembina Ekskul</h4>
                        <p>Pantau presensi dan kegiatan binaan</p>
                    </div>
                </div>

                <div class="platform-item">
                    <span class="platform-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m7 14 4-4 4 3 5-6"/></svg>
                    </span>
                    <div class="platform-text">
                        <h4>Laporan Real-Time</h4>
                        <p>Rekap kegiatan dan statistik kehadiran</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Tombol accessibility -->
        <button type="button" class="accessibility-btn" aria-label="Accessibility">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 8v8M8 12h8"/></svg>
        </button>

        <!-- ===================== KOLOM KANAN: FORM LOGIN ===================== -->
        <main class="login-right">
            <a class="beranda-link" href="{{ url('/') }}">‹ Beranda</a>

            <div class="login-form-wrap">
                <h1 class="login-title">Log In Akun</h1>
                <p class="login-subtitle">Hi, Selamat Datang <span class="hashtag">#SOULERS</span></p>

                @if ($errors->any())
                    <div class="login-alert">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('login') }}" method="POST" novalidate>
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="email">Email<span class="required">*</span></label>
                        <input class="form-control" id="email" type="text" name="email" value="{{ old('email') }}" placeholder="Masukkan email atau no. handphone" required autofocus>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password<span class="required">*</span></label>
                        <div class="input-wrap">
                            <input class="form-control password-input" id="password" type="password" name="password" placeholder="Masukkan password" required>
                            <button type="button" class="password-toggle" id="passwordToggle" aria-label="Tampilkan password">
                                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="m3 3 18 18"/><path d="M10.6 10.6a3 3 0 0 0 4.2 4.2"/><path d="M9.9 5.2A10.4 10.4 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-3.2 3.9M6.2 6.2A16.5 16.5 0 0 0 2 12s3.5 7 10 7c1.5 0 2.9-.4 4.1-1"/></svg>
                            </button>
                        </div>

                        <div class="form-hint">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            <span class="form-hint-text">Password default: <code>password</code></span>
                        </div>
                    </div>

                    <div class="form-meta">
                        <label class="remember"><input type="checkbox" name="remember" @checked(old('remember'))> Ingat Saya</label>
                    </div>

                    <button type="submit" class="login-button">LOGIN</button>
                </form>
            </div>
        </main>

    </div>

</div>

<script>
    document.getElementById('passwordToggle').addEventListener('click', function () {
        var pw = document.getElementById('password');
        var open = document.getElementById('eyeOpen');
        var closed = document.getElementById('eyeClosed');
        var show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        if (open && closed) {
            open.style.display = show ? 'none' : '';
            closed.style.display = show ? '' : 'none';
        }
    });
</script>

</body>
</html>