@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

@section('search')
    <form method="GET" action="{{ route('siswa.katalog') }}" class="relative w-full max-w-md hidden sm:block">
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
        </span>
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari ekskul, siswa, kegiatan..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition-all">
    </form>
@endsection

@section('content')
    @php
        $pendaftaranStatus = null;
        $pendaftaranMessage = '';
        $pendaftaranColor = '';
        $pendaftaranSoft = '';
        $pendaftaranIcon = '';

        if ($siswa) {
            $pending = $siswa->pendingPendaftaran();
            $diterima = $pendaftaran ?? $siswa->resolveActivePendaftaran();
            $ditolak = $siswa->pendaftarans()->where('status', \App\Models\Pendaftaran::STATUS_DITOLAK)->latest('id')->first();

            // Hanya tampilkan alert jika berstatus pending atau ditolak (jika diterima, sudah tampil jelas di card ekskul aktif)
            if ($pending) {
                $pendaftaranStatus = 'pending';
                $pendaftaranMessage = 'Kamu sudah mengajukan pendaftaran ke ekskul '.$pending->ekskul->nama_ekskul.'. Menunggu persetujuan ketua.';
                $pendaftaranColor = 'from-amber-400 to-yellow-500';
                $pendaftaranSoft = 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-950/30 dark:border-amber-900/50 dark:text-amber-300';
                $pendaftaranIcon = '<i class="fa-solid fa-clock text-amber-900 text-base"></i>';
            } elseif ($ditolak && !$diterima) {
                $pendaftaranStatus = 'ditolak';
                $pendaftaranMessage = 'Pendaftaran kamu ke ekskul '.$ditolak->ekskul->nama_ekskul.' belum disetujui.';
                $pendaftaranColor = 'from-rose-500 to-red-600';
                $pendaftaranSoft = 'bg-rose-50 border-rose-200 text-rose-800 dark:bg-rose-950/30 dark:border-rose-900/50 dark:text-rose-300';
                $pendaftaranIcon = '<i class="fa-solid fa-xmark text-white text-base"></i>';
            }
        }

        // Signature blue hero gradient & shadow matching all other dashboards
        $heroGrad = 'from-sky-400 via-blue-400 to-blue-600';
        $heroShadow = 'shadow-sky-200';
    @endphp

    {{-- HERO CARD --}}
    <div data-tilt-card class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-4 md:p-6 md:pb-12 lg:pb-14 text-white soul-timbul tilt-card animate-fade-up">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative grid md:grid-cols-5 gap-4 lg:gap-6 items-center">
            <!-- TEXT + CTA -->
            <div class="md:col-span-3">
                <h1 class="text-xl md:text-2xl font-extrabold tracking-tight leading-tight">
                    Halo, {{ $siswa->nama ?? auth()->user()->username }}
                </h1>
                <p class="text-sm text-white/85 mt-1 font-medium">
                    {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
                <p class="text-xs text-white/70 mt-2 max-w-lg leading-relaxed">
                    @if($hasMultipleEkskul && $ekskul)
                        Menampilkan dashboard untuk ekskul <strong>{{ $ekskul->nama_ekskul }}</strong>. Gunakan tombol switch untuk berpindah dashboard ekskul.
                    @else
                        Selamat datang kembali di portal ekskul SOUL. Pantau kegiatan, kehadiran, dan pengajuan ekskulmu di sini.
                    @endif
                </p>

                <div class="flex gap-2 flex-wrap mt-4">
                    @if(isset($activeEkskuls) && $activeEkskuls->isNotEmpty())
                        <a href="{{ route('siswa.presensi', $ekskul ? ['ekskul' => $ekskul->id] : []) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white text-sky-700 font-bold text-xs rounded-xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                            Lihat Presensi
                        </a>
                    @else
                        <a href="{{ route('siswa.daftar-ekskul') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white text-sky-700 font-bold text-xs rounded-xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                            <i class="fa-solid fa-plus text-xs"></i>
                            Daftar Ekskul
                        </a>
                    @endif
                    <a href="{{ route('siswa.katalog') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-xl hover:bg-white/20 transition-all">
                        <i class="fa-solid fa-book-open text-xs"></i>
                        Jelajahi Katalog
                    </a>
                </div>
            </div>

            <!-- ILLUSTRATIVE PANEL + FLOATING STATS -->
            <div class="md:col-span-2 relative hidden md:block">
                <div class="relative h-36 md:h-40 lg:h-44 rounded-3xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center">
                    <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
                        <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-white/20 blur-2xl"></div>
                    </div>
                    <img src="{{ asset('images/default.png') }}" alt="Ilustrasi Siswa"
                         class="h-36 md:h-40 lg:h-44 w-auto max-w-[110px] md:max-w-[130px] lg:max-w-[150px] object-contain drop-shadow-xl animate-floaty select-none relative z-0 mb-1 md:mb-2 pointer-events-none">
                </div>

                <!-- floating stat card overlapping bottom edge -->
                <div class="absolute -bottom-6 md:-bottom-7 left-2 right-2 z-10 grid grid-cols-3 gap-1 bg-white rounded-xl shadow-xl shadow-sky-900/10 p-2">
                    <div class="text-center border-r border-slate-100 pr-1">
                        <p class="text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $hasMultipleEkskul ? 1 : $activeEkskuls->count() }}</p>
                        <p class="text-[10px] md:text-xs text-slate-400 font-semibold mt-1">Ekskul</p>
                    </div>
                    <div class="text-center border-r border-slate-100 px-1">
                        <p class="text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $totalHadir ?? 0 }}</p>
                        <p class="text-[10px] md:text-xs text-slate-400 font-semibold mt-1">Hadir</p>
                    </div>
                    <div class="text-center pl-1">
                        <p class="text-sm md:text-base font-extrabold text-sky-700 leading-none">{{ $kegiatanMendatang->count() }}</p>
                        <p class="text-[10px] md:text-xs text-slate-400 font-semibold mt-1">Agenda</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="relative mt-4 pt-3 md:mt-11 lg:mt-12 border-t border-white/15 grid grid-cols-1 sm:grid-cols-3 gap-2">
            <a href="{{ route('siswa.presensi', $ekskul ? ['ekskul' => $ekskul->id] : []) }}" class="group flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-calendar-check text-sm text-white"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none text-white">Presensi</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Jadwal & Absen</p>
                </div>
            </a>
            <a href="{{ route('siswa.rekap', $ekskul ? ['ekskul' => $ekskul->id] : []) }}" class="group flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-chart-simple text-sm text-white"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none text-white">Rekap Absensi</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Riwayat Kehadiran</p>
                </div>
            </a>
            <a href="{{ route('siswa.katalog') }}" class="group flex items-center gap-2 px-3 py-2 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-book-open text-sm text-white"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none text-white">Katalog</p>
                    <p class="text-xs text-white/70 mt-1 leading-none">Cari Ekskul</p>
                </div>
            </a>
        </div>
    </div>

    @if ($isWarned)
        <div class="rounded-2xl border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-950/30 p-4 flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400 text-lg shrink-0 mt-0.5"></i>
            <div class="text-xs leading-relaxed text-amber-800 dark:text-amber-300">
                <p class="font-bold">Kamu sedang dalam status peringatan{{ $hasMultipleEkskul && $ekskul ? ' di ekskul ' . $ekskul->nama_ekskul : '' }}.</p>
                <p class="mt-0.5">Tingkatkan kehadiran dan partisipasimu di ekskul agar statusmu kembali normal.</p>
            </div>
        </div>
    @endif

    @if ($pendaftaranStatus)
        <div class="rounded-2xl border p-4 flex items-start gap-3 {{ $pendaftaranSoft }}">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $pendaftaranColor }} flex items-center justify-center shrink-0 shadow-sm">
                {!! $pendaftaranIcon !!}
            </div>
            <div class="text-xs leading-relaxed pt-1">{!! $pendaftaranMessage !!}</div>
        </div>
    @endif

    @if ($isNonaktif)
        <div class="rounded-2xl border border-rose-200 dark:border-rose-900/50 bg-rose-50 dark:bg-rose-950/30 p-5 flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-950 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-ban text-base"></i>
            </div>
            <div class="text-xs leading-relaxed">
                <p class="font-bold text-rose-700 dark:text-rose-300">
                    @if ($nonaktifStatus === 'keluar')
                        Kamu telah keluar dari ekskul
                    @else
                        Kamu dinonaktifkan dari ekskul
                    @endif
                    {{ $ekskulTerakhir->nama_ekskul ?? '' }}.
                </p>
                <p class="text-rose-600/90 dark:text-rose-400/90 mt-1">
                    Kamu masih bisa menjelajahi katalog dan mendaftar ke ekskul lain yang membuka pendaftaran.
                </p>
                <a href="{{ route('siswa.katalog') }}" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold transition shadow-sm">
                    Jelajahi Katalog Ekskul
                </a>
            </div>
        </div>
    @endif

    {{-- STATS CARDS - MOBILE ONLY (di desktop sudah tampil di hero card) --}}
    <div class="grid grid-cols-3 gap-2.5 md:hidden">
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#1a1a1a] border border-sky-100 dark:border-neutral-800 p-3 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Ekskul</p>
            <h3 class="text-lg font-extrabold mt-0.5 text-sky-700 dark:text-sky-400">{{ $hasMultipleEkskul ? 1 : $activeEkskuls->count() }}</h3>
            <p class="text-[10px] font-semibold text-sky-600 dark:text-sky-400">Diikuti</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#1a1a1a] border border-sky-100 dark:border-neutral-800 p-3 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Kehadiran</p>
            <h3 class="text-lg font-extrabold mt-0.5 text-sky-700 dark:text-sky-400">{{ $totalHadir ?? 0 }}</h3>
            <p class="text-[10px] font-semibold text-sky-600 dark:text-sky-400">Pertemuan</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#1a1a1a] border border-sky-100 dark:border-neutral-800 p-3 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Kegiatan</p>
            <h3 class="text-lg font-extrabold mt-0.5 text-sky-700 dark:text-sky-400">{{ $kegiatanMendatang->count() }}</h3>
            <p class="text-[10px] font-semibold text-sky-600 dark:text-sky-400">Agenda</p>
        </div>
    </div>

    {{-- Ekskul aktif (filtered to selected when multi-ekskul) --}}
    @if ($activeEkskuls->isNotEmpty())
        @php
            $displayEkskuls = ($hasMultipleEkskul && $ekskul)
                ? collect([$ekskul])
                : $activeEkskuls;
            $isSingleEkskul = $displayEkskuls->count() === 1;
        @endphp
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white">
                        @if($hasMultipleEkskul && $ekskul)
                            Ekskul Aktif · {{ $ekskul->nama_ekskul }}
                        @else
                            Ekskul Saya
                        @endif
                    </h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Informasi ekstrakurikuler yang kamu ikuti</p>
                </div>
                @if($activeEkskuls->count() === 1)
                    <a href="{{ route('siswa.katalog') }}" class="text-xs font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700 flex items-center gap-1 transition">
                        <span>Tambah Ekskul Lain (Slot 2/2)</span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 {{ $isSingleEkskul ? '' : 'md:grid-cols-2' }} gap-4">
                @foreach ($displayEkskuls as $ekskulAktif)
                    @php
                        $pendAktif = $siswa?->activePendaftaranUntuk($ekskulAktif);
                        $isKetua = $siswa?->jabatan === 'ketua' && $siswa?->ekskulDipimpin()?->id === $ekskulAktif->id;
                    @endphp

                    @if($isSingleEkskul)
                        {{-- Tampilan Penuh (Full-Width) jika hanya 1 ekskul agar rapi dan tidak ada space kosong --}}
                        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm p-4 md:p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-5 animate-fade-up">
                            <div class="flex items-start sm:items-center gap-4 min-w-0 flex-1">
                                <img src="{{ $ekskulAktif->logo_url }}" alt="{{ $ekskulAktif->nama_ekskul }}" class="w-13 h-13 md:w-14 md:h-14 rounded-xl object-cover border border-slate-100 dark:border-neutral-800 shrink-0 shadow-sm">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm md:text-base font-extrabold text-slate-900 dark:text-white">{{ $ekskulAktif->nama_ekskul }}</h4>
                                        @if($isKetua)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-900/50 px-2 py-0.5 rounded-full">
                                                <i class="fa-solid fa-crown text-[9px]"></i>
                                                Ketua
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-neutral-400 mt-1 max-w-2xl line-clamp-2">
                                        {{ $ekskulAktif->deskripsi ?: 'Ekskul aktif kamu saat ini.' }}
                                    </p>
                                    <div class="flex items-center gap-4 mt-2 text-xs text-slate-500 dark:text-neutral-400 flex-wrap">
                                        @if($ekskulAktif->pembina)
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fa-solid fa-user-tie text-sky-500 text-xs"></i>
                                                <span>Pembina: <strong class="text-slate-700 dark:text-slate-200">{{ $ekskulAktif->pembina->nama }}</strong></span>
                                            </span>
                                        @endif
                                        @if($ekskulAktif->jadwal)
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fa-regular fa-clock text-sky-500 text-xs"></i>
                                                <span>{{ $ekskulAktif->jadwal }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-neutral-800 flex-wrap shrink-0 md:ml-auto">
                                <a href="{{ route('ekskul.detail', $ekskulAktif) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold transition shadow-sm">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                                    Lihat Ekskul
                                </a>
                                <a href="{{ route('siswa.presensi', ['ekskul' => $ekskulAktif->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 hover:bg-slate-200 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                                    <i class="fa-solid fa-calendar-check text-[11px]"></i>
                                    Presensi
                                </a>
                                <a href="{{ route('siswa.nilai', ['ekskul' => $ekskulAktif->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 hover:bg-slate-200 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                                    <i class="fa-solid fa-award text-[11px]"></i>
                                    Nilai
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- Tampilan Grid 2-Kolom jika mengikuti 2 ekskul sekaligus --}}
                        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm p-4 md:p-5 flex flex-col justify-between gap-3 animate-fade-up">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-bold text-slate-800 dark:text-slate-100">{{ $ekskulAktif->nama_ekskul }}</h4>
                                        @if($isKetua)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-900/50 px-2 py-0.5 rounded-full">
                                                <i class="fa-solid fa-crown text-[9px]"></i>
                                                Ketua
                                            </span>
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-neutral-400">
                                        {{ \Illuminate\Support\Str::limit($ekskulAktif->deskripsi, 90) ?: 'Ekskul aktif kamu saat ini.' }}
                                    </p>
                                </div>
                                <img src="{{ $ekskulAktif->logo_url }}" alt="{{ $ekskulAktif->nama_ekskul }}" class="w-12 h-12 rounded-xl object-cover border border-slate-100 dark:border-neutral-800 shrink-0 shadow-sm">
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-neutral-800/80 flex-wrap mt-auto">
                                <a href="{{ route('ekskul.detail', $ekskulAktif) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold transition shadow-sm">
                                    Lihat Ekskul
                                </a>
                                <a href="{{ route('siswa.presensi', ['ekskul' => $ekskulAktif->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-neutral-800 hover:bg-slate-200 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                                    Presensi
                                </a>
                                <a href="{{ route('siswa.nilai', ['ekskul' => $ekskulAktif->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-neutral-800 hover:bg-slate-200 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                                    Nilai
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm p-6 md:p-8 text-center">
            <div class="mx-auto w-12 h-12 rounded-xl bg-slate-50 dark:bg-neutral-800/80 border border-dashed border-slate-300 dark:border-neutral-700 flex items-center justify-center text-slate-400 dark:text-slate-500 text-2xl font-light select-none">
                +
            </div>
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mt-3">Kamu belum bergabung di ekskul apa pun</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto leading-relaxed">Temukan ekskul yang sesuai dengan minat dan bakatmu. Bergabunglah sekarang!</p>
            <div class="flex flex-wrap justify-center gap-2.5 mt-4">
                <a href="{{ route('siswa.daftar-ekskul') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Daftar Ekskul
                </a>
                <a href="{{ route('siswa.katalog') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl hover:bg-slate-200 dark:hover:bg-neutral-700 transition-all">
                    <i class="fa-solid fa-book-open text-xs"></i>
                    Jelajahi Katalog
                </a>
            </div>
        </div>
    @endif

    {{-- CARD PENGURUS EKSKUL --}}
    @if($ekskul && isset($pengurusAktif))
        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm p-4 md:p-5 animate-fade-up">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white">Pengurus {{ $ekskul->nama_ekskul }}</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Struktur pengurus inti ekskul</p>
                </div>
                <span class="text-xs font-semibold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/40 border border-sky-100 dark:border-sky-800 px-3 py-1 rounded-full">4 Posisi</span>
            </div>

            @php
                $jabatanLabels = [
                    'ketua' => 'Ketua',
                    'wakil_ketua' => 'Wakil Ketua',
                    'sekretaris' => 'Sekretaris',
                    'bendahara' => 'Bendahara',
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($jabatanLabels as $jKey => $jTitle)
                    @php
                        $pItem = $pengurusAktif->get($jKey);
                    @endphp

                    <div class="rounded-xl border border-sky-100 dark:border-neutral-800 bg-slate-50/50 dark:bg-neutral-900/40 p-3.5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-[10px] font-bold text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-950/50 border border-sky-100 dark:border-sky-800 px-2 py-0.5 rounded-md uppercase tracking-wider">
                                    {{ $jTitle }}
                                </span>
                            </div>

                            @if($pItem && $pItem->siswa)
                                <div class="flex items-center gap-3">
                                    <img src="{{ $pItem->siswa->foto_url ?? asset('images/default.png') }}" alt="{{ $pItem->siswa->nama }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 dark:border-neutral-700 shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate" title="{{ $pItem->siswa->nama }}">
                                            {{ $pItem->siswa->nama }}
                                        </h4>
                                        <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                            {{ $pItem->siswa->kelas?->nama ?? 'Siswa' }} • NIS: {{ $pItem->siswa->nis }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="py-2.5 px-3 rounded-lg border border-dashed border-slate-200 dark:border-neutral-800 text-center">
                                    <p class="text-[11px] font-medium text-slate-400">Belum Ada Pengurus</p>
                                    <p class="text-[10px] text-slate-400/70 mt-0.5">Posisi belum terisi</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- MAIN GRID: AGENDA KEGIATAN & TESTIMONI --}}
    <div class="grid grid-cols-1 {{ $ekskul ? 'lg:grid-cols-3' : '' }} gap-4 md:gap-5 items-stretch">
        <!-- GRAFIK KEHADIRAN SISWA -->
        <div class="{{ $ekskul ? 'lg:col-span-2' : 'w-full' }} bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up flex flex-col h-full">
            <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-sky-50 dark:border-neutral-800 shrink-0">
                <h3 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white whitespace-nowrap">Grafik Kehadiran</h3>

                {{-- Keterangan satu baris: Kolom Hadir, Izin, Sakit, Alpha --}}
                <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto flex-nowrap shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 text-blue-700 dark:text-blue-300 text-[11px] font-bold whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Hadir: {{ $statsPresensi['hadir'] ?? 0 }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-100 dark:border-amber-900/50 text-amber-700 dark:text-amber-300 text-[11px] font-bold whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Izin: {{ $statsPresensi['izin'] ?? 0 }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-950/40 border border-purple-100 dark:border-purple-900/50 text-purple-700 dark:text-purple-300 text-[11px] font-bold whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        Sakit: {{ $statsPresensi['sakit'] ?? 0 }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-100 dark:border-rose-900/50 text-rose-700 dark:text-rose-300 text-[11px] font-bold whitespace-nowrap">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Alpha: {{ $statsPresensi['alpha'] ?? 0 }}
                    </span>
                </div>
            </div>

            <div class="p-4 md:p-5 flex-1 flex flex-col justify-center">
                {{-- Chart Canvas with Kesiswaan Grid Lines --}}
                <div class="h-56 md:h-64 relative w-full">
                    <canvas id="siswaPresensiChart"></canvas>
                </div>
            </div>
        </div>

        <!-- TESTIMONI & TANYA KE KETUA -->
        @if($ekskul)
            <div class="lg:col-span-1 bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden animate-fade-up flex flex-col h-full">
                <div class="px-5 py-4 border-b border-sky-50 dark:border-neutral-800 shrink-0">
                    <h3 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white">Beri Testimoni & Tanya</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Suaramu untuk {{ $ekskul->nama_ekskul }}</p>
                </div>
                <div class="p-4 md:p-5 space-y-4 flex-1 flex flex-col justify-between">
                    @if($hasSubmittedTestimoni)
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 bg-sky-50 dark:bg-sky-950/30 border border-sky-100 dark:border-sky-900/50 rounded-xl px-3.5 py-2.5 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-sky-500 text-xs"></i>
                            <span>Kamu sudah mengirim testimoni.</span>
                        </p>
                    @else
                        <form method="POST" action="{{ route('ekskul.testimoni.store', $ekskul) }}" class="space-y-2">
                            @csrf
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Testimoni</label>
                            <textarea name="quote" rows="2" required maxlength="2000" placeholder="Tulis pengalamanmu di {{ $ekskul->nama_ekskul }}..."
                                class="w-full px-3.5 py-2 bg-sky-50/50 dark:bg-neutral-800/50 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 transition"></textarea>
                            @error('quote') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                            <button type="submit" class="px-3.5 py-1.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm transition">Kirim Testimoni</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('ekskul.faq.store', $ekskul) }}" class="space-y-2 pt-3 border-t border-slate-100 dark:border-neutral-800">
                        @csrf
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pertanyaan ke Ketua</label>
                        <input type="text" name="pertanyaan" required maxlength="255" placeholder="Tulis pertanyaanmu..."
                            class="w-full px-3.5 py-2 bg-sky-50/50 dark:bg-neutral-800/50 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 transition">
                        @error('pertanyaan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        <button type="submit" class="px-3.5 py-1.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm transition">Tanyakan ke Ketua</button>
                    </form>
                </div>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const presensiCanvas = document.getElementById('siswaPresensiChart');
        if (!presensiCanvas || typeof Chart === 'undefined') return;

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const anim = reduceMotion ? {} : { duration: 900, easing: 'easeOutQuart' };

        Chart.defaults.font.family = '"Plus Jakarta Sans", system-ui, sans-serif';
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#64748b';

        function getChartColors() {
            const isDark = document.documentElement.classList.contains('dark-mode') || document.documentElement.classList.contains('dark');
            return {
                isDark: isDark,
                gridColor: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(226, 232, 240, 0.6)',
                tickColor: isDark ? '#94a3b8' : '#64748b',
                tooltipBg: isDark ? '#1e293b' : '#0F172A',
            };
        }

        const themeColors = getChartColors();
        const ctx = presensiCanvas.getContext('2d');

        const presensiData = [
            {{ $statsPresensi['hadir'] ?? 0 }},
            {{ $statsPresensi['izin'] ?? 0 }},
            {{ $statsPresensi['sakit'] ?? 0 }},
            {{ $statsPresensi['alpha'] ?? 0 }}
        ];

        const presensiChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                datasets: [{
                    label: 'Jumlah Presensi',
                    data: presensiData,
                    backgroundColor: ['#2563EB', '#F59E0B', '#8B5CF6', '#EF4444'],
                    hoverBackgroundColor: ['#1D4ED8', '#D97706', '#7C3AED', '#DC2626'],
                    borderRadius: 6,
                    maxBarThickness: 38,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: anim,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: themeColors.tooltipBg,
                        titleFont: { weight: 700 },
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(ctx) { return ctx.parsed.y + ' pertemuan'; }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, stepSize: 1, font: { size: 10 }, color: themeColors.tickColor },
                        grid: { color: themeColors.gridColor, drawBorder: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 600 }, maxRotation: 0, autoSkip: true, color: themeColors.tickColor }
                    }
                }
            }
        });

        window.addEventListener('soul-theme-change', function () {
            const c = getChartColors();
            if (presensiChart.options.scales?.y) {
                if (presensiChart.options.scales.y.grid) presensiChart.options.scales.y.grid.color = c.gridColor;
                if (presensiChart.options.scales.y.ticks) presensiChart.options.scales.y.ticks.color = c.tickColor;
            }
            if (presensiChart.options.scales?.x?.ticks) {
                presensiChart.options.scales.x.ticks.color = c.tickColor;
            }
            if (presensiChart.options.plugins?.tooltip) {
                presensiChart.options.plugins.tooltip.backgroundColor = c.tooltipBg;
            }
            presensiChart.update('none');
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                icon: 'success',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    const content = Swal.getHtmlContainer();
                    if (content) {
                        const timerElement = document.createElement('div');
                        timerElement.className = 'text-sm text-gray-500 mt-2';
                        timerElement.id = 'timer-text';
                        content.appendChild(timerElement);
                    }
                },
                willClose: () => {
                    window.location.href = '{{ route('siswa.dashboard') }}';
                }
            });

            let timeLeft = 5;
            const timerInterval = setInterval(() => {
                timeLeft--;
                const timerText = document.getElementById('timer-text');
                if (timerText) {
                    timerText.textContent = `Mengalihkan ke halaman dashboard dalam ${timeLeft} detik...`;
                }
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                }
            }, 1000);
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Gagal!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#2563EB'
            });
        @endif
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const card = document.querySelector('[data-tilt-card]');
        if (!card) return;

        const canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (!canHover || reduceMotion) return;

        const MAX_TILT = 7;
        let frame = null;

        card.addEventListener('animationend', function () {
            card.classList.remove('animate-fade-up');
            if (card.matches(':hover')) card.classList.add('is-tilting');
        }, { once: true });

        card.addEventListener('pointerenter', function () {
            card.classList.add('is-tilting');
        });

        card.addEventListener('pointermove', function (event) {
            const rect = card.getBoundingClientRect();
            const px = (event.clientX - rect.left) / rect.width;
            const py = (event.clientY - rect.top) / rect.height;
            const rotateX = (0.5 - py) * MAX_TILT * 2;
            const rotateY = (px - 0.5) * MAX_TILT * 2;

            if (frame) cancelAnimationFrame(frame);
            frame = requestAnimationFrame(function () {
                frame = null;
                card.style.transform = 'perspective(1200px) rotateX(' + rotateX.toFixed(2) + 'deg) rotateY(' + rotateY.toFixed(2) + 'deg) scale(1.015)';
            });
        });

        function resetTilt() {
            if (frame) {
                cancelAnimationFrame(frame);
                frame = null;
            }
            card.classList.remove('is-tilting');
            card.style.transform = '';
        }

        card.addEventListener('pointerleave', resetTilt);
        card.addEventListener('pointercancel', resetTilt);
    });
</script>
@endpush
