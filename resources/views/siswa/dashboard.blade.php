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
<body class="bg-theme-lightBg text-theme-dark font-sans antialiased flex min-h-screen">

    <!-- SIDEBAR LEFT -->
    <aside class="w-64 bg-white border-r border-gray-100 flex flex-col justify-between p-6 hidden md:flex shrink-0">
        <div>
            <!-- Logo SOUL -->
            <div class="flex items-center gap-3 mb-10">
                <div class="w-10 h-10 rounded-2xl bg-theme-blue text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-500/20">
                    
                </div>
                <div>
                    <h1 class="font-extrabold text-base tracking-wide leading-none">SOUL</h1>
                    <span class="text-[10px] text-gray-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                </div>
            </div>

            <!-- Navigation Menu -->
            <div class="text-[11px] font-bold text-gray-400 tracking-wider uppercase mb-3 px-3">Menu Utama</div>
            <nav class="space-y-1">
                <a href="#" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-theme-blue rounded-2xl font-bold text-xs transition">
                    <span class="text-base"></span> Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Katalog Ekskul
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Presensi & Kegiatan
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Pengajuan Keluar
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Laporan Bulanan
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Profile
                </a>
            </nav>
        </div>

        <!-- User Profile Card Bottom -->
        <div class="bg-gray-50 p-3.5 rounded-2xl flex items-center justify-between border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-theme-yellow text-theme-dark font-extrabold flex items-center justify-center text-xs shadow-sm">
                    N
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold leading-tight">Nazwa Nurhafiza</h4>
                    <p class="text-[10px] text-gray-400">XII RPL 1</p>
                </div>
            </div>
            <a href="/login" class="text-gray-400 hover:text-red-500 text-xs font-bold">✕</a>
        </div>
    </aside>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- TOP NAVBAR HEADER -->
        <header class="px-8 py-5 bg-white border-b border-gray-100 flex items-center justify-between gap-4">
            <!-- Search Bar -->
            <div class="relative w-full max-w-md">
                <input type="text" placeholder="Cari ekskul, siswa, kegiatan..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs text-theme-dark placeholder-gray-400 focus:outline-none focus:bg-white focus:border-theme-blue transition">
                <span class="absolute left-3.5 top-2.5 text-gray-400 text-sm"></span>
            </div>

            <!-- Top Right Profile / Role Badge -->
            <div class="flex items-center gap-3">
                <div class="bg-blue-50 text-theme-blue px-4 py-1.5 rounded-full text-xs font-bold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-theme-blue"></span> Siswa ▾
                </div>
                <button class="w-9 h-9 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-xs relative text-gray-500">
                    
                    <span class="w-2 h-2 rounded-full bg-theme-yellow absolute top-2 right-2 border-2 border-white"></span>
                </button>
            </div>
        </header>

        <!-- DASHBOARD BODY CONTENT -->
        <main class="p-8 space-y-8 overflow-y-auto">
            
            <!-- Greeting & Header CTA -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-theme-dark flex items-center gap-2">
                        Selamat datang, Nazwa! 
                    </h1>
                    <p class="text-xs text-gray-400 mt-1">Rabu, 19 Agustus 2026 · Semester Ganjil 2026/2027</p>
                </div>
                <!-- Siswa hanya bisa daftar 1 ekskul -->
                <button class="px-6 py-3 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full shadow-lg shadow-blue-500/20 transition flex items-center gap-2">
                    <span>+</span> Daftar Ekskul
                </button>
            </div>

            <!-- TOP STATS METRIC CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat 1 -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Status Ekskul</p>
                        <h3 class="text-3xl font-extrabold text-theme-dark mt-2">1 <span class="text-xs font-normal text-gray-400">Ekskul (Max)</span></h3>
                        <p class="text-[11px] font-medium text-emerald-500 mt-2">✓ Terdaftar Aktif</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-theme-blue flex items-center justify-center text-xl"></div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Tingkat Kehadiran</p>
                        <h3 class="text-3xl font-extrabold text-theme-dark mt-2">92%</h3>
                        <p class="text-[11px] font-medium text-emerald-500 mt-2">↗ +5% dari bulan lalu</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl"></div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Kegiatan Mendatang</p>
                        <h3 class="text-3xl font-extrabold text-theme-dark mt-2">2</h3>
                        <p class="text-[11px] font-medium text-theme-blue mt-2">Minggu ini</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-theme-yellow flex items-center justify-center text-xl"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-base font-extrabold text-theme-dark">Ekskul Saya</h2>
                            <span class="text-xs font-semibold text-gray-400">1 dari Maksimal 1 Ekskul</span>
                        </div>

                        <!-- Card Ekskul Tunggal Terdaftar -->
                        <div class="p-5 bg-gray-50/80 rounded-2xl border border-gray-100 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-theme-blue text-white font-extrabold flex items-center justify-center text-sm shadow-md shadow-blue-500/20">
                                    SE
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-theme-dark">Software Engineering Club</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">Anggota · Sesi berikutnya: Jumat</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-theme-dark">92%</span>
                                <p class="text-[10px] text-gray-400">Kehadiran</p>
                                <div class="w-20 bg-gray-200 h-1.5 rounded-full mt-1.5 overflow-hidden">
                                    <div class="bg-theme-blue h-full w-[92%] rounded-full"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Notice Mendaftar Ekskul Baru jika sudah ada 1 -->
                        <div class="mt-4 p-4 rounded-2xl border border-dashed border-gray-200 text-center bg-gray-50/50">
                            <p class="text-xs text-gray-400">Kamu sudah terdaftar di 1 ekskul. Keluar dari ekskul aktif jika ingin mendaftar ke ekskul lain.</p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Kegiatan Mendatang & Aktivitas Terbaru -->
                <div class="space-y-6">
                    <!-- Kegiatan Mendatang -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <h2 class="text-base font-extrabold text-theme-dark mb-4">Kegiatan Mendatang</h2>
                        <div class="space-y-4">
                            <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-100/80">
                                <h4 class="text-xs font-bold text-theme-dark">Workshop Laravel & Flutter</h4>
                                <p class="text-[11px] text-gray-400 mt-0.5">Jum, 15:30 WIB</p>
                                <p class="text-[11px] text-theme-blue font-medium mt-1">📍 Lab Komputer 2</p>
                            </div>
                            <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-100/80">
                                <h4 class="text-xs font-bold text-theme-dark">Evaluasi Proyek Akhir</h4>
                                <p class="text-[11px] text-gray-400 mt-0.5">Sab, 09:00 WIB</p>
                                <p class="text-[11px] text-theme-blue font-medium mt-1">📍 Ruang AULA</p>
                            </div>
                        </div>
                    </div>

                    <!-- Aktivitas Terbaru -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                        <h2 class="text-base font-extrabold text-theme-dark mb-4">Aktivitas Terbaru</h2>
                        <div class="space-y-3">
                            <div class="flex items-start gap-3 text-xs">
                                <div class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center font-bold text-[10px] shrink-0">✓</div>
                                <div>
                                    <p class="font-semibold text-theme-dark">Presensi SE Club berhasil dicatat</p>
                                    <span class="text-[10px] text-gray-400">2 jam lalu</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 text-xs">
                                <div class="w-6 h-6 rounded-full bg-blue-50 text-theme-blue flex items-center justify-center font-bold text-[10px] shrink-0">📢</div>
                                <div>
                                    <p class="font-semibold text-theme-dark">Jadwal latihan baru diumumkan</p>
                                    <span class="text-[10px] text-gray-400">1 hari lalu</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

</body>
</html>