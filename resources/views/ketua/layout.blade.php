<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SOUL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-mode-head')
    <style>

        /* Sidebar mobile selalu overlay, jangan dipaksa jadi relative (split layar) */
        #sidebar-mobile { position: fixed; }

        /* Aksesibilitas & mikro-interaksi ketua */
        #sidebar-mobile nav a {
            min-height: 42px;
        }
        #sidebar-mobile a:focus-visible,
        #sidebar-mobile button:focus-visible,
        header a:focus-visible,
        header button:focus-visible {
            outline: 3px solid rgba(56, 189, 248, .45);
            outline-offset: 2px;
        }
        #sidebar-mobile nav > a:hover {
            transform: translateX(2px);
        }
        #sidebar-mobile nav > a[href*="profil-ekskul"],
        #sidebar-mobile nav > a[href*="prestasi"],
        #sidebar-mobile nav > a[href*="testimoni"],
        #sidebar-mobile nav > a[href*="faq"] {
            display: none;
        }
        #sidebar-mobile nav > a[href*="kegiatan"],
        #sidebar-mobile nav > a[href*="rekap-absensi"],
        #sidebar-mobile nav > a[href*="presensi/rekap"],
        #sidebar-mobile nav > a[href*="laporan-bulanan"],
        #sidebar-mobile nav > a[href*="pendaftaran"],
        #sidebar-mobile nav > a[href*="anggota"],
        #sidebar-mobile nav > a[href*="pengajuan-keluar"] {
            display: none !important;
        }
        #sidebar-mobile .legacy-katalog-link {
            display: none !important;
        }
        #sidebar-mobile .legacy-membership-link,
        #sidebar-mobile .legacy-activity-link {
            display: none !important;
        }
        #sidebar-mobile {
            animation: sidebarIn .22s cubic-bezier(.22,1,.36,1);
        }
        @keyframes sidebarIn {
            from { opacity: 0; transform: translateX(-18px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            .animate-fade-up, .animate-blob, .animate-floaty, #sidebar-mobile { animation: none !important; }
        }
    </style>
    @include('partials.responsive-tables')
</head>
<body class="ketua-layout bg-gradient-to-br from-sky-50 via-white to-amber-50 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-950 text-slate-800 dark:text-slate-200 font-sans antialiased flex min-h-screen overflow-x-hidden selection:bg-sky-100 dark:selection:bg-sky-900 selection:text-sky-700 dark:selection:text-sky-300">

    @php $namaEkskul = auth()->user()->siswa?->pendaftarans()->where('status', 'diterima')->first()?->ekskul->nama_ekskul ?? ''; @endphp@include('partials.pill-sidebar', [
    'psTitle' => 'Menu Ketua',
    'psLogoBrand' => 'SOUL',
    'psDashboardUrl' => route('ketua.dashboard'),
    'psNotifUrl' => route('ketua.notifikasi'),
    'psProfileUrl' => route('profile.edit', ['from' => 'ketua']),
'psItems' => [
        ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => route('ketua.dashboard'), 'is' => 'ketua.dashboard'],
        ['icon' => 'users', 'label' => 'Keanggotaan', 'children' => [
            ['icon' => 'users', 'label' => 'Kelola Anggota', 'url' => route('ketua.anggota.index'), 'is' => ['ketua.anggota.index', 'ketua.anggota.update-status']],
            ['icon' => 'clipboard-list', 'label' => 'Pendaftaran', 'url' => route('ketua.pendaftaran.index'), 'is' => 'ketua.pendaftaran.*'],
            ['icon' => 'logout', 'label' => 'Pengajuan Keluar', 'url' => route('ketua.pengajuan-keluar.index'), 'is' => ['ketua.pengajuan-keluar.index', 'ketua.pengajuan-keluar.show', 'ketua.pengajuan-keluar.update']],
        ]],
        ['icon' => 'calendar', 'label' => 'Kegiatan', 'children' => [
            ['icon' => 'calendar', 'label' => 'Kegiatan', 'url' => route('ketua.kegiatan.index'), 'is' => 'ketua.kegiatan.*'],
            ['icon' => 'clipboard-check', 'label' => 'Rekap Absensi', 'url' => route('ketua.presensi.rekap'), 'is' => 'ketua.presensi.rekap'],
            ['icon' => 'columns', 'label' => 'Laporan Bulanan', 'url' => route('ketua.laporan-bulanan.index'), 'is' => 'ketua.laporan-bulanan.*'],
        ]],
        ['icon' => 'building', 'label' => 'Kelola Katalog', 'children' => [
            ['icon' => 'building', 'label' => 'Profil Ekskul', 'url' => route('ketua.profil-ekskul.edit'), 'is' => 'ketua.profil-ekskul.*'],
            ['icon' => 'star', 'label' => 'Prestasi', 'url' => route('ketua.prestasi.index'), 'is' => 'ketua.prestasi.*'],
            ['icon' => 'chat', 'label' => 'Testimoni', 'url' => route('ketua.testimoni.index'), 'is' => 'ketua.testimoni.*'],
            ['icon' => 'help', 'label' => 'FAQ', 'url' => route('ketua.faq.index'), 'is' => 'ketua.faq.*'],
        ]],
        ['icon' => 'user', 'label' => 'Profile', 'url' => route('profile.edit', ['from' => 'ketua']), 'is' => 'profile.edit'],
    ],
    'psMore' => [],
])

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 w-full ps-page">

        <!-- TOP NAVBAR HEADER -->
        <header class="px-4 md:px-8 py-4 bg-white/70 dark:bg-neutral-900/70 backdrop-blur-lg border-b border-sky-100 dark:border-neutral-800 flex items-center justify-between gap-3 md:gap-4 sticky top-0 z-30">
            <!-- KIRI: Hamburger (Mobile) + Logo + Search -->
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button type="button" aria-label="Buka menu navigasi" aria-controls="sidebar-mobile" aria-expanded="false" onclick="openSidebar()" class="w-9 h-9 rounded-full bg-white border border-sky-100 flex items-center justify-center text-slate-500 md:hidden shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <!-- Logo Mobile -->
                <div class="flex items-center gap-2 md:hidden shrink-0">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-sky-300">K</div>
                    <span class="font-extrabold text-sm tracking-tight text-slate-900">SOUL</span>
                </div>

                <!-- Search (Desktop) -->
                <form method="GET" action="{{ route('ketua.kegiatan.index') }}" class="soul-search-wrap relative w-full max-w-md hidden sm:block">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center w-4 h-4 pointer-events-none opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="search" name="cari" data-soul-search value="{{ request('cari') }}" placeholder="Cari kegiatan, anggota, pendaftaran..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 dark:bg-neutral-800/70 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition-all">
                </form>
            </div>

            <!-- KANAN: Info User + Notifikasi + Logout -->
            <div class="flex items-center gap-2 md:gap-3 shrink-0">
                <div class="bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-800/50 px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2 hidden sm:block">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Ketua
                </div>

                @php
                    $__siswaKetua     = auth()->user()?->siswa;
                    $__activeEkskulsK = $__siswaKetua ? $__siswaKetua->activeEkskuls() : collect();
                    $__hasMultiK      = $__activeEkskulsK->count() >= 2;
                    $__ledEkskulK     = $__siswaKetua?->ekskulDipimpin();

                    if ($__hasMultiK) {
                        $__othEksK = $__ledEkskulK
                            ? $__activeEkskulsK->firstWhere('id', '!=', $__ledEkskulK->id)
                            : $__activeEkskulsK->skip(1)->first();
                    }
                @endphp

                {{-- Switch Dashboard: tepat di sebelah tombol notifikasi jika ketua ikut 2 ekskul --}}
                @if($__hasMultiK && isset($__othEksK) && $__othEksK)
                    <!-- Desktop: Tombol Switch Dashboard (Tema Biru) -->
                    <a href="{{ route('siswa.dashboard', ['ekskul' => $__othEksK->id]) }}"
                       id="header-switch-dashboard-ketua"
                       title="Switch Dashboard ke {{ $__othEksK->nama_ekskul }} (Anggota)"
                       class="group relative hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/40 dark:hover:bg-sky-900/50 border border-sky-200 dark:border-sky-800/70 text-sky-700 dark:text-sky-300 text-xs font-bold transition-all shadow-sm hover:shadow shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:rotate-180 duration-500 text-sky-600 dark:text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span class="whitespace-nowrap font-bold">Switch Dashboard</span>
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-sky-200/60 dark:bg-sky-900/80 text-[10px] font-semibold text-sky-800 dark:text-sky-200 truncate max-w-[100px]">
                            {{ $__othEksK->nama_ekskul }}
                        </span>
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                    </a>

                    <!-- Mobile: Tombol Switch Dashboard ringkas (Tema Biru) -->
                    <a href="{{ route('siswa.dashboard', ['ekskul' => $__othEksK->id]) }}"
                       id="header-switch-dashboard-ketua-mobile"
                       title="Switch Dashboard ke {{ $__othEksK->nama_ekskul }}"
                       class="sm:hidden relative w-9 h-9 rounded-xl bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/40 dark:hover:bg-sky-900/50 border border-sky-200 dark:border-sky-800/70 text-sky-700 dark:text-sky-300 flex items-center justify-center transition-all shadow-sm shrink-0">
                        <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-sky-500 border-2 border-white dark:border-neutral-900 animate-pulse"></span>
                    </a>
                @endif

                <a href="{{ route('ketua.notifikasi') }}" aria-label="Buka notifikasi" class="w-9 h-9 rounded-xl bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex items-center justify-center text-xs relative text-slate-600 dark:text-slate-400 hover:bg-sky-50 dark:hover:bg-neutral-700 hover:border-sky-200 dark:hover:border-neutral-700 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(($unreadNotifCount ?? 0) > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-amber-400 to-yellow-500 absolute top-1.5 right-1.5 border-2 border-white shadow-sm"></span>
                    @endif
                </a>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="bg-red-50 dark:bg-red-950/30 hover:bg-red-100 dark:hover:bg-red-950/50 text-red-500 dark:text-red-400 px-4 py-2 rounded-lg text-xs font-semibold border border-red-100 dark:border-red-900/50 transition shadow-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="w-full min-w-0 p-4 md:p-8 space-y-6 overflow-y-auto">
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-xs font-semibold rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 text-red-700 dark:text-red-300 text-xs font-semibold rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

        {{-- Pencarian global dengan suggestion (seperti Google) --}}
    @include('partials.search-autocomplete')

@stack('scripts')

    @include('partials.onboarding')

    <script>
        let sidebarTrigger = null;

        function openSidebar() {
            sidebarTrigger = document.activeElement;
            document.getElementById('sidebar-mobile').classList.remove('hidden');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            document.querySelector('[aria-controls="sidebar-mobile"]')?.setAttribute('aria-expanded', 'true');
            document.querySelector('#sidebar-mobile a, #sidebar-mobile button')?.focus();
        }
        function closeSidebar() {
            document.getElementById('sidebar-mobile').classList.add('hidden');
            document.getElementById('sidebar-overlay').classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            document.querySelector('[aria-controls="sidebar-mobile"]')?.setAttribute('aria-expanded', 'false');
            if (sidebarTrigger) sidebarTrigger.focus();
        }

        document.querySelectorAll('#sidebar-mobile a').forEach((link) => {
            link.addEventListener('click', closeSidebar);
        });

        document.querySelector('#sidebar-mobile nav')?.addEventListener('click', (event) => {
            const summary = event.target.closest('summary');
            const activeGroup = summary?.parentElement;
            if (!(activeGroup instanceof HTMLDetailsElement)) return;

            document.querySelectorAll('#sidebar-mobile nav details[open]').forEach((group) => {
                if (group !== activeGroup) group.removeAttribute('open');
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !document.getElementById('sidebar-mobile').classList.contains('hidden')) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
