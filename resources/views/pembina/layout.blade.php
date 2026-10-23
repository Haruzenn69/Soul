<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pembina') - SOUL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-mode-head')
    <style>

        /* Sidebar mobile selalu overlay, jangan dipaksa jadi relative (split layar) */
        #sidebar-mobile { position: fixed; }
    </style>
    @include('partials.responsive-tables')
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-amber-50 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-950 text-slate-800 dark:text-slate-200 font-sans antialiased flex min-h-screen overflow-x-hidden selection:bg-sky-100 dark:selection:bg-sky-900 selection:text-sky-700 dark:selection:text-sky-300">@php
    $__pembinaEkskuls = auth()->user()?->pembina?->ekskuls ?? collect();
    $__anggotaItem = [
        'icon'     => 'users',
        'label'    => 'Data Anggota',
        'is'       => ['pembina.anggota*', 'pembina.ekskul.pilih-ketua*'],
        'children' => $__pembinaEkskuls->map(fn ($e) => [
            'icon'  => 'users',
            'label' => $e->nama_ekskul,
            'url'   => route('pembina.anggota', ['ekskul' => $e->id]),
            'is'    => (request()->routeIs('pembina.anggota') && (int) request()->input('ekskul') === $e->id) || (request()->routeIs('pembina.ekskul.pilih-ketua*') && (request()->route('ekskul') instanceof \App\Models\Ekskul ? request()->route('ekskul')->id : request()->route('ekskul')) == $e->id) ? 'pembina.*' : null,
        ])->toArray(),
    ];
@endphp
@include('partials.pill-sidebar', [
    'psTitle' => 'Menu Pembina',
    'psLogoBrand' => 'SOUL',
    'psDashboardUrl' => route('pembina.dashboard'),
    'psNotifUrl' => route('pembina.notifikasi'),
    'psProfileUrl' => route('pembina.profile'),
    'psItems' => [
        ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => route('pembina.dashboard'), 'is' => 'pembina.dashboard'],
        ['icon' => 'users', 'label' => 'Keanggotaan', 'children' => [
            ['icon' => 'clipboard-list', 'label' => 'Pendaftaran', 'url' => route('pembina.pendaftaran'), 'is' => 'pembina.pendaftaran'],
            $__anggotaItem,
            ['icon' => 'user-check', 'label' => 'Kelola Pelatih', 'url' => route('pembina.pelatih.index'), 'is' => 'pembina.pelatih.*'],
        ]],
        ['icon' => 'calendar', 'label' => 'Presensi', 'url' => route('pembina.presensi'), 'is' => 'pembina.presensi'],
        ['icon' => 'clipboard-check', 'label' => 'Penilaian & Laporan', 'children' => [
            ['icon' => 'clipboard-check', 'label' => 'Penilaian', 'url' => route('pembina.penilaian'), 'is' => 'pembina.penilaian*'],
            ['icon' => 'bars', 'label' => 'Rekap Absensi', 'url' => route('pembina.rekap'), 'is' => 'pembina.rekap'],
            ['icon' => 'document', 'label' => 'Laporan Bulanan', 'url' => route('pembina.laporan.index'), 'is' => 'pembina.laporan.*'],
        ]],
        ['icon' => 'chat', 'label' => 'Kelola Konten', 'children' => [
            ['icon' => 'chat', 'label' => 'Testimoni', 'url' => route('pembina.testimoni.index'), 'is' => 'pembina.testimoni.*'],
            ['icon' => 'help', 'label' => 'FAQ', 'url' => route('pembina.faq.index'), 'is' => 'pembina.faq.*'],
        ]],
        ['icon' => 'user', 'label' => 'Profil', 'url' => route('pembina.profile'), 'is' => 'pembina.profile'],
    ],
    'psMore' => [],
])

<!-- MAIN CONTENT -->
    <div class="w-full flex-1 flex flex-col min-w-0 ps-page">

        <!-- TOP NAVBAR HEADER -->
        <header class="px-4 md:px-8 py-4 bg-white/70 dark:bg-neutral-900/80 backdrop-blur-lg border-b border-sky-100 dark:border-neutral-800 flex items-center justify-between gap-3 md:gap-4 sticky top-0 z-30">
            <!-- KIRI: Hamburger (Mobile) + Logo + Search -->
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <!-- Tombol Hamburger (Mobile) - Pindah ke Kiri -->
                <button onclick="openSidebar()" class="w-9 h-9 rounded-full bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex items-center justify-center text-slate-500 dark:text-slate-400 md:hidden shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <!-- Logo Mobile -->
                <div class="flex items-center gap-2 md:hidden shrink-0">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-sky-300">P</div>
                    <span class="font-extrabold text-sm tracking-tight text-slate-900 dark:text-white">SOUL</span>
                </div>

                <!-- Search (Desktop) -->
                <div class="soul-search-wrap relative w-full max-w-md hidden sm:block">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="search" data-soul-search placeholder="Cari data siswa, presensi, laporan..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 dark:focus:border-sky-500 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition-all">
                </div>
            </div>

            <!-- KANAN: Info User + Notifikasi + Logout -->
            <div class="flex items-center gap-2 md:gap-3 shrink-0">
                <div class="bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50 px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2 hidden sm:block">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Pembina
                </div>
                <a href="{{ route('pembina.notifikasi') }}" class="w-9 h-9 rounded-xl bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex items-center justify-center text-xs relative text-slate-600 dark:text-slate-400 hover:bg-sky-50 dark:hover:bg-neutral-700 hover:border-sky-200 dark:hover:border-neutral-600 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(($unreadNotifCount ?? 0) > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-amber-400 to-yellow-500 absolute top-1.5 right-1.5 border-2 border-white dark:border-neutral-800 shadow-sm"></span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="bg-red-50 dark:bg-rose-950/30 hover:bg-red-100 dark:hover:bg-rose-900/40 text-red-400 dark:text-rose-400 px-4 py-2 rounded-lg text-xs font-semibold border border-red-100 dark:border-rose-900/50 transition shadow-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="p-4 md:p-8 space-y-6 overflow-y-auto">
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

        {{-- Pencarian global dengan suggestion (seperti Google) --}}
    @include('partials.search-autocomplete')

@stack('scripts')

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

    @include('partials.onboarding')
</body>
</html>
