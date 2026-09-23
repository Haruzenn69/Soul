<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Ekskul - SOUL</title>
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
    @include('partials.responsive-tables')
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
                    <a href="{{ route('siswa.katalog') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold text-xs shadow-sm shadow-sky-100 transition-all">
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
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
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
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
                <a href="{{ route('siswa.katalog') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold text-xs shadow-sm shadow-sky-100 transition-all">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
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
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
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
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari ekskul..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
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

            <!-- Header dengan Tombol Daftar -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-up">
                <div>
                    <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900">Katalog Ekskul</h1>
                    <p class="text-xs text-slate-400 mt-1 font-medium">Temukan ekskul yang sesuai dengan minatmu</p>
                </div>
                @if(!$isRegistered && !$isPending)
                    <a href="{{ route('siswa.daftar-ekskul') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-2xl shadow-lg shadow-sky-200 transition-all hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Daftar Ekskul
                    </a>
                @elseif($isPending)
                    <span class="px-4 py-2.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-2xl border border-amber-200 flex items-center gap-1.5 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Menunggu Verifikasi
                    </span>
                @else
                    <span class="px-4 py-2.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-2xl border border-emerald-200 shadow-sm">
                        Sudah Terdaftar
                    </span>
                @endif
            </div>

            <!-- Filter -->
            <div class="flex gap-2 flex-wrap animate-fade-up" style="animation-delay: .1s">
                <button class="px-4 py-2 bg-gradient-to-r from-sky-400 to-blue-500 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200">Semua</button>
                <button class="px-4 py-2 bg-white text-slate-600 text-xs font-bold rounded-xl border border-sky-100 shadow-sm hover:bg-sky-50 transition-all">Olahraga</button>
                <button class="px-4 py-2 bg-white text-slate-600 text-xs font-bold rounded-xl border border-sky-100 shadow-sm hover:bg-sky-50 transition-all">Seni</button>
                <button class="px-4 py-2 bg-white text-slate-600 text-xs font-bold rounded-xl border border-sky-100 shadow-sm hover:bg-sky-50 transition-all">Akademik</button>
            </div>

            <!-- Daftar Ekskul -->
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 lg:gap-5">
                @forelse($ekskuls as $index => $ekskul)
                <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 p-4 md:p-5 hover:shadow-xl hover:-translate-y-1 hover:border-sky-200 transition-all duration-300 animate-fade-up" style="animation-delay: {{ $index * 0.05 }}s">
                    <div class="flex items-center gap-2 md:gap-3 mb-3">
                        <div class="w-10 h-10 md:w-12 md:h-12 shrink-0 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white font-extrabold flex items-center justify-center text-[10px] md:text-sm shadow-lg shadow-sky-200 uppercase">
                            {{ substr($ekskul->nama_ekskul, 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xs md:text-sm font-extrabold text-slate-900 truncate">{{ $ekskul->nama_ekskul }}</h3>
                            <p class="text-[9px] md:text-[10px] text-slate-400 truncate font-medium">Pembina: {{ $ekskul->pembina->nama ?? '-' }}</p>
                        </div>
                    </div>
                    <p class="text-[10px] md:text-xs text-slate-600 mb-3 line-clamp-2 leading-relaxed">{{ $ekskul->deskripsi ?? 'Deskripsi belum tersedia' }}</p>

                    @if($ekskul->is_open_recruitment)
                        <div class="mb-3">
                            <span class="text-[9px] md:text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-full">Buka Pendaftaran</span>
                            <p class="text-[9px] md:text-[10px] text-slate-400 mt-1.5 font-medium">{{ $ekskul->jadwal ?? 'Jadwal belum diatur' }}</p>
                        </div>
                        <a href="{{ route('siswa.form-daftar', $ekskul->id) }}" class="block w-full py-2 md:py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-[10px] md:text-xs font-bold rounded-xl transition-all shadow-md shadow-sky-200 text-center hover:-translate-y-0.5">
                            Daftar Sekarang
                        </a>
                    @else
                        <div class="mb-3">
                            <span class="text-[9px] md:text-[10px] font-bold text-red-500 bg-red-50 border border-red-100 px-2 py-1 rounded-full">Tidak Membuka Pendaftaran</span>
                            <p class="text-[9px] md:text-[10px] text-slate-400 mt-1.5 font-medium">{{ $ekskul->jadwal ?? 'Jadwal belum diatur' }}</p>
                        </div>
                        <button class="w-full py-2 md:py-2.5 bg-slate-100 text-slate-400 text-[10px] md:text-xs font-bold rounded-xl cursor-not-allowed border border-slate-100" disabled>
                            Tidak Tersedia
                        </button>
                    @endif

                    <div class="mt-3 pt-3 border-t border-sky-50">
                        <a href="{{ route('ekskul.detail', $ekskul) }}" class="text-sky-600 text-[10px] md:text-xs font-bold hover:underline flex items-center gap-1">
                            Lihat Detail
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-span-2 lg:col-span-3 bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center py-12">
                    <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <p class="text-xs font-semibold text-slate-500">Belum ada ekskul yang tersedia.</p>
                </div>
                @endforelse
            </div>

        </main>
    </div>

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
    </script>

</body>
</html>