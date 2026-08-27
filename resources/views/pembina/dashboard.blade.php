<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pembina - SOUL</title>
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
<body class="bg-theme-lightBg text-theme-dark font-sans antialiased flex min-h-screen">

    <!-- SIDEBAR LEFT (PEMBINA) -->
    <aside class="w-64 bg-white border-r border-gray-100 flex flex-col justify-between p-6 hidden md:flex shrink-0">
        <div>
            <div class="flex items-center gap-3 mb-10">
                <div class="w-10 h-10 rounded-2xl bg-theme-dark text-theme-yellow flex items-center justify-center font-bold text-lg shadow-md">
                    
                </div>
                <div>
                    <h1 class="font-extrabold text-base tracking-wide leading-none">SOUL</h1>
                    <span class="text-[10px] text-gray-400 font-semibold tracking-wider uppercase">Panel Pembina</span>
                </div>
            </div>

            <div class="text-[11px] font-bold text-gray-400 tracking-wider uppercase mb-3 px-3">Menu Pembina</div>
            <nav class="space-y-1">
                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-theme-blue rounded-2xl font-bold text-xs transition">
                    <span class="text-base"></span> Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Input Presensi
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Foto Dokumentasi
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Verifikasi Laporan
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Cetak Rekap
                </a>
            </nav>
        </div>

        <div class="bg-gray-50 p-3.5 rounded-2xl flex items-center justify-between border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-theme-blue text-white font-extrabold flex items-center justify-center text-xs shadow-sm">
                    BN
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold leading-tight">Bu Nita, S.Kom</h4>
                    <p class="text-[10px] text-gray-400">Pembina Eskul</p>
                </div>
            </div>
            <a href="/login" class="text-gray-400 hover:text-red-500 text-xs font-bold">✕</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <header class="px-8 py-5 bg-white border-b border-gray-100 flex items-center justify-between gap-4">
            <div class="relative w-full max-w-md">
                <input type="text" placeholder="Cari data siswa, presensi, laporan..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs text-theme-dark placeholder-gray-400 focus:outline-none focus:bg-white focus:border-theme-blue transition">
                <span class="absolute left-3.5 top-2.5 text-gray-400 text-sm">🔍</span>
            </div>

            <!-- HEADER PROFILE & LOGOUT DROPDOWN -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <h4 class="text-xs font-bold text-theme-dark leading-tight">
                            {{ Auth::user()->username ?? Auth::user()->name }}
                        </h4>
                        <p class="text-[10px] text-gray-400 uppercase font-semibold">
                            {{ Auth::user()->role }}
                        </p>
                    </div>

                    <!-- Form Logout (Method POST) -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-full text-xs font-bold flex items-center gap-2 border border-red-100 transition shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-8 space-y-8 overflow-y-auto">
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-theme-dark">
                        Selamat datang, Bu Nita! 
                    </h1>
                    <p class="text-xs text-gray-400 mt-1">Kelola presensi, dokumentasi, dan verifikasi laporan bulanan eskul.</p>
                </div>
                <button class="px-6 py-3 bg-theme-yellow hover:bg-yellow-400 text-theme-dark font-bold text-xs rounded-full shadow-md transition flex items-center gap-2">
                    <span>+</span> Input Presensi Baru
                </button>
            </div>

            <!-- STATS PEMBINA -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Eskul Dibina</p>
                        <h3 class="text-3xl font-extrabold text-theme-dark mt-2">1 <span class="text-xs font-normal text-gray-400">Ekskul</span></h3>
                        <p class="text-[11px] font-medium text-theme-blue mt-2">Software Engineering</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-theme-blue flex items-center justify-center text-xl"></div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Total Anggota Aktif</p>
                        <h3 class="text-3xl font-extrabold text-theme-dark mt-2">32 <span class="text-xs font-normal text-gray-400">Siswa</span></h3>
                        <p class="text-[11px] font-medium text-emerald-500 mt-2">95% Kehadiran Rata-rata</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl"></div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Laporan Bulanan</p>
                        <h3 class="text-3xl font-extrabold text-theme-dark mt-2">1 <span class="text-xs font-normal text-amber-500">Perlu Verifikasi</span></h3>
                        <p class="text-[11px] font-medium text-gray-400 mt-2">Bulan Agustus 2026</p>
                    </div>
                </div>
            </div>

            <!-- CONTENT LISTING -->
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <h2 class="text-base font-extrabold text-theme-dark mb-4">Siswa Perlu Verifikasi Pendaftaran</h2>
                <div class="p-4 bg-gray-50 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-theme-blue text-white font-bold flex items-center justify-center text-xs">
                            A
                        </div>
                        <div>
                            <h4 class="text-xs font-bold">Ahmad Fauzi</h4>
                            <p class="text-[10px] text-gray-400">Kelas XI RPL 2 · Mengajukan pendaftaran baru</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-full transition">Setujui</button>
                        <button class="px-4 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold text-[11px] rounded-full transition">Tolak</button>
                    </div>
                </div>
            </div>

        </main>
    </div>

</body>
</html>