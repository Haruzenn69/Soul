@extends('layouts.siswa')

@section('title', 'Katalog Ekskul')

@section('search')
    <form method="GET" action="{{ route('siswa.katalog') }}" class="relative w-full max-w-md hidden sm:block">
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </span>
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari ekskul..."
               class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
    </form>
@endsection

@section('content')
    @php
        $filters = [
            'semua' => ['label' => 'Semua', 'count' => $jumlah['semua'] ?? 0],
            'buka'  => ['label' => 'Dibuka', 'count' => $jumlah['buka'] ?? 0],
            'tutup' => ['label' => 'Ditutup', 'count' => $jumlah['tutup'] ?? 0],
        ];
        $ekskulAktifIds = ($ekskulAktifs ?? collect())->pluck('id')->all();
    @endphp

    {{-- PAGE HEADER (SIMPLE SEPERTI PAGE NILAI) --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 animate-fade-up">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Katalog Ekskul</h1>
            <p class="text-xs text-slate-500 mt-1 max-w-xl">
                Temukan ekskul yang cocok untuk kamu. Bebas memilih hingga maksimal {{ \App\Support\AturanEkskul::MAKS_EKSKUL }} ekskul (tidak harus ekskul wajib). Khusus ekskul kategori wajib, hanya dapat diikuti maksimal 1 ekskul.
            </p>
        </div>

        {{-- Status Pill Simple --}}
        <div class="shrink-0">
            @if ($menunggu)
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-xs font-semibold shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shrink-0"></span>
                    <span>Menunggu verifikasi ketua</span>
                </div>
            @elseif ($ekskulAktifs->isNotEmpty())
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-semibold shadow-sm max-w-xs sm:max-w-md">
                    <i class="fa-solid fa-circle-check text-emerald-500 shrink-0 text-sm"></i>
                    <span class="truncate">Terdaftar: <strong class="text-blue-600 font-bold">{{ $ekskulAktifs->pluck('nama_ekskul')->join(', ') }}</strong> ({{ $ekskulAktifs->count() }}/{{ \App\Support\AturanEkskul::MAKS_EKSKUL }})</span>
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-500 text-xs font-semibold shadow-sm">
                    <i class="fa-solid fa-circle-info text-slate-400 shrink-0 text-xs"></i>
                    <span>Belum mendaftar ekskul</span>
                </div>
            @endif
        </div>
    </div>

    {{-- FILTER + SEARCH BAR --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mb-5 animate-fade-up" style="animation-delay:.05s">
        {{-- Filter Tabs --}}
        <div class="flex items-center gap-1.5 bg-white border border-slate-200/80 rounded-2xl p-1.5 shadow-sm overflow-x-auto scrollbar-none">
            @foreach ($filters as $key => $item)
                @php
                    $isActive = ($status === $key) || ($status === null && $key === 'semua');
                    $href = route('siswa.katalog', array_filter(
                        array_merge(request()->except('status'), ['status' => $key === 'semua' ? null : $key]),
                        fn($v) => $v !== null && $v !== ''
                    ));
                @endphp
                <a href="{{ $href }}"
                   class="catalog-filter-tab flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap
                          {{ $isActive
                              ? 'bg-blue-600 text-white shadow-sm'
                              : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                    <span>{{ $item['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-lg text-[10px] font-extrabold
                                 {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
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
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama ekskul atau pembina..."
                       class="w-full pl-9 pr-4 py-2.5 bg-white border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all shadow-sm">
            </form>
            @if (request('cari') || $status)
                <a href="{{ route('siswa.katalog') }}"
                   class="flex items-center gap-1.5 px-3.5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 hover:text-slate-900 rounded-2xl text-xs font-semibold transition-all shadow-sm shrink-0"
                   title="Reset filter & pencarian">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    <span class="hidden sm:inline">Reset</span>
                </a>
            @endif
        </div>
    </div>

    {{-- STATS ROW --}}
    <div class="grid grid-cols-3 gap-3 mb-5 animate-fade-up" style="animation-delay:.08s">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center shrink-0 text-blue-600">
                <i class="fa-solid fa-cubes text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-base font-extrabold text-slate-900 leading-none">{{ $jumlah['semua'] }}</p>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Total Ekskul</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0 text-emerald-600">
                <i class="fa-solid fa-door-open text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-base font-extrabold text-slate-900 leading-none">{{ $jumlah['buka'] }}</p>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Buka Daftar</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm px-4 py-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 text-slate-500">
                <i class="fa-solid fa-door-closed text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-base font-extrabold text-slate-900 leading-none">{{ $jumlah['tutup'] }}</p>
                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Ditutup</p>
            </div>
        </div>
    </div>

    {{-- CARDS GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-4 md:gap-5 animate-fade-up" style="animation-delay:.1s">
        @forelse ($ekskuls as $ekskul)
            @php
                $logo = \App\Support\EkskulInfo::logo($ekskul->logo);
                $buka = $ekskul->is_open_recruitment;
            @endphp
            <div class="catalog-card group flex flex-col bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden hover:shadow-md hover:border-slate-300 hover:-translate-y-0.5 transition-all duration-200">
                {{-- Card Header Strip --}}
                <div class="h-1 w-full {{ $buka ? 'bg-blue-600' : 'bg-slate-200' }}"></div>

                <div class="p-3 sm:p-5 flex flex-col flex-1">
                    {{-- Top Row: Logo + Status --}}
                    <div class="flex items-start justify-between gap-3 mb-3">
                        @if ($logo)
                            <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-slate-50 border border-slate-200/80 p-1 sm:p-1.5 flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                                <img src="{{ $logo }}" alt="Logo {{ $ekskul->nama_ekskul }}" class="w-full h-full object-contain">
                            </div>
                        @else
                            <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-lg sm:rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center text-xs sm:text-sm shadow-sm uppercase shrink-0 group-hover:scale-105 transition-transform">
                                {{ substr($ekskul->nama_ekskul, 0, 2) }}
                            </div>
                        @endif

                        @if (in_array($ekskul->id, $ekskulAktifIds, true))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-[10px] font-bold shrink-0">
                                <i class="fa-solid fa-check text-[10px]"></i>
                                Ekskul Kamu
                            </span>
                        @elseif ($buka)
                            <span class="recruitment-open-badge inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse shrink-0"></span>
                                Buka
                            </span>
                        @else
                            <span class="catalog-closed-badge inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-500 text-[10px] font-semibold shrink-0">
                                Ditutup
                            </span>
                        @endif
                    </div>

                    {{-- Name & Desc --}}
                    <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition-colors line-clamp-1">
                            {{ $ekskul->nama_ekskul }}
                        </h3>
                        @if ($ekskul->isWajib())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                Wajib
                            </span>
                        @elseif ($ekskul->kategori)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/60">
                                {{ $ekskul->kategori }}
                            </span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-2 flex-1">
                        {{ $ekskul->tagline ?: ($ekskul->deskripsi ?: 'Deskripsi belum tersedia.') }}
                    </p>

                    {{-- Meta Info --}}
                    <div class="mt-3 space-y-1.5">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-user-tie text-slate-400 shrink-0 text-xs"></i>
                            <span class="text-[11px] text-slate-500 truncate">{{ $ekskul->pembina->nama ?? 'Belum ditentukan' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-clock text-slate-400 shrink-0 text-xs"></i>
                            <span class="text-[11px] text-slate-500 truncate">{{ $ekskul->jadwal ?: 'Jadwal belum diatur' }}</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    @php $alasanEkskul = $alasanPerEkskul[$ekskul->id] ?? null; @endphp
                    <div class="mt-4 pt-3.5 border-t border-slate-100 flex flex-col gap-2">
                        <div class="flex gap-2">
                            <a href="{{ route('ekskul.detail', $ekskul) }}"
                               class="flex-1 py-2 px-1 sm:px-2 bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 text-[10px] sm:text-xs font-semibold rounded-lg sm:rounded-xl border border-slate-200 transition-all text-center">
                                Detail
                            </a>
                            @if (in_array($ekskul->id, $ekskulAktifIds, true))
                                <span class="flex-1 py-2 px-1 sm:px-2 bg-blue-50 text-blue-700 text-[9px] sm:text-xs font-bold rounded-lg sm:rounded-xl border border-blue-200 text-center select-none flex items-center justify-center gap-1">
                                    <i class="fa-solid fa-check text-[9px] sm:text-xs"></i>
                                    Ekskul Kamu
                                </span>
                            @elseif (! $buka)
                                <span class="catalog-unavailable flex-1 py-2 px-1 sm:px-2 bg-slate-50 text-slate-400 text-[9px] sm:text-xs font-semibold rounded-lg sm:rounded-xl border border-slate-200 cursor-not-allowed text-center select-none">
                                    Ditutup
                                </span>
                            @elseif ($alasanEkskul === null)
                                <a href="{{ route('siswa.form-daftar', $ekskul->id) }}"
                                   class="flex-1 py-2 px-1 sm:px-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] sm:text-xs font-bold rounded-lg sm:rounded-xl shadow-sm transition-all text-center">
                                    Daftar
                                </a>
                            @else
                                <span class="catalog-unavailable flex-1 py-2 px-1 sm:px-2 bg-slate-50 text-slate-400 text-[9px] sm:text-xs font-semibold rounded-lg sm:rounded-xl border border-slate-200 cursor-not-allowed text-center select-none" title="{{ $alasanEkskul }}">
                                    Tidak Tersedia
                                </span>
                            @endif
                        </div>
                        @if (! in_array($ekskul->id, $ekskulAktifIds, true) && $buka && $alasanEkskul !== null)
                            <p class="text-[10px] text-center text-amber-600 leading-relaxed">{{ $alasanEkskul }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200/80 shadow-sm p-10 md:p-16 text-center animate-fade-up">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-magnifying-glass text-2xl text-slate-400"></i>
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
                       class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        Reset Filter
                    </a>
                @endif
            </div>
        @endforelse
    </div>
@endsection
