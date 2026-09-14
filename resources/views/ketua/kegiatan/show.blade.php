@extends('ketua.layout')
@section('title', 'Detail Kegiatan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Detail Kegiatan</h1>
            <p class="text-xs text-slate-400 mt-1">{{ $kegiatan->materi }}</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('ketua.presensi.create', $kegiatan) }}" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 transition w-full sm:w-auto items-center justify-center gap-2">Input Presensi</a>
            <a href="{{ route('ketua.kegiatan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition w-full sm:w-auto items-center justify-center gap-2">Kembali</a>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 space-y-5">
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal</p>
            <p class="font-medium text-sm">{{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }}</p>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Materi</p>
            <p class="font-medium text-sm">{{ $kegiatan->materi }}</p>
        </div>
        @if($kegiatan->deskripsi)
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Deskripsi</p>
            <p class="font-medium text-sm whitespace-pre-line">{{ $kegiatan->deskripsi }}</p>
        </div>
        @endif
        @if($kegiatan->dokumentasi)
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dokumentasi</p>
            <img src="{{ asset('storage/' . $kegiatan->dokumentasi) }}" alt="Dokumentasi Kegiatan" class="mt-2 max-w-sm rounded-2xl border border-sky-100 shadow-sm">
        </div>
        @endif

        <h3 class="text-sm font-extrabold text-slate-900 pt-2 border-t border-sky-100">Daftar Presensi</h3>
        <div class="overflow-x-auto">
        <table class="card-table w-full text-left text-xs md:text-sm">
            <thead class="bg-sky-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    <th class="px-4 py-3 font-semibold text-slate-500 whitespace-nowrap">Nama</th>
                    <th class="px-4 py-3 font-semibold text-slate-500 whitespace-nowrap">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                @forelse($kegiatan->presensis as $presensi)
                    <tr class="hover:bg-sky-50/50 transition">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $presensi->pendaftaran->siswa->nama ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($presensi->status === 'hadir')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">Hadir</span>
                            @elseif($presensi->status === 'sakit')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700 border border-amber-200">Sakit</span>
                            @elseif($presensi->status === 'izin')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-sky-100 text-sky-700 border border-sky-200">Izin</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-100 text-red-700 border border-red-200">Alpha</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-400">Belum ada presensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
@endsection