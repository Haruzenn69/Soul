@extends('pembina.layout')
@section('title', 'Rekap Absensi')

@section('content')
    <div class="animate-fade-up">
        <h1 class="text-xl font-extrabold text-slate-900">Rekap Absensi</h1>
        <p class="text-xs text-slate-400 mt-0.5">Rekap kehadiran anggota per bulan pada ekskul yang anda bina</p>
    </div>

    {{-- Filter Bulan & Ekskul --}}
    <form method="GET" action="{{ route('pembina.rekap') }}" class="bg-white p-4 md:p-5 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 flex flex-wrap items-center gap-3 animate-fade-up" style="animation-delay: .1s">
        <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Periode Bulan
        </span>
        <select name="bulan" class="px-3 py-1.5 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
            @foreach($availableMonths as $option)
                @php
                    $optionLabel = \Carbon\Carbon::createFromFormat('Y-m', $option)->translatedFormat('F Y');
                @endphp
                <option value="{{ $option }}" {{ $bulan === $option ? 'selected' : '' }}>{{ $optionLabel }}</option>
            @endforeach
        </select>

        @if(count($ekskuls) > 1)
            <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Ekskul
            </span>
            <select name="ekskul" class="px-3 py-1.5 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                @foreach($ekskuls as $ekskul)
                    <option value="{{ $ekskul->id }}" {{ (int) $ekskulId === (int) $ekskul->id ? 'selected' : '' }}>{{ $ekskul->nama_ekskul }}</option>
                @endforeach
            </select>
        @endif

        <button type="submit" class="px-4 py-1.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 transition-all hover:-translate-y-0.5">Tampilkan Rekap</button>
        <span class="ml-auto text-[11px] font-semibold text-slate-400 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</span>
    </form>

    {{-- Ringkasan Bulan Ini --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 animate-fade-up" style="animation-delay: .15s">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-100 p-4 md:p-5 shadow-lg shadow-emerald-100/60 hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Hadir</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-emerald-600 mt-1 md:mt-1.5">{{ $totalHadir }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-emerald-200">H</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-emerald-700 mt-0.5 md:mt-1">Kehadiran</p>
        </div>

        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 p-4 md:p-5 shadow-lg shadow-amber-100/60 hover:-translate-y-1 transition-all duration-300" style="animation-delay: .2s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Izin</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-amber-600 mt-1 md:mt-1.5">{{ $totalIzin }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-amber-200">I</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-amber-700 mt-0.5 md:mt-1">Perizinan</p>
        </div>

        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-rose-50 to-white border border-rose-100 p-4 md:p-5 shadow-lg shadow-rose-100/60 hover:-translate-y-1 transition-all duration-300" style="animation-delay: .25s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Sakit</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-red-600 mt-1 md:mt-1.5">{{ $totalSakit }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-rose-400 to-red-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-rose-200">S</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-rose-700 mt-0.5 md:mt-1">Keterangan</p>
        </div>

        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-4 md:p-5 shadow-lg shadow-sky-100/60 hover:-translate-y-1 transition-all duration-300" style="animation-delay: .3s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[9px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Alpha</p>
                    <h3 class="text-xl md:text-3xl font-extrabold text-sky-700 mt-1 md:mt-1.5">{{ $totalAlpha }}</h3>
                </div>
                <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center text-sm md:text-base font-extrabold shadow-md shadow-sky-200">A</div>
            </div>
            <p class="text-[9px] md:text-[11px] font-semibold text-sky-600 mt-0.5 md:mt-1">Tanpa Keterangan</p>
        </div>
    </div>

    {{-- Tabel Rekap per Anggota --}}
    @php
        $selectedEkskul = $ekskuls->firstWhere('id', (int) $ekskulId);
    @endphp
    <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .35s">
        <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Rekap {{ $selectedEkskul?->nama_ekskul ?? 'Anggota' }}</h2>
                <p class="text-[11px] text-slate-400 mt-0.5">Rekap kehadiran seluruh anggota pada {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</p>
            </div>
            <span class="text-[11px] font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ count($rekaps) }} Anggota</span>
        </div>

        @if(count($rekaps) > 0)
            <div class="overflow-x-auto">
                <table class="card-table w-full text-xs">
                    <thead>
                        <tr class="bg-gradient-to-r from-sky-50 to-blue-50">
                            <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">No</th>
                            <th class="text-left p-3 font-semibold text-slate-500">NIS</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Nama</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Kelas</th>
                            <th class="text-center p-3 font-semibold text-emerald-600">Hadir</th>
                            <th class="text-center p-3 font-semibold text-amber-600">Izin</th>
                            <th class="text-center p-3 font-semibold text-red-600">Sakit</th>
                            <th class="text-center p-3 font-semibold text-sky-600">Alpha</th>
                            <th class="text-center p-3 font-semibold text-slate-500">Total</th>
                            <th class="text-left p-3 font-semibold text-slate-500 rounded-r-xl">Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekaps as $index => $rekap)
                            <tr class="border-b border-sky-50 hover:bg-sky-50/50 transition">
                                <td class="p-3 text-slate-500">{{ $index + 1 }}</td>
                                <td class="p-3 font-medium text-slate-700">{{ $rekap->pendaftaran->siswa->nis ?? '-' }}</td>
                                <td class="p-3 font-medium text-slate-800">{{ $rekap->pendaftaran->siswa->nama ?? '-' }}</td>
                                <td class="p-3 text-slate-600">{{ $rekap->pendaftaran->siswa->kelas->nama ?? '-' }}</td>
                                <td class="p-3 text-center font-bold text-emerald-600">{{ $rekap->hadir }}</td>
                                <td class="p-3 text-center font-bold text-amber-600">{{ $rekap->izin }}</td>
                                <td class="p-3 text-center font-bold text-red-600">{{ $rekap->sakit }}</td>
                                <td class="p-3 text-center font-bold text-sky-600">{{ $rekap->alpha }}</td>
                                <td class="p-3 text-center font-semibold text-slate-700">{{ $rekap->total }}</td>
                                <td class="p-3 min-w-[9rem]">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full rounded-full
                                                {{ $rekap->persentaseKehadiran >= 75 ? 'bg-gradient-to-r from-emerald-400 to-teal-500' : ($rekap->persentaseKehadiran >= 50 ? 'bg-gradient-to-r from-amber-300 to-yellow-400' : 'bg-gradient-to-r from-rose-400 to-red-500') }}"
                                                style="width: {{ $rekap->persentaseKehadiran }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-600 whitespace-nowrap">{{ $rekap->persentaseKehadiran }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-slate-500">Belum ada anggota aktif</p>
                <p class="text-[11px] text-slate-400 mt-1">Tidak ada anggota dengan status diterima pada ekskul ini</p>
            </div>
        @endif
    </div>

    {{-- Daftar Kegiatan Bulan Ini --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .4s">
        <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Kegiatan {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</h2>
                <p class="text-[11px] text-slate-400 mt-0.5">Agenda kegiatan ekskul pada bulan terpilih</p>
            </div>
            <span class="text-[11px] font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ count($kegiatans) }} Kegiatan</span>
        </div>

        @if(count($kegiatans) > 0)
            <div class="overflow-x-auto">
                <table class="card-table w-full text-xs">
                    <thead>
                        <tr class="bg-gradient-to-r from-sky-50 to-blue-50">
                            <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">Tanggal</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Materi</th>
                            <th class="text-center p-3 font-semibold text-emerald-600">Hadir</th>
                            <th class="text-center p-3 font-semibold text-amber-600">Izin</th>
                            <th class="text-center p-3 font-semibold text-red-600">Sakit</th>
                            <th class="text-center p-3 font-semibold text-sky-600 rounded-r-xl">Alpha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $presensiCounts = $kegiatans->map(function ($kegiatan) {
                                $grouped = $kegiatan->presensis->groupBy('status');
                                return [
                                    'hadir' => $grouped->get('hadir', collect())->count(),
                                    'izin' => $grouped->get('izin', collect())->count(),
                                    'sakit' => $grouped->get('sakit', collect())->count(),
                                    'alpha' => $grouped->get('alpha', collect())->count(),
                                ];
                            });
                        @endphp
                        @foreach($kegiatans as $index => $kegiatan)
                            <tr class="border-b border-sky-50 hover:bg-sky-50/50 transition">
                                <td class="p-3">
                                    <span class="text-slate-700 font-semibold">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('d M Y') }}</span>
                                    <span class="block text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->translatedFormat('l') }}</span>
                                </td>
                                <td class="p-3 font-medium text-slate-800">{{ $kegiatan->materi ?? 'Kegiatan' }}</td>
                                <td class="p-3 text-center font-bold text-emerald-600">{{ $presensiCounts[$index]['hadir'] }}</td>
                                <td class="p-3 text-center font-bold text-amber-600">{{ $presensiCounts[$index]['izin'] }}</td>
                                <td class="p-3 text-center font-bold text-red-600">{{ $presensiCounts[$index]['sakit'] }}</td>
                                <td class="p-3 text-center font-bold text-sky-600">{{ $presensiCounts[$index]['alpha'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-slate-500">Belum ada kegiatan di bulan ini</p>
                <p class="text-[11px] text-slate-400 mt-1">Tidak ada kegiatan ekskul yang tercatat pada {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</p>
            </div>
        @endif
    </div>
@endsection