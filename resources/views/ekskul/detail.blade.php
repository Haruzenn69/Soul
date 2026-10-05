<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ekskul->nama_ekskul }} - Detail Ekskul | SOUL</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($ekskul->tagline ?: $ekskul->deskripsi, 155) }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-mode-head')
    <style>
        .ekskul-gradient-mesh {
            background-color: #0284c7;
            background-image: 
                radial-gradient(at 0% 0%, hsla(199, 89%, 48%, 1) 0, transparent 50%),
                radial-gradient(at 50% 0%, hsla(217, 91%, 60%, 1) 0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(43, 96%, 56%, 0.8) 0, transparent 50%);
        }
        html.dark-mode .ekskul-gradient-mesh,
        html.dark .ekskul-gradient-mesh {
            background-color: #0c4a6e !important;
            background-image: 
                radial-gradient(at 0% 0%, hsla(199, 89%, 30%, 1) 0, transparent 50%),
                radial-gradient(at 50% 0%, hsla(217, 91%, 35%, 1) 0, transparent 50%),
                radial-gradient(at 100% 0%, hsla(43, 96%, 35%, 0.8) 0, transparent 50%) !important;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-amber-50/50 dark:from-[#121212] dark:via-[#161616] dark:to-[#121212] font-sans text-slate-800 dark:text-slate-200 antialiased selection:bg-sky-100 selection:text-sky-700">
    @php
        $cover = \App\Support\EkskulInfo::cover($ekskul->cover);
        $logo = \App\Support\EkskulInfo::logo($ekskul->logo);
        $monogram = \Illuminate\Support\Str::of($ekskul->nama_ekskul)->upper()->substr(0, 2)->value();
        $bisaGabung = $ekskul->is_open_recruitment;
        $kontributor = auth()->check() && auth()->user()->role !== 'kesiswaan';
        $userSiswa = auth()->check() && auth()->user()->role === 'siswa' ? auth()->user()->siswa : null;
        $pendaftaranUser = $userSiswa ? $userSiswa->pendaftarans()->where('ekskul_id', $ekskul->id)->latest()->first() : null;
        $sudahTerdaftar = $pendaftaranUser && in_array($pendaftaranUser->status, ['diterima', 'peringatan']);
        $sedangMenunggu = $pendaftaranUser && $pendaftaranUser->status === 'menunggu';
    @endphp

    {{-- TOP NAVBAR --}}
    <header class="sticky top-0 z-40 border-b border-sky-100 dark:border-neutral-800 bg-white/85 dark:bg-[#181818]/90 backdrop-blur-md transition-colors">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3 md:px-8">
            <a href="{{ route('siswa.landing') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-400 to-blue-600 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-sky-200 dark:shadow-none group-hover:scale-105 transition-transform">
                    S
                </div>
                <span class="text-base font-extrabold tracking-tight text-slate-900 dark:text-white">SOUL</span>
            </a>

            <div class="flex items-center gap-2">
                {{-- Theme Mode Toggle Button --}}
                <button type="button" onclick="window.SoulTheme ? window.SoulTheme.toggle() : null"
                        class="w-9 h-9 rounded-xl border border-sky-100 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:bg-sky-50 dark:hover:bg-neutral-700 text-slate-600 dark:text-amber-300 flex items-center justify-center transition shadow-sm"
                        title="Ubah Mode Terang / Gelap" aria-label="Toggle theme">
                    <svg class="w-4 h-4 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="w-4 h-4 hidden dark:block text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                {{-- Back to Katalog --}}
                <a href="{{ route('siswa.katalog') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-sky-100 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:bg-sky-50 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-sky-300 text-xs font-bold transition-all shadow-sm group">
                    <svg class="w-4 h-4 text-sky-500 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Katalog Ekskul</span>
                </a>
            </div>
        </div>
    </header>



    <main class="pb-16">
        {{-- HERO SECTION --}}
        <section class="relative pt-4 md:pt-6">
            <div class="mx-auto max-w-6xl px-4 md:px-8">
                {{-- Banner Cover Container --}}
                <div class="relative overflow-hidden rounded-3xl border border-sky-100/70 dark:border-neutral-800 shadow-lg bg-white dark:bg-[#1a1a1a]">
                    @if ($cover)
                        <div class="relative h-48 sm:h-64 md:h-80 w-full overflow-hidden">
                            <img src="{{ $cover }}" alt="Cover {{ $ekskul->nama_ekskul }}"
                                 class="h-full w-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-black/10"></div>
                        </div>
                    @else
                        <div class="h-44 sm:h-56 md:h-64 w-full ekskul-gradient-mesh relative flex items-center justify-center overflow-hidden">
                            {{-- Decorative glow circles --}}
                            <div class="absolute -right-12 -top-12 w-64 h-64 rounded-full bg-amber-400/30 blur-3xl pointer-events-none"></div>
                            <div class="absolute -left-12 -bottom-12 w-64 h-64 rounded-full bg-sky-300/40 blur-3xl pointer-events-none"></div>
                            <div class="text-center px-4 relative z-10">
                                <span class="inline-block text-xs font-extrabold uppercase tracking-widest text-amber-300 bg-black/20 backdrop-blur-md px-3.5 py-1 rounded-full mb-2">
                                    Ekstrakurikuler Resmi SMKN 11
                                </span>
                                <h2 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight drop-shadow-md">
                                    {{ $ekskul->nama_ekskul }}
                                </h2>
                            </div>
                        </div>
                    @endif

                    {{-- Badges Floating over Cover --}}
                    <div class="absolute top-4 left-4 right-4 flex items-center justify-between gap-2 z-10">
                        @if ($bisaGabung)
                            <div class="inline-flex items-center gap-2 rounded-full bg-emerald-500/90 backdrop-blur-md px-3.5 py-1.5 text-xs font-bold text-white shadow-md">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-200 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                </span>
                                Buka Pendaftaran
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 rounded-full bg-slate-900/85 backdrop-blur-md px-3.5 py-1.5 text-xs font-semibold text-slate-200 shadow-md">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Pendaftaran Ditutup
                            </div>
                        @endif

                        @if ($ekskul->jadwal)
                            <div class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-neutral-900/90 backdrop-blur-md px-3 py-1.5 text-xs font-bold text-slate-800 dark:text-slate-200 shadow-md">
                                <svg class="w-3.5 h-3.5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{{ $ekskul->jadwal }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Profile Header Block --}}
                    <div class="p-6 md:p-8 bg-white dark:bg-[#1a1a1a] relative">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                            {{-- Crest / Logo & Identity --}}
                            <div class="flex items-start sm:items-center gap-4 sm:gap-5 min-w-0">
                                <div class="shrink-0 -mt-14 md:-mt-16 relative z-10">
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-sky-400 via-blue-500 to-blue-600 text-white font-extrabold flex items-center justify-center text-2xl sm:text-3xl shadow-xl shadow-sky-200 dark:shadow-none border-4 border-white dark:border-[#262626] overflow-hidden">
                                        @if ($logo)
                                            <img src="{{ $logo }}" alt="Logo {{ $ekskul->nama_ekskul }}" class="w-full h-full object-contain p-2 bg-white dark:bg-[#1e1e1e]">
                                        @else
                                            <span class="tracking-wider">{{ $monogram }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                            {{ $ekskul->nama_ekskul }}
                                        </h1>
                                        <span class="px-2.5 py-0.5 rounded-full bg-sky-50 dark:bg-sky-950/60 border border-sky-100 dark:border-sky-800 text-sky-700 dark:text-sky-300 text-xs font-bold">
                                            Resmi
                                        </span>
                                    </div>

                                    @if ($ekskul->tagline)
                                        <p class="text-sm sm:text-base font-medium text-slate-600 dark:text-slate-300 mt-1 flex items-center gap-1.5">
                                            <span class="text-amber-500 text-lg leading-none font-serif">“</span>
                                            <span>{{ $ekskul->tagline }}</span>
                                            <span class="text-amber-500 text-lg leading-none font-serif">”</span>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                                @if ($sudahTerdaftar)
                                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold shadow-sm">
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Kamu Anggota Ekskul Ini
                                    </div>
                                @elseif ($sedangMenunggu)
                                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/50 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs font-bold shadow-sm">
                                        <svg class="w-4 h-4 text-amber-500 shrink-0 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Pendaftaran Menunggu Verifikasi
                                    </div>
                                @elseif ($bisaGabung)
                                    <a href="{{ route('siswa.form-daftar', $ekskul) }}"
                                       class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-400 via-blue-500 to-blue-600 hover:from-sky-500 hover:to-blue-700 text-white text-xs sm:text-sm font-bold shadow-lg shadow-sky-300 dark:shadow-none hover:shadow-sky-400 hover:-translate-y-0.5 transition-all">
                                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                        Daftar Sekarang
                                    </a>
                                @else
                                    <button disabled
                                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-400 dark:text-neutral-500 border border-slate-200 dark:border-neutral-700 text-xs font-bold cursor-not-allowed select-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Pendaftaran Ditutup
                                    </button>
                                @endif

                                <a href="#faq" class="px-4 py-2.5 rounded-xl border border-sky-200 dark:border-neutral-700 bg-sky-50/70 dark:bg-neutral-800 hover:bg-sky-100 dark:hover:bg-neutral-700 text-sky-800 dark:text-sky-300 text-xs font-bold transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    FAQ
                                </a>
                            </div>
                        </div>

                        {{-- QUICK HIGHLIGHT METRIC CARDS --}}
                        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-neutral-800 grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                            {{-- Pembina Card --}}
                            <div class="p-3.5 sm:p-4 rounded-2xl bg-sky-50/60 dark:bg-neutral-900 border border-sky-100 dark:border-neutral-800 flex items-start gap-3 transition hover:border-sky-200 dark:hover:border-neutral-700">
                                <div class="w-9 h-9 rounded-xl bg-sky-100 dark:bg-sky-950 text-sky-700 dark:text-sky-300 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-neutral-400">Pembina</p>
                                    <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 truncate mt-0.5">
                                        {{ $ekskul->pembina->nama ?? 'Belum ditentukan' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Pelatih Card --}}
                            @php
                                $pelatihAktif = ($ekskul->pelatih && $ekskul->pelatih->isTerverifikasi()) ? $ekskul->pelatih : null;
                            @endphp
                            <div class="p-3.5 sm:p-4 rounded-2xl bg-amber-50/60 dark:bg-neutral-900 border border-amber-100 dark:border-neutral-800 flex items-start gap-3 transition hover:border-amber-200 dark:hover:border-neutral-700">
                                <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-neutral-400">Pelatih</p>
                                        @if($pelatihAktif)
                                            <span class="inline-flex items-center gap-0.5 text-[9px] font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/80 px-1.5 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800" title="Terverifikasi Kesiswaan">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                Terverifikasi
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 truncate mt-0.5">
                                        {{ $pelatihAktif ? $pelatihAktif->nama : 'Belum ada' }}
                                    </p>
                                    @if($pelatihAktif && $pelatihAktif->sosmed)
                                        <p class="text-[10px] text-slate-400 dark:text-neutral-500 truncate mt-0.5">
                                            {{ $pelatihAktif->sosmed }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Anggota Card --}}
                            <div class="p-3.5 sm:p-4 rounded-2xl bg-emerald-50/60 dark:bg-neutral-900 border border-emerald-100 dark:border-neutral-800 flex items-start gap-3 transition hover:border-emerald-200 dark:hover:border-neutral-700">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-neutral-400">Anggota Aktif</p>
                                    <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 truncate mt-0.5">
                                        {{ $totalAnggota }} Siswa
                                    </p>
                                </div>
                            </div>

                            {{-- Jadwal Card --}}
                            <div class="p-3.5 sm:p-4 rounded-2xl bg-blue-50/60 dark:bg-neutral-900 border border-blue-100 dark:border-neutral-800 flex items-start gap-3 transition hover:border-blue-200 dark:hover:border-neutral-700">
                                <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-neutral-400">Jadwal Rutin</p>
                                    <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 truncate mt-0.5">
                                        {{ $ekskul->jadwal ?? 'Belum diatur' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- CONTENT SECTIONS --}}
        <div class="mx-auto max-w-6xl px-4 md:px-8 mt-10 space-y-12">

            {{-- 1. TENTANG & VISI TUJUAN EKSKUL --}}
            <section id="tentang" class="scroll-mt-20">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-sky-100 dark:bg-sky-950/70 text-sky-700 dark:text-sky-300 font-extrabold text-[11px] uppercase tracking-wider">
                        Tentang Kami
                    </span>
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                </div>
                <h2 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Mengenal Lebih Dekat {{ $ekskul->nama_ekskul }}
                </h2>

                <div class="mt-6 grid gap-6 lg:grid-cols-12">
                    {{-- Deskripsi Utama --}}
                    <div class="lg:col-span-7 bg-white dark:bg-[#1a1a1a] rounded-3xl p-6 sm:p-8 border border-sky-100 dark:border-neutral-800 shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div class="h-1.5 w-full bg-gradient-to-r from-sky-400 via-blue-500 to-amber-400 absolute top-0 left-0 right-0"></div>

                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-300 border border-sky-100 dark:border-sky-800 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Profil & Eksplorasi</h3>
                                    <p class="text-xs text-slate-400 dark:text-neutral-400">Gambaran kegiatan dan peran ekstrakurikuler</p>
                                </div>
                            </div>

                            <div class="prose prose-slate dark:prose-invert max-w-none">
                                <p class="text-[15px] leading-[1.8] text-slate-700 dark:text-slate-300 whitespace-pre-line">
                                    {{ $ekskul->deskripsi ?: 'Deskripsi untuk ekstrakurikuler ini belum ditambahkan oleh pembina atau ketua ekskul. Silakan hubungi pengurus atau tanyakan langsung melalui formulir pertanyaan di bawah.' }}
                                </p>
                            </div>
                        </div>

                        {{-- Feature Pills --}}
                        <div class="mt-6 pt-5 border-t border-slate-100 dark:border-neutral-800 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Pengembangan Bakat
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Pelatihan Berkala
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Sertifikat & Nilai Resmi
                            </span>
                        </div>
                    </div>

                    {{-- Visi & Tujuan Box --}}
                    <div class="lg:col-span-5 flex flex-col gap-6">
                        {{-- Tujuan Card --}}
                        <div class="bg-gradient-to-br from-amber-50/80 via-white to-yellow-50/50 dark:from-neutral-900 dark:via-[#1a1a1a] dark:to-neutral-900 rounded-3xl p-6 sm:p-7 border border-amber-200/80 dark:border-neutral-800 shadow-sm relative overflow-hidden flex-1">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-yellow-500 text-white flex items-center justify-center shadow-md shadow-amber-200 dark:shadow-none font-bold shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Fokus & Tujuan</h3>
                                    <p class="text-xs text-amber-700/80 dark:text-amber-400">Target pembinaan anggota</p>
                                </div>
                            </div>

                            @if ($ekskul->tujuan)
                                <p class="text-[14px] leading-[1.75] text-slate-700 dark:text-slate-300 bg-white/80 dark:bg-neutral-800/80 rounded-2xl p-4 border border-amber-100 dark:border-neutral-700">
                                    {{ $ekskul->tujuan }}
                                </p>
                            @else
                                <p class="text-xs text-slate-500 dark:text-neutral-400 italic bg-white/60 dark:bg-neutral-800/60 rounded-2xl p-4 border border-amber-100/60 dark:border-neutral-700">
                                    Fokus utama ekskul adalah memfasilitasi minat dan bakat siswa, melatih kepemimpinan, dan membangun karakter kolaboratif dalam lingkungan yang suportif.
                                </p>
                            @endif
                        </div>

                        {{-- Jadwal Card Mini --}}
                        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-6 border border-sky-100 dark:border-neutral-800 shadow-sm">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-300 flex items-center justify-center font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">Timeline Pertemuan</h4>
                                </div>
                                <span class="text-xs font-bold text-sky-600 dark:text-sky-300 bg-sky-50 dark:bg-sky-950/60 px-2.5 py-1 rounded-full">
                                    Mingguan
                                </span>
                            </div>

                            @include('partials.ekskul-minggu', [
                                'ekskul' => $ekskul,
                                'besar' => true,
                                'kelas' => 'mt-1',
                            ])
                        </div>
                    </div>
                </div>
            </section>

            {{-- 2. INFORMASI PRESTASI --}}
            <section id="prestasi" class="scroll-mt-20">
                <div class="flex items-center justify-between gap-4 flex-wrap mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 font-extrabold text-[11px] uppercase tracking-wider flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                Hall of Fame
                            </span>
                            <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                        </div>
                        <h2 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Prestasi & Penghargaan
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-neutral-400 mt-1">
                            Torehan juara dan prestasi membanggakan yang diraih oleh anggota {{ $ekskul->nama_ekskul }}.
                        </p>
                    </div>

                    @if ($ekskul->prestasis->isNotEmpty())
                        <div class="px-3.5 py-1.5 rounded-full bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs font-bold flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ $ekskul->prestasis->count() }} Prestasi Terukir
                        </div>
                    @endif
                </div>

                @if ($ekskul->prestasis->isNotEmpty())
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($ekskul->prestasis as $prestasi)
                            <article class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-amber-100/90 dark:border-neutral-800 shadow-sm hover:shadow-xl hover:border-amber-300 dark:hover:border-amber-500/50 hover:-translate-y-1.5 transition-all duration-300 overflow-hidden flex flex-col group relative">
                                {{-- Golden Top Accent Strip --}}
                                <div class="h-1.5 w-full bg-gradient-to-r from-amber-400 via-yellow-400 to-amber-500"></div>

                                @if ($prestasi->foto)
                                    <div class="relative aspect-[16/10] w-full overflow-hidden bg-slate-100 dark:bg-neutral-800">
                                        <img src="{{ asset('storage/' . $prestasi->foto) }}" alt="{{ $prestasi->judul }}"
                                             loading="lazy"
                                             class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                                        
                                        @if ($prestasi->tahun)
                                            <span class="absolute top-3 right-3 rounded-full bg-amber-400/95 backdrop-blur-md px-3 py-1 text-[11px] font-extrabold text-slate-900 shadow-md flex items-center gap-1.5">
                                                <svg class="w-3 h-3 text-slate-900" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                                                {{ $prestasi->tahun }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <div class="p-6 bg-gradient-to-br from-amber-50/80 via-yellow-50/40 to-white dark:from-neutral-900 dark:via-[#1a1a1a] dark:to-neutral-900 flex items-center justify-between border-b border-amber-100/70 dark:border-neutral-800">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-yellow-500 text-white flex items-center justify-center shadow-lg shadow-amber-200 dark:shadow-none group-hover:rotate-6 transition-transform">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                        </div>

                                        @if ($prestasi->tahun)
                                            <span class="rounded-full bg-amber-100 dark:bg-amber-950/80 border border-amber-200 dark:border-amber-800 px-3 py-1 text-xs font-bold text-amber-800 dark:text-amber-300 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $prestasi->tahun }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="p-5 flex-1 flex flex-col justify-between">
                                    <div>
                                        @if ($prestasi->kategori)
                                            <span class="inline-block px-2.5 py-0.5 rounded-full bg-sky-50 dark:bg-sky-950/60 border border-sky-100 dark:border-sky-800 text-sky-700 dark:text-sky-300 text-[11px] font-bold">
                                                {{ $prestasi->kategori }}
                                            </span>
                                        @endif

                                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-sky-400 transition-colors mt-2.5 leading-snug">
                                            {{ $prestasi->judul }}
                                        </h3>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-neutral-800 flex items-center justify-between text-xs text-slate-400 dark:text-neutral-400">
                                        <span class="flex items-center gap-1 text-amber-600 dark:text-amber-400 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Terverifikasi
                                        </span>
                                        <span class="text-[11px]">SMKN 11 Bandung</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-3xl border border-dashed border-amber-200 dark:border-neutral-800 bg-amber-50/40 dark:bg-neutral-900/40 p-8 text-center">
                        <div class="mx-auto w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum Ada Catatan Prestasi</h4>
                        <p class="text-xs text-slate-500 dark:text-neutral-400 mt-1 max-w-sm mx-auto">
                            Ekskul ini siap mengukir sejarah prestasi baru. Bergabunglah dan jadilah bagian dari tim juara berikutnya!
                        </p>
                    </div>
                @endif
            </section>

            {{-- 3. AGENDA & KEGIATAN --}}
            @if ($ekskul->kegiatans->isNotEmpty())
                <section id="kegiatan" class="scroll-mt-20">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950/70 text-blue-800 dark:text-blue-300 font-extrabold text-[11px] uppercase tracking-wider">
                            Aktivitas
                        </span>
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Agenda & Kegiatan Rutin
                    </h2>

                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        @foreach ($ekskul->kegiatans as $kegiatan)
                            <article class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-5 border border-slate-100 dark:border-neutral-800 shadow-sm hover:shadow-md hover:border-sky-200 dark:hover:border-neutral-700 transition-all flex items-start gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 text-white flex flex-col items-center justify-center shrink-0 shadow-md shadow-sky-200 dark:shadow-none">
                                    <span class="text-lg font-extrabold leading-none">{{ $kegiatan->tanggal_kegiatan->translatedFormat('d') }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider mt-0.5 leading-none">{{ $kegiatan->tanggal_kegiatan->translatedFormat('M') }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $kegiatan->materi }}</h3>
                                    </div>
                                    @if ($kegiatan->deskripsi)
                                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2">
                                            {{ $kegiatan->deskripsi }}
                                        </p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 4. GALERI DOKUMENTASI --}}
            @if ($galeris->isNotEmpty())
                <section id="galeri" class="scroll-mt-20">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/70 text-emerald-800 dark:text-emerald-300 font-extrabold text-[11px] uppercase tracking-wider">
                            Dokumentasi
                        </span>
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Galeri Kegiatan
                    </h2>

                    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                        @foreach ($galeris as $foto)
                            <div class="group relative aspect-square overflow-hidden rounded-2xl bg-slate-100 dark:bg-neutral-800 border border-slate-200/80 dark:border-neutral-800 shadow-sm">
                                <img src="{{ asset('storage/' . $foto) }}" alt="Dokumentasi {{ $ekskul->nama_ekskul }}"
                                     loading="lazy"
                                     class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                    <span class="text-white text-[11px] font-semibold flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                        Lihat Foto
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 5. TESTIMONI SISWA --}}
            @if ($ekskul->testimoniss->isNotEmpty())
                <section id="testimoni" class="scroll-mt-20">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300 font-extrabold text-[11px] uppercase tracking-wider">
                            Kata Mereka
                        </span>
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Cerita & Pengalaman Anggota
                    </h2>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($ekskul->testimoniss as $testimoni)
                            <article class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-6 border border-amber-100 dark:border-neutral-800 shadow-sm hover:shadow-lg transition-all flex flex-col justify-between relative group">
                                <span class="text-4xl font-serif text-amber-300 dark:text-amber-500/50 leading-none select-none absolute top-4 right-5 opacity-40">“</span>
                                
                                <div>
                                    <div class="flex items-center gap-1 text-amber-400 mb-3">
                                        @for($i = 0; $i < 5; $i++)
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <p class="text-sm leading-relaxed text-slate-700 dark:text-slate-300 italic relative z-10">
                                        "{{ $testimoni->quote }}"
                                    </p>
                                </div>

                                <div class="mt-5 pt-4 border-t border-slate-100 dark:border-neutral-800 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-yellow-500 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm shadow-amber-200 dark:shadow-none">
                                        {{ strtoupper(substr($testimoni->nama, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $testimoni->nama }}</p>
                                        <p class="text-[10px] text-slate-400 dark:text-neutral-400 truncate">{{ $testimoni->kelas ?? 'Anggota Aktif' }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 6. FAQ (FREQUENTLY ASKED QUESTIONS) --}}
            <section id="faq" class="scroll-mt-20">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full bg-sky-100 dark:bg-sky-950/70 text-sky-800 dark:text-sky-300 font-extrabold text-[11px] uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pusat Bantuan
                    </span>
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                </div>
                <h2 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Pertanyaan yang Sering Diajukan (FAQ)
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-neutral-400 mt-1">
                    Jawaban resmi pengurus seputar pendaftaran, latihan, dan perlengkapan di {{ $ekskul->nama_ekskul }}.
                </p>

                <div class="mt-6 space-y-3">
                    @forelse ($ekskul->faqs as $faq)
                        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm hover:border-sky-300 dark:hover:border-neutral-700 transition-all duration-200 overflow-hidden">
                            <details class="group">
                                <summary class="flex items-center justify-between gap-4 p-4 sm:p-5 cursor-pointer list-none select-none">
                                    <div class="flex items-center gap-3.5 min-w-0">
                                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm shadow-sky-200 dark:shadow-none">
                                            Q
                                        </div>
                                        <span class="text-sm sm:text-base font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-sky-400 transition-colors">
                                            {{ $faq->pertanyaan }}
                                        </span>
                                    </div>
                                    <div class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-neutral-800 text-sky-600 dark:text-sky-300 flex items-center justify-center shrink-0 transition-transform duration-300 group-open:rotate-180 group-open:bg-blue-600 group-open:text-white">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </summary>

                                <div class="px-5 pb-5 pt-1 border-t border-sky-50 dark:border-neutral-800/80 bg-gradient-to-b from-sky-50/20 to-transparent dark:from-neutral-900/30">
                                    <div class="flex items-start gap-3 mt-3">
                                        <div class="w-6 h-6 rounded-lg bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300 font-extrabold text-[11px] flex items-center justify-center shrink-0">
                                            A
                                        </div>
                                        <div class="text-sm leading-relaxed text-slate-600 dark:text-slate-300 space-y-1">
                                            <p>{{ $faq->jawaban }}</p>
                                        </div>
                                    </div>
                                </div>
                            </details>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-dashed border-sky-200 dark:border-neutral-800 bg-sky-50/40 dark:bg-neutral-900/40 p-8 text-center">
                            <div class="mx-auto w-12 h-12 rounded-2xl bg-sky-100 dark:bg-sky-950 text-sky-600 dark:text-sky-400 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum Ada Pertanyaan Terpublikasi</h4>
                            <p class="text-xs text-slate-500 dark:text-neutral-400 mt-1 max-w-sm mx-auto">
                                Punya pertanyaan seputar ekskul ini? Gunakan formulir di bawah untuk bertanya langsung kepada ketua ekskul!
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- 7. INTERACTIVE FORMS: TESTIMONI & TANYA FAQ --}}
            @if ($kontributor)
                <section class="border-t border-sky-100 dark:border-neutral-800 pt-10">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950/70 text-blue-800 dark:text-blue-300 font-extrabold text-[11px] uppercase tracking-wider">
                            Suarakan Pendapatmu
                        </span>
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Kirim Testimoni & Pertanyaan
                    </h2>

                    <div class="mt-6 grid gap-6 lg:grid-cols-2">
                        {{-- Form Testimoni --}}
                        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-6 sm:p-7 border border-amber-100/90 dark:border-neutral-800 shadow-sm flex flex-col justify-between relative overflow-hidden">
                            <div class="h-1.5 w-full bg-gradient-to-r from-amber-400 to-yellow-500 absolute top-0 left-0 right-0"></div>

                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Kirim Testimoni Pengalaman</h3>
                                        <p class="text-xs text-slate-400 dark:text-neutral-400">Ceritakan kesanmu bergabung di ekskul ini</p>
                                    </div>
                                </div>

                                @if ($hasSubmittedTestimoni)
                                    <div class="mt-4 rounded-2xl border border-amber-200 dark:border-amber-800 bg-amber-50/70 dark:bg-amber-950/40 p-4 text-xs leading-relaxed text-amber-800 dark:text-amber-300 flex items-start gap-3">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Kamu sudah mengirim testimoni untuk ekskul ini. Testimoni akan ditampilkan setelah disetujui ketua.</span>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('ekskul.testimoni.store', $ekskul) }}" class="mt-4 space-y-3">
                                        @csrf
                                        <label for="quote" class="block text-xs font-bold text-slate-700 dark:text-slate-300">Kutipan Testimoni Kamu</label>
                                        <textarea id="quote" name="quote" rows="4" required maxlength="2000"
                                                  placeholder="Tuliskan pengalaman berhargamu, serunya latihan, atau bimbingan pelatih..."
                                                  class="w-full rounded-2xl border border-slate-200 dark:border-neutral-700 bg-slate-50/50 dark:bg-neutral-900 p-3.5 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-neutral-500 focus:bg-white dark:focus:bg-neutral-800 focus:border-amber-400 focus:ring-4 focus:ring-amber-100 dark:focus:ring-amber-900/30 transition resize-y"></textarea>
                                        @error('quote')
                                            <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                        <button type="submit"
                                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-white text-xs font-bold shadow-md shadow-amber-200 dark:shadow-none transition hover:-translate-y-0.5">
                                            Kirim Testimoni
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Form FAQ --}}
                        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-6 sm:p-7 border border-sky-100/90 dark:border-neutral-800 shadow-sm flex flex-col justify-between relative overflow-hidden">
                            <div class="h-1.5 w-full bg-gradient-to-r from-sky-400 to-blue-600 absolute top-0 left-0 right-0"></div>

                            <div>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-950 text-sky-600 dark:text-sky-300 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tanya ke Ketua Ekskul</h3>
                                        <p class="text-xs text-slate-400 dark:text-neutral-400">Pertanyaanmu akan dijawab dan tampil di FAQ</p>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('ekskul.faq.store', $ekskul) }}" class="mt-4 space-y-3">
                                    @csrf
                                    <label for="pertanyaan" class="block text-xs font-bold text-slate-700 dark:text-slate-300">Pertanyaan Kamu</label>
                                    <input id="pertanyaan" type="text" name="pertanyaan" required maxlength="255"
                                           placeholder="Contoh: Apakah pemula yang belum bisa dasar boleh ikut?"
                                           class="w-full rounded-xl border border-slate-200 dark:border-neutral-700 bg-slate-50/50 dark:bg-neutral-900 px-3.5 py-2.5 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-neutral-500 focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900/30 transition">
                                    @error('pertanyaan')
                                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                                    @enderror
                                    <button type="submit"
                                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-400 to-blue-600 hover:from-sky-500 hover:to-blue-700 text-white text-xs font-bold shadow-md shadow-sky-200 dark:shadow-none transition hover:-translate-y-0.5">
                                        Kirim Pertanyaan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- 8. BOTTOM HERO CTA --}}
            <section class="mt-14">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-sky-400 via-blue-500 to-blue-600 p-8 sm:p-10 shadow-xl shadow-sky-200 dark:shadow-none text-white">
                    {{-- Decorative Circles --}}
                    <div class="absolute -right-8 -top-8 w-48 h-48 rounded-full bg-amber-400/30 blur-2xl pointer-events-none"></div>
                    <div class="absolute left-1/3 -bottom-8 w-40 h-40 rounded-full bg-white/20 blur-xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="max-w-xl">
                            <span class="inline-block px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-extrabold text-amber-300 mb-2">
                                Pilihan Ekskul Terbaik
                            </span>
                            <h2 class="text-xl sm:text-3xl font-extrabold tracking-tight">
                                Tertarik Bergabung dengan {{ $ekskul->nama_ekskul }}?
                            </h2>
                            <p class="mt-2 text-xs sm:text-sm text-sky-100 leading-relaxed">
                                Jadilah bagian dari keluarga besar {{ $ekskul->nama_ekskul }}. Kembangkan potensi, raih prestasi, dan jalin persahabatan baru di SMKN 11 Bandung!
                            </p>
                        </div>

                        <div class="shrink-0 flex items-center gap-3">
                            @if ($sudahTerdaftar)
                                <div class="px-5 py-3 rounded-xl bg-white text-emerald-700 text-xs font-bold shadow-lg">
                                    ✓ Sudah Terdaftar
                                </div>
                            @elseif ($sedangMenunggu)
                                <div class="px-5 py-3 rounded-xl bg-amber-400 text-slate-900 text-xs font-bold shadow-lg">
                                    ⏳ Menunggu Verifikasi
                                </div>
                            @elseif ($bisaGabung)
                                <a href="{{ route('siswa.form-daftar', $ekskul) }}"
                                   class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 text-xs sm:text-sm font-extrabold shadow-lg shadow-amber-500/30 hover:scale-105 transition-all">
                                    <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    Daftar Sekarang
                                </a>
                            @else
                                <span class="px-5 py-3 rounded-xl bg-white/20 backdrop-blur-md text-white/80 text-xs font-bold select-none cursor-not-allowed">
                                    Pendaftaran Ditutup
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="border-t border-sky-100 dark:border-neutral-800 bg-white/60 dark:bg-[#141414] py-6 text-center text-xs text-slate-400 dark:text-neutral-500">
        <div class="mx-auto max-w-6xl px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p>&copy; {{ date('Y') }} SOUL - Sistem Operasional Unit Layanan Ekstrakurikuler SMKN 11 Bandung</p>
            <div class="flex items-center gap-4 text-slate-500 dark:text-neutral-400">
                <a href="{{ route('siswa.katalog') }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition">Katalog</a>
                <a href="{{ route('siswa.landing') }}" class="hover:text-sky-600 dark:hover:text-sky-400 transition">Beranda</a>
            </div>
        </div>
    </footer>
</body>
</html>
