@extends('ketua.layout')
@section('title', 'Rekap Absensi - ' . $ekskul->nama_ekskul)

@php
    $statusList = ['hadir', 'sakit', 'izin', 'alpha'];
    $statusBadge = [
        'hadir' => ['bg-emerald-100 text-emerald-700', 'H', 'Hadir'],
        'sakit' => ['bg-violet-100 text-violet-700', 'S', 'Sakit'],
        'izin'  => ['bg-amber-100 text-amber-700', 'I', 'Izin'],
        'alpha' => ['bg-rose-100 text-rose-700', 'A', 'Alpha'],
        null    => ['bg-slate-100 text-slate-400', '–', 'Belum diabsen'],
    ];
    $statusCol = [
        'hadir' => '#10B981',
        'sakit' => '#8B5CF6',
        'izin'  => '#F59E0B',
        'alpha' => '#EF4444',
    ];
@endphp

@section('content')
    <div class="space-y-6">
        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Rekap Absensi</h1>
                <p class="text-xs text-slate-400 mt-1">Rekapitulasi kehadiran anggota {{ $ekskul->nama_ekskul }} untuk {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('ketua.presensi.rekap') }}" class="flex items-center gap-2">
                    <label for="bulan" class="text-xs font-semibold text-slate-500">Bulan {{ $tahun }}</label>
                    <select id="bulan" name="bulan" required onchange="this.form.submit()"
                        class="rounded-xl border-slate-200 text-xs focus:border-sky-400 focus:ring-sky-400">
                        @foreach ($bulanOptions as $bulanOption)
                            <option value="{{ $bulanOption }}"
                                @selected($bulan === $bulanOption)
                                @disabled($bulanOption > $bulanMaksimal)
                                class="{{ $bulanOption > $bulanMaksimal ? 'text-gray-400' : '' }}">
                                {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $bulanOption)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('ketua.kegiatan.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Kegiatan</a>
                <a href="{{ route('ketua.dashboard') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Dashboard</a>
            </div>
        </div>

        {{-- LEGEND --}}
        <div class="flex items-center gap-4 flex-wrap bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 px-5 py-4 animate-fade-up">
            <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Keterangan</span>
            @foreach ($statusList as $status)
                @php [$bg, $letter, $label] = $statusBadge[$status]; @endphp
                <span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600">
                    <span class="w-5 h-5 rounded-md {{ $bg }} flex items-center justify-center text-[10px] font-extrabold">{{ $letter }}</span>
                    {{ $label }}
                </span>
            @endforeach
            <span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600">
                <span class="w-5 h-5 rounded-md bg-slate-100 text-slate-400 flex items-center justify-center text-[10px] font-extrabold">–</span>
                Belum diabsen
            </span>
        </div>

        {{-- RECAP CARD --}}
        <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .1s">
            <div class="px-5 md:px-6 py-4 flex justify-between items-center border-b border-sky-50">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Matriks Kehadiran</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $anggotas->count() }} anggota · {{ $kegiatans->count() }} kegiatan</p>
                </div>
                <span class="text-[10px] font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ $ekskul->nama_ekskul }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="card-table w-full text-left text-xs md:text-sm min-w-max">
                    <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                        <tr>
                            <th class="px-4 md:px-5 py-3 font-semibold text-slate-500 sticky left-0 bg-gradient-to-r from-sky-50 to-blue-50 z-10" rowspan="2">No</th>
                            <th class="px-4 md:px-5 py-3 font-semibold text-slate-500 sticky left-10 bg-gradient-to-r from-sky-50 to-blue-50 z-10" rowspan="2">Nama</th>
                            <th colspan="{{ max($kegiatans->count(), 1) }}" class="px-3 py-3 font-semibold text-slate-500 text-center">Pertemuan (tanggal & materi)</th>
                            <th colspan="5" class="px-3 py-3 font-semibold text-slate-500 text-center">Total</th>
                        </tr>
                        <tr>
                            @forelse ($kegiatans as $kegiatan)
                                <th class="px-2.5 py-2.5 font-bold text-slate-600 text-center whitespace-nowrap" title="{{ $kegiatan->materi }}">
                                    {{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('d M') : 'Keg. #' . $kegiatan->id }}
                                </th>
                            @empty
                                <th class="px-3 py-2.5 font-semibold text-slate-400 text-center">Belum ada kegiatan</th>
                            @endforelse
                            <th class="px-3 py-2.5 font-bold text-emerald-600 text-center">Hadir</th>
                            <th class="px-3 py-2.5 font-bold text-amber-600 text-center">Izin</th>
                            <th class="px-3 py-2.5 font-bold text-violet-600 text-center">Sakit</th>
                            <th class="px-3 py-2.5 font-bold text-rose-600 text-center">Alpha</th>
                            <th class="px-3 py-2.5 font-bold text-slate-600 text-center">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-50">
                        @forelse ($anggotas as $anggota)
                            @php
                                $byKegiatan = $anggota->presensis->keyBy('kegiatan_id');
                                $totalHadir = $anggota->presensis->where('status', 'hadir')->count();
                                $totalIzin = $anggota->presensis->where('status', 'izin')->count();
                                $totalSakit = $anggota->presensis->where('status', 'sakit')->count();
                                $totalAlpha = $anggota->presensis->where('status', 'alpha')->count();
                                $terisi = $anggota->presensis->count();
                                $persen = $terisi > 0 ? round((($totalHadir + $totalSakit + $totalIzin) / $terisi) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-sky-50/50 transition">
                                <td class="px-4 md:px-5 py-3 whitespace-nowrap sticky left-0 bg-white z-10">{{ $loop->iteration }}</td>
                                <td class="px-4 md:px-5 py-3 whitespace-nowrap sticky left-10 bg-white z-10">
                                    <span class="font-bold text-slate-800">{{ $anggota->siswa->nama }}</span>
                                    <span class="block text-[10px] text-slate-400 mt-0.5">{{ $anggota->siswa->kelas?->nama ?? 'Tanpa kelas' }}</span>
                                </td>
                                @forelse ($kegiatans as $kegiatan)
                                    @php
                                        $presensi = $byKegiatan->get($kegiatan->id);
                                        [$bg, $letter] = $statusBadge[$presensi?->status];
                                    @endphp
                                    <td class="px-2.5 py-3 text-center" title="{{ $kegiatan->materi }} · {{ $statusBadge[$presensi?->status][2] }}">
                                        <span class="w-6 h-6 inline-flex items-center justify-center rounded-md {{ $bg }} text-[10px] font-extrabold">{{ $letter }}</span>
                                    </td>
                                @empty
                                    <td class="px-3 py-3 text-center text-slate-300">–</td>
                                @endforelse
                                <td class="px-3 py-3 text-center font-bold text-emerald-600">{{ $totalHadir }}</td>
                                <td class="px-3 py-3 text-center font-bold text-amber-600">{{ $totalIzin }}</td>
                                <td class="px-3 py-3 text-center font-bold text-violet-600">{{ $totalSakit }}</td>
                                <td class="px-3 py-3 text-center font-bold text-rose-600">{{ $totalAlpha }}</td>
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="font-bold text-slate-700">{{ $persen }}%</span>
                                        <span class="w-10 h-1.5 rounded-full bg-slate-100 overflow-hidden inline-block">
                                            <span class="block h-full rounded-full {{ $persen >= 75 ? 'bg-emerald-400' : ($persen >= 50 ? 'bg-amber-400' : 'bg-rose-400') }}" style="width: {{ $persen }}%"></span>
                                        </span>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $kegiatans->count() + 6 }}" class="px-4 py-12 text-center text-slate-400">
                                    Belum ada anggota aktif untuk direkap.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
