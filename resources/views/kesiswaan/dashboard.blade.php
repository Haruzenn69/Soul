@extends('layouts.kesiswaan')

@section('title', 'Dashboard Kesiswaan')

@section('content')
@php
    $hour = (int) now()->format('H');
    $greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 15 ? 'Selamat Siang' : ($hour < 18 ? 'Selamat Sore' : 'Selamat Malam'));
    $userName = auth()->user()->username ?? 'Kesiswaan';
@endphp

<div class="space-y-6 animate-fade-up">

    {{-- 1. PAGE TITLE & ACTION BAR (Reference UI) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Dashboard</h1>
            <p class="text-xs text-slate-400 dark:text-slate-500 font-medium mt-0.5">Ringkasan aktivitas kesiswaan dan manajemen sekolah</p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            {{-- Filter Dropdown (By day v) --}}
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-neutral-800 border border-slate-200/80 dark:border-neutral-700 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition shadow-sm">
                    <span>Semua Data</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            {{-- Info Tooltip Button (i) --}}
            <button type="button" title="Informasi Dashboard" class="w-9 h-9 rounded-xl bg-white dark:bg-neutral-800 border border-slate-200/80 dark:border-neutral-700 flex items-center justify-center text-slate-400 hover:text-slate-600 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </button>

            {{-- "+ Create new" Primary Button with Quick Dropdown --}}
            <div class="relative" id="create-new-dropdown-wrapper">
                <button type="button" id="btn-create-new" onclick="toggleCreateDropdown()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-500/25 hover:shadow-lg hover:shadow-blue-500/35 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Create new</span>
                </button>

                <div id="create-dropdown-menu" class="hidden absolute right-0 mt-2 w-52 rounded-2xl bg-white dark:bg-neutral-800 border border-slate-100 dark:border-neutral-700 shadow-xl py-2 z-50 animate-fade-down">
                    <a href="{{ route('kesiswaan.users.create') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-sky-50 dark:hover:bg-neutral-700 transition">
                        <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-xs">👤</span>
                        Tambah Akun Baru
                    </a>
                    <a href="{{ route('kesiswaan.users.create', ['role' => 'siswa']) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-neutral-700 transition">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs">🎒</span>
                        Tambah Siswa
                    </a>
                    <a href="{{ route('kesiswaan.ekskuls.create') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-neutral-700 transition">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">🏆</span>
                        Tambah Ekskul
                    </a>
                    <a href="{{ route('kesiswaan.kelas.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-violet-50 dark:hover:bg-neutral-700 transition">
                        <span class="w-6 h-6 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center text-xs">🏫</span>
                        Kelola Kelas
                    </a>
                    <div class="my-1 border-t border-slate-100 dark:border-neutral-700"></div>
                    <a href="{{ route('kesiswaan.tahun-ajaran.index') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold text-blue-600 hover:bg-blue-50 dark:hover:bg-neutral-700 transition">
                        <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs">📅</span>
                        Tahun Ajaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. GREETING CARD + NOTIFICATIONS PANEL ROW (Reference UI) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        {{-- LEFT: GREETING CARD WITH MEDITATING BEAR MASCOT --}}
        <div class="lg:col-span-7 xl:col-span-8 relative rounded-[2rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 shadow-[0_12px_32px_-8px_rgba(0,0,0,0.06)] p-6 md:p-8 flex flex-col justify-between overflow-hidden">
            {{-- Soft gradient background accents --}}
            <div class="absolute top-0 right-0 w-80 h-80 rounded-full bg-sky-50/70 dark:bg-sky-950/20 blur-3xl pointer-events-none -translate-y-1/3 translate-x-1/4"></div>
            <div class="absolute bottom-0 left-0 w-60 h-60 rounded-full bg-amber-50/60 dark:bg-amber-950/20 blur-3xl pointer-events-none translate-y-1/3 -translate-x-1/4"></div>

            <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
                {{-- Left Text & Quick Actions --}}
                <div class="flex-1 text-center md:text-left min-w-0">
                    <h2 class="text-2xl sm:text-3xl md:text-[2rem] font-extrabold text-slate-900 dark:text-white tracking-tight leading-snug">
                        Hi, {{ $userName }}!
                    </h2>
                    <p class="text-sm text-slate-400 dark:text-slate-500 mt-1 font-medium">
                        What are we doing today?
                    </p>

                    {{-- 4 Quick Action Links (Reference UI Style with icon circles) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-3 mt-6 max-w-md mx-auto md:mx-0">
                        {{-- Check Calendar / Tahun Ajaran --}}
                        <a href="{{ route('kesiswaan.tahun-ajaran.index') }}" class="group flex items-center gap-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-blue-600 transition">
                            <span class="w-6 h-6 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </span>
                            <span>Check Calendar</span>
                        </a>

                        {{-- Manage Wallet / Ekskul --}}
                        <a href="{{ route('kesiswaan.ekskuls.index') }}" class="group flex items-center gap-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-amber-600 transition">
                            <span class="w-6 h-6 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </span>
                            <span>Manage Wallet</span>
                        </a>

                        {{-- Manage Workers / Siswa --}}
                        <a href="{{ route('kesiswaan.siswa.index') }}" class="group flex items-center gap-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-rose-600 transition">
                            <span class="w-6 h-6 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <span>Manage Workers</span>
                        </a>

                        {{-- Manage Projects / Akun --}}
                        <a href="{{ route('kesiswaan.users.index') }}" class="group flex items-center gap-2.5 text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-emerald-600 transition">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </span>
                            <span>Manage Projects</span>
                        </a>
                    </div>
                </div>

                {{-- Mascot Bear Illustration (Exact replica from Reference) --}}
                <div class="shrink-0 flex items-center justify-center w-52 sm:w-60 md:w-64 -mb-4 md:-mb-8 md:-mr-4">
                    @include('partials.mascot-bear')
                </div>
            </div>
        </div>

        {{-- RIGHT: NOTIFICATIONS PANEL (Reference UI) --}}
        <div class="lg:col-span-5 xl:col-span-4 rounded-[2rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 shadow-[0_12px_32px_-8px_rgba(0,0,0,0.06)] p-6 flex flex-col justify-between">
            <div>
                {{-- Header with Bell Icon and "See all" --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-700 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Notifications</h3>
                    </div>
                    <a href="{{ route('kesiswaan.notifikasi') }}" class="text-xs font-bold text-slate-400 hover:text-blue-600 transition">See all</a>
                </div>

                {{-- Notification Cards Stack (Reference UI) --}}
                <div class="space-y-3">
                    @php
                        // Default mock notification cards if none or low count in db, ensuring identical visual aesthetic
                        $presetStyles = [
                            ['bg' => 'bg-amber-400', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>'],
                            ['bg' => 'bg-rose-500', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
                            ['bg' => 'bg-blue-600', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>'],
                        ];
                    @endphp

                    @forelse($notifikasis->take(3) as $idx => $notif)
                        @php
                            $style = $presetStyles[$idx % count($presetStyles)];
                        @endphp
                        <a href="{{ route('kesiswaan.notifikasi') }}" class="group relative flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50/80 dark:bg-neutral-800/80 hover:bg-slate-100 dark:hover:bg-neutral-800 border border-slate-100 dark:border-neutral-700/60 transition shadow-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl {{ $style['bg'] }} text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        {!! $style['icon'] !!}
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">{{ $notif->judul }}</h4>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500 truncate mt-0.5">{{ Str::limit($notif->pesan, 38) }}</p>
                                </div>
                            </div>
                            <span class="w-6 h-6 rounded-full border border-slate-200 dark:border-neutral-700 text-slate-400 flex items-center justify-center group-hover:text-blue-600 group-hover:border-blue-300 transition shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </a>
                    @empty
                        {{-- Fallback matching reference UI format --}}
                        <div class="group relative flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50/80 dark:bg-neutral-800/80 hover:bg-slate-100 transition shadow-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl bg-amber-400 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">Pendaftaran Ekskul Baru</h4>
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ $pendingPendaftaran }} pendaftaran menunggu verifikasi.</p>
                                </div>
                            </div>
                            <span class="w-6 h-6 rounded-full border border-slate-200 text-slate-400 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </div>

                        <div class="group relative flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50/80 dark:bg-neutral-800/80 hover:bg-slate-100 transition shadow-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl bg-rose-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">Perubahan Profil Siswa</h4>
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">Siswa memperbarui riwayat profil akun.</p>
                                </div>
                            </div>
                            <span class="w-6 h-6 rounded-full border border-slate-200 text-slate-400 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </div>

                        <div class="group relative flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50/80 dark:bg-neutral-800/80 hover:bg-slate-100 transition shadow-sm">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">Tahun Ajaran Aktif</h4>
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ $tahunAjaranAktif?->nama ?? '2026/2027' }} sedang berjalan.</p>
                                </div>
                            </div>
                            <span class="w-6 h-6 rounded-full border border-slate-200 text-slate-400 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- 3. FOUR STAT CARDS ROW (Exact Reference UI with layered tab shadow effect) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        {{-- CARD 1: Total Siswa (Red/Pink squircle) --}}
        <div class="relative group">
            <div class="absolute -bottom-2 inset-x-4 h-4 bg-slate-200/50 dark:bg-neutral-800/80 rounded-2xl -z-10 group-hover:-bottom-2.5 transition-all"></div>
            <div class="relative rounded-[1.75rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 p-6 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.05)] transition-all">
                <div class="flex items-center justify-between mb-4">
                    {{-- Squircle pink/red icon --}}
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200/60 dark:border-rose-900/50 text-rose-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <button class="w-6 h-6 rounded-lg text-slate-300 hover:text-slate-600 transition flex items-center justify-center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                    </button>
                </div>
                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 leading-snug">Total Siswa<br>Aktif Sekolah</p>
                <p class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white mt-2 tracking-tight">{{ number_format($totalSiswa) }}</p>
            </div>
        </div>

        {{-- CARD 2: Total Ekskul (Blue squircle) --}}
        <div class="relative group">
            <div class="absolute -bottom-2 inset-x-4 h-4 bg-slate-200/50 dark:bg-neutral-800/80 rounded-2xl -z-10 group-hover:-bottom-2.5 transition-all"></div>
            <div class="relative rounded-[1.75rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 p-6 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.05)] transition-all">
                <div class="flex items-center justify-between mb-4">
                    {{-- Squircle blue icon --}}
                    <div class="w-11 h-11 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200/60 dark:border-blue-900/50 text-blue-600 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <button class="w-6 h-6 rounded-lg text-slate-300 hover:text-slate-600 transition flex items-center justify-center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                    </button>
                </div>
                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 leading-snug">Total Ekskul<br>Terdaftar</p>
                <p class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white mt-2 tracking-tight">{{ number_format($totalEkskul) }}</p>
            </div>
        </div>

        {{-- CARD 3: Tahun Ajaran (Amber/Yellow squircle) --}}
        <div class="relative group">
            <div class="absolute -bottom-2 inset-x-4 h-4 bg-slate-200/50 dark:bg-neutral-800/80 rounded-2xl -z-10 group-hover:-bottom-2.5 transition-all"></div>
            <div class="relative rounded-[1.75rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 p-6 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.05)] transition-all">
                <div class="flex items-center justify-between mb-4">
                    {{-- Squircle yellow icon --}}
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-900/50 text-amber-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <button class="w-6 h-6 rounded-lg text-slate-300 hover:text-slate-600 transition flex items-center justify-center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                    </button>
                </div>
                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 leading-snug">Tahun Ajaran<br>Aktif Sekarang</p>
                <p class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white mt-2 tracking-tight">{{ $tahunAjaranAktif?->nama ?? '2026/2027' }}</p>
            </div>
        </div>

        {{-- CARD 4: Pendaftaran Pending (Green squircle) --}}
        <div class="relative group">
            <div class="absolute -bottom-2 inset-x-4 h-4 bg-slate-200/50 dark:bg-neutral-800/80 rounded-2xl -z-10 group-hover:-bottom-2.5 transition-all"></div>
            <div class="relative rounded-[1.75rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 p-6 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.05)] transition-all">
                <div class="flex items-center justify-between mb-4">
                    {{-- Squircle green icon --}}
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-900/50 text-emerald-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <button class="w-6 h-6 rounded-lg text-slate-300 hover:text-slate-600 transition flex items-center justify-center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                    </button>
                </div>
                <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 leading-snug">Pendaftaran Ekskul<br>Menunggu Verifikasi</p>
                <p class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white mt-2 tracking-tight">{{ number_format($pendingPendaftaran) }}</p>
            </div>
        </div>

    </div>

    {{-- 4. CHARTS & ANALYTICS SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Line Chart: Pembuatan Akun --}}
        <div class="lg:col-span-2 rounded-[2rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Pertumbuhan Akun</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium mt-0.5">Tren registrasi akun 6 bulan terakhir</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <span class="text-xs font-extrabold text-slate-700 dark:text-slate-300">{{ $totalUsers }} Total Akun</span>
                </div>
            </div>
            <div class="h-56 md:h-64 relative">
                <canvas id="akunChart"></canvas>
            </div>
        </div>

        {{-- Side Info: Tahun Ajaran & Ekskul Status --}}
        <div class="rounded-[2rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] p-6 flex flex-col justify-between gap-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-3">Ringkasan Sistem</h3>

                {{-- Status Rekrutmen Card --}}
                <div class="rounded-2xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-100 dark:border-neutral-700/60 p-4 mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Rekrutmen Ekskul</span>
                        <span class="text-xs font-extrabold text-emerald-600 bg-emerald-100 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">{{ $ekskulBuka }} Buka</span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-neutral-700 h-2 rounded-full overflow-hidden mt-3">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $totalEkskul > 0 ? round(($ekskulBuka / $totalEkskul) * 100) : 0 }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-slate-400 font-bold mt-2">
                        <span>{{ $ekskulBuka }} Ekskul Terbuka</span>
                        <span>{{ max(0, $totalEkskul - $ekskulBuka) }} Tertutup</span>
                    </div>
                </div>

                {{-- Kelas Terdaftar Card --}}
                <div class="rounded-2xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-100 dark:border-neutral-700/60 p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Kelas Terdaftar</span>
                        <span class="text-xs font-extrabold text-violet-600 bg-violet-100 dark:bg-violet-950/40 px-2 py-0.5 rounded-full">{{ $totalKelas }} Kelas</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2 font-medium">Tingkat 10, 11, dan 12 pada tahun ajaran aktif.</p>
                </div>
            </div>

            <a href="{{ route('kesiswaan.tahun-ajaran.index') }}" class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-neutral-800 hover:bg-slate-200 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-bold text-center transition flex items-center justify-center gap-1.5">
                <span>Kelola Kenaikan Kelas</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    {{-- 5. TWO BAR CHARTS ROW --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Chart Pembuatan Kelas --}}
        <div class="rounded-[2rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Pembuatan Kelas</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium mt-0.5">Penambahan kelas tiap bulan</p>
                </div>
                <span class="text-lg font-extrabold text-amber-500">{{ $totalKelas }}</span>
            </div>
            <div class="h-44 md:h-52 relative">
                <canvas id="kelasChart"></canvas>
            </div>
        </div>

        {{-- Chart Pembuatan Ekskul --}}
        <div class="rounded-[2rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Pembuatan Ekskul</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium mt-0.5">Penambahan ekstrakurikuler baru</p>
                </div>
                <span class="text-lg font-extrabold text-emerald-500">{{ $totalEkskul }}</span>
            </div>
            <div class="h-44 md:h-52 relative">
                <canvas id="ekskulChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 6. QUICK ACCESS MODULES --}}
    <div class="rounded-[2rem] bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-800 shadow-[0_10px_30px_-10px_rgba(0,0,0,0.05)] p-6">
        <div class="flex justify-between items-center mb-5">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Modul Pengelolaan Master</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Akses cepat ke konfigurasi dan manajemen data</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('kesiswaan.ekskuls.index') }}" class="group p-4 rounded-2xl bg-slate-50/70 dark:bg-neutral-800/60 hover:bg-slate-100 border border-slate-100 dark:border-neutral-700/60 transition flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Data Ekskul</h4>
                        <p class="text-[10px] text-slate-400">{{ $totalEkskul }} Ekskul</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>

            <a href="{{ route('kesiswaan.kelas.index') }}" class="group p-4 rounded-2xl bg-slate-50/70 dark:bg-neutral-800/60 hover:bg-slate-100 border border-slate-100 dark:border-neutral-700/60 transition flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-900/40 text-violet-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Data Kelas</h4>
                        <p class="text-[10px] text-slate-400">{{ $totalKelas }} Kelas</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-violet-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>

            <a href="{{ route('kesiswaan.siswa.index') }}" class="group p-4 rounded-2xl bg-slate-50/70 dark:bg-neutral-800/60 hover:bg-slate-100 border border-slate-100 dark:border-neutral-700/60 transition flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Data Siswa</h4>
                        <p class="text-[10px] text-slate-400">{{ $totalSiswa }} Siswa</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-amber-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>

            <a href="{{ route('kesiswaan.laporan-penilaian.index') }}" class="group p-4 rounded-2xl bg-slate-50/70 dark:bg-neutral-800/60 hover:bg-slate-100 border border-slate-100 dark:border-neutral-700/60 transition flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">Penilaian</h4>
                        <p class="text-[10px] text-slate-400">Laporan & Nilai</p>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-300 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
    function toggleCreateDropdown() {
        const menu = document.getElementById('create-dropdown-menu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('create-new-dropdown-wrapper');
        const menu = document.getElementById('create-dropdown-menu');
        if (wrapper && menu && !wrapper.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const anim = reduceMotion ? {} : { duration: 900, easing: 'easeOutQuart' };

        Chart.defaults.font.family = '"Plus Jakarta Sans", system-ui, sans-serif';
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#64748b';

        const akunLabels = @json($akunPerBulan['labels']);
        const akunData   = @json($akunPerBulan['data']);
        const kelasData  = @json($kelasPerBulan['data']);
        const ekskulData = @json($ekskulPerBulan['data']);

        const dashboardCharts = [];

        function getChartColors() {
            const isDark = document.documentElement.classList.contains('dark-mode') || document.documentElement.classList.contains('dark');
            return {
                isDark: isDark,
                gridColor: isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(226, 232, 240, 0.6)',
                tickColor: isDark ? '#94a3b8' : '#64748b',
                tooltipBg: isDark ? '#1e293b' : '#0F172A',
                pointBorderColor: isDark ? '#1e293b' : '#fff',
            };
        }

        const themeColors = getChartColors();

        // --- GRAFIK AKUN (line) ---
        const akunCanvas = document.getElementById('akunChart');
        if (akunCanvas) {
            const ctx = akunCanvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, ctx.canvas.clientHeight || 260);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0.01)');

            const akunChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: akunLabels,
                    datasets: [{
                        label: 'Akun',
                        data: akunData,
                        borderColor: '#2563EB',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.45,
                        borderWidth: 3,
                        pointBackgroundColor: '#2563EB',
                        pointBorderColor: themeColors.pointBorderColor,
                        pointBorderWidth: 2.5,
                        pointRadius: 4.5,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: anim,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: themeColors.tooltipBg,
                            titleFont: { weight: 700 },
                            padding: 10,
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: function (ctx) { return ctx.parsed.y + ' akun terdaftar'; }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, stepSize: 1, color: themeColors.tickColor },
                            grid: { color: themeColors.gridColor, drawBorder: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { maxRotation: 0, autoSkip: true, color: themeColors.tickColor }
                        }
                    }
                }
            });
            dashboardCharts.push(akunChart);
        }

        const barOptions = (color) => {
            const c = getChartColors();
            return {
                responsive: true,
                maintainAspectRatio: false,
                animation: anim,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: c.tooltipBg,
                        titleFont: { weight: 700 },
                        padding: 10,
                        cornerRadius: 12,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, stepSize: 1, font: { size: 10 }, color: c.tickColor },
                        grid: { color: c.gridColor, drawBorder: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, maxRotation: 0, autoSkip: true, color: c.tickColor }
                    }
                }
            };
        };

        // --- GRAFIK KELAS (bar) ---
        const kelasCanvas = document.getElementById('kelasChart');
        if (kelasCanvas) {
            const kelasChart = new Chart(kelasCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: akunLabels,
                    datasets: [{
                        label: 'Kelas',
                        data: kelasData,
                        backgroundColor: '#F59E0B',
                        hoverBackgroundColor: '#D97706',
                        borderRadius: 10,
                        maxBarThickness: 32,
                    }]
                },
                options: barOptions('#F59E0B')
            });
            dashboardCharts.push(kelasChart);
        }

        // --- GRAFIK EKSKUL (bar) ---
        const ekskulCanvas = document.getElementById('ekskulChart');
        if (ekskulCanvas) {
            const ekskulChart = new Chart(ekskulCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: akunLabels,
                    datasets: [{
                        label: 'Ekskul',
                        data: ekskulData,
                        backgroundColor: '#10B981',
                        hoverBackgroundColor: '#059669',
                        borderRadius: 10,
                        maxBarThickness: 32,
                    }]
                },
                options: barOptions('#10B981')
            });
            dashboardCharts.push(ekskulChart);
        }

        function updateChartThemes() {
            const c = getChartColors();
            dashboardCharts.forEach(chart => {
                if (!chart) return;
                if (chart.options.scales?.y) {
                    if (chart.options.scales.y.grid) chart.options.scales.y.grid.color = c.gridColor;
                    if (chart.options.scales.y.ticks) chart.options.scales.y.ticks.color = c.tickColor;
                }
                if (chart.options.scales?.x) {
                    if (chart.options.scales.x.ticks) chart.options.scales.x.ticks.color = c.tickColor;
                }
                if (chart.options.plugins?.tooltip) {
                    chart.options.plugins.tooltip.backgroundColor = c.tooltipBg;
                }
                chart.update('none');
            });
        }

        window.addEventListener('soul-theme-change', updateChartThemes);
    });
</script>
@endsection 