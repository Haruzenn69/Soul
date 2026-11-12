@extends('layouts.kesiswaan')

@section('title', 'Verifikasi & Data Pelatih')

@section('content')
<div class="space-y-6 animate-fade-up">

    {{-- HERO CARD --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white soul-timbul">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                    Verifikasi Pelatih Ekskul
                </h1>
                <p class="text-xs text-white/80 mt-1 max-w-xl leading-relaxed">
                    Tinjau data biodata, CV, dan sertifikat PDF pelatih yang diajukan oleh Pembina. Pelatih yang telah diverifikasi akan otomatis tampil di katalog ekskul.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
               
            </div>
        </div>
    </div>



    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total --}}
        <div class="bg-white rounded-2xl p-4 border border-sky-100 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0">
                <i class="fa-solid fa-users text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pelatih</p>
                <p class="text-xl font-black text-sky-700">{{ $stats['total'] }}</p>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-2xl p-4 border border-sky-100 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0">
                <i class="fa-solid fa-hourglass-half text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Perlu Diverifikasi</p>
                <p class="text-xl font-black text-sky-700">{{ $stats['pending'] }}</p>
            </div>
        </div>

        {{-- Terverifikasi --}}
        <div class="bg-white rounded-2xl p-4 border border-sky-100 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0">
                <i class="fa-solid fa-circle-check text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Terverifikasi</p>
                <p class="text-xl font-black text-sky-700">{{ $stats['terverifikasi'] }}</p>
            </div>
        </div>

        {{-- Ditolak --}}
        <div class="bg-white rounded-2xl p-4 border border-sky-100 shadow-sm flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0">
                <i class="fa-solid fa-circle-xmark text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ditolak</p>
                <p class="text-xl font-black text-sky-700">{{ $stats['ditolak'] }}</p>
            </div>
        </div>
    </div>

    {{-- FILTER & SEARCH (POPOVER DESIGN) --}}
    @php
        $sort = $sort ?? 'created_at';
        $direction = $direction ?? 'desc';
        $currentSortCombo = $sort . '_' . $direction;
        $isStatusFiltered = request()->filled('status') && request('status') !== 'all';
        $isSortFiltered = $currentSortCombo !== 'created_at_desc';
        $activeFilterCount = ($isStatusFiltered ? 1 : 0) + ($isSortFiltered ? 1 : 0);
        $hasActiveFilter = request()->filled('q') || $activeFilterCount > 0;
    @endphp

    <div x-data="{ openFilter: false, sortCombo: '{{ $currentSortCombo }}' }" class="space-y-3">
        {{-- QUICK STATUS TABS --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <a href="{{ route('kesiswaan.pelatih.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
               class="px-3.5 py-1.5 rounded-2xl text-xs font-bold transition whitespace-nowrap {{ $status === 'all' ? 'bg-sky-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Semua ({{ $stats['total'] }})
            </a>
            <a href="{{ route('kesiswaan.pelatih.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
               class="px-3.5 py-1.5 rounded-2xl text-xs font-bold transition whitespace-nowrap {{ $status === 'pending' ? 'bg-sky-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Menunggu Verifikasi ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('kesiswaan.pelatih.index', array_merge(request()->except('status', 'page'), ['status' => 'terverifikasi'])) }}"
               class="px-3.5 py-1.5 rounded-2xl text-xs font-bold transition whitespace-nowrap {{ $status === 'terverifikasi' ? 'bg-sky-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Terverifikasi ({{ $stats['terverifikasi'] }})
            </a>
            <a href="{{ route('kesiswaan.pelatih.index', array_merge(request()->except('status', 'page'), ['status' => 'ditolak'])) }}"
               class="px-3.5 py-1.5 rounded-2xl text-xs font-bold transition whitespace-nowrap {{ $status === 'ditolak' ? 'bg-sky-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Ditolak ({{ $stats['ditolak'] }})
            </a>
        </div>

        {{-- SEARCH & POPOVER FILTER FORM --}}
        <form method="GET" action="{{ route('kesiswaan.pelatih.index') }}" id="filter-pelatih-kesiswaan-form" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm flex flex-col md:flex-row gap-3 items-stretch md:items-center">
            <input type="hidden" name="sort" id="sort-input" :value="sortCombo.substring(0, sortCombo.lastIndexOf('_'))">
            <input type="hidden" name="direction" id="direction-input" :value="sortCombo.substring(sortCombo.lastIndexOf('_') + 1)">

            {{-- Input Pencarian --}}
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pelatih, ekskul, pembina..."
                       class="w-full pl-4 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            {{-- Tombol Filter & Popover Panel --}}
            <div class="relative flex items-center gap-2">
                <button type="button" @click="openFilter = !openFilter"
                        class="px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 border {{ $activeFilterCount > 0 ? 'bg-sky-50 border-sky-300 text-sky-700 shadow-sm shadow-sky-100' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                    
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
                    <a href="{{ route('kesiswaan.pelatih.index') }}"
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
                                
                            </span>
                            <h4 class="text-xs font-bold text-slate-800">Opsi Filter Pelatih</h4>
                        </div>
                        <button type="button" @click="openFilter = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">
                            &times;
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status Verifikasi</label>
                            <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="terverifikasi" {{ $status === 'terverifikasi' ? 'selected' : '' }}>Terverifikasi</option>
                                <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Urutkan Berdasarkan</label>
                            <select x-model="sortCombo" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="created_at_desc">Terbaru</option>
                                <option value="created_at_asc">Terlama</option>
                                <option value="nama_asc">Nama (A-Z)</option>
                                <option value="nama_desc">Nama (Z-A)</option>
                                <option value="status_verifikasi_asc">Status Verifikasi</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('kesiswaan.pelatih.index', array_filter(['q' => request('q')])) }}"
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
                        <a href="{{ route('kesiswaan.pelatih.index', request()->except('q', 'page')) }}" class="hover:text-slate-700 transition">&times;</a>
                    </span>
                @endif

                @if($isStatusFiltered)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-xs font-semibold">
                        <span>Status: <strong>{{ ucfirst($status) }}</strong></span>
                        <a href="{{ route('kesiswaan.pelatih.index', request()->except('status', 'page')) }}" class="hover:text-slate-700 transition">&times;</a>
                    </span>
                @endif

                @if($isSortFiltered)
                    @php
                        $sortLabels = [
                            'created_at_asc' => 'Terlama',
                            'nama_asc' => 'Nama (A-Z)',
                            'nama_desc' => 'Nama (Z-A)',
                            'status_verifikasi_asc' => 'Status Verifikasi',
                        ];
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-xs font-semibold">
                        <span>Urutan: <strong>{{ $sortLabels[$currentSortCombo] ?? $currentSortCombo }}</strong></span>
                        <a href="{{ route('kesiswaan.pelatih.index', request()->except(['sort', 'direction', 'page'])) }}" class="hover:text-slate-700 transition">&times;</a>
                    </span>
                @endif

                <a href="{{ route('kesiswaan.pelatih.index') }}" class="text-xs font-bold text-sky-700 hover:text-sky-800 hover:underline transition ml-1">
                    Hapus Semua Filter
                </a>
            </div>
        @endif
    </div>

    {{-- LIST DATA PELATIH --}}
    <div class="space-y-4">
        @forelse ($pelatihs as $pelatih)
            <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-5 md:p-6 transition">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                    {{-- Identity & Detail --}}
                    <div class="flex items-start gap-4 flex-1">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 text-white font-black text-base flex items-center justify-center shadow-md shrink-0">
                            {{ strtoupper(substr($pelatih->nama, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-base font-extrabold text-slate-900">{{ $pelatih->nama }}</h2>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                    {{ ucfirst($pelatih->jenis_kelamin ?? 'Laki-laki') }}
                                </span>

                                @if($pelatih->isTerverifikasi())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-sky-600 text-white border border-sky-600">
                                        
                                        Terverifikasi
                                    </span>
                                @elseif($pelatih->isPending())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-white text-sky-700 border border-sky-300">
                                        
                                        Menunggu Verifikasi Kesiswaan
                                    </span>
                                @elseif($pelatih->isDitolak())
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                        
                                        Ditolak
                                    </span>
                                @endif
                            </div>

                            {{-- Target Ekskul & Pembina Pengusul --}}
                            <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs">
                                <span class="font-bold text-sky-700">
                                    Ekskul: <span class="text-slate-800">{{ $pelatih->ekskul?->nama_ekskul ?? '-' }}</span>
                                </span>
                                <span class="text-slate-400">&bull;</span>
                                <span class="text-slate-600">
                                    Pengusul: <strong class="text-slate-800">{{ $pelatih->pembina?->nama ?? 'Sistem / Umum' }}</strong>
                                </span>
                                <span class="text-slate-400">&bull;</span>
                                <span class="text-slate-400 text-[11px]">
                                    Diajukan: {{ $pelatih->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                                </span>
                            </div>

                            {{-- Kontak --}}
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-slate-500 mt-2">
                                <span class="flex items-center gap-1">
                                    
                                    {{ $pelatih->no_hp }}
                                </span>
                                @if($pelatih->email)
                                    <span class="flex items-center gap-1">
                                        
                                        {{ $pelatih->email }}
                                    </span>
                                @endif
                                @if($pelatih->sosmed)
                                    <span class="flex items-center gap-1 text-slate-600">
                                        
                                        {{ $pelatih->sosmed }}
                                    </span>
                                @endif
                                @if($pelatih->domisili || $pelatih->alamat)
                                    <span class="text-slate-500 font-medium">| Domisili: <strong class="text-slate-700">{{ $pelatih->domisili ?? '-' }}</strong> &bull; <span class="italic text-slate-400">Alamat: {{ $pelatih->alamat ?? '-' }}</span></span>
                                @endif
                            </div>

                            {{-- Catatan Verifikasi Jika Ada --}}
                            @if($pelatih->isDitolak() && $pelatih->catatan_verifikasi)
                                <div class="mt-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700">
                                    <strong class="font-bold">Alasan Penolakan:</strong> {{ $pelatih->catatan_verifikasi }}
                                </div>
                            @endif

                            @if($pelatih->isTerverifikasi() && $pelatih->verified_at)
                                <p class="text-[10px] text-slate-400 mt-2">
                                    Diverifikasi pada: {{ $pelatih->verified_at->translatedFormat('d M Y, H:i') }}
                                    @if($pelatih->verifiedBy) &bull; Oleh: {{ $pelatih->verifiedBy->username ?? $pelatih->verifiedBy->nama }} @endif
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Documents & Action Buttons --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0 border-t lg:border-t-0 pt-4 lg:pt-0">
                        {{-- Dokumen Berkas --}}
                        <div class="flex items-center gap-2">
                            {{-- CV Wajib PDF --}}
                            @if($pelatih->cv)
                                <a href="{{ route('storage.public', ['path' => $pelatih->cv]) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-sm transition">
                                    
                                    Buka CV (PDF)
                                </a>
                            @endif

                            {{-- Sertifikat Wajib PDF --}}
                            @if($pelatih->sertifikat)
                                <a href="{{ route('storage.public', ['path' => $pelatih->sertifikat]) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 font-bold text-xs transition">
                                    
                                    Buka Sertifikat (PDF)
                                </a>
                            @else
                                <span class="px-2.5 py-1.5 rounded-xl bg-slate-100 text-slate-400 text-[10px] font-bold">
                                    Tanpa Sertifikat
                                </span>
                            @endif
                        </div>

                        {{-- Action Buttons untuk Pending --}}
                        @if($pelatih->isPending())
                            <div class="flex items-center gap-2">
                                {{-- Tombol Verifikasi (Approve) --}}
                                <form action="{{ route('kesiswaan.pelatih.verifikasi', $pelatih) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin memverifikasi dan menyetujui pelatih ini? Pelatih akan langsung aktif di katalog ekskul.')">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-md shadow-sky-600/20 transition">
                                        
                                        Setujui & Verifikasi
                                    </button>
                                </form>

                                {{-- Tombol Tolak (Reject) --}}
                                <button type="button"
                                        onclick="openTolakModal('{{ $pelatih->id }}', '{{ addslashes($pelatih->nama) }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-600 font-bold text-xs border border-slate-200 transition">
                                    
                                    Tolak
                                </button>
                            </div>
                        @elseif($pelatih->isDitolak())
                            {{-- Jika ditolak, kesiswaan bisa mengubah keputusan jika ada revisi --}}
                            <form action="{{ route('kesiswaan.pelatih.verifikasi', $pelatih) }}" method="POST"
                                  onsubmit="return confirm('Verifikasi ulang pelatih ini?')">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold text-xs border border-sky-200 transition">
                                    Verifikasi Ulang
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-3xl border border-sky-100 shadow-sm">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 text-sky-400 flex items-center justify-center mb-3">
                    
                </div>
                <h3 class="text-sm font-bold text-slate-700">Tidak Ada Data Pelatih</h3>
                <p class="text-xs text-slate-400 mt-1">
                    Tidak ada pendaftaran pelatih dengan kriteria filter saat ini.
                </p>
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

{{-- MODAL TOLAK VERIFIKASI --}}
<dialog id="modal-tolak" class="modal rounded-3xl backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm p-0 shadow-2xl w-full max-w-md mx-auto border border-sky-100">
    <div class="bg-white rounded-3xl p-6">
        <div class="flex justify-center">
            <img src="{{ asset('images/bingung.png') }}" alt="Tolak pendaftaran" class="w-24 h-24 object-contain">
        </div>
        <div class="text-center mt-2">
            <h3 class="text-sm font-extrabold text-slate-900">
                Tolak Pendaftaran Pelatih
            </h3>
            <p class="text-[11px] text-slate-400 font-medium">Berikan alasan agar pembina dapat merevisi berkas</p>
        </div>

        <form id="form-tolak" method="POST" class="mt-4 space-y-4">
            @csrf
            <p class="text-xs text-slate-600">
                Anda akan menolak pengajuan untuk pelatih <strong id="tolak-pelatih-nama" class="text-slate-900"></strong>. Berikan alasan penolakan agar pembina dapat merevisi berkas:
            </p>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alasan Penolakan <span class="text-rose-500">*</span>
                </label>
                <textarea name="catatan" rows="3" required placeholder="Contoh: Sertifikat yang diunggah tidak terbaca / masa berlaku lisensi telah habis..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modal-tolak').close()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-sky-600 text-white font-bold text-xs hover:bg-sky-700 transition shadow-md shadow-sky-600/20">
                    Kirim Penolakan
                </button>
            </div>
        </form>
    </div>
</dialog>

<script>
function openTolakModal(pelatihId, pelatihNama) {
    document.getElementById('tolak-pelatih-nama').textContent = pelatihNama;
    document.getElementById('form-tolak').action = `/kesiswaan/pelatih/${pelatihId}/tolak`;
    document.getElementById('modal-tolak').showModal();
}
</script>
@endsection
