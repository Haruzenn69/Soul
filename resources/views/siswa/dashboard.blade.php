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
                <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 bg-blue-50 text-blue-600 rounded-xl font-semibold text-xs transition-colors">
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

        <!-- User Profile Card Bottom -->
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
        
        <!-- TOP NAVBAR HEADER -->
        <header class="px-8 py-4 bg-white border-b border-slate-200/80 flex items-center justify-between gap-4">
            <div class="relative w-full max-w-md">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" placeholder="Cari ekskul, siswa, kegiatan..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all">
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-slate-100/80 text-slate-700 border border-slate-200/60 px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> Siswa
                </div>
                <button class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-xs relative text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="w-2 h-2 rounded-full bg-amber-400 absolute top-2 right-2 border-2 border-white"></span>
                </button>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-xs font-semibold border border-red-100 transition shadow-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- DASHBOARD CONTENT -->
        <main class="p-8 space-y-6 overflow-y-auto">
            
            <!-- Greeting & Header CTA -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">
                        Selamat datang, {{ $siswa->nama ?? auth()->user()->username }}
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }} · Semester Ganjil 2026/2027</p>
                </div>
                @if(!$ekskul)
                    <a href="{{ route('siswa.daftar-ekskul') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Daftar Ekskul
                    </a>
                @endif
            </div>

            <!-- STATS CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Status Ekskul</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $ekskul ? '1' : '0' }} <span class="text-xs font-normal text-slate-400">Ekskul</span></h3>
                        <p class="text-[11px] font-medium {{ $ekskul ? 'text-emerald-600' : 'text-amber-600' }} mt-1">
                            {{ $ekskul ? 'Terdaftar Aktif' : 'Belum Terdaftar' }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Kehadiran</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalHadir ?? 0 }} <span class="text-xs font-normal text-slate-400">Pertemuan</span></h3>
                        <p class="text-[11px] font-medium text-emerald-600 mt-1">Presensi Tercatat</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Kegiatan Mendatang</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $kegiatanMendatang->count() }}</h3>
                        <p class="text-[11px] font-medium text-blue-600 mt-1">Agenda Ekskul</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- MAIN GRID CONTENT -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- LEFT COLUMN: Ekskul Saya -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                        <div class="flex justify-between items-center mb-5">
                            <h2 class="text-sm font-bold text-slate-900">Ekskul Saya</h2>
                            <span class="text-xs font-medium text-slate-400">{{ $ekskul ? '1' : '0' }} dari Maksimal 1 Ekskul</span>
                        </div>

                        @if($ekskul)
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
                                    <span class="text-xs font-bold text-slate-900">{{ $totalHadir ?? 0 }} Pertemuan</span>
                                </div>
                            </div>

                            <div class="mt-4 p-3.5 rounded-xl border border-dashed border-slate-200 text-center bg-slate-50/50">
                                <p class="text-[11px] text-slate-400 leading-relaxed">Kamu sudah terdaftar di 1 ekskul. Keluar dari ekskul aktif jika ingin mendaftar ke ekskul lain.</p>
                            </div>
                        @else
                            <div class="p-8 text-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                                <p class="text-xs font-medium text-slate-500">Kamu belum mendaftar di ekstrakurikuler mana pun.</p>
                                <a href="{{ route('siswa.katalog') }}" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                    Jelajahi Katalog Ekskul
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT COLUMN: Kegiatan Mendatang -->
                <div class="space-y-6">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm">
                        <h2 class="text-sm font-bold text-slate-900 mb-3.5">Kegiatan Mendatang</h2>
                        <div class="space-y-3">
                            @forelse($kegiatanMendatang ?? [] as $kegiatan)
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/50">
                                    <h4 class="text-xs font-semibold text-slate-800">{{ $kegiatan->materi ?? 'Kegiatan' }}</h4>
                                    <p class="text-[10px] text-slate-400 mt-0.5">
                                        {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('dddd, DD MMM Y') }}
                                    </p>
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
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
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

</body>
</html>