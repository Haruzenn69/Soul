@extends('layouts.sidebar-ketua')
@section('title', 'Kegiatan')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Kegiatan</h1>
        <a href="{{ route('ketua.kegiatan.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Tambah Kegiatan
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-left">Materi</th>
                    <th class="px-4 py-2 text-left">Hadir</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatans as $kegiatan)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $kegiatan->materi }}</td>
                        <td class="px-4 py-2">{{ $kegiatan->presensis->where('status', 'hadir')->count() }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="text-blue-600 hover:underline">Detail</a>
                            <a href="{{ route('ketua.presensi.create', $kegiatan) }}" class="ml-2 text-green-600 hover:underline">Absen</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-400">Belum ada kegiatan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
