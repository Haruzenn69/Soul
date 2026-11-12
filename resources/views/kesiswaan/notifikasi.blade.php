@extends('layouts.kesiswaan')
@section('title', 'Notifikasi')

@section('content')
    <a href="{{ route('kesiswaan.dashboard') }}" class="mb-3 inline-flex items-center justify-center gap-2 px-3.5 py-2 bg-white hover:bg-sky-50 text-slate-600 text-xs font-semibold rounded-lg border border-sky-100 shadow-sm transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i>
        Kembali
    </a>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 animate-fade-up">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Notifikasi</h1>
            <p class="text-xs text-slate-400 mt-0.5">Pemberitahuan terkait data kesiswaan</p>
        </div>
    </div>

    <!-- RINGKASAN STATISTIK -->
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
            <form method="POST" action="{{ route('kesiswaan.notifikasi.read-all') }}" class="min-w-0">
                @csrf
                <button type="submit" class="w-full h-full px-2 sm:px-3.5 py-2.5 bg-white hover:bg-sky-50 text-slate-600 text-[10px] sm:text-xs font-semibold rounded-lg border border-sky-100 shadow-sm transition">
                    Tandai Semua Dibaca
                </button>
            </form>
            <form method="POST" action="{{ route('kesiswaan.notifikasi.clear-all') }}" class="min-w-0" onsubmit="return confirm('Hapus semua notifikasi? Tindakan ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full h-full px-2 sm:px-3.5 py-2.5 bg-white hover:bg-rose-50 text-rose-600 text-[10px] sm:text-xs font-semibold rounded-lg border border-rose-200 shadow-sm transition">
                    Hapus Semua
                </button>
            </form>
        </div>
    @endif

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
                            @if($notif->siswa_id)
                                <a href="{{ route('kesiswaan.siswa.riwayat-profil', $notif->siswa_id) }}" class="text-xs font-bold text-sky-700 hover:text-sky-800 px-3 py-1.5 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-lg shadow-sm transition inline-flex items-center gap-1">
                                    
                                    Lihat Riwayat Profil
                                </a>
                            @elseif($notif->pembina_id)
                                <a href="{{ route('kesiswaan.pembina.riwayat-profil', $notif->pembina_id) }}" class="text-xs font-bold text-sky-700 hover:text-sky-800 px-3 py-1.5 bg-sky-50 hover:bg-sky-100 border border-sky-200 rounded-lg shadow-sm transition inline-flex items-center gap-1">
                                    
                                    Lihat Riwayat Profil
                                </a>
                            @endif

                            @if(!$notif->is_read)
                                <form method="POST" action="{{ route('kesiswaan.notifikasi.read', $notif) }}" class="w-full sm:w-auto">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-slate-600 hover:text-slate-800 px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg w-full sm:w-auto text-center shadow-sm transition">
                                        Tandai dibaca
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('kesiswaan.notifikasi.destroy', $notif) }}" class="w-full sm:w-auto" onsubmit="return confirm('Hapus notifikasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700 px-3 py-1.5 bg-white border border-rose-200 hover:bg-rose-50 rounded-lg w-full sm:w-auto text-center shadow-sm transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 md:p-12 text-center bg-white rounded-2xl border border-sky-100 shadow-sm animate-fade-up">
                <div class="w-12 h-12 md:w-14 md:h-14 mx-auto bg-gradient-to-br from-sky-100 to-blue-100 border border-sky-200 rounded-2xl flex items-center justify-center text-sky-400 mb-3">
                    
                </div>
                <p class="text-sm font-medium text-slate-600">Belum ada notifikasi</p>
                <p class="text-xs text-slate-400 mt-1">Notifikasi pendaftaran, pengajuan keluar, dan aktivitas siswa akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
@endsection
