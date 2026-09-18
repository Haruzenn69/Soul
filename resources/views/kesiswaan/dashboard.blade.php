@extends('layouts.kesiswaan')

@section('title', 'Dashboard Kesiswaan')

@section('content')
<div class="space-y-6">
    <!-- HERO GREETING -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200 animate-fade-up">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 -right-10 w-72 h-72 rounded-full bg-amber-200/40 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute top-10 right-1/4 w-16 h-16 rounded-full bg-amber-300/50 blur-2xl"></div>
        </div>

        <div class="relative flex flex-col lg:flex-row lg:items-center gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-3 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-white/15 backdrop-blur border border-white/20 text-[10px] font-bold tracking-wide uppercase">Dashboard Kesiswaan</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight">
                    Halo, {{ auth()->user()->username }}!
                </h1>
                <p class="text-white/80 text-sm mt-2 font-medium">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>

            <div class="flex gap-3 shrink-0 flex-wrap">
                <a href="{{ route('kesiswaan.users.create') }}" class="inline-flex items-center gap-2 px-4 md:px-5 py-2.5 md:py-3 bg-white/15 backdrop-blur rounded-2xl border border-white/25 text-[10px] md:text-xs font-bold hover:bg-white/25 transition-all shadow-md shadow-sky-900/10">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Akun Baru
                </a>
            </div>
        </div>
    </div>

    <!-- METRICS GRID (4 CARDS → 2 di HP, 4 di Desktop) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <!-- Card 1: Total Akun -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .1s">
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-sky-100 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Akun</p>
                    <h3 class="text-xl md:text-2xl font-extrabold mt-0.5 md:mt-1 text-sky-700">{{ $totalUsers }}</h3>
                    <p class="text-[9px] md:text-[11px] font-semibold text-sky-600 mt-0.5 md:mt-1">Semua role</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center border border-white/40 shadow-lg shadow-sky-300">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Siswa -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-100 to-white border border-emerald-200 p-3 md:p-5 shadow-md shadow-emerald-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .2s">
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-emerald-100 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Siswa</p>
                    <h3 class="text-xl md:text-2xl font-extrabold mt-0.5 md:mt-1 text-emerald-600">{{ $totalSiswa }}</h3>
                    <p class="text-[9px] md:text-[11px] font-semibold text-emerald-600 mt-0.5 md:mt-1">Termasuk ketua</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center border border-white/40 shadow-lg shadow-emerald-300">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Ekskul Aktif -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-100 to-white border border-amber-200 p-3 md:p-5 shadow-md shadow-amber-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .3s">
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-amber-100 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Ekskul Aktif</p>
                    <h3 class="text-xl md:text-2xl font-extrabold mt-0.5 md:mt-1 text-amber-600">{{ $totalEkskul }}</h3>
                    <p class="text-[9px] md:text-[11px] font-semibold text-amber-600 mt-0.5 md:mt-1">{{ $ekskulBuka }} Buka Pendaftaran</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-yellow-500 text-white flex items-center justify-center border border-white/40 shadow-lg shadow-amber-300">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4: Kelas Terdaftar -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-100 to-white border border-slate-200 p-3 md:p-5 shadow-md shadow-slate-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .4s">
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-slate-100 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Kelas Terdaftar</p>
                    <h3 class="text-xl md:text-2xl font-extrabold mt-0.5 md:mt-1 text-slate-600">{{ $totalKelas }}</h3>
                    <p class="text-[9px] md:text-[11px] font-semibold text-slate-500 mt-0.5 md:mt-1">Semua tingkat</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-2xl bg-gradient-to-br from-slate-300 to-slate-400 text-white flex items-center justify-center border border-white/40 shadow-lg shadow-slate-300">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- GRAFIK & ANALISIS -->
    <div class="space-y-6 pt-2">
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
                @php($ekskulBuka = $ekskulDetail->where('is_open_recruitment', true)->values())
                @php($ekskulTutup = $ekskulDetail->where('is_open_recruitment', false)->values())

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
                        @forelse($ekskulBuka as $ekskul)
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
                        @forelse($ekskulTutup as $ekskul)
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

    <!-- NAVIGATION MENU (3 CARD → 1 di HP, 3 di Desktop) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-5 pt-2">
        <a href="{{ route('kesiswaan.users.index') }}"
           class="bg-white p-4 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 hover:border-sky-200 hover:shadow-xl hover:shadow-sky-100 transition-all block group animate-fade-up" style="animation-delay: .15s">
            <div class="flex items-center justify-between">
                <h2 class="text-xs md:text-sm font-extrabold text-slate-900 group-hover:text-sky-600 transition">Kelola Akun Pengguna</h2>
                <span class="text-[9px] md:text-xs font-bold text-sky-600 bg-sky-50 border border-sky-100 px-2 md:px-2.5 py-0.5 md:py-1 rounded-lg">Akses →</span>
            </div>
            <p class="text-[10px] md:text-xs text-slate-500 mt-1.5 md:mt-2 leading-relaxed">
                Buat akun, atur peranan, dan reset kata sandi ke default "password".
            </p>
        </a>

        <a href="{{ route('kesiswaan.ekskuls.index') }}"
           class="bg-white p-4 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 hover:border-sky-200 hover:shadow-xl hover:shadow-sky-100 transition-all block group animate-fade-up" style="animation-delay: .25s">
            <div class="flex items-center justify-between">
                <h2 class="text-xs md:text-sm font-extrabold text-slate-900 group-hover:text-sky-600 transition">Kelola Ekskul</h2>
                <span class="text-[9px] md:text-xs font-bold text-sky-600 bg-sky-50 border border-sky-100 px-2 md:px-2.5 py-0.5 md:py-1 rounded-lg">Akses →</span>
            </div>
            <p class="text-[10px] md:text-xs text-slate-500 mt-1.5 md:mt-2 leading-relaxed">
                Pengaturan data ekstrakurikuler beserta penetapan pembina dan pelatih.
            </p>
        </a>

        <a href="{{ route('kesiswaan.kelas.index') }}"
           class="bg-white p-4 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 hover:border-sky-200 hover:shadow-xl hover:shadow-sky-100 transition-all block group animate-fade-up" style="animation-delay: .35s">
            <div class="flex items-center justify-between">
                <h2 class="text-xs md:text-sm font-extrabold text-slate-900 group-hover:text-sky-600 transition">Kelola Kelas</h2>
                <span class="text-[9px] md:text-xs font-bold text-sky-600 bg-sky-50 border border-sky-100 px-2 md:px-2.5 py-0.5 md:py-1 rounded-lg">Akses →</span>
            </div>
            <p class="text-[10px] md:text-xs text-slate-500 mt-1.5 md:mt-2 leading-relaxed">
                Manajemen daftar kelas berdasarkan tingkat dan periode tahun ajaran.
            </p>
        </a>
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