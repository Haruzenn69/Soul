<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SOULERS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * , *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body { height: 100%; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

        /* ===================== LOGIN PAGE ===================== */
        .login-page {
            min-height: 100vh;
            min-height: 100svh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f3f4f6;
            overflow-x: hidden;
        }

        /* ===================== CONTAINER UTAMA ===================== */
        .login-container {
            width: 100%;
            max-width: 1240px;
            height: 720px;
            max-height: calc(100svh - 48px);
            display: flex;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 24px 60px -12px rgba(15, 23, 42, 0.18);
        }

        /* ===================== KOLOM KIRI: GAMBAR ===================== */
        .login-image {
            flex: 0 0 50%;
            width: 50%;
            position: relative;
            overflow: hidden;
        }
        .login-image img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* ===================== KOLOM KANAN: FORM ===================== */
        .login-content {
            flex: 0 0 50%;
            width: 50%;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 64px 72px;
            background: #ffffff;
        }

        .back-link {
            position: absolute;
            top: 24px;
            left: 32px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .back-link:hover { color: #0f172a; }

        /* ===================== BUNGKUS FORM ===================== */
        .login-form-wrapper {
            width: 100%;
            max-width: 420px;
            margin: auto;
        }

        .login-label {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 10px;
        }

        .login-title {
            font-size: clamp(2.2rem, 3.4vw, 3rem);
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -0.02em;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 28px;
        }

        /* ===================== FORM ===================== */
        form { display: block; }

        .form-group { margin-bottom: 14px; }
        .form-group input {
            width: 100%;
            height: 46px;
            padding: 0 16px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            background: #f8fafc;
            color: #0f172a;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.15s ease, background 0.15s ease, box-shadow 0.15s ease;
        }
        .form-group input::placeholder { color: #94a3b8; }
        .form-group input:focus {
            border-color: #2563EB;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .login-alert {
            width: 100%;
            margin-bottom: 14px;
            padding: 10px 14px;
            border: 1px solid #fecdd3;
            border-radius: 9px;
            background: #fff1f2;
            color: #e11d48;
            font-size: 13px;
            font-weight: 500;
        }

        .signup-text {
            margin-top: 14px;
            font-size: 14px;
            color: #64748b;
        }
        .signup-text a {
            font-weight: 600;
            color: #2563EB;
            text-decoration: none;
        }
        .signup-text a:hover { text-decoration: underline; }

        .login-button {
            width: 100%;
            height: 48px;
            margin-top: 20px;
            border: none;
            border-radius: 9px;
            background: #EAB308;
            color: #1f2937;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            box-shadow: 0 6px 16px -6px rgba(234, 179, 8, 0.5);
            transition: background 0.15s ease, transform 0.1s ease;
        }
        .login-button:hover { background: #ca8a04; color: #ffffff; }
        .login-button:active { transform: scale(0.99); }
        .login-button:focus-visible { outline: 2px solid #2563EB; outline-offset: 2px; }

        /* ===================== BRAND SECTION ===================== */
        .brand-section {
            position: absolute;
            bottom: 26px;
            left: 32px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .brand-logo {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }
        .brand-logo--hidden { visibility: hidden; }
        .brand-name {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.06em;
            color: #0f172a;
        }

        /* ===================== RESPONSIVE ===================== */
        @media (max-width: 960px) {
            .login-page { padding: 0; }
            .login-container {
                flex-direction: column;
                height: auto;
                max-height: none;
                min-height: 100svh;
                border-radius: 0;
            }
            .login-image {
                flex: none;
                width: 100%;
                height: 240px;
            }
            .login-image img { position: relative; }
            .login-content {
                width: 100%;
                flex: none;
                padding: 56px 28px 96px;
                justify-content: flex-start;
            }
            .login-form-wrapper { margin: 0 auto; }
        }

        @media (max-width: 480px) {
            .login-image { height: 180px; }
            .login-content { padding: 48px 20px 88px; }
            .back-link { left: 20px; }
            .brand-section { left: 20px; }
            .login-title { font-size: 2rem; }
        }
    </style>
</head>
<body class="login-page">

    <!-- ===================== CONTAINER UTAMA ===================== -->
    <div class="login-container">

        <!-- KOLOM KIRI: GAMBAR -->
        <div class="login-image">
            <img src="{{ asset('images/firefly.jpg') }}" alt="Login illustration">
        </div>

        <!-- KOLOM KANAN: FORM -->
        <div class="login-content">

            <a class="back-link" href="{{ url('/') }}">← Back</a>

            <div class="login-form-wrapper">
                <p class="login-label">Login to</p>

                <h1 class="login-title">HELLO<br>SOULERS</h1>
                <p class="login-subtitle">Masuk dan akses semua fitur kami</p>

                <!-- ALERT ERROR -->
                @if ($errors->any())
                    <div class="login-alert">{{ $errors->first() }}</div>
                @endif

                <!-- Form Input -->
                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Enter email" required autofocus>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" placeholder="Enter password" required>
                    </div>

                    <div class="signup-text">
                        Don't have an account?
                        <a href="#">Sign up</a>
                    </div>

                    <button type="submit" class="login-button">Masuk</button>
                </form>
            </div>

            <div class="brand-section">
                <img class="brand-logo" src="{{ asset('images/logo.png') }}" alt="Logo SOUL" onerror="this.onerror=null; this.classList.add('brand-logo--hidden');">
                <span class="brand-name">SOUL</span>
            </div>

        </div>

    </div>

</body>
</html>