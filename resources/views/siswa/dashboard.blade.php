<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SOUL</title>
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
    @include('partials.responsive-tables')
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-amber-50 text-slate-800 font-sans antialiased flex min-h-screen selection:bg-sky-100 selection:text-sky-700 overflow-x-hidden">

    <!-- SIDEBAR LEFT -->
    <aside class="w-64 hidden md:flex flex-col justify-between shrink-0 bg-white/90 backdrop-blur border-r border-sky-100 shadow-sm relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-sky-100/70 blur-3xl animate-blob"></div>
            <div class="absolute bottom-0 -left-20 w-56 h-56 rounded-full bg-amber-100/70 blur-3xl animate-blob" style="animation-delay: 3s"></div>
        </div>

        <div class="relative p-5 flex flex-col h-full">
            <!-- Logo SOUL -->
            <div class="flex items-center gap-3 mb-8 px-2 mt-1">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-sky-300">
                    SOUL
                </div>
                <div>
                    <h1 class="font-extrabold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                </div>
            </div>

            <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3">Menu Utama</div>
            <nav class="space-y-1.5">
                <a href="{{ route('siswa.dashboard') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold text-xs shadow-sm shadow-sky-100 transition-all">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </span>
                    Dashboard
                </a>

                <a href="{{ route('siswa.katalog') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span>
                    Katalog Ekskul
                </a>

                <a href="{{ route('siswa.presensi') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    Presensi & Kegiatan
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    Profile
                </a>
            </nav>

            <!-- User Profile Card Bottom -->
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
    </aside>

    <!-- MOBILE SIDEBAR OVERLAY -->
    <div id="sidebar-overlay" class="hidden fixed inset-0 z-40 bg-black/50 md:hidden" onclick="closeSidebar()"></div>

    <!-- MOBILE SIDEBAR -->
    <div id="sidebar-mobile" class="hidden fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-sky-100 relative overflow-hidden flex flex-col justify-between p-5 md:hidden shadow-2xl">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-sky-100/80 blur-3xl animate-blob"></div>
            <div class="absolute -bottom-24 -left-16 w-64 h-64 rounded-full bg-amber-100/80 blur-3xl animate-blob" style="animation-delay: 3s"></div>
        </div>
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
                <a href="{{ route('siswa.dashboard') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold text-xs shadow-sm shadow-sky-100 transition-all">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
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
    <div class="flex-1 flex flex-col min-w-0">

        <!-- TOP NAVBAR HEADER -->
        <header class="px-4 md:px-8 py-4 bg-white/70 backdrop-blur-lg border-b border-sky-100 flex items-center justify-between gap-3 md:gap-4 sticky top-0 z-30">
            <div class="relative w-full max-w-md hidden sm:block">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" placeholder="Cari ekskul, siswa, kegiatan..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
            </div>

            <div class="flex items-center gap-2 md:gap-3 ml-auto">
                <div class="flex items-center gap-2 md:hidden">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-sky-300">S</div>
                    <span class="font-extrabold text-sm tracking-tight text-slate-900">SOUL</span>
                </div>
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
                <button onclick="openSidebar()" class="w-9 h-9 rounded-full bg-white border border-sky-100 flex items-center justify-center text-slate-500 md:hidden shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-400 px-4 py-2 rounded-lg text-xs font-semibold border border-red-100 transition shadow-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- DASHBOARD CONTENT -->
        <main class="p-4 md:p-8 space-y-6 overflow-y-auto">

            <!-- NOTIFIKASI STATUS PENDAFTARAN -->
            @php
                $pendaftaranStatus = null;
                $pendaftaranMessage = '';
                $pendaftaranColor = '';
                $pendaftaranIcon = '';

                if ($siswa) {
                    $pending = $siswa->pendaftarans()->where('status', 'pending')->first();
                    $diterima = $siswa->pendaftarans()->whereIn('status', ['diterima', 'peringatan'])->first();
                    $ditolak = $siswa->pendaftarans()->where('status', 'ditolak')->first();

                    if ($diterima) {
                        $pendaftaranStatus = 'diterima';
                        $pendaftaranMessage = 'Kamu sudah terdaftar di ekskul ' . $diterima->ekskul->nama_ekskul . '.';
                        $pendaftaranColor = 'from-emerald-500 to-teal-600';
                        $pendaftaranSoft = 'bg-emerald-50 border-emerald-200 text-emerald-800';
                        $pendaftaranIcon = '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    } elseif ($pending) {
                        $pendaftaranStatus = 'pending';
                        $pendaftaranMessage = 'Kamu sudah mengajukan pendaftaran ke ekskul ' . $pending->ekskul->nama_ekskul . '.';
                        $pendaftaranColor = 'from-amber-400 to-yellow-500';
                        $pendaftaranSoft = 'bg-amber-50 border-amber-200 text-amber-800';
                        $pendaftaranIcon = '<svg class="w-5 h-5 text-amber-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    } elseif ($ditolak) {
                        $pendaftaranStatus = 'ditolak';
                        $pendaftaranMessage = 'Pendaftaran kamu ke ekskul ' . $ditolak->ekskul->nama_ekskul . ' ditolak oleh ketua ekskul.';
                        $pendaftaranColor = 'from-rose-500 to-red-600';
                        $pendaftaranSoft = 'bg-rose-50 border-rose-200 text-rose-800';
                        $pendaftaranIcon = '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
                    }
                }
            @endphp

            @if($pendaftaranStatus)
                <div class="p-4 rounded-2xl border {{ $pendaftaranSoft }} flex items-start gap-3 bg-white/70 backdrop-blur shadow-sm animate-fade-up">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $pendaftaranColor }} flex items-center justify-center shrink-0 shadow-lg">
                        {!! $pendaftaranIcon !!}
                    </div>
                    <div class="flex-1 pt-0.5">
                        <p class="text-sm font-semibold">{{ $pendaftaranMessage }}</p>
                        @if($pendaftaranStatus == 'pending')
                            <p class="text-xs mt-1 opacity-70">Status pendaftaranmu sedang diproses oleh ketua ekskul.</p>
                        @elseif($pendaftaranStatus == 'ditolak')
                            <p class="text-xs mt-1 opacity-70">Kamu dapat mendaftar ke ekskul lain.</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- ALERT PERINGATAN / NONAKTIF -->
            @if(isset($isWarned) && $isWarned)
                <div class="p-4 rounded-2xl border bg-amber-50 border-amber-200 flex items-start gap-3">
                    <div class="mt-0.5 text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-amber-800">Kamu mendapatkan peringatan dari ketua ekskul</p>
                        <p class="text-xs mt-1 text-amber-700">Tingkatkan keaktifan dan kehadiranmu. Kamu tetap terhitung sebagai anggota aktif ekskul {{ $ekskul->nama_ekskul ?? '' }}.</p>
                    </div>
                    <a href="{{ route('siswa.notifikasi') }}" class="shrink-0 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-semibold rounded-lg transition">Lihat Notifikasi</a>
                </div>
            @elseif(isset($isNonaktif) && $isNonaktif)
                <div class="p-4 rounded-2xl border bg-red-50 border-red-200 flex items-start gap-3">
                    <div class="mt-0.5 text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-red-800">Kamu dinonaktifkan dari ekskul</p>
                        <p class="text-xs mt-1 text-red-700">Status keanggotaanmu saat ini nonaktif. Hubungi ketua ekskul jika ini kurang tepat.</p>
                    </div>
                    <a href="{{ route('siswa.notifikasi') }}" class="shrink-0 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-[11px] font-semibold rounded-lg transition">Lihat Notifikasi</a>
                </div>
            @endif

            <!-- Greeting & Header CTA -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Selamat datang, {{ $siswa->nama ?? auth()->user()->username }}
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }} · Semester Ganjil 2026/2027</p>
                </div>

                <div class="relative flex flex-col lg:flex-row lg:items-center gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-3 flex-wrap">
                            <span class="px-3 py-1 rounded-full bg-white/15 backdrop-blur border border-white/20 text-[10px] font-bold tracking-wide uppercase">Dashboard Siswa</span>
                            @if($ekskul)
                                <span class="px-3 py-1 rounded-full bg-amber-300/30 backdrop-blur border border-amber-200/40 text-[10px] font-bold tracking-wide uppercase flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                                    Aktif di {{ $ekskul->nama_ekskul }}
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight">
                            Halo, {{ $siswa->nama ?? auth()->user()->username }}!
                        </h1>
                        <p class="text-sm text-white/85 mt-1.5 font-medium">
                            {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }} · Semester Ganjil 2026/2027
                        </p>
                        <p class="text-xs text-white/70 mt-3 max-w-lg leading-relaxed">
                            Selamat datang kembali di SOUL. Yuk pantau ekskul, kehadiran, dan agenda kegiatanmu di bawah ini!
                        </p>
                    </div>

                    <div class="flex lg:flex-col gap-3">
                        @if(!$ekskul && !$pendaftaranStatus)
                            <a href="{{ route('siswa.daftar-ekskul') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Daftar Ekskul
                            </a>
                        @elseif($pendaftaranStatus == 'pending')
                            <a href="{{ route('siswa.notifikasi') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/15 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-2xl hover:bg-white/25 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                Cek Status
                            </a>
                        @else
                            <a href="{{ route('siswa.presensi') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Lihat Presensi
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="relative mt-6 pt-5 border-t border-white/15 grid grid-cols-3 gap-3">
                    <a href="{{ route('siswa.katalog') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold leading-none">Katalog</p>
                            <p class="text-[10px] text-white/70 mt-1 leading-none">Jelajahi Ekskul</p>
                        </div>
                    </a>
                    <a href="{{ route('siswa.presensi') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold leading-none">Presensi</p>
                            <p class="text-[10px] text-white/70 mt-1 leading-none">Riwayat Hadir</p>
                        </div>
                    </a>
                    <a href="{{ route('siswa.notifikasi') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                        <div class="w-8 h-8 rounded-lg bg-amber-300/40 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-bold leading-none">Notifikasi</p>
                            <p class="text-[10px] text-white/70 mt-1 leading-none">Info Terbaru</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="grid grid-cols-3 gap-3 md:gap-4">
                <!-- Card 1: Status Ekskul -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .1s">
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-sky-100 blur-2xl"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Status Ekskul</p>
                            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $ekskul ? '1' : '0' }}</h3>
                            <p class="text-[9px] md:text-[11px] font-semibold {{ $ekskul ? 'text-emerald-600' : 'text-amber-600' }} mt-0.5 md:mt-1">{{ $ekskul ? 'Terdaftar' : 'Belum' }}</p>
                        </div>
                        <div class="w-8 h-8 md:w-12 md:h-12 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center border border-white/40 shadow-lg shadow-sky-300">
                            <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Kehadiran -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 p-3 md:p-5 shadow-md shadow-amber-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .2s">
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-amber-100 blur-2xl"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Kehadiran</p>
                            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-amber-600">{{ $totalHadir ?? 0 }}</h3>
                            <p class="text-[9px] md:text-[11px] font-semibold text-amber-700 mt-0.5 md:mt-1">Pertemuan</p>
                        </div>
                        <div class="w-8 h-8 md:w-12 md:h-12 rounded-2xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center border border-white/40 shadow-lg shadow-amber-200">
                            <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Kegiatan Mendatang -->
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .3s">
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-sky-100 blur-2xl"></div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Kegiatan</p>
                            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $kegiatanMendatang->count() }}</h3>
                            <p class="text-[9px] md:text-[11px] font-semibold text-sky-600 mt-0.5 md:mt-1">Agenda</p>
                        </div>
                        <div class="w-8 h-8 md:w-12 md:h-12 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center border border-white/40 shadow-lg shadow-sky-300">
                            <svg class="w-4 h-4 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN GRID CONTENT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT COLUMN: Ekskul Saya -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .15s">
                        <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900">Ekskul Saya</h2>
                                <p class="text-[11px] text-slate-400 mt-0.5">Kelola keanggotaan ekstrakurikulermu</p>
                            </div>
                            <span class="text-[11px] font-semibold text-slate-400 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ $ekskul ? '1' : '0' }} / 1 Ekskul</span>
                        </div>

                        @if($ekskul)
                            <div class="p-6">
                                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 text-white shadow-lg shadow-sky-200">
                                    <div class="absolute inset-0 pointer-events-none">
                                        <div class="absolute -top-16 -right-10 w-56 h-56 rounded-full bg-amber-200/40 blur-3xl"></div>
                                        <div class="absolute bottom-0 left-1/4 w-40 h-40 rounded-full bg-white/20 blur-2xl animate-floaty"></div>
                                    </div>

                                    <div class="relative flex items-center justify-between gap-4 flex-wrap">
                                        <div class="flex items-center gap-4">
                                            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur border border-white/30 flex items-center justify-center font-extrabold text-xl uppercase shadow-inner">
                                                {{ substr($ekskul->nama_ekskul, 0, 2) }}
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-extrabold leading-tight">{{ $ekskul->nama_ekskul }}</h3>
                                                <p class="text-xs text-white/75 mt-1 line-clamp-2 max-w-sm">{{ $ekskul->deskripsi ?? 'Anggota aktif' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 bg-white/15 backdrop-blur border border-white/25 rounded-2xl px-4 py-2.5">
                                            <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <div>
                                                <p class="text-lg font-extrabold leading-none">{{ $totalHadir ?? 0 }}</p>
                                                <p class="text-[10px] text-amber-200 font-semibold">Kehadiran</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="relative mt-6 flex flex-col sm:flex-row gap-3">
                                        <a href="{{ route('siswa.presensi') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-sky-700 font-bold text-xs rounded-xl hover:bg-sky-50 hover:-translate-y-0.5 transition-all shadow-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Riwayat Presensi
                                        </a>
                                        <a href="{{ route('siswa.pengajuan-keluar') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-300/30 backdrop-blur border border-amber-200/40 text-white font-bold text-xs rounded-xl hover:bg-amber-300/50 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Ajukan Keluar
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-4 p-4 rounded-2xl border border-dashed border-sky-200 text-center bg-sky-50/50">
                                    <p class="text-[11px] text-slate-500 leading-relaxed">Kamu sudah terdaftar di 1 ekskul. Untuk mendaftar ekskul lain, ajukan keluar dari ekskul aktif terlebih dahulu.</p>
                                    <a href="{{ route('siswa.katalog') }}" class="inline-flex items-center gap-1.5 mt-2 text-sky-600 text-xs font-bold hover:text-sky-700 hover:underline">
                                        Lihat semua ekskul
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="p-10 text-center">
                                <div class="mx-auto w-20 h-20 rounded-3xl bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center text-white shadow-lg shadow-sky-300 animate-floaty">
                                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-extrabold text-slate-900 mt-5">Kamu belum bergabung di ekskul apa pun</h3>
                                <p class="text-xs text-slate-400 mt-1.5 max-w-sm mx-auto leading-relaxed">Temukan ekskul yang sesuai dengan minat dan bakatmu. Bergabunglah sekarang!</p>
                                <div class="flex flex-wrap justify-center gap-3 mt-5">
                                    <a href="{{ route('siswa.daftar-ekskul') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 hover:-translate-y-0.5 hover:shadow-xl transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Daftar Ekskul
                                    </a>
                                    <a href="{{ route('siswa.katalog') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-100 text-amber-800 font-bold text-xs rounded-xl hover:bg-amber-200 transition-all">
                                        Jelajahi Katalog
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT COLUMN: Kegiatan Mendatang -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .25s">
                        <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900">Kegiatan Mendatang</h2>
                                <p class="text-[11px] text-slate-400 mt-0.5">Agenda ekskul terdekatmu</p>
                            </div>
                            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center shadow-md shadow-amber-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </span>
                        </div>

                        <div class="p-5">
                            @forelse($kegiatanMendatang ?? [] as $index => $kegiatan)
                                <div class="flex gap-3.5 {{ !$loop->last ? 'mb-4' : '' }}">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 shrink-0 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex flex-col items-center justify-center shadow-md shadow-sky-200 {{ $index === 0 ? 'animate-floaty' : '' }}">
                                            <span class="text-sm font-extrabold leading-none">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('d') }}</span>
                                            <span class="text-[8px] font-bold uppercase leading-tight opacity-80">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('M') }}</span>
                                        </div>
                                        @if(!$loop->last)
                                            <div class="w-px flex-1 bg-gradient-to-b from-sky-200 to-slate-100 my-1"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 pb-1 pt-0.5">
                                        <h4 class="text-xs font-bold text-slate-800 leading-snug">{{ $kegiatan->materi ?? 'Kegiatan' }}</h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('dddd, DD MMM Y') }}
                                        </p>
                                        @if($index === 0)
                                            <span class="inline-flex mt-2 px-2.5 py-1 rounded-full bg-amber-100 border border-amber-200 text-amber-700 text-[10px] font-bold">Terdekat</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-300 border border-sky-100">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500 mt-3">Belum ada agenda mendatang</p>
                                    <p class="text-[11px] text-slate-400 mt-1">Agenda ekskul akan muncul di sini.</p>
                                </div>
                            @endforelse
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
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        const content = Swal.getHtmlContainer();
                        if (content) {
                            const timerElement = document.createElement('div');
                            timerElement.className = 'text-sm text-gray-500 mt-2';
                            timerElement.id = 'timer-text';
                            content.appendChild(timerElement);
                        }
                    },
                    willClose: () => {
                        window.location.href = '{{ route('siswa.dashboard') }}';
                    }
                });

                let timeLeft = 5;
                const timerInterval = setInterval(() => {
                    timeLeft--;
                    const timerText = document.getElementById('timer-text');
                    if (timerText) {
                        timerText.textContent = `Mengalihkan ke halaman dashboard dalam ${timeLeft} detik...`;
                    }
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                    }
                }, 1000);
            @endif

            @if(session('error'))
                Swal.fire({
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#2563EB'
                });
            @endif
        });
    </script>

    @include('partials.username-modal')

</body>
</html>