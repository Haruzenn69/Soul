@extends('layouts.siswa')

@section('title', 'Rekap Absensi')

@section('content')
    @php
        $rk = $rekapSiswa ?? null;
        $hadir = $rk?->hadir ?? 0;
        $izin = $rk?->izin ?? 0;
        $sakit = $rk?->sakit ?? 0;
        $alpha = $rk?->alpha ?? 0;
        $total = $rk?->total ?? 0;
        $persentaseKehadiran = $rk?->persentaseKehadiran ?? 0;
    @endphp

    <div class="animate-fade-up">
        <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900">Rekap Absensi</h1>
        <p class="text-xs text-slate-400 mt-1 font-medium">Ringkasan kehadiranmu per bulan di {{ $ekskul->nama_ekskul ?? 'ekskul' }}</p>
    </div>

    @include('siswa.partials.pemilih-ekskul')

    <!-- Filter Bulan -->
    <form method="GET" action="{{ route('siswa.rekap') }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 flex flex-wrap items-center gap-3 animate-fade-up" style="animation-delay: .1s">
        @if($pendaftaran?->ekskul_id)
            <input type="hidden" name="ekskul" value="{{ $pendaftaran->ekskul_id }}">
        @endif
        <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
            <i class="fa-solid fa-calendar-days text-sky-500 text-xs"></i>
            Pilih Bulan
        </span>
        <select name="bulan" class="px-3 py-1.5 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
            @foreach($availableMonths as $option)
                @php
                    $optionLabel = \Carbon\Carbon::createFromFormat('Y-m', $option)->translatedFormat('F Y');
                @endphp
                <option value="{{ $option }}" {{ $bulan === $option ? 'selected' : '' }}>{{ $optionLabel }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-1.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 transition-all hover:-translate-y-0.5">Tampilkan Rekap</button>
        <span class="ml-auto text-xs font-semibold text-slate-400 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</span>
    </form>

    <!-- Hero Persentase Kehadiran -->
    <div data-tilt-card class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200 soul-timbul tilt-card animate-fade-up" style="animation-delay: .15s">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>
        <div class="relative grid lg:grid-cols-3 gap-6 items-center">
            <div class="lg:col-span-2">
                <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Tingkat Kehadiran</h2>
                <p class="text-xs text-white/85 mt-1.5 max-w-lg leading-relaxed">
                    Dari {{ $total }} kegiatan yang tercatat pada bulan ini, kamu hadir sebanyak {{ $hadir }} kali.
                </p>
                <div class="mt-5 h-3 rounded-full bg-white/20 overflow-hidden">
                    <div id="hero-bar" class="h-full rounded-full bg-white transition-all duration-1000" style="width: 0%"></div>
                </div>
                <p class="text-[10px] text-white/70 mt-2 font-semibold">Persentase kehadiran dibanding seluruh kegiatan yang tercatat</p>
            </div>
            <div class="lg:col-span-1 text-center lg:text-right">
                <p class="text-5xl md:text-6xl font-extrabold tracking-tight">{{ $persentaseKehadiran }}<span class="text-2xl md:text-3xl opacity-80">%</span></p>
                <p class="text-[10px] text-white/70 font-semibold uppercase tracking-wider mt-2">{{ $total }} kegiatan tercatat</p>
            </div>
        </div>
    </div>

    <!-- Statistik Presensi -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <!-- Card 1: Hadir -->
        <div class="attendance-stat attendance-stat--present relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 p-4 md:p-5 shadow-lg shadow-emerald-100/60 hover:-translate-y-1 transition-all duration-300 animate-fade-up" style="animation-delay: .2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Hadir</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-emerald-600 mt-1 md:mt-1.5">{{ $hadir }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-emerald-200">H</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-emerald-700 mt-0.5 md:mt-1">Kegiatan</p>
        </div>

        <!-- Card 2: Sakit -->
        <div class="attendance-stat attendance-stat--sick relative overflow-hidden rounded-3xl bg-gradient-to-br from-violet-50 to-white border border-violet-100 p-4 md:p-5 shadow-lg shadow-violet-100/60 hover:-translate-y-1 transition-all duration-300 animate-fade-up" style="animation-delay: .25s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Sakit</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-violet-600 mt-1 md:mt-1.5">{{ $sakit }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-violet-400 to-purple-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-violet-200">S</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-violet-700 mt-0.5 md:mt-1">Kegiatan</p>
        </div>

        <!-- Card 3: Izin -->
        <div class="attendance-stat attendance-stat--excused relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 p-4 md:p-5 shadow-lg shadow-amber-100/60 hover:-translate-y-1 transition-all duration-300 animate-fade-up" style="animation-delay: .3s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Izin</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-amber-600 mt-1 md:mt-1.5">{{ $izin }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-amber-200">I</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-amber-700 mt-0.5 md:mt-1">Kegiatan</p>
        </div>

        <!-- Card 4: Alpa -->
        <div class="attendance-stat attendance-stat--absent relative overflow-hidden rounded-3xl bg-gradient-to-br from-rose-50 to-white border border-rose-100 p-4 md:p-5 shadow-lg shadow-rose-100/60 hover:-translate-y-1 transition-all duration-300 animate-fade-up" style="animation-delay: .35s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Alpa</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-rose-600 mt-1 md:mt-1.5">{{ $alpha }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-rose-400 to-red-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-rose-200">A</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-rose-700 mt-0.5 md:mt-1">Kegiatan</p>
        </div>
    </div>

    <!-- Matriks Kehadiran -->
    @include('partials.rekap-matriks', [
        'rows' => $rekapSiswa ? collect([$rekapSiswa]) : collect(),
        'matriksTitle' => 'Matriks Kehadiran',
        'matriksSubtitle' => $rekapSiswa
            ? 'Rekap kehadiranmu &middot; '.$kegiatans->count().' kegiatan'
            : 'Belum ada rekap pada bulan ini',
        'emptyText' => 'Belum ada rekap kehadiranmu pada bulan ini.',
    ])
@endsection

@push('scripts')
<script>
    // Animasi bar persentase kehadiran
    document.addEventListener('DOMContentLoaded', function() {
        const bar = document.getElementById('hero-bar');
        if (bar) {
            requestAnimationFrame(() => {
                bar.style.width = '{{ $persentaseKehadiran }}%';
            });
        }

        // Tilt card interaktif
        const card = document.querySelector('[data-tilt-card]');
        if (!card) return;

        const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!canHover || reduceMotion) return;

        const MAX_TILT = 6;
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
                card.style.transform = 'perspective(1200px) rotateX(' + rotateX.toFixed(2) + 'deg) rotateY(' + rotateY.toFixed(2) + 'deg) scale(1.012)';
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
