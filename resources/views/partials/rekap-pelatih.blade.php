@php
    $pelatihData = $pelatih ?? null;
    $perKegiatan = $presensiPelatih ?? [];
    $rutinList = $kegiatans ?? collect();
    $eventList = $eventKegiatans ?? collect();
    $semuaKegiatan = collect($rutinList)->merge($eventList);

    $letterMap = [
        'hadir' => 'H',
        'sakit' => 'S',
        'izin' => 'I',
        'alpha' => 'A',
        null => '&ndash;',
    ];
@endphp

@if($pelatihData && $semuaKegiatan->isNotEmpty())
    <div class="bg-white rounded-3xl border border-amber-200 shadow-lg shadow-amber-100/60 overflow-hidden animate-fade-up" style="animation-delay: .2s">
        <div class="px-5 md:px-6 py-4 flex justify-between items-center border-b border-amber-100">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Kehadiran Pelatih</h2>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ $pelatihData->nama }} &middot; status H/S/I/A &middot; tidak memengaruhi kehadiran anggota</p>
            </div>
            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-full">Pelatih</span>
        </div>

        <div class="overflow-x-auto">
            <table class="card-table w-full text-left text-xs md:text-sm min-w-max">
                <thead class="bg-gradient-to-r from-amber-50 to-yellow-50">
                    <tr>
                        <th class="px-4 md:px-5 py-3 font-semibold text-slate-500">Nama</th>
                        @foreach ($semuaKegiatan as $kegiatan)
                            <th class="px-2.5 py-2.5 font-bold text-slate-600 text-center whitespace-nowrap" title="{{ $kegiatan->materi }}">
                                @if($kegiatan->isEvent())
                                    @if($kegiatan->tanggal_berakhir && $kegiatan->tanggal_berakhir->ne($kegiatan->tanggal_kegiatan))
                                        {{ $kegiatan->tanggal_kegiatan->translatedFormat('d M') }}&ndash;{{ $kegiatan->tanggal_berakhir->translatedFormat('d M') }}
                                    @else
                                        {{ $kegiatan->tanggal_kegiatan->translatedFormat('d M') }}
                                    @endif
                                    <span class="ml-1 text-[9px] font-extrabold text-amber-600">E</span>
                                @else
                                    {{ $kegiatan->tanggal_kegiatan->translatedFormat('d M') }}
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-50">
                    <tr class="hover:bg-amber-50/40 transition">
                        <td class="px-4 md:px-5 py-3 whitespace-nowrap">
                            <span class="font-bold text-slate-800">{{ $pelatihData->nama }}</span>
                            <span class="block text-[10px] text-slate-400 mt-0.5">Pelatih ekskul</span>
                        </td>
                        @foreach ($semuaKegiatan as $kegiatan)
                            @php
                                $status = $perKegiatan[$kegiatan->id] ?? null;
                            @endphp
                            <td class="px-2.5 py-3 text-center">
                                <span class="w-6 h-6 inline-flex items-center justify-center rounded-md {{ match ($status) { 'hadir' => 'bg-emerald-100 text-emerald-700', 'sakit' => 'bg-violet-100 text-violet-700', 'izin' => 'bg-amber-100 text-amber-700', 'alpha' => 'bg-rose-100 text-rose-700', default => 'bg-slate-100 text-slate-400' } }} text-[10px] font-extrabold">{!! $letterMap[$status] !!}</span>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endif