@extends('ketua.layout')
@section('title', 'Daftar Laporan Bulanan')

@section('content')
    <div class="flex justify-between items-center">
        <p class="text-xs text-gray-400">Total: {{ $laporans->count() }} laporan</p>
        <a href="{{ route('ketua.laporan-bulanan.create') }}" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">
            + Laporan Baru
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Bulan</th>
                    <th class="px-6 py-3">Materi</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($laporans as $laporan)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">{{ $laporan->bulan }}</td>
                        <td class="px-6 py-4">{{ $laporan->materi_kegiatan ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($laporan->status === 'draft')
                                <span class="text-gray-500 font-medium">Draft</span>
                            @elseif($laporan->status === 'disetujui')
                                <span class="text-green-600 font-medium">Disetujui</span>
                            @else
                                <span class="text-red-600 font-medium">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('ketua.laporan-bulanan.show', $laporan) }}" class="text-theme-blue hover:underline font-medium">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada laporan bulanan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
