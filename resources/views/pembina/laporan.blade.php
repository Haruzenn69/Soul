@extends('pembina.layout')
@section('title', 'Cetak Laporan')

@section('content')
    <div>
        <h1 class="text-xl font-bold text-slate-900">Cetak Laporan Ekskul</h1>
        <p class="text-xs text-slate-400 mt-0.5">Tinjau, unduh, dan lihat presensi dari ekskul yang anda bina</p>
    </div>

    <!-- DAFTAR LAPORAN BULANAN -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-bold text-slate-900">Laporan Bulanan</h2>
            <span class="text-xs font-medium text-slate-400">Total {{ count($laporans) }} laporan</span>
        </div>
        <p class="text-[11px] text-slate-400 mb-4 leading-relaxed">
            Laporan bulanan disusun dan diserahkan oleh ketua ekskul setiap bulan. Anda dapat meninjau detail, melihat presensi, dan mengunduh laporan dalam format PDF.
        </p>

        @if(count($laporans) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">No</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Periode</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Ekskul</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Status</th>
                            <th class="text-left p-3 font-semibold text-slate-500 rounded-r-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporans as $key => $laporan)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                            <td class="p-3">{{ $key + 1 }}</td>
                            <td class="p-3 font-medium">{{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->translatedFormat('F Y') }}</td>
                            <td class="p-3">{{ $laporan->ekskul->nama_ekskul ?? '-' }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded-full text-[10px] font-semibold
                                    @if($laporan->status == 'draft') bg-slate-100 text-slate-600
                                    @elseif($laporan->status == 'disetujui') bg-emerald-100 text-emerald-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($laporan->status) }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('pembina.laporan.show', $laporan) }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-semibold rounded-lg transition">Detail</a>
                                    <a href="{{ route('pembina.laporan.download', $laporan) }}" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-[10px] font-semibold rounded-lg transition flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-slate-400">
                <p class="text-sm">Belum ada laporan bulanan dari ketua ekskul.</p>
            </div>
        @endif
    </div>

    <!-- PRESENSI RINGKAS -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-bold text-slate-900">Rekap Presensi Kegiatan</h2>
            <a href="{{ route('pembina.presensi') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Detail Presensi</a>
        </div>

        @if(count($kegiatans) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">Kegiatan</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Tanggal</th>
                            <th class="text-center p-3 font-semibold text-emerald-600">Hadir</th>
                            <th class="text-center p-3 font-semibold text-blue-600">Izin</th>
                            <th class="text-center p-3 font-semibold text-amber-600">Sakit</th>
                            <th class="text-center p-3 font-semibold text-red-600 rounded-r-xl">Alpha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kegiatans as $kegiatan)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                            <td class="p-3 font-medium">{{ $kegiatan->materi }}</td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('DD MMM Y') }}</td>
                            <td class="p-3 text-center font-bold text-emerald-600">{{ $kegiatan->hadir_count }}</td>
                            <td class="p-3 text-center font-bold text-blue-600">{{ $kegiatan->izin_count }}</td>
                            <td class="p-3 text-center font-bold text-amber-600">{{ $kegiatan->sakit_count }}</td>
                            <td class="p-3 text-center font-bold text-red-600">{{ $kegiatan->alpha_count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-slate-400">
                <p class="text-sm">Belum ada kegiatan yang tercatat.</p>
            </div>
        @endif
    </div>
@endsection
