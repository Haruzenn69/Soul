@extends('layouts.sidebar-ketua')
@section('title', 'Input Presensi')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Input Presensi - {{ $kegiatan->materi }}</h1>

    <form action="{{ route('ketua.presensi.store', $kegiatan) }}" method="POST">
        @csrf
        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">NIS</th>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-center">Hadir</th>
                        <th class="px-4 py-2 text-center">Sakit</th>
                        <th class="px-4 py-2 text-center">Izin</th>
                        <th class="px-4 py-2 text-center">Alpha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendaftarans as $i => $pendaftaran)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $pendaftaran->siswa->nis }}</td>
                            <td class="px-4 py-2">{{ $pendaftaran->siswa->nama }}</td>
                            <td class="px-4 py-2 text-center">
                                <input type="radio" name="presensi[{{ $i }}][status]" value="hadir" checked>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <input type="radio" name="presensi[{{ $i }}][status]" value="sakit">
                            </td>
                            <td class="px-4 py-2 text-center">
                                <input type="radio" name="presensi[{{ $i }}][status]" value="izin">
                            </td>
                            <td class="px-4 py-2 text-center">
                                <input type="radio" name="presensi[{{ $i }}][status]" value="alpha">
                            </td>
                            <input type="hidden" name="presensi[{{ $i }}][pendaftaran_id]" value="{{ $pendaftaran->id }}">
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Presensi</button>
            <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
        </div>
    </form>
@endsection
