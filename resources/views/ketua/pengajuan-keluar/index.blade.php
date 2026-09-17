@extends('ketua.layout')
@section('title', 'Daftar Pengajuan Keluar')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Daftar Pengajuan Keluar</h1>
            <p class="text-xs text-slate-400 mt-1">Total: {{ $pengajuanKeluars->count() }} pengajuan</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="ketua-card-list bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="card-table ketua-card-pengajuan w-full text-left text-xs md:text-sm">
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Nama</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Tanggal</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Alasan</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Status</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                @forelse($pengajuanKeluars as $pengajuan)
                    <tr class="hover:bg-sky-50/50 transition" data-card-href="{{ route('ketua.pengajuan-keluar.show', $pengajuan) }}">
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $pengajuan->siswa->nama }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap max-w-xs truncate">{{ Str::limit($pengajuan->alasan, 30) }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            @if($pengajuan->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700 border border-amber-200">Pending</span>
                            @elseif($pengajuan->status === 'diterima')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">Diterima</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-100 text-red-700 border border-red-200">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <a href="{{ route('ketua.pengajuan-keluar.show', $pengajuan) }}" class="card-detail-link inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-700 font-semibold rounded-xl hover:from-sky-200 hover:to-blue-200 transition text-[10px] md:text-[11px]">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 md:px-6 py-10 text-center text-slate-400">
                            <p class="text-sm font-medium">Belum ada pengajuan keluar</p>
                            <p class="text-xs mt-1">Pengajuan siswa akan muncul di halaman ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
@endsection