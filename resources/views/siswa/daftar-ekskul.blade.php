@extends('layouts.siswa')

@section('title', 'Daftar Ekskul')

@section('search')
    <form method="GET" action="{{ route('siswa.daftar-ekskul') }}" class="relative w-full max-w-md hidden sm:block">
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center w-4 h-4 pointer-events-none opacity-60">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </span>
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari ekskul..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
    </form>
@endsection

@section('content')
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-up">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Daftar Ekskul</h1>
            <p class="text-xs text-slate-400 mt-0.5">Pilih ekskul yang ingin kamu ikuti</p>
        </div>
        <a href="{{ route('siswa.katalog') }}" class="px-4 py-2 bg-white hover:bg-sky-50 text-slate-600 text-xs font-semibold rounded-lg border border-sky-100 shadow-sm transition flex items-center gap-2 w-full md:w-auto justify-center md:justify-start">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Katalog
        </a>
    </div>

    <!-- Informasi -->
    <div class="bg-sky-50 p-4 rounded-2xl border border-sky-200 flex items-start gap-3 animate-fade-up" style="animation-delay: .1s">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center shrink-0 shadow-md shadow-sky-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="pt-1">
            <p class="text-xs text-sky-800">Kamu hanya bisa mendaftar ke <span class="font-semibold">1 ekskul</span> dalam satu periode. Pilih dengan baik sesuai minat dan bakatmu.</p>
        </div>
    </div>

    <!-- Daftar Ekskul -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
        @forelse($ekskuls as $ekskul)
        <div class="bg-white p-4 md:p-5 rounded-2xl border border-sky-100 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-sky-200 transition-all duration-300">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white font-bold flex items-center justify-center text-sm shadow-lg shadow-sky-200 uppercase shrink-0">
                    {{ substr($ekskul->nama_ekskul, 0, 2) }}
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm font-bold text-slate-900 truncate">{{ $ekskul->nama_ekskul }}</h3>
                    <p class="text-[10px] text-slate-400 truncate">Pembina: {{ $ekskul->pembina->nama ?? '-' }}</p>
                </div>
            </div>
            <p class="text-xs text-slate-600 mb-3 line-clamp-2">{{ $ekskul->deskripsi ?? 'Deskripsi belum tersedia' }}</p>

            @if($ekskul->is_open_recruitment)
                <div class="flex items-center justify-between gap-2 mb-3 flex-wrap">
                    <span class="recruitment-open-badge text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-full">Buka Pendaftaran</span>
                    <span class="text-[10px] text-slate-400">{{ $ekskul->jadwal ?? 'Jadwal belum diatur' }}</span>
                </div>
                <a href="{{ route('siswa.form-daftar', $ekskul->id) }}" class="block w-full py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-semibold rounded-lg transition shadow-md shadow-sky-200 text-center hover:-translate-y-0.5">
                    Daftar Sekarang
                </a>
            @else
                <div class="flex items-center justify-between gap-2 mb-3 flex-wrap">
                    <span class="text-xs font-semibold text-red-500 bg-red-50 border border-red-100 px-2 py-1 rounded-full">Tidak Membuka Pendaftaran</span>
                    <span class="text-[10px] text-slate-400">{{ $ekskul->jadwal ?? 'Jadwal belum diatur' }}</span>
                </div>
                <button class="w-full py-2.5 bg-slate-100 text-slate-400 text-xs font-semibold rounded-lg cursor-not-allowed border border-slate-100" disabled>
                    Tidak Tersedia
                </button>
            @endif
        </div>
        @empty
        <div class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-12 text-slate-400">
            <p class="text-sm">Belum ada ekskul yang tersedia untuk didaftar.</p>
        </div>
        @endforelse
    </div>

    <!-- Catatan -->
    <div class="bg-amber-50 p-4 rounded-2xl border border-amber-200 animate-fade-up">
        <p class="text-[10px] text-amber-700 text-center">
            Setelah mendaftar, status pendaftaranmu akan menunggu verifikasi dari ketua ekskul.
        </p>
    </div>
@endsection
