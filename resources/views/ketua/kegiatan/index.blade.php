@extends('ketua.layout')
@section('title', 'Daftar Kegiatan')

@section('content')
    <div class="flex justify-between items-center">
        <p class="text-xs text-gray-400">Total: {{ $kegiatans->count() }} kegiatan</p>
        <a href="{{ route('ketua.kegiatan.create') }}" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">
            + Kegiatan Baru
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Materi</th>
                    <th class="px-6 py-3">Presensi</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kegiatans as $kegiatan)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">{{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">{{ $kegiatan->materi }}</td>
                        <td class="px-6 py-4">{{ $kegiatan->presensis_count }} orang</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="text-theme-blue hover:underline font-medium">Detail</a>
                            <span class="mx-1 text-gray-300">|</span>
                            <a href="{{ route('ketua.presensi.create', $kegiatan) }}" class="text-green-600 hover:underline font-medium">Absensi</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada kegiatan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
