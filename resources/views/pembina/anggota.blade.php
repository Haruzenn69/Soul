@extends('pembina.layout')
@section('title', 'Data Anggota & Ketua Ekskul')

@section('content')
<div class="space-y-6 animate-fade-up">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Data Anggota & Penunjukan Ketua</h1>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Kelola anggota aktif dan tentukan Ketua Ekskul untuk memimpin kegiatan ekskul binaan Anda.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap justify-end">
            @if($selectedEkskul && $ekskuls->firstWhere('id', $selectedEkskul))
                @php $activeEkskul = $ekskuls->firstWhere('id', $selectedEkskul); @endphp
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-neutral-800 border border-slate-200/80 dark:border-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                    <i class="fa-solid fa-circle-check text-sky-600 dark:text-sky-400 text-xs"></i>
                    <span>{{ $activeEkskul->nama_ekskul }}</span>
                </div>
            @endif
            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-neutral-800 border border-slate-200/80 dark:border-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                <i class="fa-solid fa-shapes text-slate-400 text-xs"></i>
                <span>Membina {{ $ekskuls->count() }} Ekskul</span>
            </div>
        </div>
    </div>

    {{-- SHOWCASE KETUA TIAP EKSKUL --}}
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Ketua Ekskul Binaan Saat Ini</h2>
            <span class="text-[11px] text-slate-400 dark:text-slate-500">Dipilih langsung dari siswa aktif</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($ekskuls as $eks)
                @php
                    $ketua = $eks->ketua();
                    $anggotaEkskulCount = \App\Models\Pendaftaran::where('ekskul_id', $eks->id)->where('status', 'diterima')->count();
                @endphp
                <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-4 border border-slate-200/80 dark:border-neutral-800 shadow-sm flex flex-col justify-between transition-all hover:border-slate-300 dark:hover:border-neutral-700">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-300 border border-slate-200/60 dark:border-neutral-700">
                                {{ $eks->nama_ekskul }}
                            </span>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold">
                                {{ $anggotaEkskulCount }} Anggota
                            </span>
                        </div>

                        @if($ketua)
                            <div class="flex items-center gap-3 my-2">
                                <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 overflow-hidden flex items-center justify-center shrink-0">
                                    @if($ketua->foto_url)
                                        <img src="{{ $ketua->foto_url }}" alt="{{ $ketua->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs font-extrabold text-slate-600 dark:text-slate-300 uppercase">
                                            {{ strtoupper(substr($ketua->nama, 0, 2)) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded bg-slate-900 dark:bg-white text-white dark:text-slate-900 inline-block">Ketua</span>
                                    <h4 class="text-xs font-extrabold text-slate-900 dark:text-white truncate mt-0.5" title="{{ $ketua->nama }}">{{ $ketua->nama }}</h4>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">NIS: {{ $ketua->nis }} · {{ $ketua->kelas->nama ?? '-' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="my-2 p-3 bg-slate-50 dark:bg-neutral-900/50 border border-dashed border-slate-200 dark:border-neutral-700 rounded-xl text-center">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-neutral-800 text-slate-400 flex items-center justify-center mx-auto mb-1.5">
                                    <i class="fa-solid fa-user-plus text-xs"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Belum Ada Ketua</span>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 leading-snug">
                                    Tentukan ketua untuk memimpin ekskul.
                                </p>
                            </div>
                            <div class="mt-2 pt-2 border-t border-slate-100 dark:border-neutral-800 flex items-center justify-center">
                                <a href="{{ route('pembina.ekskul.pilih-ketua-page', $eks) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 text-white font-bold text-[11px] transition">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Pilih Ketua</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    @if($ketua)
                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-neutral-800 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500">Jabatan Aktif</span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pembina.ekskul.pilih-ketua-page', $eks) }}"
                                   class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition"
                                   title="Ganti Ketua Ekskul">
                                    <i class="fa-solid fa-rotate text-[10px]"></i>
                                    <span>Ganti</span>
                                </a>
                                <form action="{{ route('pembina.ekskul.copot-ketua', ['ekskul' => $eks->id, 'siswa' => $ketua->id]) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin mencopot jabatan Ketua Ekskul {{ $eks->nama_ekskul }} dari {{ $ketua->nama }}? Siswa akan kembali berstatus anggota biasa.')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-400 hover:text-rose-600 transition">
                                        <i class="fa-solid fa-user-xmark text-[10px]"></i>
                                        <span>Copot</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- SEARCH & SORT & FILTER PANEL --}}
    <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-slate-200/80 dark:border-neutral-800 shadow-sm animate-fade-up overflow-hidden">
        <form method="GET" action="{{ route('pembina.anggota') }}" id="filter-form">

            {{-- TOP ROW: Search + Ekskul Dropdown + Sort --}}
            <div class="p-4 flex flex-col sm:flex-row gap-3 border-b border-slate-100 dark:border-neutral-800">
                {{-- Search --}}
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="cari" id="input-cari" value="{{ $cari }}"
                        placeholder="Cari nama, NIS, atau email..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-slate-400 transition-all">
                </div>

                {{-- Ekskul Dropdown --}}
                <div class="relative shrink-0">
                    <select name="ekskul" id="select-ekskul" onchange="this.form.submit()"
                        class="pl-3.5 pr-8 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-slate-400 transition-all appearance-none">
                        @if($ekskuls->count() > 1)
                            <option value="">Semua Ekskul</option>
                        @endif
                        @foreach($ekskuls as $e)
                            <option value="{{ $e->id }}" @selected($selectedEkskul == $e->id || $ekskuls->count() === 1)>
                                {{ $e->nama_ekskul }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </span>
                </div>

                {{-- Sort --}}
                <div class="relative shrink-0">
                    <select name="sort" id="select-sort" onchange="this.form.submit()"
                        class="pl-3.5 pr-8 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-slate-400 transition-all appearance-none">
                        <option value="nama_asc"            @selected($sort === 'nama_asc')>Nama A&ndash;Z</option>
                        <option value="nama_desc"           @selected($sort === 'nama_desc')>Nama Z&ndash;A</option>
                        <option value="tanggal_daftar_desc" @selected($sort === 'tanggal_daftar_desc')>Bergabung Terbaru</option>
                        <option value="tanggal_daftar_asc"  @selected($sort === 'tanggal_daftar_asc')>Bergabung Terlama</option>
                        <option value="kehadiran_desc"      @selected($sort === 'kehadiran_desc')>Kehadiran Tertinggi</option>
                        <option value="kehadiran_asc"       @selected($sort === 'kehadiran_asc')>Kehadiran Terendah</option>
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fa-solid fa-arrow-down-wide-short text-[10px]"></i>
                    </span>
                </div>

                {{-- Submit + Reset --}}
                <div class="flex gap-2 shrink-0">
                    <button type="submit"
                        class="px-4 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition">
                        Cari
                    </button>
                    @if($cari || $selectedEkskul || $selectedJurusan || $selectedJenisKelamin || $selectedTingkat || $selectedStatusKeaktifan || $statusKeanggotaan !== 'aktif' || $tampilArsip)
                        <a href="{{ route('pembina.anggota') }}"
                            class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-600 dark:text-slate-300 text-xs font-semibold rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            {{-- BOTTOM ROW: Advanced Filters --}}
            <div class="px-4 py-2.5 bg-slate-50/70 dark:bg-neutral-900/40 flex flex-wrap gap-2.5 items-center">
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider shrink-0">Filter:</span>

                {{-- Status Keanggotaan --}}
                <div class="relative">
                    <select name="status_keanggotaan" id="select-status-keanggotaan" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-lg text-[11px] text-slate-700 dark:text-slate-200 focus:outline-none focus:border-slate-400 transition-all appearance-none font-medium">
                        <option value="aktif"   @selected($statusKeanggotaan === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected($statusKeanggotaan === 'nonaktif')>Nonaktif / Keluar</option>
                        <option value="semua"   @selected($statusKeanggotaan === 'semua')>Semua Status</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[8px]"></i></span>
                </div>

                {{-- Status Keaktifan --}}
                <div class="relative">
                    <select name="status_keaktifan" id="select-status-keaktifan" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-lg text-[11px] text-slate-700 dark:text-slate-200 focus:outline-none focus:border-slate-400 transition-all appearance-none font-medium">
                        <option value=""             @selected(!$selectedStatusKeaktifan)>Semua Keaktifan</option>
                        <option value="sangat_aktif" @selected($selectedStatusKeaktifan === 'sangat_aktif')>Sangat Aktif (>=80%)</option>
                        <option value="cukup_aktif"  @selected($selectedStatusKeaktifan === 'cukup_aktif')>Cukup Aktif (>=50%)</option>
                        <option value="kurang_aktif" @selected($selectedStatusKeaktifan === 'kurang_aktif')>Kurang Aktif (&lt;50%)</option>
                        <option value="pasif"        @selected($selectedStatusKeaktifan === 'pasif')>Pasif (0%)</option>
                        <option value="peringatan"   @selected($selectedStatusKeaktifan === 'peringatan')>Peringatan</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[8px]"></i></span>
                </div>

                {{-- Tingkat Kelas --}}
                <div class="relative">
                    <select name="tingkat" id="select-tingkat" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-lg text-[11px] text-slate-700 dark:text-slate-200 focus:outline-none focus:border-slate-400 transition-all appearance-none font-medium">
                        <option value="">Semua Tingkat</option>
                        <option value="10" @selected($selectedTingkat == '10' || $selectedTingkat == 'x')>Kelas 10</option>
                        <option value="11" @selected($selectedTingkat == '11' || $selectedTingkat == 'xi')>Kelas 11</option>
                        <option value="12" @selected($selectedTingkat == '12' || $selectedTingkat == 'xii')>Kelas 12</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[8px]"></i></span>
                </div>

                {{-- Jurusan --}}
                <div class="relative">
                    <select name="jurusan" id="select-jurusan" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-lg text-[11px] text-slate-700 dark:text-slate-200 focus:outline-none focus:border-slate-400 transition-all appearance-none font-medium">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusans as $kode => $labels)
                            <option value="{{ $kode }}" @selected($selectedJurusan === $kode)>{{ strtoupper($kode) }}</option>
                        @endforeach
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[8px]"></i></span>
                </div>

                {{-- Tampilkan Arsip --}}
                <div class="relative">
                    <select name="arsip" id="select-arsip" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-lg text-[11px] text-slate-700 dark:text-slate-200 focus:outline-none focus:border-slate-400 transition-all appearance-none font-medium">
                        <option value="0" @selected(!$tampilArsip)>Tanpa Arsip</option>
                        <option value="1" @selected($tampilArsip)>Tampilkan Arsip ({{ $arsipTotal }})</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[8px]"></i></span>
                </div>

                {{-- Jenis Kelamin --}}
                <div class="relative">
                    <select name="jenis_kelamin" id="select-jenis-kelamin" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-lg text-[11px] text-slate-700 dark:text-slate-200 focus:outline-none focus:border-slate-400 transition-all appearance-none font-medium">
                        <option value=""          @selected(!$selectedJenisKelamin)>Semua Gender</option>
                        <option value="laki-laki" @selected($selectedJenisKelamin === 'laki-laki')>Laki-laki</option>
                        <option value="perempuan" @selected($selectedJenisKelamin === 'perempuan')>Perempuan</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><i class="fa-solid fa-chevron-down text-[8px]"></i></span>
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

</div>
@endsection
