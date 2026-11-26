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

    <a href="{{ route('kesiswaan.ekskuls.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-sky-100 text-sky-700 font-bold text-xs rounded-xl shadow-sm hover:bg-sky-50 transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i>
        Kembali
    </a>

    {{-- HERO CARD --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white soul-timbul">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-4">
                {{-- Logo / Inisial --}}
                <div class="w-16 h-16 rounded-2xl overflow-hidden shrink-0 border-2 border-white/30 shadow-lg bg-white/20 flex items-center justify-center">
                    <img src="{{ $ekskul->logo_url }}" alt="{{ $ekskul->nama_ekskul }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        {{ $ekskul->nama_ekskul }}
                    </h1>
                    <p class="text-xs text-white/80 mt-1 leading-relaxed max-w-xl">
                        {{ $ekskul->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>
                    <div class="flex items-center gap-2 mt-3 flex-wrap">
                        @if ($ekskul->isSubdivisi() && $ekskul->parent)
                            <a href="{{ route('kesiswaan.ekskuls.show', $ekskul->parent_id) }}" class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-amber-400 text-slate-900 shadow-sm hover:bg-amber-300 transition">
                                <i class="fa-solid fa-code-branch text-[10px]"></i> Sub Divisi dari {{ $ekskul->parent->nama_ekskul }}
                            </a>
                        @endif
                        @if ($ekskul->kategori)
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/20 text-white shadow-sm">
                                {{ $ekskul->kategori }}
                            </span>
                        @endif
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/20 text-white shadow-sm">
                            {{ $ekskul->status ? ' Aktif' : 'X Nonaktif' }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/10 text-white/80 backdrop-blur">
                            Keanggotaan melalui Ketua
                        </span>
                        @if ($ekskul->jadwal)
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/15 text-white/90 backdrop-blur">
                                 {{ $ekskul->jadwal }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if ($ekskul->isSubdivisi() && $ekskul->parent)
        {{-- BANNER SUB DIVISI --}}
        <div class="bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 rounded-3xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl bg-sky-500 text-white flex items-center justify-center font-bold shrink-0">
                    <i class="fa-solid fa-sitemap text-sm"></i>
                </span>
                <div>
                    <span class="text-[10px] font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider block">Bagian dari Ekstrakurikuler</span>
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Ini adalah sub divisi dari <a href="{{ route('kesiswaan.ekskuls.show', $ekskul->parent_id) }}" class="font-extrabold text-sky-700 dark:text-sky-400 hover:underline">{{ $ekskul->parent->nama_ekskul }}</a>.
                    </p>
                </div>
            </div>
            <a href="{{ route('kesiswaan.ekskuls.show', $ekskul->parent_id) }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-neutral-800 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-neutral-700 text-xs font-bold shadow-xs hover:bg-sky-50 transition">
                <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                <span>Buka Ekskul Induk</span>
            </a>
        </div>
    @endif

    {{-- INFO CARDS: PEMBINA, KETUA, PELATIH --}}
    <div class="grid {{ $ekskul->isEkskul() ? 'grid-cols-1 sm:grid-cols-3' : 'grid-cols-1 sm:grid-cols-2' }} gap-2 sm:gap-4">
        {{-- Pembina --}}
        <div class="min-w-0 bg-white dark:bg-[#1a1a1a] rounded-2xl sm:rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm p-3 sm:p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <span class="text-[9px] sm:text-[11px] font-bold text-slate-400 dark:text-neutral-400 uppercase tracking-wide sm:tracking-wider">Pembina</span>
                </div>
                @if ($ekskul->pembina)
                    <div class="flex min-w-0 flex-col sm:flex-row items-start sm:items-center gap-1 sm:gap-3">
                        <div class="w-7 h-7 sm:w-11 sm:h-11 rounded-lg sm:rounded-2xl overflow-hidden shrink-0 bg-indigo-100 dark:bg-indigo-950/50 shadow-xs">
                            <img src="{{ $ekskul->pembina->foto_url }}" alt="{{ $ekskul->pembina->nama }}" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-[10px] sm:text-sm text-slate-900 dark:text-white truncate">{{ $ekskul->pembina->nama }}</div>
                            <div class="text-[9px] sm:text-[11px] text-slate-400 dark:text-neutral-400 truncate">NIP: {{ $ekskul->pembina->nip ?? '-' }}</div>
                            <div class="hidden sm:block text-[11px] text-slate-400 dark:text-neutral-400 truncate">{{ $ekskul->pembina->email ?? '-' }}</div>
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
        <div class="min-w-0 bg-white dark:bg-[#1a1a1a] rounded-2xl sm:rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm p-3 sm:p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2 sm:mb-3">
                    <span class="text-[9px] sm:text-[11px] font-bold text-slate-400 dark:text-neutral-400 uppercase tracking-wide sm:tracking-wider">
                        {{ $ekskul->isSubdivisi() ? 'Ketua Sub Divisi' : 'Ketua' }}
                    </span>
                </div>
                @php
                    $displayKetua = $ekskul->isSubdivisi() ? $ekskul->ketuaSubdivisi() : $ketua;
                @endphp
                @if ($displayKetua)
                    <div class="flex min-w-0 flex-col sm:flex-row items-start sm:items-center gap-1 sm:gap-3">
                        <div class="w-7 h-7 sm:w-11 sm:h-11 rounded-lg sm:rounded-2xl overflow-hidden shrink-0 bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 font-extrabold flex items-center justify-center text-[10px] sm:text-xs uppercase shadow-xs border border-amber-200 dark:border-amber-900/50">
                            @if ($displayKetua->foto_url)
                                <img src="{{ $displayKetua->foto_url }}" alt="{{ $displayKetua->nama }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($displayKetua->nama, 0, 2)) }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-[10px] sm:text-sm text-slate-900 dark:text-white truncate">{{ $displayKetua->nama }}</span>
                            </div>
                            <div class="text-[11px] text-slate-400 dark:text-neutral-400 truncate">NIS: {{ $displayKetua->nis }} · {{ $displayKetua->kelas->nama ?? '-' }}</div>
                            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $ekskul->isSubdivisi() ? 'Ketua Sub Divisi' : 'Ketua Aktif' }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-2 py-3 text-center">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 flex items-center justify-center text-amber-500">
                            <i class="fa-solid fa-crown text-sm"></i>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-neutral-400">Belum ada ketua</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Pelatih (Hanya untuk Ekskul, Komunitas tidak ada pelatih) --}}
        @if ($ekskul->isEkskul())
            <div class="min-w-0 bg-white dark:bg-[#1a1a1a] rounded-2xl sm:rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm p-3 sm:p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2 sm:mb-3">
                        <span class="text-[9px] sm:text-[11px] font-bold text-slate-400 dark:text-neutral-400 uppercase tracking-wide sm:tracking-wider">Pelatih</span>
                    </div>
                    @if ($ekskul->pelatih)
                        <div class="flex min-w-0 flex-col sm:flex-row items-start sm:items-center gap-1 sm:gap-3">
                            <div class="w-7 h-7 sm:w-11 sm:h-11 rounded-lg sm:rounded-2xl overflow-hidden bg-emerald-100 dark:bg-emerald-950/50 shrink-0 shadow-xs">
                                <img src="{{ asset('images/pelatih.webp') }}" alt="Foto Profil {{ $ekskul->pelatih->nama }}" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-[10px] sm:text-sm text-slate-900 dark:text-white truncate">{{ $ekskul->pelatih->nama }}</span>
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
        @endif
    </div>

    {{-- SECTION SUB DIVISI (HANYA UNTUK EKSKUL UTAMA / BUKAN SUB DIVISI DAN BUKAN KOMUNITAS) --}}
    @if (!$ekskul->isSubdivisi() && $ekskul->isEkskul())
        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm p-5 md:p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-neutral-800">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                        <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Sub Divisi ({{ $ekskul->subdivisis->count() }})</h2>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Kelompok atau divisi peminatan di bawah naungan {{ $ekskul->nama_ekskul }}.</p>
                </div>

                <button type="button" onclick="document.getElementById('modal-tambah-subdivisi').showModal()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 hover:-translate-y-0.5 transition-all self-start sm:self-auto">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambahkan Sub Divisi</span>
                </button>
            </div>

            @if($ekskul->subdivisis->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 dark:border-neutral-800 p-8 text-center bg-slate-50/50 dark:bg-neutral-900/50">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-sky-500 flex items-center justify-center mb-3">
                        <i class="fa-solid fa-sitemap text-lg"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum ada sub divisi pada {{ $ekskul->nama_ekskul }}.</p>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-md mx-auto">
                        Jika ekskul ini memiliki cabang/divisi seperti halnya Seni (Tari, Vokal, Band, Rampak) atau GMC (Mandarin, Jepang, Literasi), Anda dapat menambahkannya di sini.
                    </p>
                    <button type="button" onclick="document.getElementById('modal-tambah-subdivisi').showModal()" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 bg-sky-50 dark:bg-sky-950/50 hover:bg-sky-100 text-sky-700 dark:text-sky-300 text-xs font-bold rounded-xl border border-sky-200 dark:border-sky-800 transition">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Tambahkan Sub Divisi Sekarang</span>
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    @foreach($ekskul->subdivisis as $sub)
                        @php
                            $ketuaSub = $ekskul->wakilKetuaSubdivisi($sub) ?? $sub->ketua();
                        @endphp
                        <div class="p-4 rounded-2xl border border-slate-200/80 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-xs hover:border-sky-300 transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                        Sub Divisi
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-400">
                                        {{ $sub->pendaftarans_count ?? $sub->pendaftarans()->count() }} Anggota
                                    </span>
                                </div>

                                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-1">
                                    {{ $sub->nama_ekskul }}
                                </h3>

                                @if($sub->deskripsi)
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 mb-3">
                                        {{ $sub->deskripsi }}
                                    </p>
                                @endif

                                {{-- KETUA SUB DIVISI (WAKIL KETUA DI INDUK) --}}
                                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-100 dark:border-neutral-800 mb-3 flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-[10px] font-bold shrink-0">
                                        @if($ketuaSub?->foto_url)
                                            <img src="{{ $ketuaSub->foto_url }}" class="w-full h-full object-cover rounded-lg">
                                        @else
                                            {{ strtoupper(substr($ketuaSub?->nama ?? 'W', 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider">Ketua Sub Divisi (Wakil Ketua):</span>
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">
                                            {{ $ketuaSub?->nama ?? 'Belum Ditentukan' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-neutral-800">
                                <a href="{{ route('kesiswaan.ekskuls.show', $sub) }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-600 hover:text-sky-700">
                                    <span>Detail Anggota</span>
                                    <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                </a>

                                <form method="POST" action="{{ route('kesiswaan.ekskuls.subdivisi.destroy', [$ekskul, $sub]) }}"
                                      onsubmit="return confirm('Hapus sub divisi \'{{ $sub->nama_ekskul }}\' dari {{ $ekskul->nama_ekskul }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-[10px] font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-lg transition" title="Hapus Sub Divisi">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- MODAL TAMBAH SUB DIVISI --}}
        <dialog id="modal-tambah-subdivisi" class="rounded-3xl backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm p-0 w-full max-w-md shadow-2xl border border-sky-100">
            <form method="POST" action="{{ route('kesiswaan.ekskuls.subdivisi.store', $ekskul) }}" class="p-6 md:p-8 space-y-4 bg-white dark:bg-neutral-900">
                @csrf
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-neutral-800">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Tambah Sub Divisi</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Tambahkan peminatan di bawah {{ $ekskul->nama_ekskul }}</p>
                    </div>
                    <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                        &times;
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1.5">Nama Sub Divisi <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_ekskul" placeholder="Contoh: Band, Vokal, Tari, Rampak, Mandarin..." required
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-2xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1.5">Deskripsi Singkat</label>
                    <textarea name="deskripsi" rows="2" placeholder="Penjelasan tentang sub divisi ini..."
                              class="w-full px-4 py-2.5 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-2xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-1.5">Jadwal (Opsional)</label>
                    <input type="text" name="jadwal" placeholder="Contoh: Setiap Rabu, 15:30 - 17:00"
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-2xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>

                <div class="flex gap-2.5 pt-3 border-t border-slate-100 dark:border-neutral-800">
                    <button type="button" onclick="this.closest('dialog').close()"
                            class="px-5 py-2.5 bg-slate-100 dark:bg-neutral-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-2xl transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                        Simpan Sub Divisi
                    </button>
                </div>
            </form>
        </dialog>
    @endif

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
                
                <span>Anggota ({{ $anggota->total() }})</span>
            </div>
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIS anggota..."
                       class="w-full pl-4 pr-4 py-2.5 bg-slate-50 dark:bg-neutral-800/80 border border-slate-200 dark:border-neutral-700 rounded-2xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
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
                                
                            </a>
                        </th>
                        <th class="py-4 px-5">Kelas</th>
                        <th class="py-4 px-5">
                            <a href="{{ $sortLink('tanggal_daftar') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Bergabung
                                
                            </a>
                        </th>
                        <th class="py-4 px-5">
                            <a href="{{ $sortLink('status') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Status
                                
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
                                                <span class="text-rose-400">P</span>
                                            @else
                                                <span class="text-sky-400">L</span>
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
                                            
                                            <span>Detail</span>
                                        </a>
                                        <a href="{{ route('kesiswaan.users.edit', $s->user_id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 dark:bg-sky-950/40 hover:bg-sky-100 dark:hover:bg-sky-900/60 text-sky-700 dark:text-sky-300 font-bold rounded-xl transition text-xs"
                                           title="Edit Data {{ $s->nama }}">
                                            
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
