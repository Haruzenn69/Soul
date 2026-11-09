@extends('pembina.layout')
@section('title', 'Dashboard Pembina')

@section('content')
    {{-- HERO CARD --}}
    <div data-tilt-card class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-4 md:p-6 md:pb-12 lg:pb-14 text-white soul-timbul tilt-card animate-fade-up">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative grid md:grid-cols-5 gap-4 lg:gap-6 items-center">
            {{-- TEXT + CTA --}}
            <div class="md:col-span-3">
                <h1 class="text-xl md:text-2xl font-extrabold tracking-tight leading-tight">
                    Halo, {{ $pembina->nama ?? 'Pembina' }}
                </h1>
                <p class="text-sm text-white/85 mt-1 font-medium">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </p>
                <p class="text-xs text-white/70 mt-2 max-w-lg leading-relaxed">
                    Pantau keanggotaan, presensi, dan laporan ekskul binaanmu dengan mudah di sini.
                </p>

                <div class="flex gap-2 flex-wrap mt-4">
                    <a href="{{ route('pembina.anggota') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white text-sky-700 font-bold text-xs rounded-xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                        <i class="fa-solid fa-users text-xs"></i>
                        Kelola Anggota
                    </a>
                    <a href="{{ route('pembina.presensi') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-xl hover:bg-white/20 transition-all">
                        <i class="fa-solid fa-calendar-check text-xs"></i>
                        Lihat Presensi
                    </a>
                </div>
            </div>

            {{-- ILLUSTRATIVE PANEL + FLOATING STATS --}}
            <div class="md:col-span-2 relative hidden md:block">
                <div class="relative h-36 md:h-40 lg:h-44 rounded-3xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center">
                    <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
                        <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-white/20 blur-2xl"></div>
                    </div>
                    <img src="{{ asset('images/default.png') }}" alt="Ilustrasi Pembina"
                         class="h-36 md:h-40 lg:h-44 w-auto max-w-[110px] md:max-w-[130px] lg:max-w-[150px] object-contain drop-shadow-xl animate-floaty select-none relative z-0 mb-1 md:mb-2 pointer-events-none">
                </div>

                {{-- floating stat card overlapping bottom edge --}}
                <div class="absolute -bottom-6 md:-bottom-7 left-2 right-2 z-10 grid grid-cols-3 gap-1 bg-white dark:bg-[#1a1a1a] border border-slate-100 dark:border-neutral-800 rounded-xl shadow-xl shadow-sky-900/10 p-2">
                    <div class="text-center border-r border-slate-100 dark:border-neutral-800 pr-1">
                        <div class="flex items-center justify-center gap-1">
                            <i class="fa-solid fa-shapes text-sky-600 dark:text-sky-400 text-xs"></i>
                            <p class="text-sm md:text-base font-extrabold text-sky-700 dark:text-sky-400 leading-none">{{ $ekskuls->count() }}</p>
                        </div>
                        <p class="text-[10px] md:text-xs text-slate-400 font-semibold mt-1">Ekskul</p>
                    </div>
                    <div class="text-center border-r border-slate-100 dark:border-neutral-800 px-1">
                        <div class="flex items-center justify-center gap-1">
                            <i class="fa-solid fa-users text-sky-600 dark:text-sky-400 text-xs"></i>
                            <p class="text-sm md:text-base font-extrabold text-sky-700 dark:text-sky-400 leading-none">{{ $anggotaAktifCount ?? 0 }}</p>
                        </div>
                        <p class="text-[10px] md:text-xs text-slate-400 font-semibold mt-1">Anggota</p>
                    </div>
                    <div class="text-center pl-1">
                        <div class="flex items-center justify-center gap-1">
                            <i class="fa-solid fa-user-clock text-amber-500 text-xs"></i>
                            <p class="text-sm md:text-base font-extrabold text-amber-600 dark:text-amber-400 leading-none">{{ count($pendaftaranPending ?? []) }}</p>
                        </div>
                        <p class="text-[10px] md:text-xs text-slate-400 font-semibold mt-1">Pending</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions: Anggota, Presensi, Laporan --}}
        <div class="relative mt-4 pt-3 md:mt-11 lg:mt-12 border-t border-white/15 grid grid-cols-1 sm:grid-cols-3 gap-2">
            <a href="{{ route('pembina.anggota') }}" class="group flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-users text-sm text-white"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none text-white">Anggota</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Data Keanggotaan</p>
                </div>
            </a>
            <a href="{{ route('pembina.presensi') }}" class="group flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-calendar-check text-sm text-white"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none text-white">Presensi</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Rekap Kehadiran</p>
                </div>
            </a>
            <a href="{{ route('pembina.laporan.index') }}" class="group flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-file-lines text-sm text-white"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none text-white">Laporan</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Cetak & Approve</p>
                </div>
            </a>
        </div>
    </div>

    {{-- STATS CARDS - MOBILE ONLY --}}
    <div class="grid grid-cols-3 gap-2.5 md:hidden">
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#1a1a1a] border border-sky-100 dark:border-neutral-800 p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Ekskul</p>
                <i class="fa-solid fa-shapes text-sky-500 text-xs"></i>
            </div>
            <h3 class="text-lg font-extrabold mt-0.5 text-sky-700 dark:text-sky-400">{{ $ekskuls->count() }}</h3>
            <p class="text-[10px] font-semibold {{ $ekskuls->count() > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">{{ $ekskuls->count() > 0 ? 'Aktif' : 'Belum' }}</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#1a1a1a] border border-sky-100 dark:border-neutral-800 p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Anggota</p>
                <i class="fa-solid fa-users text-sky-500 text-xs"></i>
            </div>
            <h3 class="text-lg font-extrabold mt-0.5 text-sky-700 dark:text-sky-400">{{ $anggotaAktifCount ?? 0 }}</h3>
            <p class="text-[10px] font-semibold text-sky-600 dark:text-sky-400">Siswa Aktif</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#1a1a1a] border border-sky-100 dark:border-neutral-800 p-3 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Pending</p>
                <i class="fa-solid fa-user-clock text-amber-500 text-xs"></i>
            </div>
            <h3 class="text-lg font-extrabold mt-0.5 text-sky-700 dark:text-sky-400">{{ count($pendaftaranPending ?? []) }}</h3>
            <p class="text-[10px] font-semibold text-amber-600 dark:text-amber-400">Menunggu</p>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- CARD ANGGOTA (EKSKUL PER BIDANG) --}}
            @if(($ekskulsByBidang ?? collect())->isNotEmpty())
                @foreach($ekskulsByBidang as $bidangNama => $ekskulBidangList)
                @php
                    $badgeClass = \App\Support\EkskulInfo::bidangBadgeClass($bidangNama);
                    $totalAktifBidang = 0;
                    foreach($ekskulBidangList as $eksB) {
                        $totalAktifBidang += \App\Models\Pendaftaran::where('ekskul_id', $eksB->id)->where('status', 'diterima')->count();
                    }
                @endphp
                <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up" style="animation-delay: .15s">
                    <div class="px-5 py-4 flex items-center justify-between border-b border-sky-50 dark:border-neutral-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs shrink-0 border border-sky-100 dark:border-sky-900/50">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Anggota · Bidang {{ $bidangNama }}</h2>
                                    <span class="px-2 py-0.5 rounded-lg border text-[10px] font-bold {{ $badgeClass }}">{{ $bidangNama }}</span>
                                </div>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $ekskulBidangList->count() }} ekskul · {{ $totalAktifBidang }} anggota aktif</p>
                            </div>
                        </div>
                        <a href="{{ route('pembina.anggota') }}" class="text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700 inline-flex items-center gap-1">
                            Kelola <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="divide-y divide-sky-50 dark:divide-neutral-800">
                        @foreach($ekskulBidangList as $eksB)
                        @php
                            $ketuaEksB = $eksB->ketua();
                            $anggotaCount = \App\Models\Pendaftaran::where('ekskul_id', $eksB->id)->where('status', 'diterima')->count();
                        @endphp
                        <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-950/30 flex items-center justify-center text-sky-700 dark:text-sky-400 font-extrabold text-xs shrink-0 border border-sky-100 dark:border-sky-800/50">
                                    {{ strtoupper(substr($eksB->nama_ekskul, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-extrabold text-slate-900 dark:text-white truncate">{{ $eksB->nama_ekskul }}</h4>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        <i class="fa-solid fa-user-group text-[10px] mr-1 text-slate-400"></i>{{ $anggotaCount }} Anggota
                                        @if($ketuaEksB) · Ketua: <span class="font-semibold text-sky-700 dark:text-sky-400">{{ $ketuaEksB->nama }}</span>@endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($eksB->status)
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50">Aktif</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-slate-50 dark:bg-neutral-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-neutral-700">Nonaktif</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @else
                <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm p-10 text-center animate-fade-up">
                    <div class="mx-auto w-14 h-14 rounded-2xl bg-sky-50 dark:bg-sky-950/30 flex items-center justify-center text-sky-300 dark:text-sky-600 border border-sky-100 dark:border-sky-800/50 mb-3">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Belum ada ekskul binaan</p>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Hubungi kesiswaan untuk mendapatkan binaan ekskul</p>
                </div>
            @endif

            {{-- CARD PRESENSI (KEGIATAN MENDATANG) --}}
            <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up" style="animation-delay: .2s">
                <div class="px-5 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs shrink-0 border border-sky-100 dark:border-sky-900/50">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Presensi & Kegiatan</h2>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Jadwal kegiatan ekskul mendatang</p>
                        </div>
                    </div>
                    <a href="{{ route('pembina.presensi') }}" class="text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700 inline-flex items-center gap-1">
                        Lihat Semua <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                @if(($kegiatanMendatang ?? collect())->count() > 0)
                    <div class="divide-y divide-sky-50 dark:divide-neutral-800">
                        @foreach($kegiatanMendatang as $index => $kegiatan)
                            <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 shrink-0 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex flex-col items-center justify-center shadow-sm shadow-sky-200 dark:shadow-sky-900/30">
                                        <span class="text-xs font-extrabold leading-none">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('d') }}</span>
                                        <span class="text-[7px] font-bold uppercase leading-tight opacity-80">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('M') }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-slate-800 dark:text-white truncate block">{{ $kegiatan->materi ?? 'Kegiatan' }}</span>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $kegiatan->ekskul->nama_ekskul ?? 'Ekskul' }} · {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('dddd, DD MMM') }}</p>
                                    </div>
                                </div>
                                @if($index === 0)
                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 text-amber-700 dark:text-amber-300 text-[10px] font-bold shrink-0">Terdekat</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-sky-50 dark:bg-sky-950/30 flex items-center justify-center text-sky-300 dark:text-sky-600 border border-sky-100 dark:border-sky-800/50">
                            <i class="fa-solid fa-calendar-xmark text-xl"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-3">Belum ada agenda mendatang</p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Agenda ekskul akan muncul di sini</p>
                    </div>
                @endif
            </div>

            {{-- 2-COL GRID: PELATIH + LAPORAN --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- PELATIH EKSKUL --}}
                <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up" style="animation-delay: .25s">
                    <div class="px-5 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs shrink-0">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Pelatih Ekskul</h2>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Kelola pelatih terverifikasi</p>
                            </div>
                        </div>
                        <a href="{{ route('pembina.pelatih.index') }}" class="text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700 inline-flex items-center gap-1">
                            Kelola <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    @forelse($ekskuls ?? collect() as $ekskulBinaan)
                        <div class="p-4">
                            <form method="POST" action="{{ route('pembina.ekskul.pelatih', $ekskulBinaan) }}" class="p-3.5 bg-slate-50 dark:bg-neutral-900/50 rounded-xl border border-slate-100 dark:border-neutral-700">
                                @csrf
                                @method('PATCH')
                                <p class="text-xs font-bold text-slate-800 dark:text-white mb-1">{{ $ekskulBinaan->nama_ekskul }}</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mb-3">Pelatih saat ini: <span class="font-bold text-slate-600 dark:text-slate-400">{{ $ekskulBinaan->pelatih?->nama ?? '-' }}</span></p>
                                <div class="flex gap-2">
                                    <select name="pelatih_id"
                                        class="flex-1 min-w-0 px-3 py-2.5 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition">
                                        <option value="" {{ $ekskulBinaan->pelatih_id ? '' : 'selected' }}>Tanpa pelatih</option>
                                        @foreach ($pelatihs as $pl)
                                            <option value="{{ $pl->id }}" {{ $ekskulBinaan->pelatih_id == $pl->id ? 'selected' : '' }}>{{ $pl->nama }} ({{ $pl->status }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="shrink-0 px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <div class="mx-auto w-14 h-14 rounded-2xl bg-sky-50 dark:bg-sky-950/30 flex items-center justify-center text-sky-300 dark:text-sky-600 border border-sky-100 dark:border-sky-800/50">
                                <i class="fa-solid fa-user-tie text-xl"></i>
                            </div>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-3">Belum ada ekskul binaan</p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Hubungi kesiswaan untuk mendapatkan binaan</p>
                        </div>
                    @endforelse
                </div>

                {{-- CARD LAPORAN (LAPORAN BULANAN) --}}
                <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up" style="animation-delay: .3s">
                    <div class="px-5 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs shrink-0">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Laporan Bulanan</h2>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Draft laporan ekskul</p>
                            </div>
                        </div>
                        <a href="{{ route('pembina.laporan.index') }}" class="text-[11px] font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700 inline-flex items-center gap-1">
                            Cetak <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                    <div class="p-4">
                        @forelse($laporanDraft ?? [] as $laporan)
                            <div class="p-3.5 bg-slate-50 dark:bg-neutral-900/50 rounded-xl border border-slate-100 dark:border-neutral-700 flex items-center justify-between mb-3 gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center text-xs font-extrabold shadow-sm shadow-sky-200 dark:shadow-sky-900/30 shrink-0">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->format('m') }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->translatedFormat('F Y') }}</h4>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $laporan->ekskul->nama_ekskul ?? 'Ekskul' }}</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 text-[10px] font-bold rounded-full border border-amber-200 dark:border-amber-800/50 shrink-0">Draft</span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="mx-auto w-14 h-14 rounded-2xl bg-sky-50 dark:bg-sky-950/30 flex items-center justify-center text-sky-300 dark:text-sky-600 border border-sky-100 dark:border-sky-800/50">
                                    <i class="fa-solid fa-file-lines text-xl"></i>
                                </div>
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-3">Belum ada laporan</p>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Laporan bulanan akan muncul di sini</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="space-y-6">

            {{-- TESTIMONI & FAQ --}}
            <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up" style="animation-delay: .2s">
                <div class="px-5 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Testimoni & FAQ</h2>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Moderasi kontribusi siswa</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 grid grid-cols-2 gap-3">
                    <a href="{{ route('pembina.testimoni.index') }}" class="p-3.5 bg-slate-50 dark:bg-neutral-900/50 rounded-xl border border-slate-100 dark:border-neutral-700 hover:border-sky-200 dark:hover:border-neutral-600 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400">Testimoni</span>
                            @if($testimoniPendingCount > 0)
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            @endif
                        </div>
                        <p class="text-2xl font-extrabold text-sky-700 dark:text-sky-400 mt-1">{{ $testimoniPendingCount }}</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $testimoniPendingCount > 0 ? 'menunggu persetujuan' : 'tidak ada antrian' }}</p>
                    </a>
                    <a href="{{ route('pembina.faq.index') }}" class="p-3.5 bg-slate-50 dark:bg-neutral-900/50 rounded-xl border border-slate-100 dark:border-neutral-700 hover:border-sky-200 dark:hover:border-neutral-600 transition">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400">FAQ</span>
                            @if($faqPendingCount > 0)
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            @endif
                        </div>
                        <p class="text-2xl font-extrabold text-sky-700 dark:text-sky-400 mt-1">{{ $faqPendingCount }}</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $faqPendingCount > 0 ? 'menunggu jawaban' : 'tidak ada antrian' }}</p>
                    </a>
                </div>

                @if($ekskuls->isNotEmpty())
                <div class="px-4 pb-4 space-y-4">
                    <form action="{{ route('pembina.testimoni.store') }}" method="POST" class="p-3.5 bg-slate-50 dark:bg-neutral-900/50 rounded-xl border border-slate-100 dark:border-neutral-700 space-y-2.5">
                        @csrf
                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Tambah Testimoni</p>
                        <select name="ekskul_id" required
                            class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition">
                            @foreach($ekskuls as $ex)
                                <option value="{{ $ex->id }}">{{ $ex->nama_ekskul }}</option>
                            @endforeach
                        </select>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="nama" required placeholder="Nama"
                                class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition">
                            <input type="text" name="kelas" placeholder="Kelas (opsional)"
                                class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition">
                        </div>
                        <textarea name="quote" rows="2" required placeholder="Isi testimoni..."
                            class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition"></textarea>
                        <button type="submit" class="px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-[10px] font-bold rounded-xl transition">Simpan Testimoni</button>
                    </form>

                    <form action="{{ route('pembina.faq.store') }}" method="POST" class="p-3.5 bg-slate-50 dark:bg-neutral-900/50 rounded-xl border border-slate-100 dark:border-neutral-700 space-y-2.5">
                        @csrf
                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300">Tambah FAQ</p>
                        <select name="ekskul_id" required
                            class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition">
                            @foreach($ekskuls as $ex)
                                <option value="{{ $ex->id }}">{{ $ex->nama_ekskul }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="pertanyaan" required placeholder="Pertanyaan umum..."
                            class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition">
                        <textarea name="jawaban" rows="2" required placeholder="Jawaban..."
                            class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition"></textarea>
                        <button type="submit" class="px-3.5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-[10px] font-bold rounded-xl transition">Simpan FAQ</button>
                    </form>
                </div>
                @endif
            </div>

            {{-- PENDAFTARAN SISWA --}}
            <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up" style="animation-delay: .25s">
                <div class="px-5 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950/40 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs shrink-0">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">Pendaftaran Siswa</h2>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">Menunggu verifikasi</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-amber-600 dark:text-amber-400">{{ count($pendaftaranPending ?? []) }} Pending</span>
                </div>

                @if(isset($pendaftaranPending) && count($pendaftaranPending) > 0)
                    <div class="p-4 space-y-3">
                        @foreach($pendaftaranPending as $item)
                        <div class="p-3.5 bg-slate-50 dark:bg-neutral-900/50 rounded-xl flex items-center justify-between border border-slate-100 dark:border-neutral-700 gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 text-white font-extrabold flex items-center justify-center text-xs uppercase shadow-sm shadow-sky-200 dark:shadow-sky-900/30 shrink-0">
                                    {{ substr($item->siswa->nama ?? 'A', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $item->siswa->nama ?? '-' }}</h4>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500">{{ $item->siswa->kelas->nama ?? '-' }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 text-[10px] font-bold rounded-full border border-amber-200 dark:border-amber-800/50 shrink-0">Pending</span>
                        </div>
                        @endforeach
                        <div class="p-3 bg-sky-50 dark:bg-sky-950/20 rounded-xl border border-sky-100 dark:border-sky-800/50">
                            <p class="text-xs text-sky-700 dark:text-sky-300">Verifikasi pendaftaran dilakukan oleh ketua ekskul.</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-sky-50 dark:bg-sky-950/30 flex items-center justify-center text-sky-300 dark:text-sky-600 border border-sky-100 dark:border-sky-800/50">
                            <i class="fa-solid fa-clipboard-check text-xl"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-3">Tidak ada pendaftaran baru</p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Semua pendaftaran sudah diproses</p>
                    </div>
                @endif
            </div>

        </div>

    </div>

@push('scripts')
<script>
    document.querySelectorAll('[data-tilt-card]').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const midX = rect.width / 2;
            const midY = rect.height / 2;
            const rotateY = ((x - midX) / midX) * 4;
            const rotateX = ((midY - y) / midY) * 4;
            card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.01,1.01,1.01)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(800px) rotateX(0) rotateY(0) scale3d(1,1,1)';
        });
    });
</script>
@endpush
@endsection
