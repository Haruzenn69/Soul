@extends('layouts.kesiswaan')

@section('title', 'Dashboard Kesiswaan')

@section('content')
    {{-- HERO --}}
    <div data-tilt-card class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-3.5 sm:p-5 md:p-6 text-white shadow-xl shadow-sky-200 soul-timbul tilt-card animate-fade-up">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>
        <div class="relative grid grid-cols-5 gap-2 md:gap-4 lg:gap-6 items-center">
            <!-- TEXT + CTA -->
            <div class="min-w-0 col-span-3">
                <h1 class="text-base sm:text-xl md:text-2xl lg:text-[26px] font-extrabold tracking-tight leading-tight break-words">
                    Selamat datang {{ auth()->user()->username }}
                </h1>
                <p class="text-[11px] sm:text-sm md:text-[13px] lg:text-sm text-white/85 mt-1 font-medium leading-snug">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </p>
                <p class="text-[10px] sm:text-xs md:text-xs lg:text-[13px] text-white/80 mt-1.5 sm:mt-2 leading-relaxed text-left">
                    Kelola akun pengguna, data ekstrakurikuler, dan daftar kelas sekolah melalui panel kesiswaan.
                </p>

                <div class="flex flex-row flex-wrap items-center gap-1.5 sm:gap-2 mt-3 sm:mt-4">
                    <a href="{{ route('kesiswaan.siswa.index') }}" class="inline-flex max-w-full items-center justify-center gap-1 px-2.5 py-1.5 sm:px-4 sm:py-2 bg-white text-sky-700 font-bold text-[10px] sm:text-xs rounded-xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                        <i class="fa-solid fa-graduation-cap text-[10px] sm:text-xs shrink-0"></i>
                        <span>Data Siswa</span>
                    </a>
                    <a href="{{ route('kesiswaan.pembina.index') }}" class="inline-flex max-w-full items-center justify-center gap-1 px-2.5 py-1.5 sm:px-4 sm:py-2 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-[10px] sm:text-xs rounded-xl hover:bg-white/20 transition-all">
                        <i class="fa-solid fa-chalkboard-user text-[10px] sm:text-xs shrink-0"></i>
                        <span>Data Guru</span>
                    </a>
                    <a href="{{ route('kesiswaan.users.index') }}" class="inline-flex max-w-full items-center justify-center gap-1 px-2.5 py-1.5 sm:px-4 sm:py-2 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-[10px] sm:text-xs rounded-xl hover:bg-white/20 transition-all">
                        <i class="fa-solid fa-users text-[10px] sm:text-xs shrink-0"></i>
                        <span>Semua Akun</span>
                    </a>
                </div>
            </div>

            <!-- FOTO + STAT CARD (maskot menempel pas di atas kartu putih pada semua ukuran layar) -->
            <div class="col-span-2 relative min-w-0 flex flex-col justify-end">
                <div class="relative rounded-2xl md:rounded-3xl bg-white/10 backdrop-blur border border-white/20 p-1.5 sm:p-2.5 md:p-3 flex flex-col items-center justify-end overflow-hidden shadow-inner">
                    <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-white/20 blur-2xl pointer-events-none"></div>

                    <!-- FOTO MASKOT: menempel pas di atas card putih tanpa celah -->
                    <div class="relative z-0 -mb-2 sm:-mb-2.5 md:-mb-3.5 flex items-end justify-center pt-1">
                        <img src="{{ asset('images/dashboardbiru.png') }}" alt="Ilustrasi Kesiswaan"
                             class="h-20 sm:h-24 md:h-36 lg:h-40 w-auto object-contain drop-shadow-xl select-none pointer-events-none">
                    </div>

                    <!-- STAT CARD: berada tepat di bawah maskot, menutup potongan bawah gambar secara presisi -->
                    <div class="relative z-10 w-full grid grid-cols-3 gap-0.5 sm:gap-1 bg-white rounded-xl shadow-lg shadow-sky-900/10 p-1 sm:p-1.5 md:p-2">
                        <div class="text-center border-r border-slate-100 pr-0.5 sm:pr-1">
                            <p class="text-[11px] sm:text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $totalUsers }}</p>
                            <p class="text-[8px] sm:text-[9px] md:text-xs text-slate-400 font-semibold mt-0.5 sm:mt-1">Akun</p>
                        </div>
                        <div class="text-center border-r border-slate-100 px-0.5 sm:px-1">
                            <p class="text-[11px] sm:text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $totalSiswa }}</p>
                            <p class="text-[8px] sm:text-[9px] md:text-xs text-slate-400 font-semibold mt-0.5 sm:mt-1">Siswa</p>
                        </div>
                        <div class="text-center pl-0.5 sm:pl-1">
                            <p class="text-[11px] sm:text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $totalEkskul }}</p>
                            <p class="text-[8px] sm:text-[9px] md:text-xs text-slate-400 font-semibold mt-0.5 sm:mt-1">Ekskul</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="relative mt-3 sm:mt-4 pt-2.5 sm:pt-3 border-t border-white/15 grid grid-cols-2 sm:grid-cols-4 gap-1.5 sm:gap-2">
            <a href="{{ route('kesiswaan.siswa.index') }}" class="group flex min-w-0 items-center gap-1 sm:gap-2 px-1.5 py-1.5 sm:px-3 sm:py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-graduation-cap text-[10px] sm:text-sm text-white"></i>
                </div>
                <div class="min-w-0 text-left">
                    <p class="text-[10px] sm:text-xs font-bold leading-none truncate">Siswa</p>
                    <p class="text-[8px] sm:text-[10px] md:text-xs text-white/70 mt-0.5 sm:mt-1 leading-tight truncate">Data Siswa</p>
                </div>
            </a>
            <a href="{{ route('kesiswaan.pembina.index') }}" class="group flex min-w-0 items-center gap-1 sm:gap-2 px-1.5 py-1.5 sm:px-3 sm:py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-chalkboard-user text-[10px] sm:text-sm text-white"></i>
                </div>
                <div class="min-w-0 text-left">
                    <p class="text-[10px] sm:text-xs font-bold leading-none truncate">Guru</p>
                    <p class="text-[8px] sm:text-[10px] md:text-xs text-white/70 mt-0.5 sm:mt-1 leading-tight truncate">Data Guru</p>
                </div>
            </a>
            <a href="{{ route('kesiswaan.ekskuls.index') }}" class="group flex min-w-0 items-center gap-1 sm:gap-2 px-1.5 py-1.5 sm:px-3 sm:py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-shapes text-[10px] sm:text-sm text-white"></i>
                </div>
                <div class="min-w-0 text-left">
                    <p class="text-[10px] sm:text-xs font-bold leading-none truncate">Ekskul</p>
                    <p class="text-[8px] sm:text-[10px] md:text-xs text-white/70 mt-0.5 sm:mt-1 leading-tight truncate">Data Ekskul</p>
                </div>
            </a>
            <a href="{{ route('kesiswaan.kelas.index') }}" class="group flex min-w-0 items-center gap-1 sm:gap-2 px-1.5 py-1.5 sm:px-3 sm:py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-school text-[10px] sm:text-sm text-white"></i>
                </div>
                <div class="min-w-0 text-left">
                    <p class="text-[10px] sm:text-xs font-bold leading-none truncate">Kelas</p>
                    <p class="text-[8px] sm:text-[10px] md:text-xs text-white/70 mt-0.5 sm:mt-1 leading-tight truncate">Data Kelas</p>
                </div>
            </a>
        </div>
    </div>

    {{-- GRAFIK & ANALISIS (desain sesi ini) --}}
    <div class="space-y-4 md:space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm md:text-base font-extrabold text-slate-900 tracking-tight">Grafik Pembuatan</h2>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 md:gap-5">
            <!-- Grafik Akun: penuh di desktop, berdampingan dengan kelas di HP -->
            <div class="col-span-2 bg-white rounded-2xl border border-sky-100 shadow-sm p-3 sm:p-4 md:p-5">
                <div class="flex items-center justify-between mb-3 md:mb-4">
                    <div>
                        <h3 class="text-xs md:text-sm font-extrabold text-slate-900">Pembuatan Akun</h3>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">Semua role pengguna</p>
                    </div>
                    <span class="text-2xl md:text-3xl font-extrabold text-sky-600">{{ $totalUsers }}</span>
                </div>
                <div class="h-32 md:h-56 lg:h-64 relative">
                    <canvas id="akunChart"></canvas>
                </div>
            </div>

                <!-- Grafik Kelas -->
                <div class="col-span-1 md:col-span-1 bg-white rounded-2xl border border-amber-100 shadow-sm p-3 sm:p-4 md:p-5">
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
                <div class="col-span-1 md:col-span-1 bg-white rounded-2xl border border-emerald-100 shadow-sm p-3 sm:p-4 md:p-5">
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

        <!-- DAFTAR AKUN SISWA DAN GURU TERDAFTAR (Header biru seperti card paling atas, tanpa pil, tanpa svg dan emoji) -->
        <div class="bg-white rounded-2xl md:rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Header Section: Gradasi biru seperti card paling atas dengan teks putih -->
            <div class="bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-4 sm:p-5 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-white tracking-tight">Akun Siswa &amp; Guru Terdaftar</h3>
                    <p class="text-xs text-white/85 font-medium mt-0.5">Daftar akun terbaru yang telah berhasil didaftarkan ke sistem</p>
                </div>
                <a href="{{ route('kesiswaan.users.index') }}" class="inline-flex items-center text-xs font-bold text-sky-700 bg-white hover:bg-sky-50 px-3 py-1.5 rounded-xl shadow-sm transition-all">
                    Lihat Semua
                </a>
            </div>

            <!-- Table Section: Bersih, tanpa pil, tanpa svg/emoji -->
            <div class="p-3 sm:p-5 pt-2 sm:pt-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-semibold uppercase tracking-wider text-[11px]">
                                <th class="py-2.5 px-3 whitespace-nowrap">Nama Lengkap</th>
                                <th class="py-2.5 px-3 whitespace-nowrap">Peran</th>
                                <th class="py-2.5 px-3 whitespace-nowrap">NIS / NIP</th>
                                <th class="py-2.5 px-3 whitespace-nowrap">Email</th>
                                <th class="py-2.5 px-3 whitespace-nowrap text-right">Waktu Daftar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse($recentUsers as $user)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 px-3 font-semibold text-slate-800 whitespace-nowrap">
                                        {{ $user->role === 'siswa' ? ($user->siswa?->nama ?? $user->username) : ($user->pembina?->nama ?? $user->username) }}
                                    </td>
                                    <td class="py-3 px-3 text-slate-600 whitespace-nowrap">
                                        {{ $user->role === 'siswa' ? ($user->siswa?->kelas ? 'Siswa - ' . $user->siswa->kelas->nama : 'Siswa') : 'Guru / Pembina' }}
                                    </td>
                                    <td class="py-3 px-3 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                        {{ $user->role === 'siswa' ? ($user->siswa?->nis ?? '-') : ($user->pembina?->nip ?? '-') }}
                                    </td>
                                    <td class="py-3 px-3 text-slate-500 whitespace-nowrap">
                                        {{ $user->email }}
                                    </td>
                                    <td class="py-3 px-3 text-right text-slate-400 text-[11px] whitespace-nowrap">
                                        {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                        Belum ada akun siswa atau guru yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
