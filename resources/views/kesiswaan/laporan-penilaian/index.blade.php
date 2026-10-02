@extends('layouts.kesiswaan')
@section('title', 'Laporan Penilaian')

@section('content')
    <div class="bg-[#F8FAFC] -mx-4 md:-mx-8 px-4 md:px-8 py-6 md:py-8 min-h-[calc(100vh-5rem)] space-y-6 animate-fade-up">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Laporan Penilaian</h1>
                <p class="text-xs text-slate-400 mt-0.5">Rekap penilaian akhir ekskul yang dikumpulkan pembina</p>
            </div>
            <span class="inline-flex items-center gap-2 px-3.5 py-2 bg-white rounded-lg border border-slate-200 shadow-sm text-xs font-bold text-blue-600">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                Periode {{ $periode }}
            </span>
        </div>

        @if ($ekskuls->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-12 md:p-16 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-slate-900">Belum Ada Laporan</h2>
                <p class="text-xs text-slate-400 mt-1">Pembina belum mengirim laporan penilaian untuk periode ini. Anda akan mendapat notifikasi saat laporan tiba.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                    <p class="text-xl md:text-2xl font-extrabold text-slate-900">{{ $ekskuls->count() }}</p>
                    <p class="text-xs font-bold text-slate-400 mt-0.5">Ekskul Terlapor</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                    <p class="text-xl md:text-2xl font-extrabold text-amber-600">{{ $ekskuls->sum('sudahDinilai') }}</p>
                    <p class="text-xs font-bold text-slate-400 mt-0.5">Siswa Dinilai</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                    <p class="text-xl md:text-2xl font-extrabold text-blue-600">{{ number_format($ekskuls->avg('rataNilai') ?? 0, 1) }}</p>
                    <p class="text-xs font-bold text-slate-400 mt-0.5">Rata-rata Semua Ekskul</p>
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

                $hasFilter = request()->filled(['q', 'status']) || request()->filled('sort');
            @endphp

            {{-- SEARCH & FILTER FORM --}}
            <form method="GET" action="{{ route('kesiswaan.laporan-penilaian.index') }}" class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-sm flex flex-col md:flex-row gap-3 items-stretch md:items-center">
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif

                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama ekskul atau pembina..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>

                <div class="flex items-center gap-2">
                    <select name="status" class="px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="">Semua Status Kelengkapan</option>
                        <option value="lengkap" {{ request('status') === 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                        <option value="sebagian" {{ request('status') === 'sebagian' ? 'selected' : '' }}>Sebagian</option>
                        <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    </select>

                    <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm transition">
                        Filter
                    </button>

                    @if($hasFilter)
                        <a href="{{ route('kesiswaan.laporan-penilaian.index') }}"
                           class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition"
                           title="Reset filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="p-4 md:p-5 border-b border-slate-200/70">
                    <h3 class="text-sm font-bold text-slate-900">Per-Ekskul</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Daftar rekap pengumpulan nilai per ekskul.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[860px]">
                        <thead class="bg-slate-50/80 text-xs uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-bold">#</th>
                                <th class="px-4 py-3 font-bold">
                                    <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                        Ekskul
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nama') }}"/>
                                        </svg>
                                    </a>
                                </th>
                                <th class="px-4 py-3 font-bold">Pembina</th>
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
                                                <p class="text-xs text-slate-400">Pembina: {{ $item->ekskul->pembina?->nama ?? '-' }} &middot; Pelatih: {{ $item->ekskul->pelatih?->nama ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 font-semibold">{{ $item->ekskul->pembina?->nama ?? '-' }}</td>
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
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('kesiswaan.laporan-penilaian.show', $item->ekskul->id) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm shadow-blue-600/25 transition">
                                                Detail
                                            </a>
                                            <a href="{{ route('kesiswaan.laporan-penilaian.download-pdf', $item->ekskul->id) }}"
                                               title="Download PDF"
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