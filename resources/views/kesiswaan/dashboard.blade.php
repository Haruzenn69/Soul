@extends('layouts.kesiswaan')

@section('title', 'Dashboard Kesiswaan')

@section('content')
    {{-- HERO --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 lg:pb-14 text-white shadow-xl shadow-sky-200 animate-fade-up">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 -right-10 w-72 h-72 rounded-full bg-amber-200/40 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute top-10 right-1/4 w-16 h-16 rounded-full bg-amber-300/50 blur-2xl"></div>
        </div>

        <div class="relative grid lg:grid-cols-5 gap-8 items-center">
            <!-- TEXT + CTA -->
            <div class="lg:col-span-3">
                <div class="flex items-center gap-2 mb-3 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-white/15 backdrop-blur border border-white/20 text-[10px] font-bold tracking-wide uppercase">Dashboard Kesiswaan</span>
                    <span class="px-3 py-1 rounded-full bg-amber-300/30 backdrop-blur border border-amber-200/40 text-[10px] font-bold tracking-wide uppercase flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                        {{ $ekskulBuka }} Ekskul Buka
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight">
                    Halo, {{ auth()->user()->username }}!
                </h1>
                <p class="text-sm text-white/85 mt-1.5 font-medium">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }} · Semester Ganjil 2026/2027
                </p>
                <p class="text-xs text-white/70 mt-3 max-w-lg leading-relaxed">
                    Kelola akun pengguna, data ekstrakurikuler, dan daftar kelas sekolah melalui panel kesiswaan.
                </p>

                <div class="flex gap-3 flex-wrap mt-6">
                    <a href="{{ route('kesiswaan.users.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Akun Baru
                    </a>
                    <a href="{{ route('kesiswaan.users.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-2xl hover:bg-white/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Daftar Akun
                    </a>
                </div>
            </div>

            <!-- ILLUSTRATIVE PANEL + FLOATING STATS (desktop only) -->
            <div class="lg:col-span-2 relative hidden lg:block">
                <div class="relative h-52 rounded-3xl bg-white/10 backdrop-blur border border-white/20 overflow-hidden flex items-center justify-center">
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-amber-300/30 blur-2xl"></div>
                    <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-white/20 blur-2xl"></div>
                    <div class="relative w-24 h-24 rounded-3xl bg-white/20 backdrop-blur border border-white/30 flex items-center justify-center text-white shadow-inner animate-floaty">
                        <svg class="w-11 h-11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- floating stat card overlapping bottom edge -->
                <div class="absolute -bottom-10 left-3 right-3 grid grid-cols-3 gap-2 bg-white rounded-2xl shadow-xl shadow-sky-900/10 p-3">
                    <div class="text-center border-r border-slate-100 pr-1">
                        <p class="text-base font-extrabold text-sky-700 leading-none">{{ $totalUsers }}</p>
                        <p class="text-[9px] text-slate-400 font-semibold mt-1">Akun</p>
                    </div>
                    <div class="text-center border-r border-slate-100 px-1">
                        <p class="text-base font-extrabold text-amber-600 leading-none">{{ $totalSiswa }}</p>
                        <p class="text-[9px] text-slate-400 font-semibold mt-1">Siswa</p>
                    </div>
                    <div class="text-center pl-1">
                        <p class="text-base font-extrabold text-sky-700 leading-none">{{ $totalEkskul }}</p>
                        <p class="text-[9px] text-slate-400 font-semibold mt-1">Ekskul</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="relative mt-6 pt-5 border-t border-white/15 grid grid-cols-3 gap-3">
            <a href="{{ route('kesiswaan.users.index') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Akun</p>
                    <p class="text-[10px] text-white/70 mt-1 leading-none">Kelola Pengguna</p>
                </div>
            </a>
            <a href="{{ route('kesiswaan.ekskuls.index') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Ekskul</p>
                    <p class="text-[10px] text-white/70 mt-1 leading-none">Data Ekskul</p>
                </div>
            </a>
            <a href="{{ route('kesiswaan.kelas.index') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-amber-300/40 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Kelas</p>
                    <p class="text-[10px] text-white/70 mt-1 leading-none">Data Kelas</p>
                </div>
            </a>
        </div>
    </div>

    {{-- STATS CARDS - MOBILE/TABLET --}}
    <div class="grid grid-cols-3 gap-3 lg:hidden">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 animate-fade-up" style="animation-delay: .1s">
            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Akun Pengguna</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $totalUsers }}</h3>
            <p class="text-[9px] md:text-[11px] font-semibold text-sky-600 mt-0.5 md:mt-1">Semua role</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 p-3 md:p-5 shadow-md shadow-amber-100 animate-fade-up" style="animation-delay: .2s">
            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Siswa</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-amber-600">{{ $totalSiswa }}</h3>
            <p class="text-[9px] md:text-[11px] font-semibold text-amber-700 mt-0.5 md:mt-1">Terdaftar</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 animate-fade-up" style="animation-delay: .3s">
            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Ekskul</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $totalEkskul }}</h3>
            <p class="text-[9px] md:text-[11px] font-semibold text-sky-600 mt-0.5 md:mt-1">{{ $ekskulBuka }} buka</p>
        </div>
    </div>

    {{-- GRAFIK & ANALISIS (desain sesi ini) --}}
    <div class="space-y-4 md:space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm md:text-base font-extrabold text-slate-900 tracking-tight">Grafik Pembuatan</h2>
                <p class="text-[10px] md:text-xs text-slate-500 font-medium mt-0.5">Tren 6 bulan terakhir</p>
            </div>
            <span class="text-[10px] md:text-xs font-bold text-slate-500 bg-white border border-slate-200 px-3 py-1.5 rounded-lg hidden sm:inline-flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span> Live dari database
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-5">
            <!-- Grafik Akun (besar) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 p-4 md:p-5">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div>
                        <h3 class="text-xs md:text-sm font-extrabold text-slate-900">Pembuatan Akun</h3>
                        <p class="text-[10px] md:text-[11px] text-slate-400 font-medium mt-0.5">Semua role pengguna</p>
                    </div>
                    <span class="text-2xl md:text-3xl font-extrabold text-sky-600">{{ $totalUsers }}</span>
                </div>
                <div class="h-48 md:h-56 lg:h-64 relative">
                    <canvas id="akunChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3 md:gap-5">
                <!-- Grafik Kelas -->
                <div class="bg-white rounded-2xl border border-amber-100 shadow-lg shadow-amber-100/60 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-2 md:mb-3">
                        <div>
                            <h3 class="text-xs md:text-sm font-extrabold text-slate-900">Pembuatan Kelas</h3>
                            <p class="text-[10px] md:text-[11px] text-slate-400 font-medium mt-0.5">Semua tingkat</p>
                        </div>
                        <span class="text-xl md:text-2xl font-extrabold text-amber-500">{{ $totalKelas }}</span>
                    </div>
                    <div class="h-32 md:h-40 lg:h-52 relative">
                        <canvas id="kelasChart"></canvas>
                    </div>
                </div>

                <!-- Grafik Ekskul -->
                <div class="bg-white rounded-2xl border border-emerald-100 shadow-lg shadow-emerald-100/60 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-2 md:mb-3">
                        <div>
                            <h3 class="text-xs md:text-sm font-extrabold text-slate-900">Pembuatan Ekskul</h3>
                            <p class="text-[10px] md:text-[11px] text-slate-400 font-medium mt-0.5">Ekstrakurikuler</p>
                        </div>
                        <span class="text-xl md:text-2xl font-extrabold text-emerald-500">{{ $totalEkskul }}</span>
                    </div>
                    <div class="h-32 md:h-40 lg:h-52 relative">
                        <canvas id="ekskulChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- EKSUL BUKA / TUTUP PENDAFTARAN -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-lg shadow-slate-100/60 p-4 md:p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3 md:mb-4">
                <div>
                    <h3 class="text-xs md:text-sm font-extrabold text-slate-900">Status Pendaftaran Ekskul</h3>
                    <p class="text-[10px] md:text-[11px] text-slate-400 font-medium mt-0.5">Ekskul mana yang sedang membuka atau menutup pendaftaran</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-lg">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $ekskulStatus->get(1, 0) }} Terbuka
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold text-slate-500 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-lg">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> {{ $ekskulStatus->get(0, 0) }} Tertutup
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                @php($ekskulBukaList = $ekskulDetail->where('is_open_recruitment', true)->values())
                @php($ekskulTutupList = $ekskulDetail->where('is_open_recruitment', false)->values())

                <div class="rounded-xl bg-emerald-50/60 border border-emerald-100 p-3.5 md:p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-7 h-7 rounded-lg bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        <span class="text-xs font-extrabold text-emerald-700">Buka Pendaftaran</span>
                    </div>
                    <ul class="space-y-2">
                        @forelse($ekskulBukaList as $ekskul)
                            <li class="flex items-center justify-between gap-2 bg-white rounded-lg border border-emerald-100 px-3 py-2">
                                <span class="truncate text-[11px] md:text-xs font-semibold text-slate-700">{{ $ekskul->nama_ekskul }}</span>
                                <span class="shrink-0 text-[9px] md:text-[10px] font-bold text-emerald-600 bg-emerald-100/80 px-2 py-0.5 rounded-full">Terbuka</span>
                            </li>
                        @empty
                            <li class="text-[11px] text-slate-400 font-medium py-2">Belum ada ekskul yang membuka pendaftaran.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="rounded-xl bg-slate-50/70 border border-slate-100 p-3.5 md:p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-7 h-7 rounded-lg bg-slate-400 text-white flex items-center justify-center shadow-md shadow-slate-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        <span class="text-xs font-extrabold text-slate-600">Tutup Pendaftaran</span>
                    </div>
                    <ul class="space-y-2">
                        @forelse($ekskulTutupList as $ekskul)
                            <li class="flex items-center justify-between gap-2 bg-white rounded-lg border border-slate-200 px-3 py-2">
                                <span class="truncate text-[11px] md:text-xs font-semibold text-slate-600">{{ $ekskul->nama_ekskul }}</span>
                                <span class="shrink-0 text-[9px] md:text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">Tertutup</span>
                            </li>
                        @empty
                            <li class="text-[11px] text-slate-400 font-medium py-2">Belum ada ekskul yang menutup pendaftaran.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:pt-6">

        <!-- LEFT COLUMN -->
        <div class="lg:col-span-2 space-y-6">

            <!-- KELOLA AKUN -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .15s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Kelola Akun Pengguna</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Buat akun, atur peranan, dan reset kata sandi</p>
                    </div>
                    <span class="text-[11px] font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ $totalUsers }} Akun</span>
                </div>

                <div class="p-6">
                    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 text-white shadow-lg shadow-sky-200">
                        <div class="absolute inset-0 pointer-events-none">
                            <div class="absolute -top-16 -right-10 w-56 h-56 rounded-full bg-amber-200/40 blur-3xl"></div>
                            <div class="absolute bottom-0 left-1/4 w-40 h-40 rounded-full bg-white/20 blur-2xl animate-floaty"></div>
                        </div>

                        <div class="relative flex items-center justify-between gap-4 flex-wrap">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur border border-white/30 flex items-center justify-center text-white shadow-inner">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-extrabold leading-tight">Manajemen Akun Terpusat</h3>
                                    <p class="text-xs text-white/75 mt-1 max-w-sm leading-relaxed">Kelola akun pembina, siswa, dan ketua ekskul dalam satu tempat.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 bg-white/15 backdrop-blur border border-white/25 rounded-2xl px-4 py-2.5">
                                <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <div>
                                    <p class="text-lg font-extrabold leading-none">{{ $totalUsers }}</p>
                                    <p class="text-[10px] text-amber-200 font-semibold">Total Akun</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative mt-6 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('kesiswaan.users.create') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-sky-700 font-bold text-xs rounded-xl hover:bg-sky-50 hover:-translate-y-0.5 transition-all shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Buat Akun Baru
                            </a>
                            <a href="{{ route('kesiswaan.users.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-300/30 backdrop-blur border border-amber-200/40 text-white font-bold text-xs rounded-xl hover:bg-amber-300/50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                Kelola Akun
                            </a>
                        </div>
                    </div>

                    <div class="mt-4 p-4 rounded-2xl border border-dashed border-sky-200 text-center bg-sky-50/50">
                        <p class="text-[11px] text-slate-500 leading-relaxed">Akun baru dibuat dengan kata sandi default <span class="font-bold text-slate-700">"password"</span> yang bisa di-reset dari halaman daftar akun.</p>
                    </div>
                </div>
            </div>

            <!-- MODUL PENGELOLAAN -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .2s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Modul Pengelolaan</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Akses cepat ke seluruh data master</p>
                    </div>
                    <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 border border-amber-100 px-3 py-1.5 rounded-full">{{ $ekskulBuka }} Buka Pendaftaran</span>
                </div>

                <div class="p-5">
                    <a href="{{ route('kesiswaan.ekskuls.index') }}" class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-xl flex items-center justify-between mb-2.5 border border-sky-100 gap-3 hover:border-sky-200 hover:shadow-sm transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center shadow-md shadow-sky-200 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-800">Data Ekskul</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">Pengaturan data ekstrakurikuler beserta penetapan pembina</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-sky-50 border border-sky-100 text-sky-600 text-[10px] font-bold rounded-full shrink-0">{{ $totalEkskul }} Ekskul</span>
                    </a>
                    <a href="{{ route('kesiswaan.kelas.index') }}" class="p-3.5 bg-gradient-to-r from-amber-50 to-white rounded-xl flex items-center justify-between mb-2.5 border border-amber-100 gap-3 hover:border-amber-200 hover:shadow-sm transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center shadow-md shadow-amber-200 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-800">Data Kelas</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">Manajemen daftar kelas berdasarkan tingkat dan periode tahun ajaran</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-50 border border-amber-100 text-amber-600 text-[10px] font-bold rounded-full shrink-0">{{ $totalKelas }} Kelas</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-6">

            <!-- RINGKASAN -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .25s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Ringkasan</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Statistik data sekolah</p>
                    </div>
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center shadow-md shadow-sky-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14"/>
                        </svg>
                    </span>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="rounded-2xl bg-gradient-to-br from-sky-50 to-white border border-sky-100 p-3 text-center">
                            <p class="text-xl font-extrabold text-sky-700 leading-none">{{ $totalUsers }}</p>
                            <p class="text-[9px] text-slate-400 font-semibold mt-1.5">Akun</p>
                        </div>
                        <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 p-3 text-center">
                            <p class="text-xl font-extrabold text-emerald-600 leading-none">{{ $totalSiswa }}</p>
                            <p class="text-[9px] text-slate-400 font-semibold mt-1.5">Siswa</p>
                        </div>
                        <div class="rounded-2xl bg-gradient-to-br from-amber-50 to-white border border-amber-100 p-3 text-center">
                            <p class="text-xl font-extrabold text-amber-600 leading-none">{{ $totalEkskul }}</p>
                            <p class="text-[9px] text-slate-400 font-semibold mt-1.5">Ekskul</p>
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-amber-100 bg-amber-50/70 px-4 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </span>
                            <div>
                                <p class="text-xs font-bold text-slate-700">Ekskul Buka Pendaftaran</p>
                                <p class="text-[10px] text-slate-400">{{ $ekskulBuka }} dari {{ $totalEkskul }} ekskul</p>
                            </div>
                        </div>
                        <span class="text-amber-600 text-sm" aria-hidden="true">→</span>
                    </div>
                </div>
            </div>

            <!-- INFO -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .3s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Informasi</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tips pengelolaan akun</p>
                    </div>
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center shadow-md shadow-amber-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                </div>

                <div class="p-5 space-y-3">
                    <div class="rounded-xl border border-sky-100 bg-sky-50/70 p-3.5 flex items-start gap-3">
                        <svg class="w-4 h-4 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-[11px] text-slate-600 leading-relaxed">Setiap akun baru memiliki kata sandi default <span class="font-bold text-slate-800">"password"</span>. Disarankan untuk segera diubah oleh pemilik akun.</p>
                    </div>
                    <div class="rounded-xl border border-amber-100 bg-amber-50/70 p-3.5 flex items-start gap-3">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-[11px] text-slate-600 leading-relaxed">Pastikan data ekskul, pembina, dan kelas sudah lengkap sebelum membuka pendaftaran.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const anim = reduceMotion ? {} : { duration: 900, easing: 'easeOutQuart' };

        Chart.defaults.font.family = '"Plus Jakarta Sans", system-ui, sans-serif';
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#64748b';

        const akunLabels = @json($akunPerBulan['labels']);
        const akunData   = @json($akunPerBulan['data']);
        const kelasData  = @json($kelasPerBulan['data']);
        const ekskulData = @json($ekskulPerBulan['data']);

        // --- GRAFIK AKUN (line + area gradient) ---
        const akunCanvas = document.getElementById('akunChart');
        if (akunCanvas) {
            const ctx = akunCanvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, akunCanvas.clientHeight);
            gradient.addColorStop(0, 'rgba(56, 189, 248, 0.35)');
            gradient.addColorStop(1, 'rgba(56, 189, 248, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: akunLabels,
                    datasets: [{
                        label: 'Akun',
                        data: akunData,
                        borderColor: '#2563EB',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#2563EB',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: anim,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            titleFont: { weight: 700 },
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: function (ctx) { return ctx.parsed.y + ' akun'; }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, stepSize: 1 },
                            grid: { color: 'rgba(226, 232, 240, 0.6)', drawBorder: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { maxRotation: 0, autoSkip: true }
                        }
                    }
                }
            });
        }

        const barOptions = (color) => ({
            responsive: true,
            maintainAspectRatio: false,
            animation: anim,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0F172A',
                    titleFont: { weight: 700 },
                    padding: 10,
                    cornerRadius: 10,
                    displayColors: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, stepSize: 1, font: { size: 10 } },
                    grid: { color: 'rgba(226, 232, 240, 0.6)', drawBorder: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, maxRotation: 0, autoSkip: true }
                }
            }
        });

        // --- GRAFIK KELAS (bar) ---
        const kelasCanvas = document.getElementById('kelasChart');
        if (kelasCanvas) {
            new Chart(kelasCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: akunLabels,
                    datasets: [{
                        label: 'Kelas',
                        data: kelasData,
                        backgroundColor: '#FACC15',
                        hoverBackgroundColor: '#F59E0B',
                        borderRadius: 6,
                        maxBarThickness: 24,
                    }]
                },
                options: barOptions('#FACC15')
            });
        }

        // --- GRAFIK EKSUL (bar emerald) ---
        const ekskulCanvas = document.getElementById('ekskulChart');
        if (ekskulCanvas) {
            new Chart(ekskulCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: akunLabels,
                    datasets: [{
                        label: 'Ekskul',
                        data: ekskulData,
                        backgroundColor: '#34D399',
                        hoverBackgroundColor: '#10B981',
                        borderRadius: 6,
                        maxBarThickness: 24,
                    }]
                },
                options: barOptions('#34D399')
            });
        }
    });
</script>
@endsection