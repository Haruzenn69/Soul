@extends('layouts.kesiswaan')
@section('title', 'Kelas ' . $kela->nama . ' - Tahun Ajaran ' . $tahunAjaran->nama)

@section('content')
@php
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

    $isKelas11 = $kela->tingkat === 'xi';
    $isAktif = $tahunAjaran->isAktif();
    $hasPending = $availableSiswa->isNotEmpty();
    $isEmpty = $siswas->total() === 0;
@endphp

<div class="space-y-6 animate-fade-up">

    {{-- HERO CARD --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-500 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <a href="{{ route('kesiswaan.tahun-ajaran.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs text-white/80 hover:text-white font-bold transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        Tahun Ajaran
                    </a>
                    <span class="text-white/40">/</span>
                    <span class="text-xs text-white/90 font-medium">{{ $tahunAjaran->nama }}</span>
                    <span class="text-white/40">/</span>
                    <span class="text-xs text-white font-bold">{{ $kela->nama }}</span>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        Kelas {{ $kela->nama }}
                    </h1>
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-white/20 backdrop-blur border border-white/20 text-white">
                        Tingkat {{ strtoupper($kela->tingkat) }}
                    </span>
                    @if($isAktif)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-400/30 text-emerald-100 border border-emerald-300/30">
                            TA Aktif
                        </span>
                    @endif
                </div>

                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Tahun Ajaran: <span class="font-bold text-white">{{ $tahunAjaran->nama }}</span>
                    &bull; Jurusan: <span class="font-bold text-white">{{ strtoupper($kela->jurusan) }}</span>
                    &bull; Rombel: <span class="font-bold text-white">{{ $kela->rombel }}</span>
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.tahun-ajaran.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-xl hover:bg-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Daftar TA & Kelas
                </a>
                @if($isAktif && $isKelas11 && $hasPending)
                    <button type="button" onclick="document.getElementById('penempatan-section').scrollIntoView({behavior: 'smooth'})"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-slate-900 font-extrabold text-xs rounded-xl shadow-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Ambil Siswa Kelas 10 ({{ $availableSiswa->count() }})
                    </button>
                @endif
                <a href="{{ route('kesiswaan.kelas.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-sky-700 font-bold text-xs rounded-xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Kelola di Data Kelas
                </a>
            </div>
        </div>

        {{-- QUICK STATS PILLS --}}
        <div class="relative mt-6 pt-5 border-t border-white/15">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/15">
                    <span class="text-[10px] font-bold text-white/75 uppercase tracking-wider block">Total Siswa</span>
                    <span class="text-xl font-black text-white mt-0.5 block">{{ $stats['total'] }}</span>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/15">
                    <span class="text-[10px] font-bold text-white/75 uppercase tracking-wider block">Laki-Laki (♂)</span>
                    <span class="text-xl font-black text-white mt-0.5 block">{{ $stats['laki'] }}</span>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/15">
                    <span class="text-[10px] font-bold text-white/75 uppercase tracking-wider block">Perempuan (♀)</span>
                    <span class="text-xl font-black text-white mt-0.5 block">{{ $stats['perempuan'] }}</span>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/15">
                    @if($isKelas11)
                        <span class="text-[10px] font-bold text-white/75 uppercase tracking-wider block">Sumber Kelas 10</span>
                        <span class="text-sm font-extrabold text-amber-300 mt-1 block truncate">{{ $selectedSourceKelas?->nama ?? 'Tidak ada' }}</span>
                    @else
                        <span class="text-[10px] font-bold text-white/75 uppercase tracking-wider block">Tingkat</span>
                        <span class="text-sm font-extrabold text-white mt-1 block">Tingkat {{ strtoupper($kela->tingkat) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION UNTUK KELAS 11: AMBIL SISWA DARI KELAS 10 --}}
    @if($isAktif && $isKelas11 && $sourceKelasOptions->isNotEmpty())
        <div id="penempatan-section" class="bg-gradient-to-br from-amber-50 to-orange-50/50 rounded-3xl border border-amber-200/80 shadow-md p-6 animate-fade-up">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b border-amber-200/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold shadow-md shadow-amber-200 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Ambil Siswa dari Kelas 10</h2>
                        <p class="text-xs text-amber-800/80 mt-0.5">
                            Pilih kelas 10 asal, lalu pilih siswa yang akan dinaikkan ke kelas <span class="font-bold text-amber-900">{{ $kela->nama }}</span>.
                        </p>
                    </div>
                </div>

                {{-- Dropdown Pilih Kelas 10 Asal --}}
                <form method="GET" action="{{ route('kesiswaan.tahun-ajaran.kelas.show', [$tahunAjaran, $kela]) }}" class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-700 whitespace-nowrap">Kelas Asal:</label>
                    <select name="source_kelas_id" onchange="this.form.submit()"
                            class="px-3 py-2 bg-white border border-amber-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-300 shadow-sm">
                        @foreach($sourceKelasOptions as $opt)
                            <option value="{{ $opt->id }}" {{ $selectedSourceKelas?->id === $opt->id ? 'selected' : '' }}>
                                {{ $opt->nama }} ({{ $opt->siswas_count }} siswa)
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if($availableSiswa->isNotEmpty())
                <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.kelas.assign', [$tahunAjaran, $kela]) }}" class="mt-4" id="form-penempatan">
                    @csrf

                    <div class="flex items-center justify-between gap-3 mb-3">
                        <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="check-all-pending" class="w-4 h-4 text-sky-600 rounded border-amber-300 focus:ring-sky-500">
                            <span class="text-xs font-bold text-slate-700">Pilih Semua Siswa di {{ $selectedSourceKelas->nama }}</span>
                        </label>
                        <span id="selected-counter" class="text-xs font-semibold text-amber-800">
                            0 siswa dipilih
                        </span>
                    </div>

                    <div class="max-h-72 overflow-y-auto rounded-2xl border border-amber-200 bg-white divide-y divide-amber-100">
                        @foreach($availableSiswa as $ps)
                            <label class="flex items-center justify-between p-3.5 hover:bg-amber-50/50 cursor-pointer transition select-none">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="siswa_ids[]" value="{{ $ps->id }}"
                                           class="pending-checkbox w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500">
                                    <div>
                                        <p class="text-xs font-bold text-slate-900 leading-snug">{{ $ps->nama }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">
                                            NIS: <span class="font-mono text-slate-600">{{ $ps->nis }}</span>
                                            &bull; Kelas: <span class="font-semibold text-amber-700">{{ $selectedSourceKelas->nama }}</span>
                                            &bull; {{ ucfirst($ps->jenis_kelamin ?? '-') }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                    Tersedia
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit" id="btn-submit-assign" disabled
                                class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-extrabold text-xs rounded-xl shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed hover:from-emerald-600 hover:to-teal-700">
                            + Naikkan Siswa Terpilih ke Kelas {{ $kela->nama }}
                        </button>
                    </div>
                </form>
            @else
                <div class="py-6 text-center text-xs text-amber-800">
                    Tidak ada siswa di kelas {{ $selectedSourceKelas?->nama ?? 'asal' }}.
                </div>
            @endif
        </div>
    @endif

    {{-- SECTION UNTUK KELAS 10: NAIKKAN SISWA KE KELAS 11 --}}
    @if($isAktif && $kela->tingkat === 'x' && $targetKelasOptions->isNotEmpty() && $siswas->total() > 0)
        <div class="bg-gradient-to-br from-sky-50 to-blue-50/50 rounded-3xl border border-sky-200/80 shadow-md p-6 animate-fade-up">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b border-sky-200/60">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-sky-500 text-white flex items-center justify-center font-bold shadow-md shadow-sky-200 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Naikkan Siswa ke Kelas 11</h2>
                        <p class="text-xs text-sky-800/80 mt-0.5">
                            Pilih siswa dari kelas <span class="font-bold text-sky-900">{{ $kela->nama }}</span> ini untuk dinaikkan ke kelas tingkat 11.
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.kelas.assign', [$tahunAjaran, $kela]) }}" class="mt-4" id="form-promosi-x">
                @csrf

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-slate-700 whitespace-nowrap">Pilih Kelas 11 Tujuan:</label>
                        <select name="target_kelas_id" required
                                class="px-3 py-2 bg-white border border-sky-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-300 shadow-sm">
                            <option value="">-- Pilih Kelas 11 --</option>
                            @foreach($targetKelasOptions as $opt)
                                <option value="{{ $opt->id }}">
                                    {{ $opt->nama }} ({{ $opt->siswas_count }} siswa saat ini)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="check-all-x" class="w-4 h-4 text-sky-600 rounded border-sky-300 focus:ring-sky-500">
                        <span class="text-xs font-bold text-slate-700">Pilih Semua Siswa</span>
                    </label>
                </div>

                <div class="max-h-72 overflow-y-auto rounded-2xl border border-sky-200 bg-white divide-y divide-sky-100">
                    @foreach($siswas as $s)
                        <label class="flex items-center justify-between p-3.5 hover:bg-sky-50/50 cursor-pointer transition select-none">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}"
                                       class="promosi-checkbox w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500">
                                <div>
                                    <p class="text-xs font-bold text-slate-900 leading-snug">{{ $s->nama }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        NIS: <span class="font-mono text-slate-600">{{ $s->nis }}</span>
                                        &bull; {{ ucfirst($s->jenis_kelamin ?? '-') }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full border border-sky-200">
                                Kelas 10
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" id="btn-submit-promosi" disabled
                            class="px-6 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 text-white font-extrabold text-xs rounded-xl shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed hover:from-sky-600 hover:to-blue-700">
                        + Naikkan Siswa Terpilih ke Kelas 11
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- SEARCH & FILTER FORM --}}
    <form method="GET" action="{{ route('kesiswaan.tahun-ajaran.kelas.show', [$tahunAjaran, $kela]) }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">

        <div class="flex flex-col md:flex-row gap-3 items-stretch">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIS siswa di kelas ini..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Cari
                </button>
                @if (request()->filled('q') || $sort !== 'nama' || $direction !== 'asc')
                    <a href="{{ route('kesiswaan.tahun-ajaran.kelas.show', [$tahunAjaran, $kela]) }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- TABEL SISWA KELAS --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Siswa
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nama') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nis') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                NIS
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nis') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">Jenis Kelamin</th>
                        <th class="py-3.5 px-5">No. Telp / WA</th>
                        <th class="py-3.5 px-5">Jabatan</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($siswas as $s)
                        <tr class="hover:bg-sky-50/30 transition">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0 overflow-hidden">
                                        @if ($s->foto_url)
                                            <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($s->nama, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 leading-snug truncate">{{ $s->nama }}</div>
                                        <div class="text-[11px] text-slate-400 truncate">{{ $s->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap font-mono font-semibold text-slate-700">{{ $s->nis }}</td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-semibold {{ $s->jenis_kelamin === 'perempuan' ? 'text-rose-600' : 'text-sky-700' }}">
                                    <span class="text-sm leading-none">{{ $s->jenis_kelamin === 'perempuan' ? '♀' : '♂' }}</span>
                                    {{ ucfirst($s->jenis_kelamin ?? '-') }}
                                </span>
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap text-slate-600">{{ $s->no_telp ?: '-' }}</td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if ($s->jabatan === 'ketua')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-[11px] bg-amber-50 text-amber-700 border border-amber-100">
                                        Ketua Ekskul
                                    </span>
                                @elseif($s->jabatan === 'anggota')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-[11px] bg-sky-50 text-sky-700 border border-sky-100">
                                        Anggota Ekskul
                                    </span>
                                @else
                                    <span class="text-slate-600">Siswa Reguler</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                @if ($s->user_id)
                                    <a href="{{ route('kesiswaan.users.edit', $s->user_id) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold rounded-xl transition text-xs"
                                       title="Edit Akun {{ $s->nama }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Akun
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum ada akun</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada siswa di kelas {{ $kela->nama }} ini.</p>
                                    @if($isAktif && $isKelas11 && $hasPending)
                                        <p class="text-[11px] text-amber-600 mt-1 font-medium">Gunakan form penempatan di atas untuk menambahkan siswa dari kelas 10.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-2 justify-between items-center">
            <p class="text-[11px] text-slate-400 font-semibold">
                Menampilkan {{ $siswas->firstItem() ?? 0 }}-{{ $siswas->lastItem() ?? 0 }} dari {{ $siswas->total() }} siswa
            </p>
            {{ $siswas->links() }}
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Form 1: Ambil Siswa dari Kelas 10 ke Kelas 11
        const checkAll = document.getElementById('check-all-pending');
        const checkboxes = document.querySelectorAll('.pending-checkbox');
        const counter = document.getElementById('selected-counter');
        const submitBtn = document.getElementById('btn-submit-assign');

        function updateState() {
            const checkedCount = document.querySelectorAll('.pending-checkbox:checked').length;
            if (counter) counter.textContent = `${checkedCount} siswa dipilih`;
            if (submitBtn) submitBtn.disabled = checkedCount === 0;
            if (checkAll && checkboxes.length > 0) {
                checkAll.checked = checkedCount === checkboxes.length;
                checkAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = checkAll.checked);
                updateState();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateState);
        });

        // Form 2: Naikkan Siswa dari Kelas 10 ke Kelas 11
        const checkAllX = document.getElementById('check-all-x');
        const checkboxesX = document.querySelectorAll('.promosi-checkbox');
        const submitBtnX = document.getElementById('btn-submit-promosi');

        function updateStateX() {
            const checkedCountX = document.querySelectorAll('.promosi-checkbox:checked').length;
            if (submitBtnX) submitBtnX.disabled = checkedCountX === 0;
            if (checkAllX && checkboxesX.length > 0) {
                checkAllX.checked = checkedCountX === checkboxesX.length;
                checkAllX.indeterminate = checkedCountX > 0 && checkedCountX < checkboxesX.length;
            }
        }

        if (checkAllX) {
            checkAllX.addEventListener('change', function () {
                checkboxesX.forEach(cb => cb.checked = checkAllX.checked);
                updateStateX();
            });
        }

        checkboxesX.forEach(cb => {
            cb.addEventListener('change', updateStateX);
        });
    });
</script>
@endsection
