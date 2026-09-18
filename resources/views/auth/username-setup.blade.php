<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Username - SOULERS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html, body { height: 100%; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

        .setup-page {
            min-height: 100vh;
            min-height: 100svh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f3f4f6;
            overflow-x: hidden;
        }

        .setup-container {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-radius: 12px;
            padding: 56px 40px 40px;
            box-shadow: 0 24px 60px -12px rgba(15, 23, 42, 0.18);
            text-align: center;
        }

        .setup-logo {
            width: 56px;
            height: 56px;
            margin: 0 auto 22px;
            border-radius: 16px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .setup-logo svg { width: 28px; height: 28px; color: #ffffff; }

        .setup-title {
            font-size: clamp(1.5rem, 2.8vw, 1.85rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.2;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .setup-subtitle {
            font-size: 14px;
            font-weight: 500;
            line-height: 1.6;
            color: #64748b;
            margin-bottom: 28px;
        }

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

        .setup-alert {
            width: 100%;
            margin-bottom: 14px;
            padding: 10px 14px;
            border: 1px solid #fecdd3;
            border-radius: 9px;
            background: #fff1f2;
            color: #e11d48;
            font-size: 13px;
            font-weight: 500;
            text-align: left;
        }

        .setup-hint {
            margin-top: 12px;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;
        }

        .setup-button {
            width: 100%;
            height: 48px;
            margin-top: 20px;
            border: none;
            border-radius: 9px;
            background: #2563EB;
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            box-shadow: 0 6px 16px -6px rgba(37, 99, 235, 0.5);
            transition: background 0.15s ease, transform 0.1s ease;
        }
        .setup-button:hover { background: #1d4ed8; }
        .setup-button:active { transform: scale(0.99); }
        .setup-button:focus-visible { outline: 2px solid #2563EB; outline-offset: 2px; }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .back-link:hover { color: #0f172a; }

        @media (max-width: 480px) {
            .setup-container { padding: 48px 24px 32px; }
        }
    </style>
</head>
<body class="setup-page">

    <div class="setup-container">

        <div class="setup-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
        </div>

        <h1 class="setup-title">Lengkapi Username</h1>
        <p class="setup-subtitle">Selamat datang! Sebelum masuk, pilih username yang unik supaya mudah digunakan untuk login berikutnya.</p>

        @if ($errors->any())
            <div class="setup-alert">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('username.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <input type="text" name="username" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus minlength="3" maxlength="255">
            </div>

            <button type="submit" class="setup-button">Simpan Username</button>
        </form>

        <p class="setup-hint">Username hanya boleh huruf, angka, titik (.), dan underscore (_).</p>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="back-link">Keluar</button>
        </form>

    </div>

</body>
</html>