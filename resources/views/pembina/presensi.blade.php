@extends('pembina.layout')
@section('title', 'Presensi Kegiatan')

@section('content')
@php
    $sort = $sort ?? 'tanggal_kegiatan';
    $direction = $direction ?? 'desc';
    $currentSortCombo = $sort . '_' . $direction;
    $hasActiveFilter = request()->filled('cari') || request()->filled('sort');
@endphp

@if(!isset($selectedEkskul) || !$selectedEkskul)
    {{-- STEP 1: KATALOG KARTU EKSKUL --}}
    <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-neutral-800 shadow-sm animate-fade-up">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-neutral-800">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-xs font-bold border border-sky-200/60 dark:border-sky-800/50 mb-2">
                    <i class="fa-solid fa-calendar-check text-xs"></i> Presensi Kegiatan
                </span>
                <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Pilih Ekstrakurikuler</h1>
                <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Pilih salah satu ekstrakurikuler untuk melihat riwayat presensi kegiatannya.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pembina.rekap') }}" class="px-4 py-2 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-200/50 dark:shadow-none transition">
                    <i class="fa-solid fa-chart-bar text-xs mr-1"></i> Rekap Absensi
                </a>
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

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 dark:text-slate-400">Total Kegiatan</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $card->totalKegiatan }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 dark:text-slate-400">Anggota Aktif</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $card->totalAnggota }}</span>
                            </div>
                            @if($card->kegiatanTerakhir)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 dark:text-slate-400">Kegiatan Terakhir</span>
                                    <span class="font-bold text-slate-600 dark:text-slate-300">{{ \Carbon\Carbon::parse($card->kegiatanTerakhir->tanggal_kegiatan)->translatedFormat('d M Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('pembina.presensi', ['ekskul' => $card->ekskul->id]) }}"
                       class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs">
                        <span>Lihat Presensi</span>
                        <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@else
    {{-- STEP 2: DETAIL PRESENSI EKSKUL TERPILIH --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 animate-fade-up">
        <div class="flex items-center gap-3">
            @if($ekskuls->count() > 1)
                <a href="{{ route('pembina.presensi') }}"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition shrink-0">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Pilih Ekskul Lain</span>
                </a>
            @endif
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 dark:text-white">Presensi: {{ $activeEkskul->nama_ekskul ?? 'Kegiatan' }}</h1>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Riwayat kehadiran anggota pada setiap kegiatan ekskul</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pembina.rekap', ['ekskul' => $selectedEkskul]) }}" class="px-4 py-2 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-200/50 dark:shadow-none transition">
                Lihat Rekap Absensi
            </a>
        </div>
    </div>

    <!-- FILTER 1-KLIK & PENCARIAN -->
    <div x-data="{ showFilter: {{ $hasActiveFilter ? 'true' : 'false' }} }"
         class="bg-white dark:bg-[#1a1a1a] p-4 rounded-2xl border border-slate-200/80 dark:border-neutral-800 shadow-sm animate-fade-up">
        <form method="GET" action="{{ route('pembina.presensi') }}" id="filter-presensi-pembina-form">
            <input type="hidden" name="ekskul" value="{{ $selectedEkskul }}">
            <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">
            <input type="hidden" name="direction" id="direction-input" value="{{ $direction }}">

            {{-- SEARCH + TOMBOL FILTER 1-KLIK --}}
            <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="search" name="cari" value="{{ request('cari') }}" placeholder="Cari materi kegiatan atau nama anggota..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-500 transition-all">
                </div>

                {{-- TOMBOL TOGGLE FILTER 1-KLIK --}}
                <button type="button" @click="showFilter = !showFilter"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border text-xs font-bold transition shadow-xs"
                    :class="showFilter ? 'bg-sky-50 dark:bg-sky-950/50 border-sky-300 dark:border-sky-700 text-sky-700 dark:text-sky-300' : 'bg-white dark:bg-neutral-800 border-slate-200 dark:border-neutral-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50'">
                    <i class="fa-solid fa-sliders text-xs"></i>
                    <span>Filter</span>
                    @if($hasActiveFilter)
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    @endif
                    <i class="fa-solid text-[10px] text-slate-400 transition-transform duration-200" :class="showFilter ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>

                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition shadow-xs">
                    Cari
                </button>

                @if($hasActiveFilter)
                    <a href="{{ route('pembina.presensi', ['ekskul' => $selectedEkskul]) }}" class="px-3.5 py-2.5 text-xs font-semibold text-slate-500 hover:text-rose-600 transition text-center">
                        Reset
                    </a>
                @endif
            </div>

            {{-- PANEL FILTER 1-KLIK (EXPANDABLE) --}}
            <div x-show="showFilter" x-cloak x-transition
                 class="mt-3 pt-3 border-t border-slate-100 dark:border-neutral-800 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Urutan Kegiatan</label>
                    <select id="sort-select" class="w-full px-3 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all">
                        <option value="tanggal_kegiatan_desc" {{ $currentSortCombo === 'tanggal_kegiatan_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                        <option value="tanggal_kegiatan_asc" {{ $currentSortCombo === 'tanggal_kegiatan_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                        <option value="materi_asc" {{ $currentSortCombo === 'materi_asc' ? 'selected' : '' }}>Materi (A-Z)</option>
                        <option value="materi_desc" {{ $currentSortCombo === 'materi_desc' ? 'selected' : '' }}>Materi (Z-A)</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- LIST KEGIATAN & PRESENSI RINGKAS & EFISIEN -->
    <div class="space-y-4 animate-fade-up" style="animation-delay: .2s">
        @if(count($kegiatans) > 0)
            <div class="space-y-3">
                @foreach($kegiatans as $kegiatan)
                    @php
                        $presensiList = $kegiatan->presensis;
                        $hadirCount = $presensiList->where('status', 'hadir')->count();
                        $izinCount = $presensiList->where('status', 'izin')->count();
                        $sakitCount = $presensiList->where('status', 'sakit')->count();
                        $alphaCount = $presensiList->where('status', 'alpha')->count();
                        $totalCount = $presensiList->count();
                        $hadirPct = $totalCount > 0 ? round(($hadirCount / $totalCount) * 100) : 0;
                    @endphp

                    <div x-data="{ openDetail: false }"
                         class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-slate-200/80 dark:border-neutral-800 shadow-sm overflow-hidden transition-all hover:border-slate-300 dark:hover:border-neutral-700">
                        {{-- SUMMARY BARIS KEGIATAN --}}
                        <div class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex flex-col items-center justify-center shrink-0 text-sky-600 dark:text-sky-400">
                                    <span class="text-[10px] font-extrabold uppercase leading-none">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('M') }}</span>
                                    <span class="text-xs font-black leading-none mt-0.5">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('d') }}</span>
                                </div>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white truncate">{{ $kegiatan->materi }}</h3>
                                        @if($kegiatan->ekskul)
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-neutral-700">
                                                {{ $kegiatan->ekskul->nama_ekskul }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->translatedFormat('l, d F Y') }}
                                        @if($kegiatan->waktu_mulai)
                                            · {{ substr($kegiatan->waktu_mulai, 0, 5) }} - {{ substr($kegiatan->waktu_selesai ?? '', 0, 5) }} WIB
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- STATISTIK KEHADIRAN RINGKAS & TOMBOL TOGGLE --}}
                            <div class="flex items-center gap-3 flex-wrap justify-between md:justify-end">
                                <div class="flex items-center gap-1.5 text-[11px]">
                                    <span class="px-2 py-0.5 rounded-md font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/50" title="Hadir">
                                        {{ $hadirCount }} Hadir
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md font-bold bg-sky-50 dark:bg-sky-950/50 text-sky-700 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/50" title="Izin">
                                        {{ $izinCount }} Izin
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/50" title="Sakit">
                                        {{ $sakitCount }} Sakit
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md font-bold bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/50" title="Alpha">
                                        {{ $alphaCount }} Alpha
                                    </span>
                                    <span class="text-[10px] font-extrabold text-slate-400 ml-1">
                                        ({{ $hadirPct }}%)
                                    </span>
                                </div>

                                <button type="button" @click="openDetail = !openDetail"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-300 transition">
                                    <span x-text="openDetail ? 'Tutup Detail' : 'Detail Presensi ({{ $totalCount }})'"></span>
                                    <i class="fa-solid text-[10px] transition-transform duration-200" :class="openDetail ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                </button>
                            </div>
                        </div>

                        {{-- DETAIL TABEL SISWA (TERTUTUP SECARA DEFAULT) --}}
                        <div x-show="openDetail" x-cloak x-transition
                             class="border-t border-slate-100 dark:border-neutral-800 bg-slate-50/50 dark:bg-neutral-900/40 p-4">
                            @if($totalCount > 0)
                                <div class="overflow-x-auto">
                                    <table class="card-table w-full text-xs">
                                        <thead class="text-[10px] uppercase font-bold text-slate-400 border-b border-slate-200/60 dark:border-neutral-800">
                                            <tr>
                                                <th class="text-left pb-2 font-semibold">Nama Siswa</th>
                                                <th class="text-left pb-2 font-semibold">Kelas</th>
                                                <th class="text-center pb-2 font-semibold">Status Presensi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-neutral-800/60">
                                            @foreach($presensiList as $presensi)
                                            <tr class="hover:bg-white dark:hover:bg-neutral-800/50 transition">
                                                <td class="py-2.5 font-bold text-slate-800 dark:text-slate-200">{{ $presensi->pendaftaran->siswa->nama ?? '-' }}</td>
                                                <td class="py-2.5 text-slate-500 dark:text-slate-400">{{ $presensi->pendaftaran->siswa->kelas->nama ?? '-' }}</td>
                                                <td class="py-2.5 text-center">
                                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold border
                                                        @if($presensi->status == 'hadir') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-800
                                                        @elseif($presensi->status == 'izin') bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800
                                                        @elseif($presensi->status == 'sakit') bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/60 dark:text-amber-400 dark:border-amber-800
                                                        @else bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-400 dark:border-rose-800 @endif">
                                                        {{ ucfirst($presensi->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-xs text-slate-400 text-center py-2">Belum ada presensi dicatat untuk kegiatan ini.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @include('partials.table-pagination', ['rows' => $kegiatans, 'label' => 'kegiatan'])
        @else
            <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-slate-200/80 dark:border-neutral-800 p-12 text-center text-slate-400">
                <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex items-center justify-center text-slate-400 mb-3">
                    <i class="fa-solid fa-calendar-xmark text-lg"></i>
                </div>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Tidak ada kegiatan yang ditemukan</p>
                <p class="text-[11px] text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter yang dipilih</p>
                @if($hasActiveFilter)
                    <div class="mt-4">
                        <a href="{{ route('pembina.presensi', ['ekskul' => $selectedEkskul]) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition">Reset Filter</a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sortSelect = document.getElementById('sort-select');
            var sortInput = document.getElementById('sort-input');
            var directionInput = document.getElementById('direction-input');
            var form = document.getElementById('filter-presensi-pembina-form');

            if (sortSelect && sortInput && directionInput && form) {
                sortSelect.addEventListener('change', function () {
                    var val = this.value.split('_');
                    var dir = val.pop();
                    var col = val.join('_');
                    sortInput.value = col;
                    directionInput.value = dir || 'desc';
                    form.submit();
                });
            }
        });
    </script>
@endif
@endsection
