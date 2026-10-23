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
<body class="bg-gradient-to-br from-sky-50 via-white to-amber-50 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-950 text-slate-800 dark:text-slate-200 font-sans antialiased flex min-h-screen overflow-x-hidden selection:bg-sky-100 dark:selection:bg-sky-900 selection:text-sky-700 dark:selection:text-sky-300">
@include('partials.pill-sidebar', [
    'psTitle' => 'Menu Kesiswaan',
    'psLogoBrand' => 'SOUL',
    'psDashboardUrl' => route('kesiswaan.dashboard'),
    'psNotifUrl' => route('kesiswaan.notifikasi'),
    'psProfileUrl' => route('kesiswaan.profile'),
    'psItems' => [
        ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => route('kesiswaan.dashboard'), 'is' => 'kesiswaan.dashboard'],
        [
            'icon' => 'users',
            'label' => 'Akun Pengguna',
            'is' => ['kesiswaan.users.*', 'kesiswaan.siswa.*', 'kesiswaan.pembina.*', 'kesiswaan.pelatih.*'],
            'children' => [
                ['icon' => 'users', 'label' => 'Akun Pengguna', 'url' => route('kesiswaan.users.index'), 'is' => ['kesiswaan.users.*']],
                ['icon' => 'users', 'label' => 'Data Siswa', 'url' => route('kesiswaan.siswa.index'), 'is' => ['kesiswaan.siswa.*']],
                ['icon' => 'user', 'label' => 'Data Pembina', 'url' => route('kesiswaan.pembina.index'), 'is' => ['kesiswaan.pembina.*']],
                ['icon' => 'user-check', 'label' => 'Data Pelatih', 'url' => route('kesiswaan.pelatih.index'), 'is' => ['kesiswaan.pelatih.*']],
            ],
        ],
        ['icon' => 'building', 'label' => 'Data Ekskul', 'url' => route('kesiswaan.ekskuls.index'), 'is' => 'kesiswaan.ekskuls.*'],
        ['icon' => 'document', 'label' => 'Data Kelas', 'url' => route('kesiswaan.kelas.index'), 'is' => 'kesiswaan.kelas.*'],
        ['icon' => 'calendar', 'label' => 'Tahun Ajaran', 'url' => route('kesiswaan.tahun-ajaran.index'), 'is' => 'kesiswaan.tahun-ajaran.*'],
        ['icon' => 'clipboard-check', 'label' => 'Laporan Penilaian', 'url' => route('kesiswaan.laporan-penilaian.index'), 'is' => 'kesiswaan.laporan-penilaian.*'],
        ['icon' => 'user', 'label' => 'Profile', 'url' => route('kesiswaan.profile'), 'is' => 'kesiswaan.profile'],
    ],
    'psMore' => [],
])

<!-- MAIN CONTENT CONTAINER -->
    <div class="w-full flex-1 flex flex-col min-w-0 ps-page">

        <!-- TOP NAVBAR HEADER -->
        <header class="px-4 md:px-8 py-3.5 bg-white/80 dark:bg-neutral-900/80 backdrop-blur-xl border-b border-sky-100/80 dark:border-neutral-800 flex items-center justify-between gap-3 md:gap-4 sticky top-0 z-30">
            <!-- KIRI: Hamburger (Mobile) + Logo + Search -->
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <!-- Tombol Hamburger (Mobile) -->
                <button onclick="openSidebar()" class="w-9 h-9 rounded-full bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex items-center justify-center text-slate-500 dark:text-slate-400 md:hidden shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <!-- Logo Mobile -->
                <div class="flex items-center gap-2 md:hidden shrink-0">
                    <div class="w-8 h-8 rounded-xl overflow-hidden flex items-center justify-center shadow-md shadow-sky-200 dark:shadow-sky-900">
                        <img src="{{ asset('images/logo-light-nobg.png') }}" alt="SOUL" class="w-full h-full object-contain ps-logo-img-light">
                        <img src="{{ asset('images/logo-dark-nobg.png') }}" alt="SOUL" class="w-full h-full object-contain ps-logo-img-dark">
                    </div>
                    <span class="font-extrabold text-sm tracking-tight text-slate-900 dark:text-white">SOUL</span>
                </div>

                <!-- Search (Desktop) -->
                <form method="GET" action="{{ route('kesiswaan.users.index') }}" class="soul-search-wrap relative w-full max-w-sm hidden sm:block">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center w-4 h-4 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="search" name="q" data-soul-search value="{{ request('q') }}" placeholder="Cari akun, siswa, kelas, ekskul..." class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200/70 dark:border-neutral-700 rounded-full text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition-all">
                </form>
            </div>



            <!-- KANAN: Notifikasi -->
            <div class="flex items-center gap-2.5 md:gap-3 shrink-0">
                <a href="{{ route('kesiswaan.notifikasi') }}" class="w-9 h-9 rounded-full bg-slate-50 dark:bg-neutral-800 border border-slate-200/70 dark:border-neutral-700 flex items-center justify-center text-xs relative text-slate-600 dark:text-slate-400 hover:bg-sky-50 dark:hover:bg-neutral-700 hover:border-sky-200 dark:hover:border-neutral-700 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(($unreadNotifCount ?? 0) > 0)
                        <span class="w-2 h-2 rounded-full bg-amber-400 absolute top-1.5 right-1.5 border border-white dark:border-neutral-900 shadow-sm"></span>
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
    {{-- Pencarian global dengan suggestion (seperti Google) --}}
    @include('partials.search-autocomplete')

</body>
</html>
