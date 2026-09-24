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
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tanggal kegiatan</p>
                <p class="font-semibold text-sm text-slate-800">{{ $kegiatan->tanggal_kegiatan->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama kegiatan</p>
                <p class="font-semibold text-sm text-slate-800">{{ $kegiatan->materi }}</p>
            </div>
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

        @php
            $rekap = [
                'hadir' => $kegiatan->presensis->where('status', 'hadir')->count(),
                'izin' => $kegiatan->presensis->where('status', 'izin')->count(),
                'sakit' => $kegiatan->presensis->where('status', 'sakit')->count(),
                'alpha' => $kegiatan->presensis->where('status', 'alpha')->count(),
            ];
        @endphp
        <div class="flex items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Rekap presensi</h3>
                <p class="text-[10px] text-slate-400 mt-1">Ringkasan status kehadiran anggota.</p>
            </div>
            <a href="{{ route('ketua.presensi.create', $kegiatan) }}" class="text-[11px] font-bold text-sky-600 hover:text-sky-800 transition">Perbarui presensi</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-6">
            <div class="p-3 rounded-2xl bg-green-50 border border-green-100 text-center">
                <p class="text-lg font-bold text-green-600">{{ $rekap['hadir'] }}</p>
                <p class="text-[10px] text-green-600/70 font-semibold uppercase">Hadir</p>
            </div>
            <div class="p-3 rounded-2xl bg-blue-50 border border-blue-100 text-center">
                <p class="text-lg font-bold text-blue-600">{{ $rekap['izin'] }}</p>
                <p class="text-[10px] text-blue-600/70 font-semibold uppercase">Izin</p>
            </div>
            <div class="p-3 rounded-2xl bg-yellow-50 border border-yellow-100 text-center">
                <p class="text-lg font-bold text-yellow-600">{{ $rekap['sakit'] }}</p>
                <p class="text-[10px] text-yellow-600/70 font-semibold uppercase">Sakit</p>
            </div>
            <div class="p-3 rounded-2xl bg-red-50 border border-red-100 text-center">
                <p class="text-lg font-bold text-red-600">{{ $rekap['alpha'] }}</p>
                <p class="text-[10px] text-red-600/70 font-semibold uppercase">Alpha</p>
            </div>
        </div>

        <h3 class="text-sm font-bold text-slate-800 mb-3">Daftar presensi</h3>
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
        <div class="mt-5 pt-5 border-t border-sky-100 flex flex-col sm:flex-row gap-2">
            <a href="{{ route('ketua.kegiatan.edit', $kegiatan) }}" class="px-5 py-2.5 bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs font-bold rounded-xl transition text-center">Edit kegiatan</a>
            <form action="{{ route('ketua.kegiatan.destroy', $kegiatan) }}" method="POST" onsubmit="return confirm('{{ $kegiatan->presensis->count() > 0 ? "Kegiatan ini sudah diisi presensi (".$kegiatan->presensis->count()." orang). Menghapus kegiatan juga akan menghapus data presensinya! Yakin hapus?" : "Yakin hapus kegiatan ini?" }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-5 py-2.5 bg-rose-100 hover:bg-rose-200 text-rose-700 text-xs font-bold rounded-xl transition">Hapus kegiatan</button>
            </form>
            <a href="{{ route('ketua.kegiatan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition text-center">Kembali</a>
        </div>
    </div>
@endsection