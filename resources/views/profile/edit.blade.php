<!DOCTYPE html>
@php
    $psAuthUser = auth()->user();
    $psIsKetua = $psAuthUser && $psAuthUser->siswa && $psAuthUser->siswa->jabatan === 'ketua';
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
        'more' => [],
    ] : [
        'title' => 'Menu Siswa',
        'dash' => route('siswa.dashboard'),
        'notif' => route('siswa.notifikasi'),
        'items' => [
            ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => route('siswa.dashboard'), 'is' => 'siswa.dashboard'],
            ['icon' => 'calendar', 'label' => 'Presensi & Kegiatan', 'url' => route('siswa.presensi'), 'is' => 'siswa.presensi'],
            ['icon' => 'bars', 'label' => 'Rekap Absensi', 'url' => route('siswa.rekap'), 'is' => 'siswa.rekap'],
            ['icon' => 'user', 'label' => 'Profil Saya', 'url' => route('profile.edit'), 'is' => 'profile.edit'],
            ['icon' => 'document', 'label' => 'Katalog Ekskul', 'url' => route('siswa.katalog'), 'is' => ['siswa.katalog', 'ekskul.detail']],
        ],
        'more' => [
            ['label' => 'Daftar Ekskul', 'url' => route('siswa.daftar-ekskul'), 'is' => 'siswa.daftar-ekskul'],
            ['label' => 'Ajukan Keluar', 'url' => route('siswa.pengajuan-keluar'), 'is' => ['siswa.pengajuan-keluar', 'siswa.pengajuan-keluar.store']],
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
    </style>
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-amber-50 text-slate-800 font-sans antialiased min-h-screen md:flex selection:bg-sky-100 selection:text-sky-700 overflow-x-hidden">@include('partials.pill-sidebar', [
    'psTitle' => $psSidebar['title'],
    'psLogoBrand' => 'SOUL',
    'psDashboardUrl' => $psSidebar['dash'],
    'psNotifUrl' => $psSidebar['notif'],
    'psProfileUrl' => route('profile.edit'),
    'psItems' => $psSidebar['items'],
    'psMore' => $psSidebar['more'],
])

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

<div class="flex items-center justify-between mb-2 px-3">
                <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Menu utama</div>
                <span class="text-[9px] font-semibold text-slate-300">{{ $psIsKetua ? 'PANEL KETUA' : 'PANEL SISWA' }}</span>
            </div>
            <nav aria-label="Menu utama mobile" class="space-y-1.5">
                @foreach($psSidebar['items'] as $psMItem)
                    @php $psMActive = request()->routeIs((array) $psMItem['is']); @endphp
                    <a href="{{ $psMItem['url'] }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ $psMActive ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
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
                @endforeach
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
    <div class="w-full flex-1 flex flex-col min-w-0 ps-page">

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
                @if(! $psIsKetua)
                <form method="GET" action="{{ route('siswa.katalog') }}" class="relative w-full max-w-md hidden sm:block">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                </form>
                @endif
            </div>

            <!-- KANAN: Info User + Notifikasi + Logout -->
            <div class="flex items-center gap-2 md:gap-3 shrink-0">
                <div class="bg-sky-50 text-sky-700 border border-sky-100 px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2 hidden sm:block">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> {{ $psIsKetua ? 'Ketua' : 'Siswa' }}
                </div>
                <a href="{{ $psSidebar['notif'] }}" class="w-9 h-9 rounded-xl bg-white border border-sky-100 flex items-center justify-center text-xs relative text-slate-600 hover:bg-sky-50 hover:border-sky-200 transition-all shadow-sm">
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
                                            &#10004;&#65039; Kamu sudah mengajukan permohonan keluar. Mohon tunggu verifikasi dan persetujuan dari ketua ekskul.
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