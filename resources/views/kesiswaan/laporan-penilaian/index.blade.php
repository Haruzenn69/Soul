@extends('layouts.kesiswaan')
@section('title', 'Laporan Penilaian')

@section('content')
    <div class="bg-[#F8FAFC] -mx-4 md:-mx-8 px-4 md:px-8 py-6 md:py-8 min-h-[calc(100vh-5rem)] space-y-6 animate-fade-up">
        
        {{-- HEADER & TABS --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 pb-2 border-b border-slate-200/80">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Laporan Penilaian</h1>
                    @if($tahunAjaranAktif)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            TA Aktif: {{ $tahunAjaranAktif->nama }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-1">Rekap penilaian akhir ekskul dan riwayat nilai siswa per tahun ajaran</p>
            </div>

            {{-- PEMILIH PERIODE TAHUN AJARAN --}}
            <form method="GET" action="{{ route('kesiswaan.laporan-penilaian.index') }}" class="flex items-center gap-2 w-full lg:w-auto">
                @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif

                <div class="relative w-full sm:w-72">
                    <label for="periode-select" class="sr-only">Pilih Periode</label>
                    <select id="periode-select" name="periode" onchange="this.form.submit()"
                            class="w-full pl-3.5 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-100 focus:border-sky-400 transition cursor-pointer">
                        @foreach($daftarPeriode as $itemP)
                            <option value="{{ $itemP['label'] }}" {{ $periode === $itemP['label'] ? 'selected' : '' }}>
                                {{ $itemP['label'] }} {{ $itemP['is_aktif'] ? '★ (Aktif)' : ($itemP['is_historis'] ? '(Historis)' : '') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- TAB NAVIGASI UTAMA --}}
        <div class="flex items-center gap-2 border-b border-slate-200">
            <a href="{{ route('kesiswaan.laporan-penilaian.index', ['periode' => $periode]) }}"
               class="px-4 py-2.5 text-xs font-bold border-b-2 border-sky-500 text-sky-600 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Rekap Nilai Ekskul
            </a>
            <a href="{{ route('kesiswaan.laporan-penilaian.historis-siswa') }}"
               class="px-4 py-2.5 text-xs font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                </svg>
                Riwayat Nilai Siswa
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-600 border border-sky-100">Historis</span>
            </a>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-xl md:text-2xl font-extrabold text-slate-900">{{ $ekskuls->count() }}</p>
                <p class="text-xs font-bold text-slate-400 mt-0.5">Ekskul Terdaftar</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-xl md:text-2xl font-extrabold text-amber-600">{{ $ekskuls->sum('sudahDinilai') }}</p>
                <p class="text-xs font-bold text-slate-400 mt-0.5">Siswa Dinilai ({{ $periode }})</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-xl md:text-2xl font-extrabold text-blue-600">{{ number_format($ekskuls->avg('rataNilai') ?? 0, 1) }}</p>
                <p class="text-xs font-bold text-slate-400 mt-0.5">Rata-rata Nilai Ekskul</p>
            </div>
        </div>

        @php
            $sort = $sort ?? 'dinilai';
            $direction = $direction ?? 'desc';

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

            $activeFilterCount = (request()->filled('status') ? 1 : 0);
            $hasActiveFilter = request()->filled('q') || $activeFilterCount > 0;
        @endphp

        {{-- SEARCH & FILTER BAR --}}
        <div x-data="{ openFilter: false }" class="space-y-3">
            <form method="GET" action="{{ route('kesiswaan.laporan-penilaian.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-sm flex flex-col md:flex-row gap-3 items-stretch md:items-center">
                <input type="hidden" name="periode" value="{{ $periode }}">
                @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif

                {{-- Input Pencarian --}}
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama ekskul atau pembina yang menilai..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>

                {{-- Tombol Filter & Popover --}}
                <div class="relative flex items-center gap-2">
                    <button type="button" @click="openFilter = !openFilter"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 border {{ $activeFilterCount > 0 ? 'bg-sky-50 border-sky-300 text-sky-700 shadow-sm shadow-sky-100' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                        <svg class="w-4 h-4 {{ $activeFilterCount > 0 ? 'text-sky-600' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter Status</span>
                        @if($activeFilterCount > 0)
                            <span class="w-5 h-5 rounded-full bg-sky-500 text-white text-[10px] font-black flex items-center justify-center">
                                {{ $activeFilterCount }}
                            </span>
                        @endif
                    </button>

                    <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm transition">
                        Cari
                    </button>

                    @if($hasActiveFilter)
                        <a href="{{ route('kesiswaan.laporan-penilaian.index', ['periode' => $periode]) }}"
                           class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition"
                           title="Reset filter">
                            Reset
                        </a>
                    @endif

                    {{-- DROPDOWN PANEL POPOVER --}}
                    <div x-show="openFilter"
                         @click.away="openFilter = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl p-5 border border-slate-200 shadow-xl shadow-slate-900/10 z-50 space-y-4"
                         style="display: none;">
                        
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h4 class="text-xs font-bold text-slate-800">Filter Kelengkapan Nilai</h4>
                            <button type="button" @click="openFilter = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">
                                &times;
                            </button>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status Pengumpulan</label>
                            <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Status Kelengkapan</option>
                                <option value="lengkap" {{ request('status') === 'lengkap' ? 'selected' : '' }}>Lengkap (Semua Dinilai)</option>
                                <option value="sebagian" {{ request('status') === 'sebagian' ? 'selected' : '' }}>Sebagian (Sedang Berjalan)</option>
                                <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu (Belum Dinilai)</option>
                            </select>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="{{ route('kesiswaan.laporan-penilaian.index', array_filter(['periode' => $periode, 'q' => request('q')])) }}"
                               class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition">
                                Reset
                            </a>
                            <button type="submit" @click="openFilter = false"
                                    class="px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- ACTIVE FILTER CHIPS --}}
            @if($hasActiveFilter)
                <div class="flex flex-wrap items-center gap-2 pt-1 px-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Filter Aktif:</span>

                    @if(request()->filled('q'))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-xs font-semibold">
                            <span>Pencarian: "<strong>{{ request('q') }}</strong>"</span>
                            <a href="{{ route('kesiswaan.laporan-penilaian.index', request()->except('q', 'page')) }}" class="hover:text-rose-500 transition">&times;</a>
                        </span>
                    @endif

                    @if(request()->filled('status'))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-xs font-semibold">
                            <span>Status: <strong>{{ ucfirst(request('status')) }}</strong></span>
                            <a href="{{ route('kesiswaan.laporan-penilaian.index', request()->except('status', 'page')) }}" class="hover:text-rose-500 transition">&times;</a>
                        </span>
                    @endif

                    <a href="{{ route('kesiswaan.laporan-penilaian.index', ['periode' => $periode]) }}" class="text-xs font-bold text-rose-500 hover:text-rose-600 hover:underline transition ml-1">
                        Hapus Filter
                    </a>
                </div>
            @endif
        </div>

        {{-- TABEL DATA EKSKUL --}}
        @if ($ekskuls->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-12 md:p-16 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-slate-900">Tidak Ada Data Penilaian</h2>
                <p class="text-xs text-slate-400 mt-1">Tidak ada data ekskul atau laporan penilaian pada periode <strong>{{ $periode }}</strong>.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="p-4 md:p-5 border-b border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Rekap Penilaian Per-Ekskul</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Periode: <span class="font-bold text-slate-700">{{ $periode }}</span></p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[820px]">
                        <thead class="bg-slate-50/80 text-xs uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-bold w-12">#</th>
                                <th class="px-4 py-3 font-bold">
                                    <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                        Ekskul
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nama') }}"/>
                                        </svg>
                                    </a>
                                </th>
                                <th class="px-4 py-3 font-bold">Pembina Penilai</th>
                                <th class="px-4 py-3 font-bold text-center">
                                    <a href="{{ $sortLink('anggota') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                        Anggota
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('anggota') }}"/>
                                        </svg>
                                    </a>
                                </th>
                                <th class="px-4 py-3 font-bold text-center">
                                    <a href="{{ $sortLink('dinilai') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                        Dinilai
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('dinilai') }}"/>
                                        </svg>
                                    </a>
                                </th>
                                <th class="px-4 py-3 font-bold text-center">
                                    <a href="{{ $sortLink('rata') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                        Rata-rata
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('rata') }}"/>
                                        </svg>
                                    </a>
                                </th>
                                <th class="px-4 py-3 font-bold text-center">Status</th>
                                <th class="px-4 py-3 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($ekskuls as $item)
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-4 py-3 text-slate-400 font-semibold">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3 min-w-[180px]">
                                            <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 leading-tight truncate">{{ $item->ekskul->nama_ekskul }}</p>
                                                <p class="text-xs text-slate-400">{{ $item->ekskul->kategori?->nama_kategori ?? 'Umum' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="min-w-[160px]">
                                            <p class="font-bold text-slate-800 leading-tight">{{ $item->pembinaPenilai?->nama ?? $item->ekskul->pembina?->nama ?? '-' }}</p>
                                            <p class="text-[11px] text-slate-400">
                                                @if($item->pembinaPenilai && $item->pembinaPenilai->id !== $item->ekskul->pembina_id)
                                                    <span class="text-amber-600 font-semibold">Penilai Khusus</span> &middot; Pembina: {{ $item->ekskul->pembina?->nama ?? '-' }}
                                                @else
                                                    Pembina Ekskul
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-900">{{ $item->totalAnggota }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-amber-600">{{ $item->sudahDinilai }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-900">{{ number_format($item->rataNilai, 1) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($item->sudahDinilai > 0)
                                            @if ($item->sudahDinilai >= $item->totalAnggota)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Lengkap
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">Sebagian</span>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-400 border border-slate-200">Menunggu</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('kesiswaan.laporan-penilaian.show', ['ekskul' => $item->ekskul->id, 'periode' => $periode]) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm shadow-blue-600/25 transition">
                                                Detail
                                            </a>
                                            <a href="{{ route('kesiswaan.laporan-penilaian.download-pdf', ['ekskul' => $item->ekskul->id, 'periode' => $periode]) }}"
                                               title="Download PDF Laporan"
                                               class="inline-flex items-center justify-center w-8 h-8 bg-white hover:bg-red-50 text-red-600 rounded-lg border border-slate-200 shadow-sm transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection