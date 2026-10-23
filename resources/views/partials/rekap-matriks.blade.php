@php
    $statusList = ['hadir', 'sakit', 'izin', 'alpha'];
    $statusBadge = [
        'hadir' => ['bg-emerald-100 text-emerald-700', 'H', 'Hadir'],
        'sakit' => ['bg-violet-100 text-violet-700', 'S', 'Sakit'],
        'izin'  => ['bg-amber-100 text-amber-700', 'I', 'Izin'],
        'alpha' => ['bg-rose-100 text-rose-700', 'A', 'Alpha'],
        null    => ['bg-slate-100 text-slate-400', '&#x2013;', 'Belum diabsen'],
    ];
    $matriksTitle = $matriksTitle ?? 'Matriks Kehadiran';
    $eventCount = isset($eventKegiatans) ? $eventKegiatans->count() : 0;
    $matriksSubtitle = $matriksSubtitle ?? ($rows->count().' anggota · '.$kegiatans->count().' kegiatan rutin'.($eventCount > 0 ? ' · '.$eventCount.' event' : ''));
@endphp

{{-- LEGEND --}}
<div class="flex items-center gap-4 flex-wrap bg-white dark:bg-neutral-900 rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-lg shadow-sky-100/60 dark:shadow-none px-5 py-4 animate-fade-up">
    <span class="text-[10px] font-bold text-slate-400 dark:text-neutral-500 tracking-wider uppercase">Keterangan</span>
    @foreach ($statusList as $status)
        @php [$bg, $letter, $label] = $statusBadge[$status]; @endphp
        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 dark:text-neutral-300">
            <span class="attendance-status attendance-status--{{ $status }} w-5 h-5 rounded-md {{ $bg }} flex items-center justify-center text-[10px] font-extrabold">{{ $letter }}</span>
            {{ $label }}
        </span>
    @endforeach
    <span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 dark:text-neutral-300">
        <span class="attendance-status attendance-status--unmarked w-5 h-5 rounded-md bg-slate-100 text-slate-400 flex items-center justify-center text-[10px] font-extrabold">&#x2013;</span>
        Belum diabsen
    </span>
</div>

{{-- MATRIKS CARD --}}
<div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-lg shadow-sky-100/60 dark:shadow-none overflow-hidden animate-fade-up" style="animation-delay: .1s">
    <div class="px-5 md:px-6 py-4 flex justify-between items-center border-b border-sky-50 dark:border-neutral-800">
        <div>
            <h2 class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $matriksTitle }}</h2>
            <p class="text-[11px] text-slate-400 dark:text-neutral-500 mt-0.5">{{ $matriksSubtitle }}</p>
        </div>
        @if(isset($ekskul) && $ekskul)
            <span class="text-[10px] font-semibold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/30 border border-sky-100 dark:border-sky-800/50 px-3 py-1.5 rounded-full">{{ $ekskul->nama_ekskul }}</span>
        @endif
    </div>

    @include('partials.table-client-tools', [
        'tableId' => 'rekap-matriks-table',
        'searchCols' => [1],
        'filterCols' => [],
        'filterOptions' => [],
        'defaultSize' => 10,
    ])

    <div class="overflow-x-auto">
        <table id="rekap-matriks-table" class="card-table w-full text-left text-xs md:text-sm min-w-max">
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50 dark:from-neutral-800 dark:to-neutral-800/90">
                <tr>
                    <th class="px-4 md:px-5 py-3 font-semibold text-slate-500 dark:text-neutral-400 sticky left-0 bg-sky-50 dark:bg-neutral-800 z-10" rowspan="2">No</th>
                    <th data-sort-index="1" class="px-4 md:px-5 py-3 font-semibold text-slate-500 dark:text-neutral-400 sticky left-10 bg-sky-50 dark:bg-neutral-800 z-10 cursor-pointer select-none hover:text-slate-800 dark:hover:text-neutral-200 transition" rowspan="2" title="Klik untuk urutkan">Nama</th>
                    <th colspan="{{ max($kegiatans->count(), 1) }}" class="px-3 py-3 font-semibold text-slate-500 dark:text-neutral-400 text-center">Pertemuan (tanggal &amp; materi)</th>
                    <th colspan="5" class="px-3 py-3 font-semibold text-slate-500 dark:text-neutral-400 text-center">Total</th>
                </tr>
                <tr>
                    @forelse ($kegiatans as $kegiatan)
                        <th class="px-2.5 py-2.5 font-bold text-slate-600 dark:text-neutral-300 text-center whitespace-nowrap" title="{{ $kegiatan->materi }}">
                            {{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('d M') : 'Keg. #'.$kegiatan->id }}
                        </th>
                    @empty
                        <th class="px-3 py-2.5 font-semibold text-slate-400 dark:text-neutral-500 text-center">Belum ada kegiatan</th>
                    @endforelse
                    <th class="px-3 py-2.5 font-bold text-emerald-600 dark:text-emerald-400 text-center">Hadir</th>
                    <th class="px-3 py-2.5 font-bold text-amber-600 dark:text-amber-400 text-center">Izin</th>
                    <th class="px-3 py-2.5 font-bold text-violet-600 dark:text-violet-400 text-center">Sakit</th>
                    <th class="px-3 py-2.5 font-bold text-rose-600 dark:text-rose-400 text-center">Alpha</th>
                    <th class="px-3 py-2.5 font-bold text-slate-600 dark:text-neutral-400 text-center">% Kehadiran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50 dark:divide-neutral-800">
                @forelse ($rows as $row)
                    @php
                        $anggota = $row->pendaftaran->siswa;
                        $persen = (float) $row->persentaseKehadiran;
                    @endphp
                    <tr class="hover:bg-sky-50/50 dark:hover:bg-neutral-800/60 transition">
                        <td class="px-4 md:px-5 py-3 whitespace-nowrap sticky left-0 bg-white dark:bg-neutral-900 z-10 text-slate-700 dark:text-neutral-300">{{ $loop->iteration }}</td>
                        <td class="px-4 md:px-5 py-3 whitespace-nowrap sticky left-10 bg-white dark:bg-neutral-900 z-10">
                            <span class="font-bold text-slate-800 dark:text-neutral-100">{{ $anggota->nama }}</span>
                            <span class="block text-[10px] text-slate-400 dark:text-neutral-500 mt-0.5">{{ $anggota->kelas?->nama ?? 'Tanpa kelas' }}</span>
                        </td>
                        @forelse ($kegiatans as $kegiatan)
                            @php
                                $status = $row->sel[$kegiatan->id] ?? null;
                                [$bg, $letter] = $statusBadge[$status];
                            @endphp
                            <td class="px-2.5 py-3 text-center" title="{{ $kegiatan->materi }} · {{ $statusBadge[$status][2] }}">
                                <span class="attendance-status attendance-status--{{ $status ?? 'unmarked' }} w-6 h-6 inline-flex items-center justify-center rounded-md {{ $bg }} text-[10px] font-extrabold">{{ $letter }}</span>
                            </td>
                        @empty
                            <td class="px-3 py-3 text-center text-slate-300 dark:text-neutral-600">&#x2013;</td>
                        @endforelse
                        <td class="px-3 py-3 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $row->hadir }}</td>
                        <td class="px-3 py-3 text-center font-bold text-amber-600 dark:text-amber-400">{{ $row->izin }}</td>
                        <td class="px-3 py-3 text-center font-bold text-violet-600 dark:text-violet-400">{{ $row->sakit }}</td>
                        <td class="px-3 py-3 text-center font-bold text-rose-600 dark:text-rose-400">{{ $row->alpha }}</td>
                        <td class="px-3 py-3 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="font-bold text-slate-700 dark:text-neutral-300">{{ $row->persentaseKehadiran }}%</span>
                                <span class="w-10 h-1.5 rounded-full bg-slate-100 dark:bg-neutral-700 overflow-hidden inline-block">
                                    <span class="block h-full rounded-full {{ $persen >= 75 ? 'bg-emerald-400' : ($persen >= 50 ? 'bg-amber-400' : 'bg-rose-400') }}" style="width: {{ min($persen, 100) }}%"></span>
                                </span>
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $kegiatans->count() + 6 }}" class="px-4 py-12 text-center text-slate-400 dark:text-neutral-500">
                            {{ $emptyText ?? 'Belum ada anggota aktif untuk direkap.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
