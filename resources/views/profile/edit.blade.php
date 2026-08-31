<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Siswa - SOUL</title>
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

                <a href="{{ route('siswa.presensi') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </span> 
                    Presensi & Kegiatan
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 bg-blue-50 text-blue-600 rounded-xl font-semibold text-xs transition-colors">
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
                <input type="text" placeholder="Cari..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all">
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
                <h1 class="text-xl font-bold text-slate-900">Profile Saya</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kelola informasi akun dan data diri kamu</p>
            </div>

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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- KOLOM KIRI: Foto Profile & Informasi Singkat -->
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm text-center">
                        <!-- Foto Profile -->
                        <div class="relative inline-block">
                            <div class="w-32 h-32 rounded-full bg-blue-100 border-4 border-blue-600 flex items-center justify-center mx-auto overflow-hidden">
                                <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <button class="absolute bottom-2 right-2 bg-blue-600 hover:bg-blue-700 text-white p-1.5 rounded-full shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>

                        <h3 class="text-sm font-bold text-slate-900 mt-4">{{ $siswa->nama ?? auth()->user()->username }}</h3>
                        <p class="text-xs text-slate-400">{{ $siswa->kelas->nama ?? 'Siswa' }}</p>
                        
                        <div class="mt-4 pt-4 border-t border-slate-200/60">
                            <p class="text-[10px] text-slate-400">Bergabung sejak</p>
                            <p class="text-xs font-medium text-slate-700">{{ $siswa->created_at ? \Carbon\Carbon::parse($siswa->created_at)->isoFormat('D MMMM Y') : '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN: Data Diri & Pengajuan Keluar -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Data Diri -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                        <h2 class="text-sm font-bold text-slate-900 mb-4">Data Diri</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Nama Lengkap</p>
                                <p class="text-sm font-medium text-slate-800 mt-1">{{ $siswa->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">NIS</p>
                                <p class="text-sm font-medium text-slate-800 mt-1">{{ $siswa->nis ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Kelas</p>
                                <p class="text-sm font-medium text-slate-800 mt-1">{{ $siswa->kelas->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Jenis Kelamin</p>
                                <p class="text-sm font-medium text-slate-800 mt-1">{{ ucfirst($siswa->jenis_kelamin ?? '-') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Username</p>
                                <p class="text-sm font-medium text-slate-800 mt-1">{{ auth()->user()->username ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Email</p>
                                <p class="text-sm font-medium text-slate-800 mt-1">{{ auth()->user()->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Ekskul -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                        <h2 class="text-sm font-bold text-slate-900 mb-4">Informasi Ekskul</h2>
                        @if($ekskul)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Nama Ekskul</p>
                                    <p class="text-sm font-medium text-slate-800 mt-1">{{ $ekskul->nama_ekskul }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Jabatan</p>
                                    <p class="text-sm font-medium text-slate-800 mt-1">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $siswa->jabatan == 'ketua' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ ucfirst($siswa->jabatan ?? 'Anggota') }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Status</p>
                                    <p class="text-sm font-medium text-emerald-600 mt-1">Aktif</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Pembina</p>
                                    <p class="text-sm font-medium text-slate-800 mt-1">{{ $ekskul->pembina->nama ?? '-' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-6 text-slate-400">
                                <p class="text-sm">Kamu belum terdaftar di ekskul manapun.</p>
                                <a href="{{ route('siswa.katalog') }}" class="inline-block mt-2 text-blue-600 text-xs font-semibold hover:underline">Lihat Katalog Ekskul</a>
                            </div>
                        @endif
                    </div>

                    <!-- Pengajuan Keluar Ekskul -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-bold text-slate-900">Pengajuan Keluar Ekskul</h2>
                            <span class="text-[10px] font-semibold text-amber-600 bg-amber-50 px-3 py-1 rounded-full">Sakral</span>
                        </div>
                        
                        @if($ekskul)
                            <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 mb-4">
                                <p class="text-xs text-amber-700">
                                    Pengajuan keluar dari ekskul bersifat permanen. Setelah dikeluarkan, kamu harus mendaftar ulang jika ingin bergabung kembali.
                                </p>
                            </div>

                            @php
                                $hasPendingPengajuan = isset($pengajuan) && $pengajuan->where('status', 'pending')->count() > 0;
                            @endphp

                            @if($hasPendingPengajuan)
                                <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 mb-4">
                                    <p class="text-xs text-amber-800 font-medium">
                                        ⏳ Kamu sudah mengajukan permohonan keluar. Mohon tunggu verifikasi dan persetujuan dari ketua ekskul.
                                    </p>
                                </div>
                            @else
                                <form method="POST" action="{{ route('siswa.pengajuan-keluar.store') }}">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-xs font-semibold text-slate-700 block mb-1">
                                                Alasan Keluar <span class="text-red-500">*</span>
                                            </label>
                                            <textarea name="alasan" required rows="3" 
                                                class="w-full p-3 bg-slate-50 border @error('alasan') border-red-300 @else border-slate-200 @enderror rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500 transition" 
                                                placeholder="Tuliskan alasan kamu ingin keluar dari ekskul ini...">{{ old('alasan') }}</textarea>
                                            @error('alasan')
                                                <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                            Ajukan Permohonan Keluar
                                        </button>
                                    </div>
                                </form>
                            @endif

                            <!-- Riwayat Pengajuan Keluar -->
                            @if(isset($pengajuan) && count($pengajuan) > 0)
                                <div class="mt-6 pt-6 border-t border-slate-200/60">
                                    <p class="text-xs font-semibold text-slate-600 mb-3">Riwayat Pengajuan</p>
                                    <div class="space-y-2">
                                        @foreach($pengajuan as $item)
                                        <div class="p-3 bg-slate-50 rounded-xl flex items-center justify-between">
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
                        @else
                            <div class="text-center py-6 text-slate-400">
                                <p class="text-sm">Kamu belum terdaftar di ekskul manapun.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </main>
    </div>

</body>
</html>