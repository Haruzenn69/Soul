<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ekskul->nama_ekskul }} &mdash; Detail Ekstrakurikuler | SOUL</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($ekskul->tagline ?: $ekskul->deskripsi, 155) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-mode-head')
    <style>
        .soul-logo-dark { display: none; }
        .soul-logo-light { display: block; }
        :is(html.dark-mode, html.dark) .soul-logo-light { display: none !important; }
        :is(html.dark-mode, html.dark) .soul-logo-dark { display: block !important; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-neutral-950 font-sans text-slate-800 dark:text-neutral-200 antialiased selection:bg-sky-100 selection:text-sky-700">
    @php
        $cover = \App\Support\EkskulInfo::cover($ekskul->cover);
        $logo = \App\Support\EkskulInfo::logo($ekskul->logo);
        $monogram = \Illuminate\Support\Str::of($ekskul->nama_ekskul)->upper()->substr(0, 2)->value();
        $kontributor = auth()->check() && auth()->user()->role !== 'kesiswaan';
        $userSiswa = auth()->check() && auth()->user()->role === 'siswa' ? auth()->user()->siswa : null;
        $pendaftaranUser = $userSiswa ? $userSiswa->pendaftarans()->where('ekskul_id', $ekskul->id)->latest()->first() : null;
        $sudahTerdaftar = $pendaftaranUser && in_array($pendaftaranUser->status, ['diterima', 'peringatan']);
        $sedangMenunggu = $pendaftaranUser && $pendaftaranUser->status === 'menunggu';
        $alasanTidakBisa = ($userSiswa && ! $sudahTerdaftar && ! $sedangMenunggu) ? $userSiswa->alasanTidakBisaDaftar($ekskul) : null;
        $bisaGabung = $ekskul->is_open_recruitment && $alasanTidakBisa === null;
        $pelatihAktif = ($ekskul->pelatih && $ekskul->pelatih->isTerverifikasi()) ? $ekskul->pelatih : null;
        $ketuaAktif = $ekskul->ketua();
    @endphp

    {{-- TOP NAVBAR --}}
    <header class="sticky top-0 z-40 border-b border-sky-100 dark:border-neutral-800 bg-white/90 dark:bg-neutral-900/90 backdrop-blur-md transition-colors">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3 md:px-6">
            {{-- Brand & Breadcrumbs --}}
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ auth()->check() && auth()->user()->role === 'siswa' ? route('siswa.dashboard') : route('siswa.landing') }}" class="flex items-center gap-2.5 group shrink-0" aria-label="SOUL">
                    <div class="w-8 h-8 rounded-xl overflow-hidden flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/logo-light-nobg.png') }}" alt="SOUL" class="w-full h-full object-contain soul-logo-light">
                        <img src="{{ asset('images/logo-dark-nobg.png') }}" alt="SOUL" class="w-full h-full object-contain soul-logo-dark">
                    </div>
                    <span class="font-extrabold text-sm tracking-tight text-slate-900 dark:text-white hidden sm:inline">SOUL</span>
                </a>

                <span class="text-slate-300 dark:text-neutral-700 select-none">/</span>

                <nav class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-neutral-400 min-w-0">
                    <a href="{{ route('siswa.katalog') }}" class="hover:text-sky-600 dark:hover:text-sky-400 font-medium transition truncate shrink-0">Katalog Ekskul</a>
                    <span class="text-slate-300 dark:text-neutral-700 select-none">&rsaquo;</span>
                    <span class="font-semibold text-slate-800 dark:text-neutral-100 truncate">{{ $ekskul->nama_ekskul }}</span>
                </nav>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 shrink-0">
                {{-- Theme Mode Toggle --}}
                <button type="button" onclick="window.SoulTheme ? window.SoulTheme.toggle() : null"
                        class="w-9 h-9 rounded-xl border border-sky-100 dark:border-neutral-800 bg-white dark:bg-neutral-800 hover:bg-slate-50 dark:hover:bg-neutral-700 text-slate-600 dark:text-amber-300 flex items-center justify-center transition shadow-xs"
                        title="Ubah Mode Tampilan" aria-label="Toggle theme">
                    <svg class="w-4 h-4 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="w-4 h-4 hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                @auth
                    @php
                        $dashRoute = match(auth()->user()->role) {
                            'siswa' => route('siswa.dashboard'),
                            'pembina' => route('pembina.dashboard'),
                            'kesiswaan' => route('kesiswaan.dashboard'),
                            'ketua' => route('ketua.dashboard'),
                            default => route('dashboard'),
                        };
                    @endphp
                    <a href="{{ $dashRoute }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-neutral-800 hover:bg-slate-200 dark:hover:bg-neutral-700 text-slate-700 dark:text-neutral-200 text-xs font-semibold transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold transition shadow-xs">
                        Masuk
                    </a>
                @endauth

                <a href="{{ route('siswa.katalog') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-sky-100 dark:border-neutral-800 bg-white dark:bg-neutral-800 hover:bg-slate-50 dark:hover:bg-neutral-700 text-slate-600 dark:text-neutral-300 text-xs font-semibold transition shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Katalog</span>
                </a>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 md:px-6 py-6 space-y-6">

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <div class="rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 p-4 text-xs font-medium text-emerald-800 dark:text-emerald-300 flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 p-4 text-xs font-medium text-rose-800 dark:text-rose-300 flex items-center gap-2.5">
                <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- HERO PROFILE CARD --}}
        <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden">
            {{-- Banner Cover --}}
            <div class="relative h-44 sm:h-56 md:h-64 w-full bg-slate-800 overflow-hidden">
                @if ($cover)
                    <img src="{{ $cover }}" alt="Cover {{ $ekskul->nama_ekskul }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                @else
                    <div class="w-full h-full bg-gradient-to-r from-sky-600 via-blue-600 to-indigo-700 dark:from-neutral-850 dark:via-neutral-800 dark:to-neutral-850 flex items-center justify-center p-6">
                        <div class="text-center text-white/90">
                            <span class="text-xs font-semibold tracking-wider uppercase text-sky-200 dark:text-neutral-400">Ekstrakurikuler</span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1">{{ $ekskul->nama_ekskul }}</h2>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Profile Details Bar --}}
            <div class="p-5 sm:p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-5">
                    {{-- Logo & Identity --}}
                    <div class="flex items-start sm:items-end gap-4 min-w-0">
                        <div class="-mt-14 sm:-mt-16 shrink-0 relative z-10">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white dark:bg-neutral-800 border-4 border-white dark:border-neutral-900 shadow-md flex items-center justify-center text-2xl font-black text-sky-600 dark:text-sky-400 overflow-hidden">
                                @if ($logo)
                                    <img src="{{ $logo }}" alt="Logo {{ $ekskul->nama_ekskul }}" class="w-full h-full object-contain p-2">
                                @else
                                    <span>{{ $monogram }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                    {{ $ekskul->nama_ekskul }}
                                </h1>

                                @if ($ekskul->isWajib())
                                    <span class="px-2.5 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800/60 text-amber-700 dark:text-amber-400 text-xs font-bold">
                                        Wajib
                                    </span>
                                @elseif ($ekskul->kategori)
                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-300 text-xs font-semibold">
                                        {{ $ekskul->kategori }}
                                    </span>
                                @endif

                                @if ($ekskul->is_open_recruitment)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-400 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Pendaftaran Dibuka
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-neutral-400 text-xs font-semibold">
                                        Pendaftaran Ditutup
                                    </span>
                                @endif
                            </div>

                            @if ($ekskul->tagline)
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-neutral-400 italic">
                                    &ldquo;{{ $ekskul->tagline }}&rdquo;
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Actions Button --}}
                    <div class="shrink-0 flex items-center gap-2 pt-2 md:pt-0">
                        @if ($sudahTerdaftar)
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-bold">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Anggota Aktif</span>
                            </div>
                        @elseif ($sedangMenunggu)
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 text-xs font-bold">
                                <svg class="w-4 h-4 text-amber-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Menunggu Verifikasi</span>
                            </div>
                        @elseif ($bisaGabung)
                            <a href="{{ route('siswa.form-daftar', $ekskul) }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-sm transition hover:-translate-y-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                <span>Daftar Sekarang</span>
                            </a>
                        @else
                            <button type="button" disabled title="{{ $alasanTidakBisa ?? 'Pendaftaran Ditutup' }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-400 dark:text-neutral-500 text-xs font-semibold cursor-not-allowed select-none">
                                <span>{{ $alasanTidakBisa ? 'Tidak Dapat Mendaftar' : 'Pendaftaran Ditutup' }}</span>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- STATS / KEY INFO CARDS --}}
                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-neutral-800 grid grid-cols-2 md:grid-cols-4 gap-3">
                    {{-- Pembina --}}
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-100 dark:border-neutral-800 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-neutral-400">Pembina</p>
                            <p class="text-xs font-bold text-slate-800 dark:text-neutral-200 truncate mt-0.5">
                                {{ $ekskul->pembina->nama ?? 'Belum ditentukan' }}
                            </p>
                        </div>
                    </div>

                    {{-- Pelatih --}}
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-100 dark:border-neutral-800 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1">
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-neutral-400">Pelatih</p>
                                @if($pelatihAktif)
                                    <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400" title="Terverifikasi">✓</span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-slate-800 dark:text-neutral-200 truncate mt-0.5">
                                {{ $pelatihAktif ? $pelatihAktif->nama : 'Belum ditentukan' }}
                            </p>
                        </div>
                    </div>

                    {{-- Ketua Ekskul --}}
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-100 dark:border-neutral-800 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-neutral-400">Ketua</p>
                            <p class="text-xs font-bold text-slate-800 dark:text-neutral-200 truncate mt-0.5">
                                {{ $ketuaAktif ? $ketuaAktif->nama : 'Belum dipilih' }}
                            </p>
                        </div>
                    </div>

                    {{-- Anggota Aktif --}}
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-100 dark:border-neutral-800 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-neutral-400">Anggota</p>
                            <p class="text-xs font-bold text-slate-800 dark:text-neutral-200 truncate mt-0.5">
                                {{ $totalAnggota }} Siswa
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- KOLOM KIRI (UTAMA: 8 KANTONG) --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- TENTANG EKSKUL --}}
                <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 md:p-7 border border-sky-100 dark:border-neutral-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100 dark:border-neutral-800">
                        <div class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-neutral-800 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Tentang Ekstrakurikuler</h2>
                    </div>

                    <div class="prose prose-slate dark:prose-invert max-w-none text-xs sm:text-sm leading-relaxed text-slate-600 dark:text-neutral-300">
                        <p class="whitespace-pre-line">{{ $ekskul->deskripsi ?: 'Deskripsi untuk ekstrakurikuler ini belum ditambahkan oleh pembina atau ketua ekskul.' }}</p>
                    </div>

                    @if ($ekskul->tujuan)
                        <div class="mt-4 p-4 rounded-2xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-100 dark:border-neutral-800">
                            <h3 class="text-xs font-bold text-slate-800 dark:text-neutral-200 mb-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Fokus & Tujuan Pembinaan</span>
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-neutral-300 leading-relaxed">{{ $ekskul->tujuan }}</p>
                        </div>
                    @endif
                </div>

                {{-- GALERI DOKUMENTASI --}}
                @if ($galeris->isNotEmpty())
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 md:p-7 border border-sky-100 dark:border-neutral-800 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-neutral-800">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-neutral-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Dokumentasi Kegiatan</h2>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 dark:text-neutral-500">{{ $galeris->count() }} Foto</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach ($galeris as $foto)
                                <div class="aspect-4/3 overflow-hidden rounded-2xl bg-slate-100 dark:bg-neutral-800 border border-slate-100 dark:border-neutral-800">
                                    <img src="{{ asset('storage/' . $foto) }}" alt="Foto kegiatan {{ $ekskul->nama_ekskul }}"
                                         loading="lazy" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- AGENDA / KEGIATAN TERAKHIR --}}
                @if ($ekskul->kegiatans->isNotEmpty())
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 md:p-7 border border-sky-100 dark:border-neutral-800 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-neutral-800">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-neutral-800 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Aktivitas & Agenda Terkini</h2>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 dark:text-neutral-500">{{ $ekskul->kegiatans->count() }} Kegiatan</span>
                        </div>

                        <div class="space-y-3">
                            @foreach ($ekskul->kegiatans as $kegiatan)
                                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-100 dark:border-neutral-800 flex items-start gap-3.5">
                                    <div class="w-12 h-12 rounded-xl bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex flex-col items-center justify-center shrink-0">
                                        <span class="text-sm font-extrabold text-slate-900 dark:text-white leading-none">{{ $kegiatan->tanggal_kegiatan->translatedFormat('d') }}</span>
                                        <span class="text-[9px] font-bold uppercase text-slate-400 dark:text-neutral-400 leading-none mt-0.5">{{ $kegiatan->tanggal_kegiatan->translatedFormat('M') }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white">{{ $kegiatan->materi }}</h3>
                                            @if($kegiatan->isEvent())
                                                <span class="px-2 py-0.2 rounded-md bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 text-[10px] font-bold border border-amber-200 dark:border-amber-800/60">Event</span>
                                            @endif
                                        </div>
                                        @if ($kegiatan->deskripsi)
                                            <p class="text-xs text-slate-500 dark:text-neutral-400 mt-0.5 line-clamp-2">{{ $kegiatan->deskripsi }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- PRESTASI & PENGHARGAAN --}}
                @if ($ekskul->prestasis->isNotEmpty())
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 md:p-7 border border-sky-100 dark:border-neutral-800 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-neutral-800">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-neutral-800 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                </div>
                                <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Prestasi & Pencapaian</h2>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 dark:text-neutral-500">{{ $ekskul->prestasis->count() }} Prestasi</span>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($ekskul->prestasis as $prestasi)
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-100 dark:border-neutral-800 flex items-start gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 flex items-center justify-center font-bold shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if ($prestasi->tahun)
                                                <span class="px-2 py-0.5 rounded-md bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 text-[10px] font-bold text-slate-700 dark:text-neutral-300">{{ $prestasi->tahun }}</span>
                                            @endif
                                            @if ($prestasi->kategori)
                                                <span class="text-[10px] font-semibold text-sky-600 dark:text-sky-400">{{ $prestasi->kategori }}</span>
                                            @endif
                                        </div>
                                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white mt-1 leading-snug">{{ $prestasi->judul }}</h3>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- TESTIMONI ANGGOTA --}}
                @if ($ekskul->testimoniss->isNotEmpty())
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 md:p-7 border border-sky-100 dark:border-neutral-800 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-neutral-800">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-neutral-800 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                </div>
                                <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Pengalaman Anggota</h2>
                            </div>
                            <span class="text-xs font-semibold text-slate-400 dark:text-neutral-500">{{ $ekskul->testimoniss->count() }} Cerita</span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($ekskul->testimoniss as $testimoni)
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-neutral-800/50 border border-slate-100 dark:border-neutral-800 flex flex-col justify-between">
                                    <p class="text-xs text-slate-700 dark:text-neutral-300 leading-relaxed italic">
                                        &ldquo;{{ $testimoni->quote }}&rdquo;
                                    </p>
                                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-neutral-700/60 flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-xs font-bold flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($testimoni->nama, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 dark:text-neutral-200 truncate">{{ $testimoni->nama }}</p>
                                            <p class="text-[10px] text-slate-400 dark:text-neutral-400 truncate">{{ $testimoni->kelas ?? 'Anggota Ekskul' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- FORMULIR TESTIMONI (JIKA SISWA TERDAFTAR / KONTRIBUTOR) --}}
                @if ($kontributor)
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 border border-sky-100 dark:border-neutral-800 shadow-sm space-y-3">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 16l-4 1 1-4 8.5-8.5z"/></svg>
                            <span>Bagikan Pengalamanmu</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-neutral-400">Pernah atau sedang mengikuti ekskul ini? Ceritakan kesan dan manfaat yang kamu rasakan untuk siswa lainnya.</p>

                        @if ($hasSubmittedTestimoni)
                            <div class="rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 p-3 text-xs text-amber-800 dark:text-amber-300">
                                Kamu sudah mengirim testimoni untuk ekskul ini. Testimoni akan ditampilkan setelah disetujui pengurus.
                            </div>
                        @else
                            <form method="POST" action="{{ route('ekskul.testimoni.store', $ekskul) }}" class="space-y-3 pt-1">
                                @csrf
                                <textarea name="quote" rows="3" required maxlength="2000"
                                          placeholder="Tuliskan pengalaman seru atau keterampilan yang kamu dapatkan..."
                                          class="w-full rounded-xl border border-slate-200 dark:border-neutral-700 bg-slate-50 dark:bg-neutral-800 p-3 text-xs text-slate-800 dark:text-neutral-100 placeholder-slate-400 dark:placeholder-neutral-500 focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 dark:focus:ring-neutral-700 transition resize-y"></textarea>
                                @error('quote')
                                    <p class="text-xs font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold transition shadow-xs">
                                    Kirim Testimoni
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

            </div>

            {{-- KOLOM KANAN (SIDEBAR: 4 KANTONG) --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- JADWAL MINGGUAN CARD --}}
                <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 border border-sky-100 dark:border-neutral-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-neutral-800">
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Jadwal Latihan</span>
                        </h2>
                        <span class="text-[11px] font-semibold text-slate-400 dark:text-neutral-500">Rutin</span>
                    </div>

                    @include('partials.ekskul-minggu', [
                        'ekskul' => $ekskul,
                        'besar' => true,
                        'kelas' => 'mt-1',
                    ])
                </div>

                {{-- PUSAT BANTUAN / FAQ --}}
                <div class="bg-white dark:bg-neutral-900 rounded-3xl p-6 border border-sky-100 dark:border-neutral-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-neutral-800">
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Tanya Jawab (FAQ)</span>
                        </h2>
                        <span class="text-[11px] font-semibold text-slate-400 dark:text-neutral-500">{{ $ekskul->faqs->count() }}</span>
                    </div>

                    <div class="space-y-2.5">
                        @forelse ($ekskul->faqs as $faq)
                            <details class="group rounded-2xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-100 dark:border-neutral-800 overflow-hidden">
                                <summary class="flex items-center justify-between gap-2 p-3 text-xs font-bold text-slate-800 dark:text-neutral-200 cursor-pointer list-none select-none">
                                    <span>{{ $faq->pertanyaan }}</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </summary>
                                <div class="px-3 pb-3 pt-1 text-xs text-slate-600 dark:text-neutral-300 border-t border-slate-100 dark:border-neutral-700/60">
                                    {{ $faq->jawaban }}
                                </div>
                            </details>
                        @empty
                            <p class="text-xs text-slate-400 dark:text-neutral-500 text-center py-3">Belum ada pertanyaan terpublikasi untuk ekskul ini.</p>
                        @endforelse
                    </div>

                    {{-- FORM AJUKAN PERTANYAAN --}}
                    @if ($kontributor)
                        <div class="pt-3 border-t border-slate-100 dark:border-neutral-800">
                            <p class="text-xs font-bold text-slate-700 dark:text-neutral-300 mb-2">Ada yang ingin ditanyakan?</p>
                            <form method="POST" action="{{ route('ekskul.faq.store', $ekskul) }}" class="space-y-2">
                                @csrf
                                <input type="text" name="pertanyaan" required maxlength="255"
                                       placeholder="Tulis pertanyaanmu di sini..."
                                       class="w-full rounded-xl border border-slate-200 dark:border-neutral-700 bg-slate-50 dark:bg-neutral-800 px-3 py-2 text-xs text-slate-800 dark:text-neutral-100 placeholder-slate-400 dark:placeholder-neutral-500 focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 dark:focus:ring-neutral-700 transition">
                                @error('pertanyaan')
                                    <p class="text-xs font-semibold text-rose-600">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="w-full py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 hover:bg-slate-200 dark:hover:bg-neutral-700 text-slate-700 dark:text-neutral-200 text-xs font-bold transition">
                                    Kirim Pertanyaan
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- KETENTUAN PENDAFTARAN --}}
                <div class="rounded-3xl p-5 bg-sky-50/60 dark:bg-neutral-900 border border-sky-100 dark:border-neutral-800 text-xs space-y-2 text-slate-600 dark:text-neutral-400">
                    <p class="font-bold text-slate-800 dark:text-neutral-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Ketentuan Pemilihan Ekskul</span>
                    </p>
                    <ul class="list-disc pl-4 space-y-1">
                        <li>Maksimal mengikuti <strong>{{ \App\Support\AturanEkskul::MAKS_EKSKUL }} ekstrakurikuler</strong> secara bersamaan.</li>
                        <li>Khusus kategori wajib, hanya diperbolehkan memilih maksimal <strong>1 ekskul wajib</strong>.</li>
                        <li>Pendaftaran yang sudah dikirim akan diverifikasi oleh ketua ekskul terkait.</li>
                    </ul>
                </div>

            </div>
        </div>

    </main>

    {{-- FOOTER --}}
    <footer class="border-t border-sky-100 dark:border-neutral-800 bg-white/70 dark:bg-neutral-900/70 py-6 text-center text-xs text-slate-400 dark:text-neutral-500 mt-12">
        <div class="mx-auto max-w-6xl px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>&copy; {{ date('Y') }} SOUL &mdash; SMKN 11 Bandung</p>
            <div class="flex items-center gap-4 text-slate-500 dark:text-neutral-400">
                <a href="{{ route('siswa.katalog') }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition">Katalog</a>
                <a href="{{ route('siswa.landing') }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition">Beranda</a>
            </div>
        </div>
    </footer>
</body>
</html>
