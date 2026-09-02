@extends('ketua.layout')

@section('title', 'Dashboard Ketua')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <h1 class="text-xl font-bold text-slate-900">
                Selamat datang, {{ auth()->user()->siswa->nama ?? 'Ketua' }}
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                Kelola ekstrakurikuler {{ $ekskul->nama_ekskul ?? '-' }}
            </p>
        </div>
        <a href="{{ route('ketua.kegiatan.create') }}" 
           class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Kegiatan Baru
        </a>
    </div>

    <!-- STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Anggota</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalAnggota }}</h3>
                <p class="text-[11px] font-medium text-blue-600 mt-1">Anggota Aktif</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Pendaftaran Pending</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $pendingCount }}</h3>
                <p class="text-[11px] font-medium text-amber-600 mt-1">Perlu Ditinjau</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Pengajuan Keluar</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $pengajuanCount }}</h3>
                <p class="text-[11px] font-medium text-red-600 mt-1">Menunggu Keputusan</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Tren Kehadiran Pertemuan</h3>
                    <p class="text-[11px] text-slate-400">Statistik kehadiran anggota pada 6 pertemuan terbaru</p>
                </div>
                <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-semibold">Line Chart</span>
            </div>

            <div class="relative h-64 w-full">
                @if(isset($chartKegiatan['labels']) && count($chartKegiatan['labels']) > 0)
                    <canvas id="attendanceTrendChart"></canvas>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 text-xs">
                        <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Belum ada data kegiatan dan presensi.</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Distribusi Anggota</h3>
                    <p class="text-[11px] text-slate-400">Komposisi berdasarkan tingkat kelas</p>
                </div>
                <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-semibold">Doughnut</span>
            </div>

            <div class="relative h-64 w-full flex items-center justify-center">
                @if(isset($chartKelas['data']) && array_sum($chartKelas['data']) > 0)
                    <canvas id="classDistributionChart"></canvas>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 text-xs">
                        <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Belum ada data anggota aktif.</span>
                    </div>
                @endif
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
                        backgroundColor: ['#2563EB', '#FACC15', '#0F172A'],
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