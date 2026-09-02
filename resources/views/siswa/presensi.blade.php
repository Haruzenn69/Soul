<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi & Kegiatan - SOUL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: { blue: '#2563EB', darkBlue: '#1D4ED8', yellow: '#FACC15', dark: '#0F172A', lightBg: '#F8FAFC' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#F8FAFC] text-slate-800 font-sans antialiased flex min-h-screen">

    <!-- SIDEBAR LEFT -->
    <aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between p-5 hidden md:flex shrink-0">
        <div>
            <div class="flex items-center gap-3 mb-8 px-2">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-500/20">
                    SOUL
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                </div>
            </div>

            <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3">Menu Utama</div>
            <nav class="space-y-1">
                <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
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

                <a href="{{ route('siswa.katalog') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span> 
                    Katalog Ekskul
                </a>

                <a href="{{ route('siswa.presensi') }}" class="flex items-center gap-3 px-3.5 py-2.5 bg-blue-50 text-blue-600 rounded-xl font-semibold text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </span> 
                    Presensi & Kegiatan
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span> 
                    Profile
                </a>
            </nav>
        </div>

        <div class="bg-slate-50 p-3 rounded-xl flex items-center justify-between border border-slate-200/60">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-amber-400 text-slate-900 font-bold flex items-center justify-center text-xs">
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
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <header class="px-8 py-4 bg-white border-b border-slate-200/80 flex items-center justify-between gap-4">
            <div class="relative w-full max-w-md">
                <input type="text" placeholder="Cari presensi..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-slate-100/80 text-slate-700 border border-slate-200/60 px-3 py-1 rounded-lg text-xs font-semibold">
                    Siswa
                </div>
                <a href="{{ route('siswa.notifikasi') }}" class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-xs relative text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-xs font-semibold border border-red-100 transition shadow-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="p-8 space-y-6 overflow-y-auto">
            
            <div>
                <h1 class="text-xl font-bold text-slate-900">Riwayat Presensi</h1>
                <p class="text-xs text-slate-400 mt-0.5">Catatan kehadiranmu di setiap kegiatan ekskul</p>
            </div>

            <!-- Filter Bulan -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-sm flex flex-wrap items-center gap-3">
                <span class="text-xs font-semibold text-slate-600">Filter Bulan</span>
                <select class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition">
                    <option value="">Semua Bulan</option>
                    <option value="2026-08">Agustus 2026</option>
                    <option value="2026-07">Juli 2026</option>
                    <option value="2026-06">Juni 2026</option>
                    <option value="2026-05">Mei 2026</option>
                </select>
                <button class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">Terapkan</button>
                <span class="ml-auto text-[10px] text-slate-400">Menampilkan {{ count($presensis) }} data</span>
            </div>

            <!-- Statistik Presensi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Hadir</p>
                            <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $presensis->where('status', 'hadir')->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-lg">H</div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Kegiatan</p>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Izin</p>
                            <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $presensis->where('status', 'izin')->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-lg">I</div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Kegiatan</p>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Sakit</p>
                            <h3 class="text-2xl font-bold text-red-600 mt-1">{{ $presensis->where('status', 'sakit')->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600 text-lg">S</div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Kegiatan</p>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Alpha</p>
                            <h3 class="text-2xl font-bold text-slate-600 mt-1">{{ $presensis->where('status', 'alpha')->count() }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 text-lg">A</div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Kegiatan</p>
                </div>
            </div>

            <!-- Daftar Presensi -->
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/70 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Riwayat Kegiatan</h2>
                    <span class="text-[10px] text-slate-400">Total {{ count($presensis) }} kegiatan</span>
                </div>

                @if(count($presensis) > 0)
                    <div class="divide-y divide-slate-100">
                        @foreach($presensis->sortByDesc('kegiatan.tanggal_kegiatan') as $presensi)
                            @php
                                $statusColors = [
                                    'hadir' => 'bg-emerald-100 text-emerald-700',
                                    'izin' => 'bg-amber-100 text-amber-700',
                                    'sakit' => 'bg-red-100 text-red-700',
                                    'alpha' => 'bg-slate-200 text-slate-600'
                                ];
                                $statusLabels = [
                                    'hadir' => 'Hadir',
                                    'izin' => 'Izin',
                                    'sakit' => 'Sakit',
                                    'alpha' => 'Alpha'
                                ];
                            @endphp
                            <div class="px-6 py-4 hover:bg-slate-50 transition">
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
                                                <span class="text-blue-600">| {{ $presensi->kegiatan->ekskul->nama_ekskul ?? '' }}</span>
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
                        <p class="text-sm font-medium">Belum ada riwayat presensi</p>
                        <p class="text-xs mt-1">Kamu belum memiliki catatan kehadiran di kegiatan ekskul</p>
                    </div>
                @endif
            </div>

        </main>
    </div>

</body>
</html>