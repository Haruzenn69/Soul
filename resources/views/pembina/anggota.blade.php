@extends('pembina.layout')
@section('title', 'Data Anggota & Ketua Ekskul')

@section('content')
<div class="space-y-6 animate-fade-up">

    @if(!$selectedEkskul)
        {{-- STEP 1: KATALOG KARTU EKSKUL --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-neutral-800 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-neutral-800">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-xs font-bold border border-sky-200/60 dark:border-sky-800/50 mb-2">
                        <i class="fa-solid fa-users text-xs"></i> Data Keanggotaan
                    </span>
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Pilih Ekstrakurikuler</h1>
                    <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Pilih salah satu ekstrakurikuler di bawah ini untuk melihat dan mengelola data anggotanya.
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
                                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                    {{ $card->totalAnggota }} Anggota
                                </span>
                            </div>

                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition mb-2">
                                {{ $card->ekskul->nama_ekskul }}
                            </h3>

                            {{-- INFO KETUA --}}
                            <div class="p-3 bg-white dark:bg-neutral-800 rounded-xl border border-slate-200/60 dark:border-neutral-700/60 mb-4 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-neutral-700 flex items-center justify-center shrink-0 overflow-hidden">
                                    @if($card->ketua && $card->ketua->foto_url)
                                        <img src="{{ $card->ketua->foto_url }}" alt="{{ $card->ketua->nama }}" class="w-full h-full object-cover">
                                    @elseif($card->ketua)
                                        <span class="text-[11px] font-extrabold text-slate-700 dark:text-slate-200 uppercase">
                                            {{ strtoupper(substr($card->ketua->nama, 0, 2)) }}
                                        </span>
                                    @else
                                        <i class="fa-solid fa-crown text-slate-400 text-xs"></i>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 block">Ketua Ekskul:</span>
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">
                                        {{ $card->ketua?->nama ?? 'Belum Ada Ketua' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('pembina.anggota', ['ekskul' => $card->ekskul->id]) }}"
                           class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs">
                            <span>Kelola Anggota</span>
                            <i class="fa-solid fa-arrow-right text-[11px]"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- STEP 2: DETAIL DATA ANGGOTA EKSKUL TERPILIH --}}
        {{-- HEADER & EKSKUL SELECTOR --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-4 md:p-5 border border-slate-200/80 dark:border-neutral-800 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                @if($ekskuls->count() > 1)
                    <a href="{{ route('pembina.anggota') }}"
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition shrink-0"
                       title="Kembali ke Pilihan Ekskul">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Pilih Ekskul Lain</span>
                    </a>
                @endif
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-[11px] font-bold border border-sky-200/60 dark:border-sky-800/50">
                            <i class="fa-solid fa-users text-xs"></i> Data Keanggotaan
                        </span>
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">
                            Membina {{ $ekskuls->count() }} Ekskul
                        </span>
                    </div>
                    <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">Data Anggota: {{ $ekskuls->firstWhere('id', $selectedEkskul)?->nama_ekskul }}</h1>
                </div>
            </div>
        </div>

    {{-- CARD KETUA & RINGKASAN EKSKUL AKTIF (SATU KARTU, TIDAK BERLEBIHAN) --}}
    @php
        $activeEkskul = $selectedEkskul ? $ekskuls->firstWhere('id', $selectedEkskul) : $ekskuls->first();
        if (!$activeEkskul && $ekskuls->isNotEmpty()) {
            $activeEkskul = $ekskuls->first();
        }
        $activeKetua = $activeEkskul ? $activeEkskul->ketua() : null;
        $activeAnggotaCount = $activeEkskul
            ? \App\Models\Pendaftaran::where('ekskul_id', $activeEkskul->id)->where('status', 'diterima')->count()
            : 0;
    @endphp

    @if($activeEkskul)
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-4 md:p-5 border border-slate-200/80 dark:border-neutral-800 shadow-sm transition">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                {{-- INFO EKSKUL & KETUA --}}
                <div class="flex items-center gap-4 min-w-0">
                    <div class="w-14 h-14 min-w-[56px] min-h-[56px] max-w-[56px] max-h-[56px] rounded-2xl bg-slate-100 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 overflow-hidden flex items-center justify-center shrink-0 shadow-sm" style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
                        @if($activeKetua && $activeKetua->foto_url)
                            <img src="{{ $activeKetua->foto_url }}" alt="{{ $activeKetua->nama }}" class="w-full h-full object-cover" style="width: 56px; height: 56px; max-width: 56px; max-height: 56px; object-fit: cover;">
                        @elseif($activeKetua)
                            <span class="text-sm font-extrabold text-slate-700 dark:text-slate-200 uppercase">
                                {{ strtoupper(substr($activeKetua->nama, 0, 2)) }}
                            </span>
                        @else
                            <i class="fa-solid fa-crown text-slate-400 text-lg"></i>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/50">
                                {{ $activeEkskul->nama_ekskul }}
                            </span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-semibold">
                                {{ $activeAnggotaCount }} Anggota Diterima
                            </span>
                            @if($activeKetua)
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-300/40 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-crown text-[9px] text-amber-500"></i> Ketua Terpilih
                                </span>
                            @endif
                        </div>

                        @if($activeKetua)
                            <h3 class="text-sm md:text-base font-extrabold text-slate-900 dark:text-white truncate" title="{{ $activeKetua->nama }}">
                                {{ $activeKetua->nama }}
                            </h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                NIS: <span class="font-medium text-slate-600 dark:text-slate-300">{{ $activeKetua->nis }}</span> · Kelas: <span class="font-medium text-slate-600 dark:text-slate-300">{{ $activeKetua->kelas->nama ?? '-' }}</span>
                            </p>
                        @else
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                Belum Ada Ketua Terpilih
                            </h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                Tentukan Ketua Ekskul untuk memimpin kegiatan {{ $activeEkskul->nama_ekskul }}.
                            </p>
                        @endif
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="flex items-center gap-2 shrink-0 md:self-center border-t md:border-t-0 pt-3 md:pt-0 border-slate-100 dark:border-neutral-800 flex-wrap">
                    @if($activeKetua)
                        <a href="{{ route('pembina.ekskul.pilih-ketua-page', $activeEkskul) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition"
                           title="Ganti Ketua Ekskul">
                            <i class="fa-solid fa-rotate text-xs"></i>
                            <span>Ganti Ketua</span>
                        </a>
                        <form action="{{ route('pembina.ekskul.copot-ketua', ['ekskul' => $activeEkskul->id, 'siswa' => $activeKetua->id]) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin mencopot jabatan Ketua Ekskul {{ $activeEkskul->nama_ekskul }} dari {{ $activeKetua->nama }}? Siswa akan kembali berstatus anggota biasa.')">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 dark:hover:bg-rose-950/70 text-rose-600 dark:text-rose-400 font-bold text-xs transition"
                                    title="Copot Ketua">
                                <i class="fa-solid fa-user-xmark text-xs"></i>
                                <span>Copot</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('pembina.ekskul.pilih-ketua-page', $activeEkskul) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 text-white font-bold text-xs transition shadow-sm">
                            <i class="fa-solid fa-crown text-amber-400 text-xs"></i>
                            <span>Pilih Ketua Ekskul</span>
                        </a>
                    @endif
                    <a href="{{ route('pembina.pengurus.index', ['ekskul' => $activeEkskul->id]) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/50 dark:hover:bg-sky-900/50 text-sky-700 dark:text-sky-300 font-bold text-xs transition border border-sky-200/60 dark:border-sky-800/60"
                       title="Kelola Struktur Pengurus Lengkap">
                        <i class="fa-solid fa-users-gear text-xs"></i>
                        <span>Kelola Pengurus &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    @php
        $hasActiveFilter = (bool) ($cari || ($selectedEkskul && $ekskuls->count() > 1 && $selectedEkskul !== $ekskuls->first()?->id) || $selectedJurusan || $selectedJenisKelamin || $selectedTingkat || $selectedStatusKeaktifan || $statusKeanggotaan !== 'aktif' || $tampilArsip || ($sort && $sort !== 'nama_asc'));
        $activeFilterCount = collect([
            $selectedJurusan ? 1 : 0,
            $selectedJenisKelamin ? 1 : 0,
            $selectedTingkat ? 1 : 0,
            $selectedStatusKeaktifan ? 1 : 0,
            $statusKeanggotaan !== 'aktif' ? 1 : 0,
            $tampilArsip ? 1 : 0,
        ])->sum();
    @endphp

    {{-- SEARCH & FILTER PANEL 1-KLIK --}}
    <div x-data="{ showFilter: {{ $hasActiveFilter ? 'true' : 'false' }} }"
         class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-slate-200/80 dark:border-neutral-800 shadow-sm animate-fade-up overflow-hidden p-4">
        <form method="GET" action="{{ route('pembina.anggota') }}" id="filter-form">
            <input type="hidden" name="ekskul" value="{{ $selectedEkskul }}">
            {{-- ROW UTAMA: SEARCH + TOMBOL FILTER 1-KLIK --}}
            <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
                {{-- Search --}}
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="cari" id="input-cari" value="{{ $cari }}"
                        placeholder="Cari nama, NIS, atau email..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-500 transition-all">
                </div>

                {{-- TOMBOL FILTER 1-KLIK --}}
                <button type="button" @click="showFilter = !showFilter"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border text-xs font-bold transition shadow-xs"
                    :class="showFilter ? 'bg-sky-50 dark:bg-sky-950/50 border-sky-300 dark:border-sky-700 text-sky-700 dark:text-sky-300' : 'bg-white dark:bg-neutral-800 border-slate-200 dark:border-neutral-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50'">
                    <i class="fa-solid fa-sliders text-xs"></i>
                    <span>Filter</span>
                    @if($activeFilterCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full bg-sky-500 text-white text-[10px] font-extrabold">{{ $activeFilterCount }}</span>
                    @endif
                    <i class="fa-solid text-[10px] text-slate-400 transition-transform duration-200" :class="showFilter ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>

                {{-- Submit + Reset --}}
                <button type="submit"
                    class="px-5 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition shadow-xs">
                    Cari
                </button>
                @if($hasActiveFilter)
                    <a href="{{ route('pembina.anggota', ['ekskul' => $selectedEkskul]) }}"
                        class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-xl transition text-center">
                        Reset
                    </a>
                @endif
            </div>

            {{-- PANEL SEMUA FILTER (EXPANDABLE 1-KLIK) --}}
            <div x-show="showFilter" x-cloak x-transition
                 class="mt-3 pt-3 border-t border-slate-100 dark:border-neutral-800 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs">
                {{-- Status Keanggotaan --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Status Keanggotaan</label>
                    <div class="relative">
                        <select name="status_keanggotaan" id="select-status-keanggotaan" onchange="this.form.submit()"
                            class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none font-medium">
                            <option value="aktif"   @selected($statusKeanggotaan === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected($statusKeanggotaan === 'nonaktif')>Nonaktif / Keluar</option>
                            <option value="semua"   @selected($statusKeanggotaan === 'semua')>Semua Status</option>
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                    </div>
                </div>

                {{-- Status Keaktifan --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Keaktifan Presensi</label>
                    <div class="relative">
                        <select name="status_keaktifan" id="select-status-keaktifan" onchange="this.form.submit()"
                            class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none font-medium">
                            <option value=""             @selected(!$selectedStatusKeaktifan)>Semua Keaktifan</option>
                            <option value="sangat_aktif" @selected($selectedStatusKeaktifan === 'sangat_aktif')>Sangat Aktif (>=80%)</option>
                            <option value="cukup_aktif"  @selected($selectedStatusKeaktifan === 'cukup_aktif')>Cukup Aktif (>=50%)</option>
                            <option value="kurang_aktif" @selected($selectedStatusKeaktifan === 'kurang_aktif')>Kurang Aktif (&lt;50%)</option>
                            <option value="pasif"        @selected($selectedStatusKeaktifan === 'pasif')>Pasif (0%)</option>
                            <option value="peringatan"   @selected($selectedStatusKeaktifan === 'peringatan')>Peringatan</option>
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                    </div>
                </div>

                {{-- Tingkat Kelas --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Tingkat Kelas</label>
                    <div class="relative">
                        <select name="tingkat" id="select-tingkat" onchange="this.form.submit()"
                            class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none font-medium">
                            <option value="">Semua Tingkat</option>
                            <option value="10" @selected($selectedTingkat == '10' || $selectedTingkat == 'x')>Kelas 10</option>
                            <option value="11" @selected($selectedTingkat == '11' || $selectedTingkat == 'xi')>Kelas 11</option>
                            <option value="12" @selected($selectedTingkat == '12' || $selectedTingkat == 'xii')>Kelas 12</option>
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                    </div>
                </div>

                {{-- Jurusan --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Jurusan</label>
                    <div class="relative">
                        <select name="jurusan" id="select-jurusan" onchange="this.form.submit()"
                            class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none font-medium">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusans as $kode => $labels)
                                <option value="{{ $kode }}" @selected($selectedJurusan === $kode)>{{ strtoupper($kode) }}</option>
                            @endforeach
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                    </div>
                </div>

                {{-- Jenis Kelamin --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Gender</label>
                    <div class="relative">
                        <select name="jenis_kelamin" id="select-jenis-kelamin" onchange="this.form.submit()"
                            class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none font-medium">
                            <option value=""          @selected(!$selectedJenisKelamin)>Semua Gender</option>
                            <option value="laki-laki" @selected($selectedJenisKelamin === 'laki-laki')>Laki-laki</option>
                            <option value="perempuan" @selected($selectedJenisKelamin === 'perempuan')>Perempuan</option>
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                    </div>
                </div>

                {{-- Sort --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Urutan</label>
                    <div class="relative">
                        <select name="sort" id="select-sort" onchange="this.form.submit()"
                            class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none font-medium">
                            <option value="nama_asc"            @selected($sort === 'nama_asc')>Nama A&ndash;Z</option>
                            <option value="nama_desc"           @selected($sort === 'nama_desc')>Nama Z&ndash;A</option>
                            <option value="tanggal_daftar_desc" @selected($sort === 'tanggal_daftar_desc')>Bergabung Terbaru</option>
                            <option value="tanggal_daftar_asc"  @selected($sort === 'tanggal_daftar_asc')>Bergabung Terlama</option>
                            <option value="kehadiran_desc"      @selected($sort === 'kehadiran_desc')>Kehadiran Tertinggi</option>
                            <option value="kehadiran_asc"       @selected($sort === 'kehadiran_asc')>Kehadiran Terendah</option>
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                    </div>
                </div>

                {{-- Tampilkan Arsip --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Arsip</label>
                    <div class="relative">
                        <select name="arsip" id="select-arsip" onchange="this.form.submit()"
                            class="w-full pl-3 pr-8 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none font-medium">
                            <option value="0" @selected(!$tampilArsip)>Tanpa Arsip</option>
                            <option value="1" @selected($tampilArsip)>Tampilkan Arsip ({{ $arsipTotal }})</option>
                        </select>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[10px]"></i></span>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABEL ANGGOTA --}}
    <div id="tabel-anggota" class="bg-white dark:bg-[#1a1a1a] p-5 md:p-6 rounded-2xl border border-slate-200/80 dark:border-neutral-800 shadow-sm animate-fade-up">
        <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">
                    Daftar Anggota
                    @if($selectedEkskul && $ekskuls->firstWhere('id', $selectedEkskul))
                        <span class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-neutral-700">{{ $ekskuls->firstWhere('id', $selectedEkskul)->nama_ekskul }}</span>
                    @else
                        <span class="ml-2 text-xs font-semibold text-slate-400 dark:text-neutral-500">Semua Ekskul</span>
                    @endif
                </h3>
                <p class="text-xs text-slate-400 dark:text-neutral-500 mt-0.5">Siswa biasa dapat diangkat menjadi Ketua Ekskul untuk membantu presensi dan kegiatan.</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-400 rounded-xl">Total {{ count($anggota) }} Anggota</span>
        </div>

        @if(count($anggota) > 0)
            <div class="overflow-x-auto">
                <table class="card-table w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-neutral-800/80 text-slate-500 dark:text-neutral-400 border-b border-slate-100 dark:border-neutral-800">
                            <th class="p-3 font-semibold rounded-l-xl text-left">No</th>
                            <th class="p-3 font-semibold text-left">Identitas Siswa</th>
                            <th class="p-3 font-semibold text-left">Kelas</th>
                            @if(!$selectedEkskul && $ekskuls->count() > 1)<th class="p-3 font-semibold text-left">Ekskul</th>@endif
                            <th class="p-3 font-semibold text-left">Jabatan</th>
                            <th class="p-3 font-semibold text-left">Kehadiran</th>
                            <th class="p-3 font-semibold text-center rounded-r-xl">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                        @foreach($anggota as $key => $item)
                        @php
                            $isKetua = $item->siswa?->jabatan === 'ketua';
                            $currentKetuaEkskul = $item->ekskul?->ketua();
                            $isKetuaInThisEkskul = $isKetua && ($currentKetuaEkskul?->id === $item->siswa_id);
                            $pct = $item->persentase_kehadiran ?? 0;
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-neutral-800/40 transition">
                            <td class="p-3 text-slate-400 dark:text-neutral-500 font-medium">{{ $key + 1 }}</td>
                            <td class="p-3">
                                <a href="{{ route('pembina.anggota.show', $item) }}" class="flex items-center gap-2.5 group">
                                    <div class="w-8 h-8 rounded-xl overflow-hidden shrink-0 flex items-center justify-center font-bold text-xs bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-400 border border-slate-200/80 dark:border-neutral-700">
                                        @if($item->siswa?->foto_url)
                                            <img src="{{ $item->siswa->foto_url }}" alt="{{ $item->siswa->nama }}" class="w-full h-full object-cover">
                                        @else
                                            <span>{{ strtoupper(substr($item->siswa->nama ?? 'S', 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-900 dark:text-neutral-100 group-hover:text-sky-600 dark:group-hover:text-sky-400 block transition">{{ $item->siswa->nama ?? '-' }}</span>
                                        <span class="text-[10px] text-slate-400 dark:text-neutral-500">NIS: {{ $item->siswa->nis ?? '-' }}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="p-3 font-medium text-slate-700 dark:text-neutral-300">{{ $item->siswa->kelas->nama ?? '-' }}</td>
                            @if(!$selectedEkskul && $ekskuls->count() > 1)
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300 font-medium text-[11px] border border-slate-200/60 dark:border-neutral-700">
                                        {{ $item->ekskul->nama_ekskul ?? '-' }}
                                    </span>
                                </td>
                            @endif
                            <td class="p-3">
                                @if($isKetuaInThisEkskul)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-900 dark:bg-white text-white dark:text-slate-900">
                                        <i class="fa-solid fa-crown text-[9px]"></i> Ketua
                                    </span>
                                @elseif($isKetua)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-400">
                                        Ketua Lain
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-neutral-400">
                                        Anggota
                                    </span>
                                @endif
                            </td>
                            {{-- Kehadiran % --}}
                            <td class="p-3">
                                <div class="flex items-center gap-2 min-w-[75px]">
                                    <div class="flex-1 bg-slate-100 dark:bg-neutral-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full rounded-full bg-sky-500 dark:bg-sky-400 transition-all" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 shrink-0">{{ $pct }}%</span>
                                </div>
                            </td>
                            {{-- Status Keanggotaan --}}
                            <td class="p-3 text-center">
                                @if($item->status === 'diterima')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/50">Aktif</span>
                                @elseif($item->status === 'peringatan')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/50">Peringatan</span>
                                @elseif($item->status === 'nonaktif')
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-neutral-400 border border-slate-200 dark:border-neutral-700">Nonaktif</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-800/50">Keluar</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-slate-400">
                <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-neutral-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
                <p class="text-xs font-semibold">Tidak ada anggota yang sesuai dengan filter yang dipilih.</p>
                <a href="{{ route('pembina.anggota') }}" class="text-[11px] text-sky-600 hover:underline mt-1 inline-block">Reset semua filter</a>
            </div>
        @endif
    </div>

    {{-- ARSIP: hanya muncul saat filter "Tampilkan Arsip" aktif --}}
    @if($tampilArsip)
        @include('partials.anggota-arsip', [
            'arsip' => $arsip,
            'tampilArsip' => $tampilArsip,
            'arsipAction' => route('pembina.anggota'),
            'arsipShowEkskul' => $ekskuls->count() > 1,
            'arsipPeriodeRoute' => 'pembina.arsip.periode-ketua',
        ])
    @endif
    @endif

</div>
@endsection
