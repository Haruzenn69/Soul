@extends('ketua.layout')
@section('title', 'Input Presensi - ' . $kegiatan->materi)

@section('content')
    <p class="text-xs text-gray-400 mb-4">{{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }}</p>

    <form action="{{ route('ketua.presensi.store', $kegiatan) }}" method="POST">
        @csrf
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($anggotas as $anggota)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">{{ $anggota->siswa->nama }}</td>
                            <td class="px-6 py-4">
                                <select name="presensi[{{ $loop->index }}][pendaftaran_id]" class="hidden">
                                    <option value="{{ $anggota->id }}"></option>
                                </select>
                                <select name="presensi[{{ $loop->index }}][status]"
                                    class="px-3 py-1.5 rounded-full bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue">
                                    @php $current = in_array($anggota->id, $presensiExisting) ? \App\Models\Presensi::where('kegiatan_id', $kegiatan->id)->where('pendaftaran_id', $anggota->id)->first()->status : 'hadir'; @endphp
                                    <option value="hadir" {{ $current === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="sakit" {{ $current === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="izin" {{ $current === 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="alpha" {{ $current === 'alpha' ? 'selected' : '' }}>Alpha</option>
                                </select>
                            </td>
                        </tr>
                        <input type="hidden" name="presensi[{{ $loop->index }}][pendaftaran_id]" value="{{ $anggota->id }}">
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex gap-2">
            <button type="submit" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Simpan Presensi</button>
            <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Batal</a>
        </div>
    </form>
@endsection
