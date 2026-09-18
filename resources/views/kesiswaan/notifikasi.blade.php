@extends('layouts.kesiswaan')
@section('title', 'Notifikasi')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 animate-fade-up">
        <div>
            <h1 class="text-lg md:text-xl font-extrabold text-slate-900">Notifikasi</h1>
            <p class="text-xs text-slate-400 mt-0.5">Pemberitahuan terkait data kesiswaan</p>
        </div>
        @if($notifikasis->isNotEmpty())
            <form method="POST" action="{{ route('kesiswaan.notifikasi.read-all') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" class="px-3.5 py-2 bg-white hover:bg-sky-50 text-slate-600 text-xs font-semibold rounded-lg border border-sky-100 shadow-sm transition w-full sm:w-auto">
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <!-- RINGKASAN STATISTIK -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
        <div class="bg-white p-3.5 md:p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .1s">
            <p class="text-lg md:text-2xl font-extrabold text-slate-900">{{ $notifikasis->count() }}</p>
            <p class="text-[10px] md:text-[11px] font-bold text-slate-400">Total</p>
        </div>
        <div class="bg-white p-3.5 md:p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .2s">
            <p class="text-lg md:text-2xl font-extrabold text-amber-600">{{ $notifikasis->where('is_read', false)->count() }}</p>
            <p class="text-[10px] md:text-[11px] font-bold text-slate-400">Belum Dibaca</p>
        </div>
        <div class="bg-white p-3.5 md:p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .3s">
            <p class="text-lg md:text-2xl font-extrabold text-emerald-600">{{ $notifikasis->where('tipe', 'diterima')->count() }}</p>
            <p class="text-[10px] md:text-[11px] font-bold text-slate-400">Masuk</p>
        </div>
    </div>

    <!-- DAFTAR NOTIFIKASI -->
    <div class="space-y-3 md:space-y-4">
        @forelse($notifikasis as $notif)
            @php
                $typeColor = match($notif->tipe) {
                    'diterima' => 'bg-emerald-50 border-emerald-200',
                    'ditolak' => 'bg-red-50 border-red-200',
                    default => 'bg-sky-50 border-sky-200',
                };
                $typeIcon = match($notif->tipe) {
                    'diterima' => '<svg class="w-4 h-4 md:w-5 md:h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                    'ditolak' => '<svg class="w-4 h-4 md:w-5 md:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
                    default => '<svg class="w-4 h-4 md:w-5 md:h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                };
            @endphp
            <div class="p-3.5 md:p-4 rounded-xl md:rounded-2xl border {{ $typeColor }} {{ $notif->is_read ? 'opacity-70' : '' }} bg-white/70 backdrop-blur shadow-sm animate-fade-up">
                <div class="flex items-start gap-3">
                    <div class="flex items-center justify-center w-9 h-9 md:w-10 md:h-10 rounded-xl bg-white border border-slate-200 shadow-sm shrink-0">
                        {!! $typeIcon !!}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="text-xs md:text-sm font-bold text-slate-900 leading-snug truncate min-w-0">{{ $notif->judul }}</h3>
                            <span class="text-[10px] text-slate-400 shrink-0 whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[11px] md:text-xs text-slate-600 mt-2 leading-relaxed">{{ $notif->pesan }}</p>
                        @if(!$notif->is_read)
                            <div class="mt-2.5 flex justify-end">
                                <form method="POST" action="{{ route('kesiswaan.notifikasi.read', $notif) }}" class="w-full sm:w-auto">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-semibold text-sky-600 hover:text-sky-700 px-3 py-1.5 bg-white border border-sky-200 rounded-lg w-full sm:w-auto text-center shadow-sm">
                                        Tandai dibaca
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 md:p-12 text-center bg-white rounded-2xl border border-sky-100 shadow-sm animate-fade-up">
                <div class="w-12 h-12 md:w-14 md:h-14 mx-auto bg-gradient-to-br from-sky-100 to-blue-100 border border-sky-200 rounded-2xl flex items-center justify-center text-sky-400 mb-3">
                    <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-600">Belum ada notifikasi</p>
                <p class="text-xs text-slate-400 mt-1">Notifikasi pendaftaran, pengajuan keluar, dan aktivitas siswa akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
@endsection