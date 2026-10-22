@extends('layouts.kesiswaan')

@section('title', 'Data Pembina')

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

    $hasFilter = request()->filled(['q', 'jenis_kelamin']);
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
                    Data Pembina
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Daftar seluruh pembina ekskul. Kelola penugasan ekskul yang dibina oleh masing-masing pembina di sini.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.users.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Pembina
                </a>
            </div>
        </div>
    </div>

    {{-- Notifikasi success/error ditampilkan oleh layouts.kesiswaan --}}
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
        if (request()->filled('jenis_kelamin')) $activeFilterCount++;
        if (request()->filled('kategori')) $activeFilterCount++;
        $hasActiveSearch = request()->filled('q');
        $hasAnyFilterOrSearch = $activeFilterCount > 0 || $hasActiveSearch;
    @endphp

    {{-- SEARCH & FILTER FORM DENGAN POPOVER --}}
    <form method="GET" action="{{ route('kesiswaan.pembina.index') }}" x-data="{ openFilter: false }" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm relative space-y-3">
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
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIP, email, atau no. telp..."
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
                @if ($hasAnyFilterOrSearch || $hasFilter || request('kategori') || $sort !== 'nama' || $direction !== 'asc')
                    <a href="{{ route('kesiswaan.pembina.index') }}"
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
                     class="absolute right-0 top-full mt-2 w-[calc(100vw-2.5rem)] sm:w-[440px] bg-white rounded-3xl border border-sky-100 shadow-2xl p-5 z-50 space-y-4">

                    {{-- Header Popover --}}
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-slate-900">Filter Guru / Pembina</h3>
                                <p class="text-[11px] text-slate-400">Pilih kategori atau gender pembina</p>
                            </div>
                        </div>
                        <button type="button" @click="openFilter = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Grid Kategori Filter --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        {{-- Jenis Kelamin --}}
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Jenis Kelamin</option>
                                <option value="laki-laki" {{ request('jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ request('jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        {{-- Kategori Binaan --}}
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Kategori Binaan</label>
                            <select name="kategori" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Kategori</option>
                                @foreach (\App\Support\AturanEkskul::KATEGORI_LIST as $kat)
                                    <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Footer Popover --}}
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <a href="{{ route('kesiswaan.pembina.index', request()->only('q', 'sort', 'direction')) }}"
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

                {{-- Jenis Kelamin --}}
                @if (request()->filled('jenis_kelamin'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                        Gender: {{ ucfirst(request('jenis_kelamin')) }}
                        <a href="{{ route('kesiswaan.pembina.index', request()->except('jenis_kelamin', 'page')) }}" class="text-sky-400 hover:text-rose-600 font-extrabold">&times;</a>
                    </span>
                @endif

                {{-- Kategori --}}
                @if (request()->filled('kategori'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                        Kategori: {{ request('kategori') }}
                        <a href="{{ route('kesiswaan.pembina.index', request()->except('kategori', 'page')) }}" class="text-sky-400 hover:text-rose-600 font-extrabold">&times;</a>
                    </span>
                @endif

                <a href="{{ route('kesiswaan.pembina.index', request()->only('q', 'sort', 'direction')) }}" class="text-[11px] font-bold text-rose-500 hover:underline ml-1">
                    Hapus Semua
                </a>
            </div>
        @endif
    </form>

    {{-- TABEL PEMBINA --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Pembina
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nama') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">Kategori Binaan</th>
                        <th class="py-3.5 px-5">Ekskul Dibina</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($pembinas as $p)
                        <tr class="hover:bg-sky-50/30 transition">
                            {{-- Pembina / Avatar & Info --}}
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0 overflow-hidden">
                                        @if ($p->foto_url)
                                             <img src="{{ $p->foto_url }}" alt="{{ $p->nama }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($p->nama ?? '?', 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 leading-snug truncate">{{ $p->nama ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-400 truncate flex items-center gap-1.5 mt-0.5">
                                            <span>{{ $p->nip ? 'NIP. ' . $p->nip : ($p->email ?? '-') }}</span>
                                            @if ($p->jenis_kelamin)
                                                <span>&bull;</span>
                                                <span class="{{ $p->jenis_kelamin === 'perempuan' ? 'text-rose-500' : 'text-sky-600' }} font-medium">
                                                    {{ $p->jenis_kelamin === 'perempuan' ? '♀ Perempuan' : '♂ Laki-laki' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kategori Binaan & Riwayat --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if ($p->kategori_aktif)
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-bold text-[11px] bg-sky-50 text-sky-700 border border-sky-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                            {{ $p->kategori_aktif }}
                                        </span>
                                        <span class="block text-[10px] text-slate-400 mt-0.5">Membina {{ $p->ekskuls->count() }} ekskul</span>
                                    </div>
                                @elseif ($p->kategori_pernah_dibina)
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-bold text-[11px] bg-amber-50 text-amber-700 border border-amber-200" title="Data riwayat: pernah membina kategori ini">
                                            <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Pernah: {{ $p->kategori_pernah_dibina }}
                                        </span>
                                        <span class="block text-[10px] text-amber-600/80 mt-0.5 font-medium">Tidak membina (Riwayat)</span>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum ada kategori</span>
                                @endif
                            </td>

                            {{-- Ekskul Dibina --}}
                            <td class="py-3.5 px-5">
                                <div class="flex flex-col gap-1.5 items-start">
                                    <div class="flex flex-wrap gap-1 items-center">
                                        @forelse ($p->ekskuls as $ek)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $ek->nama_ekskul }}
                                            </span>
                                        @empty
                                            <span class="text-[11px] text-slate-400 italic">0 Ekskul</span>
                                        @endforelse
                                    </div>
                                    <button type="button" onclick="document.getElementById('modal-kelola-{{ $p->id }}').showModal()"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                                        Kelola Ekskul ({{ $p->ekskuls->count() }}/4)
                                    </button>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <a href="{{ route('kesiswaan.pembina.riwayat-profil', $p) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 hover:border-sky-300 hover:bg-sky-50 text-slate-600 hover:text-sky-700 font-medium rounded-lg transition text-xs mr-1" title="Riwayat Profil">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Riwayat
                                </a>
                                @if ($p->user_id)
                                    <a href="{{ route('kesiswaan.users.edit', $p->user_id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 hover:border-sky-300 hover:bg-sky-50 text-slate-600 hover:text-sky-700 font-medium rounded-lg transition text-xs">
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
                            <td colspan="4" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Tidak ada pembina yang sesuai kriteria.</p>
                                    @if ($hasFilter || request('kategori'))
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah kata kunci atau hapus filter.</p>
                                        <a href="{{ route('kesiswaan.pembina.index') }}" class="mt-3 text-xs font-bold text-sky-600 hover:text-sky-700">
                                            Tampilkan semua pembina
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
                Menampilkan {{ $pembinas->firstItem() ?? 0 }}-{{ $pembinas->lastItem() ?? 0 }} dari {{ $pembinas->total() }} pembina
            </p>
            {{ $pembinas->links() }}
        </div>
    </div>

</div>

@foreach ($pembinas as $p)
    <dialog id="modal-kelola-{{ $p->id }}" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-lg shadow-2xl border border-sky-100">
        <div class="p-6 space-y-4 bg-white">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Kelola Ekskul Pembina</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $p->nama }} · {{ $p->ekskuls->count() }}/4 ekskul</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center">×</button>
            </div>

            {{-- KATEGORI BADGE STATUS --}}
            @if ($p->kategori_aktif)
                <div class="p-3 bg-sky-50/80 rounded-2xl border border-sky-100 text-xs">
                    <p class="font-bold text-sky-900">Kategori Binaan: <span class="text-sky-700 font-extrabold">{{ $p->kategori_aktif }}</span></p>
                    <p class="text-[11px] text-sky-700/80 mt-0.5">Pembina ini dapat ditugaskan pada ekskul dari kategori mana saja (lintas kategori), maksimal <strong>4 ekskul</strong>.</p>
                </div>
            @elseif ($p->kategori_pernah_dibina)
                <div class="p-3 bg-amber-50/80 rounded-2xl border border-amber-100 text-xs">
                    <p class="font-bold text-amber-900">Riwayat Kategori: <span class="text-amber-800 font-extrabold">Pernah Membina {{ $p->kategori_pernah_dibina }}</span></p>
                    <p class="text-[11px] text-amber-700/80 mt-0.5">Pembina ini saat ini tidak membina ekskul, namun memiliki riwayat pembinaan di kategori <strong>{{ $p->kategori_pernah_dibina }}</strong>.</p>
                </div>
            @else
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200/80 text-xs">
                    <p class="font-bold text-slate-700">Belum Ada Kategori</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Pembina dapat ditugaskan ke ekskul dari kategori apa saja, maksimal 4 ekskul.</p>
                </div>
            @endif

            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Ekskul yang dibina saat ini</h3>
                @forelse ($p->ekskuls as $ekskul)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div>
                            <p class="text-xs font-bold text-slate-800">{{ $ekskul->nama_ekskul }}</p>
                            <span class="inline-block mt-0.5 px-2 py-0.5 bg-sky-100 text-sky-700 font-bold text-[10px] rounded-md">
                                Kategori: {{ $ekskul->kategori ?: 'Lainnya' }}
                            </span>
                        </div>
                        <form action="{{ route('kesiswaan.pembina.remove-ekskul', [$p, $ekskul]) }}" method="POST" onsubmit="return confirm('Lepas penugasan {{ $ekskul->nama_ekskul }} dari {{ $p->nama }}? Kategori akan disimpan sebagai riwayat pembina.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-xl text-[11px] font-bold bg-rose-50 text-rose-600 hover:bg-rose-100">
                                Tidak Membina Lagi
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-2">Belum ada ekskul yang sedang dibina.</p>
                @endforelse
            </div>

            @if ($p->ekskuls->count() < 4)
                @php
                    $availableEkskuls = $ekskulList->whereNull('pembina_id');
                @endphp
                <form action="{{ route('kesiswaan.pembina.assign-ekskul', $p) }}" method="POST" class="pt-3 border-t border-slate-100 space-y-3">
                    @csrf
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tambah ekskul (lintas kategori diperbolehkan)</label>
                    <select name="ekskul_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800">
                        <option value="" disabled selected>Pilih ekskul...</option>
                        @foreach ($availableEkskuls as $ekskul)
                            <option value="{{ $ekskul->id }}">
                                {{ $ekskul->nama_ekskul }} · Kategori: {{ $ekskul->kategori ?: 'Lainnya' }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl">
                        Tugaskan Ekskul
                    </button>
                </form>
            @else
                <p class="text-[11px] text-amber-700 bg-amber-50 border border-amber-100 rounded-xl p-3">Batas maksimal 4 ekskul per pembina tercapai.</p>
            @endif
        </div>
    </dialog>
@endforeach
@endsection
