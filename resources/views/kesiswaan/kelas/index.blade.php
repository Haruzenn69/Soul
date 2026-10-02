@extends('layouts.kesiswaan')

@section('title', 'Data Kelas')

@section('content')
@php
    $sort = $sort ?? 'nama';
    $direction = $direction ?? 'asc';

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

    $hasFilter = request()->filled(['q', 'tahun_ajaran_id']) || request()->filled('sort');
@endphp

<div class="space-y-5 animate-fade-up">

    {{-- HERO CARD BIRU (STYLE SAMA DENGAN DATA SISWA & AKUN PENGGUNA) --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        {{-- Ambient blur circles --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        {{-- Header + CTA --}}
        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                    Data Kelas
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Kelola data rombongan belajar per tingkat dan tahun ajaran. Pantau dan buka detail siswa di masing-masing kelas.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                @if ($tahunAjarans->isEmpty())
                    <span class="px-4 py-2.5 bg-amber-500/20 border border-amber-300/30 text-amber-100 rounded-2xl text-xs font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Belum ada tahun ajaran
                    </span>
                @else
                    <button type="button" onclick="document.getElementById('modal-create').showModal()"
                            class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Kelas
                    </button>
                @endif
            </div>
        </div>

        {{-- FILTER TABS PER TINGKATAN (DI DALAM HERO CARD) --}}
        <div class="relative mt-6 pt-5 border-t border-white/15">
            <div class="text-[11px] font-bold text-white/75 uppercase tracking-wider mb-2.5">
                Filter Tingkatan Kelas:
            </div>
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                @php
                    $tabAllUrl = route('kesiswaan.kelas.index', array_filter(['q' => request('q'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]));
                    $tabXUrl = route('kesiswaan.kelas.index', array_filter(['tingkat' => 'x', 'q' => request('q'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]));
                    $tabXIUrl = route('kesiswaan.kelas.index', array_filter(['tingkat' => 'xi', 'q' => request('q'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]));
                    $tabXIIUrl = route('kesiswaan.kelas.index', array_filter(['tingkat' => 'xii', 'q' => request('q'), 'tahun_ajaran_id' => request('tahun_ajaran_id')]));
                @endphp

                {{-- Tab: Semua Tingkat --}}
                <a href="{{ $tabAllUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap {{ empty($activeTingkat) ? 'bg-white text-sky-700 shadow-md shadow-sky-900/15' : 'bg-white/10 backdrop-blur border border-white/20 text-white hover:bg-white/20' }}">
                    <span>Semua Tingkat</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ empty($activeTingkat) ? 'bg-sky-100 text-sky-700 font-bold' : 'bg-white/15 text-white' }}">
                        {{ $counts['all'] }}
                    </span>
                </a>

                {{-- Tab: Kelas 10 (X) --}}
                <a href="{{ $tabXUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap {{ $activeTingkat === 'x' ? 'bg-white text-sky-700 shadow-md shadow-sky-900/15' : 'bg-white/10 backdrop-blur border border-white/20 text-white hover:bg-white/20' }}">
                    <span>Kelas 10 (X)</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $activeTingkat === 'x' ? 'bg-sky-100 text-sky-700 font-bold' : 'bg-white/15 text-white' }}">
                        {{ $counts['x'] }}
                    </span>
                </a>

                {{-- Tab: Kelas 11 (XI) --}}
                <a href="{{ $tabXIUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap {{ $activeTingkat === 'xi' ? 'bg-white text-sky-700 shadow-md shadow-sky-900/15' : 'bg-white/10 backdrop-blur border border-white/20 text-white hover:bg-white/20' }}">
                    <span>Kelas 11 (XI)</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $activeTingkat === 'xi' ? 'bg-sky-100 text-sky-700 font-bold' : 'bg-white/15 text-white' }}">
                        {{ $counts['xi'] }}
                    </span>
                </a>

                {{-- Tab: Kelas 12 (XII) --}}
                <a href="{{ $tabXIIUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap {{ $activeTingkat === 'xii' ? 'bg-white text-sky-700 shadow-md shadow-sky-900/15' : 'bg-white/10 backdrop-blur border border-white/20 text-white hover:bg-white/20' }}">
                    <span>Kelas 12 (XII)</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $activeTingkat === 'xii' ? 'bg-sky-100 text-sky-700 font-bold' : 'bg-white/15 text-white' }}">
                        {{ $counts['xii'] }}
                    </span>
                </a>
            </div>
        </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-2xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SEARCH & FILTER FORM --}}
    <form method="GET" action="{{ route('kesiswaan.kelas.index') }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm">
        @if ($activeTingkat)
            <input type="hidden" name="tingkat" value="{{ $activeTingkat }}">
        @endif
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if(request('direction'))
            <input type="hidden" name="direction" value="{{ request('direction') }}">
        @endif

        <div class="flex flex-col md:flex-row gap-3 items-stretch">
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kelas (mis. 10 PPLG 1, DKV, dll)..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            {{-- Filter Tahun Ajaran --}}
            <div class="w-full md:w-56">
                <select name="tahun_ajaran_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Submit & Reset --}}
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Filter
                </button>
                @if ($hasFilter || $activeTingkat)
                    <a href="{{ route('kesiswaan.kelas.index') }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition"
                       title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- TABEL KELAS (STYLE KONSISTEN DENGAN DATA SISWA) --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Nama Kelas
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nama') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('tingkat') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Tingkat
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('tingkat') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">Jurusan / Rombel</th>
                        <th class="py-3.5 px-5">Tahun Ajaran</th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('siswas_count') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Jumlah Siswa
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('siswas_count') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($kelas as $k)
                        <tr class="hover:bg-sky-50/30 transition">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 font-black flex items-center justify-center text-xs shrink-0">
                                        {{ config("kelas.tingkat.{$k->tingkat}", $k->tingkat) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('kesiswaan.kelas.show', $k) }}" class="font-extrabold text-slate-900 hover:text-sky-600 transition block text-sm">
                                            {{ $k->nama }}
                                        </a>
                                        <div class="text-[11px] text-slate-400">Rombel ke-{{ $k->rombel }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full font-bold text-xs bg-sky-50 text-sky-700 border border-sky-100 uppercase">
                                    Tingkat {{ config("kelas.tingkat.{$k->tingkat}", $k->tingkat) }}
                                </span>
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <div class="font-semibold text-slate-700">{{ $k->jurusan_label ?? strtoupper($k->jurusan) }}</div>
                                <div class="text-[11px] text-slate-400">Rombel {{ $k->rombel }}</div>
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <div class="font-semibold text-slate-700">{{ $k->tahunAjaran?->nama ?? '-' }}</div>
                                @if ($k->tahunAjaran?->is_active)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full font-bold text-[10px] bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        Aktif
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <a href="{{ route('kesiswaan.kelas.show', $k) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 hover:bg-sky-100 text-slate-700 hover:text-sky-700 transition"
                                   title="Lihat siswa di kelas ini">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>{{ $k->siswas->count() }} Siswa</span>
                                </a>
                            </td>

                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="flex gap-2 justify-end items-center">
                                    {{-- Tombol Lihat Siswa --}}
                                    <a href="{{ route('kesiswaan.kelas.show', $k) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm shadow-sky-200 transition"
                                       title="Lihat Siswa Kelas {{ $k->nama }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat Siswa
                                    </a>

                                    {{-- Tombol Edit --}}
                                    <button type="button"
                                            onclick='openEdit({{ json_encode([
                                                "id" => $k->id,
                                                "nama" => $k->nama,
                                                "tingkat" => $k->tingkat,
                                                "jurusan" => $k->jurusan,
                                                "rombel" => $k->rombel,
                                                "tahun_ajaran_id" => $k->tahun_ajaran_id,
                                            ]) }})'
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('kesiswaan.kelas.destroy', $k) }}" method="POST"
                                          onsubmit="return confirm('Hapus kelas {{ $k->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold rounded-xl text-xs transition">
                                            <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Tidak ada kelas yang ditemukan.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah filter tingkatan atau kata kunci pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-2 justify-between items-center">
            <p class="text-[11px] text-slate-400 font-semibold">
                Menampilkan {{ $kelas->firstItem() ?? 0 }}-{{ $kelas->lastItem() ?? 0 }} dari {{ $kelas->total() }} kelas
            </p>
            {{ $kelas->links() }}
        </div>
    </div>

    {{-- MODAL CREATE --}}
    <dialog id="modal-create" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-md shadow-2xl border border-sky-100">
        <form method="POST" action="{{ route('kesiswaan.kelas.store') }}" class="p-6 md:p-8 space-y-4 bg-white">
            @csrf
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Tambah Kelas Baru</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Konfigurasi rombel kelas sekolah</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Tingkat <span class="text-rose-500">*</span></label>
                <select name="tingkat" required data-tingkat
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="" disabled selected>Pilih tingkat...</option>
                    @foreach (config('kelas.tingkat') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Jurusan <span class="text-rose-500">*</span></label>
                <select name="jurusan" required data-jurusan disabled
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition disabled:opacity-60">
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Nomor Rombel <span class="text-rose-500">*</span></label>
                <input type="number" name="rombel" min="1" required data-rombel
                       placeholder="Contoh: 1, 2, 3..."
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Tahun Ajaran <span class="text-rose-500">*</span></label>
                <select name="tahun_ajaran_id" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="" disabled selected>Pilih tahun ajaran...</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ $ta->is_active ? 'selected' : '' }}>
                            {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="px-4 py-3 bg-sky-50 border border-sky-100 rounded-2xl">
                <span class="block text-[10px] font-bold text-sky-600 uppercase tracking-wide mb-0.5">Nama Kelas Terbentuk</span>
                <p data-preview class="text-sm font-extrabold text-sky-900">Pilih tingkat, jurusan, dan rombel</p>
            </div>

            <div class="flex gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="this.closest('dialog').close()"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                    Simpan Kelas
                </button>
            </div>
        </form>
    </dialog>

    {{-- MODAL EDIT --}}
    <dialog id="modal-edit" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-md shadow-2xl border border-sky-100">
        <form id="form-edit" method="POST" class="p-6 md:p-8 space-y-4 bg-white">
            @csrf
            @method('PUT')
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Edit Kelas</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Perbarui data rombel kelas</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Tingkat <span class="text-rose-500">*</span></label>
                <select name="tingkat" required data-tingkat
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    @foreach (config('kelas.tingkat') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Jurusan <span class="text-rose-500">*</span></label>
                <select name="jurusan" required data-jurusan
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Nomor Rombel <span class="text-rose-500">*</span></label>
                <input type="number" name="rombel" min="1" required data-rombel
                       placeholder="misal: 1, 2, 3..."
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Tahun Ajaran <span class="text-rose-500">*</span></label>
                <select name="tahun_ajaran_id" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="px-4 py-3 bg-sky-50 border border-sky-100 rounded-2xl">
                <span class="block text-[10px] font-bold text-sky-600 uppercase tracking-wide mb-0.5">Nama Kelas Terbentuk</span>
                <p data-preview class="text-sm font-extrabold text-sky-900">Pilih tingkat, jurusan, dan rombel</p>
            </div>

            <div class="flex gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="this.closest('dialog').close()"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </dialog>

</div>

<script>
    const tingkatLabels = @json(config('kelas.tingkat'));

    const jurusanMap = (function () {
        const map = {};
        const jurusan = @json(config('kelas.jurusan'));
        Object.entries(jurusan).forEach(([kode, labels]) => {
            Object.keys(tingkatLabels).forEach((tingkat) => {
                map[tingkat] = map[tingkat] || [];
                map[tingkat].push({ value: kode, label: labels[tingkat] || kode });
            });
        });
        return map;
    })();

    function fillJurusan(select, tingkat, selectedValue) {
        select.innerHTML = '';
        if (!tingkat) {
            select.appendChild(placeholderOption('Pilih tingkat dulu...'));
            select.disabled = true;
            return;
        }

        select.disabled = false;
        (jurusanMap[tingkat] || []).forEach((j) => {
            const opt = document.createElement('option');
            opt.value = j.value;
            opt.textContent = j.label;
            select.appendChild(opt);
        });

        if (selectedValue && [...select.options].some((o) => o.value === selectedValue)) {
            select.value = selectedValue;
        } else if (!selectedValue) {
            select.appendChild(placeholderOption('Pilih jurusan...', true));
            select.selectedIndex = 0;
        }

        if (select.value && select.selectedIndex === -1) {
            select.selectedIndex = 0;
        }
    }

    function placeholderOption(text, isPlaceholder) {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = text;
        if (isPlaceholder) {
            opt.disabled = true;
            opt.selected = true;
        }
        return opt;
    }

    function updatePreview(form) {
        const tingkat = form.querySelector('[name=tingkat]').value;
        const jurusan = form.querySelector('[name=jurusan]').value;
        const rombel = form.querySelector('[name=rombel]').value;
        const tLabel = tingkatLabels[tingkat] || '';
        const jLabel = (jurusanMap[tingkat] || []).find((j) => j.value === jurusan)?.label || '';
        const preview = form.querySelector('[data-preview]');
        preview.textContent = [tLabel, jLabel, rombel].filter(Boolean).join(' ') || 'Pilih tingkat, jurusan, dan rombel';
    }

    function bindFormEvents(form) {
        const tingkat = form.querySelector('[name=tingkat]');
        const jurusan = form.querySelector('[name=jurusan]');
        const rombel = form.querySelector('[name=rombel]');

        tingkat.addEventListener('change', () => {
            fillJurusan(jurusan, tingkat.value, '');
            updatePreview(form);
        });
        jurusan.addEventListener('change', () => updatePreview(form));
        rombel.addEventListener('input', () => updatePreview(form));

        updatePreview(form);
    }

    bindFormEvents(document.querySelector('#modal-create form'));
    bindFormEvents(document.querySelector('#modal-edit form'));

    function openEdit(data) {
        const form = document.getElementById('form-edit');
        form.action = '{{ url('kesiswaan/kelas') }}/' + data.id;
        form.querySelector('[name=tingkat]').value = data.tingkat || '';
        fillJurusan(form.querySelector('[name=jurusan]'), data.tingkat || '', data.jurusan || '');
        form.querySelector('[name=rombel]').value = data.rombel || '';
        form.querySelector('[name=tahun_ajaran_id]').value = data.tahun_ajaran_id || '';
        updatePreview(form);
        document.getElementById('modal-edit').showModal();
    }

    (function restoreCreateForm() {
        const form = document.querySelector('#modal-create form');
        const oldTingkat = @json(old('tingkat'));
        if (!oldTingkat) return;
        form.querySelector('[name=tingkat]').value = oldTingkat;
        fillJurusan(form.querySelector('[name=jurusan]'), oldTingkat, @json(old('jurusan')));
        if (@json(old('rombel'))) form.querySelector('[name=rombel]').value = @json(old('rombel'));
        if (@json(old('tahun_ajaran_id'))) form.querySelector('[name=tahun_ajaran_id]').value = @json(old('tahun_ajaran_id'));
        updatePreview(form);
        document.getElementById('modal-create').showModal();
    })();
</script>
@endsection
