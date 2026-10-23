@extends('layouts.kesiswaan')

@section('title', 'Kelola Akun Pengguna')

@section('content')
@php
    $sort = $sort ?? 'created_at';
    $direction = $direction ?? 'desc';

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
@endphp
<div class="space-y-5 animate-fade-up">

    {{-- HERO CARD BIRU (STYLE SAMA DENGAN DASHBOARD KESISWAAN) --}}
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
                    Akun Pengguna
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Kelola data pengguna, perbarui akun siswa, guru/pembina, dan staf sekolah melalui panel kesiswaan.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.users.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Akun
                </a>
            </div>
        </div>

        {{-- FILTER TABS DI DALAM CARD BIRU --}}
        <div class="relative mt-6 pt-5 border-t border-white/15">
            <div class="text-[11px] font-bold text-white/75 uppercase tracking-wider mb-2.5">
                Filter Tampilan Akun:
            </div>
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                @php
                    $activeRole = request('role');
                    $currentQ = request('q');
                    
                    $tabAllUrl = route('kesiswaan.users.index', array_filter(['q' => $currentQ]));
                    $tabSiswaUrl = route('kesiswaan.users.index', array_filter(['role' => 'siswa', 'q' => $currentQ]));
                    $tabGuruUrl = route('kesiswaan.users.index', array_filter(['role' => 'pembina', 'q' => $currentQ]));
                    $tabStaffUrl = route('kesiswaan.users.index', array_filter(['role' => 'staff', 'q' => $currentQ]));
                @endphp

                {{-- Tab: Semua --}}
                <a href="{{ $tabAllUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap {{ empty($activeRole) ? 'bg-white text-sky-700 shadow-md shadow-sky-900/15' : 'bg-white/10 backdrop-blur border border-white/20 text-white hover:bg-white/20' }}">
                    <span>Semua Akun</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ empty($activeRole) ? 'bg-sky-100 text-sky-700 font-bold' : 'bg-white/15 text-white' }}">
                        {{ $counts['all'] }}
                    </span>
                </a>

                {{-- Tab: Siswa (Hanya Siswa) --}}
                <a href="{{ $tabSiswaUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap {{ $activeRole === 'siswa' ? 'bg-white text-sky-700 shadow-md shadow-sky-900/15' : 'bg-white/10 backdrop-blur border border-white/20 text-white hover:bg-white/20' }}">
                    <span>Siswa</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $activeRole === 'siswa' ? 'bg-sky-100 text-sky-700 font-bold' : 'bg-white/15 text-white' }}">
                        {{ $counts['siswa'] }}
                    </span>
                </a>

                {{-- Tab: Guru / Pembina (Hanya Guru) --}}
                <a href="{{ $tabGuruUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap {{ in_array($activeRole, ['pembina', 'guru']) ? 'bg-white text-sky-700 shadow-md shadow-sky-900/15' : 'bg-white/10 backdrop-blur border border-white/20 text-white hover:bg-white/20' }}">
                    <span>Guru / Pembina</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ in_array($activeRole, ['pembina', 'guru']) ? 'bg-sky-100 text-sky-700 font-bold' : 'bg-white/15 text-white' }}">
                        {{ $counts['guru'] }}
                    </span>
                </a>

                {{-- Tab: Staf & Admin --}}
                <a href="{{ $tabStaffUrl }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap {{ in_array($activeRole, ['staff', 'kesiswaan', 'admin']) ? 'bg-white text-sky-700 shadow-md shadow-sky-900/15' : 'bg-white/10 backdrop-blur border border-white/20 text-white hover:bg-white/20' }}">
                    <span>Staf & Admin</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ in_array($activeRole, ['staff', 'kesiswaan', 'admin']) ? 'bg-sky-100 text-sky-700 font-bold' : 'bg-white/15 text-white' }}">
                        {{ $counts['staff'] }}
                    </span>
                </a>
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER FORM (POPOVER DESIGN) --}}
    @php
        $activeFilterCount = (request()->filled('role') ? 1 : 0);
        $hasActiveFilter = request()->filled('q') || $activeFilterCount > 0;
    @endphp

    <div x-data="{ openFilter: false }" class="space-y-3">
        <form method="GET" action="{{ route('kesiswaan.users.index') }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm flex flex-col md:flex-row gap-3 items-stretch md:items-center">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif

            {{-- Input Pencarian --}}
            <div class="soul-search-wrap relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="search" name="q" data-soul-search value="{{ request('q') }}" placeholder="Cari nama, NIS, NIP, username, atau email..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            {{-- Tombol Filter & Popover Panel --}}
            <div class="relative flex items-center gap-2">
                <button type="button" @click="openFilter = !openFilter"
                        class="px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 border {{ $activeFilterCount > 0 ? 'bg-sky-50 border-sky-300 text-sky-700 shadow-sm shadow-sky-100' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                    <svg class="w-4 h-4 {{ $activeFilterCount > 0 ? 'text-sky-600' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filter Data</span>
                    @if($activeFilterCount > 0)
                        <span class="w-5 h-5 rounded-full bg-sky-500 text-white text-[10px] font-black flex items-center justify-center">
                            {{ $activeFilterCount }}
                        </span>
                    @endif
                </button>

                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Cari
                </button>

                @if($hasActiveFilter)
                    <a href="{{ route('kesiswaan.users.index') }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition"
                       title="Reset filter">
                        Reset
                    </a>
                @endif

                {{-- DROPDOWN PANEL POPOVER --}}
                <div x-show="openFilter"
                     @click.away="openFilter = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="absolute right-0 top-full mt-2 w-80 md:w-96 bg-white rounded-3xl p-5 border border-sky-100 shadow-xl shadow-sky-900/10 z-50 space-y-4"
                     style="display: none;">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-sky-50 text-sky-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            </span>
                            <h4 class="text-xs font-bold text-slate-800">Opsi Filter Pengguna</h4>
                        </div>
                        <button type="button" @click="openFilter = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">
                            &times;
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Role Pengguna</label>
                            <select name="role" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Semua Role</option>
                                <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                                <option value="pembina" {{ in_array(request('role'), ['pembina', 'guru']) ? 'selected' : '' }}>Guru / Pembina</option>
                                <option value="kesiswaan" {{ request('role') === 'kesiswaan' ? 'selected' : '' }}>Kesiswaan</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('kesiswaan.users.index', array_filter(['q' => request('q')])) }}"
                           class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition">
                            Reset Opsi
                        </a>
                        <button type="submit" @click="openFilter = false"
                                class="px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- AKTIF FILTER CHIPS / TAGS --}}
        @if($hasActiveFilter)
            <div class="flex flex-wrap items-center gap-2 pt-1 px-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Filter Aktif:</span>

                @if(request()->filled('q'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-xs font-semibold">
                        <span>Pencarian: "<strong>{{ request('q') }}</strong>"</span>
                        <a href="{{ route('kesiswaan.users.index', request()->except('q', 'page')) }}" class="hover:text-rose-500 transition">&times;</a>
                    </span>
                @endif

                @if(request()->filled('role'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-xs font-semibold">
                        <span>Role: <strong>{{ ucfirst(request('role')) }}</strong></span>
                        <a href="{{ route('kesiswaan.users.index', request()->except('role', 'page')) }}" class="hover:text-rose-500 transition">&times;</a>
                    </span>
                @endif

                <a href="{{ route('kesiswaan.users.index') }}" class="text-xs font-bold text-rose-500 hover:text-rose-600 hover:underline transition ml-1">
                    Hapus Semua Filter
                </a>
            </div>
        @endif
    </div>

    {{-- TABEL PENGGUNA --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('username') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Pengguna
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('username') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('role') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Role
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortIcon('role') }}"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($users as $user)
                        <tr class="hover:bg-sky-50/30 transition">
                            {{-- Pengguna / Nama --}}
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0 overflow-hidden">
                                        @php
                                            $userFoto = $user->siswa?->foto_url ?? ($user->pembina?->foto ? asset('storage/'.$user->pembina->foto) : null);
                                        @endphp
                                        @if($userFoto)
                                            <img src="{{ $userFoto }}" alt="Avatar" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($user->siswa?->nama ?? $user->pembina?->nama ?? $user->username ?? 'U', 0, 2)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 leading-snug">
                                            {{ $user->siswa?->nama ?? $user->pembina?->nama ?? ($user->username ?? 'User') }}
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            @if($user->username)
                                                <span>{{ '@' . $user->username }}</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-amber-700 bg-amber-50 border border-amber-200/60 px-1.5 py-0.5 rounded-lg text-[10px] font-semibold">
                                                    Belum set username (Login: {{ $user->siswa?->nis ?? $user->pembina?->nip ?? $user->email }})
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Role (Bukan berbentuk pil, teks bersih dan rapi) --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <div class="text-xs font-semibold text-slate-700">
                                    @if ($user->role === 'siswa')
                                        <span>Siswa</span>
                                        @if ($user->siswa?->jabatan === 'ketua')
                                            <span class="text-amber-600 font-bold ml-1">(Ketua)</span>
                                        @endif
                                    @elseif ($user->role === 'pembina')
                                        <span>Guru / Pembina</span>
                                    @elseif ($user->role === 'kesiswaan')
                                        <span>Kesiswaan</span>
                                    @elseif ($user->role === 'admin')
                                        <span>Admin</span>
                                    @else
                                        <span>{{ ucfirst($user->role) }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Aksi (Hanya Edit Akun) --}}
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex justify-end items-center">
                                    @if ($user->role === 'admin' && auth()->user()->role !== 'admin')
                                        <span class="text-[11px] text-slate-400 italic flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/>
                                            </svg>
                                            Dikelola Admin
                                        </span>
                                    @else
                                        <a href="{{ route('kesiswaan.users.show', $user) }}"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-xl transition text-xs mr-1"
                                           title="Lihat Detail Akun">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </a>
                                        <a href="{{ route('kesiswaan.users.edit', $user) }}"
                                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold rounded-xl transition text-xs"
                                           title="Edit Akun">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit Akun
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Tidak ada akun yang sesuai kriteria.</p>
                                    @if(request()->filled('q') || request()->filled('role'))
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah kata kunci atau hapus filter yang diterapkan.</p>
                                        <a href="{{ route('kesiswaan.users.index') }}" class="mt-3 text-xs font-bold text-sky-600 hover:text-sky-700">
                                            Tampilkan semua akun
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
