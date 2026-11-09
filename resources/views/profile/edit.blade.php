<!DOCTYPE html>
@php
    $psAuthUser = auth()->user();
    $siswa = $siswa ?? $psAuthUser?->siswa;

    if (request('from') === 'siswa') {
        $psIsKetua = false;
    } elseif (request('from') === 'ketua') {
        $psIsKetua = $siswa ? ($ekskul && $siswa->riwayatKetua($ekskul)->whereNull('selesai')->exists() || $siswa->ekskulDipimpin() !== null) : false;
    } else {
        $isKetuaDiEkskul = $ekskul && $siswa && $siswa->riwayatKetua($ekskul)->whereNull('selesai')->exists();
        $psIsKetua = $ekskul ? $isKetuaDiEkskul : ($siswa ? ($siswa->ekskulDipimpin() !== null) : false);
    }
    $psIconSvg = [
        'dashboard' => '<path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/><path d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/><path d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>',
        'calendar' => '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'clipboard-check' => '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/>',
        'clipboard-list' => '<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'document' => '<path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
        'bars' => '<path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'users' => '<path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
        'user' => '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        'logout' => '<path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>',
        'lock' => '<rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/>',
        'columns' => '<path d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>',
        'building' => '<path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        'star' => '<path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
        'chat' => '<path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
        'help' => '<path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ];
    $psSidebar = $psIsKetua ? [
        'title' => 'Menu Ketua',
        'dash' => route('ketua.dashboard'),
        'notif' => route('ketua.notifikasi'),
        'items' => [
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
        'more' => [],
    ] : [
        'title' => $ekskul ? 'Menu Siswa · ' . $ekskul->nama_ekskul : 'Menu Siswa',
        'dash' => route('siswa.dashboard', $ekskul ? ['ekskul' => $ekskul->id] : []),
        'notif' => route('siswa.notifikasi'),
        'items' => [
            ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => route('siswa.dashboard', $ekskul ? ['ekskul' => $ekskul->id] : []), 'is' => 'siswa.dashboard'],
            ['icon' => 'document', 'label' => 'Katalog Ekskul', 'url' => route('siswa.katalog'), 'is' => ['siswa.katalog', 'ekskul.detail']],
            ['icon' => 'calendar', 'label' => 'Presensi & Kegiatan', 'url' => route('siswa.presensi', $ekskul ? ['ekskul' => $ekskul->id] : []), 'is' => 'siswa.presensi'],
            ['icon' => 'bars', 'label' => 'Rekap Absensi', 'url' => route('siswa.rekap', $ekskul ? ['ekskul' => $ekskul->id] : []), 'is' => 'siswa.rekap'],
            ['icon' => 'user', 'label' => 'Profil Saya', 'url' => route('profile.edit', array_merge($ekskul ? ['ekskul' => $ekskul->id] : [], ['from' => 'siswa'])), 'is' => 'profile.edit'],
        ],
        'more' => [
            ['label' => 'Daftar Ekskul', 'url' => route('siswa.daftar-ekskul'), 'is' => 'siswa.daftar-ekskul'],
            ['label' => 'Ajukan Keluar', 'url' => route('siswa.pengajuan-keluar', $ekskul ? ['ekskul' => $ekskul->id] : []), 'is' => ['siswa.pengajuan-keluar', 'siswa.pengajuan-keluar.store']],
        ],
    ];
@endphp
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $psIsKetua ? 'Profile Ketua - SOUL' : 'Profile Siswa - SOUL' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-mode-head')
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-amber-50 dark:from-neutral-950 dark:via-neutral-900 dark:to-neutral-950 text-slate-800 dark:text-slate-200 font-sans antialiased min-h-screen md:flex selection:bg-sky-100 dark:selection:bg-sky-900 selection:text-sky-700 dark:selection:text-sky-300 overflow-x-hidden">@include('partials.pill-sidebar', [
    'psTitle' => $psSidebar['title'],
    'psLogoBrand' => 'SOUL',
    'psDashboardUrl' => $psSidebar['dash'],
    'psNotifUrl' => '',
    'psProfileUrl' => route('profile.edit', array_merge($ekskul ? ['ekskul' => $ekskul->id] : [], ['from' => $psIsKetua ? 'ketua' : 'siswa'])),
    'psItems' => $psSidebar['items'],
    'psMore' => $psSidebar['more'],
])

    <!-- MOBILE SIDEBAR OVERLAY -->
    <div id="sidebar-overlay" class="hidden fixed inset-0 z-40 bg-black/50 backdrop-blur-[2px] md:hidden" onclick="closeSidebar()"></div>

    <!-- MOBILE SIDEBAR -->
    <div id="sidebar-mobile" class="hidden fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white dark:bg-neutral-900 border-r border-sky-100 dark:border-neutral-800 overflow-y-auto md:hidden shadow-2xl">
        <div class="relative h-full flex flex-col overflow-y-auto">
            <div class="flex items-center justify-between mb-8 px-2 mt-1">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl overflow-hidden flex items-center justify-center shadow-lg shadow-sky-200 dark:shadow-sky-900">
                        <img src="{{ asset('images/logo-light-nobg.png') }}" alt="SOUL" class="w-full h-full object-contain ps-logo-img-light">
                        <img src="{{ asset('images/logo-dark-nobg.png') }}" alt="SOUL" class="w-full h-full object-contain ps-logo-img-dark">
                    </div>
                    <div>
                        <h1 class="font-extrabold text-sm tracking-tight text-slate-900 dark:text-white leading-none">SOUL</h1>
                        <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                    </div>
                </div>
                <button onclick="closeSidebar()" class="w-8 h-8 rounded-full bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-neutral-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex items-center justify-between mb-2 px-3">
                <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Menu utama</div>
                <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500">{{ $psIsKetua ? 'PANEL KETUA' : 'PANEL SISWA' }}</span>
            </div>
            <nav aria-label="Menu utama mobile" class="space-y-1.5">
                @foreach($psSidebar['items'] as $psMItem)
                    @php
                        $psMActive = isset($psMItem['children'])
                            ? collect($psMItem['children'])->contains(fn ($child) => request()->routeIs((array) $child['is']))
                            : request()->routeIs((array) $psMItem['is']);
                    @endphp
                    @if(isset($psMItem['children']))
                        <details class="group" {{ $psMActive ? 'open' : '' }}>
                            <summary class="relative flex items-center gap-3 px-3.5 py-2.5 {{ $psMActive ? 'bg-gradient-to-r from-sky-100 to-blue-100 dark:from-sky-950/40 dark:to-blue-950/40 text-sky-800 dark:text-sky-200 rounded-xl font-semibold' : 'text-slate-500 dark:text-slate-400 hover:bg-sky-50 dark:hover:bg-neutral-800/60 hover:text-sky-700 dark:hover:text-sky-300 rounded-xl font-medium' }} text-xs transition-all cursor-pointer list-none">
                                <span class="text-base flex items-center justify-center w-4 h-4">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        {!! $psIconSvg[$psMItem['icon']] ?? '' !!}
                                    </svg>
                                </span>
                                {{ $psMItem['label'] }}
                                <svg class="w-4 h-4 ml-auto transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                            </summary>
                            <div class="ml-7 pl-4 mt-1 space-y-1 border-l border-sky-100 dark:border-neutral-800">
                                @foreach($psMItem['children'] as $psMChild)
                                    @php $psMChildActive = request()->routeIs((array) $psMChild['is']); @endphp
                                    <a href="{{ $psMChild['url'] }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs {{ $psMChildActive ? 'bg-sky-50 dark:bg-sky-950/50 text-sky-700 dark:text-sky-300 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:bg-sky-50 dark:hover:bg-neutral-800/60 hover:text-sky-700 dark:hover:text-sky-300' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            {!! $psIconSvg[$psMChild['icon']] ?? '' !!}
                                        </svg>
                                        {{ $psMChild['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @else
                        <a href="{{ $psMItem['url'] }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ $psMActive ? 'bg-gradient-to-r from-sky-100 to-blue-100 dark:from-sky-950/40 dark:to-blue-950/40 text-sky-800 dark:text-sky-200 rounded-xl font-semibold shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:bg-sky-50 dark:hover:bg-neutral-800/60 hover:text-sky-700 dark:hover:text-sky-300 rounded-xl font-medium' }} text-xs transition-all">
                            @if($psMActive)
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                            @endif
                            <span class="text-base flex items-center justify-center w-4 h-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    {!! $psIconSvg[$psMItem['icon']] ?? '' !!}
                                </svg>
                            </span>
                            {{ $psMItem['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="mt-auto pt-6">
                <div class="bg-gradient-to-r from-sky-50 to-amber-50 dark:from-neutral-800 dark:to-neutral-850 p-3 rounded-2xl flex items-center justify-between border border-sky-100 dark:border-neutral-700 shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 font-extrabold flex items-center justify-center text-xs shadow-md shadow-amber-200">
                            {{ strtoupper(substr($siswa->nama ?? auth()->user()->username ?? 'S', 0, 1)) }}
                        </div>
                        <div class="text-left">
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 leading-tight">{{ $siswa->nama ?? auth()->user()->username }}</h4>
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
    <div class="w-full flex-1 flex flex-col min-w-0 ps-page">

        <!-- TOP NAVBAR HEADER -->
        <header class="px-4 md:px-8 py-4 bg-white/70 dark:bg-neutral-900/70 backdrop-blur-lg border-b border-sky-100 dark:border-neutral-800 flex items-center justify-between gap-3 md:gap-4 sticky top-0 z-30">
            <!-- KIRI: Hamburger (Mobile) + Logo + Search -->
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <!-- Tombol Hamburger (Mobile) - Pindah ke Kiri -->
                <button onclick="openSidebar()" aria-label="Buka menu navigasi" class="w-9 h-9 rounded-full bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex items-center justify-center text-slate-500 dark:text-slate-400 md:hidden shadow-sm shrink-0">
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
                @if(! $psIsKetua)
                <form method="GET" action="{{ route('siswa.katalog') }}" class="relative w-full max-w-md hidden sm:block">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 dark:bg-neutral-800/80 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition-all">
                </form>
                @endif
            </div>

            <!-- KANAN: Info User + Notifikasi + Logout -->
            <div class="flex items-center gap-2 md:gap-3 shrink-0">
                <div class="bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-800/70 px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2 hidden sm:block">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> {{ $psIsKetua ? 'Ketua' : 'Siswa' }}
                </div>

                @php
                    $__activeEkskulsP  = $siswa ? $siswa->activeEkskuls() : collect();
                    $__hasMultiEkskulP = $__activeEkskulsP->count() >= 2;
                    if ($__hasMultiEkskulP && $ekskul) {
                        $__othEksP     = $__activeEkskulsP->firstWhere('id', '!=', $ekskul->id);
                        $__isOthKetuaP = $__othEksP && $siswa->riwayatKetua($__othEksP)->whereNull('selesai')->exists();
                        $__switchUrlP  = $__isOthKetuaP ? route('ketua.dashboard') : ($__othEksP ? route('siswa.dashboard', ['ekskul' => $__othEksP->id]) : '#');
                    }
                @endphp

                @if($__hasMultiEkskulP && isset($__othEksP) && $__othEksP)
                    <!-- Desktop: Tombol Switch Dashboard -->
                    <a href="{{ $__switchUrlP }}"
                       id="header-switch-dashboard-profile"
                       title="Switch Dashboard ke {{ $__othEksP->nama_ekskul }}{{ $__isOthKetuaP ? ' (Ketua)' : '' }}"
                       class="group relative hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/40 dark:hover:bg-sky-900/50 border border-sky-200 dark:border-sky-800/70 text-sky-700 dark:text-sky-300 text-xs font-bold transition-all shadow-sm hover:shadow shrink-0">
                        <svg class="w-3.5 h-3.5 transition-transform group-hover:rotate-180 duration-500 text-sky-600 dark:text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span class="whitespace-nowrap font-bold">Switch Dashboard</span>
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-sky-200/60 dark:bg-sky-900/80 text-[10px] font-semibold text-sky-800 dark:text-sky-200 truncate max-w-[100px]">
                            {{ $__othEksP->nama_ekskul }}
                            @if($__isOthKetuaP)
                                <span class="text-[9px] text-amber-600 dark:text-amber-400 font-extrabold">&bull; Ketua</span>
                            @endif
                        </span>
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                    </a>

                    <!-- Mobile: Tombol Switch Dashboard ringkas -->
                    <a href="{{ $__switchUrlP }}"
                       id="header-switch-dashboard-profile-mobile"
                       title="Switch Dashboard ke {{ $__othEksP->nama_ekskul }}{{ $__isOthKetuaP ? ' (Ketua)' : '' }}"
                       class="sm:hidden relative w-9 h-9 rounded-xl bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/40 dark:hover:bg-sky-900/50 border border-sky-200 dark:border-sky-800/70 text-sky-700 dark:text-sky-300 flex items-center justify-center transition-all shadow-sm shrink-0">
                        <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-sky-500 border-2 border-white dark:border-neutral-900 animate-pulse"></span>
                    </a>
                @endif

                <a href="{{ $psSidebar['notif'] }}" aria-label="Notifikasi" class="w-9 h-9 rounded-xl bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex items-center justify-center text-xs relative text-slate-600 dark:text-slate-400 hover:bg-sky-50 dark:hover:bg-neutral-700 hover:border-sky-200 dark:hover:border-neutral-700 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(($unreadNotifCount ?? 0) > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-amber-400 to-yellow-500 absolute top-1.5 right-1.5 border-2 border-white dark:border-neutral-900 shadow-sm"></span>
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

        <!-- MAIN PAGE CONTENT -->
        <main class="p-4 md:p-8 space-y-6 overflow-y-auto max-w-7xl mx-auto w-full">
            <!-- HEADER -->
            <div class="animate-fade-up">
                <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">Profil Saya</h1>
                <p class="text-xs text-slate-400 dark:text-slate-400 mt-1 font-medium">Kelola informasi akun, kontak, dan data diri kamu</p>
            </div>

            @php $profilLengkap = $siswa && $siswa->isProfileComplete(); @endphp

            @if($errors->any())
                <div class="p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/50 text-red-700 dark:text-red-300 text-xs font-semibold rounded-2xl shadow-sm animate-fade-up">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!$profilLengkap)
                <div class="p-5 rounded-3xl border border-amber-200 dark:border-amber-800/80 bg-amber-50/70 dark:bg-amber-950/20 shadow-sm animate-fade-up flex items-start gap-4">
                    <div class="w-10 h-10 shrink-0 rounded-2xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-200 dark:shadow-amber-950/50">
                        <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-sm font-extrabold text-amber-900 dark:text-amber-200">Lengkapi Data Diri Kamu</h2>
                        <p class="text-xs text-amber-700 dark:text-amber-300/90 mt-1 leading-relaxed">Data biodata siswa yang belum lengkap perlu diperbarui oleh pihak Kesiswaan. Kamu tetap dapat memperbarui foto profil, nomor telepon, dan email di bawah.</p>
                    </div>
                </div>
            @endif

            <!-- 2-COLUMN STRUCTURE -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <!-- KOLOM KIRI (lg:col-span-4): Identitas Profil + Ekskul + Pengajuan Keluar -->
                <div class="lg:col-span-4 space-y-6">

                    <!-- CARD IDENTITAS PROFIL -->
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm p-6 text-center animate-fade-up">
                        <!-- Foto Avatar -->
                        <div class="relative inline-block mx-auto">
                            <div class="w-28 h-28 md:w-32 md:h-32 rounded-full bg-slate-100 dark:bg-neutral-800 border-4 border-sky-400 dark:border-sky-600 flex items-center justify-center overflow-hidden shadow-lg shadow-sky-200 dark:shadow-sky-950/50">
                                <img id="siswa-avatar-preview" src="{{ $siswa?->foto_url ?? asset('images/default.png') }}" alt="Foto Profile" class="w-full h-full object-cover">
                            </div>
                            <span class="absolute bottom-1 right-1 w-7 h-7 rounded-full bg-sky-500 text-white flex items-center justify-center text-xs shadow-md border-2 border-white dark:border-neutral-900">
                                <i class="fa-solid fa-user text-[11px]"></i>
                            </span>
                        </div>

                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white mt-4">{{ $siswa?->nama ?? auth()->user()->username }}</h3>
                        <p class="text-xs text-sky-600 dark:text-sky-400 font-bold">@ {{ auth()->user()->username }}</p>

                        <div class="mt-2.5 flex items-center justify-center gap-2 flex-wrap">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $psIsKetua ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60' : 'bg-sky-100 text-sky-800 dark:bg-sky-950/50 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60' }}">
                                {{ $psIsKetua ? 'Ketua Ekskul' : 'Siswa' }}
                            </span>
                            @if($siswa?->kelas)
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300 border border-slate-200 dark:border-neutral-700">
                                    {{ $siswa->kelas->nama }}
                                </span>
                            @endif
                        </div>

                        <!-- Quick Metadata -->
                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-neutral-800 text-left space-y-2.5 text-xs">
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                                    <i class="fa-solid fa-id-card text-sky-500 text-xs"></i> NIS
                                </span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $siswa?->nis ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                                    <i class="fa-solid fa-graduation-cap text-sky-500 text-xs"></i> Angkatan
                                </span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $siswa?->angkatan ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center gap-1.5">
                                    <i class="fa-solid fa-calendar text-sky-500 text-xs"></i> Terdaftar Sejak
                                </span>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $siswa?->created_at ? \Carbon\Carbon::parse($siswa->created_at)->isoFormat('D MMM Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- CARD INFORMASI EKSKUL -->
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-neutral-800 flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-users text-sky-500 text-xs"></i>
                                <h2 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Ekskul Aktif</h2>
                            </div>
                            @if(isset($__hasMultiEkskulP) && $__hasMultiEkskulP)
                                <div class="flex items-center gap-1 bg-sky-50 dark:bg-neutral-800 p-1 rounded-xl">
                                    @foreach($__activeEkskulsP as $pEks)
                                        <a href="{{ route('profile.edit', ['ekskul' => $pEks->id]) }}"
                                           class="px-2 py-0.5 rounded-lg text-[10px] font-bold transition-all {{ ($ekskul?->id === $pEks->id) ? 'bg-sky-500 text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-sky-700 dark:hover:text-sky-300' }}">
                                            {{ $pEks->nama_ekskul }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            @if($ekskul)
                                @php
                                    $isKetuaDiEkskul = $ekskul && $siswa && $siswa->riwayatKetua($ekskul)->whereNull('selesai')->exists();
                                @endphp
                                <div class="space-y-3">
                                    <div class="p-3 bg-slate-50 dark:bg-neutral-800/80 rounded-2xl border border-slate-100 dark:border-neutral-700 flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xs font-extrabold uppercase shadow-sm shrink-0">
                                            {{ substr($ekskul->nama_ekskul, 0, 2) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Ekskul</p>
                                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate mt-0.5">{{ $ekskul->nama_ekskul }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2.5">
                                        <div class="p-3 bg-slate-50 dark:bg-neutral-800/80 rounded-2xl border border-slate-100 dark:border-neutral-700">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jabatan</p>
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $isKetuaDiEkskul ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300' }}">
                                                {{ $isKetuaDiEkskul ? 'Ketua' : 'Anggota' }}
                                            </span>
                                        </div>
                                        <div class="p-3 bg-slate-50 dark:bg-neutral-800/80 rounded-2xl border border-slate-100 dark:border-neutral-700">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</p>
                                            <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Aktif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-slate-50 dark:bg-neutral-800/80 rounded-2xl border border-slate-100 dark:border-neutral-700">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pembina</p>
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $ekskul->pembina->nama ?? 'Belum ditentukan' }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <div class="mx-auto w-12 h-12 rounded-2xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center text-slate-400 mb-2.5">
                                        <i class="fa-solid fa-shapes text-lg"></i>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Belum terdaftar di ekskul manapun.</p>
                                    <a href="{{ route('siswa.katalog') }}" class="inline-flex items-center gap-1.5 mt-2.5 text-blue-600 dark:text-blue-400 text-xs font-bold hover:underline">
                                        <span>Jelajahi Katalog Ekskul</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- CARD PENGAJUAN KELUAR (JIKA ADA EKSKUL) -->
                    @if($ekskul)
                        <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-rose-200 dark:border-rose-900/60 shadow-sm overflow-hidden animate-fade-up">
                            <div class="px-5 py-4 border-b border-rose-100 dark:border-rose-900/40 bg-rose-50/50 dark:bg-rose-950/20 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-door-open text-rose-500 text-xs"></i>
                                    <h2 class="text-xs font-extrabold text-rose-900 dark:text-rose-200 uppercase tracking-wider">Pengajuan Keluar</h2>
                                </div>
                                <span class="text-[9px] font-bold text-rose-700 dark:text-rose-300 bg-rose-100 dark:bg-rose-900/60 px-2 py-0.5 rounded-full">Permanen</span>
                            </div>

                            <div class="p-5">
                                @php
                                    $hasPendingPengajuan = isset($pengajuan) && $pengajuan->where('status', 'pending')->where('ekskul_id', $ekskul->id)->count() > 0;
                                @endphp

                                @if($hasPendingPengajuan)
                                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 rounded-2xl border border-amber-200 dark:border-amber-900/60 text-xs text-amber-800 dark:text-amber-300 font-semibold flex items-start gap-2">
                                        <i class="fa-solid fa-clock text-amber-600 mt-0.5 shrink-0"></i>
                                        <span>Permohonan keluar sedang menunggu verifikasi ketua ekskul {{ $ekskul->nama_ekskul }}.</span>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('siswa.pengajuan-keluar.store') }}" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="ekskul_id" value="{{ $ekskul->id }}">
                                        <div>
                                            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block mb-1">
                                                Alasan Keluar dari {{ $ekskul->nama_ekskul }} <span class="text-rose-500">*</span>
                                            </label>
                                            <textarea name="alasan" required rows="2"
                                                class="w-full p-2.5 bg-slate-50 dark:bg-neutral-800/80 border @error('alasan') border-rose-300 @else border-slate-200 dark:border-neutral-700 @enderror rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-rose-400 focus:ring-2 focus:ring-rose-100 transition-all"
                                                placeholder="Tuliskan alasan yang jelas...">{{ old('alasan') }}</textarea>
                                            @error('alasan')
                                                <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <button type="submit" class="w-full py-2 bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold rounded-xl transition shadow-sm">
                                            Kirim Pengajuan Keluar
                                        </button>
                                    </form>
                                @endif

                                <!-- Riwayat Pengajuan -->
                                @if(isset($pengajuan) && count($pengajuan) > 0)
                                    <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-neutral-800">
                                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2">Riwayat Pengajuan</p>
                                        <div class="space-y-2">
                                            @foreach($pengajuan as $item)
                                                <div class="p-2.5 bg-slate-50 dark:bg-neutral-800/60 rounded-xl border border-slate-100 dark:border-neutral-700 flex items-center justify-between gap-2 text-xs">
                                                    <div class="min-w-0">
                                                        <p class="text-[11px] font-medium text-slate-700 dark:text-slate-300 truncate">{{ $item->alasan }}</p>
                                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->isoFormat('DD MMM Y') }}</p>
                                                    </div>
                                                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] font-bold border
                                                        {{ $item->status == 'pending' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                                                        {{ $item->status == 'diterima' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : '' }}
                                                        {{ $item->status == 'ditolak' ? 'bg-rose-100 text-rose-700 border-rose-200' : '' }}">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>

                <!-- KOLOM KANAN (lg:col-span-8): Form Akun & Kontak + Biodata Sekolah + Ubah Password -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- CARD 1: FORM PENGATURAN AKUN & KONTAK (BISA DIUBAH SISWA) -->
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up">
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-neutral-800 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Pengaturan Akun & Kontak</h2>
                                <p class="text-xs text-slate-400 dark:text-slate-400 mt-0.5">Informasi ini dapat kamu perbarui secara mandiri</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-sky-50 dark:bg-sky-950/50 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60">
                                Dapat Diubah
                            </span>
                        </div>

                        <form method="POST" action="{{ route('siswa.profile.update-data') }}" enctype="multipart/form-data" class="p-6 space-y-4">
                            @csrf

                            <!-- Ganti Foto Profil -->
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider block mb-1.5">
                                    Unggah Foto Profil Baru
                                </label>
                                <input type="file" name="foto" id="siswa-foto-input" accept="image/jpeg,image/png,image/jpg,image/webp"
                                       onchange="if(this.files && this.files[0]){ const r = new FileReader(); r.onload = e => { const img = document.getElementById('siswa-avatar-preview'); if(img){ img.src = e.target.result; } }; r.readAsDataURL(this.files[0]); }"
                                       class="w-full px-3 py-2 bg-slate-50 dark:bg-neutral-800/60 border @error('foto') border-rose-300 @else border-slate-200 dark:border-neutral-700 @enderror rounded-2xl text-xs text-slate-800 dark:text-slate-300 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition file:mr-3 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Format gambar: JPG, PNG, atau WebP (maks. 2MB). Foto akan langsung ter-update di kartu profil sebelah kiri.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                                <!-- Username -->
                                <div>
                                    <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider block mb-1.5">
                                        Username Login <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">@</span>
                                        <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}" required
                                               class="w-full pl-8 pr-4 py-2.5 bg-slate-50 dark:bg-neutral-800/60 border @error('username') border-rose-300 @else border-slate-200 dark:border-neutral-700 @enderror rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition">
                                    </div>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Username digunakan untuk masuk ke akun Anda.</p>
                                </div>

                                <!-- No Telp / WhatsApp -->
                                <div>
                                    <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider block mb-1.5">
                                        No. Handphone / WhatsApp
                                    </label>
                                    <input type="text" name="no_telp" value="{{ old('no_telp', $siswa?->no_telp) }}" placeholder="Contoh: 081234567890"
                                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-neutral-800/60 border border-slate-200 dark:border-neutral-700 rounded-2xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition">
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Nomor aktif untuk koordinasi kegiatan.</p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider block mb-1.5">
                                    Alamat Email
                                </label>
                                <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" placeholder="contoh@email.com"
                                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-neutral-800/60 border @error('email') border-rose-300 @else border-slate-200 dark:border-neutral-700 @enderror rounded-2xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition">
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Digunakan untuk menerima notifikasi akun.</p>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                                    Simpan Perubahan Akun
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- CARD 2: BIODATA RESMI SISWA (DIKELOLA SEKOLAH) -->
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up">
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-neutral-800 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Biodata Resmi Siswa</h2>
                                <p class="text-xs text-slate-400 dark:text-slate-400 mt-0.5">Identitas resmi yang terdaftar di buku induk sekolah</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-neutral-700">
                                <i class="fa-solid fa-lock text-[10px] text-slate-400"></i>
                                Dikelola Kesiswaan
                            </span>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                <div class="p-3 bg-slate-50 dark:bg-neutral-800/60 rounded-2xl border border-slate-100 dark:border-neutral-700/80">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</p>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $siswa?->nama ?? '-' }}</p>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-neutral-800/60 rounded-2xl border border-slate-100 dark:border-neutral-700/80">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIS</p>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $siswa?->nis ?? '-' }}</p>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-neutral-800/60 rounded-2xl border border-slate-100 dark:border-neutral-700/80">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</p>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $siswa?->kelas?->nama ?? '-' }}</p>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-neutral-800/60 rounded-2xl border border-slate-100 dark:border-neutral-700/80">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Angkatan</p>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $siswa?->angkatan ?? '-' }}</p>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-neutral-800/60 rounded-2xl border border-slate-100 dark:border-neutral-700/80">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jenis Kelamin</p>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ ucfirst($siswa?->jenis_kelamin ?? '-') }}</p>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-neutral-800/60 rounded-2xl border border-slate-100 dark:border-neutral-700/80">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Agama</p>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $siswa?->agama ?? '-' }}</p>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-neutral-800/60 rounded-2xl border border-slate-100 dark:border-neutral-700/80">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tempat, Tanggal Lahir</p>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1">
                                        {{ $siswa?->tempat_lahir ?? '-' }}, {{ $siswa?->tanggal_lahir ? $siswa->tanggal_lahir->isoFormat('D MMMM Y') : '-' }}
                                    </p>
                                </div>
                                <div class="p-3 bg-slate-50 dark:bg-neutral-800/60 rounded-2xl border border-slate-100 dark:border-neutral-700/80">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Media Sosial</p>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1">{{ $siswa?->medsos ?? '-' }}</p>
                                </div>
                                <div class="md:col-span-2 p-3 bg-slate-50 dark:bg-neutral-800/60 rounded-2xl border border-slate-100 dark:border-neutral-700/80">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat Domisili</p>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white mt-1 leading-relaxed">{{ $siswa?->alamat ?? 'Belum ada data alamat' }}</p>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-slate-50 dark:bg-neutral-800/40 rounded-2xl border border-slate-200/70 dark:border-neutral-700 text-xs text-slate-500 dark:text-slate-400 flex items-start gap-2.5">
                                <i class="fa-solid fa-circle-info text-sky-500 text-sm mt-0.5 shrink-0"></i>
                                <span class="text-[11px] leading-relaxed">Apabila terdapat kesalahan pada data identitas di atas (nama, kelas, NIS, atau data kelahiran), silakan hubungi staf Kesiswaan atau wali kelas Anda untuk pembaruan data resmi.</span>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 3: UBAH PASSWORD -->
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up">
                        <div class="px-6 py-5 border-b border-slate-100 dark:border-neutral-800 flex items-center justify-between">
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Ubah Password</h2>
                                <p class="text-xs text-slate-400 dark:text-slate-400 mt-0.5">Pastikan akun kamu menggunakan kata sandi yang kuat dan aman</p>
                            </div>
                            <div class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-neutral-800 text-slate-600 dark:text-slate-400 flex items-center justify-center border border-slate-200 dark:border-neutral-700">
                                <i class="fa-solid fa-key text-xs"></i>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('password.update') }}" class="p-6 space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="update_password_current_password" class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider block mb-1.5">
                                    Password Saat Ini <span class="text-rose-500">*</span>
                                </label>
                                <input type="password" id="update_password_current_password" name="current_password" required autocomplete="current-password" placeholder="Masukkan password saat ini"
                                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-neutral-800/60 border {{ $errors->updatePassword->has('current_password') ? 'border-rose-300' : 'border-slate-200 dark:border-neutral-700' }} rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition">
                                @if($errors->updatePassword->has('current_password'))
                                    <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $errors->updatePassword->first('current_password') }}</p>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="update_password_password" class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider block mb-1.5">
                                        Password Baru <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="password" id="update_password_password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter"
                                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-neutral-800/60 border {{ $errors->updatePassword->has('password') ? 'border-rose-300' : 'border-slate-200 dark:border-neutral-700' }} rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition">
                                    @if($errors->updatePassword->has('password'))
                                        <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $errors->updatePassword->first('password') }}</p>
                                    @endif
                                </div>

                                <div>
                                    <label for="update_password_password_confirmation" class="text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider block mb-1.5">
                                        Konfirmasi Password Baru <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="password" id="update_password_password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru"
                                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-neutral-800/60 border {{ $errors->updatePassword->has('password_confirmation') ? 'border-rose-300' : 'border-slate-200 dark:border-neutral-700' }} rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition">
                                    @if($errors->updatePassword->has('password_confirmation'))
                                        <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                                    Perbarui Password
                                </button>
                            </div>
                        </form>
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
