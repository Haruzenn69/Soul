<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Ekskul - SOUL</title>
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
        @keyframes blob { 0%, 100% { transform: translate(0, 0) scale(1); } 33% { transform: translate(24px, -18px) scale(1.08); } 66% { transform: translate(-16px, 12px) scale(.94); } }
        .animate-fade-up { animation: fadeUp .6s cubic-bezier(.22,1,.36,1) both; }
        .animate-blob { animation: blob 10s ease-in-out infinite; }
    </style>
    @include('partials.responsive-tables')
</head>
<body class="bg-gradient-to-br from-sky-50 via-white to-amber-50 text-slate-800 font-sans antialiased min-h-screen md:flex overflow-x-hidden">@include('partials.pill-sidebar', [
    'psTitle' => 'Menu Siswa',
    'psLogoBrand' => 'SOUL',
    'psDashboardUrl' => route('siswa.dashboard'),
    'psNotifUrl' => route('siswa.notifikasi'),
    'psProfileUrl' => route('profile.edit'),
    'psItems' => [
        ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => route('siswa.dashboard'), 'is' => 'siswa.dashboard'],
        ['icon' => 'calendar', 'label' => 'Presensi & Kegiatan', 'url' => route('siswa.presensi'), 'is' => 'siswa.presensi'],
        ['icon' => 'bars', 'label' => 'Rekap Absensi', 'url' => route('siswa.rekap'), 'is' => 'siswa.rekap'],
        ['icon' => 'user', 'label' => 'Profil Saya', 'url' => route('profile.edit'), 'is' => 'profile.edit'],
        ['icon' => 'document', 'label' => 'Katalog Ekskul', 'url' => route('siswa.katalog'), 'is' => ['siswa.katalog', 'ekskul.detail']],
    ],
    'psMore' => [
        ['label' => 'Daftar Ekskul', 'url' => route('siswa.daftar-ekskul'), 'is' => 'siswa.daftar-ekskul'],
        ['label' => 'Ajukan Keluar', 'url' => route('siswa.pengajuan-keluar'), 'is' => ['siswa.pengajuan-keluar', 'siswa.pengajuan-keluar.store']],
    ],
])

    <!-- OVERLAY -->
    <div id="sidebar-overlay" class="hidden fixed inset-0 z-[998] bg-black/50 md:hidden" onclick="closeSidebar()"></div>

    <!-- MOBILE SIDEBAR -->
    <div id="sidebar-mobile" class="hidden fixed inset-y-0 left-0 z-[999] w-64 bg-white border-r border-sky-100 overflow-hidden md:hidden shadow-2xl">
        <div class="absolute inset-0 pointer-events-none z-0">
            <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-sky-100/80 blur-3xl animate-blob pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-16 w-64 h-64 rounded-full bg-amber-100/80 blur-3xl animate-blob pointer-events-none" style="animation-delay: 3s"></div>
        </div>

        <div class="relative z-10 h-full flex flex-col p-5">
            <div class="relative z-20 flex items-center justify-between mb-8 px-2 mt-1 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-sky-300">SOUL</div>
                    <div>
                        <h1 class="font-extrabold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                        <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                    </div>
                </div>
                <button type="button" onclick="closeSidebar()" class="relative z-20 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3 shrink-0">Menu Utama</div>
            <nav class="space-y-1.5">
                <a href="{{ route('siswa.dashboard') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </span>
                    Dashboard
                </a>
                <a href="{{ route('siswa.katalog') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </span>
                    Katalog Ekskul
                </a>
                <a href="{{ route('siswa.presensi') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    Presensi & Kegiatan
                </a>
                <a href="{{ route('siswa.rekap') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    Rekap Absensi
                </a>
                <a href="{{ route('profile.edit') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    Profile
                </a>
            </nav>

            <div class="mt-auto pt-6 shrink-0">
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
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button onclick="openSidebar()" class="w-9 h-9 rounded-full bg-white border border-sky-100 flex items-center justify-center text-slate-500 md:hidden shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex items-center gap-2 md:hidden shrink-0">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-sky-300">S</div>
                    <span class="font-extrabold text-sm tracking-tight text-slate-900">SOUL</span>
                </div>

                <form method="GET" action="{{ route('siswa.daftar-ekskul') }}" class="relative w-full max-w-md hidden sm:block">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center w-4 h-4 pointer-events-none opacity-60">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari ekskul..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                </form>
            </div>

            <div class="flex items-center gap-2 md:gap-3 shrink-0">
                <div class="bg-sky-50 text-sky-700 border border-sky-100 px-3 py-1 rounded-lg text-xs font-semibold hidden sm:block">
                    Siswa
                </div>
                <a href="{{ route('siswa.notifikasi') }}" class="w-9 h-9 rounded-xl bg-white border border-sky-100 flex items-center justify-center text-xs relative text-slate-600 hover:bg-sky-50 hover:border-sky-200 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-400 px-4 py-2 rounded-lg text-xs font-semibold border border-red-100 transition shadow-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="p-4 md:p-8 space-y-5 md:space-y-6">

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-up">
                <div>
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Daftar Ekskul</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Pilih ekskul yang ingin kamu ikuti</p>
                </div>
                <a href="{{ route('siswa.katalog') }}" class="px-4 py-2 bg-white hover:bg-sky-50 text-slate-600 text-xs font-semibold rounded-lg border border-sky-100 shadow-sm transition flex items-center gap-2 w-full md:w-auto justify-center md:justify-start">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Katalog
                </a>
            </div>

            <!-- Informasi -->
            <div class="bg-sky-50 p-4 rounded-2xl border border-sky-200 flex items-start gap-3 animate-fade-up" style="animation-delay: .1s">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-sky-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="pt-1">
                    <p class="text-xs text-sky-800">Kamu hanya bisa mendaftar ke <span class="font-semibold">1 ekskul</span> dalam satu periode. Pilih dengan baik sesuai minat dan bakatmu.</p>
                </div>
            </div>

            <!-- Daftar Ekskul -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
                @forelse($ekskuls as $ekskul)
                <div class="bg-white p-4 md:p-5 rounded-2xl border border-sky-100 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-sky-200 transition-all duration-300">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white font-bold flex items-center justify-center text-sm shadow-lg shadow-sky-200 uppercase shrink-0">
                            {{ substr($ekskul->nama_ekskul, 0, 2) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm font-bold text-slate-900 truncate">{{ $ekskul->nama_ekskul }}</h3>
                            <p class="text-[10px] text-slate-400 truncate">Pembina: {{ $ekskul->pembina->nama ?? '-' }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 mb-3 line-clamp-2">{{ $ekskul->deskripsi ?? 'Deskripsi belum tersedia' }}</p>

                    @if($ekskul->is_open_recruitment)
                        <div class="flex items-center justify-between gap-2 mb-3 flex-wrap">
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-full">Buka Pendaftaran</span>
                            <span class="text-[10px] text-slate-400">{{ $ekskul->jadwal ?? 'Jadwal belum diatur' }}</span>
                        </div>
                        <a href="{{ route('siswa.form-daftar', $ekskul->id) }}" class="block w-full py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-semibold rounded-lg transition shadow-md shadow-sky-200 text-center hover:-translate-y-0.5">
                            Daftar Sekarang
                        </a>
                    @else
                        <div class="flex items-center justify-between gap-2 mb-3 flex-wrap">
                            <span class="text-[10px] font-semibold text-red-500 bg-red-50 border border-red-100 px-2 py-1 rounded-full">Tidak Membuka Pendaftaran</span>
                            <span class="text-[10px] text-slate-400">{{ $ekskul->jadwal ?? 'Jadwal belum diatur' }}</span>
                        </div>
                        <button class="w-full py-2.5 bg-slate-100 text-slate-400 text-xs font-semibold rounded-lg cursor-not-allowed border border-slate-100" disabled>
                            Tidak Tersedia
                        </button>
                    @endif
                </div>
                @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-12 text-slate-400">
                    <p class="text-sm">Belum ada ekskul yang tersedia untuk didaftar.</p>
                </div>
                @endforelse
            </div>

            <!-- Catatan -->
            <div class="bg-amber-50 p-4 rounded-2xl border border-amber-200 animate-fade-up">
                <p class="text-[10px] text-amber-700 text-center">
                    Setelah mendaftar, status pendaftaranmu akan menunggu verifikasi dari ketua ekskul.
                </p>
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
    </script>

</body>
</html>