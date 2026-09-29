@extends('layouts.siswa')

@section('title', 'Pengajuan Keluar')

@section('content')
    <div class="animate-fade-up">
        <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Pengajuan Keluar</h1>
        <p class="text-xs text-slate-400 mt-0.5">Ajukan permohonan keluar dari ekskul yang sedang diikuti</p>
    </div>

    <!-- Form Pengajuan -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-sm animate-fade-up" style="animation-delay: .1s">
        <h2 class="text-sm font-bold text-slate-900 mb-4">Form Pengajuan Keluar</h2>

        @if($ekskul)
            <div class="mb-4 p-4 bg-gradient-to-r from-sky-50 to-blue-50 rounded-xl border border-sky-200 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center text-xs font-extrabold uppercase shadow-md shadow-sky-200 shrink-0">
                    {{ substr($ekskul->nama_ekskul, 0, 2) }}
                </div>
                <p class="text-xs text-sky-800">Kamu terdaftar di <span class="font-bold">{{ $ekskul->nama_ekskul }}</span></p>
            </div>

            <form action="{{ route('siswa.pengajuan-keluar.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-700 block mb-1">
                            Alasan Keluar <span class="text-red-500">*</span>
                            <span class="text-red-500 font-medium normal-case">(wajib diisi)</span>
                        </label>
                        <textarea name="alasan" required rows="5"
                            class="w-full p-3 bg-sky-50/60 border @error('alasan') border-red-300 @else border-sky-100 @enderror rounded-xl text-xs text-slate-800 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition placeholder:text-slate-400"
                            placeholder="Tuliskan alasan kamu keluar dari ekskul secara jelas dan masuk akal...">{{ old('alasan') }}</textarea>
                        <p class="text-[10px] text-slate-400 mt-1.5 leading-relaxed">Wajib diisi. Tulis alasan yang jelas (minimal 10 karakter / 2 kata) — alasan seperti "asd", "gatau", atau "malas" tidak diterima.</p>
                        @error('alasan')
                            <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-5 py-3 sm:py-2.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition shadow-md shadow-red-200 hover:-translate-y-0.5">
                        Ajukan Permohonan
                    </button>
                </div>
            </form>
        @else
            <div class="text-center py-8 text-slate-400">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <p class="text-sm">Kamu belum terdaftar di ekskul manapun.</p>
                <a href="{{ route('siswa.katalog') }}" class="inline-block mt-3 text-sky-600 text-xs font-semibold hover:underline">Lihat Katalog Ekskul</a>
            </div>
        @endif
    </div>

    <!-- Riwayat Pengajuan -->
    @if(count($pengajuan) > 0)
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-sm animate-fade-up" style="animation-delay: .2s">
        <h2 class="text-sm font-bold text-slate-900 mb-4">Riwayat Pengajuan</h2>
        <div class="space-y-3">
            @foreach($pengajuan as $item)
            <div class="p-4 bg-gradient-to-r from-sky-50 to-amber-50 rounded-xl border border-sky-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-xs text-slate-600 leading-relaxed break-words">{{ $item->alasan }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->isoFormat('DD MMM Y') }}</p>
                </div>
                <span class="shrink-0 px-3 py-1 rounded-full text-xs font-semibold border self-start sm:self-auto
                    {{ $item->status == 'pending' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                    {{ $item->status == 'diterima' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : '' }}
                    {{ $item->status == 'ditolak' ? 'bg-red-100 text-red-700 border-red-200' : '' }}">
                    {{ ucfirst($item->status) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endsection
