@extends('ketua.layout')
@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-theme-dark">
                Selamat datang, {{ auth()->user()->siswa->nama ?? 'Ketua' }}! 👋
            </h1>
            <p class="text-xs text-gray-400 mt-1">Kelola ekstrakurikuler {{ $ekskul->nama_ekskul ?? '-' }} Anda.</p>
        </div>
        <a href="{{ route('ketua.kegiatan.create') }}" class="px-6 py-3 bg-theme-yellow hover:bg-yellow-400 text-theme-dark font-bold text-xs rounded-full shadow-md transition flex items-center gap-2">
            <span>+</span> Buat Kegiatan Baru
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Total Anggota</p>
                <h3 class="text-3xl font-extrabold text-theme-dark mt-2">{{ $totalAnggota }}</h3>
                <p class="text-[11px] font-medium text-theme-blue mt-2">Anggota Aktif</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-theme-blue flex items-center justify-center text-xl">👥</div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Pendaftaran Pending</p>
                <h3 class="text-3xl font-extrabold text-theme-dark mt-2">{{ $pendingCount }}</h3>
                <p class="text-[11px] font-medium text-amber-500 mt-2">Perlu Ditinjau</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">📝</div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Pengajuan Keluar</p>
                <h3 class="text-3xl font-extrabold text-theme-dark mt-2">{{ $pengajuanCount }}</h3>
                <p class="text-[11px] font-medium text-red-500 mt-2">Menunggu Keputusan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-xl">🚪</div>
        </div>
    </div>

    <!-- CHARTS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 1. Line Chart: Tren Kehadiran Kegiatan Terakhir (2 Kolom) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-theme-dark">Tren Kehadiran Pertemuan</h3>
                    <p class="text-[11px] text-gray-400">Statistik kehadiran anggota pada 6 pertemuan terbaru</p>
                </div>
                <span class="px-3 py-1 bg-blue-50 text-theme-blue rounded-full text-[10px] font-bold">Line Chart</span>
            </div>

            <div class="relative h-64 w-full">
                @if(isset($chartKegiatan['labels']) && count($chartKegiatan['labels']) > 0)
                    <canvas id="attendanceTrendChart"></canvas>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 text-xs">
                        <span class="text-3xl mb-2">📈</span>
                        Belum ada data kegiatan dan presensi.
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Doughnut Chart: Distribusi Anggota Berdasarkan Kelas (1 Kolom) -->
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-theme-dark">Distribusi Anggota</h3>
                    <p class="text-[11px] text-gray-400">Komposisi berdasarkan tingkat kelas</p>
                </div>
                <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-bold">Doughnut</span>
            </div>

            <div class="relative h-64 w-full flex items-center justify-center">
                @if(isset($chartKelas['data']) && array_sum($chartKelas['data']) > 0)
                    <canvas id="classDistributionChart"></canvas>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-gray-400 text-xs">
                        <span class="text-3xl mb-2">👥</span>
                        Belum ada data anggota aktif.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. Line Chart: Tren Kehadiran ---
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

        // --- 2. Doughnut Chart: Distribusi Kelas ---
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
