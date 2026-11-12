@extends('ketua.layout')

@section('title', 'Dashboard Ketua' . ($ekskul ? ' ' . $ekskul->nama_ekskul : ''))

@section('content')
    {{-- HERO CARD --}}
    <div data-tilt-card class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-4 md:p-6 md:pb-12 lg:pb-14 text-white soul-timbul tilt-card animate-fade-up">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative grid grid-cols-5 gap-2 md:gap-4 lg:gap-6 items-center">
            {{-- TEXT + CTA --}}
            <div class="min-w-0 col-span-3">
                <h1 class="text-base sm:text-xl md:text-2xl font-extrabold tracking-tight leading-tight break-words">
                    Halo, {{ auth()->user()->siswa->nama ?? 'Ketua' }}!
                </h1>
                <p class="text-[11px] sm:text-sm text-white/85 mt-1 font-medium leading-snug">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </p>
                <p class="text-[10px] sm:text-xs text-white/70 mt-2 max-w-[15rem] sm:max-w-[22rem] leading-relaxed text-justify">
                    Kelola anggota, kegiatan, dan kehadiran ekskulmu di sini.
                </p>

                <div class="flex flex-row flex-wrap items-center gap-1.5 sm:gap-2 mt-3 sm:mt-4">
                    <a href="{{ route('ketua.kegiatan.create') }}" class="inline-flex max-w-full items-center justify-center gap-1 px-2 py-2 sm:gap-1.5 sm:px-4 bg-white text-sky-700 font-bold text-[10px] sm:text-xs rounded-xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                        <i class="fa-solid fa-plus text-[10px] sm:text-xs shrink-0"></i>
                        Buat Kegiatan Baru
                    </a>
                    <a href="{{ route('ketua.anggota.index') }}" class="inline-flex max-w-full items-center justify-center gap-1 px-2 py-2 sm:gap-1.5 sm:px-4 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-[10px] sm:text-xs rounded-xl hover:bg-white/20 transition-all">
                        <i class="fa-solid fa-users text-[10px] sm:text-xs shrink-0"></i>
                        Kelola Anggota
                    </a>
                </div>
            </div>

            {{-- ILLUSTRATIVE PANEL + FLOATING STATS (desktop) --}}
            <div class="col-span-2 relative min-w-0">
                <div class="relative h-32 sm:h-36 md:h-40 lg:h-44 rounded-2xl md:rounded-3xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center">
                    <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
                        <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-white/20 blur-2xl"></div>
                    </div>
                    <img src="{{ asset('images/default.png') }}" alt="Ilustrasi Ketua"
                         class="h-24 sm:h-28 md:h-40 lg:h-44 w-auto max-w-full md:max-w-[130px] lg:max-w-[150px] object-contain drop-shadow-xl animate-floaty select-none relative z-0 mb-8 md:mb-2 pointer-events-none">
                </div>

                {{-- floating stat card --}}
                <div class="absolute bottom-2 md:-bottom-7 left-1 right-1 sm:left-2 sm:right-2 z-10 grid grid-cols-3 gap-0.5 sm:gap-1 bg-white rounded-lg sm:rounded-xl shadow-xl shadow-sky-900/10 p-1 sm:p-2">
                    <div class="text-center border-r border-slate-100 pr-1">
                        <p class="text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $totalAnggota }}</p>
                        <p class="text-[9px] md:text-xs text-slate-400 font-semibold mt-1">Anggota</p>
                    </div>
                    <div class="text-center border-r border-slate-100 px-1">
                        <p class="text-sm md:text-base font-extrabold text-amber-600 leading-none">{{ $pendingCount }}</p>
                        <p class="text-[9px] md:text-xs text-slate-400 font-semibold mt-1">Pendaftaran</p>
                    </div>
                    <div class="text-center pl-1">
                        <p class="text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $kegiatanBulanIni ?? 0 }}</p>
                        <p class="text-[9px] md:text-xs text-slate-400 font-semibold mt-1">Kegiatan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="relative mt-4 pt-3 md:mt-11 lg:mt-12 border-t border-white/15 grid grid-cols-3 gap-1.5 sm:gap-2">
            <a href="{{ route('ketua.pendaftaran.index') }}" class="group flex min-w-0 items-center gap-1.5 sm:gap-2 px-2 sm:px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-clipboard-list text-sm text-white"></i>
                </div>
                <div class="min-w-0 text-left">
                    <p class="text-xs font-bold leading-none text-white">Pendaftaran</p>
                    <p class="text-[10px] sm:text-xs text-white/70 mt-1 leading-tight">Tinjau Masuk</p>
                </div>
            </a>
            <a href="{{ route('ketua.kegiatan.index') }}" class="group flex min-w-0 items-center gap-1.5 sm:gap-2 px-2 sm:px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-calendar text-sm text-white"></i>
                </div>
                <div class="min-w-0 text-left">
                    <p class="text-xs font-bold leading-none text-white">Kegiatan</p>
                    <p class="text-[10px] sm:text-xs text-white/70 mt-1 leading-tight">Agenda Ekskul</p>
                </div>
            </a>
            <a href="{{ route('ketua.laporan-bulanan.index') }}" class="group flex min-w-0 items-center gap-1.5 sm:gap-2 px-2 sm:px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-chart-simple text-sm text-white"></i>
                </div>
                <div class="min-w-0 text-left">
                    <p class="text-[10px] sm:text-xs font-bold leading-tight text-white">Laporan</p>
                    <p class="text-[10px] sm:text-xs text-white/70 mt-1 leading-tight">Bulanan</p>
                </div>
            </a>
        </div>
    </div>

    {{-- STATS CARDS - MOBILE (di desktop sudah di hero) --}}
    <div class="hidden grid-cols-3 gap-2.5 md:hidden">
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#1a1a1a] border border-sky-100 dark:border-neutral-800 p-3 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Anggota</p>
            <h3 class="text-lg font-extrabold mt-0.5 text-sky-700 dark:text-sky-400">{{ $totalAnggota }}</h3>
            <p class="text-[10px] font-semibold text-sky-600 dark:text-sky-400">Terdaftar</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#1a1a1a] border border-sky-100 dark:border-neutral-800 p-3 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Pendaftaran</p>
            <h3 class="text-lg font-extrabold mt-0.5 text-amber-600 dark:text-amber-400">{{ $pendingCount }}</h3>
            <p class="text-[10px] font-semibold text-amber-600 dark:text-amber-400">Menunggu</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#1a1a1a] border border-sky-100 dark:border-neutral-800 p-3 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Kegiatan</p>
            <h3 class="text-lg font-extrabold mt-0.5 text-sky-700 dark:text-sky-400">{{ $kegiatanBulanIni ?? 0 }}</h3>
            <p class="text-[10px] font-semibold text-sky-600 dark:text-sky-400">Bulan Ini</p>
        </div>
    </div>

    {{-- GRAFIK: TREN KEHADIRAN (full-width) --}}
    <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up">
        <div class="px-5 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
            <div>
                <h3 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white">Tren Kehadiran Pertemuan</h3>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Statistik kehadiran anggota pada 6 pertemuan terbaru</p>
            </div>
            <span class="text-xs font-semibold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/40 border border-sky-100 dark:border-sky-800 px-3 py-1 rounded-full">{{ $totalAnggota }} Anggota</span>
        </div>

        <div class="p-5">
            <div class="h-48 md:h-56 lg:h-64 relative w-full">
                <canvas id="attendanceTrendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- GRID: DISTRIBUSI KELAS + AKSI TERTUNDA --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-5">
        {{-- DISTRIBUSI ANGGOTA (bar chart) --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up">
            <div class="px-5 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
                <div>
                    <h3 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white">Distribusi Anggota</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Komposisi berdasarkan tingkat kelas</p>
                </div>
                <span class="text-xs font-semibold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/40 border border-sky-100 dark:border-sky-800 px-3 py-1 rounded-full">{{ $totalAnggota }} Total</span>
            </div>

            <div class="p-5">
                <div class="h-40 md:h-48 lg:h-56 relative w-full">
                    <canvas id="classDistributionChart"></canvas>
                </div>
            </div>
        </div>

        {{-- AKSI TERTUNDA --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up">
            <div class="px-5 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
                <div>
                    <h3 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white">Yang perlu kamu cek</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Aksi cepat untuk tugas yang menunggu</p>
                </div>
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-800 px-3 py-1 rounded-full">{{ $pendingCount + $pengajuanCount }} Tertunda</span>
            </div>

            <div class="p-4 md:p-5 space-y-3">
                <a href="{{ route('ketua.pendaftaran.index') }}" class="flex items-center justify-between gap-3 rounded-xl border border-sky-100 dark:border-neutral-800 bg-sky-50/50 dark:bg-neutral-900/40 px-4 py-3 hover:bg-sky-100/50 dark:hover:bg-neutral-800/60 transition">
                    <span class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-sky-100 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                            <i class="fa-solid fa-clipboard-list text-sm"></i>
                        </span>
                        <span>
                            <span class="block text-xs font-bold text-slate-700 dark:text-slate-200">Pendaftaran baru</span>
                            <span class="block text-[10px] text-slate-400">{{ $pendingCount }} menunggu tinjauan</span>
                        </span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-sky-400 text-xs"></i>
                </a>
                <a href="{{ route('ketua.pengajuan-keluar.index') }}" class="flex items-center justify-between gap-3 rounded-xl border border-sky-100 dark:border-neutral-800 bg-sky-50/50 dark:bg-neutral-900/40 px-4 py-3 hover:bg-sky-100/50 dark:hover:bg-neutral-800/60 transition">
                    <span class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                            <i class="fa-solid fa-right-from-bracket text-sm"></i>
                        </span>
                        <span>
                            <span class="block text-xs font-bold text-slate-700 dark:text-slate-200">Pengajuan keluar</span>
                            <span class="block text-[10px] text-slate-400">{{ $pengajuanCount }} menunggu keputusan</span>
                        </span>
                    </span>
                    <i class="fa-solid fa-chevron-right text-rose-400 text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- TESTIMONI & FAQ --}}
    @if($ekskul)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-5">
        {{-- TESTIMONI --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up">
            <div class="px-5 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
                <div>
                    <h3 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white">Testimoni</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Moderasi kontribusi siswa</p>
                </div>
                <span class="text-xs font-semibold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/40 border border-sky-100 dark:border-sky-800 px-3 py-1 rounded-full">{{ $testimoniPendingCount }} Pending</span>
            </div>

            <div class="p-4 md:p-5 space-y-3">
                <a href="{{ route('ketua.testimoni.index') }}" class="flex items-center justify-between gap-3 p-3 bg-sky-50/50 dark:bg-neutral-900/40 rounded-xl border border-sky-100 dark:border-neutral-800 hover:bg-sky-100/50 dark:hover:bg-neutral-800/60 transition">
                    <span>
                        <span class="block text-2xl font-extrabold text-sky-700 dark:text-sky-400 leading-none">{{ $testimoniPendingCount }}</span>
                        <span class="block text-[10px] text-slate-400 mt-1">{{ $testimoniPendingCount > 0 ? 'menunggu persetujuan' : 'tidak ada antrian' }}</span>
                    </span>
                    <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400">Kelola</span>
                </a>

                <form action="{{ route('ketua.testimoni.store') }}" method="POST" class="p-3 bg-sky-50/50 dark:bg-neutral-900/40 rounded-xl border border-sky-100 dark:border-neutral-800 space-y-2.5">
                    @csrf
                    <p class="text-[11px] font-bold text-slate-700 dark:text-slate-200">Tambah Testimoni</p>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" name="nama" required placeholder="Nama"
                            class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-sky-400 transition">
                        <input type="text" name="kelas" placeholder="Kelas (opsional)"
                            class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-sky-400 transition">
                    </div>
                    <textarea name="quote" rows="2" required placeholder="Isi testimoni..."
                        class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-sky-400 transition"></textarea>
                    <button type="submit" class="px-3.5 py-2 bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold rounded-xl transition shadow-sm">Simpan Testimoni</button>
                </form>
            </div>
        </div>

        {{-- FAQ --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up">
            <div class="px-5 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
                <div>
                    <h3 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white">FAQ</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Moderasi kontribusi siswa</p>
                </div>
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-800 px-3 py-1 rounded-full">{{ $faqPendingCount }} Pending</span>
            </div>

            <div class="p-4 md:p-5 space-y-3">
                <a href="{{ route('ketua.faq.index') }}" class="flex items-center justify-between gap-3 p-3 bg-amber-50/50 dark:bg-neutral-900/40 rounded-xl border border-amber-100 dark:border-neutral-800 hover:bg-amber-100/50 dark:hover:bg-neutral-800/60 transition">
                    <span>
                        <span class="block text-2xl font-extrabold text-amber-600 dark:text-amber-400 leading-none">{{ $faqPendingCount }}</span>
                        <span class="block text-[10px] text-slate-400 mt-1">{{ $faqPendingCount > 0 ? 'menunggu jawaban' : 'tidak ada antrian' }}</span>
                    </span>
                    <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Kelola</span>
                </a>

                <form action="{{ route('ketua.faq.store') }}" method="POST" class="p-3 bg-amber-50/50 dark:bg-neutral-900/40 rounded-xl border border-amber-100 dark:border-neutral-800 space-y-2.5">
                    @csrf
                    <p class="text-[11px] font-bold text-slate-700 dark:text-slate-200">Tambah FAQ</p>
                    <input type="text" name="pertanyaan" required placeholder="Pertanyaan umum..."
                        class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-amber-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-amber-400 transition">
                    <textarea name="jawaban" rows="2" required placeholder="Jawaban..."
                        class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-amber-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-amber-400 transition"></textarea>
                    <button type="submit" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl transition shadow-sm">Simpan FAQ</button>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') return;

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const anim = reduceMotion ? {} : { duration: 900, easing: 'easeOutQuart' };

        Chart.defaults.font.family = '"Plus Jakarta Sans", system-ui, sans-serif';
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#64748b';

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

        const ketuaCharts = [];
        const themeColors = getChartColors();

        // --- TREN KEHADIRAN (line chart, kesiswaan style dengan grid garis-garis) ---
        const attendanceCanvas = document.getElementById('attendanceTrendChart');
        if (attendanceCanvas) {
            let labels = @json($chartKegiatan['labels'] ?? []);
            let dataHadir = @json($chartKegiatan['hadir'] ?? []);
            let dataIzin = @json($chartKegiatan['izin'] ?? []);
            let dataSakit = @json($chartKegiatan['sakit'] ?? []);
            let dataAlpha = @json($chartKegiatan['alpha'] ?? []);

            // Fallback template jika belum ada agenda/presensi agar chart & grid tetap tampil cantik
            if (!labels || labels.length === 0) {
                labels = ['Pertemuan 1', 'Pertemuan 2', 'Pertemuan 3', 'Pertemuan 4', 'Pertemuan 5', 'Pertemuan 6'];
                dataHadir = [0, 0, 0, 0, 0, 0];
                dataIzin = [0, 0, 0, 0, 0, 0];
                dataSakit = [0, 0, 0, 0, 0, 0];
                dataAlpha = [0, 0, 0, 0, 0, 0];
            }

            const ctx = attendanceCanvas.getContext('2d');
            const attendanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Hadir',
                            data: dataHadir,
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
                        },
                        {
                            label: 'Izin',
                            data: dataIzin,
                            borderColor: '#F59E0B',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2,
                            pointBackgroundColor: '#F59E0B',
                            pointBorderColor: themeColors.pointBorderColor,
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            borderDash: [4, 4],
                        },
                        {
                            label: 'Sakit',
                            data: dataSakit,
                            borderColor: '#8B5CF6',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2,
                            pointBackgroundColor: '#8B5CF6',
                            pointBorderColor: themeColors.pointBorderColor,
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            borderDash: [2, 2],
                        },
                        {
                            label: 'Alpha',
                            data: dataAlpha,
                            borderColor: '#EF4444',
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            borderWidth: 2,
                            pointBackgroundColor: '#EF4444',
                            pointBorderColor: themeColors.pointBorderColor,
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: anim,
                    interaction: { mode: 'index', intersect: false },
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
                                color: themeColors.tickColor,
                                padding: 12
                            }
                        },
                        tooltip: {
                            backgroundColor: themeColors.tooltipBg,
                            titleFont: { weight: 700 },
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: true,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, stepSize: 1, font: { size: 10 }, color: themeColors.tickColor },
                            grid: { color: themeColors.gridColor, drawBorder: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, maxRotation: 0, autoSkip: true, color: themeColors.tickColor }
                        }
                    }
                }
            });
            ketuaCharts.push(attendanceChart);
        }

        // --- DISTRIBUSI KELAS (bar chart, kesiswaan style) ---
        const classCanvas = document.getElementById('classDistributionChart');
        if (classCanvas) {
            let classLabels = @json($chartKelas['labels'] ?? []);
            let classData = @json($chartKelas['data'] ?? []);

            if (!classLabels || classLabels.length === 0) {
                classLabels = ['Kelas 10', 'Kelas 11', 'Kelas 12'];
                classData = [0, 0, 0];
            }

            const ctxClass = classCanvas.getContext('2d');
            const classChart = new Chart(ctxClass, {
                type: 'bar',
                data: {
                    labels: classLabels,
                    datasets: [{
                        label: 'Anggota',
                        data: classData,
                        backgroundColor: '#0EA5E9',
                        hoverBackgroundColor: '#0284C7',
                        borderRadius: 6,
                        maxBarThickness: 24,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: anim,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: themeColors.tooltipBg,
                            titleFont: { weight: 700 },
                            padding: 10,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(ctx) { return ctx.parsed.y + ' siswa'; }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, stepSize: 1, font: { size: 10 }, color: themeColors.tickColor },
                            grid: { color: themeColors.gridColor, drawBorder: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, maxRotation: 0, autoSkip: true, color: themeColors.tickColor }
                        }
                    }
                }
            });
            ketuaCharts.push(classChart);
        }

        // --- Theme update handler ---
        function updateKetuaCharts() {
            const c = getChartColors();
            ketuaCharts.forEach(function(chart) {
                if (!chart) return;
                if (chart.options.scales?.y) {
                    if (chart.options.scales.y.grid) chart.options.scales.y.grid.color = c.gridColor;
                    if (chart.options.scales.y.ticks) chart.options.scales.y.ticks.color = c.tickColor;
                }
                if (chart.options.scales?.x) {
                    if (chart.options.scales.x.ticks) chart.options.scales.x.ticks.color = c.tickColor;
                }
                if (chart.options.plugins?.legend?.labels) {
                    chart.options.plugins.legend.labels.color = c.tickColor;
                }
                if (chart.options.plugins?.tooltip) {
                    chart.options.plugins.tooltip.backgroundColor = c.tooltipBg;
                }
                if (chart.config.type === 'line') {
                    chart.data.datasets.forEach(function(ds) {
                        ds.pointBorderColor = c.pointBorderColor;
                    });
                }
                chart.update('none');
            });
        }

        window.addEventListener('soul-theme-change', updateKetuaCharts);
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
@endpush
