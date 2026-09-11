<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOUL — Platform Ekstrakurikuler Sekolah</title>
    <meta name="description" content="SOUL menyatukan pendaftaran, presensi, dan laporan seluruh ekstrakurikuler sekolah dalam satu platform.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>
    @php($accountUrl = auth()->check() ? route('dashboard') : route('login'))

    <!-- ===================== HEADER ===================== -->
    <header class="site-header">
        <nav>
            <a href="/" class="header-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                <span class="header-logo-text">SOUL</span>
            </a>

            <div class="header-actions">
                <div class="header-nav-links">
                    <a href="#ekskul" class="header-nav-link">Daftar Ekskul</a>
                    <a href="#features" class="header-nav-link">Fitur</a>
                    <a href="#community" class="header-nav-link">Tentang Kami</a>
                </div>

                <button class="menu-btn" onclick="openSheet()" type="button" aria-label="Menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
            </div>
        </nav>
    </header>

    <!-- ===================== MOBILE SHEET ===================== -->
    <div class="sheet-overlay" id="sheetOverlay" onclick="closeSheet()"></div>
    <div class="sheet-panel" id="sheetPanel">
        <div class="sheet-header">
            <button class="sheet-close" onclick="closeSheet()" type="button" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <div class="sheet-links">
            <a href="#ekskul" class="sheet-link" onclick="closeSheet()">Daftar Ekskul</a>
            <a href="#features" class="sheet-link" onclick="closeSheet()">Fitur</a>
            <a href="#community" class="sheet-link" onclick="closeSheet()">Tentang Kami</a>
        </div>
        <div class="sheet-footer">
            <a href="{{ $accountUrl }}" class="btn-outline">Sign In</a>
            <a href="{{ $accountUrl }}" class="btn-primary">Get Started</a>
        </div>
    </div>

    <!-- ===================== HERO ===================== -->
    <section class="hero-section">
        <div class="hero-bg hero-bg--desktop" style="background-image: url('{{ asset('images/firefly.jpg') }}');" aria-hidden="true"></div>
        <div class="hero-bg hero-bg--mobile" style="background-image: url('{{ asset('images/firefly-mobile.jpeg') }}');" aria-hidden="true"></div>
        <div class="hero-overlay" aria-hidden="true"></div>

        <div class="hero-content">

            <h1 class="hero-title" aria-label="Temukan komunitas terbaikmu disini">
                <span class="word" style="--delay: 0.1s; --rotate: -1deg; --scale: 1; --y: 0;">Temukan <span class="highlight">komunitas</span></span>
                <span class="word" style="--delay: 0.2s; --rotate: 1deg; --scale: 1; --y: 0;">terbaikmu disini</span>
            </h1>

            <p class="hero-desc">
                Satu platform untuk pendaftaran, presensi, dan laporan seluruh ekstrakurikuler SMKN 11 Bandung
            </p>

            <div class="hero-cta">
                <a href="{{ $accountUrl }}"><button>Mulai Sekarang</button></a>
            </div>
        </div>
    </section>

    <!-- ===================== VISI & MISI ===================== -->
    <section class="vision-section" aria-label="Visi dan Misi">
        <div class="vision-content">
            <div class="vision-eyebrow reveal">
                <span class="vision-eyebrow-line"></span>
                <h2 class="vision-eyebrow-text">VISI &amp; MISI</h2>
                <span class="vision-eyebrow-line vision-eyebrow-line--right"></span>
            </div>

            <div class="vision-quote">
                <p class="vision-quote-text reveal" style="--reveal-delay: 0.1s;">
                    "Membangun generasi muda yang berkarakter, kreatif, dan berdaya saing melalui wadah ekstrakurikuler yang inklusif, terorganisir, dan bermakna."
                </p>
                <p class="vision-quote-desc reveal" style="--reveal-delay: 0.2s;">
                    SOUL hadir untuk memudahkan siswa menemukan, mengikuti, dan berkembang dalam setiap komunitas ekstrakurikuler di SMKN 11 Bandung.
                </p>
            </div>
        </div>
    </section>

    <!-- ===================== SECTION EKSKUL (3D COVERFLOW) ===================== -->
    <section class="coverflow" id="ekskul" aria-label="Daftar ekstrakurikuler">
        <div class="coverflow-bg" aria-hidden="true">
            <img class="coverflow-bg-img" data-coverflow-bg alt="">
            <div class="coverflow-bg-overlay"></div>
        </div>

        <div class="coverflow-inner" data-coverflow>
            <div class="coverflow-eyebrow reveal reveal--left">
                <span class="coverflow-eyebrow-line"></span>
                <h2 class="coverflow-eyebrow-text">EKSTRAKURIKULER</h2>
                <span class="coverflow-eyebrow-line coverflow-eyebrow-line--right"></span>
            </div>
            @if($ekskuls->count() > 0)

            <div class="coverflow-stage reveal reveal--right" style="--reveal-x: 140px; --reveal-delay: 0.1s;">
                @foreach($ekskuls->take(6) as $ekskul)
                <article class="coverflow-card">
                    @if($ekskul->cover || $ekskul->logo)
                        <img class="coverflow-card-img" src="{{ $ekskul->cover ? asset('storage/'.$ekskul->cover) : asset('storage/'.$ekskul->logo) }}" alt="{{ $ekskul->nama_ekskul }}">
                    @else
                        <div class="coverflow-card-fallback"><span>{{ strtoupper(substr($ekskul->nama_ekskul, 0, 2)) }}</span></div>
                    @endif
                    <div class="coverflow-vignette"></div>
                    <div class="coverflow-content">
                        <div class="coverflow-tag-row">
                            <span class="coverflow-tag">#{{ strtoupper(substr($ekskul->nama_ekskul, 0, 2)) }}</span>
                        </div>
                        <div class="coverflow-card-body">
                            <h3 class="coverflow-title1">{{ strtoupper($ekskul->nama_ekskul) }}</h3>
                            @if($ekskul->tagline)
                                <span class="coverflow-title2">{{ $ekskul->tagline }}</span>
                            @endif
                            <div class="coverflow-divider"></div>
                            @if($ekskul->deskripsi)
                                <p class="coverflow-desc">{{ $ekskul->deskripsi }}</p>
                            @endif
                            @auth
                            <a href="{{ route('ekskul.detail', $ekskul) }}" class="coverflow-cta">
                                Lihat Ekskul
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
@else
                            <span class="coverflow-cta-disabled" style="opacity: 0.5; pointer-events: none;">
                                Lihat Ekskul
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
@endauth
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <button class="coverflow-arrow coverflow-arrow--prev" data-coverflow-prev type="button" aria-label="Ekskul sebelumnya">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button class="coverflow-arrow coverflow-arrow--next" data-coverflow-next type="button" aria-label="Ekskul berikutnya">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
            </button>

            <div class="coverflow-dots">
                @foreach($ekskuls->take(6) as $index => $ekskul)
                <button class="coverflow-dot" data-coverflow-dot type="button" aria-label="Ekskul {{ $index + 1 }}"></button>
                @endforeach
            </div>

            <div class="coverflow-cta-wrap reveal reveal--left" style="--reveal-delay: 0.2s;">
                <a href="{{ route('siswa.katalog') }}" class="coverflow-btn">LIHAT SEMUA EKSKUL</a>
            </div>

            @else
            <p class="ekskul-empty">Belum ada ekstrakurikuler yang tersedia.</p>
            @endif
        </div>
    </section>


    <!-- ===================== FEATURE 1: REGISTRASI SATU KLIK (gambar kiri) ===================== -->
    <section class="feature-detail-section feature-detail-section--1" id="features" aria-label="Registrasi Satu Klik">
        <div class="feature-shapes" aria-hidden="true">
            <span class="feature-shape feature-shape--blob-a"></span>
            <span class="feature-shape feature-shape--ring-a"></span>
            <span class="feature-shape feature-shape--circle-a"></span>
        </div>
        <div class="feature-detail-container">
            <div class="feature-detail-media reveal reveal--left">
                <img class="feature-detail-img" src="/images/siswafoto1.jpg" alt="Siswa mendaftar ekstrakurikuler">
            </div>
            <div class="feature-detail-text reveal reveal--right" style="--reveal-delay: 0.15s;">
                <h2 class="feature-detail-title">Pusatnya informasi Komunitas</h2>
                <p class="feature-detail-desc">Menjadi Pusat Informasi Yang Memudahkan Semua kalangan Sekolah untuk mengkses Komunitas Resmi di SMKN 11 Bandung.</p>
                <span class="feature-detail-badge">Fitur Utama</span>
            </div>
        </div>
    </section>

    <!-- ===================== FEATURE 2: PRESENSI OTOMATIS (gambar kanan) ===================== -->
    <section class="feature-detail-section feature-detail-section--2 feature-detail-section--reverse" aria-label="Presensi Otomatis">
        <div class="feature-shapes" aria-hidden="true">
            <span class="feature-shape feature-shape--blob-b"></span>
            <span class="feature-shape feature-shape--ring-b"></span>
            <span class="feature-shape feature-shape--circle-b"></span>
        </div>
        <div class="feature-detail-container">
            <div class="feature-detail-media reveal reveal--right">
                <img class="feature-detail-img" src="/images/siswafoto2.jpg" alt="Siswa scan QR code untuk presensi">
            </div>
            <div class="feature-detail-text reveal reveal--left" style="--reveal-delay: 0.15s;">
                <h2 class="feature-detail-title">Fitur Lengkap untuk Semua</h2>
                <p class="feature-detail-desc">Semua Fitur yang dihadirkan bertujuan untuk mempermudah Pengelolaan sistem bagi Pengurus Komunitas.</p>
            </div>
        </div>
    </section>

    <!-- ===================== FEATURE 3: LAPORAN REAL-TIME (gambar kiri) ===================== -->
    <section class="feature-detail-section feature-detail-section--3" aria-label="Laporan Real-time">
        <div class="feature-shapes" aria-hidden="true">
            <span class="feature-shape feature-shape--blob-a"></span>
            <span class="feature-shape feature-shape--ring-a"></span>
            <span class="feature-shape feature-shape--dot-grid"></span>
        </div>
        <div class="feature-detail-container">
            <div class="feature-detail-media reveal reveal--left">
                <img class="feature-detail-img" src="/images/siswafoto3.jpg" alt="Sistem laporan real-time">
            </div>
            <div class="feature-detail-text reveal reveal--right" style="--reveal-delay: 0.15s;">
                <h2 class="feature-detail-title">Efisiensi Tanpa Batas</h2>
                <p class="feature-detail-desc">Solusi cerdas untuk menghemat waktu dan tenaga. Selesaikan lebih banyak hal dengan usaha yang jauh lebih efisien.</p>
            </div>
        </div>
    </section>

    <!-- ===================== FEATURE 4: DASHBOARD ANALITIK (gambar kanan) ===================== -->
    <section class="feature-detail-section feature-detail-section--4 feature-detail-section--reverse" aria-label="Dashboard Analitik">
        <div class="feature-shapes" aria-hidden="true">
            <span class="feature-shape feature-shape--blob-b"></span>
            <span class="feature-shape feature-shape--ring-c"></span>
            <span class="feature-shape feature-shape--circle-b"></span>
        </div>
        <div class="feature-detail-container">
            <div class="feature-detail-media reveal reveal--right">
                <img class="feature-detail-img" src="/images/siswafoto4.jpg" alt="Dashboard analitik">
            </div>
            <div class="feature-detail-text reveal reveal--left" style="--reveal-delay: 0.15s;">
                <h2 class="feature-detail-title">Sinergi Visual dan Sistem</h2>
                <p class="feature-detail-desc">Perpaduan sempurna antara antarmuka yang indah dan sistem yang tangguh, menghadirkan pengalaman pengguna yang lancar dan intuitif.</p>
                <span class="feature-detail-badge">Untuk Admin &amp; Pembina</span>
            </div>
        </div>
    </section>

    <!-- ===================== TEAM / TENTANG KAMI ===================== -->
    <section class="team-section" id="community" aria-labelledby="team-heading">
        <div class="team-bg" aria-hidden="true">
            <div class="team-blob team-blob--1"></div>
            <div class="team-blob team-blob--2"></div>
        </div>

        <div class="team-container">
            <header class="team-header reveal">
                <h2 class="team-heading" id="team-heading">Di balik satu platform,<br>ada kami</h2>
                <p class="team-desc">Sekelompok siswa dan pendidik yang menyatukan seluruh ekstrakurikuler SMKN 11 Bandung dalam satu platform yang mudah, aman, dan terintegrasi.</p>
            </header>

            <div class="team-grid">
                <!-- Member 1 -->
                <article class="team-card reveal reveal--scale" data-tilt>
                    <div class="team-card-inner">
                        <div class="team-card-glow" aria-hidden="true"></div>
                        <div class="team-card-spark" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.8a2 2 0 0 0 1.3 1.3L21 12l-5.8 1.9a2 2 0 0 0-1.3 1.3L12 21l-1.9-5.8a2 2 0 0 0-1.3-1.3L3 12l5.8-1.9a2 2 0 0 0 1.3-1.3L12 3z"/></svg>
                        </div>
                        <div class="team-card-body">
                            <div class="team-avatar">
                                <div class="team-avatar-halo" aria-hidden="true"></div>
                                <div class="team-avatar-frame">
                                    <img class="team-avatar-img" src="{{ asset('images/rizki.jpg') }}" alt="Adit Pratama">
                                </div>
                            </div>
                            <div class="team-info">
                                <h3 class="team-name">M.Rizki Bintang</h3>
                                <div class="team-location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span>Bandung</span>
                                </div>
                                <p class="team-bio">Siswa Terpelajar SMKN 11 Bandung</p>
                                <div class="team-skills">
                                    <span class="team-skill">Present's Member</span>
                                </div>
                                <div class="team-social">
                                    <a href="#" class="team-social-link" aria-label="Twitter"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg></a>
                                    <a href="#" class="team-social-link" aria-label="LinkedIn"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg></a>
                                    <a href="#" class="team-social-link" aria-label="GitHub"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg></a>
                                    <a href="#" class="team-social-link" aria-label="Email"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Member 2 -->
                <article class="team-card reveal reveal--scale" data-tilt style="--reveal-delay: 0.12s;">
                    <div class="team-card-inner">
                        <div class="team-card-glow" aria-hidden="true"></div>
                        <div class="team-card-spark" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.8a2 2 0 0 0 1.3 1.3L21 12l-5.8 1.9a2 2 0 0 0-1.3 1.3L12 21l-1.9-5.8a2 2 0 0 0-1.3-1.3L3 12l5.8-1.9a2 2 0 0 0 1.3-1.3L12 3z"/></svg>
                        </div>
                        <div class="team-card-body">
                            <div class="team-avatar">
                                <div class="team-avatar-halo" aria-hidden="true"></div>
                                <div class="team-avatar-frame">
                                    <img class="team-avatar-img" src="{{ asset('images/nazwa.jpeg') }}" alt="Rina Fitriani">
                                </div>
                            </div>
                            <div class="team-info">
                                <h3 class="team-name">Nazwa Nurhafiza</h3>
                                <div class="team-location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span>Bandung</span>
                                </div>
                                <p class="team-bio">Siswi Terpelajar SMKN 11 Bandung</p>
                                <div class="team-skills">
                                    <span class="team-skill">Present's Member</span>
                                </div>
                                <div class="team-social">
                                    <a href="#" class="team-social-link" aria-label="Twitter"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg></a>
                                    <a href="#" class="team-social-link" aria-label="LinkedIn"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg></a>
                                    <a href="#" class="team-social-link" aria-label="GitHub"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg></a>
                                    <a href="#" class="team-social-link" aria-label="Email"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Member 3 -->
                <article class="team-card reveal reveal--scale" data-tilt style="--reveal-delay: 0.24s;">
                    <div class="team-card-inner">
                        <div class="team-card-glow" aria-hidden="true"></div>
                        <div class="team-card-spark" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 5.8a2 2 0 0 0 1.3 1.3L21 12l-5.8 1.9a2 2 0 0 0-1.3 1.3L12 21l-1.9-5.8a2 2 0 0 0-1.3-1.3L3 12l5.8-1.9a2 2 0 0 0 1.3-1.3L12 3z"/></svg>
                        </div>
                        <div class="team-card-body">
                            <div class="team-avatar">
                                <div class="team-avatar-halo" aria-hidden="true"></div>
                                <div class="team-avatar-frame">
                                    <img class="team-avatar-img" src="{{ asset('images/fadhil.jpeg') }}" alt="Fadhil">
                                </div>
                            </div>
                            <div class="team-info">
                                <h3 class="team-name">Fadhil Al hafidzh</h3>
                                <div class="team-location">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                    <span>Bandung</span>
                                </div>
                                <p class="team-bio">Siswa Terpelajar SMKN 11 Bandung</p>
                                <div class="team-skills">
                                    <span class="team-skill">Present's Member</span>
                                </div>
                                <div class="team-social">
                                    <a href="#" class="team-social-link" aria-label="Twitter"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg></a>
                                    <a href="#" class="team-social-link" aria-label="LinkedIn"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg></a>
                                    <a href="#" class="team-social-link" aria-label="GitHub"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg></a>
                                    <a href="#" class="team-social-link" aria-label="Email"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ===================== FOOTER ===================== -->
    <footer class="site-footer" role="contentinfo">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Brand Column -->
                <div class="footer-col footer-col--brand reveal">
                    <a href="/" class="footer-logo" aria-label="SOUL - Beranda">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                        <span class="footer-logo-text">SOUL</span>
                    </a>
                    <p class="footer-brand-desc">
                        Platform terpadu untuk mengelola pendaftaran, presensi, dan laporan seluruh ekstrakurikuler sekolah dalam satu tempat.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="footer-social-link" aria-label="Instagram">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="YouTube">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/></svg>
                        </a>
                        <a href="#" class="footer-social-link" aria-label="Twitter">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Navigation Columns -->
                <nav class="footer-col reveal" style="--reveal-delay: 0.08s;" aria-label="Produk">
                    <h3 class="footer-heading">Produk</h3>
                    <ul class="footer-links">
                        <li><a href="#features" class="footer-link">Fitur Unggulan</a></li>
                        <li><a href="{{ route('siswa.katalog') }}" class="footer-link">Katalog Ekskul</a></li>
                        <li><a href="#" class="footer-link">Presensi Digital</a></li>
                        <li><a href="#" class="footer-link">Laporan & Analitik</a></li>
                        <li><a href="#" class="footer-link">Notifikasi Cerdas</a></li>
                    </ul>
                </nav>

                <nav class="footer-col reveal" style="--reveal-delay: 0.16s;" aria-label="Komunitas">
                    <h3 class="footer-heading">Komunitas</h3>
                    <ul class="footer-links">
                        <li><a href="#community" class="footer-link">Ekskul & Komunitas</a></li>
                        <li><a href="#" class="footer-link">Kepemimpinan Siswa</a></li>
                        <li><a href="#" class="footer-link">Kegiatan & Kompetisi</a></li>
                        <li><a href="#" class="footer-link">Jaringan Alumni</a></li>
                        <li><a href="#" class="footer-link">Galeri Kegiatan</a></li>
                    </ul>
                </nav>

                <nav class="footer-col reveal" style="--reveal-delay: 0.24s;" aria-label="Dukungan">
                    <h3 class="footer-heading">Dukungan</h3>
                    <ul class="footer-links">
                        <li><a href="#" class="footer-link">Pusat Bantuan</a></li>
                        <li><a href="#" class="footer-link">Panduan Pengguna</a></li>
                        <li><a href="#" class="footer-link">FAQ</a></li>
                        <li><a href="#" class="footer-link">Hubungi Kami</a></li>
                        <li><a href="#" class="footer-link">Lapor Masalah</a></li>
                    </ul>
                </nav>

                <nav class="footer-col reveal" style="--reveal-delay: 0.32s;" aria-label="Tentang">
                    <h3 class="footer-heading">Tentang</h3>
                    <ul class="footer-links">
                        <li><a href="#" class="footer-link">Tentang SOUL</a></li>
                        <li><a href="#" class="footer-link">Visi & Misi</a></li>
                        <li><a href="#" class="footer-link">Tim Pengembang</a></li>
                        <li><a href="#" class="footer-link">Karir</a></li>
                        <li><a href="#" class="footer-link">Media</a></li>
                    </ul>
                </nav>
            </div>

            <!-- Divider -->
            <div class="footer-divider"></div>

            <!-- Bottom Bar -->
            <div class="footer-bottom">
                <p class="footer-copyright">
                    &copy; {{ date('Y') }} SOUL. Hak cipta dilindungi.
                </p>
                <div class="footer-legal">
                    <a href="#" class="footer-legal-link">Kebijakan Privasi</a>
                    <span class="footer-legal-sep" aria-hidden="true"></span>
                    <a href="#" class="footer-legal-link">Syarat & Ketentuan</a>
                    <span class="footer-legal-sep" aria-hidden="true"></span>
                    <a href="#" class="footer-legal-link">Cookie Policy</a>
                </div>
                <p class="footer-made">
                    Dibangun dengan <span class="footer-heart" aria-hidden="true">♥</span> untuk sekolah Indonesia
                </p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/welcome.js') }}"></script>
<script>
    function checkEkskulLogin(ekskulId) {
        @if(auth()->check())
            window.location.href = '{{ route('ekskul.detail', $ekskul) }}';
        @else
            window.location.href = '{{ route('login') }}';
        @endif
    }
</script>
</body>
</html>
