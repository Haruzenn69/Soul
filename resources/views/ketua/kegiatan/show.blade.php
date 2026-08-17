@extends('layouts.sidebar-ketua')
@section('title', 'Detail Kegiatan')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Detail Kegiatan</h1>

    <div class="bg-white p-6 rounded shadow mb-4">
        <p><strong>Materi:</strong> {{ $kegiatan->materi }}</p>
        <p><strong>Tanggal:</strong> {{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }}</p>
        <p><strong>Total Hadir:</strong> {{ $kegiatan->presensis->where('status', 'hadir')->count() }} orang</p>
    </div>

    <a href="{{ route('ketua.presensi.create', $kegiatan) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4 inline-block">
        Input Presensi
    </a>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">NIS</th>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatan->presensis as $presensi)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $presensi->pendaftaran->siswa->nis }}</td>
                        <td class="px-4 py-2">{{ $presensi->pendaftaran->siswa->nama }}</td>
                        <td class="px-4 py-2">
                            @if($presensi->status === 'hadir')
                                <span class="text-green-600">Hadir</span>
                            @elseif($presensi->status === 'sakit')
                                <span class="text-yellow-600">Sakit</span>
                            @elseif($presensi->status === 'izin')
                                <span class="text-blue-600">Izin</span>
                            @else
                                <span class="text-red-600">Alpha</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-400">Belum ada presensi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
