@extends('layouts.kesiswaan')

@section('title', 'Dashboard Kesiswaan')

@section('content')
    {{-- HERO --}}
    <div data-tilt-card class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-4 md:p-6 md:pb-12 lg:pb-14 text-white shadow-xl shadow-sky-200 soul-timbul tilt-card animate-fade-up">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>
        <div class="relative grid md:grid-cols-5 gap-4 lg:gap-6 items-center">
            <!-- TEXT + CTA -->
            <div class="md:col-span-3">
                <h1 class="text-xl md:text-2xl font-extrabold tracking-tight leading-tight">
                    Selamat datang {{ auth()->user()->username }}
                </h1>
                <p class="text-sm text-white/85 mt-1 font-medium">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </p>
                <p class="text-xs text-white/70 mt-2 max-w-lg leading-relaxed">
                    Kelola akun pengguna, data ekstrakurikuler, dan daftar kelas sekolah melalui panel kesiswaan.
                </p>

                <div class="flex gap-2 flex-wrap mt-4">
                    <a href="{{ route('kesiswaan.users.create') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white text-sky-700 font-bold text-xs rounded-xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        Buat Akun Baru
                    </a>
                    <a href="{{ route('kesiswaan.users.index') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-xl hover:bg-white/20 transition-all">
                        <i class="fa-solid fa-users text-xs"></i>
                        Daftar Akun
                    </a>
                </div>
            </div>

            <!-- ILLUSTRATIVE PANEL + FLOATING STATS -->
            <div class="md:col-span-2 relative hidden md:block">
                <div class="relative h-36 md:h-40 lg:h-44 rounded-3xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center">
                    <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
                        <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-white/20 blur-2xl"></div>
                    </div>
                    <img src="{{ asset('images/dashboardbiru.png') }}" alt="Ilustrasi Kesiswaan"
                         class="h-36 md:h-40 lg:h-44 w-auto max-w-[110px] md:max-w-[130px] lg:max-w-[150px] object-contain drop-shadow-xl animate-floaty select-none relative z-0 mb-1 md:mb-2 pointer-events-none">
                </div>

                <!-- floating stat card overlapping bottom edge -->
                <div class="absolute -bottom-6 md:-bottom-7 left-2 right-2 z-10 grid grid-cols-3 gap-1 bg-white rounded-xl shadow-xl shadow-sky-900/10 p-2">
                    <div class="text-center border-r border-slate-100 pr-1">
                        <p class="text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $totalUsers }}</p>
                        <p class="text-[10px] md:text-xs text-slate-400 font-semibold mt-1">Akun</p>
                    </div>
                    <div class="text-center border-r border-slate-100 px-1">
                        <p class="text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $totalSiswa }}</p>
                        <p class="text-[10px] md:text-xs text-slate-400 font-semibold mt-1">Siswa</p>
                    </div>
                    <div class="text-center pl-1">
                        <p class="text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $totalEkskul }}</p>
                        <p class="text-[10px] md:text-xs text-slate-400 font-semibold mt-1">Ekskul</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="relative mt-4 pt-3 md:mt-11 lg:mt-12 border-t border-white/15 grid grid-cols-1 sm:grid-cols-3 gap-2">
            <a href="{{ route('kesiswaan.users.index') }}" class="group flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-users text-sm text-white"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Akun</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Kelola Pengguna</p>
                </div>
            </a>
            <a href="{{ route('kesiswaan.ekskuls.index') }}" class="group flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-shapes text-sm text-white"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Ekskul</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Data Ekskul</p>
                </div>
            </a>
            <a href="{{ route('kesiswaan.kelas.index') }}" class="group flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-chalkboard-user text-sm text-white"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Kelas</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Data Kelas</p>
                </div>
            </a>
        </div>
    </div>

    {{-- STATS CARDS - MOBILE/TABLET --}}
    <div class="grid grid-cols-3 gap-3 md:hidden">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 animate-fade-up" style="animation-delay: .1s">
            <p class="text-xs font-bold text-slate-400 tracking-wider uppercase">Akun Pengguna</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $totalUsers }}</h3>
            <p class="text-xs font-semibold text-sky-600 mt-0.5 md:mt-1">Semua role</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 animate-fade-up" style="animation-delay: .2s">
            <p class="text-xs font-bold text-slate-400 tracking-wider uppercase">Siswa</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $totalSiswa }}</h3>
            <p class="text-xs font-semibold text-sky-600 mt-0.5 md:mt-1">Terdaftar</p>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const card = document.querySelector('[data-tilt-card]');
        if (!card) return;

        const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!canHover || reduceMotion) return;

        const MAX_TILT = 7;
        let frame = null;

        card.addEventListener('animationend', function () {
            card.classList.remove('animate-fade-up');
            if (card.matches(':hover')) card.classList.add('is-tilting');
        }, { once: true });

        card.addEventListener('pointerenter', function () {
            card.classList.add('is-tilting');
        });

        card.addEventListener('pointermove', function (event) {
            const rect = card.getBoundingClientRect();
            const px = (event.clientX - rect.left) / rect.width;
            const py = (event.clientY - rect.top) / rect.height;
            const rotateX = (0.5 - py) * MAX_TILT * 2;
            const rotateY = (px - 0.5) * MAX_TILT * 2;

            if (frame) cancelAnimationFrame(frame);
            frame = requestAnimationFrame(function () {
                frame = null;
                card.style.transform = 'perspective(1200px) rotateX(' + rotateX.toFixed(2) + 'deg) rotateY(' + rotateY.toFixed(2) + 'deg) scale(1.015)';
            });
        });

        function resetTilt() {
            if (frame) {
                cancelAnimationFrame(frame);
                frame = null;
            }
            card.classList.remove('is-tilting');
            card.style.transform = '';
        }

        card.addEventListener('pointerleave', resetTilt);
        card.addEventListener('pointercancel', resetTilt);
    });
</script>
@endsection
