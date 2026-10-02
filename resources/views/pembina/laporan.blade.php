@extends('pembina.layout')
@section('title', 'Cetak Laporan')

@section('content')
    <div class="animate-fade-up space-y-6">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Cetak Laporan Ekskul</h1>
            <p class="text-xs text-slate-400 mt-0.5">Tinjau, unduh, dan lihat presensi dari ekskul yang anda bina</p>
        </div>

        <!-- DAFTAR LAPORAN BULANAN -->
        <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 space-y-4">
            <div class="flex flex-wrap justify-between items-center gap-2">
                <h2 class="text-sm font-extrabold text-slate-900">Laporan Bulanan</h2>
                <span class="text-xs font-medium text-slate-400">Total {{ $laporans->total() }} laporan</span>
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Laporan bulanan disusun dan diserahkan oleh ketua ekskul setiap bulan. Anda dapat meninjau detail, melihat presensi, dan mengunduh laporan dalam format PDF.
            </p>

            {{-- Search & Filter Bar --}}
            <div class="bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden">
                <form method="GET" action="{{ route('pembina.laporan') }}" id="laporan-filter-form">
                    <div class="p-4 flex flex-col sm:flex-row gap-3">
                        {{-- Search --}}
                        <div class="relative flex-1">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </span>
                            <input type="text" name="cari" value="{{ request('cari') }}"
                                placeholder="Cari bulan atau nama ekskul..."
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                        </div>

                        {{-- Filter Status --}}
                        <div class="relative shrink-0">
                            <select name="status" onchange="this.form.submit()"
                                class="pl-4 pr-8 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all appearance-none">
                                <option value="" @selected(!request('status'))>Semua Status</option>
                                <option value="menunggu"  @selected(request('status') === 'menunggu')>Menunggu Persetujuan</option>
                                <option value="disetujui" @selected(request('status') === 'disetujui')>Disetujui</option>
                                <option value="ditolak"   @selected(request('status') === 'ditolak')>Ditolak</option>
                            </select>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </span>
                        </div>

                        {{-- Filter Ekskul --}}
                        @if($ekskuls->count() > 1)
                        <div class="relative shrink-0">
                            <select name="ekskul" onchange="this.form.submit()"
                                class="pl-4 pr-8 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all appearance-none">
                                <option value="">Semua Ekskul</option>
                                @foreach($ekskuls as $e)
                                    <option value="{{ $e->id }}" @selected(request('ekskul') == $e->id)>{{ $e->nama_ekskul }}</option>
                                @endforeach
                            </select>
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </span>
                        </div>
                        @endif

                        {{-- Sort hidden inputs --}}
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="direction" value="{{ $direction }}">

                        {{-- Submit + Reset --}}
                        <div class="flex gap-2 shrink-0">
                            <button type="submit"
                                class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-2xl transition shadow-sm">
                                Cari
                            </button>
                            @if(request('cari') || request('status') || request('ekskul'))
                                <a href="{{ route('pembina.laporan') }}"
                                    class="px-4 py-2.5 bg-white hover:bg-slate-100 text-slate-600 text-xs font-bold rounded-2xl border border-slate-200 transition">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            @if($laporans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="card-table w-full text-xs">
                        <thead class="bg-sky-50">
                            <tr>
                                <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">No</th>
                                @include('partials.th-sort', ['label' => 'Periode', 'key' => 'bulan', 'sort' => $sort, 'direction' => $direction, 'class' => 'text-left p-3 font-semibold text-slate-500'])
                                <th class="text-left p-3 font-semibold text-slate-500">Ekskul</th>
                                @include('partials.th-sort', ['label' => 'Status', 'key' => 'status', 'sort' => $sort, 'direction' => $direction, 'class' => 'text-left p-3 font-semibold text-slate-500'])
                                <th class="text-left p-3 font-semibold text-slate-500 rounded-r-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($laporans as $key => $laporan)
                            <tr class="border-b border-sky-50 hover:bg-sky-50/50 transition">
                                <td class="p-3">{{ $laporans->firstItem() + $key }}</td>
                                <td class="p-3 font-medium">{{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->translatedFormat('F Y') }}</td>
                                <td class="p-3">{{ $laporan->ekskul->nama_ekskul ?? '-' }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-semibold
                                        @if($laporan->status == 'menunggu') bg-sky-100 text-sky-700
                                        @elseif($laporan->status == 'disetujui') bg-emerald-100 text-emerald-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ ucfirst($laporan->status) }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('pembina.laporan.show', $laporan) }}" class="px-3 py-1.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-[10px] font-semibold rounded-lg transition shadow-md shadow-sky-200">Detail</a>
                                        <a href="{{ route('pembina.laporan.download', $laporan) }}" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[10px] font-semibold rounded-lg transition shadow-md shadow-red-200 flex items-center gap-1">
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
                @if($laporans->hasPages())
                    <div class="pt-2">
                        {{ $laporans->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-8 text-slate-400">
                    <p class="text-sm font-medium">
                        @if(request('cari') || request('status') || request('ekskul'))
                            Tidak ada laporan yang sesuai filter.
                            <a href="{{ route('pembina.laporan') }}" class="text-sky-500 hover:underline text-xs block mt-1">Reset filter</a>
                        @else
                            Belum ada laporan bulanan dari ketua ekskul.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- PRESENSI RINGKAS -->
        <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 space-y-4" style="animation-delay: .15s">
            <div class="flex flex-wrap justify-between items-center gap-2">
                <h2 class="text-sm font-extrabold text-slate-900">Rekap Presensi Kegiatan</h2>
                <a href="{{ route('pembina.presensi') }}" class="text-xs font-bold text-sky-600 hover:underline">Lihat Detail Presensi</a>
            </div>

            @if($kegiatans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="card-table w-full text-xs">
                        <thead class="bg-sky-50">
                            <tr>
                                <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">Kegiatan</th>
                                <th class="text-left p-3 font-semibold text-slate-500">Tanggal</th>
                                <th class="text-center p-3 font-semibold text-emerald-600">Hadir</th>
                                <th class="text-center p-3 font-semibold text-sky-600">Izin</th>
                                <th class="text-center p-3 font-semibold text-amber-600">Sakit</th>
                                <th class="text-center p-3 font-semibold text-red-600 rounded-r-xl">Alpha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kegiatans as $kegiatan)
                            <tr class="border-b border-sky-50 hover:bg-sky-50/50 transition">
                                <td class="p-3 font-medium">{{ $kegiatan->materi }}</td>
                                <td class="p-3">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('DD MMM Y') }}</td>
                                <td class="p-3 text-center font-bold text-emerald-600">{{ $kegiatan->hadir_count }}</td>
                                <td class="p-3 text-center font-bold text-sky-600">{{ $kegiatan->izin_count }}</td>
                                <td class="p-3 text-center font-bold text-amber-600">{{ $kegiatan->sakit_count }}</td>
                                <td class="p-3 text-center font-bold text-red-600">{{ $kegiatan->alpha_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($kegiatans->hasPages())
                    <div class="pt-2">
                        {{ $kegiatans->withQueryString()->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-8 text-slate-400">
                    <p class="text-sm">Belum ada kegiatan yang tercatat.</p>
                </div>
            @endif
        </div>
    </div>
@endsection