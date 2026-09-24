@php
    $statusList = ['hadir', 'sakit', 'izin', 'alpha'];
    $statusBadge = [
        'hadir' => ['bg-emerald-100 text-emerald-700', 'H', 'Hadir'],
        'sakit' => ['bg-violet-100 text-violet-700', 'S', 'Sakit'],
        'izin'  => ['bg-amber-100 text-amber-700', 'I', 'Izin'],
        'alpha' => ['bg-rose-100 text-rose-700', 'A', 'Alpha'],
        null    => ['bg-slate-100 text-slate-400', '–', 'Belum diabsen'],
    ];
    $matriksTitle = $matriksTitle ?? 'Matriks Kehadiran';
    $matriksSubtitle = $matriksSubtitle ?? ($rows->count().' anggota · '.$kegiatans->count().' kegiatan');
@endphp

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

{{-- MATRIKS CARD --}}
<div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .1s">
    <div class="px-5 md:px-6 py-4 flex justify-between items-center border-b border-sky-50">
        <div>
            <h2 class="text-sm font-extrabold text-slate-900">{{ $matriksTitle }}</h2>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ $matriksSubtitle }}</p>
        </div>
        @if(isset($ekskul) && $ekskul)
            <span class="text-[10px] font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ $ekskul->nama_ekskul }}</span>
        @endif
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
                            {{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('d M') : 'Keg. #'.$kegiatan->id }}
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
                @forelse ($rows as $row)
                    @php
                        $anggota = $row->pendaftaran->siswa;
                        $persen = (float) $row->persentaseKehadiran;
                    @endphp
                    <tr class="hover:bg-sky-50/50 transition">
                        <td class="px-4 md:px-5 py-3 whitespace-nowrap sticky left-0 bg-white z-10">{{ $loop->iteration }}</td>
                        <td class="px-4 md:px-5 py-3 whitespace-nowrap sticky left-10 bg-white z-10">
                            <span class="font-bold text-slate-800">{{ $anggota->nama }}</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ $anggota->kelas?->nama ?? 'Tanpa kelas' }}</span>
                        </td>
                        @forelse ($kegiatans as $kegiatan)
                            @php
                                $status = $row->sel[$kegiatan->id] ?? null;
                                [$bg, $letter] = $statusBadge[$status];
                            @endphp
                            <td class="px-2.5 py-3 text-center" title="{{ $kegiatan->materi }} · {{ $statusBadge[$status][2] }}">
                                <span class="w-6 h-6 inline-flex items-center justify-center rounded-md {{ $bg }} text-[10px] font-extrabold">{{ $letter }}</span>
                            </td>
                        @empty
                            <td class="px-3 py-3 text-center text-slate-300">–</td>
                        @endforelse
                        <td class="px-3 py-3 text-center font-bold text-emerald-600">{{ $row->hadir }}</td>
                        <td class="px-3 py-3 text-center font-bold text-amber-600">{{ $row->izin }}</td>
                        <td class="px-3 py-3 text-center font-bold text-violet-600">{{ $row->sakit }}</td>
                        <td class="px-3 py-3 text-center font-bold text-rose-600">{{ $row->alpha }}</td>
                        <td class="px-3 py-3 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="font-bold text-slate-700">{{ $row->persentaseKehadiran }}%</span>
                                <span class="w-10 h-1.5 rounded-full bg-slate-100 overflow-hidden inline-block">
                                    <span class="block h-full rounded-full {{ $persen >= 75 ? 'bg-emerald-400' : ($persen >= 50 ? 'bg-amber-400' : 'bg-rose-400') }}" style="width: {{ min($persen, 100) }}%"></span>
                                </span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $kegiatans->count() + 6 }}" class="px-4 py-12 text-center text-slate-400">
                            {{ $emptyText ?? 'Belum ada anggota aktif untuk direkap.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>