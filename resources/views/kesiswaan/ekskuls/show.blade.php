@extends('layouts.kesiswaan')

@section('title', 'Detail Ekskul – ' . $ekskul->nama_ekskul)

@section('content')
@php
    $sort = $sort ?? 'status';
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

    $hasFilter = request()->filled(['q', 'status_anggota']) || request()->filled('sort');
    $statusColors = [
        'diterima'   => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
        'pending'    => 'bg-amber-50 text-amber-700 border border-amber-100',
        'ditolak'    => 'bg-rose-50 text-rose-600 border border-rose-100',
        'nonaktif'   => 'bg-slate-100 text-slate-500 border border-slate-200',
        'peringatan' => 'bg-orange-50 text-orange-600 border border-orange-100',
    ];
    $statusLabels = [
        'diterima'   => 'Aktif',
        'pending'    => 'Pending',
        'ditolak'    => 'Ditolak',
        'nonaktif'   => 'Nonaktif',
        'peringatan' => 'Peringatan',
    ];
@endphp

<div class="space-y-5 animate-fade-up">

    {{-- HERO CARD --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-4">
                {{-- Logo / Inisial --}}
                <div class="w-14 h-14 rounded-2xl overflow-hidden shrink-0 border-2 border-white/30 shadow-lg">
                    @if ($ekskul->logo)
                        <img src="{{ asset('storage/' . $ekskul->logo) }}" alt="{{ $ekskul->nama_ekskul }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-white/20 flex items-center justify-center font-black text-lg uppercase">
                            {{ strtoupper(substr($ekskul->nama_ekskul, 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        {{ $ekskul->nama_ekskul }}
                    </h1>
                    <p class="text-xs text-white/75 mt-1 leading-relaxed max-w-lg">
                        {{ $ekskul->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>
                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                        @if ($ekskul->kategori)
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/20 text-white">
                                {{ $ekskul->kategori }}
                            </span>
                        @endif
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $ekskul->status ? 'bg-white/20 text-white' : 'bg-rose-500/30 text-white' }}">
                            {{ $ekskul->status ? '✓ Aktif' : '✕ Nonaktif' }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $ekskul->is_open_recruitment ? 'bg-emerald-400/30 text-white' : 'bg-white/10 text-white/70' }}">
                            {{ $ekskul->is_open_recruitment ? '● Buka Pendaftaran' : '○ Tutup Pendaftaran' }}
                        </span>
                        @if ($ekskul->jadwal)
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/10 text-white/80">
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

    {{-- INFO CARDS: PEMBINA & PELATIH --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Pembina --}}
        <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pembina Ekskul</div>
                <div class="w-7 h-7 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            @if ($ekskul->pembina)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0">
                        @if ($ekskul->pembina->foto_url)
                            <img src="{{ $ekskul->pembina->foto_url }}" alt="{{ $ekskul->pembina->nama }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-xs uppercase">
                                {{ strtoupper(substr($ekskul->pembina->nama, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="font-bold text-sm text-slate-900">{{ $ekskul->pembina->nama }}</div>
                        <div class="text-[11px] text-slate-400">{{ $ekskul->pembina->nip ?? 'NIP belum diisi' }}</div>
                        <div class="text-[11px] text-slate-400">{{ $ekskul->pembina->email ?? '-' }}</div>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 text-slate-400 italic text-xs">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-300">?</div>
                    Belum ada pembina
                </div>
            @endif
        </div>

        {{-- Pelatih --}}
        <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pelatih Ekskul</div>
                <div class="w-7 h-7 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            @if ($ekskul->pelatih)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 font-bold flex items-center justify-center text-xs uppercase shrink-0">
                        {{ strtoupper(substr($ekskul->pelatih->nama, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm text-slate-900">{{ $ekskul->pelatih->nama }}</span>
                            @if($ekskul->pelatih->isTerverifikasi())
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Terverifikasi</span>
                            @elseif($ekskul->pelatih->isPending())
                                <a href="{{ route('kesiswaan.pelatih.index', ['status' => 'pending']) }}" class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 hover:underline">Verifikasi Sekarang &rarr;</a>
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-400">{{ ucfirst($ekskul->pelatih->jenis_kelamin ?? '-') }} &bull; {{ $ekskul->pelatih->no_hp ?? 'No. HP belum diisi' }}</div>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 text-slate-400 italic text-xs">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-300">?</div>
                    Belum ada pelatih
                </div>
            @endif
        </div>
    </div>

    {{-- SEARCH & FILTER ANGGOTA --}}
    <form method="GET" action="{{ route('kesiswaan.ekskuls.show', $ekskul) }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm">
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if(request('direction'))
            <input type="hidden" name="direction" value="{{ request('direction') }}">
        @endif
        <div class="flex flex-col lg:flex-row gap-3 items-stretch">
            <div class="text-xs font-extrabold text-slate-700 flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Daftar Anggota
            </div>
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIS anggota..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>
            <select name="status_anggota" class="w-full lg:w-48 px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
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
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- TABEL ANGGOTA --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Anggota
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
                        <th class="py-3.5 px-5">Kelas</th>
                        <th class="py-3.5 px-5">Jenis Kelamin</th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('tanggal_daftar') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Tanggal Daftar
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('tanggal_daftar') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('status') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Status
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('status') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($anggota as $daftar)
                        @php $s = $daftar->siswa; @endphp
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
                            <td class="py-3.5 px-5 whitespace-nowrap font-semibold text-slate-700">{{ $s->nis ?? '-' }}</td>
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] bg-sky-50 text-sky-700 border border-sky-100">
                                    {{ $s->kelas?->nama ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-semibold {{ $s->jenis_kelamin === 'perempuan' ? 'text-rose-600' : 'text-sky-700' }}">
                                    <span class="text-sm leading-none">{{ $s->jenis_kelamin === 'perempuan' ? '♀' : '♂' }}</span>
                                    {{ ucfirst($s->jenis_kelamin ?? '-') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap text-slate-500">
                                {{ $daftar->tanggal_daftar?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] {{ $statusColors[$daftar->status] ?? 'bg-slate-100 text-slate-500' }}">
                                    {{ $statusLabels[$daftar->status] ?? ucfirst($daftar->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                @if ($s->user_id)
                                    <a href="{{ route('kesiswaan.users.edit', $s->user_id) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold rounded-xl transition text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada anggota di ekskul ini.</p>
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

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-2 justify-between items-center">
            <p class="text-[11px] text-slate-400 font-semibold">
                Menampilkan {{ $anggota->firstItem() ?? 0 }}–{{ $anggota->lastItem() ?? 0 }} dari {{ $anggota->total() }} anggota
            </p>
            {{ $anggota->links() }}
        </div>
    </div>

</div>
@endsection
