@extends('layouts.kesiswaan')

@section('title', 'Detail Ekskul – ' . $ekskul->nama_ekskul)

@section('content')
@php
    $sort = $sort ?? 'status';
    $direction = $direction ?? 'asc';
    $ketua = $ekskul->ketua();

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

    $hasFilter = request()->filled(['q', 'status_anggota']) || request()->filled('sort');
    $statusColors = [
        'diterima'   => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-900/50',
        'pending'    => 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 border border-amber-100 dark:border-amber-900/50',
        'ditolak'    => 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/50',
        'nonaktif'   => 'bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-neutral-400 border border-slate-200 dark:border-neutral-700',
        'peringatan' => 'bg-orange-50 dark:bg-orange-950/40 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-900/50',
    ];
    $statusLabels = [
        'diterima'   => 'Aktif',
        'pending'    => 'Pending',
        'ditolak'    => 'Ditolak',
        'nonaktif'   => 'Nonaktif',
        'peringatan' => 'Peringatan',
    ];
@endphp

<div class="space-y-6 animate-fade-up">

    {{-- HERO CARD --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200 dark:shadow-none">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-4">
                {{-- Logo / Inisial --}}
                <div class="w-16 h-16 rounded-2xl overflow-hidden shrink-0 border-2 border-white/30 shadow-lg bg-white/20 flex items-center justify-center font-black text-xl uppercase">
                    @if ($ekskul->logo)
                        <img src="{{ asset('storage/' . $ekskul->logo) }}" alt="{{ $ekskul->nama_ekskul }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($ekskul->nama_ekskul, 0, 2)) }}
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        {{ $ekskul->nama_ekskul }}
                    </h1>
                    <p class="text-xs text-white/80 mt-1 leading-relaxed max-w-xl">
                        {{ $ekskul->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>
                    <div class="flex items-center gap-2 mt-3 flex-wrap">
                        @if ($ekskul->kategori)
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/20 text-white backdrop-blur">
                                {{ $ekskul->kategori }}
                            </span>
                        @endif
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $ekskul->status ? 'bg-emerald-400/30 text-white' : 'bg-rose-500/30 text-white' }} backdrop-blur">
                            {{ $ekskul->status ? '✓ Aktif' : '✕ Nonaktif' }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $ekskul->is_open_recruitment ? 'bg-amber-300/30 text-white' : 'bg-white/10 text-white/70' }} backdrop-blur">
                            {{ $ekskul->is_open_recruitment ? '● Buka Pendaftaran' : '○ Tutup Pendaftaran' }}
                        </span>
                        @if ($ekskul->jadwal)
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/15 text-white/90 backdrop-blur">
                                🕐 {{ $ekskul->jadwal }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <a href="{{ route('kesiswaan.ekskuls.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-2xl hover:bg-white/20 transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </div>

    {{-- INFO CARDS: PEMBINA, KETUA, PELATIH --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Pembina --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-slate-400 dark:text-neutral-400 uppercase tracking-wider">Pembina Ekskul</span>
                    <div class="w-7 h-7 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>
                @if ($ekskul->pembina)
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl overflow-hidden shrink-0 bg-indigo-100 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-extrabold flex items-center justify-center text-xs uppercase shadow-xs">
                            @if ($ekskul->pembina->foto_url)
                                <img src="{{ $ekskul->pembina->foto_url }}" alt="{{ $ekskul->pembina->nama }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($ekskul->pembina->nama, 0, 2)) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $ekskul->pembina->nama }}</div>
                            <div class="text-[11px] text-slate-400 dark:text-neutral-400 truncate">NIP: {{ $ekskul->pembina->nip ?? '-' }}</div>
                            <div class="text-[11px] text-slate-400 dark:text-neutral-400 truncate">{{ $ekskul->pembina->email ?? '-' }}</div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 text-slate-400 dark:text-neutral-500 italic text-xs py-2">
                        <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center text-slate-400">?</div>
                        Belum ada pembina
                    </div>
                @endif
            </div>
        </div>

        {{-- Ketua Ekskul --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-slate-400 dark:text-neutral-400 uppercase tracking-wider">Ketua Ekskul</span>
                    <div class="w-7 h-7 rounded-xl bg-amber-50 dark:bg-amber-950/50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M2 19l2-9 5 5 3-8 3 8 5-5 2 9H2z"/></svg>
                    </div>
                </div>
                @if ($ketua)
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl overflow-hidden shrink-0 bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 font-extrabold flex items-center justify-center text-xs uppercase shadow-xs border border-amber-200 dark:border-amber-900/50">
                            @if ($ketua->foto_url)
                                <img src="{{ $ketua->foto_url }}" alt="{{ $ketua->nama }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($ketua->nama, 0, 2)) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $ketua->nama }}</span>
                            </div>
                            <div class="text-[11px] text-slate-400 dark:text-neutral-400 truncate">NIS: {{ $ketua->nis }} · {{ $ketua->kelas->nama ?? '-' }}</div>
                            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Ketua Aktif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-2 py-3 text-center">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 flex items-center justify-center text-amber-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-neutral-400">Belum ada ketua</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Pelatih --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold text-slate-400 dark:text-neutral-400 uppercase tracking-wider">Pelatih Ekskul</span>
                    <div class="w-7 h-7 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                @if ($ekskul->pelatih)
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 font-extrabold flex items-center justify-center text-xs uppercase shrink-0 shadow-xs">
                            {{ strtoupper(substr($ekskul->pelatih->nama, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-sm text-slate-900 dark:text-white truncate">{{ $ekskul->pelatih->nama }}</span>
                                @if($ekskul->pelatih->isTerverifikasi())
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300">Terverifikasi</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-slate-400 dark:text-neutral-400 truncate">{{ ucfirst($ekskul->pelatih->jenis_kelamin ?? '-') }} · {{ $ekskul->pelatih->no_hp ?? '-' }}</div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 text-slate-400 dark:text-neutral-500 italic text-xs py-2">
                        <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-neutral-800 flex items-center justify-center text-slate-400">?</div>
                        Belum ada pelatih
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER ANGGOTA --}}
    <form method="GET" action="{{ route('kesiswaan.ekskuls.show', $ekskul) }}" class="bg-white dark:bg-[#1a1a1a] p-4 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm">
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if(request('direction'))
            <input type="hidden" name="direction" value="{{ request('direction') }}">
        @endif
        <div class="flex flex-col lg:flex-row gap-3 items-stretch lg:items-center">
            <div class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>Anggota ({{ $anggota->total() }})</span>
            </div>
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIS anggota..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-neutral-800/80 border border-slate-200 dark:border-neutral-700 rounded-2xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>
            <select name="status_anggota" class="w-full lg:w-48 px-3.5 py-2.5 bg-slate-50 dark:bg-neutral-800/80 border border-slate-200 dark:border-neutral-700 rounded-2xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                <option value="">Semua Status</option>
                <option value="diterima" {{ request('status_anggota') === 'diterima' ? 'selected' : '' }}>Aktif</option>
                <option value="pending" {{ request('status_anggota') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="ditolak" {{ request('status_anggota') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                <option value="nonaktif" {{ request('status_anggota') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                <option value="peringatan" {{ request('status_anggota') === 'peringatan' ? 'selected' : '' }}>Peringatan</option>
            </select>
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">Filter</button>
                @if ($hasFilter)
                    <a href="{{ route('kesiswaan.ekskuls.show', $ekskul) }}"
                       class="px-4 py-2.5 bg-slate-100 dark:bg-neutral-800 hover:bg-slate-200 dark:hover:bg-neutral-700 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-2xl transition">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- TABEL ANGGOTA --}}
    <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-neutral-800/60 border-b border-slate-100 dark:border-neutral-800 text-[11px] font-bold text-slate-400 dark:text-neutral-400 tracking-wider uppercase">
                        <th class="py-4 px-5">
                            <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Siswa
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nama') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-4 px-5">Kelas</th>
                        <th class="py-4 px-5">
                            <a href="{{ $sortLink('tanggal_daftar') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Bergabung
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('tanggal_daftar') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-4 px-5">
                            <a href="{{ $sortLink('status') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Status
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('status') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-4 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                    @forelse ($anggota as $daftar)
                        @php
                            $s = $daftar->siswa;
                            $isKetua = $s && $ketua && $s->id === $ketua->id;
                        @endphp
                        <tr class="hover:bg-sky-50/30 dark:hover:bg-neutral-800/40 transition">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0 flex items-center justify-center font-extrabold text-xs uppercase {{ $isKetua ? 'bg-amber-100 text-amber-800 border-2 border-amber-300' : 'bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300' }}">
                                        @if ($s && $s->foto_url)
                                            <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($s->nama ?? 'S', 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900 dark:text-white leading-snug truncate">{{ $s->nama ?? '-' }}</span>
                                            @if($isKetua)
                                                <span class="px-1.5 py-0.5 rounded-md bg-amber-500 text-white text-[9px] font-extrabold uppercase">Ketua</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400 dark:text-neutral-400 truncate flex items-center gap-2">
                                            <span>NIS: {{ $s->nis ?? '-' }}</span>
                                            @if(($s->jenis_kelamin ?? '') === 'perempuan')
                                                <span class="text-rose-400">♀</span>
                                            @else
                                                <span class="text-sky-400">♂</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50">
                                    {{ $s->kelas?->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap text-slate-500 dark:text-neutral-400">
                                {{ $daftar->tanggal_daftar?->isoFormat('D MMM Y') ?? '-' }}
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] {{ $statusColors[$daftar->status] ?? 'bg-slate-100 text-slate-500' }}">
                                    {{ $statusLabels[$daftar->status] ?? ucfirst($daftar->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                @if ($s && $s->user_id)
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('kesiswaan.users.show', $s->user_id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 font-bold rounded-xl transition text-xs"
                                           title="Lihat Detail {{ $s->nama }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </a>
                                        <a href="{{ route('kesiswaan.users.edit', $s->user_id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 dark:bg-sky-950/40 hover:bg-sky-100 dark:hover:bg-sky-900/60 text-sky-700 dark:text-sky-300 font-bold rounded-xl transition text-xs"
                                           title="Edit Data {{ $s->nama }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum ada akun</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400 dark:text-neutral-500">
                                    <svg class="w-10 h-10 mb-2 text-slate-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600 dark:text-neutral-400">Belum ada anggota di ekskul ini.</p>
                                    @if ($hasFilter)
                                        <a href="{{ route('kesiswaan.ekskuls.show', $ekskul) }}" class="mt-3 text-xs font-bold text-sky-600 hover:text-sky-700">
                                            Tampilkan semua anggota
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.table-pagination', ['rows' => $anggota, 'label' => 'anggota'])
    </div>

</div>
@endsection
