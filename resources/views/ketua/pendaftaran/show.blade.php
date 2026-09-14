@extends('ketua.layout')
@section('title', 'Detail Pendaftaran')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Detail Pendaftaran</h1>
            <p class="text-xs text-slate-400 mt-1">{{ $pendaftaran->siswa->nama }}</p>
        </div>
        <a href="{{ route('ketua.pendaftaran.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition w-full sm:w-auto items-center justify-center gap-2">Kembali</a>
    </div>

    <!-- Detail Card -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-lg space-y-5">
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal Daftar</p>
            <p class="font-medium text-sm">{{ $pendaftaran->tanggal_daftar->format('d/m/Y') }}</p>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">NIS</p>
            <p class="font-medium text-sm">{{ $pendaftaran->siswa->nis }}</p>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama</p>
            <p class="font-medium text-sm">{{ $pendaftaran->siswa->nama }}</p>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kelas</p>
            <p class="font-medium text-sm">{{ $pendaftaran->siswa->kelas->nama ?? '-' }}</p>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Alasan Bergabung</p>
            <p class="font-medium text-sm text-slate-700 bg-sky-50/50 p-3 rounded-2xl border border-sky-100 mt-1">{{ $pendaftaran->alasan ?? 'Tidak mencantumkan alasan.' }}</p>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status</p>
            @if($pendaftaran->status === 'pending')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700 border border-amber-200">Pending</span>
            @elseif($pendaftaran->status === 'diterima')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">Diterima</span>
            @elseif($pendaftaran->status === 'ditolak')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-100 text-red-700 border border-red-200">Ditolak</span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">{{ ucfirst($pendaftaran->status) }}</span>
            @endif
        </div>

        @if($pendaftaran->status === 'pending')
        <div class="pt-4 border-t border-sky-100 flex gap-2 flex-wrap">
            <form action="{{ route('ketua.pendaftaran.update', $pendaftaran) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="diterima">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-emerald-400 to-emerald-500 hover:from-emerald-500 hover:to-emerald-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-200 transition w-full sm:w-auto">Terima</button>
            </form>
            <form action="{{ route('ketua.pendaftaran.update', $pendaftaran) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="ditolak">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-red-400 to-red-500 hover:from-red-500 hover:to-red-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-red-200 transition w-full sm:w-auto">Tolak</button>
            </form>
        </div>
        @endif

        <div class="pt-4 border-t border-sky-100">
            <a href="{{ route('ketua.pendaftaran.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition w-full sm:w-auto text-center">Kembali</a>
        </div>
    </div>
@endsection