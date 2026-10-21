@extends('layouts.siswa')

@section('title', 'Pengajuan Keluar Ekskul')

@section('content')
    <div class="animate-fade-up">
        <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white">Pengajuan Keluar Ekskul</h1>
        <p class="text-xs text-slate-400 dark:text-neutral-400 mt-0.5">Ajukan permohonan keluar dari salah satu ekskul yang sedang kamu ikuti</p>
    </div>

    <!-- Form Pengajuan -->
    <div class="bg-white dark:bg-[#1a1a1a] p-5 md:p-6 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm animate-fade-up" style="animation-delay: .1s">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Form Permohonan Keluar
        </h2>

        @if(isset($pendaftarans) && $pendaftarans->isNotEmpty())
            <form action="{{ route('siswa.pengajuan-keluar.store') }}" method="POST">
                @csrf
                <div class="space-y-5">
                    {{-- PILIHAN EKSKUL --}}
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-200 block mb-2">
                            Pilih Ekskul yang Ingin Kamu Keluar <span class="text-rose-500">*</span>
                        </label>

                        @if($pendaftarans->count() > 1)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($pendaftarans as $p)
                                    @php
                                        $selectedEkskulId = old('ekskul_id', request('ekskul', $ekskul?->id ?? $pendaftarans->first()->ekskul_id));
                                        $isChecked = (int)$selectedEkskulId === (int)$p->ekskul_id;
                                    @endphp
                                    <label class="relative flex items-center gap-3.5 p-4 rounded-2xl border cursor-pointer transition-all {{ $isChecked ? 'border-rose-500 bg-rose-50/50 dark:bg-rose-950/20 ring-2 ring-rose-200 dark:ring-rose-900/40' : 'border-slate-200 dark:border-neutral-700 hover:border-slate-300 dark:hover:border-neutral-600 bg-white dark:bg-neutral-800/40' }}">
                                        <input type="radio" name="ekskul_id" value="{{ $p->ekskul_id }}" class="text-rose-600 focus:ring-rose-500 w-4 h-4"
                                            {{ $isChecked ? 'checked' : '' }} required>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate">{{ $p->ekskul->nama_ekskul }}</p>
                                            <p class="text-[10px] text-slate-400 dark:text-neutral-400 mt-0.5">Terdaftar sejak {{ $p->tanggal_daftar?->isoFormat('D MMM Y') ?? '-' }}</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                            {{ strtoupper(substr($p->ekskul->nama_ekskul, 0, 1)) }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-neutral-400 mt-1.5">
                                Kamu terdaftar di {{ $pendaftarans->count() }} ekskul. Pilih salah satu ekskul yang ingin diajukan permohonan keluar.
                            </p>
                        @else
                            @php $single = $pendaftarans->first(); @endphp
                            <input type="hidden" name="ekskul_id" value="{{ $single->ekskul_id }}">
                            <div class="p-4 bg-slate-50 dark:bg-neutral-800/60 border border-slate-200 dark:border-neutral-700 rounded-2xl flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center text-xs font-extrabold uppercase shrink-0 shadow-sm">
                                        {{ substr($single->ekskul->nama_ekskul, 0, 2) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $single->ekskul->nama_ekskul }}</p>
                                        <p class="text-[10px] text-slate-400 dark:text-neutral-400">Terdaftar sejak {{ $single->tanggal_daftar?->isoFormat('D MMMM Y') ?? '-' }}</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-900/50">
                                    Aktif
                                </span>
                            </div>
                        @endif

                        @error('ekskul_id')
                            <p class="text-red-500 text-[10px] mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ALASAN KELUAR --}}
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-200 block mb-1.5">
                            Alasan Keluar <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alasan" required rows="4"
                            class="w-full p-3.5 bg-slate-50 dark:bg-neutral-800/60 border @error('alasan') border-red-300 dark:border-red-800 @else border-slate-200 dark:border-neutral-700 @enderror rounded-2xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-rose-400 focus:ring-4 focus:ring-rose-100 dark:focus:ring-rose-900/30 transition placeholder:text-slate-400"
                            placeholder="Tuliskan alasan kamu keluar dari ekskul secara jelas dan masuk akal...">{{ old('alasan') }}</textarea>
                        <p class="text-[10px] text-slate-400 dark:text-neutral-400 mt-1.5 leading-relaxed">
                            Tulis alasan yang jelas (minimal 10 karakter / 2 kata) &mdash; alasan seperti "asd", "gatau", atau "malas" tidak dapat diterima.
                        </p>
                        @error('alasan')
                            <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-rose-200 dark:shadow-none hover:-translate-y-0.5">
                            Kirim Permohonan Keluar
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="text-center py-10 text-slate-400">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 flex items-center justify-center text-sky-400 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Kamu belum terdaftar di ekskul aktif manapun.</p>
                <p class="text-[11px] text-slate-400 dark:text-neutral-400 mt-1">Kamu bisa menjelajahi pilihan ekskul yang tersedia di katalog.</p>
                <a href="{{ route('siswa.katalog') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Lihat Katalog Ekskul &rarr;
                </a>
            </div>
        @endif
    </div>

    <!-- Riwayat Pengajuan -->
    @if(isset($pengajuan) && count($pengajuan) > 0)
    <div class="bg-white dark:bg-[#1a1a1a] p-5 md:p-6 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm animate-fade-up" style="animation-delay: .2s">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Riwayat Pengajuan Keluar
        </h2>
        <div class="space-y-3">
            @foreach($pengajuan as $item)
            <div class="p-4 bg-slate-50 dark:bg-neutral-800/60 rounded-2xl border border-slate-100 dark:border-neutral-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        @if($item->ekskul)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 dark:bg-sky-950/50 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                {{ $item->ekskul->nama_ekskul }}
                            </span>
                        @endif
                        <span class="text-[10px] text-slate-400 dark:text-neutral-400">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->isoFormat('DD MMMM Y') }}</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed break-words">{{ $item->alasan }}</p>
                </div>
                <span class="shrink-0 px-3 py-1 rounded-full text-xs font-bold border self-start sm:self-auto
                    {{ $item->status == 'pending' ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-900/50' : '' }}
                    {{ $item->status == 'diterima' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/50' : '' }}
                    {{ $item->status == 'ditolak' ? 'bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-900/50' : '' }}">
                    @if($item->status == 'pending') Menunggu Keputusan @elseif($item->status == 'diterima') Disetujui @else Ditolak @endif
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection
