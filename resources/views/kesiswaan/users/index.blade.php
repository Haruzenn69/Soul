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

    {{-- SEARCH & FILTER FORM --}}
    <form method="GET" action="{{ route('kesiswaan.users.index') }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm flex flex-col md:flex-row gap-3 items-stretch md:items-center">
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if(request('direction'))
            <input type="hidden" name="direction" value="{{ request('direction') }}">
        @endif

        <div class="relative flex-1">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIS, NIP, username, atau email..."
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
        </div>

        <div class="flex items-center gap-2">
            <select name="role" class="px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                <option value="">Semua Role</option>
                <option value="siswa" {{ request('role') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                <option value="pembina" {{ in_array(request('role'), ['pembina', 'guru']) ? 'selected' : '' }}>Guru / Pembina</option>
                <option value="kesiswaan" {{ request('role') === 'kesiswaan' ? 'selected' : '' }}>Kesiswaan</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>

            <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                Filter
            </button>

            @if(request()->filled('q') || request()->filled('role') || request()->filled('sort'))
                <a href="{{ route('kesiswaan.users.index') }}"
                   class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition"
                   title="Reset filter">
                    Reset
                </a>
            @endif
        </div>
    </form>

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
