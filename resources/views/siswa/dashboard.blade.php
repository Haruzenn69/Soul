<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SOUL</title>
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
<body class="bg-[#F8FAFC] text-slate-800 font-sans antialiased flex min-h-screen selection:bg-blue-100 selection:text-blue-600">

    <!-- SIDEBAR LEFT -->
    <aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between p-5 hidden md:flex shrink-0">
        <div>
            <!-- Logo SOUL -->
            <div class="flex items-center gap-3 mb-8 px-2">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                    ✨
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                </div>
            </div>

            <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3">Menu Utama</div>
            <nav class="space-y-1">
                <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 bg-blue-50 text-blue-600 rounded-xl font-semibold text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard Icon" class="w-full h-full object-contain">
                    </span> 
                    Dashboard
                </a>

                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <img src="{{ asset('images/catalog.png') }}" alt="Katalog Icon" class="w-full h-full object-contain">
                    </span> 
                    Katalog Ekskul
                </a>

                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <img src="{{ asset('images/presensi.png') }}" alt="Presensi Icon" class="w-full h-full object-contain">
                    </span> 
                    Presensi & Kegiatan
                </a>

                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <img src="{{ asset('images/pengajuan-keluar.png') }}" alt="Pengajuan Keluar Icon" class="w-full h-full object-contain">
                    </span> 
                    Pengajuan Keluar
                </a>

                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <img src="{{ asset('images/laporan-bulanan.png') }}" alt="Laporan Icon" class="w-full h-full object-contain">
                    </span> 
                    Laporan Bulanan
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <img src="{{ asset('images/profile.png') }}" alt="Profile Icon" class="w-full h-full object-contain">
                    </span> 
                    Profile
                </a>
            </nav>
        </div>

        <!-- User Profile Card Bottom -->
        <div class="bg-slate-50 p-3 rounded-xl flex items-center justify-between border border-slate-200/60">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-amber-400 text-slate-900 font-bold flex items-center justify-center text-xs">
                    {{ strtoupper(substr($siswa->nama ?? auth()->user()->username ?? 'S', 0, 1)) }}
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ $siswa->nama ?? auth()->user()->username }}</h4>
                    <p class="text-[10px] text-slate-400 font-medium">{{ $siswa->kelas->nama_kelas ?? 'Siswa' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-rose-600 text-xs font-bold transition-colors p-1">✕</button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP NAVBAR HEADER -->
        <header class="px-8 py-4 bg-white border-b border-slate-200/80 flex items-center justify-between gap-4">
            <!-- Search Bar -->
            <div class="relative w-full max-w-md">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center w-4 h-4 pointer-events-none opacity-60">
                    <img src="{{ asset('images/search.png') }}" alt="Search" class="w-full h-full object-contain">
                </span>
                <input type="text" placeholder="Cari ekskul, siswa, kegiatan..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all">
            </div>

            <!-- Top Right Profile / Role Badge -->
            <div class="flex items-center gap-3">
                <div class="bg-slate-100/80 text-slate-700 border border-slate-200/60 px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2 cursor-pointer hover:bg-slate-100 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Siswa ▾
                </div>
                <button class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-xs relative text-slate-600 hover:bg-slate-100 transition-colors">
                    <div class="w-5 h-5 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/notification.png') }}" alt="Icon" class="w-full h-full object-contain">
                    </div>
                    <span class="w-2 h-2 rounded-full bg-amber-400 absolute top-2 right-2 border-2 border-white"></span>
                </button>
            </div>
        </header>

        <!-- DASHBOARD BODY CONTENT -->
        <main class="p-8 space-y-6 overflow-y-auto">
            
            <!-- Greeting & Header CTA -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        Selamat datang, {{ $siswa->nama ?? auth()->user()->username }}! 
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }} · Semester Ganjil 2026/2027</p>
                </div>
                @if(!$ekskul)
                    <a href="#" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-all active:scale-[0.98] flex items-center gap-2">
                        <span class="text-sm font-bold">+</span> Daftar Ekskul
                    </a>
                @endif
            </div>

            <!-- TOP STATS METRIC CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Stat 1: Status Ekskul -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm/50 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Status Ekskul</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $ekskul ? '1' : '0' }} <span class="text-xs font-normal text-slate-400">Ekskul (Max)</span></h3>
                        <p class="text-[11px] font-medium {{ $ekskul ? 'text-emerald-600' : 'text-amber-600' }} mt-1 flex items-center gap-1">
                            {{ $ekskul ? '✓ Terdaftar Aktif' : '• Belum Terdaftar' }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center overflow-hidden shrink-0">
                        <img src="{{ asset('images/status.png') }}" alt="Icon" class="w-5 h-5 object-contain">
                    </div>
                </div>

                <!-- Stat 2: Total Kehadiran -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm/50 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Kehadiran</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalHadir ?? 0 }} <span class="text-xs font-normal text-slate-400">Pertemuan</span></h3>
                        <p class="text-[11px] font-medium text-emerald-600 mt-1">Presensi Tercatat</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center overflow-hidden shrink-0">
                        <img src="{{ asset('images/statistik.png') }}" alt="Icon" class="w-5 h-5 object-contain">
                    </div>
                </div>

                <!-- Stat 3: Kegiatan Mendatang -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm/50 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Kegiatan Mendatang</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ count($kegiatanMendatang ?? []) }}</h3>
                        <p class="text-[11px] font-medium text-blue-600 mt-1">Agenda Ekskul</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center overflow-hidden shrink-0">
                        <img src="{{ asset('images/kegiatan.png') }}" alt="Icon" class="w-5 h-5 object-contain">
                    </div>
                </div>
            </div>

            <!-- MAIN GRID CONTENT (Eskul Saya vs Schedule) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- LEFT COLUMN: Ekskul Saya -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm/50">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="text-sm font-bold text-slate-900">Ekskul Saya</h2>
                            <span class="text-xs font-medium text-slate-400">{{ $ekskul ? '1' : '0' }} dari Maksimal 1 Ekskul</span>
                        </div>

                        @if($ekskul)
                            <!-- Card Ekskul Terdaftar -->
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/60 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-11 h-11 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center text-xs shadow-sm uppercase">
                                        {{ substr($ekskul->nama_ekskul, 0, 2) }}
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-bold text-slate-900">{{ $ekskul->nama_ekskul }}</h3>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $ekskul->deskripsi ?? 'Anggota Aktif' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold text-slate-900">{{ $totalHadir ?? 0 }} Presence</span>
                                </div>
                            </div>

                            <div class="mt-4 p-3.5 rounded-xl border border-dashed border-slate-200 text-center bg-slate-50/50">
                                <p class="text-[11px] text-slate-400 leading-relaxed">Kamu sudah terdaftar di 1 ekskul. Keluar dari ekskul aktif jika ingin mendaftar ke ekskul lain.</p>
                            </div>
                        @else
                            <!-- Tampilan Jika Belum Daftar Ekskul -->
                            <div class="p-8 text-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                                <p class="text-xs font-medium text-slate-500">Kamu belum mendaftar di ekstrakurikuler mana pun.</p>
                                <a href="#" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                    Jelajahi Katalog Ekskul
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT COLUMN: Kegiatan Mendatang -->
                <div class="space-y-6">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm/50">
                        <h2 class="text-sm font-bold text-slate-900 mb-3.5">Kegiatan Mendatang</h2>
                        <div class="space-y-3">
                            @forelse($kegiatanMendatang ?? [] as $kegiatan)
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/50">
                                    <h4 class="text-xs font-semibold text-slate-800">{{ $kegiatan->nama_kegiatan }}</h4>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($kegiatan->tanggal)->isoFormat('dddd, DD MMM Y - HH:mm') }} WIB</p>
                                    <p class="text-[10px] text-blue-600 font-medium mt-1">{{ $kegiatan->lokasi }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 text-center py-4">Belum ada agenda kegiatan mendatang.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

</body>
</html>