@extends('ketua.layout')
@section('title', 'Detail Kegiatan')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <div class="mb-4">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Tanggal</p>
            <p class="font-medium text-sm">{{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }}</p>
        </div>
        <div class="mb-6">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Materi</p>
            <p class="font-medium text-sm">{{ $kegiatan->materi }}</p>
        </div>
        @if($kegiatan->deskripsi)
        <div class="mb-6">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Deskripsi</p>
            <p class="font-medium text-sm whitespace-pre-line">{{ $kegiatan->deskripsi }}</p>
        </div>
        @endif
        @if($kegiatan->dokumentasi)
        <div class="mb-6">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Dokumentasi</p>
            <img src="{{ asset('storage/' . $kegiatan->dokumentasi) }}" alt="Dokumentasi Kegiatan" class="mt-2 max-w-sm rounded-2xl border border-gray-100 shadow-sm">
        </div>
        @endif

        <h3 class="text-xs font-bold text-gray-400 uppercase mb-3">Daftar Presensi</h3>
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-2">No</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($kegiatan->presensis as $presensi)
                    <tr>
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $presensi->pendaftaran->siswa->nama ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($presensi->status === 'hadir')
                                <span class="text-green-600 font-medium">Hadir</span>
                            @elseif($presensi->status === 'sakit')
                                <span class="text-yellow-600 font-medium">Sakit</span>
                            @elseif($presensi->status === 'izin')
                                <span class="text-blue-600 font-medium">Izin</span>
                            @else
                                <span class="text-red-600 font-medium">Alpha</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-400">Belum ada presensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4 flex gap-2">
            <a href="{{ route('ketua.presensi.create', $kegiatan) }}" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Input Presensi</a>
            <a href="{{ route('ketua.kegiatan.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Kembali</a>
        </div>
    </div>
@endsection
