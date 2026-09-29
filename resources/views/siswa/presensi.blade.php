@extends('layouts.siswa')

@section('title', 'Presensi & Kegiatan')

@section('search')
    <form method="GET" action="{{ route('siswa.presensi') }}" class="relative w-full max-w-md hidden sm:block">
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </span>
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari presensi..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
    </form>
@endsection

@section('content')
    <div class="animate-fade-up">
        <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900">Riwayat Presensi</h1>
        <p class="text-xs text-slate-400 mt-1 font-medium">Catatan kehadiranmu di setiap kegiatan ekskul</p>
    </div>

    <!-- Filter Bulan -->
    <form method="GET" action="{{ route('siswa.presensi') }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 flex flex-wrap items-center gap-3 animate-fade-up" style="animation-delay: .1s">
        @if(request('cari'))
            <input type="hidden" name="cari" value="{{ request('cari') }}">
        @endif
        <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Filter Bulan
        </span>
        <select name="bulan" class="px-3 py-1.5 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
            <option value="" {{ request('bulan') === null ? 'selected' : '' }}>Semua Bulan</option>
            <option value="2026-08" {{ request('bulan') === '2026-08' ? 'selected' : '' }}>Agustus 2026</option>
            <option value="2026-07" {{ request('bulan') === '2026-07' ? 'selected' : '' }}>Juli 2026</option>
            <option value="2026-06" {{ request('bulan') === '2026-06' ? 'selected' : '' }}>Juni 2026</option>
            <option value="2026-05" {{ request('bulan') === '2026-05' ? 'selected' : '' }}>Mei 2026</option>
        </select>
        <button type="submit" class="px-4 py-1.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 transition-all hover:-translate-y-0.5">Terapkan</button>
        <span class="ml-auto text-xs font-semibold text-slate-400 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">Menampilkan {{ count($presensis) }} data</span>
    </form>

    <!-- Statistik Presensi -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <!-- Card 1: Hadir -->
        <div class="attendance-stat attendance-stat--present relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 p-4 md:p-5 shadow-lg shadow-emerald-100/60 hover:-translate-y-1 transition-all duration-300 animate-fade-up" style="animation-delay: .15s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Hadir</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-emerald-600 mt-1 md:mt-1.5">{{ $presensis->where('status', 'hadir')->count() }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-emerald-200">H</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-emerald-700 mt-0.5 md:mt-1">Kegiatan</p>
        </div>

        <!-- Card 2: Izin -->
        <div class="attendance-stat attendance-stat--excused relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 p-4 md:p-5 shadow-lg shadow-amber-100/60 hover:-translate-y-1 transition-all duration-300 animate-fade-up" style="animation-delay: .2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Izin</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-amber-600 mt-1 md:mt-1.5">{{ $presensis->where('status', 'izin')->count() }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-amber-200">I</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-amber-700 mt-0.5 md:mt-1">Kegiatan</p>
        </div>

        <!-- Card 3: Sakit -->
        <div class="attendance-stat attendance-stat--sick relative overflow-hidden rounded-3xl bg-gradient-to-br from-violet-50 to-white border border-violet-100 p-4 md:p-5 shadow-lg shadow-violet-100/60 hover:-translate-y-1 transition-all duration-300 animate-fade-up" style="animation-delay: .25s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Sakit</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-violet-600 mt-1 md:mt-1.5">{{ $presensis->where('status', 'sakit')->count() }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-violet-400 to-purple-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-violet-200">S</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-violet-700 mt-0.5 md:mt-1">Kegiatan</p>
        </div>

        <!-- Card 4: Alpha -->
        <div class="attendance-stat attendance-stat--absent relative overflow-hidden rounded-3xl bg-gradient-to-br from-rose-50 to-white border border-rose-100 p-4 md:p-5 shadow-lg shadow-rose-100/60 hover:-translate-y-1 transition-all duration-300 animate-fade-up" style="animation-delay: .3s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Alpha</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-rose-600 mt-1 md:mt-1.5">{{ $presensis->where('status', 'alpha')->count() }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-rose-400 to-red-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-rose-200">A</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-rose-700 mt-0.5 md:mt-1">Kegiatan</p>
        </div>
    </div>

    <!-- Daftar Presensi -->
    <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .35s">
        <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Riwayat Kegiatan</h2>
                <p class="text-[11px] text-slate-400 mt-0.5">Daftar lengkap kehadiranmu</p>
            </div>
            <span class="text-xs font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">Total {{ count($presensis) }} kegiatan</span>
        </div>

        @if(count($presensis) > 0)
            <div class="divide-y divide-sky-50">
                @foreach($presensis->sortByDesc('kegiatan.tanggal_kegiatan') as $presensi)
                    @php
                        $statusColors = [
                            'hadir' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            'izin' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'sakit' => 'bg-violet-100 text-violet-700 border-violet-200',
                            'alpha' => 'bg-rose-100 text-rose-700 border-rose-200'
                        ];
                        $statusLabels = [
                            'hadir' => 'Hadir',
                            'izin' => 'Izin',
                            'sakit' => 'Sakit',
                            'alpha' => 'Alpha'
                        ];
                        $dayNum = \Carbon\Carbon::parse($presensi->kegiatan->tanggal_kegiatan)->format('d');
                        $dayMon = \Carbon\Carbon::parse($presensi->kegiatan->tanggal_kegiatan)->format('M');
                    @endphp
                    <div class="px-6 py-4 hover:bg-sky-50/50 transition-colors">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-10 h-10 shrink-0 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex flex-col items-center justify-center shadow-sm shadow-sky-200">
                                    <span class="text-xs font-extrabold leading-none">{{ $dayNum }}</span>
                                    <span class="text-[7px] font-bold uppercase leading-tight opacity-80">{{ $dayMon }}</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs font-bold text-slate-800 truncate">
                                            {{ $presensi->kegiatan->materi ?? 'Kegiatan' }}
                                        </h4>
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border {{ $statusColors[$presensi->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                            {{ $statusLabels[$presensi->status] ?? $presensi->status }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 mt-1 text-[11px] text-slate-500">
                                        <span>{{ \Carbon\Carbon::parse($presensi->kegiatan->tanggal_kegiatan)->isoFormat('dddd, DD MMM Y') }}</span>
                                        @if($presensi->kegiatan->ekskul)
                                            <span class="text-sky-600 font-semibold">&middot; {{ $presensi->kegiatan->ekskul->nama_ekskul ?? '' }}</span>
                                        @endif
                                    </div>
                                    @if($presensi->keterangan)
                                        <p class="text-[11px] text-slate-500 mt-1 italic">{{ $presensi->keterangan }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right text-[10px] text-slate-400 whitespace-nowrap shrink-0">
                                @if($presensi->created_at)
                                    Dicatat {{ \Carbon\Carbon::parse($presensi->created_at)->isoFormat('DD MMM Y HH:mm') }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-slate-500">Belum ada riwayat presensi</p>
                <p class="text-[11px] text-slate-400 mt-1">Kamu belum memiliki catatan kehadiran di kegiatan ekskul</p>
            </div>
        @endif
    </div>
@endsection
