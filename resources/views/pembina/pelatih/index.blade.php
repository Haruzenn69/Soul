@extends('pembina.layout')

@section('title', 'Kelola Pelatih')

@section('content')
<div class="space-y-6 animate-fade-up">

    {{-- HERO CARD --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-500 to-indigo-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur border border-white/20 text-[11px] font-bold uppercase tracking-wider text-white mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Manajemen Kepelatihan Ekskul
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                    Kelola Pelatih Ekskul
                </h1>
                <p class="text-xs text-white/80 mt-1 max-w-xl leading-relaxed">
                    Daftarkan pelatih untuk ekskul binaanmu, lengkapi berkas CV dan sertifikat PDF wajib. Seluruh pendaftaran akan diverifikasi oleh Kesiswaan sebelum otomatis tampil di katalog ekskul.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('pembina.pelatih.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-extrabold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Daftarkan Pelatih Baru
                </a>
            </div>
        </div>
    </div>



    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total --}}
        <div class="bg-white rounded-2xl p-4 border border-sky-100 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pelatih</p>
                <p class="text-xl font-black text-slate-800">{{ $stats['total'] }}</p>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-2xl p-4 border border-amber-100 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">Menunggu Verifikasi</p>
                <p class="text-xl font-black text-slate-800">{{ $stats['pending'] }}</p>
            </div>
        </div>

        {{-- Terverifikasi --}}
        <div class="bg-white rounded-2xl p-4 border border-emerald-100 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Terverifikasi (Aktif)</p>
                <p class="text-xl font-black text-slate-800">{{ $stats['terverifikasi'] }}</p>
            </div>
        </div>

        {{-- Ditolak --}}
        <div class="bg-white rounded-2xl p-4 border border-rose-100 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider">Ditolak</p>
                <p class="text-xl font-black text-slate-800">{{ $stats['ditolak'] }}</p>
            </div>
        </div>
    </div>

    {{-- FILTER & SEARCH BAR --}}
    @php
        $sort = $sort ?? 'created_at';
        $direction = $direction ?? 'desc';
        $currentSortCombo = $sort . '_' . $direction;
        $hasActiveFilter = request()->anyFilled(['q', 'status_verifikasi', 'ekskul_id']) || request()->filled('sort');
    @endphp
    <div class="bg-white rounded-2xl p-4 border border-sky-100 shadow-sm">
        <form method="GET" action="{{ route('pembina.pelatih.index') }}" id="filter-pelatih-form" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
            <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">
            <input type="hidden" name="direction" id="direction-input" value="{{ $direction }}">
            @if(request('status_verifikasi'))
                <input type="hidden" name="status_verifikasi" value="{{ request('status_verifikasi') }}">
            @endif

            <div class="flex flex-wrap items-center gap-2">
                {{-- Filter Status Verifikasi --}}
                <a href="{{ route('pembina.pelatih.index', array_merge(request()->except('status_verifikasi', 'page'), [])) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ !request()->filled('status_verifikasi') ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Status
                </a>
                <a href="{{ route('pembina.pelatih.index', array_merge(request()->except('status_verifikasi', 'page'), ['status_verifikasi' => 'pending'])) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('status_verifikasi') === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                    Menunggu Verifikasi ({{ $stats['pending'] }})
                </a>
                <a href="{{ route('pembina.pelatih.index', array_merge(request()->except('status_verifikasi', 'page'), ['status_verifikasi' => 'terverifikasi'])) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('status_verifikasi') === 'terverifikasi' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    Terverifikasi ({{ $stats['terverifikasi'] }})
                </a>
                <a href="{{ route('pembina.pelatih.index', array_merge(request()->except('status_verifikasi', 'page'), ['status_verifikasi' => 'ditolak'])) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('status_verifikasi') === 'ditolak' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                    Ditolak ({{ $stats['ditolak'] }})
                </a>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                {{-- Dropdown Ekskul Binaan --}}
                @if($ekskuls->count() > 1)
                    <select name="ekskul_id" onchange="this.form.submit()"
                            class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400">
                        <option value="">Semua Ekskul Binaan</option>
                        @foreach ($ekskuls as $eks)
                            <option value="{{ $eks->id }}" {{ request('ekskul_id') == $eks->id ? 'selected' : '' }}>
                                {{ $eks->nama_ekskul }}
                            </option>
                        @endforeach
                    </select>
                @endif

                {{-- Dropdown Sort --}}
                <select id="sort-select" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400">
                    <option value="created_at_desc" {{ $currentSortCombo === 'created_at_desc' ? 'selected' : '' }}>Terbaru</option>
                    <option value="created_at_asc" {{ $currentSortCombo === 'created_at_asc' ? 'selected' : '' }}>Terlama</option>
                    <option value="nama_asc" {{ $currentSortCombo === 'nama_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="nama_desc" {{ $currentSortCombo === 'nama_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                    <option value="status_verifikasi_asc" {{ $currentSortCombo === 'status_verifikasi_asc' ? 'selected' : '' }}>Status</option>
                </select>

                {{-- Search Box --}}
                <div class="relative flex-1 md:w-52">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / no hp..."
                           class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400">
                    <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                @if($hasActiveFilter)
                    <a href="{{ route('pembina.pelatih.index') }}" title="Reset Filter"
                       class="p-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sortSelect = document.getElementById('sort-select');
            var sortInput = document.getElementById('sort-input');
            var directionInput = document.getElementById('direction-input');
            var form = document.getElementById('filter-pelatih-form');

            if (sortSelect && sortInput && directionInput && form) {
                sortSelect.addEventListener('change', function () {
                    var val = this.value.split('_');
                    var dir = val.pop();
                    var col = val.join('_');
                    sortInput.value = col;
                    directionInput.value = dir || 'desc';
                    form.submit();
                });
            }
        });
    </script>

    {{-- LIST CARD PELATIH --}}
    <div class="space-y-3">
        @forelse ($pelatihs as $pelatih)
            <div class="bg-white rounded-3xl border border-sky-100 p-5 md:p-6 shadow-sm hover:shadow-md transition">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    {{-- Identity & Info --}}
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 text-white font-black text-base flex items-center justify-center shadow-md shadow-sky-200 shrink-0">
                            {{ strtoupper(substr($pelatih->nama, 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-sm font-extrabold text-slate-900">{{ $pelatih->nama }}</h2>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                    {{ ucfirst($pelatih->jenis_kelamin ?? 'Laki-laki') }}
                                </span>

                                {{-- Verification Status Badge --}}
                                @if($pelatih->isTerverifikasi())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Terverifikasi Kesiswaan (Aktif di Katalog)
                                    </span>
                                @elseif($pelatih->isPending())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Menunggu Verifikasi Kesiswaan
                                    </span>
                                @elseif($pelatih->isDitolak())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Ditolak Kesiswaan
                                    </span>
                                @endif
                            </div>

                            <p class="text-xs text-sky-600 font-bold mt-1">
                                Ekskul: <span class="text-slate-800">{{ $pelatih->ekskul?->nama_ekskul ?? 'Belum ditentukan' }}</span>
                            </p>

                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-slate-500 mt-2">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ $pelatih->no_hp }}
                                </span>
                                @if($pelatih->email)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        {{ $pelatih->email }}
                                    </span>
                                @endif
                                @if($pelatih->sosmed)
                                    <span class="flex items-center gap-1 text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        {{ $pelatih->sosmed }}
                                    </span>
                                @endif
                            </div>

                            @if($pelatih->domisili || $pelatih->alamat)
                                <p class="text-[11px] text-slate-500 mt-1">
                                    <strong class="text-slate-700">Domisili:</strong> {{ $pelatih->domisili ?? '-' }} &bull;
                                    <span class="text-slate-400 italic">Alamat: {{ $pelatih->alamat ?? '-' }}</span>
                                </p>
                            @endif

                            @if($pelatih->isDitolak() && $pelatih->catatan_verifikasi)
                                <div class="mt-2 p-2.5 rounded-xl bg-rose-50 border border-rose-100 text-[11px] text-rose-700">
                                    <strong class="font-bold">Catatan Kesiswaan:</strong> {{ $pelatih->catatan_verifikasi }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Documents & Download Links --}}
                    <div class="flex items-center gap-2 shrink-0 border-t md:border-t-0 pt-3 md:pt-0">
                        {{-- CV (PDF) --}}
                        @if($pelatih->cv)
                            <a href="{{ route('storage.public', ['path' => $pelatih->cv]) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 font-bold text-xs border border-sky-200 transition">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                CV (PDF)
                            </a>
                        @endif

                        {{-- Sertifikat Pelatih (PDF) --}}
                        @if($pelatih->sertifikat)
                            <a href="{{ route('storage.public', ['path' => $pelatih->sertifikat]) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-xs border border-amber-200 transition">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Sertifikat (PDF)
                            </a>
                        @else
                            <span class="text-[10px] text-slate-400 italic">Sertifikat belum ada</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-3xl border border-sky-100 shadow-sm">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 text-sky-400 flex items-center justify-center mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Belum Ada Pelatih yang Didaftarkan</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Kamu belum mendaftarkan pelatih untuk ekskul binaanmu. Daftarkan pelatih sekarang agar dapat diverifikasi Kesiswaan.
                </p>
                <div class="mt-4">
                    <a href="{{ route('pembina.pelatih.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 text-white font-bold text-xs hover:bg-sky-700 transition shadow-md shadow-sky-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Daftarkan Pelatih Sekarang
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if ($pelatihs->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-sky-100">
            {{ $pelatihs->links() }}
        </div>
    @endif

</div>
@endsection
