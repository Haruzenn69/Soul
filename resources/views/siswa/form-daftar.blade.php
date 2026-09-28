@extends('layouts.siswa')

@section('title', 'Form Pendaftaran Ekskul')

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-up">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Form Pendaftaran Ekskul</h1>
            <p class="text-xs text-slate-400 mt-0.5">Isi data diri kamu untuk mendaftar ekskul</p>
        </div>
        <a href="{{ route('siswa.daftar-ekskul') }}" class="px-4 py-2 bg-white hover:bg-sky-50 text-slate-600 text-xs font-semibold rounded-lg border border-sky-100 shadow-sm transition flex items-center gap-2 w-full md:w-auto justify-center md:justify-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Kartu Ekskul yang dipilih -->
    <div class="bg-gradient-to-r from-sky-400 to-blue-500 p-4 md:p-5 rounded-2xl shadow-lg shadow-sky-200 text-white flex items-center gap-3 animate-fade-up max-w-2xl" style="animation-delay: .05s">
        <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl bg-white/20 backdrop-blur border border-white/30 flex items-center justify-center text-sm md:text-base font-extrabold uppercase shadow-inner shrink-0">
            {{ substr($ekskul->nama_ekskul, 0, 2) }}
        </div>
        <div class="min-w-0">
            <p class="text-[10px] font-bold tracking-wider uppercase text-white/80">Ekskul yang Dipilih</p>
            <h2 class="text-base md:text-lg font-extrabold truncate">{{ $ekskul->nama_ekskul }}</h2>
            <p class="text-[10px] text-white/75 truncate">Pembina: {{ $ekskul->pembina->nama ?? '-' }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-sm max-w-2xl animate-fade-up" style="animation-delay: .1s">
        <form method="POST" action="{{ route('siswa.daftar-ekskul.store') }}">
            @csrf
            <input type="hidden" name="ekskul_id" value="{{ $ekskul->id }}">

            <div class="space-y-4">
                <!-- Nama Lengkap -->
                <div>
                    <label class="text-xs font-semibold text-slate-700 block mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ $siswa->nama }}" class="w-full p-3 bg-sky-50/60 border border-sky-100 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-400 transition" readonly>
                </div>

                <!-- Grid: Kelas + NIS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-700 block mb-1">Kelas</label>
                        <input type="text" name="kelas" value="{{ $siswa->kelas->nama ?? '-' }}" class="w-full p-3 bg-sky-50/60 border border-sky-100 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-400 transition" readonly>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-700 block mb-1">NIS</label>
                        <input type="text" name="nis" value="{{ $siswa->nis }}" class="w-full p-3 bg-sky-50/60 border border-sky-100 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-400 transition" readonly>
                    </div>
                </div>

                <!-- Alasan Bergabung -->
                <div>
                    <label class="text-xs font-semibold text-slate-700 block mb-1">
                        Alasan Bergabung <span class="text-red-500">*</span>
                        <span class="text-red-500 font-medium normal-case">(wajib diisi)</span>
                    </label>
                    <textarea name="alasan" rows="5" required
                        class="w-full p-3 bg-sky-50/60 border @error('alasan') border-red-300 @else border-sky-100 @enderror rounded-xl text-sm text-slate-800 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition placeholder:text-slate-400"
                        placeholder="Tuliskan alasan kamu ingin bergabung dengan ekskul ini secara jelas dan masuk akal...">{{ old('alasan') }}</textarea>
                    <p class="text-[10px] text-slate-400 mt-1.5 leading-relaxed">Wajib diisi. Tulis alasan yang jelas (minimal 10 karakter / 2 kata) — alasan seperti "asd", "gatau", atau "malas" tidak diterima.</p>
                    @error('alasan')
                        <p class="text-red-500 text-[10px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-sky-200 hover:-translate-y-0.5">
                    Kirim Pendaftaran
                </button>
            </div>
        </form>
    </div>

    <!-- Catatan -->
    <div class="bg-amber-50 p-4 rounded-2xl border border-amber-200 max-w-2xl space-y-1.5 animate-fade-up" style="animation-delay: .2s">
        <p class="text-[10px] text-slate-600">
            <span class="text-red-500 font-semibold">*</span> = kolom wajib diisi.
        </p>
        <p class="text-[10px] text-amber-700 text-center leading-relaxed">
            Setelah mengirim pendaftaran, statusmu akan <span class="font-bold">pending</span> dan menunggu verifikasi dari ketua ekskul.
        </p>
    </div>
@endsection
