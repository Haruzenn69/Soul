<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi & Kegiatan - SOUL</title>
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
<body class="bg-gradient-to-br from-sky-50 via-white to-amber-50 text-slate-800 font-sans antialiased flex min-h-screen overflow-x-hidden">

    <!-- SIDEBAR LEFT -->
    <aside class="w-64 bg-white/90 backdrop-blur border-r border-sky-100 shadow-sm flex flex-col justify-between p-5 hidden md:flex shrink-0 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-sky-100/70 blur-3xl animate-blob"></div>
            <div class="absolute bottom-0 -left-20 w-56 h-56 rounded-full bg-amber-100/70 blur-3xl animate-blob" style="animation-delay: 3s"></div>
        </div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-8 px-2">
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
                <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
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

                <a href="{{ route('siswa.presensi') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold text-xs shadow-sm shadow-sky-100 transition-all">
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
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
        </div>

        <div class="relative mt-auto pt-6">
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
    </aside>

    @include('siswa.partials.mobile-sidebar')

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0">

        <header class="px-4 md:px-8 py-4 bg-white/70 backdrop-blur-lg border-b border-sky-100 flex items-center justify-between gap-3 md:gap-4 sticky top-0 z-30">
            <div class="relative w-full max-w-md hidden sm:block">
                <input type="text" placeholder="Cari presensi..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
            </div>

            <div class="flex items-center gap-2 md:gap-3 ml-auto">
                <div class="flex items-center gap-2 md:hidden">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-sky-300">S</div>
                    <span class="font-extrabold text-sm tracking-tight text-slate-900">SOUL</span>
                </div>
                <div class="bg-sky-50 text-sky-700 border border-sky-100 px-3 py-1 rounded-lg text-xs font-semibold hidden sm:block">
                    Siswa
                </div>
                <a href="{{ route('siswa.notifikasi') }}" class="w-9 h-9 rounded-xl bg-white border border-sky-100 flex items-center justify-center text-xs relative text-slate-600 hover:bg-sky-50 hover:border-sky-200 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
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

        <main class="p-4 md:p-8 space-y-6 overflow-y-auto">

            <div class="animate-fade-up">
                <h1 class="text-xl font-extrabold text-slate-900">Riwayat Presensi</h1>
                <p class="text-xs text-slate-400 mt-0.5">Catatan kehadiranmu di setiap kegiatan ekskul</p>
            </div>

            <!-- Filter Bulan -->
            <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-sm flex flex-wrap items-center gap-3 animate-fade-up" style="animation-delay: .1s">
                <span class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter Bulan
                </span>
                <select class="px-3 py-1.5 bg-sky-50/70 border border-sky-100 rounded-lg text-xs text-slate-700 focus:outline-none focus:border-sky-400 transition">
                    <option value="">Semua Bulan</option>
                    <option value="2026-08">Agustus 2026</option>
                    <option value="2026-07">Juli 2026</option>
                    <option value="2026-06">Juni 2026</option>
                    <option value="2026-05">Mei 2026</option>
                </select>
                <button class="px-4 py-1.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-semibold rounded-lg shadow-md shadow-sky-200 transition">Terapkan</button>
                <span class="ml-auto text-[10px] text-slate-400">Menampilkan {{ count($presensis) }} data</span>
            </div>

            <!-- Statistik Presensi -->
            <!-- Statistik Presensi - Responsif 2 → 4 -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <!-- Card 1: Hadir -->
                <div class="bg-white p-3 md:p-4 rounded-2xl border border-sky-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .15s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Hadir</p>
                            <h3 class="text-xl md:text-2xl font-extrabold text-emerald-600 mt-0.5 md:mt-1">{{ $presensis->where('status', 'hadir')->count() }}</h3>
                        </div>
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center text-sm md:text-base shadow-md shadow-emerald-200">H</div>
                    </div>
                    <p class="text-[8px] md:text-[10px] text-slate-400 mt-0.5 md:mt-1">Kegiatan</p>
                </div>

                <!-- Card 2: Izin -->
                <div class="bg-white p-3 md:p-4 rounded-2xl border border-sky-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .2s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Izin</p>
                            <h3 class="text-xl md:text-2xl font-extrabold text-amber-600 mt-0.5 md:mt-1">{{ $presensis->where('status', 'izin')->count() }}</h3>
                        </div>
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center text-sm md:text-base shadow-md shadow-amber-200">I</div>
                    </div>
                    <p class="text-[8px] md:text-[10px] text-slate-400 mt-0.5 md:mt-1">Kegiatan</p>
                </div>

                <!-- Card 3: Sakit -->
                <div class="bg-white p-3 md:p-4 rounded-2xl border border-sky-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .25s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Sakit</p>
                            <h3 class="text-xl md:text-2xl font-extrabold text-red-600 mt-0.5 md:mt-1">{{ $presensis->where('status', 'sakit')->count() }}</h3>
                        </div>
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-rose-400 to-red-500 text-white flex items-center justify-center text-sm md:text-base shadow-md shadow-rose-200">S</div>
                    </div>
                    <p class="text-[8px] md:text-[10px] text-slate-400 mt-0.5 md:mt-1">Kegiatan</p>
                </div>

                <!-- Card 4: Alpha -->
                <div class="bg-white p-3 md:p-4 rounded-2xl border border-sky-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .3s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Alpha</p>
                            <h3 class="text-xl md:text-2xl font-extrabold text-slate-600 mt-0.5 md:mt-1">{{ $presensis->where('status', 'alpha')->count() }}</h3>
                        </div>
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-sky-200 to-blue-300 text-blue-700 flex items-center justify-center text-sm md:text-base shadow-md shadow-sky-100">A</div>
                    </div>
                    <p class="text-[8px] md:text-[10px] text-slate-400 mt-0.5 md:mt-1">Kegiatan</p>
                </div>
            </div>

            <!-- Daftar Presensi -->
            <div class="bg-white rounded-2xl border border-sky-100 shadow-sm overflow-hidden animate-fade-up" style="animation-delay: .35s">
                <div class="px-6 py-4 border-b border-sky-50 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Riwayat Kegiatan</h2>
                    <span class="text-[10px] text-slate-400">Total {{ count($presensis) }} kegiatan</span>
                </div>

                @if(count($presensis) > 0)
                    <div class="divide-y divide-sky-50">
                        @foreach($presensis->sortByDesc('kegiatan.tanggal_kegiatan') as $presensi)
                            @php
                                $statusColors = [
                                    'hadir' => 'bg-emerald-100 text-emerald-700',
                                    'izin' => 'bg-amber-100 text-amber-700',
                                    'sakit' => 'bg-red-100 text-red-700',
                                    'alpha' => 'bg-sky-200 text-sky-700'
                                ];
                                $statusLabels = [
                                    'hadir' => 'Hadir',
                                    'izin' => 'Izin',
                                    'sakit' => 'Sakit',
                                    'alpha' => 'Alpha'
                                ];
                            @endphp
                            <div class="px-6 py-4 hover:bg-sky-50/50 transition">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h4 class="text-sm font-semibold text-slate-800">
                                                {{ $presensi->kegiatan->materi ?? 'Kegiatan' }}
                                            </h4>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $statusColors[$presensi->status] ?? 'bg-slate-100 text-slate-600' }}">
                                                {{ $statusLabels[$presensi->status] ?? $presensi->status }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3 mt-1 text-[11px] text-slate-500">
                                            <span>{{ \Carbon\Carbon::parse($presensi->kegiatan->tanggal_kegiatan)->isoFormat('dddd, DD MMM Y') }}</span>
                                            @if($presensi->kegiatan->ekskul)
                                                <span class="text-sky-600">| {{ $presensi->kegiatan->ekskul->nama_ekskul ?? '' }}</span>
                                            @endif
                                        </div>
                                        @if($presensi->keterangan)
                                            <p class="text-[11px] text-slate-500 mt-1 italic">{{ $presensi->keterangan }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right text-[10px] text-slate-400 whitespace-nowrap">
                                        @if($presensi->waktu_presensi)
                                            Dicatat {{ \Carbon\Carbon::parse($presensi->waktu_presensi)->isoFormat('DD MMM Y HH:mm') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 text-slate-400">
                        <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">Belum ada riwayat presensi</p>
                        <p class="text-xs mt-1">Kamu belum memiliki catatan kehadiran di kegiatan ekskul</p>
                    </div>
                @endif
            </div>

        </main>
    </div>

</body>
</html>