@extends('ketua.layout')
@section('title', 'Detail Kegiatan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white">Detail Kegiatan</h1>
            <p class="text-xs text-slate-400 dark:text-neutral-400 mt-1">{{ $kegiatan->materi }}</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('ketua.presensi.create', $kegiatan) }}" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 dark:shadow-none transition w-full sm:w-auto inline-flex items-center justify-center gap-2">Input Presensi</a>
            <a href="{{ route('ketua.kegiatan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-600 dark:text-neutral-300 font-bold text-xs rounded-xl transition w-full sm:w-auto inline-flex items-center justify-center gap-2">Kembali</a>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-white dark:bg-neutral-900 p-5 md:p-6 rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-lg shadow-sky-100/60 dark:shadow-none space-y-5">
        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-neutral-400 mb-1">Tanggal kegiatan</p>
                <p class="font-semibold text-sm text-slate-800 dark:text-neutral-200">{{ $kegiatan->tanggalText() }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-500 dark:text-neutral-400 mb-1">Nama kegiatan</p>
                <p class="font-semibold text-sm text-slate-800 dark:text-neutral-200">
                    {{ $kegiatan->materi }}
                    @if($kegiatan->isEvent())
                        <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">Event</span>
                    @endif
                </p>
            </div>
        </div>
        @if($kegiatan->deskripsi)
        <div>
            <p class="text-xs font-bold text-slate-500 dark:text-neutral-400 mb-1">Deskripsi</p>
            <p class="font-medium text-sm text-slate-700 dark:text-neutral-300 whitespace-pre-line">{{ $kegiatan->deskripsi }}</p>
        </div>
        @endif
        @if($kegiatan->dokumentasi)
        <div>
            <p class="text-xs font-bold text-slate-500 dark:text-neutral-400 mb-1">Dokumentasi</p>
            <img src="{{ asset('storage/' . $kegiatan->dokumentasi) }}" alt="Dokumentasi Kegiatan" class="mt-2 max-w-sm rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm">
        </div>
        @endif

        @php
            $pelatih = $kegiatan->ekskul->pelatih;
        @endphp
        <div class="rounded-2xl border border-amber-200/80 bg-amber-50/50 dark:bg-neutral-800/60 dark:border-neutral-700/80 p-4 flex items-center justify-between gap-3 transition">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-yellow-500 text-amber-950 dark:text-amber-950 flex items-center justify-center text-xs font-extrabold shadow-sm shrink-0">P</span>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-neutral-100">Kehadiran Pelatih</h3>
                    <p class="text-xs text-slate-500 dark:text-neutral-400 mt-0.5">{{ $pelatih?->nama ?? 'Pelatih belum tercatat' }}</p>
                </div>
            </div>
            @if($presensiPelatih)
                @php
                    $pelatihBadge = match ($presensiPelatih->status) {
                        'hadir' => 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60',
                        'sakit' => 'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/60',
                        'izin' => 'bg-sky-100 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-400 dark:border-sky-800/60',
                        default => 'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/60',
                    };
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $pelatihBadge }}">{{ ucfirst($presensiPelatih->status) }}</span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-300 border border-slate-200 dark:border-neutral-700">Belum diabsen</span>
            @endif
        </div>

        @php
            $rekap = [
                'hadir' => $kegiatan->presensis->where('status', 'hadir')->count(),
                'izin' => $kegiatan->presensis->where('status', 'izin')->count(),
                'sakit' => $kegiatan->presensis->where('status', 'sakit')->count(),
                'alpha' => $kegiatan->presensis->where('status', 'alpha')->count(),
            ];
        @endphp
        <div class="flex items-center justify-between gap-3 pt-1">
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-neutral-100">Rekap presensi</h3>
                <p class="text-xs text-slate-500 dark:text-neutral-400 mt-0.5">Ringkasan status kehadiran anggota.</p>
            </div>
            <a href="{{ route('ketua.presensi.create', $kegiatan) }}" class="text-xs font-bold text-sky-600 dark:text-sky-400 hover:text-sky-800 dark:hover:text-sky-300 transition">Perbarui presensi</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 mb-6">
            <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/25 border border-emerald-100 dark:border-emerald-900/50 text-center transition">
                <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $rekap['hadir'] }}</p>
                <p class="text-[10px] text-emerald-700/80 dark:text-emerald-400/80 font-bold uppercase tracking-wider mt-0.5">Hadir</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-sky-50/70 dark:bg-sky-950/25 border border-sky-100 dark:border-sky-900/50 text-center transition">
                <p class="text-xl font-extrabold text-sky-600 dark:text-sky-400">{{ $rekap['izin'] }}</p>
                <p class="text-[10px] text-sky-700/80 dark:text-sky-400/80 font-bold uppercase tracking-wider mt-0.5">Izin</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/25 border border-amber-100 dark:border-amber-900/50 text-center transition">
                <p class="text-xl font-extrabold text-amber-600 dark:text-amber-400">{{ $rekap['sakit'] }}</p>
                <p class="text-[10px] text-amber-700/80 dark:text-amber-400/80 font-bold uppercase tracking-wider mt-0.5">Sakit</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-rose-50/70 dark:bg-rose-950/25 border border-rose-100 dark:border-rose-900/50 text-center transition">
                <p class="text-xl font-extrabold text-rose-600 dark:text-rose-400">{{ $rekap['alpha'] }}</p>
                <p class="text-[10px] text-rose-700/80 dark:text-rose-400/80 font-bold uppercase tracking-wider mt-0.5">Alpha</p>
            </div>
        </div>

        <h3 class="text-sm font-bold text-slate-800 dark:text-neutral-100 mb-3">Daftar presensi</h3>
        @include('partials.table-client-tools', [
            'tableId' => 'presensi-show-table',
            'searchCols' => [1],
            'filterCols' => [2],
            'filterOptions' => ['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha'],
            'defaultSize' => 10,
        ])
        <div class="overflow-x-auto rounded-xl border border-sky-100 dark:border-neutral-800">
        <table id="presensi-show-table" class="card-table w-full text-left text-xs md:text-sm">
            <thead class="bg-sky-50/80 dark:bg-neutral-800/80">
                <tr>
                    <th class="px-4 py-3 font-semibold text-slate-600 dark:text-neutral-400 whitespace-nowrap">No</th>
                    <th data-sort-index="1" class="px-4 py-3 font-semibold text-slate-600 dark:text-neutral-400 whitespace-nowrap cursor-pointer select-none hover:text-slate-800 dark:hover:text-neutral-200 transition" title="Klik untuk urutkan">Nama</th>
                    <th class="px-4 py-3 font-semibold text-slate-600 dark:text-neutral-400 whitespace-nowrap">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50 dark:divide-neutral-800/80">
                @forelse($kegiatan->presensis as $presensi)
                    <tr class="hover:bg-sky-50/50 dark:hover:bg-neutral-800/50 transition">
                        <td class="px-4 py-3 whitespace-nowrap text-slate-700 dark:text-neutral-300">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 whitespace-nowrap font-medium text-slate-800 dark:text-neutral-200">{{ $presensi->pendaftaran->siswa->nama ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($presensi->status === 'hadir')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60">Hadir</span>
                            @elseif($presensi->status === 'sakit')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">Sakit</span>
                            @elseif($presensi->status === 'izin')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-100 dark:bg-sky-950/40 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60">Izin</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60">Alpha</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-slate-400 dark:text-neutral-500">Belum ada presensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div class="mt-5 pt-5 border-t border-sky-100 dark:border-neutral-800 flex flex-col sm:flex-row gap-2">
            <a href="{{ route('ketua.kegiatan.edit', $kegiatan) }}" class="px-5 py-2.5 bg-amber-100 hover:bg-amber-200 dark:bg-amber-950/40 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-400 text-xs font-bold rounded-xl transition text-center border border-transparent dark:border-amber-800/50">Edit kegiatan</a>
            <form action="{{ route('ketua.kegiatan.destroy', $kegiatan) }}" method="POST" onsubmit="return confirm('{{ $kegiatan->presensis->count() > 0 ? "Kegiatan ini sudah diisi presensi (".$kegiatan->presensis->count()." orang). Menghapus kegiatan juga akan menghapus data presensinya! Yakin hapus?" : "Yakin hapus kegiatan ini?" }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-5 py-2.5 bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/40 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-400 text-xs font-bold rounded-xl transition border border-transparent dark:border-rose-800/50">Hapus kegiatan</button>
            </form>
            <a href="{{ route('ketua.kegiatan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-600 dark:text-neutral-300 text-xs font-bold rounded-xl transition text-center">Kembali</a>
        </div>
    </div>
</div>
@endsection
