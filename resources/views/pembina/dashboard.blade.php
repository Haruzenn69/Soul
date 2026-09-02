@extends('pembina.layout')
@section('title', 'Dashboard Pembina')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Selamat datang, {{ $pembina->nama ?? 'Pembina' }}</h1>
            <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
        </div>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Ekskul Dibina</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $ekskul ? 1 : 0 }}</h3>
                <p class="text-[11px] font-medium text-blue-600 mt-1">{{ $ekskul->nama_ekskul ?? 'Belum ada ekskul' }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Anggota Aktif</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ count($anggota ?? []) }}</h3>
                <p class="text-[11px] font-medium text-emerald-600 mt-1">Siswa terdaftar</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Pendaftaran Baru</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ count($pendaftaranPending ?? []) }}</h3>
                <p class="text-[11px] font-medium text-amber-600 mt-1">Menunggu verifikasi</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KIRI: Daftar Anggota -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Daftar Anggota Ekskul</h2>
                    <a href="{{ route('pembina.anggota') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua</a>
                </div>

                @if(isset($anggota) && count($anggota) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">No</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">NIS</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">Nama</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">Kelas</th>
                                    <th class="text-left p-3 font-semibold text-slate-500 rounded-r-xl">Jabatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($anggota as $key => $item)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="p-3">{{ $key + 1 }}</td>
                                    <td class="p-3 font-medium">{{ $item->siswa->nis ?? '-' }}</td>
                                    <td class="p-3 font-medium">{{ $item->siswa->nama ?? '-' }}</td>
                                    <td class="p-3">{{ $item->siswa->kelas->nama ?? '-' }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-semibold 
                                            {{ $item->siswa->jabatan == 'ketua' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ ucfirst($item->siswa->jabatan ?? 'anggota') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <p class="text-sm">Belum ada anggota di ekskul ini.</p>
                    </div>
                @endif
            </div>

            <!-- KEGIATAN MENDATANG -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Kegiatan Mendatang</h2>
                    <a href="{{ route('pembina.presensi') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Presensi</a>
                </div>
                @forelse($kegiatanMendatang ?? [] as $kegiatan)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/50 flex items-center justify-between mb-2">
                        <div>
                            <h4 class="text-xs font-semibold text-slate-800">{{ $kegiatan->materi }}</h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $kegiatan->ekskul->nama_ekskul ?? 'Ekskul' }}</p>
                        </div>
                        <span class="text-[10px] font-medium text-slate-500 shrink-0 ml-3">
                            {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('DD MMM Y') }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada agenda kegiatan mendatang.</p>
                @endforelse
            </div>
        </div>

        <!-- KANAN: Pendaftaran + Laporan -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Pendaftaran Siswa</h2>
                    <a href="{{ route('pembina.pendaftaran') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat</a>
                </div>

                @if(isset($pendaftaranPending) && count($pendaftaranPending) > 0)
                    @foreach($pendaftaranPending as $item)
                    <div class="p-4 bg-slate-50 rounded-xl flex items-center justify-between mb-3 border border-slate-200/60">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs uppercase">
                                {{ substr($item->siswa->nama ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">{{ $item->siswa->nama ?? '-' }}</h4>
                                <p class="text-[10px] text-slate-400">
                                    {{ $item->siswa->kelas->nama ?? '-' }} · 
                                    {{ \Carbon\Carbon::parse($item->tanggal_daftar)->isoFormat('DD MMM Y') }}
                                </p>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-semibold rounded-full">
                            Pending
                        </span>
                    </div>
                    @endforeach
                    <div class="mt-4 p-3 bg-blue-50 rounded-xl border border-blue-200">
                        <p class="text-xs text-blue-700">Verifikasi pendaftaran dilakukan secara manual melalui proses offline oleh ketua ekskul.</p>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <p class="text-sm">Tidak ada pendaftaran baru.</p>
                    </div>
                @endif
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Laporan Bulanan</h2>
                    <a href="{{ route('pembina.laporan.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Cetak</a>
                </div>
                @forelse($laporanDraft ?? [] as $laporan)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/50 flex items-center justify-between mb-2">
                        <div>
                            <h4 class="text-xs font-semibold text-slate-800">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->translatedFormat('F Y') }}
                            </h4>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $laporan->ekskul->nama_ekskul ?? 'Ekskul' }}</p>
                        </div>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-semibold rounded-full">Draft</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada laporan bulanan.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
