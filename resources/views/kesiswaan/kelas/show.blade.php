@extends('layouts.kesiswaan')

@section('title', 'Siswa Kelas ' . $kelas->nama)

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

    $hasFilter = request()->filled(['q', 'jenis_kelamin', 'jabatan']);
@endphp

<div class="space-y-5 animate-fade-up">

    {{-- HERO CARD BIRU --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        {{-- Ambient blur circles --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('kesiswaan.kelas.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs text-white/80 hover:text-white font-bold transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        Data Kelas
                    </a>
                    <span class="text-white/40">/</span>
                    <span class="text-xs text-white font-bold">{{ $kelas->nama }}</span>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        Siswa {{ $kelas->nama }}
                    </h1>
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-white/20 backdrop-blur border border-white/20 text-white">
                        Tingkat {{ config('kelas.tingkat.' . $kelas->tingkat, $kelas->tingkat) }}
                    </span>
                    @if ($kelas->tahunAjaran?->is_active)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-400/30 text-emerald-100 border border-emerald-300/30">
                            TA Aktif
                        </span>
                    @endif
                </div>

                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Tahun Ajaran: <span class="font-bold text-white">{{ $kelas->tahunAjaran?->nama ?? '-' }}</span>
                    &bull; Jurusan: <span class="font-bold text-white">{{ $kelas->jurusan_label ?? strtoupper($kelas->jurusan) }}</span>
                    &bull; Rombel: <span class="font-bold text-white">{{ $kelas->rombel }}</span>
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.kelas.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-2xl hover:bg-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Daftar Kelas
                </a>
                <a href="{{ route('kesiswaan.users.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Siswa
                </a>
            </div>
        </div>

        {{-- QUICK STATS PILLS DI DALAM CARD BIRU --}}
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
                    <span class="text-[10px] font-bold text-white/75 uppercase tracking-wider block">Ketua Ekskul</span>
                    <span class="text-xl font-black text-white mt-0.5 block">{{ $stats['ketua'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER FORM --}}
    <form method="GET" action="{{ route('kesiswaan.kelas.show', $kelas) }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">

        <div class="flex flex-col md:flex-row gap-3 items-stretch">
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIS, atau email siswa di kelas ini..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            {{-- Filter Jenis Kelamin --}}
            <div class="w-full md:w-44">
                <select name="jenis_kelamin" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="">Semua Gender</option>
                    <option value="laki-laki" {{ request('jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki (♂)</option>
                    <option value="perempuan" {{ request('jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan (♀)</option>
                </select>
            </div>

            {{-- Filter Jabatan --}}
            <div class="w-full md:w-44">
                <select name="jabatan" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="">Semua Jabatan</option>
                    <option value="siswa" {{ request('jabatan') === 'siswa' ? 'selected' : '' }}>Siswa Reguler</option>
                    <option value="ketua" {{ request('jabatan') === 'ketua' ? 'selected' : '' }}>Ketua Ekskul</option>
                </select>
            </div>

            {{-- Submit & Reset --}}
            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Filter
                </button>
                @if ($hasFilter || $sort !== 'nama' || $direction !== 'asc')
                    <a href="{{ route('kesiswaan.kelas.show', $kelas) }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition"
                       title="Reset filter">
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
                                    <p class="text-xs font-semibold text-slate-600">Belum ada siswa di kelas {{ $kelas->nama }} ini.</p>
                                    @if ($hasFilter)
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah kata kunci atau hapus filter yang diterapkan.</p>
                                        <a href="{{ route('kesiswaan.kelas.show', $kelas) }}" class="mt-3 text-xs font-bold text-sky-600 hover:text-sky-700">
                                            Tampilkan semua siswa kelas ini
                                        </a>
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
@endsection
