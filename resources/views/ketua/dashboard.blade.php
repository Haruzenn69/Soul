@extends('ketua.layout')
@section('title', 'Dashboard')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-theme-dark">
                Selamat datang, {{ auth()->user()->siswa->nama ?? 'Ketua' }}! 👋
            </h1>
            <p class="text-xs text-gray-400 mt-1">Kelola ekstrakurikuler {{ $ekskul->nama_ekskul ?? '-' }} Anda.</p>
        </div>
        <a href="{{ route('ketua.kegiatan.create') }}" class="px-6 py-3 bg-theme-yellow hover:bg-yellow-400 text-theme-dark font-bold text-xs rounded-full shadow-md transition flex items-center gap-2">
            <span>+</span> Buat Kegiatan Baru
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Total Anggota</p>
                <h3 class="text-3xl font-extrabold text-theme-dark mt-2">{{ $totalAnggota }}</h3>
                <p class="text-[11px] font-medium text-theme-blue mt-2">Anggota Aktif</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-theme-blue flex items-center justify-center text-xl">👥</div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Pendaftaran Pending</p>
                <h3 class="text-3xl font-extrabold text-theme-dark mt-2">{{ $pendingCount }}</h3>
                <p class="text-[11px] font-medium text-amber-500 mt-2">Perlu Ditinjau</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">📝</div>
        </div>

        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 tracking-wider uppercase">Pengajuan Keluar</p>
                <h3 class="text-3xl font-extrabold text-theme-dark mt-2">{{ $pengajuanCount }}</h3>
                <p class="text-[11px] font-medium text-red-500 mt-2">Menunggu Keputusan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-xl">🚪</div>
        </div>
    </div>
@endsection
