@extends('pembina.layout')
@section('title', 'Rekap Absensi')

@section('content')
<div class="space-y-6 animate-fade-up">

    @if(isset($ekskulCards) && $ekskulCards)
        {{-- STEP 1: KATALOG KARTU EKSKUL UNTUK REKAP ABSENSI --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-neutral-800 shadow-sm animate-fade-up">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-neutral-800">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-xs font-bold border border-sky-200/60 dark:border-sky-800/50 mb-2">
                        <i class="fa-solid fa-chart-pie text-xs"></i> Rekapitulasi Presensi
                    </span>
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Pilih Ekstrakurikuler</h1>
                    <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Pilih salah satu ekstrakurikuler di bawah ini untuk melihat rekap kehadiran bulanan, matriks absensi, dan unduhan PDF.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 text-xs font-bold border border-slate-200/80 dark:border-neutral-700">
                        Total {{ $ekskuls->count() }} Ekskul
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($ekskulCards as $card)
                    <div class="bg-slate-50/70 dark:bg-neutral-900/60 rounded-2xl p-5 border border-slate-200/80 dark:border-neutral-800 flex flex-col justify-between hover:border-sky-300 dark:hover:border-sky-700 hover:shadow-md transition group">
                        <div>
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-sky-100 text-sky-800 dark:bg-sky-950/70 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60">
                                    {{ $card->ekskul->kategori ?? 'Umum' }}
                                </span>
                            </div>

                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition mb-3">
                                {{ $card->ekskul->nama_ekskul }}
                            </h3>

                            <div class="space-y-2 mb-4 bg-white dark:bg-neutral-800 p-3 rounded-xl border border-slate-200/60 dark:border-neutral-700/60">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 dark:text-slate-400">Total Anggota:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $card->totalAnggota }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 dark:text-slate-400">Total Pertemuan:</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $card->totalKegiatan }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('pembina.rekap', ['ekskul' => $card->ekskul->id]) }}"
                           class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs">
                            <span>Lihat Rekap</span>
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- STEP 2: DETAIL REKAP ABSENSI EKSKUL TERPILIH --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-5 md:p-6 border border-slate-200/80 dark:border-neutral-800 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                @if($ekskuls->count() > 1)
                    <a href="{{ route('pembina.rekap') }}"
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition shrink-0"
                       title="Kembali ke Pilihan Ekskul">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Pilih Ekskul Lain</span>
                    </a>
                @endif
                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-[11px] font-bold border border-sky-200/60 dark:border-sky-800/50 mb-1">
                        <i class="fa-solid fa-chart-pie text-xs"></i> Rekap Absensi
                    </span>
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Rekap: {{ $ekskul?->nama_ekskul ?? 'Semua Ekskul' }}
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">Rekapitulasi kehadiran anggota per bulan pada ekskul binaan Anda.</p>
                </div>
            </div>
        </div>

        {{-- Filter Bulan & Aksi Rekap --}}
        <form method="GET" action="{{ route('pembina.rekap') }}" class="bg-white dark:bg-[#1a1a1a] p-4 md:p-5 rounded-2xl border border-slate-200/80 dark:border-neutral-800 shadow-sm flex flex-wrap items-center gap-3">
            @if($ekskulId)
                <input type="hidden" name="ekskul" value="{{ $ekskulId }}">
            @endif

            <span class="text-xs font-bold text-slate-600 dark:text-slate-300 flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-days text-sky-500 text-xs"></i>
                <span>Periode Bulan:</span>
            </span>
            <div class="relative">
                <select name="bulan" onchange="this.form.submit()" class="pl-3 pr-8 py-1.5 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:outline-none focus:border-sky-500 appearance-none">
                    @foreach($availableMonths as $option)
                        @php
                            $optionLabel = \Carbon\Carbon::createFromFormat('Y-m', $option)->translatedFormat('F Y');
                        @endphp
                        <option value="{{ $option }}" {{ $bulan === $option ? 'selected' : '' }}>{{ $optionLabel }}</option>
                    @endforeach
                </select>
                <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]">
                    <i class="fa-solid fa-chevron-down"></i>
                </span>
            </div>

            @if($ekskul)
                <a href="{{ route('pembina.rekap.download', ['bulan' => $bulan, 'ekskul' => $ekskulId]) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                    <i class="fa-solid fa-file-pdf text-[11px]"></i>
                    <span>Download Rekap PDF</span>
                </a>
            @endif

            <span class="ml-auto text-[11px] font-semibold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 px-3 py-1.5 rounded-xl">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
            </span>
        </form>

        @include('partials.rekap-summary')
        @include('partials.rekap-matriks')
        @include('partials.rekap-event')
        @include('partials.rekap-pelatih')
    @endif

</div>
@endsection
