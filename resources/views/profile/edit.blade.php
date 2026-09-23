<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Siswa - SOUL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        theme: { blue: '#2563EB', darkBlue: '#1D4ED8', yellow: '#FACC15', dark: '#0F172A', lightBg: '#F8FAFC' }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes floaty { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @keyframes blob { 0%, 100% { transform: translate(0, 0) scale(1); } 33% { transform: translate(24px, -18px) scale(1.08); } 66% { transform: translate(-16px, 12px) scale(.94); } }
        .animate-fade-up { animation: fadeUp .6s cubic-bezier(.22,1,.36,1) both; }
        .animate-floaty { animation: floaty 5s ease-in-out infinite; }
        .animate-blob { animation: blob 10s ease-in-out infinite; }

        /* SIDEBAR COLLAPSIBLE (sama seperti dashboard) */
        #sidebar-desktop { transition: width .25s ease; }
        #sidebar-desktop.collapsed { width: 4.5rem; }
        #sidebar-desktop.collapsed .sidebar-label,
        #sidebar-desktop.collapsed .sidebar-logo-text,
        #sidebar-desktop.collapsed .sidebar-section-label,
        #sidebar-desktop.collapsed .sidebar-user-info,
        #sidebar-desktop.collapsed .sidebar-logout-text { display: none; }
        #sidebar-desktop.collapsed nav a { justify-content: center; padding-left: 0; padding-right: 0; }
        #sidebar-desktop.collapsed .sidebar-user-card { justify-content: center; }

        /* TOOLTIP - muncul saat hover ikon, hanya saat sidebar collapsed */
        .nav-tooltip {
            position: absolute;
            left: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%) translateX(-4px);
            background: #0F172A;
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            padding: 6px 10px;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity .15s ease, transform .15s ease;
            z-index: 60;
            box-shadow: 0 8px 20px rgba(15,23,42,.25);
        }
        .nav-tooltip::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #0F172A;
        }
        #sidebar-desktop.collapsed .nav-item-wrap:hover .nav-tooltip {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-amber-50 text-slate-800 font-sans antialiased min-h-screen md:flex selection:bg-sky-100 selection:text-sky-700 overflow-x-hidden">

    <!-- SIDEBAR LEFT (collapsible, ala Claude) -->
    <aside id="sidebar-desktop" class="w-64 hidden md:flex flex-col justify-between shrink-0 bg-white/90 backdrop-blur border-r border-sky-100 shadow-sm sticky top-0 self-start h-screen overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-sky-100/70 blur-3xl animate-blob"></div>
            <div class="absolute bottom-0 -left-20 w-56 h-56 rounded-full bg-amber-100/70 blur-3xl animate-blob" style="animation-delay: 3s"></div>
        </div>

        <!-- Tombol geser buka/tutup -->
        <button onclick="toggleDesktopSidebar()" class="absolute top-5 -right-3 z-20 w-6 h-6 rounded-full bg-white border border-sky-200 shadow-md flex items-center justify-center text-slate-400 hover:text-sky-600 hover:border-sky-300 transition-all">
            <svg id="sidebar-toggle-icon" class="w-3.5 h-3.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="relative p-5 flex flex-col h-full overflow-y-auto overflow-x-hidden">
            <!-- Logo SOUL -->
            <div class="flex items-center gap-3 mb-8 px-2 mt-1">
                <div class="w-10 h-10 shrink-0 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-sky-300">
                    SOUL
                </div>
                <div class="sidebar-logo-text">
                    <h1 class="font-extrabold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                </div>
            </div>

            <div class="sidebar-section-label text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3">Menu Utama</div>
            <nav class="space-y-1.5">
                <div class="nav-item-wrap relative">
                    <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                        <span class="text-base flex items-center justify-center w-4 h-4 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </span>
                        <span class="sidebar-label">Dashboard</span>
                    </a>
                    <span class="nav-tooltip">Dashboard</span>
                </div>

                <div class="nav-item-wrap relative">
                    <a href="{{ route('siswa.katalog') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                        <span class="text-base flex items-center justify-center w-4 h-4 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </span>
                        <span class="sidebar-label">Katalog Ekskul</span>
                    </a>
                    <span class="nav-tooltip">Katalog Ekskul</span>
                </div>

                <div class="nav-item-wrap relative">
                    <a href="{{ route('siswa.presensi') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                        <span class="text-base flex items-center justify-center w-4 h-4 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <span class="sidebar-label">Presensi & Kegiatan</span>
                    </a>
                    <span class="nav-tooltip">Presensi & Kegiatan</span>
                </div>

                <div class="nav-item-wrap relative">
                    <a href="{{ route('profile.edit') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold text-xs shadow-sm shadow-sky-100 transition-all">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                        <span class="text-base flex items-center justify-center w-4 h-4 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <span class="sidebar-label">Profile</span>
                    </a>
                    <span class="nav-tooltip">Profile</span>
                </div>
            </nav>

            <!-- User Profile Card Bottom -->
            <div class="mt-auto pt-6">
                <div class="nav-item-wrap relative">
                    <div class="sidebar-user-card bg-gradient-to-r from-sky-50 to-amber-50 p-3 rounded-2xl flex items-center justify-between border border-sky-100 shadow-sm">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 shrink-0 rounded-full bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 font-extrabold flex items-center justify-center text-xs shadow-md shadow-amber-200">
                                {{ strtoupper(substr($siswa->nama ?? auth()->user()->username ?? 'S', 0, 1)) }}
                            </div>
                            <div class="sidebar-user-info text-left">
                                <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ $siswa->nama ?? auth()->user()->username }}</h4>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $siswa->kelas->nama ?? 'Siswa' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-text">
                            @csrf
                            <button type="submit" class="text-slate-400 hover:text-red-500 text-xs font-bold transition-colors">Keluar</button>
                        </form>
                    </div>
                    <span class="nav-tooltip">{{ $siswa->nama ?? auth()->user()->username }} · Keluar</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- MOBILE SIDEBAR OVERLAY -->
    <div id="sidebar-overlay" class="hidden fixed inset-0 z-40 bg-black/50 md:hidden" onclick="closeSidebar()"></div>

    <!-- MOBILE SIDEBAR -->
    <div id="sidebar-mobile" class="hidden fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white border-r border-sky-100 overflow-y-auto md:hidden shadow-2xl">
        <div class="relative h-full flex flex-col overflow-y-auto">
            <div class="flex items-center justify-between mb-8 px-2 mt-1">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-sky-300">SOUL</div>
                    <div>
                        <h1 class="font-extrabold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                        <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                    </div>
                </div>
                <button onclick="closeSidebar()" class="w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3">Menu Utama</div>
            <nav class="space-y-1.5">
                <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </span>
                    Dashboard
                </a>
                <a href="{{ route('siswa.katalog') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                    Katalog Ekskul
                </a>
                <a href="{{ route('siswa.presensi') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    Presensi & Kegiatan
                </a>
                <a href="{{ route('profile.edit') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold text-xs shadow-sm shadow-sky-100 transition-all">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    Profile
                </a>
            </nav>

            <div class="mt-auto pt-6">
                <div class="bg-gradient-to-r from-sky-50 to-amber-50 p-3 rounded-2xl flex items-center justify-between border border-sky-100 shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 font-extrabold flex items-center justify-center text-xs shadow-md shadow-amber-200">
                            {{ strtoupper(substr($siswa->nama ?? auth()->user()->username ?? 'S', 0, 1)) }}
                        </div>
                        <div class="text-left">
                            <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ $siswa->nama ?? auth()->user()->username }}</h4>
                            <p class="text-[10px] text-slate-400 font-medium">{{ $siswa->kelas->nama ?? 'Siswa' }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-red-500 text-xs font-bold transition-colors">Keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="w-full flex-1 flex flex-col min-w-0">

        <!-- TOP NAVBAR HEADER -->
        <header class="px-4 md:px-8 py-4 bg-white/70 backdrop-blur-lg border-b border-sky-100 flex items-center justify-between gap-3 md:gap-4 sticky top-0 z-30">
            <!-- KIRI: Hamburger (Mobile) + Logo + Search -->
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <!-- Tombol Hamburger (Mobile) - Pindah ke Kiri -->
                <button onclick="openSidebar()" class="w-9 h-9 rounded-full bg-white border border-sky-100 flex items-center justify-center text-slate-500 md:hidden shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <!-- Logo Mobile -->
                <div class="flex items-center gap-2 md:hidden shrink-0">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-sky-300">S</div>
                    <span class="font-extrabold text-sm tracking-tight text-slate-900">SOUL</span>
                </div>

                <!-- Search (Desktop) -->
                <form method="GET" action="{{ route('siswa.katalog') }}" class="relative w-full max-w-md hidden sm:block">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                </form>
            </div>

            <!-- KANAN: Info User + Notifikasi + Logout -->
            <div class="flex items-center gap-2 md:gap-3 shrink-0">
                <div class="bg-sky-50 text-sky-700 border border-sky-100 px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2 hidden sm:block">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Siswa
                </div>
                <a href="{{ route('siswa.notifikasi') }}" class="w-9 h-9 rounded-xl bg-white border border-sky-100 flex items-center justify-center text-xs relative text-slate-600 hover:bg-sky-50 hover:border-sky-200 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(($unreadNotifCount ?? 0) > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-amber-400 to-yellow-500 absolute top-1.5 right-1.5 border-2 border-white shadow-sm"></span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-400 px-4 py-2 rounded-lg text-xs font-semibold border border-red-100 transition shadow-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- MAIN PAGE CONTENT -->
        <main class="p-4 md:p-8 space-y-6 overflow-y-auto">

            <div class="animate-fade-up">
                <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900">Profile Saya</h1>
                <p class="text-xs text-slate-400 mt-1 font-medium">Kelola informasi akun dan data diri kamu</p>
            </div>

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-2xl shadow-sm animate-fade-up">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-2xl shadow-sm animate-fade-up">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- KOLOM KIRI: Foto Profile & Informasi Singkat -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 p-6 text-center animate-fade-up" style="animation-delay: .1s">
                        <!-- Foto Profile -->
                        <div class="relative inline-block">
                            <div class="w-28 h-28 md:w-32 md:h-32 rounded-full bg-gradient-to-br from-sky-100 to-blue-100 border-4 border-sky-300 flex items-center justify-center mx-auto overflow-hidden shadow-lg shadow-sky-200 animate-floaty">
                                <span class="text-3xl md:text-4xl font-extrabold text-sky-600">
                                    {{ strtoupper(substr($siswa->nama ?? auth()->user()->username ?? 'S', 0, 1)) }}
                                </span>
                            </div>
                            <button class="absolute bottom-2 right-2 bg-gradient-to-br from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white p-1.5 rounded-full shadow-md shadow-sky-300 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>

                        <h3 class="text-sm font-extrabold text-slate-900 mt-4">{{ $siswa->nama ?? auth()->user()->username }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $siswa->kelas->nama ?? 'Siswa' }}</p>

                        <div class="mt-4 pt-4 border-t border-sky-50">
                            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Bergabung sejak</p>
                            <p class="text-xs font-bold text-slate-700 mt-1">{{ $siswa?->created_at ? \Carbon\Carbon::parse($siswa->created_at)->isoFormat('D MMMM Y') : '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN: Data Diri & Pengajuan Keluar -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Data Diri -->
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .15s">
                        <div class="px-6 py-5 border-b border-sky-50">
                            <h2 class="text-sm font-extrabold text-slate-900">Data Diri</h2>
                            <p class="text-[11px] text-slate-400 mt-0.5">Informasi akun dan identitas kamu</p>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-2xl border border-sky-100">
                                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Nama Lengkap</p>
                                <p class="text-sm font-bold text-slate-800 mt-1">{{ $siswa->nama ?? '-' }}</p>
                            </div>
                            <div class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-2xl border border-sky-100">
                                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">NIS</p>
                                <p class="text-sm font-bold text-slate-800 mt-1">{{ $siswa->nis ?? '-' }}</p>
                            </div>
                            <div class="p-3.5 bg-gradient-to-r from-amber-50 to-white rounded-2xl border border-amber-100">
                                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Kelas</p>
                                <p class="text-sm font-bold text-slate-800 mt-1">{{ $siswa->kelas->nama ?? '-' }}</p>
                            </div>
                            <div class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-2xl border border-sky-100">
                                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Jenis Kelamin</p>
                                <p class="text-sm font-bold text-slate-800 mt-1">{{ ucfirst($siswa->jenis_kelamin ?? '-') }}</p>
                            </div>
                            <div class="p-3.5 bg-gradient-to-r from-amber-50 to-white rounded-2xl border border-amber-100">
                                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Username</p>
                                <p class="text-sm font-bold text-slate-800 mt-1">{{ auth()->user()->username ?? '-' }}</p>
                            </div>
                            <div class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-2xl border border-sky-100">
                                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Email</p>
                                <p class="text-sm font-bold text-slate-800 mt-1">{{ auth()->user()->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Ekskul -->
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .2s">
                        <div class="px-6 py-5 border-b border-sky-50">
                            <h2 class="text-sm font-extrabold text-slate-900">Informasi Ekskul</h2>
                            <p class="text-[11px] text-slate-400 mt-0.5">Status keanggotaan ekstrakurikulermu</p>
                        </div>
                        <div class="p-6">
                            @if($ekskul)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-2xl border border-sky-100 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center text-xs font-extrabold uppercase shadow-md shadow-sky-200 shrink-0">
                                            {{ substr($ekskul->nama_ekskul, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Nama Ekskul</p>
                                            <p class="text-sm font-bold text-slate-800 mt-0.5">{{ $ekskul->nama_ekskul }}</p>
                                        </div>
                                    </div>
                                    <div class="p-3.5 bg-gradient-to-r from-amber-50 to-white rounded-2xl border border-amber-100">
                                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Jabatan</p>
                                        <p class="mt-1.5">
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $siswa?->jabatan == 'ketua' ? 'bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 border border-amber-200' : 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-700 border border-sky-200' }}">
                                                {{ ucfirst($siswa->jabatan ?? 'Anggota') }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-2xl border border-sky-100">
                                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Status</p>
                                        <p class="text-sm font-bold text-emerald-600 mt-1 flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Aktif
                                        </p>
                                    </div>
                                    <div class="p-3.5 bg-gradient-to-r from-amber-50 to-white rounded-2xl border border-amber-100">
                                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Pembina</p>
                                        <p class="text-sm font-bold text-slate-800 mt-1">{{ $ekskul->pembina->nama ?? '-' }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <div class="mx-auto w-14 h-14 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500">Kamu belum terdaftar di ekskul manapun.</p>
                                    <a href="{{ route('siswa.katalog') }}" class="inline-block mt-2 text-sky-600 text-xs font-bold hover:underline">Lihat Katalog Ekskul</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pengajuan Keluar Ekskul -->
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .25s">
                        <div class="px-6 py-5 flex items-center justify-between border-b border-sky-50">
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900">Pengajuan Keluar Ekskul</h2>
                                <p class="text-[11px] text-slate-400 mt-0.5">Tindakan ini bersifat permanen</p>
                            </div>
                            <span class="text-[10px] font-bold text-amber-700 bg-gradient-to-r from-amber-100 to-yellow-100 border border-amber-200 px-3 py-1.5 rounded-full">Sakral</span>
                        </div>

                        <div class="p-6">
                            @if($ekskul)
                                <div class="p-4 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-2xl border border-amber-200 mb-4">
                                    <p class="text-xs text-amber-700 leading-relaxed">
                                        Pengajuan keluar dari ekskul bersifat permanen. Setelah dikeluarkan, kamu harus mendaftar ulang jika ingin bergabung kembali.
                                    </p>
                                </div>

                                @php
                                    $hasPendingPengajuan = isset($pengajuan) && $pengajuan->where('status', 'pending')->count() > 0;
                                @endphp

                                @if($hasPendingPengajuan)
                                    <div class="p-4 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-2xl border border-amber-200 mb-4">
                                        <p class="text-xs text-amber-800 font-semibold">
                                            ⏳ Kamu sudah mengajukan permohonan keluar. Mohon tunggu verifikasi dan persetujuan dari ketua ekskul.
                                        </p>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('siswa.pengajuan-keluar.store') }}">
                                        @csrf
                                        <div class="space-y-4">
                                            <div>
                                                <label class="text-xs font-bold text-slate-700 block mb-1.5">
                                                    Alasan Keluar <span class="text-red-500">*</span>
                                                </label>
                                                <textarea name="alasan" required rows="3"
                                                    class="w-full p-3 bg-sky-50/60 border @error('alasan') border-red-300 @else border-sky-100 @enderror rounded-2xl text-xs text-slate-800 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all"
                                                    placeholder="Tuliskan alasan kamu ingin keluar dari ekskul ini...">{{ old('alasan') }}</textarea>
                                                <p class="text-[10px] text-slate-400 mt-1.5">Wajib diisi dengan alasan yang jelas dan masuk akal (minimal 10 karakter / 2 kata). Alasan kosong seperti "asd", "gatau", atau "malas" tidak diterima.</p>
                                                @error('alasan')
                                                    <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <button type="submit" class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-red-200 hover:-translate-y-0.5">
                                                Ajukan Permohonan Keluar
                                            </button>
                                        </div>
                                    </form>
                                @endif

                                <!-- Riwayat Pengajuan Keluar -->
                                @if(isset($pengajuan) && count($pengajuan) > 0)
                                    <div class="mt-6 pt-6 border-t border-sky-50">
                                        <p class="text-xs font-bold text-slate-600 mb-3">Riwayat Pengajuan</p>
                                        <div class="space-y-2">
                                            @foreach($pengajuan as $item)
                                            <div class="p-3.5 bg-gradient-to-r from-sky-50 to-amber-50 rounded-2xl border border-sky-100 flex items-center justify-between gap-3">
                                                <div class="min-w-0">
                                                    <p class="text-xs text-slate-600 truncate">{{ $item->alasan }}</p>
                                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->isoFormat('DD MMM Y') }}</p>
                                                </div>
                                                <span class="shrink-0 px-3 py-1 rounded-full text-[10px] font-bold border
                                                    {{ $item->status == 'pending' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                                                    {{ $item->status == 'diterima' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : '' }}
                                                    {{ $item->status == 'ditolak' ? 'bg-red-100 text-red-700 border-red-200' : '' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-6">
                                    <p class="text-xs font-semibold text-slate-500">Kamu belum terdaftar di ekskul manapun.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </main>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function openSidebar() {
            document.getElementById('sidebar-mobile').classList.remove('hidden');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeSidebar() {
            document.getElementById('sidebar-mobile').classList.add('hidden');
            document.getElementById('sidebar-overlay').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // TOGGLE SIDEBAR DESKTOP (collapsible ala Claude)
        function toggleDesktopSidebar() {
            const sidebar = document.getElementById('sidebar-desktop');
            const icon = document.getElementById('sidebar-toggle-icon');
            const collapsed = sidebar.classList.toggle('collapsed');
            icon.style.transform = collapsed ? 'rotate(180deg)' : 'rotate(0deg)';
            localStorage.setItem('soul_sidebar_collapsed', collapsed ? '1' : '0');
        }

        // Terapkan status tersimpan saat halaman dimuat (tanpa animasi berkedip)
        (function() {
            if (localStorage.getItem('soul_sidebar_collapsed') === '1') {
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('sidebar-desktop').classList.add('collapsed');
                    document.getElementById('sidebar-toggle-icon').style.transform = 'rotate(180deg)';
                });
            }
        })();

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0EA5E9'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#EF4444'
                });
            @endif
        });
    </script>

</body>
</html>