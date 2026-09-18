@extends('ketua.layout')

@section('title', 'Dashboard Ketua' . ($ekskul ? ' ' . $ekskul->nama_ekskul : ''))

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
                    <span class="px-3 py-1 rounded-full bg-white/15 backdrop-blur border border-white/20 text-[10px] font-bold tracking-wide uppercase">Dashboard Ketua</span>
                    @if($ekskul)
                        <span class="px-3 py-1 rounded-full bg-amber-300/30 backdrop-blur border border-amber-200/40 text-[10px] font-bold tracking-wide uppercase flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                            Memimpin {{ $ekskul->nama_ekskul }}
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight">
                    Halo, {{ auth()->user()->siswa->nama ?? 'Ketua' }}!
                </h1>
                <p class="text-sm text-white/85 mt-1.5 font-medium">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }} · Semester Ganjil 2026/2027
                </p>
                <p class="text-xs text-white/70 mt-3 max-w-lg leading-relaxed">
                    Kelola keanggotaan, tinjau pendaftaran, dan pantau kehadiran kegiatan ekskulmu di sini.
                </p>

                <div class="flex gap-3 flex-wrap mt-6">
                    <a href="{{ route('ketua.kegiatan.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Kegiatan Baru
                    </a>
                    <a href="{{ route('ketua.pendaftaran.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-2xl hover:bg-white/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Tinjau Pendaftaran
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14"/>
                        </svg>
                    </div>
                </div>

                <!-- floating stat card overlapping bottom edge -->
                <div class="absolute -bottom-10 left-3 right-3 grid grid-cols-3 gap-2 bg-white rounded-2xl shadow-xl shadow-sky-900/10 p-3">
                    <div class="text-center border-r border-slate-100 pr-1">
                        <p class="text-base font-extrabold text-sky-700 leading-none">{{ $totalAnggota }}</p>
                        <p class="text-[9px] text-slate-400 font-semibold mt-1">Anggota</p>
                    </div>
                    <div class="text-center border-r border-slate-100 px-1">
                        <p class="text-base font-extrabold text-amber-600 leading-none">{{ $pendingCount }}</p>
                        <p class="text-[9px] text-slate-400 font-semibold mt-1">Pendaftaran</p>
                    </div>
                    <div class="text-center pl-1">
                        <p class="text-base font-extrabold text-sky-700 leading-none">{{ $kegiatanBulanIni ?? 0 }}</p>
                        <p class="text-[9px] text-slate-400 font-semibold mt-1">Kegiatan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="relative mt-6 pt-5 border-t border-white/15 grid grid-cols-3 gap-3">
            <a href="{{ route('ketua.anggota.index') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Anggota</p>
                    <p class="text-[10px] text-white/70 mt-1 leading-none">Kelola Anggota</p>
                </div>
            </a>
            <a href="{{ route('ketua.pendaftaran.index') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Pendaftaran</p>
                    <p class="text-[10px] text-white/70 mt-1 leading-none">Tinjau Masuk</p>
                </div>
            </a>
            <a href="{{ route('ketua.kegiatan.index') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-amber-300/40 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Kegiatan</p>
                    <p class="text-[10px] text-white/70 mt-1 leading-none">Agenda Ekskul</p>
                </div>
            </a>
        </div>
    </div>

    {{-- STATS CARDS - MOBILE/TABLET --}}
    <div class="grid grid-cols-3 gap-3 lg:hidden">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 animate-fade-up" style="animation-delay: .1s">
            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Anggota Aktif</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $totalAnggota }}</h3>
            <p class="text-[9px] md:text-[11px] font-semibold {{ $totalAnggota ? 'text-emerald-600' : 'text-amber-600' }} mt-0.5 md:mt-1">{{ $totalAnggota ? 'Terdaftar' : 'Belum' }}</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 p-3 md:p-5 shadow-md shadow-amber-100 animate-fade-up" style="animation-delay: .2s">
            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Pendaftaran</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-amber-600">{{ $pendingCount }}</h3>
            <p class="text-[9px] md:text-[11px] font-semibold text-amber-700 mt-0.5 md:mt-1">Menunggu</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 animate-fade-up" style="animation-delay: .3s">
            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Kegiatan</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $kegiatanBulanIni ?? 0 }}</h3>
            <p class="text-[9px] md:text-[11px] font-semibold text-sky-600 mt-0.5 md:mt-1">Bulan Ini</p>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:pt-6">

        <!-- LEFT COLUMN: TREN KEHADIRAN -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .15s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Tren Kehadiran Pertemuan</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Statistik kehadiran anggota pada 6 pertemuan terbaru</p>
                    </div>
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center shadow-md shadow-sky-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14"/>
                        </svg>
                    </span>
                </div>

                <div class="p-5">
                    <div class="relative h-64 w-full">
                        @if(isset($chartKegiatan['labels']) && count($chartKegiatan['labels']) > 0)
                            <canvas id="attendanceTrendChart"></canvas>
                        @else
                            <div class="h-full flex flex-col items-center justify-center text-slate-400 text-xs">
                                <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span class="mt-2 font-semibold text-slate-500">Belum ada data kegiatan dan presensi.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-6">

            <!-- DISTRIBUSI ANGGOTA -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .2s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Distribusi Anggota</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Komposisi berdasarkan tingkat kelas</p>
                    </div>
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center shadow-md shadow-amber-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </span>
                </div>

                <div class="p-5">
                    <div class="relative h-64 w-full flex items-center justify-center">
                        @if(isset($chartKelas['data']) && array_sum($chartKelas['data']) > 0)
                            <canvas id="classDistributionChart"></canvas>
                        @else
                            <div class="h-full flex flex-col items-center justify-center text-slate-400 text-xs">
                                <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span class="mt-2 font-semibold text-slate-500">Belum ada data anggota aktif.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- PRIORITY ACTIONS -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .25s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Yang perlu kamu cek</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Aksi cepat untuk tugas yang menunggu</p>
                    </div>
                    <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 border border-amber-100 px-3 py-1.5 rounded-full">{{ $pendingCount + $pengajuanCount }} Tertunda</span>
                </div>

                <div class="p-5 space-y-3">
                    <a href="{{ route('ketua.pendaftaran.index') }}" class="flex items-center justify-between gap-3 rounded-xl border border-amber-100 bg-amber-50/70 px-4 py-3 hover:bg-amber-100/70 transition">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            <span>
                                <span class="block text-xs font-bold text-slate-700">Pendaftaran baru</span>
                                <span class="block text-[10px] text-slate-400">{{ $pendingCount }} menunggu tinjauan</span>
                            </span>
                        </span>
                        <span class="text-amber-600 text-sm" aria-hidden="true">→</span>
                    </a>
                    <a href="{{ route('ketua.pengajuan-keluar.index') }}" class="flex items-center justify-between gap-3 rounded-xl border border-rose-100 bg-rose-50/70 px-4 py-3 hover:bg-rose-100/70 transition">
                        <span class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </span>
                            <span>
                                <span class="block text-xs font-bold text-slate-700">Pengajuan keluar</span>
                                <span class="block text-[10px] text-slate-400">{{ $pengajuanCount }} menunggu keputusan</span>
                            </span>
                        </span>
                        <span class="text-rose-600 text-sm" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const attendanceCanvas = document.getElementById('attendanceTrendChart');
        if (attendanceCanvas) {
            const labels = @json($chartKegiatan['labels'] ?? []);
            const dataHadir = @json($chartKegiatan['hadir'] ?? []);
            const dataIzin = @json($chartKegiatan['izin'] ?? []);
            const dataSakit = @json($chartKegiatan['sakit'] ?? []);
            const dataAlpha = @json($chartKegiatan['alpha'] ?? []);

            new Chart(attendanceCanvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Hadir',
                            data: dataHadir,
                            borderColor: '#2563EB',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#2563EB',
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 2.5
                        },
                        {
                            label: 'Izin',
                            data: dataIzin,
                            borderColor: '#F59E0B',
                            backgroundColor: 'transparent',
                            tension: 0.35,
                            pointBackgroundColor: '#F59E0B',
                            pointRadius: 3,
                            borderDash: [4, 4],
                            borderWidth: 2
                        },
                        {
                            label: 'Sakit',
                            data: dataSakit,
                            borderColor: '#8B5CF6',
                            backgroundColor: 'transparent',
                            tension: 0.35,
                            pointBackgroundColor: '#8B5CF6',
                            pointRadius: 3,
                            borderDash: [2, 2],
                            borderWidth: 2
                        },
                        {
                            label: 'Alpha',
                            data: dataAlpha,
                            borderColor: '#EF4444',
                            backgroundColor: 'transparent',
                            tension: 0.35,
                            pointBackgroundColor: '#EF4444',
                            pointRadius: 3,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 11, weight: '500' },
                                padding: 12
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            padding: 10,
                            cornerRadius: 12
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                font: { size: 10 },
                                color: '#94A3B8'
                            },
                            grid: {
                                color: '#F1F5F9'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 10 },
                                color: '#94A3B8'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        const classCanvas = document.getElementById('classDistributionChart');
        if (classCanvas) {
            const classLabels = @json($chartKelas['labels'] ?? []);
            const classData = @json($chartKelas['data'] ?? []);

            new Chart(classCanvas, {
                type: 'doughnut',
                data: {
                    labels: classLabels,
                    datasets: [{
                        data: classData,
                        backgroundColor: ['#0EA5E9', '#FACC15', '#2563EB'],
                        borderColor: '#FFFFFF',
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 14,
                                font: { size: 11, weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0F172A',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            padding: 10,
                            cornerRadius: 12,
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return ` ${context.label}: ${value} siswa (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush