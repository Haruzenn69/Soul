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
                    <a href="#features" class="header-nav-link">Features</a>
                    <a href="#pricing" class="header-nav-link">Pricing</a>
                    <a href="#about" class="header-nav-link">About</a>
                </div>

                <button class="search-btn" onclick="openSearch()" type="button">
                    <span class="search-label">Search...</span>
                    <span class="search-shortcut">Ctrl K</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </button>

                <button class="search-btn-icon" onclick="openSearch()" type="button" aria-label="Search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </button>

                <button class="menu-btn" onclick="openSheet()" type="button" aria-label="Menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
            </div>
        </nav>
    </header>

    <!-- ===================== SEARCH MODAL ===================== -->
    <div class="search-overlay" id="searchOverlay" onclick="closeSearch(event)">
        <div class="search-modal" onclick="event.stopPropagation()">
            <div class="search-input-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input class="search-input" id="searchInput" type="text" placeholder="Type to search..." oninput="filterSearch(this.value)">
            </div>
            <div class="search-results" id="searchResults">
                <div class="search-group-label">Pages</div>
                <div class="search-item" data-title="Features" data-desc="Platform capabilities overview">
                    <div class="search-item-text"><div class="search-item-title">Features</div><div class="search-item-desc">Platform capabilities overview</div></div>
                    <span class="search-item-badge">Page</span>
                </div>
                <div class="search-item" data-title="Pricing" data-desc="Plans and pricing details">
                    <div class="search-item-text"><div class="search-item-title">Pricing</div><div class="search-item-desc">Plans and pricing details</div></div>
                    <span class="search-item-badge">Page</span>
                </div>
                <div class="search-item" data-title="About" data-desc="Learn more about SOUL">
                    <div class="search-item-text"><div class="search-item-title">About</div><div class="search-item-desc">Learn more about SOUL</div></div>
                    <span class="search-item-badge">Page</span>
                </div>
                <div class="search-group-label">Blog</div>
                <div class="search-item" data-title="The Future of Web Dev" data-desc="A quick look at upcoming web technologies.">
                    <div class="search-item-text"><div class="search-item-title">The Future of Web Dev</div><div class="search-item-desc">A quick look at upcoming web technologies.</div></div>
                    <span class="search-item-badge">Web Dev</span>
                </div>
                <div class="search-item" data-title="Minimalist Design Tips" data-desc="Learn how less can often be more in UI design.">
                    <div class="search-item-text"><div class="search-item-title">Minimalist Design Tips</div><div class="search-item-desc">Learn how less can often be more in UI design.</div></div>
                    <span class="search-item-badge">Design</span>
                </div>
                <div class="search-item" data-title="Boosting Page Speed" data-desc="Simple tricks to make your site load faster.">
                    <div class="search-item-text"><div class="search-item-title">Boosting Page Speed</div><div class="search-item-desc">Simple tricks to make your site load faster.</div></div>
                    <span class="search-item-badge">Performance</span>
                </div>
                <div class="search-item" data-title="Intro to TypeScript" data-desc="Why TypeScript makes JavaScript safer and clearer.">
                    <div class="search-item-text"><div class="search-item-title">Intro to TypeScript</div><div class="search-item-desc">Why TypeScript makes JavaScript safer and clearer.</div></div>
                    <span class="search-item-badge">Programming</span>
                </div>
                <div class="search-item" data-title="Dark Mode Design" data-desc="Best practices for building a dark theme UI.">
                    <div class="search-item-text"><div class="search-item-title">Dark Mode Design</div><div class="search-item-desc">Best practices for building a dark theme UI.</div></div>
                    <span class="search-item-badge">Design</span>
                </div>
                <div class="search-item" data-title="Understanding APIs" data-desc="Breaking down REST and GraphQL for beginners.">
                    <div class="search-item-text"><div class="search-item-title">Understanding APIs</div><div class="search-item-desc">Breaking down REST and GraphQL for beginners.</div></div>
                    <span class="search-item-badge">Backend</span>
                </div>
                <div class="search-item" data-title="CSS Grid Basics" data-desc="A quick guide to building layouts with CSS Grid.">
                    <div class="search-item-text"><div class="search-item-title">CSS Grid Basics</div><div class="search-item-desc">A quick guide to building layouts with CSS Grid.</div></div>
                    <span class="search-item-badge">Frontend</span>
                </div>
                <div class="search-item" data-title="React State Management" data-desc="Exploring useState, Redux, and other options.">
                    <div class="search-item-text"><div class="search-item-title">React State Management</div><div class="search-item-desc">Exploring useState, Redux, and other options.</div></div>
                    <span class="search-item-badge">Frontend</span>
                </div>
                <div class="search-item" data-title="SEO in 2025" data-desc="Trends and tips to rank higher on Google.">
                    <div class="search-item-text"><div class="search-item-title">SEO in 2025</div><div class="search-item-desc">Trends and tips to rank higher on Google.</div></div>
                    <span class="search-item-badge">SEO</span>
                </div>
                <div class="search-item" data-title="Debugging Like a Pro" data-desc="Tools and techniques to fix bugs faster.">
                    <div class="search-item-text"><div class="search-item-title">Debugging Like a Pro</div><div class="search-item-desc">Tools and techniques to fix bugs faster.</div></div>
                    <span class="search-item-badge">Programming</span>
                </div>
            </div>
            <div class="search-footer">
                <span><kbd>&uarr;</kbd><kbd>&darr;</kbd> Navigate</span>
                <span><kbd>&crarr;</kbd> Open</span>
                <span><kbd>Esc</kbd> Close</span>
            </div>
        </div>
    </div>

    <!-- ===================== MOBILE SHEET ===================== -->
    <div class="sheet-overlay" id="sheetOverlay" onclick="closeSheet()"></div>
    <div class="sheet-panel" id="sheetPanel">
        <div class="sheet-header">
            <button class="sheet-close" onclick="closeSheet()" type="button" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <div class="sheet-links">
            <a href="#features" class="sheet-link" onclick="closeSheet()">Features</a>
            <a href="#pricing" class="sheet-link" onclick="closeSheet()">Pricing</a>
            <a href="#about" class="sheet-link" onclick="closeSheet()">About</a>
        </div>
        <div class="sheet-footer">
            <a href="{{ $accountUrl }}" class="btn-outline">Sign In</a>
            <a href="{{ $accountUrl }}" class="btn-primary">Get Started</a>
        </div>
    </div>

    <!-- ===================== HERO ===================== -->
    <section class="hero-section">
        <div class="hero-content">

            <h1 class="hero-title" aria-label="Temukan komunitas terbaikmu disini">
                <span class="word" style="--delay: 0.1s;">Temukan <span class="highlight">komunitas</span></span>
                <span class="word word-1" style="--delay: 0.15s;">terbaikmu disini</span>
            </h1>

            <p class="hero-desc">
                Temukan Komunitas yang cocok, daftar mudah, dan jalin pertemanan baru di platform sekolahmu.
            </p>

            <div class="hero-cta">
                <a href="{{ $accountUrl }}"><button>Get Started</button></a>
            </div>
        </div>

        <div class="hero-marquee" aria-hidden="true">
            <div class="marquee-track">
                <div class="marquee-item"><img src="{{ asset('images/hendr.png') }}" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1757865579201-693dd2080c73?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1756786605218-28f7dd95a493?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1757519740947-eef07a74c4ab?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1757263005786-43d955f07fb1?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1757207445614-d1e12b8f753e?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1757269746970-dc477517268f?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1755119902709-a53513bcbedc?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <!-- duplicate -->
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1756312148347-611b60723c7a?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1757865579201-693dd2080c73?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1756786605218-28f7dd95a493?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1757519740947-eef07a74c4ab?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1757263005786-43d955f07fb1?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1757207445614-d1e12b8f753e?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1757269746970-dc477517268f?w=900&auto=format&fit=crop&q=60" alt=""></div>
                <div class="marquee-item"><img src="https://images.unsplash.com/photo-1755119902709-a53513bcbedc?w=900&auto=format&fit=crop&q=60" alt=""></div>
            </div>
        </div>
    </section>

<!-- ===================== FEATURES BENTO GRID ===================== -->
    <section class="features-section" id="features" aria-label="Fitur unggulan">
        <div class="features-container">
            <header class="features-header">
                <h2 class="features-heading">FITUR UNGGULAN</h2>
                <p class="features-subtitle">Semua yang kamu butuhkan untuk mengelola ekstrakurikuler dengan lancar</p>
            </header>

            <div class="features-grid">
                <!-- Large Feature: Registrasi Cepat -->
                <article class="feature-card feature-card--large feature-card--primary">
                    <div class="feature-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="feature-card-content">
                        <h3 class="feature-card-title">Registrasi Satu Klik</h3>
                        <p class="feature-card-desc">Daftar ekstrakurikuler favoritmu hanya dengan satu sentuhan. Tidak perlu form panjang, tidak perlu antri di kantor OSIS.</p>
                        <div class="feature-card-badge">Fitur Utama</div>
                    </div>
                </article>

                <!-- Medium Feature: Presensi Otomatis -->
                <article class="feature-card feature-card--medium feature-card--accent">
                    <div class="feature-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    </div>
                    <div class="feature-card-content">
                        <h3 class="feature-card-title">Presensi Otomatis</h3>
                        <p class="feature-card-desc">Scan QR code atau tap NFC untuk hadir. Data tersinkron real-time ke dashboard pembina.</p>
                    </div>
                </article>

                <!-- Medium Feature: Laporan Real-time -->
                <article class="feature-card feature-card--medium feature-card--success">
                    <div class="feature-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="16" y1="13" y2="13"/><line x1="8" x2="8" y1="13" y2="13"/><line x1="10" x2="10" y1="17" y2="17"/><line x1="14" x2="14" y1="17" y2="17"/></svg>
                    </div>
                    <div class="feature-card-content">
                        <h3 class="feature-card-title">Laporan Real-time</h3>
                        <p class="feature-card-desc">Pembina melihat kehadiran, statistik, dan ekspor laporan PDF/Excel kapan saja.</p>
                    </div>
                </article>

                <!-- Small Feature: Notifikasi -->
                <article class="feature-card feature-card--small feature-card--warning">
                    <div class="feature-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </div>
                    <div class="feature-card-content">
                        <h3 class="feature-card-title">Notifikasi Cerdas</h3>
                        <p class="feature-card-desc">Pengingat jadwal, pengumuman penting, dan status pendaftaran langsung ke HP.</p>
                    </div>
                </article>

                <!-- Small Feature: Katalog Ekskul -->
                <article class="feature-card feature-card--small feature-card--info">
                    <div class="feature-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div class="feature-card-content">
                        <h3 class="feature-card-title">Katalog Lengkap</h3>
                        <p class="feature-card-desc">Jelajahi semua ekstrakurikuler dengan deskripsi, jadwal, pembina, dan kuota tersedia.</p>
                    </div>
                </article>

                <!-- Large Feature: Dashboard Analitik -->
                <article class="feature-card feature-card--large feature-card--secondary">
                    <div class="feature-card-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>
                    </div>
                    <div class="feature-card-content">
                        <h3 class="feature-card-title">Dashboard Analitik</h3>
                        <p class="feature-card-desc">Visualisasikan partisipasi, tren kehadiran, dan performa ekstrakurikuler dengan grafik interaktif.</p>
                        <div class="feature-card-badge">Untuk Admin & Pembina</div>
                    </div>
                </article>
            </div>
        </div>
</section>

    <!-- ===================== SECTION EKSKUL ===================== -->
    <section class="ekskul-section">
        <div class="ekskul-container">

            <h2 class="ekskul-heading">TEMUKAN KOMUNITAS TERBAIK</h2>

            <div class="ekskul-grid">
                @forelse($ekskuls->take(6) as $ekskul)
                <article class="ekskul-card">
                    <div class="ekskul-card-media">
                        <span class="ekskul-logo">{{ strtoupper(substr($ekskul->nama_ekskul, 0, 2)) }}</span>
                    </div>
                    <div class="ekskul-card-body">
                        <h3 class="ekskul-name">{{ $ekskul->nama_ekskul }}</h3>
                        <p class="ekskul-desc">{{ $ekskul->deskripsi ?? 'Deskripsi belum tersedia' }}</p>
                        <div class="ekskul-meta">
                            <span class="ekskul-pembina">Pembina: {{ $ekskul->pembina->nama ?? '-' }}</span>
                            <span class="ekskul-status {{ $ekskul->is_open_recruitment ? 'is-open' : 'is-closed' }}">
                                {{ $ekskul->is_open_recruitment ? 'Buka Pendaftaran' : 'Tutup Pendaftaran' }}
                            </span>
                        </div>
                    </div>
                </article>
                @empty
                <p class="ekskul-empty">Belum ada ekstrakurikuler yang tersedia.</p>
                @endforelse
            </div>

            <div class="ekskul-cta">
                <a href="{{ route('siswa.katalog') }}" class="ekskul-btn">LIHAT SEMUA EKSKUL</a>
            </div>

        </div>
    </section>

    <!-- ===================== COMMUNITY SHOWCASE ===================== -->
    <section class="community-section" id="community" aria-label="Komunitas sekolah">
        <div class="community-container">
            <header class="community-header">
                <span class="community-eyebrow">EKOSISTEM KOMUNITAS</span>
                <h2 class="community-heading">Jelajahi Komunitas Sekolah</h2>
                <div class="community-heading-underline"></div>
                <p class="community-desc">
                    Temukan berbagai komunitas dan ekstrakurikuler untuk mengembangkan minat, bakat, kepemimpinan, dan kolaborasi bersama teman sekelas.
                </p>
            </header>

            <div class="community-grid">
                <!-- LEFT COLUMN - Features -->
                <div class="community-col community-col--left">
                    <article class="community-feature">
                        <div class="community-feature-icon">
                            <svg xmlns="http://www.w3.org/2000svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <div class="community-feature-content">
                            <h3 class="community-feature-title">Minat & Bakat</h3>
                            <p class="community-feature-desc">Salurkan kreativitasmu melalui seni, musik, olahraga, dan bidang non-akademik lain yang membangkitkan semangat.</p>
                        </div>
                    </article>
                    <article class="community-feature">
                        <div class="community-feature-icon">
                            <svg xmlns="http://www.w3.org/2000svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="community-feature-content">
                            <h3 class="community-feature-title">Kepemimpinan</h3>
                            <p class="community-feature-desc">Asah kemampuan memimpin, mengorganisir, dan mengambil keputusan melalui peran aktif di struktur kepengurusan.</p>
                        </div>
                    </article>
                    <article class="community-feature">
                        <div class="community-feature-icon">
                            <svg xmlns="http://www.w3.org/2000svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div class="community-feature-content">
                            <h3 class="community-feature-title">Kolaborasi</h3>
                            <p class="community-feature-desc">Belajar bekerja sama, menghargai perbedaan, dan menciptakan karya bersama yang bermakna untuk sekolah.</p>
                        </div>
                    </article>
                </div>

                <!-- CENTER - Main Visual -->
                <div class="community-col community-col--center">
                    <div class="community-visual-wrapper">
                        <div class="community-visual-frame">
                            <img src="{{ asset('images/hendr.png') }}" alt="Siswa aktif dalam kegiatan ekstrakurikuler" class="community-visual-img">
                        </div>
                        <div class="community-visual-accent community-visual-accent--1"></div>
                        <div class="community-visual-accent community-visual-accent--2"></div>
                        <div class="community-visual-accent community-visual-accent--3"></div>
                    </div>
                </div>

                <!-- RIGHT COLUMN - Features -->
                <div class="community-col community-col--right">
                    <article class="community-feature">
                        <div class="community-feature-icon">
                            <svg xmlns="http://www.w3.org/2000svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        </div>
                        <div class="community-feature-content">
                            <h3 class="community-feature-title">Pengalaman Nyata</h3>
                            <p class="community-feature-desc">Ikuti kegiatan rutin, kompetisi, workshop, dan bakti sosial yang membentuk karakter dan portofolio.</p>
                        </div>
                    </article>
                    <article class="community-feature">
                        <div class="community-feature-icon">
                            <svg xmlns="http://www.w3.org/2000svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="community-feature-content">
                            <h3 class="community-feature-title">Sertifikasi & Penghargaan</h3>
                            <p class="community-feature-desc">Dapatkan sertifikat keikutsertaan, piagam penghargaan, dan pencapaian yang berharga untuk masa depan.</p>
                        </div>
                    </article>
                    <article class="community-feature">
                        <div class="community-feature-icon">
                            <svg xmlns="http://www.w3.org/2000svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                        </div>
                        <div class="community-feature-content">
                            <h3 class="community-feature-title">Jaringan Alumni</h3>
                            <p class="community-feature-desc">Terhubung dengan senior dan alumni yang sudah sukses di berbagai bidang karir dan pendidikan tinggi.</p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== FOOTER ===================== -->
    <footer class="site-footer" role="contentinfo">
        <div class="footer-container">
            <div class="footer-grid">
                <!-- Brand Column -->
                <div class="footer-col footer-col--brand">
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
                <nav class="footer-col" aria-label="Produk">
                    <h3 class="footer-heading">Produk</h3>
                    <ul class="footer-links">
                        <li><a href="#features" class="footer-link">Fitur Unggulan</a></li>
                        <li><a href="{{ route('siswa.katalog') }}" class="footer-link">Katalog Ekskul</a></li>
                        <li><a href="#" class="footer-link">Presensi Digital</a></li>
                        <li><a href="#" class="footer-link">Laporan & Analitik</a></li>
                        <li><a href="#" class="footer-link">Notifikasi Cerdas</a></li>
                    </ul>
                </nav>

                <nav class="footer-col" aria-label="Komunitas">
                    <h3 class="footer-heading">Komunitas</h3>
                    <ul class="footer-links">
                        <li><a href="#community" class="footer-link">Ekskul & Komunitas</a></li>
                        <li><a href="#" class="footer-link">Kepemimpinan Siswa</a></li>
                        <li><a href="#" class="footer-link">Kegiatan & Kompetisi</a></li>
                        <li><a href="#" class="footer-link">Jaringan Alumni</a></li>
                        <li><a href="#" class="footer-link">Galeri Kegiatan</a></li>
                    </ul>
                </nav>

                <nav class="footer-col" aria-label="Dukungan">
                    <h3 class="footer-heading">Dukungan</h3>
                    <ul class="footer-links">
                        <li><a href="#" class="footer-link">Pusat Bantuan</a></li>
                        <li><a href="#" class="footer-link">Panduan Pengguna</a></li>
                        <li><a href="#" class="footer-link">FAQ</a></li>
                        <li><a href="#" class="footer-link">Hubungi Kami</a></li>
                        <li><a href="#" class="footer-link">Lapor Masalah</a></li>
                    </ul>
                </nav>

                <nav class="footer-col" aria-label="Tentang">
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
</body>
</html>