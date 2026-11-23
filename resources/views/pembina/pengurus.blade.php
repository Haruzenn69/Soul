@extends('pembina.layout')
@section('title', 'Kelola Pengurus Ekskul - ' . ($ekskul->nama_ekskul ?? 'SOUL'))

@section('content')
<div class="space-y-6 animate-fade-up max-w-7xl mx-auto">

@if (! $ekskul)
        {{-- STEP 1: PILIH EKSKUL TERLEBIH DAHULU (KATALOG KARTU EKSKUL) --}}
        <div class="space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-neutral-800">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-xs font-bold border border-sky-200/60 dark:border-sky-800/50 mb-2">
                        <i class="fa-solid fa-users-gear text-xs"></i> Manajemen Kepengurusan
                    </span>
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Pilih Ekstrakurikuler</h1>
                    <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Pilih salah satu ekstrakurikuler di bawah ini untuk melihat dan mengelola susunan pengurus inti (Ketua, Wakil Ketua, Sekretaris, Bendahara).
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 text-xs font-bold border border-slate-200/80 dark:border-neutral-700">
                        Total {{ $ekskuls->count() }} Ekskul
                    </span>
                </div>
            </div>

            @if($ekskuls->isEmpty())
                <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-slate-200/80 dark:border-neutral-800 shadow-sm p-12 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex items-center justify-center text-slate-400 mb-4">
                        <i class="fa-solid fa-folder-open text-xl"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Belum Ada Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-1">Anda belum ditugaskan membina ekstrakurikuler mana pun.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($ekskulCards as $card)
                        <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-5 border border-slate-200/80 dark:border-neutral-800 shadow-sm flex flex-col justify-between hover:border-sky-300 dark:hover:border-sky-700 hover:shadow-md transition group">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-sky-100 text-sky-800 dark:bg-sky-950/70 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60">
                                        {{ $card->ekskul->kategori ?? 'Umum' }}
                                    </span>
                                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                        {{ $card->pengurusCount }}/4 Jabatan
                                    </span>
                                </div>

                                <h3 class="text-base font-extrabold text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition mb-2">
                                    {{ $card->ekskul->nama_ekskul }}
                                </h3>

                                <div class="p-3 bg-slate-50 dark:bg-neutral-900 rounded-xl border border-slate-200/60 dark:border-neutral-800 mb-3 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-white dark:bg-neutral-800 border border-slate-200/80 dark:border-neutral-700 flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($card->ketua && $card->ketua->foto_url)
                                            <img src="{{ $card->ketua->foto_url }}" alt="{{ $card->ketua->nama }}" class="w-full h-full object-cover">
                                        @elseif($card->ketua)
                                            <span class="text-[11px] font-extrabold text-slate-700 dark:text-slate-200 uppercase">
                                                {{ strtoupper(substr($card->ketua->nama, 0, 2)) }}
                                            </span>
                                        @else
                                            <i class="fa-solid fa-crown text-slate-400 text-xs"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 block">Ketua Ekskul:</span>
                                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">
                                            {{ $card->ketua?->nama ?? 'Belum Ada Ketua' }}
                                        </p>
                                    </div>
                                </div>

                                @if($card->usulanCount > 0)
                                    <div class="mb-3 px-2.5 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-800/50 flex items-center gap-1.5 text-amber-700 dark:text-amber-300 text-[11px] font-semibold">
                                        <i class="fa-solid fa-clock text-amber-500 text-[10px]"></i>
                                        <span>{{ $card->usulanCount }} usulan menunggu</span>
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('pembina.pengurus.index', ['ekskul' => $card->ekskul->id]) }}"
                               class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs mt-3">
                                <span>Kelola Pengurus</span>
                                <i class="fa-solid fa-arrow-right text-[11px]"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        {{-- STEP 2: DETAIL PENGURUS EKSKUL TERPILIH --}}
        {{-- TOP BAR & TOMBOL KEMBALI (DROPDOWN FILTER REDUNDAN DIHAPUS) --}}
        <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-5 md:p-6 border border-slate-200/80 dark:border-neutral-800 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @if($ekskuls->count() > 1)
                        <a href="{{ route('pembina.pengurus.index') }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition shrink-0"
                           title="Kembali ke Pilihan Ekskul">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            <span>Pilih Ekskul Lain</span>
                        </a>
                    @endif
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-[11px] font-bold border border-sky-200/60 dark:border-sky-800/50 mb-1">
                            <i class="fa-solid fa-users-gear text-xs"></i> Manajemen Kepengurusan
                        </span>
                        <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Kelola Pengurus Inti: {{ $ekskul->nama_ekskul }}</h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Tentukan Ketua Ekskul dan struktur pengurus inti (Wakil Ketua, Sekretaris, Bendahara) untuk memimpin kegiatan ekskul.
                        </p>
                    </div>
                </div>
            </div>

            {{-- SUMMARY BADGE BAR --}}
            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-neutral-800 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-bold text-slate-700 dark:text-slate-300">Ekskul Aktif:</span>
                    <span class="px-3 py-1 rounded-xl bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 font-extrabold border border-sky-200/60 dark:border-sky-800/50">
                        {{ $ekskul->nama_ekskul }}
                    </span>
                    <span class="text-slate-400">·</span>
                    <span class="text-slate-600 dark:text-slate-400 font-medium">
                        {{ $anggotaEligible->count() }} Anggota Diterima
                    </span>
                    <span class="text-slate-400">·</span>
                    <span class="text-slate-600 dark:text-slate-400 font-medium">
                        {{ $pengurusAktif->count() }}/4 Jabatan Terisi
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="openModalTetapkan()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition shadow-xs">
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        <span>Tetapkan Pengurus</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- HERO BANNER (Biru Muda di Light Mode, Hitam di Dark Mode) --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-sky-500 to-blue-500 dark:from-neutral-900 dark:via-neutral-900 dark:to-neutral-950 p-6 md:p-8 text-white border border-sky-200/50 dark:border-neutral-800 shadow-xl shadow-sky-200/50 dark:shadow-none">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/20 dark:bg-neutral-800/20 blur-3xl"></div>
                <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-sky-200/30 dark:bg-neutral-800/20 blur-3xl"></div>
            </div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                        Struktur Pengurus Inti Ekskul
                    </h2>
                    <p class="text-xs md:text-sm text-sky-50 dark:text-slate-300 mt-2 leading-relaxed">
                        Hanya anggota berstatus <strong>Diterima</strong> yang dapat dipilih menjadi pengurus. Pembina memiliki wewenang penuh untuk menetapkan pengurus secara langsung dengan status aktif, menyetujui usulan dari ketua, serta mengganti atau mencopot pengurus aktif.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" onclick="openModalTetapkan()" class="px-5 py-2.5 rounded-2xl bg-white hover:bg-sky-50 text-sky-700 dark:bg-sky-500 dark:hover:bg-sky-600 dark:text-white font-extrabold text-xs shadow-lg shadow-sky-900/10 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-600 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tetapkan Pengurus</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- NOTIFIKASI PENDING USULAN (JIKA ADA) --}}
        @if($usulanMenunggu->isNotEmpty())
            <div class="p-5 rounded-3xl bg-amber-50 dark:bg-neutral-900 border border-amber-200 dark:border-neutral-800 shadow-sm">
                <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 flex items-center justify-center font-bold text-xs shrink-0 border border-amber-200 dark:border-amber-800/60">
                            {{ $usulanMenunggu->count() }}
                        </span>
                        <div>
                            <h3 class="text-sm font-extrabold text-amber-900 dark:text-amber-300">Usulan Pengurus dari Ketua Ekskul</h3>
                            <p class="text-[11px] text-amber-700 dark:text-amber-400">Menunggu verifikasi dan persetujuan dari Pembina.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($usulanMenunggu as $usulan)
                        <div class="bg-white dark:bg-neutral-950 rounded-2xl p-4 border border-amber-200 dark:border-neutral-800 shadow-xs flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60">
                                        {{ \App\Models\Pengurus::labelJabatan($usulan->jabatan) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">{{ $usulan->created_at->translatedFormat('d M Y') }}</span>
                                </div>
                                <div class="flex items-center gap-3 my-2">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-800 overflow-hidden shrink-0 border border-slate-200 dark:border-neutral-700">
                                        <img src="{{ $usulan->siswa?->foto_url ?? asset('images/default.png') }}" alt="{{ $usulan->siswa?->nama }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-extrabold text-slate-800 dark:text-slate-100 truncate">{{ $usulan->siswa?->nama ?? 'Siswa' }}</h4>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400">NIS: {{ $usulan->siswa?->nis ?? '-' }} • {{ $usulan->siswa?->kelas?->nama ?? '-' }}</p>
                                    </div>
                                </div>
                                @if($usulan->catatan)
                                    <p class="text-[11px] text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-neutral-900 p-2 rounded-xl mb-3 border border-slate-100 dark:border-neutral-800 italic">
                                        "{{ $usulan->catatan }}"
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 dark:border-neutral-800">
                                <form method="POST" action="{{ route('pembina.pengurus.approve', $usulan->id) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs">
                                        Setujui
                                    </button>
                                </form>
                                <button type="button" onclick="openModalTolak('{{ route('pembina.pengurus.reject', $usulan->id) }}', '{{ $usulan->siswa?->nama }}', '{{ \App\Models\Pengurus::labelJabatan($usulan->jabatan) }}')" class="py-1.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 text-xs font-bold transition border border-rose-200 dark:border-rose-900/50">
                                    Tolak
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 4 KARTU JABATAN POKOK --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                    Pengurus Aktif Saat Ini
                </h3>
                <span class="text-xs text-slate-400">4 Posisi Pokok</span>
            </div>

            @php
                $posisiStyles = [
                    'ketua' => [
                        'badge' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                        'accent' => 'from-sky-400 to-blue-500',
                        'icon' => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',
                    ],
                    'wakil_ketua' => [
                        'badge' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                        'accent' => 'from-sky-400 to-blue-500',
                        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                    ],
                    'sekretaris' => [
                        'badge' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                        'accent' => 'from-sky-400 to-blue-500',
                        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    'bendahara' => [
                        'badge' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                        'accent' => 'from-sky-400 to-blue-500',
                        'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($daftarJabatan as $jabatanKey => $jabatanNama)
                    @php
                        $pengurus = $pengurusAktif->get($jabatanKey);
                        $style = $posisiStyles[$jabatanKey] ?? $posisiStyles['ketua'];
                    @endphp

                    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-lg shadow-sky-100/50 dark:shadow-none p-5 flex flex-col justify-between transition hover:shadow-xl dark:hover:border-neutral-700">
                        <div>
                            {{-- HEADER JABATAN --}}
                            <div class="flex items-center justify-between gap-2 mb-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider border {{ $style['badge'] }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $style['icon'] }}"/>
                                    </svg>
                                    {{ $jabatanNama }}
                                </span>

                                @if($pengurus)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="text-[10px] font-bold text-slate-400">Kosong</span>
                                @endif
                            </div>

                            {{-- KONTEN PENGURUS --}}
                            @if($jabatanKey === 'wakil_ketua' && isset($subdivisis) && $subdivisis->isNotEmpty())
                                <div class="py-2 space-y-2">
                                    <div class="p-2 bg-sky-50 dark:bg-sky-950/40 rounded-xl border border-sky-100 dark:border-sky-800 text-center mb-2">
                                        <span class="text-[10px] font-bold text-sky-800 dark:text-sky-300">
                                            {{ $wakilKetuas->count() }} / {{ $subdivisis->count() }} Sub Divisi Terisi
                                        </span>
                                    </div>
                                    @foreach($subdivisis as $sub)
                                        @php
                                            $wk = $wakilKetuas->firstWhere('subdivisi_id', $sub->id);
                                        @endphp
                                        <div class="p-2 rounded-xl border {{ $wk ? 'border-sky-200 dark:border-sky-800 bg-sky-50/40 dark:bg-sky-950/30' : 'border-dashed border-slate-200 dark:border-neutral-800 bg-slate-50/30' }} flex items-center justify-between gap-2">
                                            <div class="min-w-0">
                                                <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 dark:bg-sky-900 dark:text-sky-200">
                                                    {{ $sub->nama_ekskul }}
                                                </span>
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate mt-0.5">
                                                    {{ $wk?->siswa?->nama ?? 'Belum ada ketua' }}
                                                </p>
                                                @if($wk?->siswa)
                                                    <p class="text-[9px] text-slate-400 truncate">
                                                        {{ $wk->siswa->kelas?->nama ?? '-' }}
                                                    </p>
                                                @endif
                                            </div>
                                            @if($wk)
                                                <button type="button" onclick="openModalCopot('{{ route('pembina.pengurus.copot', $wk->id) }}', '{{ $wk->siswa?->nama }}', 'Wakil Ketua ({{ $sub->nama_ekskul }})')" class="p-1 text-rose-500 hover:bg-rose-50 rounded-lg shrink-0" title="Copot">
                                                    <i class="fa-solid fa-xmark text-xs"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($pengurus && $pengurus->siswa)
                                <div class="text-center py-2">
                                    <div class="w-20 h-20 mx-auto rounded-2xl p-1 bg-gradient-to-br {{ $style['accent'] }} shadow-md mb-3">
                                        <div class="w-full h-full rounded-xl overflow-hidden bg-white dark:bg-neutral-800 flex items-center justify-center">
                                            <img src="{{ $pengurus->siswa->foto_url }}" alt="{{ $pengurus->siswa->nama }}" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-slate-900 dark:text-white truncate" title="{{ $pengurus->siswa->nama }}">
                                        {{ $pengurus->siswa->nama }}
                                    </h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        NIS: {{ $pengurus->siswa->nis }} • {{ $pengurus->siswa->kelas?->nama ?? '-' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 mt-2">
                                        Ditetapkan: {{ $pengurus->created_at->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            @else
                                <div class="text-center py-8 px-2 border-2 border-dashed border-sky-200/70 dark:border-neutral-800 bg-sky-50/30 dark:bg-neutral-950/40 rounded-2xl my-2">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-sky-100/60 dark:bg-neutral-800 text-sky-500 dark:text-neutral-500 flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum Ada Pengurus</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Posisi {{ $jabatanNama }} masih belum terisi.</p>
                                </div>
                            @endif
                        </div>

                        {{-- ACTION BUTTONS (KHUSUS PEMBINA) --}}
                        <div class="mt-4 pt-4 border-t border-sky-100/80 dark:border-neutral-800 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="openModalTetapkanJabatan('{{ $jabatanKey }}')" class="flex-1 py-2 px-3 rounded-xl bg-sky-50 dark:bg-neutral-800 hover:bg-sky-100 dark:hover:bg-neutral-700 text-sky-700 dark:text-sky-300 text-xs font-bold transition text-center border border-sky-200/60 dark:border-neutral-700 shadow-xs">
                                    {{ $pengurus ? 'Ganti ' . $jabatanNama : '+ Pilih ' . $jabatanNama }}
                                </button>

                                @if($pengurus)
                                    <button type="button" onclick="openModalCopot('{{ route('pembina.pengurus.copot', $pengurus->id) }}', '{{ $pengurus->siswa?->nama }}', '{{ $jabatanNama }}')" class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/30 hover:bg-rose-100 text-rose-600 dark:text-rose-400 transition border border-rose-100 dark:border-rose-900/50" title="Copot Pengurus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            @if($jabatanKey === 'ketua' && ! $pengurus)
                                <a href="{{ route('pembina.ekskul.pilih-ketua-page', $ekskul) }}"
                                   class="w-full py-1.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 text-[11px] font-bold transition text-center shadow-xs flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-crown text-[10px] text-amber-400"></i>
                                    <span>Pilih dari Master Siswa</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- RIWAYAT PENGURUS SELESAI / DICOPOT --}}
        @if($riwayatPengurus->isNotEmpty())
            <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 p-6 shadow-sm">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    Riwayat Kepengurusan Terdahulu
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-sky-100 dark:border-neutral-800 text-slate-400 uppercase text-[10px]">
                                <th class="pb-3 font-semibold">Nama Siswa</th>
                                <th class="pb-3 font-semibold">Jabatan</th>
                                <th class="pb-3 font-semibold">Status</th>
                                <th class="pb-3 font-semibold">Tanggal Berakhir</th>
                                <th class="pb-3 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sky-50 dark:divide-neutral-800">
                            @foreach($riwayatPengurus as $item)
                                <tr class="hover:bg-sky-50/40 dark:hover:bg-neutral-800/40 transition">
                                    <td class="py-3 font-bold text-slate-800 dark:text-slate-200">
                                        {{ $item->siswa?->nama ?? '-' }}
                                        <span class="block text-[10px] text-slate-400 font-normal">NIS: {{ $item->siswa?->nis }} • {{ $item->siswa?->kelas?->nama ?? '-' }}</span>
                                    </td>
                                    <td class="py-3 font-semibold text-slate-600 dark:text-slate-300">
                                        {{ \App\Models\Pengurus::labelJabatan($item->jabatan) }}
                                    </td>
                                    <td class="py-3">
                                        @if($item->isDitolak())
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50">
                                                Ditolak
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-300">
                                                Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-slate-500 dark:text-slate-400">
                                        {{ $item->updated_at->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="py-3 text-slate-500 dark:text-slate-400 italic">
                                        {{ $item->catatan ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    @endif
</div>

{{-- MODAL TETAPKAN / GANTI PENGURUS (PEMBINA) --}}
@if($ekskul)
<div id="modal-tetapkan" class="fixed inset-0 z-50 hidden bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-neutral-900 w-full max-w-lg rounded-3xl p-6 shadow-2xl border border-sky-100 dark:border-neutral-800 animate-fade-up">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tetapkan / Ganti Pengurus</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Pilih anggota ekskul berstatus diterima untuk diangkat.</p>
            </div>
            <button type="button" onclick="closeModalTetapkan()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 flex items-center justify-center transition">
                ✕
            </button>
        </div>

        <form method="POST" action="{{ route('pembina.pengurus.tetapkan', $ekskul->id) }}" class="space-y-4">
            @csrf

            <div>
                <label for="input-jabatan" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Jabatan</label>
                <select id="input-jabatan" name="jabatan" required onchange="toggleKetuaHint(this.value)" class="w-full px-3 py-2.5 bg-sky-50/60 dark:bg-neutral-950 border border-sky-100 dark:border-neutral-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white dark:focus:bg-neutral-900 transition">
                    @foreach($daftarJabatan as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div id="ketua-global-hint" class="mt-2 p-2.5 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-200/60 dark:border-amber-800/50 flex items-center justify-between gap-2 text-[11px] text-amber-800 dark:text-amber-300">
                    <span>Khusus Ketua, bisa dipilih dari seluruh siswa sekolah.</span>
                    <a href="{{ route('pembina.ekskul.pilih-ketua-page', $ekskul) }}" class="font-extrabold underline hover:text-amber-900 shrink-0">Pilih dari Seluruh Siswa &rarr;</a>
                </div>

                @if(isset($subdivisis) && $subdivisis->isNotEmpty())
                    <div id="subdivisi-selector-box" class="mt-2.5 p-3 bg-sky-50 dark:bg-sky-950/40 rounded-xl border border-sky-200/60 dark:border-sky-800/50" style="display: none;">
                        <label for="input-subdivisi" class="block text-xs font-bold text-sky-900 dark:text-sky-300 mb-1">
                            Pilih Sub Divisi yang Dipimpin (Khusus Wakil Ketua) <span class="text-rose-500">*</span>
                        </label>
                        <select id="input-subdivisi" name="subdivisi_id" class="w-full px-3 py-2 bg-white dark:bg-neutral-900 border border-sky-200 dark:border-neutral-800 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-400">
                            <option value="">-- Tetapkan sebagai Ketua Sub Divisi apa? --</option>
                            @foreach($subdivisis as $sub)
                                <option value="{{ $sub->id }}">Ketua Sub Divisi {{ $sub->nama_ekskul }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Wakil Ketua pada ekskul ini memimpin masing-masing Sub Divisi.</p>
                    </div>
                @endif
            </div>

            <div>
                <label for="input-siswa" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Anggota Ekskul (Status Diterima)</label>
                @if($anggotaEligible->isEmpty())
                    <p class="text-xs text-rose-500 italic">Belum ada anggota berstatus diterima di ekskul ini.</p>
                @else
                    <select id="input-siswa" name="siswa_id" required class="w-full px-3 py-2.5 bg-sky-50/60 dark:bg-neutral-950 border border-sky-100 dark:border-neutral-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white dark:focus:bg-neutral-900 transition">
                        <option value="">-- Pilih Anggota --</option>
                        @foreach($anggotaEligible as $item)
                            <option value="{{ $item->siswa_id }}">
                                {{ $item->siswa?->nama }} (NIS: {{ $item->siswa?->nis }} • {{ $item->siswa?->kelas?->nama ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label for="input-catatan" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Catatan / Keterangan (Opsional)</label>
                <textarea id="input-catatan" name="catatan" rows="2" placeholder="Contoh: Ditetapkan berdasarkan hasil musyawarah pengurus..." class="w-full px-3 py-2 bg-sky-50/60 dark:bg-neutral-950 border border-sky-100 dark:border-neutral-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white dark:focus:bg-neutral-900 transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModalTetapkan()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-neutral-700 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-extrabold shadow-md transition">
                    Tetapkan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL COPOT PENGURUS --}}
<div id="modal-copot" class="fixed inset-0 z-50 hidden bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-neutral-900 w-full max-w-md rounded-3xl p-6 shadow-2xl border border-rose-100 dark:border-neutral-800 animate-fade-up">
        <div class="text-center mb-4">
            <div class="w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-950/60 text-rose-600 mx-auto flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Copot Jabatan Pengurus?</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Apakah Anda yakin ingin mencopot <strong id="copot-nama" class="text-slate-800 dark:text-slate-200">-</strong> dari jabatan <strong id="copot-jabatan" class="text-slate-800 dark:text-slate-200">-</strong>? Siswa akan kembali menjadi anggota biasa.
            </p>
        </div>

        <form id="form-copot" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="copot-alasan" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Pencopotan (Opsional)</label>
                <textarea id="copot-alasan" name="catatan" rows="2" placeholder="Contoh: Mengundurkan diri / evaluasi kinerja..." class="w-full px-3 py-2 bg-slate-50 dark:bg-neutral-950 border border-slate-200 dark:border-neutral-800 rounded-xl text-xs text-slate-800 dark:text-slate-200"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeModalCopot()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-neutral-700 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-md transition">
                    Ya, Copot Jabatan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TOLAK USULAN --}}
<div id="modal-tolak" class="fixed inset-0 z-50 hidden bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-neutral-900 w-full max-w-md rounded-3xl p-6 shadow-2xl border border-rose-100 dark:border-neutral-800 animate-fade-up">
        <div class="mb-4">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Tolak Usulan Pengurus</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Tolak usulan <strong id="tolak-nama">-</strong> sebagai <strong id="tolak-jabatan">-</strong>.
            </p>
        </div>

        <form id="form-tolak" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="tolak-alasan" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Penolakan</label>
                <textarea id="tolak-alasan" name="catatan" rows="2" placeholder="Berikan alasan agar ketua dapat mengusulkan calon lain..." class="w-full px-3 py-2 bg-slate-50 dark:bg-neutral-950 border border-slate-200 dark:border-neutral-800 rounded-xl text-xs text-slate-800 dark:text-slate-200"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeModalTolak()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-neutral-700 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold shadow-md transition">
                    Tolak Usulan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleKetuaHint(val) {
        var hint = document.getElementById('ketua-global-hint');
        if (hint) {
            hint.style.display = (val === 'ketua') ? 'flex' : 'none';
        }
        var subBox = document.getElementById('subdivisi-selector-box');
        if (subBox) {
            subBox.style.display = (val === 'wakil_ketua') ? 'block' : 'none';
        }
    }

    function openModalTetapkan() {
        var sel = document.getElementById('input-jabatan');
        if (sel) toggleKetuaHint(sel.value);
        document.getElementById('modal-tetapkan').classList.remove('hidden');
    }
    function openModalTetapkanJabatan(jabatan, subdivisiId = null) {
        document.getElementById('input-jabatan').value = jabatan;
        toggleKetuaHint(jabatan);
        var subSelect = document.getElementById('input-subdivisi');
        if (subSelect && subdivisiId) {
            subSelect.value = subdivisiId;
        }
        openModalTetapkan();
    }
    function closeModalTetapkan() {
        document.getElementById('modal-tetapkan').classList.add('hidden');
    }

    function openModalCopot(actionUrl, nama, jabatan) {
        document.getElementById('form-copot').action = actionUrl;
        document.getElementById('copot-nama').textContent = nama;
        document.getElementById('copot-jabatan').textContent = jabatan;
        document.getElementById('modal-copot').classList.remove('hidden');
    }
    function closeModalCopot() {
        document.getElementById('modal-copot').classList.add('hidden');
    }

    function openModalTolak(actionUrl, nama, jabatan) {
        document.getElementById('form-tolak').action = actionUrl;
        document.getElementById('tolak-nama').textContent = nama;
        document.getElementById('tolak-jabatan').textContent = jabatan;
        document.getElementById('modal-tolak').classList.remove('hidden');
    }
    function closeModalTolak() {
        document.getElementById('modal-tolak').classList.add('hidden');
    }
</script>
@endif
@endsection
