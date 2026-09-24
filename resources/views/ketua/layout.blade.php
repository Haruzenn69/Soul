<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SOUL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
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
<body class="ketua-layout bg-gradient-to-br from-sky-50 via-white to-amber-50 text-slate-800 font-sans antialiased flex min-h-screen overflow-x-hidden selection:bg-sky-100 selection:text-sky-700">

    @php $namaEkskul = auth()->user()->siswa?->pendaftarans()->where('status', 'diterima')->first()?->ekskul->nama_ekskul ?? ''; @endphp@include('partials.pill-sidebar', [
    'psTitle' => 'Menu Ketua',
    'psLogoBrand' => 'SOUL',
    'psDashboardUrl' => route('ketua.dashboard'),
    'psNotifUrl' => route('ketua.notifikasi'),
    'psProfileUrl' => route('profile.edit'),
'psItems' => [
        ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => route('ketua.dashboard'), 'is' => 'ketua.dashboard'],
        ['icon' => 'calendar', 'label' => 'Kegiatan', 'url' => route('ketua.kegiatan.index'), 'is' => 'ketua.kegiatan.*'],
        ['icon' => 'clipboard-check', 'label' => 'Rekap Absensi', 'url' => route('ketua.presensi.rekap'), 'is' => 'ketua.presensi.rekap'],
        ['icon' => 'clipboard-list', 'label' => 'Pendaftaran', 'url' => route('ketua.pendaftaran.index'), 'is' => 'ketua.pendaftaran.*'],
        ['icon' => 'users', 'label' => 'Kelola Anggota', 'url' => route('ketua.anggota.index'), 'is' => ['ketua.anggota.index', 'ketua.anggota.update-status']],
        ['icon' => 'logout', 'label' => 'Pengajuan Keluar', 'url' => route('ketua.pengajuan-keluar.index'), 'is' => ['ketua.pengajuan-keluar.index', 'ketua.pengajuan-keluar.show', 'ketua.pengajuan-keluar.update']],
        ['icon' => 'columns', 'label' => 'Laporan Bulanan', 'url' => route('ketua.laporan-bulanan.index'), 'is' => 'ketua.laporan-bulanan.*'],
        ['icon' => 'building', 'label' => 'Profil Ekskul', 'url' => route('ketua.profil-ekskul.edit'), 'is' => 'ketua.profil-ekskul.*'],
        ['icon' => 'star', 'label' => 'Prestasi', 'url' => route('ketua.prestasi.index'), 'is' => 'ketua.prestasi.*'],
        ['icon' => 'chat', 'label' => 'Testimoni', 'url' => route('ketua.testimoni.index'), 'is' => 'ketua.testimoni.*'],
        ['icon' => 'help', 'label' => 'FAQ', 'url' => route('ketua.faq.index'), 'is' => 'ketua.faq.*'],
        ['icon' => 'user', 'label' => 'Profile', 'url' => route('profile.edit'), 'is' => 'profile.edit'],
    ],
    'psMore' => [],
])

    <!-- MOBILE SIDEBAR OVERLAY -->
    <div id="sidebar-overlay" aria-hidden="true" class="hidden fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-[2px] md:hidden" onclick="closeSidebar()"></div>

    <!-- MOBILE SIDEBAR -->
    <div id="sidebar-mobile" role="dialog" aria-modal="true" aria-label="Menu navigasi Ketua" class="hidden fixed inset-y-0 left-0 z-50 w-[min(21rem,88vw)] bg-white border-r border-sky-100 overflow-hidden flex flex-col justify-between p-4 md:p-5 md:hidden shadow-2xl">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-sky-100/80 blur-3xl animate-blob"></div>
            <div class="absolute -bottom-24 -left-16 w-64 h-64 rounded-full bg-amber-100/80 blur-3xl animate-blob" style="animation-delay: 3s"></div>
        </div>

        <div class="relative h-full flex flex-col overflow-y-auto">
            <div class="flex items-center justify-between mb-6 mt-1 px-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-sky-300">
                        SOUL
                    </div>
                    <div>
                        <h1 class="font-extrabold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                        <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Panel Ketua {{ $namaEkskul }}</span>
                    </div>
                </div>
                <button type="button" aria-label="Tutup menu navigasi" onclick="closeSidebar()" class="w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex items-center justify-between mb-2 px-3">
                <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Menu utama</div>
                <span class="text-[9px] font-semibold text-slate-300">PANEL KETUA</span>
            </div>
            <nav aria-label="Menu utama mobile" class="space-y-1.5">
                <a href="{{ route('ketua.dashboard') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.dashboard') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if(request()->routeIs('ketua.dashboard'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </span>
                    Dashboard
                </a>
                <a href="{{ route('ketua.kegiatan.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.kegiatan.*') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if (request()->routeIs('ketua.kegiatan.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    Kegiatan
                </a>
                <a href="{{ route('ketua.presensi.rekap') }}" aria-current="{{ request()->routeIs('ketua.presensi.rekap') ? 'page' : 'false' }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.presensi.rekap') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if (request()->routeIs('ketua.presensi.rekap'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5v2h6V5m-5 8l2 2 4-4"/></svg>
                    </span>
                    Rekap Absensi
                </a>
                <a href="{{ route('ketua.pendaftaran.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.pendaftaran.*') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if (request()->routeIs('ketua.pendaftaran.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    Pendaftaran
                </a>
                <a href="{{ route('ketua.pengajuan-keluar.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.pengajuan-keluar.*') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if (request()->routeIs('ketua.pengajuan-keluar.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </span>
                    Pengajuan Keluar
                </a>
                <a href="{{ route('ketua.anggota.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.anggota.*') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if (request()->routeIs('ketua.anggota.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </span>
                    Kelola Anggota
                </a>

                <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1 mt-4 px-3">Kelola Katalog</div>
                <a href="{{ route('ketua.profil-ekskul.edit') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.profil-ekskul.*') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </span>
                    Profil Ekskul
                </a>
                <a href="{{ route('ketua.prestasi.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.prestasi.*') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </span>
                    Prestasi
                </a>
                <a href="{{ route('ketua.testimoni.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.testimoni.*') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </span>
                    Testimoni
                </a>
                <a href="{{ route('ketua.faq.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.faq.*') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    FAQ
                </a>

                <a href="{{ route('ketua.laporan-bulanan.index') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.laporan-bulanan.*') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                    @if (request()->routeIs('ketua.laporan-bulanan.*'))
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                    @endif
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    </span>
                    Laporan Bulanan
                </a>
            </nav>
        </div>

        <div class="relative mt-auto pt-6">
            <div class="bg-gradient-to-r from-sky-50 to-amber-50 p-3 rounded-2xl flex items-center justify-between border border-sky-100 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 font-extrabold flex items-center justify-center text-xs shadow-md shadow-amber-200">
                        {{ strtoupper(substr(auth()->user()->siswa->nama ?? 'K', 0, 1)) }}
                    </div>
                    <div class="text-left">
                        <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->siswa->nama ?? 'Ketua' }}</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Ketua {{ $namaEkskul }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500 text-xs font-bold transition-colors">Keluar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 w-full ps-page">

        <!-- TOP NAVBAR HEADER -->
        <header class="px-4 md:px-8 py-4 bg-white/70 backdrop-blur-lg border-b border-sky-100 flex items-center justify-between gap-3 md:gap-4 sticky top-0 z-30">
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
                <form method="GET" action="{{ route('ketua.kegiatan.index') }}" class="relative w-full max-w-md hidden sm:block">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center w-4 h-4 pointer-events-none opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari kegiatan, anggota, pendaftaran..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                </form>
            </div>

            <!-- KANAN: Info User + Notifikasi + Logout -->
            <div class="flex items-center gap-2 md:gap-3 shrink-0">
                <div class="bg-sky-50 text-sky-700 border border-sky-100 px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2 hidden sm:block">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Ketua
                </div>
                <a href="{{ route('ketua.notifikasi') }}" aria-label="Buka notifikasi" class="w-9 h-9 rounded-xl bg-white border border-sky-100 flex items-center justify-center text-xs relative text-slate-600 hover:bg-sky-50 hover:border-sky-200 transition-all shadow-sm">
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

        <main class="w-full min-w-0 p-4 md:p-8 space-y-6 overflow-y-auto">
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

    @stack('scripts')

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

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !document.getElementById('sidebar-mobile').classList.contains('hidden')) {
                closeSidebar();
            }
        });
    </script>
</body>
</html>
