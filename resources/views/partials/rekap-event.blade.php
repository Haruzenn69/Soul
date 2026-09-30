@php
    $statusListEvent = ['hadir', 'sakit', 'izin', 'alpha'];
    $eventBadge = [
        'hadir' => ['bg-emerald-100 text-emerald-700', 'H'],
        'sakit' => ['bg-violet-100 text-violet-700', 'S'],
        'izin'  => ['bg-amber-100 text-amber-700', 'I'],
        'alpha' => ['bg-rose-100 text-rose-700', 'A'],
        null    => ['bg-slate-100 text-slate-400', '&ndash;'],
    ];
@endphp

{{-- MATRIKS KEGIATAN EVENT (tidak dihitung ke % kehadiran) --}}
@if(($eventKegiatans ?? collect())->isNotEmpty())
    <div class="bg-white rounded-3xl border border-amber-200 shadow-lg shadow-amber-100/60 overflow-hidden animate-fade-up" style="animation-delay: .15s">
        <div class="px-5 md:px-6 py-4 flex justify-between items-center border-b border-amber-100">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Kegiatan Event (Diklat, dll.)</h2>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $rows->count() }} anggota &middot; {{ $eventKegiatans->count() }} event &middot; tidak dihitung ke persentase kehadiran</p>
            </div>
            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-full">Event</span>
        </div>

        <div class="overflow-x-auto">
            <table class="card-table w-full text-left text-xs md:text-sm min-w-max">
                <thead class="bg-gradient-to-r from-amber-50 to-yellow-50">
                    <tr>
                        <th class="px-4 md:px-5 py-3 font-semibold text-slate-500 sticky left-0 bg-gradient-to-r from-amber-50 to-yellow-50 z-10">No</th>
                        <th class="px-4 md:px-5 py-3 font-semibold text-slate-500 sticky left-10 bg-gradient-to-r from-amber-50 to-yellow-50 z-10">Nama</th>
                        @foreach ($eventKegiatans as $kegiatan)
                            <th class="px-2.5 py-2.5 font-bold text-slate-600 text-center whitespace-nowrap" title="{{ $kegiatan->materi }}">
                                {{ $kegiatan->tanggalText() }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-50">
                    @foreach ($rows as $row)
                        @php
                            $anggota = $row->pendaftaran->siswa;
                        @endphp
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="px-4 md:px-5 py-3 whitespace-nowrap sticky left-0 bg-white z-10">{{ $loop->iteration }}</td>
                            <td class="px-4 md:px-5 py-3 whitespace-nowrap sticky left-10 bg-white z-10">
                                <span class="font-bold text-slate-800">{{ $anggota->nama }}</span>
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $anggota->kelas?->nama ?? 'Tanpa kelas' }}</span>
                            </td>
                            @foreach ($eventKegiatans as $kegiatan)
                                @php
                                    $status = $row->sel[$kegiatan->id] ?? null;
                                    [$bg, $letter] = $eventBadge[$status];
                                @endphp
                                <td class="px-2.5 py-3 text-center" title="{{ $kegiatan->materi }}">
                                    <span class="w-6 h-6 inline-flex items-center justify-center rounded-md {{ $bg }} text-[10px] font-extrabold">{!! $letter === '&ndash;' ? '&ndash;' : $letter !!}</span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif