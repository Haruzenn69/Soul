<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Keluar - SOUL</title>
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
                        <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard Icon" class="w-full h-full object-contain">
                    </span> 
                    Dashboard
                </a>

                <a href="{{ route('siswa.katalog') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <img src="{{ asset('images/catalog.png') }}" alt="Katalog Icon" class="w-full h-full object-contain">
                    </span> 
                    Katalog Ekskul
                </a>

                <a href="{{ route('siswa.presensi') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <img src="{{ asset('images/presensi.png') }}" alt="Presensi Icon" class="w-full h-full object-contain">
                    </span> 
                    Presensi & Kegiatan
                </a>

                <a href="{{ route('siswa.pengajuan-keluar') }}" class="flex items-center gap-3 px-3.5 py-2.5 bg-blue-50 text-blue-600 rounded-xl font-semibold text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <img src="{{ asset('images/pengajuan-keluar.png') }}" alt="Pengajuan Keluar Icon" class="w-full h-full object-contain">
                    </span> 
                    Pengajuan Keluar
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <img src="{{ asset('images/profile.png') }}" alt="Profile Icon" class="w-full h-full object-contain">
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
                <h1 class="text-xl font-bold text-slate-900">Pengajuan Keluar</h1>
                <p class="text-xs text-slate-400 mt-0.5">Ajukan permohonan keluar dari ekskul yang sedang diikuti</p>
            </div>

            <!-- Form Pengajuan -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 mb-4">Form Pengajuan Keluar</h2>

                @if($ekskul)
                    <div class="mb-4 p-3 bg-blue-50 rounded-xl border border-blue-200">
                        <p class="text-xs text-blue-700">Kamu terdaftar di <span class="font-semibold">{{ $ekskul->nama_ekskul }}</span></p>
                    </div>

                    <form action="#" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-slate-700 block mb-1">Alasan Keluar</label>
                                <textarea class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500 transition" rows="4" placeholder="Tuliskan alasan kamu keluar dari ekskul..."></textarea>
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                Ajukan Permohonan
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <p class="text-sm">Kamu belum terdaftar di ekskul manapun.</p>
                        <a href="{{ route('siswa.katalog') }}" class="inline-block mt-3 text-blue-600 text-xs font-semibold hover:underline">Lihat Katalog Ekskul</a>
                    </div>
                @endif
            </div>

            <!-- Riwayat Pengajuan -->
            @if(count($pengajuan) > 0)
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 mb-4">Riwayat Pengajuan</h2>
                <div class="space-y-3">
                    @foreach($pengajuan as $item)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/60 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-600">{{ $item->alasan }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->isoFormat('DD MMM Y') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-semibold
                            {{ $item->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $item->status == 'diterima' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $item->status == 'ditolak' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </main>
    </div>

</body>
</html>