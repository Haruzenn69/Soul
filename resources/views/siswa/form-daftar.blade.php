<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran Ekskul - SOUL</title>
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
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center w-4 h-4 pointer-events-none opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" placeholder="Cari..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
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

            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-up">
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">Form Pendaftaran Ekskul</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Isi data diri kamu untuk mendaftar ekskul</p>
                </div>
                <a href="{{ route('siswa.daftar-ekskul') }}" class="px-4 py-2 bg-white hover:bg-sky-50 text-slate-600 text-xs font-semibold rounded-lg border border-sky-100 shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>

            <!-- Form -->
            <div class="bg-white p-6 rounded-2xl border border-sky-100 shadow-sm max-w-2xl animate-fade-up" style="animation-delay: .1s">
                <form method="POST" action="{{ route('siswa.daftar-ekskul.store') }}">
                    @csrf
                    <input type="hidden" name="ekskul_id" value="{{ $ekskul->id }}">

                    <div class="space-y-4">
                        <!-- Ekskul yang Dipilih -->
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-1">Ekskul yang Dipilih</label>
                            <div class="w-full p-3 bg-gradient-to-r from-sky-50 to-blue-50 border border-sky-200 rounded-xl text-sm font-bold text-sky-700 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center text-[10px] font-extrabold uppercase shadow-md shadow-sky-200">
                                    {{ substr($ekskul->nama_ekskul, 0, 2) }}
                                </div>
                                {{ $ekskul->nama_ekskul }}
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Ekskul ini telah dipilih dari halaman sebelumnya</p>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ $siswa->nama }}" class="w-full p-3 bg-sky-50/60 border border-sky-100 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-400 transition" readonly>
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-1">Kelas</label>
                            <input type="text" name="kelas" value="{{ $siswa->kelas->nama ?? '-' }}" class="w-full p-3 bg-sky-50/60 border border-sky-100 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-400 transition" readonly>
                        </div>

                        <!-- NIS -->
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-1">NIS</label>
                            <input type="text" name="nis" value="{{ $siswa->nis }}" class="w-full p-3 bg-sky-50/60 border border-sky-100 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-400 transition" readonly>
                        </div>

                        <!-- Alasan Bergabung -->
                        <div>
                            <label class="text-xs font-semibold text-slate-700 block mb-1">Alasan Bergabung <span class="text-red-500">*</span> <span class="text-red-500 font-medium normal-case">(wajib diisi)</span></label>
                            <textarea name="alasan" rows="4" required
                                class="w-full p-3 bg-sky-50/60 border @error('alasan') border-red-300 @else border-sky-100 @enderror rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition placeholder:text-slate-400"
                                placeholder="Tuliskan alasan kamu ingin bergabung dengan ekskul ini secara jelas dan masuk akal...">{{ old('alasan') }}</textarea>
                            <p class="text-[10px] text-slate-400 mt-1">Wajib diisi. Tulis alasan yang jelas (minimal 10 karakter / 2 kata) — alasan seperti "asd", "gatau", atau "malas" tidak diterima.</p>
                            @error('alasan')
                                <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tombol Submit -->
                        <button type="submit" class="w-full py-3 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-sky-200 hover:-translate-y-0.5">
                            Kirim Pendaftaran
                        </button>
                    </div>
                </form>
            </div>

            <!-- Catatan -->
            <div class="bg-amber-50 p-4 rounded-2xl border border-amber-200 max-w-2xl space-y-1.5 animate-fade-up" style="animation-delay: .2s">
                <p class="text-[10px] text-slate-600">
                    <span class="text-red-500 font-semibold">*</span> = kolom wajib diisi.
                </p>
                <p class="text-[10px] text-amber-700 text-center">
                    Setelah mengirim pendaftaran, statusmu akan <span class="font-bold">pending</span> dan menunggu verifikasi dari ketua ekskul.
                </p>
            </div>

        </main>
    </div>

</body>
</html>