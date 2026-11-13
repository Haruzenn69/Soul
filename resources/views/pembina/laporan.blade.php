@extends('pembina.layout')
@section('title', 'Cetak Laporan Ekskul')

@section('content')
    <div class="animate-fade-up space-y-6">

        @if(!$activeEkskul)
            {{-- STEP 1: KATALOG KARTU EKSKUL UNTUK CETAK LAPORAN --}}
            <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-neutral-800 shadow-sm animate-fade-up">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-neutral-800">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-xs font-bold border border-sky-200/60 dark:border-sky-800/50 mb-2">
                            <i class="fa-solid fa-file-invoice text-xs"></i> Laporan Kegiatan & Presensi
                        </span>
                        <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Pilih Ekstrakurikuler</h1>
                        <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Pilih salah satu ekstrakurikuler di bawah ini untuk melihat, mengunduh, dan mencetak laporan bulanan resmi.
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
                                    @if($card->menungguCount > 0)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/60">
                                            {{ $card->menungguCount }} Menunggu
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition mb-3">
                                    {{ $card->ekskul->nama_ekskul }}
                                </h3>

                                <div class="space-y-2 mb-4 bg-white dark:bg-neutral-800 p-3 rounded-xl border border-slate-200/60 dark:border-neutral-700/60">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500 dark:text-slate-400">Total Laporan:</span>
                                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $card->totalLaporan }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500 dark:text-slate-400">Disetujui:</span>
                                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $card->disetujuiCount }}</span>
                                    </div>
                                    @if($card->laporanTerbaru)
                                        <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 dark:border-neutral-700">
                                            <span class="text-slate-400 text-[11px]">Terbaru:</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-300 text-[11px]">{{ \Carbon\Carbon::createFromFormat('Y-m', $card->laporanTerbaru->bulan)->translatedFormat('M Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <a href="{{ route('pembina.laporan.index', ['ekskul' => $card->ekskul->id]) }}"
                               class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs">
                                <span>Buka Laporan</span>
                                <i class="fa-solid fa-arrow-right text-[11px]"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- STEP 2: DETAIL LAPORAN EKSKUL TERPILIH --}}
            {{-- HEADER BAR & EKSKUL SELECTOR --}}
            <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-5 md:p-6 border border-slate-200/80 dark:border-neutral-800 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @if($ekskuls->count() > 1)
                        <a href="{{ route('pembina.laporan.index') }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition shrink-0"
                           title="Kembali ke Pilihan Ekskul">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            <span>Pilih Ekskul Lain</span>
                        </a>
                    @endif
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-[11px] font-bold border border-sky-200/60 dark:border-sky-800/50 mb-1">
                            <i class="fa-solid fa-file-invoice text-xs"></i> Laporan Kegiatan & Presensi
                        </span>
                        <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Laporan: {{ $activeEkskul->nama_ekskul }}</h1>
                        <p class="text-xs text-slate-400 mt-0.5">Tinjau, unduh, dan cetak berkas PDF laporan bulanan resmi.</p>
                    </div>
                </div>
            </div>

            <!-- DAFTAR LAPORAN BULANAN PER EKSKUL -->
            <div class="bg-white dark:bg-[#1a1a1a] p-5 md:p-6 rounded-3xl border border-slate-200/80 dark:border-neutral-800 shadow-sm space-y-4">
                <div class="flex flex-wrap justify-between items-center gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Daftar Laporan Bulanan</h2>
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/50">
                            {{ $activeEkskul->nama_ekskul }}
                        </span>
                    </div>
                    <span class="text-xs font-medium text-slate-400">Total {{ $laporans->total() }} laporan</span>
                </div>

                {{-- SEARCH & FILTER BAR 1-KLIK --}}
                <div x-data="{ showFilter: {{ (request('status') || request('sort')) ? 'true' : 'false' }} }"
                     class="bg-slate-50 dark:bg-neutral-900/50 p-3.5 rounded-2xl border border-slate-200/80 dark:border-neutral-800">
                    <form method="GET" action="{{ route('pembina.laporan.index') }}" id="laporan-filter-form">
                        <input type="hidden" name="ekskul" value="{{ $activeEkskul->id }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="direction" value="{{ $direction }}">

                        <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
                            {{-- Search --}}
                            <div class="relative flex-1">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                </span>
                                <input type="text" name="cari" value="{{ request('cari') }}"
                                    placeholder="Cari bulan (misal: 2026-05)..."
                                    class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#1a1a1a] border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all">
                            </div>

                            {{-- TOMBOL FILTER 1-KLIK --}}
                            <button type="button" @click="showFilter = !showFilter"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border text-xs font-bold transition shadow-xs"
                                :class="showFilter ? 'bg-sky-50 dark:bg-sky-950/50 border-sky-300 dark:border-sky-700 text-sky-700 dark:text-sky-300' : 'bg-white dark:bg-neutral-800 border-slate-200 dark:border-neutral-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50'">
                                <i class="fa-solid fa-sliders text-xs"></i>
                                <span>Filter Status</span>
                                @if(request('status'))
                                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                @endif
                                <i class="fa-solid text-[10px] text-slate-400 transition-transform duration-200" :class="showFilter ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>

                            <button type="submit"
                                class="px-5 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition shadow-xs">
                                Cari
                            </button>

                            @if(request('cari') || request('status'))
                                <a href="{{ route('pembina.laporan.index', ['ekskul' => $activeEkskul->id]) }}"
                                    class="px-3.5 py-2 text-xs font-semibold text-slate-500 hover:text-rose-600 transition text-center">
                                    Reset
                                </a>
                            @endif
                        </div>

                        {{-- EXPANDABLE STATUS & SORT --}}
                        <div x-show="showFilter" x-cloak x-transition
                             class="mt-3 pt-3 border-t border-slate-200/60 dark:border-neutral-800 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Status Laporan</label>
                                <select name="status" onchange="this.form.submit()"
                                    class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none">
                                    <option value="" @selected(!request('status'))>Semua Status</option>
                                    <option value="menunggu"  @selected(request('status') === 'menunggu')>Menunggu Persetujuan</option>
                                    <option value="disetujui" @selected(request('status') === 'disetujui')>Disetujui</option>
                                    <option value="ditolak"   @selected(request('status') === 'ditolak')>Ditolak</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                @if($laporans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="card-table w-full text-xs">
                            <thead class="bg-slate-50 dark:bg-neutral-900/60 text-slate-400 uppercase text-[10px] font-bold">
                                <tr>
                                    <th class="text-left p-3 rounded-l-xl">No</th>
                                    @include('partials.th-sort', ['label' => 'Periode Bulan', 'key' => 'bulan', 'sort' => $sort, 'direction' => $direction, 'class' => 'text-left p-3 font-semibold text-slate-500'])
                                    <th class="text-left p-3 font-semibold text-slate-500">Ekskul</th>
                                    @include('partials.th-sort', ['label' => 'Status', 'key' => 'status', 'sort' => $sort, 'direction' => $direction, 'class' => 'text-left p-3 font-semibold text-slate-500'])
                                    <th class="text-left p-3 font-semibold text-slate-500 rounded-r-xl">Cetak / Unduh</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                                @foreach($laporans as $key => $laporan)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-neutral-800/40 transition">
                                    <td class="p-3 text-slate-400 font-semibold">{{ $laporans->firstItem() + $key }}</td>
                                    <td class="p-3 font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->translatedFormat('F Y') }}</td>
                                    <td class="p-3 font-medium text-slate-600 dark:text-slate-300">{{ $laporan->ekskul->nama_ekskul ?? '-' }}</td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold border
                                            @if($laporan->status == 'menunggu') bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800
                                            @elseif($laporan->status == 'disetujui') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-800
                                            @else bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/60 dark:text-rose-400 dark:border-rose-800 @endif">
                                            {{ ucfirst($laporan->status) }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('pembina.laporan.show', $laporan) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition">
                                                Tinjau
                                            </a>
                                            <a href="{{ route('pembina.laporan.download', $laporan) }}" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center gap-1.5">
                                                <i class="fa-solid fa-file-pdf text-[10px]"></i>
                                                <span>Cetak PDF</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($laporans->hasPages())
                        <div class="pt-2">
                            {{ $laporans->withQueryString()->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-10 text-slate-400">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center text-slate-400 mb-2">
                            <i class="fa-solid fa-file-circle-xmark text-lg"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            @if(request('cari') || request('status'))
                                Tidak ada laporan bulanan yang sesuai filter.
                            @else
                                Belum ada laporan bulanan untuk ekskul {{ $activeEkskul->nama_ekskul ?? '' }}.
                            @endif
                        </p>
                    </div>
                @endif
            </div>

            <!-- REKAP PRESENSI KEGIATAN PER EKSKUL -->
            <div class="bg-white dark:bg-[#1a1a1a] p-5 md:p-6 rounded-3xl border border-slate-200/80 dark:border-neutral-800 shadow-sm space-y-4">
                <div class="flex flex-wrap justify-between items-center gap-2">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Rekap Presensi Kegiatan Ekskul</h2>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300">
                            {{ $activeEkskul->nama_ekskul }}
                        </span>
                    </div>
                    <a href="{{ route('pembina.presensi', ['ekskul' => $activeEkskul->id]) }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 transition">
                        Lihat Selengkapnya &rarr;
                    </a>
                </div>

                @if($kegiatans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="card-table w-full text-xs">
                            <thead class="bg-slate-50 dark:bg-neutral-900/60 text-slate-400 text-[10px] uppercase font-bold">
                                <tr>
                                    <th class="text-left p-3 rounded-l-xl">Materi Kegiatan</th>
                                    <th class="text-left p-3">Tanggal</th>
                                    <th class="text-center p-3 text-emerald-600">Hadir</th>
                                    <th class="text-center p-3 text-amber-600">Sakit</th>
                                    <th class="text-center p-3 text-sky-600">Izin</th>
                                    <th class="text-center p-3 text-rose-600 rounded-r-xl">Alpha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                                @foreach($kegiatans as $kegiatan)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-neutral-800/40 transition">
                                    <td class="p-3 font-bold text-slate-800 dark:text-slate-200">{{ $kegiatan->materi }}</td>
                                    <td class="p-3 text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->translatedFormat('d M Y') }}</td>
                                    <td class="p-3 text-center font-bold text-emerald-600">{{ $kegiatan->hadir_count }}</td>
                                    <td class="p-3 text-center font-bold text-amber-600">{{ $kegiatan->sakit_count }}</td>
                                    <td class="p-3 text-center font-bold text-sky-600">{{ $kegiatan->izin_count }}</td>
                                    <td class="p-3 text-center font-bold text-rose-600">{{ $kegiatan->alpha_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400 text-xs">
                        Belum ada kegiatan yang tercatat untuk ekskul ini.
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
