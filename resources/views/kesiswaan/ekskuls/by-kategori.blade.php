@extends('layouts.kesiswaan')

@section('title', 'Ekskul - ' . $kategori)

@section('content')
@php
    $sort = $sort ?? 'nama_ekskul';
    $direction = $direction ?? 'asc';

    $sortLink = function (string $column) use ($sort, $direction, $kategori) {
        $nextDirection = $sort === $column && $direction === 'asc' ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => null]);
    };

    $sortIcon = function (string $column) use ($sort, $direction) {
        if ($sort !== $column) {
            return 'M8 9l4-4 4 4M8 15l4 4 4-4';
        }
        return $direction === 'asc' ? 'M8 15l4 4 4-4' : 'M8 9l4-4 4 4';
    };

    $activeFilterCount = 0;
    if (request()->filled('status')) $activeFilterCount++;
    if (request()->filled('rekrutmen')) $activeFilterCount++;
    $hasActiveSearch = request()->filled('q');
    $hasAnyFilterOrSearch = $activeFilterCount > 0 || $hasActiveSearch;
@endphp

<div class="space-y-5 animate-fade-up">

    {{-- HERO CARD BIRU --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white soul-timbul">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-2 text-white/80 text-xs mb-1">
                    <a href="{{ route('kesiswaan.ekskuls.index') }}" class="hover:text-white transition">Data Ekskul</a>
                    <x-icon name="chevron-right" class="w-3 h-3" />
                    <span>{{ $kategori }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                    Ekskul - {{ $kategori }}
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Daftar ekstrakurikuler pada kategori {{ $kategori }}.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.ekskuls.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/10 backdrop-blur text-white font-bold text-xs rounded-2xl hover:bg-white/20 transition-all">
                    <x-icon name="arrow-left" class="w-4 h-4" />
                    Kembali
                </a>
                <button onclick="document.getElementById('modal-create').showModal()"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <x-icon name="plus" class="w-4 h-4" />
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

    {{-- SEARCH & FILTER FORM --}}
    <form method="GET" action="{{ route('kesiswaan.ekskuls.by-kategori', $kategori) }}" x-data="{ openFilter: false }" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm relative space-y-3">
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if(request('direction'))
            <input type="hidden" name="direction" value="{{ request('direction') }}">
        @endif
        <div class="flex flex-col sm:flex-row gap-2.5 items-stretch sm:items-center">
            <div class="soul-search-wrap relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <x-icon name="search" class="w-4 h-4" />
                </span>
                <input type="search" name="q" data-soul-search value="{{ request('q') }}" placeholder="Cari nama ekskul atau pembina..."
                       class="w-full pl-4 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div class="flex items-center gap-2 relative">
                <button type="button" @click="openFilter = !openFilter"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold border transition shadow-sm"
                        :class="openFilter || {{ $activeFilterCount }} > 0 ? 'bg-sky-50 text-sky-700 border-sky-300 ring-2 ring-sky-100' : 'bg-slate-50 text-slate-700 border-slate-200/80 hover:bg-slate-100'">
                    <x-icon name="filter" class="w-4 h-4" />
                    <span>Filter</span>
                    @if($activeFilterCount > 0)
                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-extrabold rounded-full bg-sky-500 text-white">
                            {{ $activeFilterCount }}
                        </span>
                    @endif
                    <x-icon name="chevron-down" class="w-3.5 h-3.5 text-slate-400 transition-transform" />
                </button>

                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Cari
                </button>

                @if ($hasAnyFilterOrSearch)
                    <a href="{{ route('kesiswaan.ekskuls.by-kategori', $kategori) }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition"
                       title="Reset semua filter">
                        Reset
                    </a>
                @endif

                <div x-show="openFilter"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     @click.away="openFilter = false"
                     style="display: none;"
                     class="absolute right-0 top-full mt-2 w-[calc(100vw-2.5rem)] sm:w-[400px] bg-white rounded-3xl border border-sky-100 shadow-2xl p-5 z-50 space-y-4">

                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold">
                                <x-icon name="filter" class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-slate-900">Filter Ekstrakurikuler</h3>
                                <p class="text-[11px] text-slate-400">Filter berdasarkan status</p>
                            </div>
                        </div>
                        <button type="button" @click="openFilter = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-xl hover:bg-slate-50 transition">
                            <x-icon name="x" class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Status Ekskul</label>
                            <select name="status" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">Pendaftaran Anggota</label>
                            <select name="rekrutmen" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Status</option>
                                <option value="buka" {{ request('rekrutmen') === 'buka' ? 'selected' : '' }}>Buka Pendaftaran</option>
                                <option value="tutup" {{ request('rekrutmen') === 'tutup' ? 'selected' : '' }}>Tutup Pendaftaran</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <a href="{{ route('kesiswaan.ekskuls.by-kategori', array_merge([$kategori], request()->only('q', 'sort', 'direction'))) }}"
                           class="text-xs font-bold text-slate-400 hover:text-rose-600 transition">
                            Reset Opsi Filter
                        </a>
                        <button type="submit" @click="openFilter = false" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                            <span>Terapkan Filter</span>
                            <x-icon name="check" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if ($hasAnyFilterOrSearch)
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Filter Aktif:</span>
                @if(request()->filled('q'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-[11px] font-semibold">
                        Pencarian: <span class="font-bold">"{{ request('q') }}"</span>
                        <a href="{{ route('kesiswaan.ekskuls.by-kategori', array_merge([$kategori], request()->except('q','page'))) }}" class="hover:text-rose-500">&times;</a>
                    </span>
                @endif
                @if(request()->filled('status'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-[11px] font-semibold">
                        Status: <span class="font-bold">{{ request('status') === 'aktif' ? 'Aktif' : 'Nonaktif' }}</span>
                        <a href="{{ route('kesiswaan.ekskuls.by-kategori', array_merge([$kategori], request()->except('status','page'))) }}" class="hover:text-rose-500">&times;</a>
                    </span>
                @endif
                @if(request()->filled('rekrutmen'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-[11px] font-semibold">
                        Rekrutmen: <span class="font-bold">{{ request('rekrutmen') === 'buka' ? 'Buka' : 'Tutup' }}</span>
                        <a href="{{ route('kesiswaan.ekskuls.by-kategori', array_merge([$kategori], request()->except('rekrutmen','page'))) }}" class="hover:text-rose-500">&times;</a>
                    </span>
                @endif
            </div>
        @endif
    </form>

    {{-- TABEL EKSKUL --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/70 text-[11px] uppercase tracking-wider text-slate-400 font-bold border-b border-slate-100">
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nama_ekskul') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600">
                                Nama Ekskul
                                <x-icon :path="$sortIcon('nama_ekskul')" class="w-3 h-3" />
                            </a>
                        </th>
                        <th class="py-3.5 px-5">Pembina</th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('status') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600">
                                Status
                                <x-icon :path="$sortIcon('status')" class="w-3 h-3" />
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('rekrutmen') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600">
                                Pendaftaran
                                <x-icon :path="$sortIcon('rekrutmen')" class="w-3 h-3" />
                            </a>
                        </th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($ekskuls as $ekskul)
                        <tr class="hover:bg-sky-50/30 transition {{ ! $ekskul->status ? 'opacity-70' : '' }}">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl {{ $ekskul->status ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center font-extrabold">
                                        {{ strtoupper(substr($ekskul->nama_ekskul, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $ekskul->nama_ekskul }}</p>
                                        @if ($ekskul->jadwal)
                                            <p class="text-[11px] text-slate-400">{{ $ekskul->jadwal }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-5">
                                @if ($ekskul->pembina)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500 overflow-hidden">
                                            @if ($ekskul->pembina->foto)
                                                <img src="{{ $ekskul->pembina->foto_url }}" alt="{{ $ekskul->pembina->nama }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($ekskul->pembina->nama, 0, 2)) }}
                                            @endif
                                        </div>
                                        <span class="text-slate-700">{{ $ekskul->pembina->nama }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] {{ $ekskul->status ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                    {{ $ekskul->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if ($ekskul->status)
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[11px] {{ $ekskul->is_open_recruitment ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                                        {{ $ekskul->is_open_recruitment ? 'Buka' : 'Tutup' }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full font-semibold text-[11px] bg-slate-200 text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="flex gap-2 justify-end items-center">
                                    <a href="{{ route('kesiswaan.ekskuls.show', $ekskul) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 {{ $ekskul->status ? 'bg-sky-500 hover:bg-sky-600 text-white shadow-sky-200' : 'bg-slate-200 hover:bg-slate-300 text-slate-700' }} font-bold text-xs rounded-xl shadow-sm transition"
                                       title="Lihat Detail Ekskul">
                                        <x-icon name="eye" class="w-3.5 h-3.5" />
                                        Detail
                                    </a>
                                    <button type="button"
                                            onclick='openEdit({{ json_encode(["id" => $ekskul->id, "nama_ekskul" => $ekskul->nama_ekskul, "kategori" => $ekskul->kategori, "pembina_id" => $ekskul->pembina_id, "deskripsi" => $ekskul->deskripsi, "jadwal" => $ekskul->jadwal, "status" => $ekskul->status ? 1 : 0]) }})'
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                                        <x-icon name="pencil" class="w-3 h-3 text-slate-500" />
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-400">Belum ada ekskul pada kategori ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ekskuls->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-2 justify-between items-center">
                <p class="text-[11px] text-slate-400 font-semibold">
                    Menampilkan {{ $ekskuls->firstItem() ?? 0 }}-{{ $ekskuls->lastItem() ?? 0 }} dari {{ $ekskuls->total() }} ekskul
                </p>
                {{ $ekskuls->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL CREATE --}}
    <dialog id="modal-create" class="rounded-3xl backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm p-0 w-full max-w-md shadow-2xl border border-sky-100">
        <form method="POST" action="{{ route('kesiswaan.ekskuls.store') }}" class="p-6 md:p-8 space-y-4 bg-white">
            @csrf
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Tambah Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Buat ekstrakurikuler baru</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <span class="text-base leading-none">&times;</span>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Nama Ekskul <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_ekskul" placeholder="Contoh: Paskibra, OSIS, Pramuka..." required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Kategori <span class="text-rose-500">*</span></label>
                <select name="kategori" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="{{ $kategori }}" selected>{{ $kategori }}</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Kategori otomatis mengikuti halaman ini.</p>
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
    <dialog id="modal-edit" class="rounded-3xl backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm p-0 w-full max-w-md shadow-2xl border border-sky-100">
        <form id="form-edit" method="POST" class="p-6 md:p-8 space-y-4 bg-white">
            @csrf
            @method('PUT')
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Edit Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Perbarui data ekstrakurikuler</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <span class="text-base leading-none">&times;</span>
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
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Kategori <span class="text-rose-500">*</span></label>
                <div id="edit_kategori_locked" class="hidden w-full px-4 py-2.5 bg-sky-50 border border-sky-200 rounded-2xl text-xs text-sky-800 font-bold flex items-center gap-2">
                    <span id="edit_kategori_locked_text">Wajib</span>
                    <input type="hidden" name="kategori" id="edit_kategori_hidden" value="Wajib">
                </div>
                <select name="kategori" id="edit_kategori" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="" disabled>Pilih kategori...</option>
                    @foreach ($kategoriList ?? \App\Support\AturanEkskul::KATEGORI_LIST as $kat)
                        @if (!\App\Support\AturanEkskul::isWajib($kat))
                            <option value="{{ $kat }}">{{ $kat }}</option>
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
</div>

<script>
    function openEdit(data) {
        const form = document.getElementById('form-edit');
        form.action = '{{ url('kesiswaan/ekskuls') }}/' + data.id;
        form.querySelector('[name=nama_ekskul]').value = data.nama_ekskul || '';

        const selStatus = document.getElementById('edit_status');
        if (selStatus) {
            selStatus.value = (data.status !== undefined && data.status !== null) ? String(data.status) : '1';
        }

        const selKategori   = document.getElementById('edit_kategori');
        const lockedBox     = document.getElementById('edit_kategori_locked');
        const lockedText    = document.getElementById('edit_kategori_locked_text');
        const hiddenKategori= document.getElementById('edit_kategori_hidden');
        const isWajib       = (data.kategori || '').toLowerCase() === 'wajib';

        if (isWajib) {
            selKategori.classList.add('hidden');
            selKategori.removeAttribute('name');
            selKategori.removeAttribute('required');
            lockedBox.classList.remove('hidden');
            lockedText.textContent = data.kategori;
            hiddenKategori.value = data.kategori;
        } else {
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

    @if ($errors->any() && old('nama_ekskul'))
        document.getElementById('modal-create').showModal();
    @endif
</script>
@endsection
