@extends('layouts.kesiswaan')

@section('title', 'Edit Ekskul - ' . $ekskul->nama_ekskul)

@section('content')
<div class="max-w-2xl mx-auto space-y-6 animate-fade-up">

    {{-- HEADER & BACK BUTTON --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('kesiswaan.ekskuls.index') }}"
               class="w-10 h-10 rounded-2xl bg-white border border-slate-200/80 text-slate-600 hover:text-sky-600 hover:border-sky-200 flex items-center justify-center transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Ekskul</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kelola data ekstrakurikuler & status keaktifan</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $ekskul->status ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
            {{ $ekskul->status ? 'Status: Aktif' : 'Status: Nonaktif' }}
        </span>
    </div>

    {{-- ALERT ERRORS --}}
    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-100 text-rose-700 text-xs rounded-2xl font-medium">
            <div class="font-bold mb-1">Terjadi kesalahan pada data yang dimasukkan:</div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM EDIT --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-6 md:p-8">
        <form method="POST" action="{{ route('kesiswaan.ekskuls.update', $ekskul) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nama Ekskul --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Nama Ekskul <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_ekskul" value="{{ old('nama_ekskul', $ekskul->nama_ekskul) }}" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            {{-- Status Keaktifan --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Status Keaktifan Ekskul <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex items-center p-3.5 rounded-2xl border border-slate-200 cursor-pointer hover:border-emerald-300 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/40 transition">
                        <input type="radio" name="status" value="1" class="sr-only" @checked(old('status', (int) $ekskul->status) == 1) required>
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full {{ old('status', (int) $ekskul->status) == 1 ? 'bg-emerald-500 ring-4 ring-emerald-100' : 'bg-slate-300' }}"></span>
                            <div>
                                <p class="text-xs font-extrabold text-slate-900">Aktif</p>
                                <p class="text-[11px] text-slate-400">Ekskul berjalan normal</p>
                            </div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3.5 rounded-2xl border border-slate-200 cursor-pointer hover:border-slate-400 has-[:checked]:border-slate-400 has-[:checked]:bg-slate-100 transition">
                        <input type="radio" name="status" value="0" class="sr-only" @checked(old('status', (int) $ekskul->status) == 0) required>
                        <div class="flex items-center gap-3">
                            <span class="w-3.5 h-3.5 rounded-full {{ old('status', (int) $ekskul->status) == 0 ? 'bg-slate-500 ring-4 ring-slate-200' : 'bg-slate-300' }}"></span>
                            <div>
                                <p class="text-xs font-extrabold text-slate-900">Nonaktif</p>
                                <p class="text-[11px] text-slate-400">Pindah ke paling bawah & abu-abu</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Kategori Ekskul --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Kategori <span class="text-rose-500">*</span></label>
                @if ($ekskul->isWajib())
                    <div class="w-full px-4 py-2.5 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-800 font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>{{ \App\Support\AturanEkskul::KATEGORI_WAJIB }}</span>
                        <span class="font-normal text-amber-600 ml-1">&mdash; Kategori ekskul wajib tidak dapat diubah</span>
                        <input type="hidden" name="kategori" value="{{ \App\Support\AturanEkskul::KATEGORI_WAJIB }}">
                    </div>
                @else
                    <select name="kategori" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="" disabled>Pilih kategori...</option>
                        @foreach ($kategoriList as $kategori)
                            @if (!\App\Support\AturanEkskul::isWajib($kategori))
                                <option value="{{ $kategori }}" @selected(old('kategori', $ekskul->kategori) === $kategori)>
                                    {{ $kategori }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                @endif
            </div>

            {{-- Pembina --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Pembina <span class="text-rose-500">*</span></label>
                <select name="pembina_id" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="" disabled>Pilih pembina...</option>
                    @foreach ($pembinas as $p)
                        @php
                            $isCurrent = $ekskul->pembina_id == $p->id;
                            $isFull = !$isCurrent && $p->ekskuls_count >= 4;
                            $isNurianti = str_contains(strtolower($p->nama ?? ''), 'nurianti');
                        @endphp
                        <option value="{{ $p->id }}" @selected(old('pembina_id', $ekskul->pembina_id) == $p->id) @disabled($isFull || $isNurianti)>
                            {{ $p->nama }} ({{ $p->ekskuls_count }}/4 ekskul) {{ $isFull ? '– Penuh' : '' }} {{ $isNurianti ? '– Tidak Diizinkan' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition resize-none">{{ old('deskripsi', $ekskul->deskripsi) }}</textarea>
            </div>

            {{-- Jadwal --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Jadwal</label>
                <input type="text" name="jadwal" value="{{ old('jadwal', $ekskul->jadwal) }}" placeholder="Contoh: Setiap Jumat 15:30 WIB"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            {{-- BUTTONS --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('kesiswaan.ekskuls.index') }}"
                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
