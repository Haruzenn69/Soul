@extends('layouts.kesiswaan')

@section('title', 'Data Pembina')

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

    $hasFilter = request()->filled(['q', 'jenis_kelamin']);
@endphp

<div class="space-y-5 animate-fade-up">

    {{-- HERO CARD BIRU --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                    Data Pembina
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Daftar seluruh pembina ekskul. Kelola penugasan ekskul yang dibina oleh masing-masing pembina di sini.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.users.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Pembina
                </a>
            </div>
        </div>
    </div>

    {{-- Notifikasi success/error ditampilkan oleh layouts.kesiswaan --}}
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
    <form method="GET" action="{{ route('kesiswaan.pembina.index') }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">

        <div class="flex flex-col lg:flex-row gap-3 items-stretch">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIP, email, atau no. telp..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <select name="jenis_kelamin" class="w-full lg:w-44 px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                <option value="">Semua Jenis Kelamin</option>
                <option value="laki-laki" {{ request('jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                <option value="perempuan" {{ request('jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
            </select>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Filter
                </button>
                @if ($hasFilter || $sort !== 'nama' || $direction !== 'asc')
                    <a href="{{ route('kesiswaan.pembina.index') }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- TABEL PEMBINA --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Pembina
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nama') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nip') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                NIP
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('nip') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('jenis_kelamin') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Jenis Kelamin
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('jenis_kelamin') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">No. Telp</th>
                        <th class="py-3.5 px-5">Ekskul Dibina</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($pembinas as $p)
                        <tr class="hover:bg-sky-50/30 transition">
                            {{-- Pembina / Avatar --}}
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0 overflow-hidden">
                                        @if ($p->foto_url)
                                            <img src="{{ $p->foto_url }}" alt="{{ $p->nama }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($p->nama ?? '?', 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 leading-snug truncate">{{ $p->nama ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-400 truncate">{{ $p->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- NIP --}}
                            <td class="py-3.5 px-5 whitespace-nowrap font-semibold text-slate-700">
                                {!! $p->nip ? e($p->nip) : '<span class="text-slate-400 italic font-normal">-</span>' !!}
                            </td>

                            {{-- Jenis Kelamin --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-semibold {{ $p->jenis_kelamin === 'perempuan' ? 'text-rose-600' : 'text-sky-700' }}">
                                    <span class="text-sm leading-none">{{ $p->jenis_kelamin === 'perempuan' ? '♀' : '♂' }}</span>
                                    {{ ucfirst($p->jenis_kelamin ?? '-') }}
                                </span>
                            </td>

                            {{-- No Telp --}}
                            <td class="py-3.5 px-5 whitespace-nowrap text-slate-600">
                                {{ $p->no_telp ?? '-' }}
                            </td>

                            {{-- Ekskul Dibina --}}
                            <td class="py-3.5 px-5">
                                <div class="flex flex-wrap gap-1 items-center">
                                    <button type="button" onclick="document.getElementById('modal-kelola-{{ $p->id }}').showModal()"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                                        Kelola Ekskul
                                    </button>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <a href="{{ route('kesiswaan.pembina.riwayat-profil', $p) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-semibold rounded-xl transition text-xs mr-1">
                                    Riwayat Profil
                                </a>
                                @if ($p->user_id)
                                    <a href="{{ route('kesiswaan.users.edit', $p->user_id) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold rounded-xl transition text-xs">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Tidak ada pembina yang sesuai kriteria.</p>
                                    @if ($hasFilter)
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah kata kunci atau hapus filter.</p>
                                        <a href="{{ route('kesiswaan.pembina.index') }}" class="mt-3 text-xs font-bold text-sky-600 hover:text-sky-700">
                                            Tampilkan semua pembina
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
                Menampilkan {{ $pembinas->firstItem() ?? 0 }}-{{ $pembinas->lastItem() ?? 0 }} dari {{ $pembinas->total() }} pembina
            </p>
            {{ $pembinas->links() }}
        </div>
    </div>

</div>

@foreach ($pembinas as $p)
    <dialog id="modal-kelola-{{ $p->id }}" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-lg shadow-2xl border border-sky-100">
        <div class="p-6 space-y-4 bg-white">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Kelola Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $p->nama }} · {{ $p->ekskuls->count() }}/4 ekskul</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center">×</button>
            </div>

            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Ekskul yang dibina</h3>
                @forelse ($p->ekskuls as $ekskul)
                    <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div>
                            <p class="text-xs font-bold text-slate-800">{{ $ekskul->nama_ekskul }}</p>
                            <p class="text-[10px] mt-0.5 text-slate-400">Ditugaskan ke pembina ini</p>
                        </div>
                        <form action="{{ route('kesiswaan.pembina.remove-ekskul', [$p, $ekskul]) }}" method="POST" onsubmit="return confirm('Lepas penugasan {{ $ekskul->nama_ekskul }} dari {{ $p->nama }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-xl text-[11px] font-bold bg-rose-50 text-rose-600 hover:bg-rose-100">
                                Tidak Membina Lagi
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-2">Belum ada ekskul yang dibina.</p>
                @endforelse
            </div>

            @if ($p->ekskuls->count() < 4)
                <form action="{{ route('kesiswaan.pembina.assign-ekskul', $p) }}" method="POST" class="pt-3 border-t border-slate-100 space-y-3">
                    @csrf
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tambah ekskul</label>
                    <select name="ekskul_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800">
                        <option value="" disabled selected>Pilih ekskul...</option>
                        @foreach ($ekskulList->whereNull('pembina_id') as $ekskul)
                            <option value="{{ $ekskul->id }}">{{ $ekskul->nama_ekskul }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl">Tambah Ekskul</button>
                </form>
            @else
                <p class="text-[11px] text-amber-700 bg-amber-50 border border-amber-100 rounded-xl p-3">Batas maksimal 4 ekskul per pembina tercapai.</p>
            @endif
        </div>
    </dialog>
@endforeach
@endsection
