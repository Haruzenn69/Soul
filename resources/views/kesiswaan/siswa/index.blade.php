@extends('layouts.kesiswaan')

@section('title', 'Data Siswa')

@section('content')
@php
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

    $hasFilter = request()->filled(['q', 'kelas_id', 'angkatan', 'jenis_kelamin', 'jabatan', 'status']);
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
                    Data Siswa
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Daftar siswa aktif dan arsip alumni. Gunakan filter status, kelas, dan angkatan untuk menemukan data.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.siswa.riwayat-profil-all') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-white/15 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-2xl hover:bg-white/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Riwayat Perubahan Profil
                </a>
                <a href="{{ route('kesiswaan.users.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Siswa
                </a>
            </div>
        </div>
    </div>

    @php
        $activeFilterCount = 0;
        if (request('status') && request('status') !== 'aktif') $activeFilterCount++;
        if (request()->filled('kelas_id')) $activeFilterCount++;
        if (request()->filled('angkatan')) $activeFilterCount++;
        if (request()->filled('jenis_kelamin')) $activeFilterCount++;
        if (request()->filled('jabatan')) $activeFilterCount++;
        $hasActiveSearch = request()->filled('q');
        $hasAnyFilterOrSearch = $activeFilterCount > 0 || $hasActiveSearch;
    @endphp

    {{-- SEARCH & FILTER FORM DENGAN POPOVER --}}
    <form method="GET" action="{{ route('kesiswaan.siswa.index') }}" x-data="{ openFilter: false }" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm relative space-y-3">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">

        <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIS, atau email siswa..."
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

                {{-- Tombol Submit Cari --}}
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Cari
                </button>

                {{-- Reset Keseluruhan --}}
                @if ($hasAnyFilterOrSearch || $sort !== 'nama' || $direction !== 'asc')
                    <a href="{{ route('kesiswaan.siswa.index') }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition"
                       title="Reset semua filter dan pencarian">
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
                     class="absolute right-0 top-full mt-2 w-[calc(100vw-2.5rem)] sm:w-[480px] md:w-[520px] bg-white rounded-3xl border border-sky-100 shadow-2xl p-5 z-50 space-y-4">

                    {{-- Header Popover --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-slate-900">Filter Data Siswa</h3>
                                <p class="text-[11px] text-slate-400">Pilih kriteria untuk menyaring data</p>
                            </div>
                        </div>
                        <button type="button" @click="openFilter = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Grid Kategori Filter --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        {{-- Kategori 1: Status Siswa --}}
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Status Siswa</label>
                            <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="aktif" {{ $selectedStatus === 'aktif' ? 'selected' : '' }}>Siswa Aktif</option>
                                <option value="menunggu_penempatan" {{ $selectedStatus === 'menunggu_penempatan' ? 'selected' : '' }}>Menunggu Penempatan</option>
                                <option value="nonaktif" {{ $selectedStatus === 'nonaktif' ? 'selected' : '' }}>Arsip Alumni</option>
                            </select>
                        </div>

                        {{-- Kategori 2: Kelas --}}
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Kelas</label>
                            <select name="kelas_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Kelas</option>
                                @foreach ($kelasList as $k)
                                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kategori 3: Angkatan --}}
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Tahun Angkatan</label>
                            <select name="angkatan" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Angkatan</option>
                                @foreach ($angkatanList as $a)
                                    <option value="{{ $a }}" {{ request('angkatan') === $a ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kategori 4: Jenis Kelamin --}}
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Jenis Kelamin</option>
                                <option value="laki-laki" {{ request('jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ request('jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        {{-- Kategori 5: Jabatan --}}
                        <div class="sm:col-span-2">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Jabatan / Peran</label>
                            <select name="jabatan" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Jabatan</option>
                                <option value="siswa" {{ request('jabatan') === 'siswa' ? 'selected' : '' }}>Siswa Reguler</option>
                                <option value="ketua" {{ request('jabatan') === 'ketua' ? 'selected' : '' }}>Ketua Ekskul</option>
                            </select>
                        </div>
                    </div>

                    {{-- Footer Popover --}}
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <a href="{{ route('kesiswaan.siswa.index', request()->only('q', 'sort', 'direction')) }}"
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

                {{-- Status --}}
                @if (request('status') && request('status') !== 'aktif')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                        Status: {{ $selectedStatus === 'menunggu_penempatan' ? 'Menunggu Penempatan' : 'Arsip Alumni' }}
                        <a href="{{ route('kesiswaan.siswa.index', array_merge(request()->except('status', 'page'), ['status' => 'aktif'])) }}" class="text-sky-400 hover:text-rose-600 font-extrabold">&times;</a>
                    </span>
                @endif

                {{-- Kelas --}}
                @if (request()->filled('kelas_id'))
                    @php $klsName = $kelasList->firstWhere('id', request('kelas_id'))?->nama ?? request('kelas_id'); @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                        Kelas: {{ $klsName }}
                        <a href="{{ route('kesiswaan.siswa.index', request()->except('kelas_id', 'page')) }}" class="text-sky-400 hover:text-rose-600 font-extrabold">&times;</a>
                    </span>
                @endif

                {{-- Angkatan --}}
                @if (request()->filled('angkatan'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                        Angkatan: {{ request('angkatan') }}
                        <a href="{{ route('kesiswaan.siswa.index', request()->except('angkatan', 'page')) }}" class="text-sky-400 hover:text-rose-600 font-extrabold">&times;</a>
                    </span>
                @endif

                {{-- Jenis Kelamin --}}
                @if (request()->filled('jenis_kelamin'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                        Gender: {{ ucfirst(request('jenis_kelamin')) }}
                        <a href="{{ route('kesiswaan.siswa.index', request()->except('jenis_kelamin', 'page')) }}" class="text-sky-400 hover:text-rose-600 font-extrabold">&times;</a>
                    </span>
                @endif

                {{-- Jabatan --}}
                @if (request()->filled('jabatan'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                        Jabatan: {{ request('jabatan') === 'ketua' ? 'Ketua Ekskul' : 'Siswa Reguler' }}
                        <a href="{{ route('kesiswaan.siswa.index', request()->except('jabatan', 'page')) }}" class="text-sky-400 hover:text-rose-600 font-extrabold">&times;</a>
                    </span>
                @endif

                <a href="{{ route('kesiswaan.siswa.index', request()->only('q', 'sort', 'direction')) }}" class="text-[11px] font-bold text-rose-500 hover:underline ml-1">
                    Hapus Semua
                </a>
            </div>
        @endif
    </form>

    {{-- TABEL SISWA --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Siswa
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nama') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('kelas') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Kelas & Angkatan
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('kelas') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">Jabatan</th>
                        <th class="py-3.5 px-5">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($siswas as $s)
                        <tr class="hover:bg-sky-50/30 transition">
                            {{-- Siswa / Avatar & Info --}}
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0 overflow-hidden">
                                        @if ($s->foto_url)
                                            <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($s->nama, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 leading-snug truncate">{{ $s->nama }}</div>
                                        <div class="text-[11px] text-slate-400 truncate flex items-center gap-1.5 mt-0.5">
                                            <span class="font-medium text-slate-600">NIS. {{ $s->nis }}</span>
                                            @if ($s->jenis_kelamin)
                                                <span>&bull;</span>
                                                <span class="{{ $s->jenis_kelamin === 'perempuan' ? 'text-rose-500' : 'text-sky-600' }} font-medium">
                                                    {{ $s->jenis_kelamin === 'perempuan' ? '♀ P' : '♂ L' }}
                                                </span>
                                            @endif
                                            @if ($s->email)
                                                <span>&bull;</span>
                                                <span class="truncate">{{ $s->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kelas & Angkatan --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] bg-sky-50 text-sky-700 border border-sky-100">
                                    {{ $s->kelas?->nama ?? '-' }}
                                </span>
                                @if ($s->angkatan)
                                    <span class="block text-[10px] text-slate-400 mt-1 font-medium">Angkatan {{ $s->angkatan }}</span>
                                @endif
                            </td>

                            {{-- Jabatan --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if ($s->jabatan === 'ketua')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-[11px] bg-amber-50 text-amber-700 border border-amber-100">
                                        Ketua Ekskul
                                    </span>
                                @else
                                    <span class="text-slate-600">Siswa Reguler</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $s->status === 'nonaktif' ? 'bg-slate-100 text-slate-600' : ($s->status === 'menunggu_penempatan' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">
                                    {{ $s->status === 'nonaktif' ? 'Alumni / Nonaktif' : ($s->status === 'menunggu_penempatan' ? 'Menunggu Penempatan' : 'Aktif') }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <a href="{{ route('kesiswaan.laporan-penilaian.detail-siswa', $s) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold rounded-xl transition text-xs mr-1"
                                   title="Riwayat Nilai Ekskul {{ $s->nama }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/>
                                    </svg>
                                    Nilai
                                </a>
                                <a href="{{ route('kesiswaan.siswa.riwayat-profil', $s) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold rounded-xl transition text-xs mr-1">
                                    Riwayat Profil
                                </a>
                                <a href="{{ route('kesiswaan.siswa.riwayat-kelas', $s) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-xl transition text-xs mr-1">Riwayat Kelas</a>
                                @if ($s->user_id)
                                    <a href="{{ route('kesiswaan.users.edit', $s->user_id) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold rounded-xl transition text-xs"
                                       title="Edit Akun {{ $s->nama }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Akun
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum ada akun</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Tidak ada siswa yang sesuai kriteria.</p>
                                    @if ($hasFilter)
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah kata kunci atau hapus filter yang diterapkan.</p>
                                        <a href="{{ route('kesiswaan.siswa.index') }}" class="mt-3 text-xs font-bold text-sky-600 hover:text-sky-700">
                                            Tampilkan semua siswa
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-2 justify-between items-center">
            <p class="text-[11px] text-slate-400 font-semibold">
                Menampilkan {{ $siswas->firstItem() ?? 0 }}-{{ $siswas->lastItem() ?? 0 }} dari {{ $siswas->total() }} siswa
            </p>
            {{ $siswas->links() }}
        </div>
    </div>

</div>
@endsection
