@extends('layouts.kesiswaan')

@section('title', 'Data Ekskul')

@section('content')
@php
    $sort = $sort ?? 'nama_ekskul';
    $direction = $direction ?? 'asc';

    $sortLink = function (string $column) use ($sort, $direction) {
        $nextDirection = $sort === $column && $direction === 'asc' ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => null]);
    };

    $sortIcon = function (string $column) use ($sort, $direction) {
        if ($sort !== $column) {
            return 'M8 9l4-4 4 4M8 15l4 4 4-4';
        }
        return $direction === 'asc' ? 'M8 15l4 4 4-4' : 'M8 9l4-4 4 4';
    };

    $hasFilter = request()->filled(['q', 'status', 'rekrutmen']) || request()->filled('sort');
@endphp

<div class="space-y-5 animate-fade-up">

    {{-- HERO CARD BIRU --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                    Data Ekskul
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Kelola data ekstrakurikuler, lihat anggota, pelatih, dan pembinanya. Gunakan pencarian, sort kolom, dan filter untuk menemukan ekskul yang diinginkan.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <button onclick="document.getElementById('modal-kategori').showModal()"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/15 hover:bg-white/25 text-white font-bold text-xs rounded-2xl border border-white/25 shadow-sm backdrop-blur-sm hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Kelola Kategori
                </button>
                <button onclick="document.getElementById('modal-create').showModal()"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Ekskul
                </button>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $activeFilterCount = 0;
        if (request()->filled('kategori')) $activeFilterCount++;
        if (request()->filled('status')) $activeFilterCount++;
        if (request()->filled('rekrutmen')) $activeFilterCount++;
        $hasActiveSearch = request()->filled('q');
        $hasAnyFilterOrSearch = $activeFilterCount > 0 || $hasActiveSearch;
    @endphp

    {{-- SEARCH & FILTER FORM DENGAN POPOVER --}}
    <form method="GET" action="{{ route('kesiswaan.ekskuls.index') }}" x-data="{ openFilter: false }" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm relative space-y-3">
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if(request('direction'))
            <input type="hidden" name="direction" value="{{ request('direction') }}">
        @endif

        <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama ekskul atau pembina..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div class="flex items-center gap-2 relative">
                {{-- Tombol Buka Filter Popover --}}
                <button type="button" @click="openFilter = !openFilter"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold border transition shadow-sm"
                        :class="openFilter || {{ $activeFilterCount }} > 0 ? 'bg-sky-50 text-sky-700 border-sky-300 ring-2 ring-sky-100' : 'bg-slate-50 text-slate-700 border-slate-200/80 hover:bg-slate-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filter</span>
                    @if($activeFilterCount > 0)
                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-extrabold rounded-full bg-sky-500 text-white">
                            {{ $activeFilterCount }}
                        </span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="openFilter ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Tombol Cari --}}
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Cari
                </button>

                {{-- Tombol Reset --}}
                @if ($hasAnyFilterOrSearch || $hasFilter)
                    <a href="{{ route('kesiswaan.ekskuls.index') }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition"
                       title="Reset semua filter">
                        Reset
                    </a>
                @endif

                {{-- PANEL POPOVER FILTER --}}
                <div x-show="openFilter"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     @click.away="openFilter = false"
                     style="display: none;"
                     class="absolute right-0 top-full mt-2 w-[calc(100vw-2.5rem)] sm:w-[460px] bg-white rounded-3xl border border-sky-100 shadow-2xl p-5 z-50 space-y-4">

                    {{-- Header Popover --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-slate-900">Filter Ekstrakurikuler</h3>
                                <p class="text-[11px] text-slate-400">Pilih kategori atau status ekskul</p>
                            </div>
                        </div>
                        <button type="button" @click="openFilter = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Grid Kategori Filter --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        {{-- Kategori --}}
                        <div class="sm:col-span-2">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Kategori Ekskul</label>
                            <select name="kategori" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategoriList ?? \App\Support\AturanEkskul::KATEGORI_LIST as $kategori)
                                    <option value="{{ $kategori }}" {{ request('kategori') === $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Ekskul --}}
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Status Ekskul</label>
                            <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        {{-- Rekrutmen --}}
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Pendaftaran Anggota</label>
                            <select name="rekrutmen" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Status</option>
                                <option value="buka" {{ request('rekrutmen') === 'buka' ? 'selected' : '' }}>Buka Pendaftaran</option>
                                <option value="tutup" {{ request('rekrutmen') === 'tutup' ? 'selected' : '' }}>Tutup Pendaftaran</option>
                            </select>
                        </div>
                    </div>

                    {{-- Footer Popover --}}
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <a href="{{ route('kesiswaan.ekskuls.index', request()->only('q', 'sort', 'direction')) }}"
                           class="text-xs font-bold text-slate-400 hover:text-rose-600 transition">
                            Reset Opsi Filter
                        </a>
                        <button type="submit" @click="openFilter = false" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                            <span>Terapkan Filter</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTIVE FILTER CHIPS (TAGS) --}}
        @if ($activeFilterCount > 0)
            <div class="flex flex-wrap items-center gap-1.5 pt-2 border-t border-slate-100">
                <span class="text-[11px] font-bold text-slate-400 mr-1">Filter Aktif:</span>

                {{-- Kategori --}}
                @if (request()->filled('kategori'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                        Kategori: {{ request('kategori') }}
                        <a href="{{ route('kesiswaan.ekskuls.index', request()->except('kategori', 'page')) }}" class="text-sky-400 hover:text-rose-600 font-extrabold">&times;</a>
                    </span>
                @endif

                {{-- Status --}}
                @if (request()->filled('status'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                        Status: {{ ucfirst(request('status')) }}
                        <a href="{{ route('kesiswaan.ekskuls.index', request()->except('status', 'page')) }}" class="text-sky-400 hover:text-rose-600 font-extrabold">&times;</a>
                    </span>
                @endif

                {{-- Rekrutmen --}}
                @if (request()->filled('rekrutmen'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                        Pendaftaran: {{ request('rekrutmen') === 'buka' ? 'Buka' : 'Tutup' }}
                        <a href="{{ route('kesiswaan.ekskuls.index', request()->except('rekrutmen', 'page')) }}" class="text-sky-400 hover:text-rose-600 font-extrabold">&times;</a>
                    </span>
                @endif

                <a href="{{ route('kesiswaan.ekskuls.index', request()->only('q', 'sort', 'direction')) }}" class="text-[11px] font-bold text-rose-500 hover:underline ml-1">
                    Hapus Semua
                </a>
            </div>
        @endif
    </form>

    {{-- TABEL EKSKUL --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nama_ekskul') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Ekskul
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nama_ekskul') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">Pembina</th>
                        <th class="py-3.5 px-5">Pelatih</th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('rekrutmen') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Rekrutmen
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('rekrutmen') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @php
                        $kategoriTersedia = $kategoriList ?? \App\Support\AturanEkskul::KATEGORI_LIST;
                        $normalizeKat = function (?string $kat) use ($kategoriTersedia): string {
                            $k = trim((string) $kat);
                            foreach ($kategoriTersedia as $valid) {
                                if (strcasecmp($k, $valid) === 0) return $valid;
                            }
                            if (in_array(strtolower($k), ['kesenian'])) return 'Seni';
                            if (in_array(strtolower($k), ['wajib', 'ekskul wajib'])) return 'Wajib';
                            return !empty($k) ? ucwords(strtolower($k)) : 'Lainnya';
                        };
                        $ekskulPerKategori = $ekskuls->getCollection()->groupBy(fn ($item) => $normalizeKat($item->kategori));
                        if (request('kategori')) {
                            $kategoriDitampilkan = [$normalizeKat(request('kategori'))];
                        } else {
                            $kategoriDitampilkan = collect($kategoriTersedia)
                                ->merge($ekskulPerKategori->keys())
                                ->unique()
                                ->values()
                                ->all();
                        }
                    @endphp
                    @foreach ($kategoriDitampilkan as $kategori)
                        <tr class="bg-sky-50/70">
                            <td colspan="5" class="px-5 py-3 text-xs font-extrabold uppercase tracking-wide text-sky-800">{{ $kategori }}</td>
                        </tr>
                        @php
                            // Ekskul aktif (status = 1) tampil di atas, ekskul nonaktif (status = 0) pindah ke paling bawah di kategorinya
                            $ekskulList = $ekskulPerKategori->get($kategori, collect())
                                ->sortBy(fn ($item) => (int) !$item->status)
                                ->values();
                        @endphp
                        @forelse ($ekskulList as $ekskul)
                        <tr class="{{ $ekskul->status ? 'hover:bg-sky-50/30' : 'bg-slate-100/70 text-slate-400 hover:bg-slate-200/50' }} transition">
                            {{-- Ekskul --}}
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0 {{ !$ekskul->status ? 'grayscale opacity-60' : '' }}">
                                        @if ($ekskul->logo)
                                            <img src="{{ asset('storage/' . $ekskul->logo) }}" alt="{{ $ekskul->nama_ekskul }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full {{ $ekskul->status ? 'bg-sky-100 text-sky-700' : 'bg-slate-200 text-slate-500' }} font-extrabold flex items-center justify-center text-xs uppercase">
                                                {{ strtoupper(substr($ekskul->nama_ekskul, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div class="font-bold {{ $ekskul->status ? 'text-slate-900' : 'text-slate-500' }} leading-snug truncate">{{ $ekskul->nama_ekskul }}</div>
                                            @if (!$ekskul->status)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-500 shrink-0">Nonaktif</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Pembina --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if ($ekskul->pembina)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg {{ $ekskul->status ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-500' }} font-bold flex items-center justify-center text-[10px] shrink-0 uppercase">
                                            {{ strtoupper(substr($ekskul->pembina->nama, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold {{ $ekskul->status ? 'text-slate-700' : 'text-slate-400' }}">{{ $ekskul->pembina->nama }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            {{-- Pelatih --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if ($ekskul->pelatih)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg {{ $ekskul->status ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-500' }} font-bold flex items-center justify-center text-[10px] shrink-0 uppercase">
                                            {{ strtoupper(substr($ekskul->pelatih->nama, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold {{ $ekskul->status ? 'text-slate-700' : 'text-slate-400' }}">{{ $ekskul->pelatih->nama }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            {{-- Rekrutmen --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if ($ekskul->status)
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[11px] {{ $ekskul->is_open_recruitment ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $ekskul->is_open_recruitment ? '● Buka' : '○ Tutup' }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full font-semibold text-[11px] bg-slate-200/70 text-slate-400">
                                        -
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="flex gap-2 justify-end items-center">
                                    {{-- Lihat Detail --}}
                                    <a href="{{ route('kesiswaan.ekskuls.show', $ekskul) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 {{ $ekskul->status ? 'bg-sky-500 hover:bg-sky-600 text-white shadow-sky-200' : 'bg-slate-200 hover:bg-slate-300 text-slate-700' }} font-bold text-xs rounded-xl shadow-sm transition"
                                       title="Lihat Detail Ekskul">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </a>

                                    {{-- Edit --}}
                                    <button type="button"
                                            onclick='openEdit({{ json_encode(["id" => $ekskul->id, "nama_ekskul" => $ekskul->nama_ekskul, "kategori" => $ekskul->kategori, "pembina_id" => $ekskul->pembina_id, "deskripsi" => $ekskul->deskripsi, "jadwal" => $ekskul->jadwal, "status" => $ekskul->status ? 1 : 0]) }})'
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-4 text-center text-xs text-slate-400">Belum ada ekskul pada kategori {{ $kategori }}.</td>
                            </tr>
                        @endforelse
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-2 justify-between items-center">
            <p class="text-[11px] text-slate-400 font-semibold">
                Menampilkan {{ $ekskuls->firstItem() ?? 0 }}-{{ $ekskuls->lastItem() ?? 0 }} dari {{ $ekskuls->total() }} ekskul
            </p>
            {{ $ekskuls->links() }}
        </div>
    </div>

    {{-- MODAL CREATE --}}
    <dialog id="modal-create" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-md shadow-2xl border border-sky-100">
        <form method="POST" action="{{ route('kesiswaan.ekskuls.store') }}" class="p-6 md:p-8 space-y-4 bg-white">
            @csrf
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Tambah Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Buat ekstrakurikuler baru</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Nama Ekskul <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_ekskul" placeholder="Contoh: Paskibra, OSIS, Pramuka..." required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                <p class="text-[10px] text-slate-400 mt-1">Pembina dapat ditentukan di halaman <a href="{{ route('kesiswaan.pembina.index') }}" class="text-sky-600 font-semibold hover:underline">Data Pembina</a>.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Kategori <span class="text-rose-500">*</span></label>
                <select name="kategori" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih kategori...</option>
                    @foreach ($kategoriList ?? \App\Support\AturanEkskul::KATEGORI_LIST as $kategori)
                        <option value="{{ $kategori }}" @selected(old('kategori') === $kategori)>{{ $kategori }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="this.closest('dialog').close()"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                    Simpan Ekskul
                </button>
            </div>
        </form>
    </dialog>

    {{-- MODAL EDIT --}}
    <dialog id="modal-edit" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-md shadow-2xl border border-sky-100">
        <form id="form-edit" method="POST" class="p-6 md:p-8 space-y-4 bg-white">
            @csrf
            @method('PUT')
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Edit Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Perbarui data ekstrakurikuler</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Nama Ekskul <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_ekskul" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Status Ekskul <span class="text-rose-500">*</span></label>
                <select name="status" id="edit_status" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Ekskul nonaktif akan dipindah ke paling bawah di kategorinya dan berwarna abu-abu.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Kategori <span class="text-rose-500">*</span></label>
                {{-- Jika ekskul Wajib: tampilkan info locked (diisi via JS) --}}
                <div id="edit_kategori_locked" class="hidden w-full px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-800 font-bold flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span id="edit_kategori_locked_text">Wajib</span>
                    <span class="font-normal text-amber-600 ml-1">&mdash; Kategori ekskul wajib tidak dapat diubah</span>
                    <input type="hidden" name="kategori" id="edit_kategori_hidden" value="Wajib">
                </div>
                {{-- Untuk ekskul non-Wajib: dropdown biasa, tanpa opsi Wajib --}}
                <select name="kategori" id="edit_kategori" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="" disabled>Pilih kategori...</option>
                    @foreach ($kategoriList ?? \App\Support\AturanEkskul::KATEGORI_LIST as $kategori)
                        @if (!\App\Support\AturanEkskul::isWajib($kategori))
                            <option value="{{ $kategori }}">{{ $kategori }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Deskripsi</label>
                <textarea name="deskripsi" rows="2"
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Jadwal</label>
                <input type="text" name="jadwal"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div class="flex gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="this.closest('dialog').close()"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </dialog>

    {{-- MODAL KELOLA KATEGORI --}}
    <dialog id="modal-kategori" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-lg shadow-2xl border border-sky-100">
        <div class="p-6 md:p-8 bg-white space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Kelola Kategori Ekskul</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Tambah dan hapus kategori ekstrakurikuler</p>
                    </div>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- FORM TAMBAH KATEGORI --}}
            <form method="POST" action="{{ route('kesiswaan.kategori.store') }}" class="p-4 bg-sky-50/70 border border-sky-100 rounded-2xl space-y-3">
                @csrf
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-sky-900 uppercase tracking-wider">Tambah Kategori Baru</h3>
                </div>
                <div class="flex gap-2">
                    <input type="text" name="nama" required placeholder="Contoh: Robotik, Keagamaan, ..."
                           class="flex-1 px-3.5 py-2.5 bg-white border border-sky-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100">
                    <button type="submit"
                            class="px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm shadow-sky-200 transition shrink-0">
                        + Tambah
                    </button>
                </div>
            </form>

            {{-- DAFTAR KATEGORI SAAT INI --}}
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Daftar Kategori Saat Ini</h3>
                <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 border border-slate-100 rounded-2xl">
                    @forelse ($kategoriModels ?? [] as $kat)
                        <div class="p-3 flex items-center justify-between gap-3 hover:bg-slate-50 transition">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="font-bold text-xs text-slate-800 truncate">{{ $kat->nama }}</span>
                                @if ($kat->isFallback())
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200" title="Kategori fallback — ekskul yang kategorinya dihapus akan dipindah ke sini">
                                        Sistem · Fallback
                                    </span>
                                @endif
                                <span class="text-[10px] text-slate-400">
                                    ({{ $kat->ekskuls_count }} ekskul)
                                </span>
                            </div>
                            @if ($kat->isFallback())
                                <span class="text-[10px] text-slate-400 italic font-medium px-2 py-1">Tidak bisa dihapus</span>
                            @else
                                <form method="POST" action="{{ route('kesiswaan.kategori.destroy', $kat) }}"
                                      onsubmit="return confirm('Hapus kategori \'{{ $kat->nama }}\'? {{ $kat->ekskuls_count > 0 ? $kat->ekskuls_count . ' ekskul pada kategori ini akan otomatis dipindah ke kategori Lainnya.' : 'Tidak ada ekskul yang terdampak.' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400">Belum ada kategori terdaftar.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </dialog>

</div>

<script>
    function openEdit(data) {
        const form = document.getElementById('form-edit');
        form.action = '{{ url('kesiswaan/ekskuls') }}/' + data.id;
        form.querySelector('[name=nama_ekskul]').value = data.nama_ekskul || '';

        // Penanganan status ekskul
        const selStatus = document.getElementById('edit_status');
        if (selStatus) {
            selStatus.value = (data.status !== undefined && data.status !== null) ? String(data.status) : '1';
        }

        // Penanganan dropdown kategori
        const selKategori   = document.getElementById('edit_kategori');
        const lockedBox     = document.getElementById('edit_kategori_locked');
        const lockedText    = document.getElementById('edit_kategori_locked_text');
        const hiddenKategori= document.getElementById('edit_kategori_hidden');
        const isWajib       = (data.kategori || '').toLowerCase() === 'wajib';

        if (isWajib) {
            // Ekskul Wajib: sembunyikan dropdown, tampilkan info locked
            selKategori.classList.add('hidden');
            selKategori.removeAttribute('name');      // jangan ikut submit
            selKategori.removeAttribute('required');
            lockedBox.classList.remove('hidden');
            lockedText.textContent = data.kategori;
            hiddenKategori.value = data.kategori;     // yang dikirim ke server
        } else {
            // Ekskul non-Wajib: tampilkan dropdown, sembunyikan locked
            selKategori.classList.remove('hidden');
            selKategori.setAttribute('name', 'kategori');
            selKategori.setAttribute('required', 'required');
            lockedBox.classList.add('hidden');
            hiddenKategori.value = '';
            selKategori.value = data.kategori || '';
        }

        form.querySelector('[name=deskripsi]').value = data.deskripsi || '';
        form.querySelector('[name=jadwal]').value = data.jadwal || '';
        document.getElementById('modal-edit').showModal();
    }

    // Auto-open modal create jika ada validation error
    @if ($errors->any() && old('nama_ekskul'))
        document.getElementById('modal-create').showModal();
    @endif
</script>
@endsection
