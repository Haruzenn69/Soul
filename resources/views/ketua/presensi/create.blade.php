@extends('ketua.layout')
@section('title', 'Input Presensi - ' . $kegiatan->materi)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Input Presensi</h1>
            <p class="text-xs text-slate-400 mt-1">{{ $kegiatan->materi }} - {{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition w-full sm:w-auto items-center justify-center gap-2">Kembali</a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <form action="{{ route('ketua.presensi.store', $kegiatan) }}" method="POST">
            @csrf
            <div class="overflow-x-auto">
            <table class="card-table w-full text-left text-xs md:text-sm">
                <thead class="bg-sky-50">
                    <tr>
                        <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                        <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Nama</th>
                        <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @foreach($anggotas as $anggota)
                        <tr class="hover:bg-sky-50/50 transition">
                            <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $anggota->siswa->nama }}</td>
                            <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                                <select name="presensi[{{ $loop->index }}][status]"
                                    class="px-3 py-1.5 rounded-full bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition w-full sm:w-auto">
                                    @php 
                                        $current = 'hadir';
                                        if (in_array($anggota->id, $presensiExisting ?? [])) {
                                            $existing = \App\Models\Presensi::where('kegiatan_id', $kegiatan->id)->where('pendaftaran_id', $anggota->id)->first();
                                            if ($existing) $current = $existing->status;
                                        }
                                    @endphp
                                    <option value="hadir" {{ $current === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="sakit" {{ $current === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="izin" {{ $current === 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="alpha" {{ $current === 'alpha' ? 'selected' : '' }}>Alpha</option>
                                </select>
                                <input type="hidden" name="presensi[{{ $loop->index }}][pendaftaran_id]" value="{{ $anggota->id }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            <div class="p-4 md:p-6 border-t border-sky-100 flex gap-2 flex-wrap">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 transition w-full sm:w-auto">Simpan Presensi</button>
                <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition w-full sm:w-auto text-center">Batal</a>
            </div>
        </form>
    </div>
@endsection