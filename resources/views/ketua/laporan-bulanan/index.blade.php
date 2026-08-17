@extends('layouts.sidebar-ketua')
@section('title', 'Laporan Bulanan')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Laporan Bulanan</h1>
        <a href="{{ route('ketua.laporan-bulanan.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Buat Laporan
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Bulan</th>
                    <th class="px-4 py-2 text-left">Materi</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $laporan)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $laporan->bulan }}</td>
                        <td class="px-4 py-2">{{ $laporan->materi_kegiatan ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @if($laporan->status === 'draft')
                                <span class="text-gray-600">Draft</span>
                            @elseif($laporan->status === 'disetujui')
                                <span class="text-green-600">Disetujui</span>
                            @else
                                <span class="text-red-600">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('ketua.laporan-bulanan.show', $laporan) }}" class="text-blue-600 hover:underline">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-400">Belum ada laporan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
