@extends('layouts.kesiswaan')

@section('title', 'Dashboard Kesiswaan')

@section('content')
    {{-- HERO --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 lg:pb-16 text-white shadow-xl shadow-sky-200 animate-fade-up">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative grid lg:grid-cols-5 gap-8 items-center">
            <!-- TEXT + CTA -->
            <div class="lg:col-span-3">
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight">
                    Selamat datang {{ auth()->user()->username }}
                </h1>
                <p class="text-sm text-white/85 mt-1.5 font-medium">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
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
                    <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-white/20 blur-2xl"></div>
                    <div class="relative w-24 h-24 rounded-3xl bg-white/20 backdrop-blur border border-white/30 flex items-center justify-center text-white shadow-inner animate-floaty">
                        <svg class="w-11 h-11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- floating stat card overlapping bottom edge -->
                <div class="absolute -bottom-10 left-3 right-3 z-10 grid grid-cols-3 gap-2 bg-white rounded-2xl shadow-xl shadow-sky-900/10 p-3">
                    <div class="text-center border-r border-slate-100 pr-1">
                        <p class="text-base font-extrabold text-sky-700 leading-none">{{ $totalUsers }}</p>
                        <p class="text-xs text-slate-400 font-semibold mt-1">Akun</p>
                    </div>
                    <div class="text-center border-r border-slate-100 px-1">
                        <p class="text-base font-extrabold text-amber-600 leading-none">{{ $totalSiswa }}</p>
                        <p class="text-xs text-slate-400 font-semibold mt-1">Siswa</p>
                    </div>
                    <div class="text-center pl-1">
                        <p class="text-base font-extrabold text-sky-700 leading-none">{{ $totalEkskul }}</p>
                        <p class="text-xs text-slate-400 font-semibold mt-1">Ekskul</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="relative mt-6 pt-5 lg:mt-16 border-t border-white/15 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <a href="{{ route('kesiswaan.users.index') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Akun</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Kelola Pengguna</p>
                </div>
            </a>
            <a href="{{ route('kesiswaan.ekskuls.index') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Ekskul</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Data Ekskul</p>
                </div>
            </a>
            <a href="{{ route('kesiswaan.kelas.index') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Kelas</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Data Kelas</p>
                </div>
            </a>
        </div>
    </div>

    {{-- STATS CARDS - MOBILE/TABLET --}}
    <div class="grid grid-cols-3 gap-3 lg:hidden">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 animate-fade-up" style="animation-delay: .1s">
            <p class="text-xs font-bold text-slate-400 tracking-wider uppercase">Akun Pengguna</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $totalUsers }}</h3>
            <p class="text-xs font-semibold text-sky-600 mt-0.5 md:mt-1">Semua role</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 p-3 md:p-5 shadow-md shadow-amber-100 animate-fade-up" style="animation-delay: .2s">
            <p class="text-xs font-bold text-slate-400 tracking-wider uppercase">Siswa</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-amber-600">{{ $totalSiswa }}</h3>
            <p class="text-xs font-semibold text-amber-700 mt-0.5 md:mt-1">Terdaftar</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 animate-fade-up" style="animation-delay: .3s">
            <p class="text-xs font-bold text-slate-400 tracking-wider uppercase">Ekskul</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $totalEkskul }}</h3>
            <p class="text-xs font-semibold text-sky-600 mt-0.5 md:mt-1">{{ $ekskulBuka }} buka</p>
        </div>
    </div>

    {{-- GRAFIK & ANALISIS (desain sesi ini) --}}
    <div class="space-y-4 md:space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm md:text-base font-extrabold text-slate-900 tracking-tight">Grafik Pembuatan</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 md:gap-5">
            <!-- Grafik Akun (baris 1, sendiri) -->
            <div class="bg-white rounded-2xl border border-sky-100 shadow-sm p-4 md:p-5">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div>
                        <h3 class="text-xs md:text-sm font-extrabold text-slate-900">Pembuatan Akun</h3>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Semua role pengguna</p>
                    </div>
                    <span class="text-2xl md:text-3xl font-extrabold text-sky-600">{{ $totalUsers }}</span>
                </div>
                <div class="h-48 md:h-56 lg:h-64 relative">
                    <canvas id="akunChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-5">
                <!-- Grafik Kelas -->
                <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-4 md:p-5">
                    <div class="flex items-center justify-between mb-2 md:mb-3">
                        <div>
                            <h3 class="text-xs md:text-sm font-extrabold text-slate-900">Pembuatan Kelas</h3>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">Semua tingkat</p>
                        </div>
                        <span class="text-xl md:text-2xl font-extrabold text-amber-500">{{ $totalKelas }}</span>
                    </div>
                    <div class="h-32 md:h-40 lg:h-52 relative">
                        <canvas id="kelasChart"></canvas>
                    </div>
                </div>

                <!-- Grafik Ekskul -->
                <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 md:p-5">
                    <div class="flex items-center justify-between mb-2 md:mb-3">
                        <div>
                            <h3 class="text-xs md:text-sm font-extrabold text-slate-900">Pembuatan Ekskul</h3>
                            <p class="text-xs text-slate-400 font-medium mt-0.5">Ekstrakurikuler</p>
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
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-5 md:p-6 text-white shadow-xl shadow-sky-200">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -bottom-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            </div>

            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3 md:mb-4">
                <div>
                    <h3 class="text-xs md:text-sm font-extrabold text-white">Status Pendaftaran Ekskul</h3>
                    <p class="text-xs text-white/75 font-medium mt-0.5">Ekskul mana yang sedang membuka atau menutup pendaftaran</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-white/15 backdrop-blur border border-white/25 px-2.5 py-1 rounded-lg">
                        <span class="w-1.5 h-1.5"></span> {{ $ekskulStatus->get(1, 0) }} Terbuka
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white/80 bg-white/10 backdrop-blur border border-white/20 px-2.5 py-1 rounded-lg">
                        <span class="w-1.5 h-1.5"></span> {{ $ekskulStatus->get(0, 0) }} Tertutup
                    </span>
                </div>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                @php($ekskulBukaList = $ekskulDetail->where('is_open_recruitment', true)->values())
                @php($ekskulTutupList = $ekskulDetail->where('is_open_recruitment', false)->values())

                <div class="rounded-xl bg-white/10 backdrop-blur border border-white/20 p-3.5 md:p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-xs font-extrabold text-white">Buka Pendaftaran</span>
                    </div>
                    <ul class="space-y-2">
                        @forelse($ekskulBukaList as $ekskul)
                            <li class="flex items-center justify-between gap-2 bg-white/10 backdrop-blur rounded-lg border border-white/15 px-3 py-2">
                                <span class="truncate text-xs font-semibold text-white">{{ $ekskul->nama_ekskul }}</span>
                                <span class="shrink-0 text-xs font-bold text-emerald-100 bg-emerald-400/30 px-2 py-0.5 rounded-full">Terbuka</span>
                            </li>
                        @empty
                            <li class="text-xs text-white/70 font-medium py-2">Belum ada ekskul yang membuka pendaftaran.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="rounded-xl bg-white/10 backdrop-blur border border-white/20 p-3.5 md:p-4">
                    <div class="flex items-center gap-2 mb-3">

                        <span class="text-xs font-extrabold text-white">Tutup Pendaftaran</span>
                    </div>
                    <ul class="space-y-2">
                        @forelse($ekskulTutupList as $ekskul)
                            <li class="flex items-center justify-between gap-2 bg-white/10 backdrop-blur rounded-lg border border-white/15 px-3 py-2">
                                <span class="truncate text-xs font-semibold text-white/85">{{ $ekskul->nama_ekskul }}</span>
                                <span class="shrink-0 text-xs font-bold text-white/80 bg-white/15 px-2 py-0.5 rounded-full">Tertutup</span>
                            </li>
                        @empty
                            <li class="text-xs text-white/70 font-medium py-2">Belum ada ekskul yang menutup pendaftaran.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="space-y-6 lg:pt-6">

        <!-- LEFT COLUMN -->
        <div class="space-y-6">

            <!-- MODUL PENGELOLAAN -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden animate-fade-up" style="animation-delay: .2s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Modul Pengelolaan</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Akses cepat ke seluruh data master</p>
                    </div>
                </div>

                <div class="px-6 pt-2 pb-3">
                    <a href="{{ route('kesiswaan.ekskuls.index') }}" class="relative overflow-hidden p-3.5 bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 rounded-xl flex items-center justify-between mb-3 border border-white/20 gap-3 hover:shadow-lg transition">
                        <div class="absolute inset-0 pointer-events-none">
                            <div class="absolute -bottom-8 -right-6 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
                        </div>
                        <div class="relative flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur border border-white/30 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-white">Data Ekskul</h4>
                                <p class="text-xs text-white/75 mt-0.5">Pengaturan data ekstrakurikuler beserta penetapan pembina</p>
                            </div>
                        </div>
                        <span class="relative px-2.5 py-1 bg-white/15 backdrop-blur border border-white/25 text-white text-xs font-bold rounded-full shrink-0">{{ $totalEkskul }} Ekskul</span>
                    </a>
                    <a href="{{ route('kesiswaan.kelas.index') }}" class="relative overflow-hidden p-3.5 bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 rounded-xl flex items-center justify-between border border-white/20 gap-3 hover:shadow-lg transition">
                        <div class="absolute inset-0 pointer-events-none">
                            <div class="absolute -bottom-8 -left-6 w-32 h-32 rounded-full bg-white/10 blur-2xl"></div>
                        </div>
                        <div class="relative flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur border border-white/30 text-white flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-white">Data Kelas</h4>
                                <p class="text-xs text-white/75 mt-0.5">Manajemen daftar kelas berdasarkan tingkat dan periode tahun ajaran</p>
                            </div>
                        </div>
                        <span class="relative px-2.5 py-1 bg-white/15 backdrop-blur border border-white/25 text-white text-xs font-bold rounded-full shrink-0">{{ $totalKelas }} Kelas</span>
                    </a>
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

        const dashboardCharts = [];

        function getChartColors() {
            const isDark = document.documentElement.classList.contains('dark-mode') || document.documentElement.classList.contains('dark');
            return {
                isDark: isDark,
                gridColor: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(226, 232, 240, 0.6)',
                tickColor: isDark ? '#94a3b8' : '#64748b',
                tooltipBg: isDark ? '#1e293b' : '#0F172A',
                pointBorderColor: isDark ? '#1e293b' : '#fff',
            };
        }

        const themeColors = getChartColors();

        // --- GRAFIK AKUN (line, tanpa gradient di belakang) ---
        const akunCanvas = document.getElementById('akunChart');
        if (akunCanvas) {
            const ctx = akunCanvas.getContext('2d');

            const akunChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: akunLabels,
                    datasets: [{
                        label: 'Akun',
                        data: akunData,
                        borderColor: '#2563EB',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#2563EB',
                        pointBorderColor: themeColors.pointBorderColor,
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
                            backgroundColor: themeColors.tooltipBg,
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
                            ticks: { precision: 0, stepSize: 1, color: themeColors.tickColor },
                            grid: { color: themeColors.gridColor, drawBorder: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { maxRotation: 0, autoSkip: true, color: themeColors.tickColor }
                        }
                    }
                }
            });
            dashboardCharts.push(akunChart);
        }

        const barOptions = (color) => {
            const c = getChartColors();
            return {
                responsive: true,
                maintainAspectRatio: false,
                animation: anim,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: c.tooltipBg,
                        titleFont: { weight: 700 },
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, stepSize: 1, font: { size: 10 }, color: c.tickColor },
                        grid: { color: c.gridColor, drawBorder: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, maxRotation: 0, autoSkip: true, color: c.tickColor }
                    }
                }
            };
        };

        // --- GRAFIK KELAS (bar) ---
        const kelasCanvas = document.getElementById('kelasChart');
        if (kelasCanvas) {
            const kelasChart = new Chart(kelasCanvas.getContext('2d'), {
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
            dashboardCharts.push(kelasChart);
        }

        // --- GRAFIK EKSUL (bar emerald) ---
        const ekskulCanvas = document.getElementById('ekskulChart');
        if (ekskulCanvas) {
            const ekskulChart = new Chart(ekskulCanvas.getContext('2d'), {
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
            dashboardCharts.push(ekskulChart);
        }

        function updateChartThemes() {
            const c = getChartColors();
            dashboardCharts.forEach(chart => {
                if (!chart) return;
                if (chart.options.scales?.y) {
                    if (chart.options.scales.y.grid) chart.options.scales.y.grid.color = c.gridColor;
                    if (chart.options.scales.y.ticks) chart.options.scales.y.ticks.color = c.tickColor;
                }
                if (chart.options.scales?.x) {
                    if (chart.options.scales.x.ticks) chart.options.scales.x.ticks.color = c.tickColor;
                }
                if (chart.options.plugins?.tooltip) {
                    chart.options.plugins.tooltip.backgroundColor = c.tooltipBg;
                }
                chart.update('none');
            });
        }

        window.addEventListener('soul-theme-change', updateChartThemes);
    });
</script>
@endsection
