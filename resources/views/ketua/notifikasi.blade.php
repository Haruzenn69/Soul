@extends('ketua.layout')
@section('title', 'Notifikasi')

@section('content')
<div class="space-y-5">
    <a href="{{ route('ketua.dashboard') }}" class="mb-3 inline-flex items-center justify-center gap-2 px-3.5 py-2 bg-white hover:bg-sky-50 text-slate-600 text-xs font-semibold rounded-lg border border-sky-100 shadow-sm transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i>
        Kembali
    </a>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 animate-fade-up">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Notifikasi</h1>
            <p class="text-xs text-slate-400 mt-1">Pemberitahuan terkait ekskul yang anda pimpin</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-3 gap-2 md:gap-4">
        <div class="bg-white p-2.5 sm:p-3.5 md:p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .1s">
            <p class="text-lg md:text-2xl font-extrabold text-slate-900">{{ $notifikasis->count() }}</p>
            <p class="text-[10px] sm:text-xs font-bold text-slate-400">Total</p>
        </div>
        <div class="bg-white p-2.5 sm:p-3.5 md:p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .2s">
            <p class="text-lg md:text-2xl font-extrabold text-amber-600">{{ $notifikasis->where('is_read', false)->count() }}</p>
            <p class="text-[10px] sm:text-xs font-bold text-slate-400">Belum Dibaca</p>
        </div>
        <div class="bg-white p-2.5 sm:p-3.5 md:p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .3s">
            <p class="text-lg md:text-2xl font-extrabold text-emerald-600">{{ $notifikasis->where('tipe', 'diterima')->count() }}</p>
            <p class="text-[10px] sm:text-xs font-bold text-slate-400">Masuk</p>
        </div>
    </div>

    @if($notifikasis->isNotEmpty())
        <div class="grid grid-cols-2 gap-2 md:gap-3">
            <form method="POST" action="{{ route('ketua.notifikasi.read-all') }}" class="min-w-0">
                @csrf
                <button type="submit" class="w-full h-full px-2 sm:px-3.5 py-2.5 bg-white hover:bg-sky-50 text-slate-600 text-[10px] sm:text-xs font-semibold rounded-lg border border-sky-100 shadow-sm transition">Tandai Semua Dibaca</button>
            </form>
            <form method="POST" action="{{ route('ketua.notifikasi.clear-all') }}" class="min-w-0" onsubmit="return confirm('Hapus semua notifikasi? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full h-full px-2 sm:px-3.5 py-2.5 bg-white hover:bg-rose-50 text-rose-600 text-[10px] sm:text-xs font-semibold rounded-lg border border-rose-200 shadow-sm transition">Hapus Semua</button>
            </form>
        </div>
    @endif

    <!-- Notification List -->
    <div class="space-y-3">
        @forelse($notifikasis as $notif)
            @php
                $typeColor = match($notif->tipe) {
                    'diterima' => 'bg-emerald-50 border-emerald-200',
                    'ditolak' => 'bg-red-50 border-red-200',
                    default => 'bg-sky-50 border-sky-200',
                };
                $typeIcon = match($notif->tipe) {
                    'diterima' => '<i class="fa-solid fa-circle-check text-emerald-600"></i>',
                    'ditolak' => '<i class="fa-solid fa-circle-xmark text-rose-600"></i>',
                    default => '<i class="fa-solid fa-bell text-sky-600"></i>',
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
                            <span class="text-xs text-slate-400 shrink-0 whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">{{ $notif->pesan }}</p>
                        <div class="mt-2.5 flex items-center justify-end gap-2 flex-wrap">
                            @if(!$notif->is_read)
                                <form method="POST" action="{{ route('ketua.notifikasi.read', $notif) }}" class="w-full sm:w-auto">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg w-full sm:w-auto text-center shadow-sm transition">Tandai dibaca</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('ketua.notifikasi.destroy', $notif) }}" class="w-full sm:w-auto" onsubmit="return confirm('Hapus notifikasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700 px-3 py-1.5 bg-white border border-rose-200 hover:bg-rose-50 rounded-lg w-full sm:w-auto text-center shadow-sm transition">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-10 text-center bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60">
                <div class="w-14 h-14 mx-auto bg-sky-100 rounded-2xl flex items-center justify-center text-sky-400 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-600">Belum ada notifikasi</p>
                <p class="text-xs text-slate-400 mt-1">Notifikasi pendaftaran, pengajuan keluar, dan laporan akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
@endsection
