@extends('ketua.layout')
@section('title', 'Input Presensi - ' . $kegiatan->materi)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Input Presensi</h1>
            <p class="text-xs text-slate-400 mt-1">{{ $kegiatan->materi }} · {{ $kegiatan->tanggal_kegiatan->isoFormat('dddd, D MMMM Y') }}</p>
        </div>
        <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition w-full sm:w-auto items-center justify-center gap-2">Kembali</a>
    </div>

    <!-- Form Card -->
    <div class="ketua-card-list bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <form action="{{ route('ketua.presensi.store', $kegiatan) }}" method="POST">
            @csrf
            <div class="px-5 md:px-6 py-4 bg-amber-50/60 border-b border-amber-100">
                <p class="text-xs font-bold text-slate-700">Kehadiran Pelatih</p>
                <p class="text-[10px] text-slate-400 mt-1">Tandai status kehadiran pelatih ekskul pada kegiatan ini.</p>
                @if($pelatih)
                    <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-3">
                        <span class="inline-flex items-center gap-2 text-xs font-bold text-slate-800">
                            <span class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center text-[10px] font-extrabold">P</span>
                            {{ $pelatih->nama }}
                        </span>
                        <select name="pelatih_presensi[status]"
                            class="px-3 py-1.5 rounded-full bg-white border border-amber-200 text-xs focus:outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition w-full sm:w-auto">
                            @php $pelatihCurrent = $pelatihStatus ?? 'hadir'; @endphp
                            <option value="hadir" {{ $pelatihCurrent === 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="sakit" {{ $pelatihCurrent === 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="izin" {{ $pelatihCurrent === 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="alpha" {{ $pelatihCurrent === 'alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                        <input type="hidden" name="pelatih_presensi[pelatih_id]" value="{{ $pelatih->id }}">
                    </div>
                @else
                    <p class="text-[10px] text-slate-400 mt-1">Ekskul ini belum memiliki pelatih yang tercatat.</p>
                @endif
            </div>
            <div class="px-5 md:px-6 py-4 bg-sky-50/60 border-b border-sky-100">
                <p class="text-xs font-bold text-slate-700">Tandai kehadiran anggota</p>
                <p class="text-[10px] text-slate-400 mt-1">Periksa status setiap anggota, lalu simpan setelah semua data sesuai.</p>
            </div>
            @include('partials.table-client-tools', [
                'tableId' => 'presensi-table',
                'searchCols' => [1],
                'filterCols' => [2],
                'filterOptions' => ['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha'],
                'defaultSize' => 10,
            ])
            <div class="overflow-x-auto">
            <table id="presensi-table" class="card-table w-full text-left text-xs md:text-sm">
                <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                    <tr>
                        <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                        <th data-sort-index="1" class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap cursor-pointer select-none hover:text-slate-800 transition" title="Klik untuk urutkan">Nama</th>
                        <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @forelse($anggotas as $anggota)
                        <tr class="hover:bg-sky-50/50 transition">
                            <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $anggota->siswa->nama }}</td>
                            <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                                <select name="presensi[{{ $loop->index }}][status]"
                                    class="px-3 py-1.5 rounded-full bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition w-full sm:w-auto">
                                    @php
                                        $current = 'hadir';
                                        if (isset($presensiExisting[$anggota->id])) {
                                            $current = $presensiExisting[$anggota->id];
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
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-10 text-center text-slate-400">Belum ada anggota aktif untuk diabsen.</td>
                        </tr>
                    @endforelse
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