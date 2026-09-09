@extends('ketua.layout')
@section('title', 'Detail Kegiatan')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <div class="mb-4">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Tanggal</p>
            <p class="font-medium text-sm">{{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }}</p>
        </div>
        <div class="mb-6">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Kegiatan</p>
            <p class="font-medium text-sm">{{ $kegiatan->kegiatan }}</p>
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

        @php
            $rekap = [
                'hadir' => $kegiatan->presensis->where('status', 'hadir')->count(),
                'izin' => $kegiatan->presensis->where('status', 'izin')->count(),
                'sakit' => $kegiatan->presensis->where('status', 'sakit')->count(),
                'alpha' => $kegiatan->presensis->where('status', 'alpha')->count(),
            ];
        @endphp
        <h3 class="text-xs font-bold text-gray-400 uppercase mb-2">Rekap Presensi</h3>
        <div class="grid grid-cols-4 gap-2 mb-6">
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
            <a href="{{ route('ketua.kegiatan.edit', $kegiatan) }}" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-full transition">Edit</a>
            <form action="{{ route('ketua.kegiatan.destroy', $kegiatan) }}" method="POST" onsubmit="return confirm('Yakin hapus kegiatan ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-full transition">Hapus</button>
            </form>
            <a href="{{ route('ketua.kegiatan.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Kembali</a>
        </div>
    </div>
@endsection
