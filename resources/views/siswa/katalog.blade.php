@extends('layouts.siswa')

@section('title', 'Katalog Ekskul')

@section('search')
    <form method="GET" action="{{ route('siswa.katalog') }}" class="relative w-full max-w-md hidden sm:block">
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </span>
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari ekskul..."
               class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
    </form>
@endsection

@section('content')
    @php
        $bisaDaftar = ! $menunggu && ! $ekskulAktif;
        $filters = [
            'semua' => ['label' => 'Semua', 'count' => $jumlah['semua'] ?? 0],
            'buka'  => ['label' => 'Dibuka', 'count' => $jumlah['buka'] ?? 0],
            'tutup' => ['label' => 'Ditutup', 'count' => $jumlah['tutup'] ?? 0],
        ];
    @endphp

    {{-- HERO BANNER --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-400 via-blue-500 to-blue-600 px-6 py-5 text-white shadow-xl shadow-sky-200 animate-fade-up mb-5">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-14 -right-8 w-52 h-52 rounded-full bg-amber-300/30 blur-3xl"></div>
            <div class="absolute -bottom-16 -left-6 w-52 h-52 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute top-6 right-1/3 w-10 h-10 rounded-full bg-amber-400/40 blur-xl"></div>
        </div>
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full bg-white/15 border border-white/20 text-[10px] font-bold tracking-widest uppercase">Ekstrakurikuler</span>
                </div>
                <h1 class="text-xl md:text-2xl font-extrabold tracking-tight leading-tight">Katalog Ekskul</h1>
                <p class="text-xs text-white/75 mt-1 max-w-lg leading-relaxed">Temukan ekskul yang cocok untuk kamu. Kamu hanya bisa aktif di 1 ekskul dalam satu periode.</p>
            </div>
            {{-- Status Badge --}}
            @if ($menunggu)
                <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-300/25 border border-amber-200/40 text-white text-xs font-semibold shrink-0 w-fit">
                    <span class="w-2 h-2 rounded-full bg-amber-300 animate-pulse shrink-0"></span>
                    Menunggu verifikasi ketua
                </div>
            @elseif ($ekskulAktif)
                <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/15 border border-white/25 text-white text-xs font-semibold shrink-0 w-fit">
                    <svg class="w-4 h-4 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Terdaftar: <strong>{{ $ekskulAktif->nama_ekskul }}</strong>
                </div>
            @else
                <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/15 border border-white/25 text-white text-xs font-semibold shrink-0 w-fit">
                    <svg class="w-4 h-4 shrink-0 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Belum mendaftar ekskul
                </div>
            @endif
        </div>
    </div>

    {{-- FILTER + SEARCH BAR --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-5 animate-fade-up" style="animation-delay:.05s">
        {{-- Filter Tabs --}}
        <div class="flex items-center gap-2 bg-white border border-sky-100 rounded-2xl p-1.5 shadow-sm shadow-sky-100/60 overflow-x-auto scrollbar-none">
            @foreach ($filters as $key => $item)
                @php
                    $isActive = ($status === $key) || ($status === null && $key === 'semua');
                    $href = route('siswa.katalog', array_filter(
                        array_merge(request()->except('status'), ['status' => $key === 'semua' ? null : $key]),
                        fn($v) => $v !== null && $v !== ''
                    ));
                @endphp
                <a href="{{ $href }}"
                   class="catalog-filter-tab flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap
                          {{ $isActive
                              ? 'bg-gradient-to-r from-sky-400 to-blue-500 text-white shadow-md shadow-sky-200'
                              : 'text-slate-500 hover:text-sky-700 hover:bg-sky-50' }}">
                    <span>{{ $item['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-lg text-[10px] font-extrabold
                                 {{ $isActive ? 'bg-white/25 text-white' : 'bg-sky-50 text-sky-600' }}">
                        {{ $item['count'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Search + Reset --}}
        <div class="flex items-center gap-2 flex-1">
            <form method="GET" action="{{ route('siswa.katalog') }}" class="relative flex-1">
                @if ($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama ekskul atau pembina..."
                       class="w-full pl-9 pr-4 py-2.5 bg-white border border-sky-100 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all shadow-sm shadow-sky-100/60">
            </form>
            @if (request('cari') || $status)
                <a href="{{ route('siswa.katalog') }}"
                   class="flex items-center gap-1.5 px-3.5 py-2.5 bg-white border border-slate-200 hover:border-red-200 hover:bg-red-50 text-slate-500 hover:text-red-600 rounded-2xl text-xs font-semibold transition-all shadow-sm shrink-0"
                   title="Reset filter & pencarian">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="hidden sm:inline">Reset</span>
                </a>
            @endif
        </div>
    </div>

    {{-- STATS ROW --}}
    <div class="grid grid-cols-3 gap-3 mb-5 animate-fade-up" style="animation-delay:.08s">
        <div class="bg-white rounded-2xl border border-sky-100 shadow-sm shadow-sky-100/60 px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 flex items-center justify-center shrink-0 shadow-md shadow-sky-200">
                <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-base font-extrabold text-slate-900 leading-none">{{ $jumlah['semua'] }}</p>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Total Ekskul</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-sky-100 shadow-sm shadow-sky-100/60 px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-base font-extrabold text-slate-900 leading-none">{{ $jumlah['buka'] }}</p>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Buka Daftar</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-sky-100 shadow-sm shadow-sky-100/60 px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-base font-extrabold text-slate-900 leading-none">{{ $jumlah['tutup'] }}</p>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Ditutup</p>
            </div>
        </div>
    </div>

    {{-- CARDS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5 animate-fade-up" style="animation-delay:.1s">
        @forelse ($ekskuls as $ekskul)
            @php
                $logo = \App\Support\EkskulInfo::logo($ekskul->logo);
                $buka = $ekskul->is_open_recruitment;
            @endphp
            <div class="catalog-card group flex flex-col bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden hover:shadow-xl hover:shadow-sky-100 hover:border-sky-200 hover:-translate-y-1 transition-all duration-300">
                {{-- Card Header Strip --}}
                <div class="h-1.5 w-full {{ $buka ? 'bg-gradient-to-r from-sky-400 to-blue-500' : 'bg-slate-200' }}"></div>

                <div class="p-5 flex flex-col flex-1">
                    {{-- Top Row: Logo + Status --}}
                    <div class="flex items-start justify-between gap-3 mb-3">
                        @if ($logo)
                            <div class="w-12 h-12 rounded-xl bg-white border border-sky-100 p-1.5 flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                                <img src="{{ $logo }}" alt="Logo {{ $ekskul->nama_ekskul }}" class="w-full h-full object-contain">
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white font-extrabold flex items-center justify-center text-sm shadow-md shadow-sky-200 uppercase shrink-0 group-hover:scale-105 transition-transform">
                                {{ substr($ekskul->nama_ekskul, 0, 2) }}
                            </div>
                        @endif

                        @if ($buka)
                            <span class="recruitment-open-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-[10px] font-bold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                                Buka
                            </span>
                        @else
                            <span class="catalog-closed-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-50 border border-slate-200 text-slate-400 text-[10px] font-semibold shrink-0">
                                Ditutup
                            </span>
                        @endif
                    </div>

                    {{-- Name & Desc --}}
                    <h3 class="text-sm font-extrabold text-slate-900 group-hover:text-sky-600 transition-colors line-clamp-1 mb-1">
                        {{ $ekskul->nama_ekskul }}
                    </h3>
                    <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-2 flex-1">
                        {{ $ekskul->tagline ?: ($ekskul->deskripsi ?: 'Deskripsi belum tersedia.') }}
                    </p>

                    {{-- Meta Info --}}
                    <div class="mt-3 space-y-1.5">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-[11px] text-slate-500 truncate">{{ $ekskul->pembina->nama ?? 'Belum ditentukan' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-[11px] text-slate-500 truncate">{{ $ekskul->jadwal ?: 'Jadwal belum diatur' }}</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-4 pt-3.5 border-t border-sky-50 flex gap-2">
                        <a href="{{ route('ekskul.detail', $ekskul) }}"
                           class="flex-1 py-2 px-2 bg-white hover:bg-sky-50 text-slate-600 hover:text-sky-700 text-xs font-bold rounded-xl border border-slate-200 hover:border-sky-200 transition-all text-center">
                            Detail
                        </a>
                        @if ($bisaDaftar && $buka)
                            <a href="{{ route('siswa.form-daftar', $ekskul->id) }}"
                               class="flex-1 py-2 px-2 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 hover:shadow-lg hover:shadow-sky-200/70 transition-all text-center hover:-translate-y-0.5">
                                Daftar
                            </a>
                        @elseif ($ekskulAktif && property_exists($ekskulAktif, 'id') && $ekskulAktif->id === $ekskul->id)
                            <span class="flex-1 py-2 px-2 bg-amber-50 text-amber-700 text-xs font-bold rounded-xl border border-amber-200 text-center select-none flex items-center justify-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Ekskul Kamu
                            </span>
                        @elseif (!$buka)
                            <span class="catalog-unavailable flex-1 py-2 px-2 bg-slate-50 text-slate-400 text-xs font-semibold rounded-xl border border-slate-200 cursor-not-allowed text-center select-none">
                                Ditutup
                            </span>
                        @else
                            <span class="catalog-unavailable flex-1 py-2 px-2 bg-slate-50 text-slate-400 text-xs font-semibold rounded-xl border border-slate-200 cursor-not-allowed text-center select-none" title="Kamu sudah memiliki ekskul atau pendaftaran menunggu verifikasi">
                                Tidak Tersedia
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 p-10 md:p-16 text-center animate-fade-up">
                <div class="w-16 h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-extrabold text-slate-900 mb-1">
                    @if (request('cari') || $status)
                        Tidak ada ekskul yang cocok
                    @else
                        Belum ada ekskul di katalog
                    @endif
                </h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                    @if (request('cari'))
                        Tidak ditemukan ekskul dengan kata kunci "{{ request('cari') }}".
                    @elseif ($status === 'buka')
                        Belum ada ekskul yang membuka pendaftaran saat ini.
                    @elseif ($status === 'tutup')
                        Semua ekskul saat ini sedang membuka pendaftaran.
                    @else
                        Katalog ekskul akan terisi setelah data ditambahkan oleh pihak sekolah.
                    @endif
                </p>
                @if (request('cari') || $status)
                    <a href="{{ route('siswa.katalog') }}"
                       class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-200 hover:shadow-lg transition-all hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Reset Filter
                    </a>
                @endif
            </div>
        @endforelse
    </div>
@endsection
