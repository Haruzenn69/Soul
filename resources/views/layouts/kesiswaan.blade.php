<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SOUL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-mode-head')
    <style>

        /* Sidebar mobile selalu overlay, jangan dipaksa jadi relative (split layar) */
        #sidebar-mobile { position: fixed; }

    </style>
    @include('partials.responsive-tables')
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-amber-50 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-950 text-slate-800 dark:text-slate-200 font-sans antialiased flex min-h-screen overflow-x-hidden selection:bg-sky-100 dark:selection:bg-sky-900 selection:text-sky-700 dark:selection:text-sky-300">@include('partials.pill-sidebar', [
    'psTitle' => 'Menu Kesiswaan',
    'psLogoBrand' => 'SOUL',
    'psDashboardUrl' => route('kesiswaan.dashboard'),
    'psNotifUrl' => route('kesiswaan.notifikasi'),
    'psProfileUrl' => route('kesiswaan.profile'),
    'psItems' => [
        ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => route('kesiswaan.dashboard'), 'is' => 'kesiswaan.dashboard'],
        ['icon' => 'users', 'label' => 'Akun Pengguna', 'url' => route('kesiswaan.users.index'), 'is' => 'kesiswaan.users.*'],
        ['icon' => 'building', 'label' => 'Data Ekskul', 'url' => route('kesiswaan.ekskuls.index'), 'is' => 'kesiswaan.ekskuls.*'],
        ['icon' => 'document', 'label' => 'Data Kelas', 'url' => route('kesiswaan.kelas.index'), 'is' => 'kesiswaan.kelas.*'],
        ['icon' => 'users', 'label' => 'Data Siswa', 'url' => route('kesiswaan.siswa.index'), 'is' => 'kesiswaan.siswa.*'],
        ['icon' => 'user', 'label' => 'Data Pembina', 'url' => route('kesiswaan.pembina.index'), 'is' => 'kesiswaan.pembina.*'],
        ['icon' => 'user-check', 'label' => 'Data Pelatih', 'url' => route('kesiswaan.pelatih.index'), 'is' => 'kesiswaan.pelatih.*'],
        ['icon' => 'clipboard-check', 'label' => 'Laporan Penilaian', 'url' => route('kesiswaan.laporan-penilaian.index'), 'is' => 'kesiswaan.laporan-penilaian.*'],
        ['icon' => 'user', 'label' => 'Profile', 'url' => route('kesiswaan.profile'), 'is' => 'kesiswaan.profile'],
    ],
    'psMore' => [],
])

<!-- MOBILE SIDEBAR OVERLAY -->
    <div id="sidebar-overlay" class="hidden fixed inset-0 z-40 bg-slate-900/50 md:hidden" onclick="closeSidebar()"></div>

    <!-- MOBILE SIDEBAR -->
    <div id="sidebar-mobile" class="hidden fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-neutral-900 border-r border-sky-100 dark:border-neutral-800 overflow-hidden flex flex-col justify-between p-4 md:p-5 md:hidden shadow-2xl">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-slate-100/80 dark:bg-neutral-800/80 blur-3xl animate-blob"></div>
            <div class="absolute -bottom-24 -left-16 w-64 h-64 rounded-full bg-slate-100/80 dark:bg-neutral-800/80 blur-3xl animate-blob" style="animation-delay: 3s"></div>
        </div>

        <div class="relative h-full flex flex-col overflow-y-auto">
            <div class="flex items-center justify-between mb-6 mt-1 px-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-500 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-sky-300">
                        SOUL
                    </div>
                    <div>
                        <h1 class="font-extrabold text-sm tracking-tight text-slate-900 dark:text-white leading-none">SOUL</h1>
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-semibold tracking-wider uppercase">Panel Kesiswaan</span>
                    </div>
                </div>
                <button onclick="closeSidebar()" class="w-8 h-8 rounded-full bg-slate-50 dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-neutral-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="text-xs font-bold text-slate-400 dark:text-slate-500 tracking-wider uppercase mb-2 px-3">Menu Utama</div>
            <nav class="space-y-1.5">
                <a href="{{ route('kesiswaan.dashboard') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.dashboard') ? 'bg-sky-100 dark:bg-sky-950/30 text-sky-800 dark:text-sky-300 rounded-xl font-semibold shadow-sm shadow-sky-100 dark:shadow-sky-900/30' : 'text-slate-500 dark:text-slate-400 hover:bg-sky-50 dark:hover:bg-sky-950/30 hover:text-sky-700 dark:hover:text-sky-300 rounded-xl font-medium' }} text-xs transition-all">
                    @if(request()->routeIs('kesiswaan.dashboard'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </span>
                    Dashboard
                </a>
                <a href="{{ route('kesiswaan.users.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.users.*') ? 'bg-sky-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if(request()->routeIs('kesiswaan.users.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </span>
                    Akun Pengguna
                </a>
                <a href="{{ route('kesiswaan.ekskuls.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.ekskuls.*') ? 'bg-sky-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if(request()->routeIs('kesiswaan.ekskuls.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                    Data Ekskul
                </a>
                <a href="{{ route('kesiswaan.kelas.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.kelas.*') ? 'bg-sky-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if(request()->routeIs('kesiswaan.kelas.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                    Data Kelas
                </a>
                <a href="{{ route('kesiswaan.siswa.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.siswa.*') ? 'bg-sky-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if(request()->routeIs('kesiswaan.siswa.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </span>
                    Data Siswa
                </a>
                <a href="{{ route('kesiswaan.pelatih.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.pelatih.*') ? 'bg-sky-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if(request()->routeIs('kesiswaan.pelatih.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                    </span>
                    Data Pelatih
                </a>
                <a href="{{ route('kesiswaan.laporan-penilaian.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.laporan-penilaian.*') ? 'bg-sky-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if(request()->routeIs('kesiswaan.laporan-penilaian.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/></svg>
                    </span>
                    Laporan Penilaian
                </a>
                <a href="{{ route('kesiswaan.profile') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.profile') ? 'bg-sky-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if(request()->routeIs('kesiswaan.profile'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    Profil
                </a>
            </nav>
        </div>

        <div class="relative mt-auto pt-6">
            <div class="bg-slate-50 p-3 rounded-2xl flex items-center justify-between border border-sky-100 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-amber-300 text-amber-900 font-extrabold flex items-center justify-center text-xs shadow-md shadow-amber-200">
                        {{ strtoupper(substr(auth()->user()->username ?? 'K', 0, 1)) }}
                    </div>
                    <div class="text-left">
                        <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->username }}</h4>
                        <p class="text-xs text-slate-400 font-medium">Staf Kesiswaan</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500 text-xs font-bold transition-colors">Keluar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="w-full flex-1 flex flex-col min-w-0 ps-page">

        <!-- TOP NAVBAR HEADER -->
        <header class="px-4 md:px-8 py-4 bg-white/70 dark:bg-neutral-900/70 backdrop-blur-lg border-b border-sky-100 dark:border-neutral-800 flex items-center justify-between gap-3 md:gap-4 sticky top-0 z-30">
            <!-- KIRI: Hamburger (Mobile) + Logo + Search -->
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <!-- Tombol Hamburger (Mobile) -->
                <button onclick="openSidebar()" class="w-9 h-9 rounded-full bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex items-center justify-center text-slate-500 dark:text-slate-400 md:hidden shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <!-- Logo Mobile -->
                <div class="flex items-center gap-2 md:hidden shrink-0">
                    <div class="w-8 h-8 rounded-xl bg-blue-500 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-sky-300">S</div>
                    <span class="font-extrabold text-sm tracking-tight text-slate-900 dark:text-white">SOUL</span>
                </div>

                <!-- Search (Desktop) -->
                <form method="GET" action="{{ route('kesiswaan.users.index') }}" class="relative w-full max-w-md hidden sm:block">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center w-4 h-4 pointer-events-none opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari akun, ekskul, kelas..." class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition-all">
                </form>
            </div>

            <!-- KANAN: Info User + Notifikasi -->
            <div class="flex items-center gap-2 md:gap-3 shrink-0">
                <div class="bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50 px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2 hidden sm:block">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Kesiswaan
                </div>
                <a href="{{ route('kesiswaan.notifikasi') }}" class="w-9 h-9 rounded-xl bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex items-center justify-center text-xs relative text-slate-600 dark:text-slate-400 hover:bg-sky-50 dark:hover:bg-neutral-700 hover:border-sky-200 dark:hover:border-neutral-700 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(($unreadNotifCount ?? 0) > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-amber-400 to-yellow-500 absolute top-1.5 right-1.5 border-2 border-white dark:border-neutral-900 shadow-sm"></span>
                    @endif
                </a>
            </div>
        </header>

        <main class="p-4 md:p-8 space-y-6 overflow-y-auto">
            @if (session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-xs font-semibold rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 text-red-700 dark:text-red-300 text-xs font-semibold rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
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
    </script>

    @yield('scripts')
</body>
</html>
