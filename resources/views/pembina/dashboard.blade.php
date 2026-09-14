@extends('pembina.layout')
@section('title', 'Dashboard Pembina')

@section('content')
    <!-- HERO GREETING -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200 animate-fade-up">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 -right-10 w-72 h-72 rounded-full bg-amber-200/40 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute top-10 right-1/4 w-16 h-16 rounded-full bg-amber-300/50 blur-2xl"></div>
        </div>

        <div class="relative flex flex-col lg:flex-row lg:items-center gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-3 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-white/15 backdrop-blur border border-white/20 text-[10px] font-bold tracking-wide uppercase">Dashboard Pembina</span>
                    @if($ekskul)
                        <span class="px-3 py-1 rounded-full bg-amber-300/30 backdrop-blur border border-amber-200/40 text-[10px] font-bold tracking-wide uppercase flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                            Membina {{ $ekskul->nama_ekskul }}
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight">
                    Halo, {{ $pembina->nama ?? 'Pembina' }}!
                </h1>
                <p class="text-white/80 text-sm mt-2 font-medium">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- STATS CARDS (3 CARD → SELALU 3 DALAM SATU BARIS) -->
    <div class="grid grid-cols-3 gap-3 md:gap-4">
        <!-- Card 1: Ekskul Dibina -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .1s">
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-sky-100 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Ekskul Dibina</p>
                    <h3 class="text-xl md:text-2xl font-extrabold mt-0.5 md:mt-1 text-sky-700">{{ $ekskul ? 1 : 0 }}</h3>
                    <p class="text-[9px] md:text-[11px] font-semibold text-sky-600 mt-0.5 md:mt-1 truncate max-w-[80px] md:max-w-full">{{ $ekskul->nama_ekskul ?? 'Belum ada' }}</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center border border-white/40 shadow-lg shadow-sky-300">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Anggota Aktif -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-100 to-white border border-emerald-200 p-3 md:p-5 shadow-md shadow-emerald-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .2s">
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-emerald-100 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Anggota Aktif</p>
                    <h3 class="text-xl md:text-2xl font-extrabold mt-0.5 md:mt-1 text-emerald-600">{{ $anggotaAktifCount ?? 0 }}</h3>
                    <p class="text-[9px] md:text-[11px] font-semibold text-emerald-600 mt-0.5 md:mt-1">Siswa terdaftar</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center border border-white/40 shadow-lg shadow-emerald-300">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3: Pendaftaran Baru -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-100 to-white border border-amber-200 p-3 md:p-5 shadow-md shadow-amber-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 animate-fade-up" style="animation-delay: .3s">
            <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-amber-100 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Pendaftaran Baru</p>
                    <h3 class="text-xl md:text-2xl font-extrabold mt-0.5 md:mt-1 text-amber-600">{{ count($pendaftaranPending ?? []) }}</h3>
                    <p class="text-[9px] md:text-[11px] font-semibold text-amber-600 mt-0.5 md:mt-1">Menunggu verifikasi</p>
                </div>
                <div class="w-8 h-8 md:w-10 md:h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-yellow-500 text-white flex items-center justify-center border border-white/40 shadow-lg shadow-amber-300">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KIRI: Daftar Anggota (TABEL) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-5 md:p-6 rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .15s">
                <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Daftar Anggota Ekskul</h2>
                        <p class="text-[10px] md:text-[11px] text-slate-400 mt-0.5">Kelola keanggotaan ekskul</p>
                    </div>
                    <a href="{{ route('pembina.anggota') }}" class="text-xs font-bold text-sky-600 hover:underline flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                @if(isset($anggota) && count($anggota) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-gradient-to-r from-sky-50 to-blue-50 rounded-xl">
                                    <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">No</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">NIS</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">Nama</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">Kelas</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">Jabatan</th>
                                    <th class="text-left p-3 font-semibold text-slate-500 rounded-r-xl">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($anggota as $key => $item)
                                <tr class="border-b border-sky-50 hover:bg-sky-50/50 transition">
                                    <td class="p-3 text-slate-500">{{ $key + 1 }}</td>
                                    <td class="p-3 font-medium text-slate-700">{{ $item->siswa->nis ?? '-' }}</td>
                                    <td class="p-3 font-medium text-slate-800">{{ $item->siswa->nama ?? '-' }}</td>
                                    <td class="p-3 text-slate-600">{{ $item->siswa->kelas->nama ?? '-' }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-semibold border
                                            {{ $item->siswa->jabatan == 'ketua' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-sky-100 text-sky-700 border-sky-200' }}">
                                            @if($item->siswa->jabatan == 'ketua')
                                                Ketua
                                            @else
                                                Anggota
                                            @endif
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        @if($item->status === 'diterima')
                                            <span class="px-2 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">Aktif</span>
                                        @elseif($item->status === 'nonaktif')
                                            <span class="px-2 py-1 rounded-full text-[10px] font-semibold bg-red-100 text-red-700 border border-red-200">Nonaktif</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700 border border-amber-200">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <div class="mx-auto w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                            <svg class="w-5 h-5 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <p class="text-xs md:text-sm font-medium">Belum ada anggota</p>
                        <p class="text-[10px] md:text-xs mt-1">Belum ada siswa yang terdaftar di ekskul ini</p>
                    </div>
                @endif
            </div>

            <!-- KEGIATAN MENDATANG -->
            <div class="bg-white p-5 md:p-6 rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .2s">
                <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Kegiatan Mendatang</h2>
                        <p class="text-[10px] md:text-[11px] text-slate-400 mt-0.5">Agenda ekskul terdekat</p>
                    </div>
                    <a href="{{ route('pembina.presensi') }}" class="text-xs font-bold text-sky-600 hover:underline flex items-center gap-1">
                        Lihat Presensi
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                @forelse($kegiatanMendatang ?? [] as $kegiatan)
                    <div class="p-3 md:p-4 bg-gradient-to-r from-sky-50 to-amber-50 rounded-xl border border-sky-100 flex flex-wrap items-center justify-between gap-3 mb-2">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center text-xs font-extrabold shadow-md shadow-sky-200">
                                {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('d') }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-800 truncate">{{ $kegiatan->materi }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $kegiatan->ekskul->nama_ekskul ?? 'Ekskul' }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-100 px-2.5 py-1 rounded-lg shrink-0">
                            {{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('DD MMM Y') }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-6 md:py-8 text-slate-400">
                        <div class="mx-auto w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                            <svg class="w-5 h-5 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-xs md:text-sm font-medium">Belum ada agenda mendatang</p>
                        <p class="text-[10px] md:text-xs mt-1">Agenda ekskul akan muncul di sini</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- KANAN: Pendaftaran + Laporan -->
        <div class="space-y-6">
            <div class="bg-white p-5 md:p-6 rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .25s">
                <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Pendaftaran Siswa</h2>
                        <p class="text-[10px] md:text-[11px] text-slate-400 mt-0.5">Menunggu verifikasi</p>
                    </div>
                    <a href="{{ route('pembina.pendaftaran') }}" class="text-xs font-bold text-sky-600 hover:underline flex items-center gap-1">
                        Lihat
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                @if(isset($pendaftaranPending) && count($pendaftaranPending) > 0)
                    @foreach($pendaftaranPending as $item)
                    <div class="p-3 md:p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-xl flex items-center justify-between mb-3 border border-sky-100 gap-3 hover:border-sky-200 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 md:w-9 md:h-9 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 text-white font-extrabold flex items-center justify-center text-[10px] md:text-xs uppercase shadow-md shadow-sky-200 shrink-0">
                                {{ substr($item->siswa->nama ?? 'A', 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-[10px] md:text-xs font-bold text-slate-800 truncate">{{ $item->siswa->nama ?? '-' }}</h4>
                                <p class="text-[9px] md:text-[10px] text-slate-400">
                                    {{ $item->siswa->kelas->nama ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <span class="px-2 md:px-3 py-0.5 md:py-1 bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 text-[8px] md:text-[10px] font-bold rounded-full border border-amber-200 shrink-0">
                            Pending
                        </span>
                    </div>
                    @endforeach
                    <div class="mt-4 p-3 bg-gradient-to-r from-sky-50 to-blue-50 rounded-xl border border-sky-200">
                        <p class="text-[10px] md:text-xs text-sky-700">Verifikasi pendaftaran dilakukan secara manual oleh ketua ekskul.</p>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <div class="mx-auto w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                            <svg class="w-5 h-5 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-xs md:text-sm font-medium">Tidak ada pendaftaran baru</p>
                        <p class="text-[10px] md:text-xs mt-1">Semua pendaftaran sudah diverifikasi</p>
                    </div>
                @endif
            </div>

            <div class="bg-white p-5 md:p-6 rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .3s">
                <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Laporan Bulanan</h2>
                        <p class="text-[10px] md:text-[11px] text-slate-400 mt-0.5">Draft laporan ekskul</p>
                    </div>
                    <a href="{{ route('pembina.laporan.index') }}" class="text-xs font-bold text-sky-600 hover:underline flex items-center gap-1">
                        Cetak
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                @forelse($laporanDraft ?? [] as $laporan)
                    <div class="p-3 md:p-4 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl border border-amber-100 flex items-center justify-between mb-2 gap-3 hover:border-amber-200 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 md:w-10 md:h-10 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center text-xs font-extrabold shadow-md shadow-amber-200">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->format('m') }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-[10px] md:text-xs font-bold text-slate-800 truncate">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->translatedFormat('F Y') }}
                                </h4>
                                <p class="text-[9px] md:text-[10px] text-slate-400 mt-0.5">{{ $laporan->ekskul->nama_ekskul ?? 'Ekskul' }}</p>
                            </div>
                        </div>
                        <span class="px-2 md:px-2.5 py-0.5 md:py-1 bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 text-[8px] md:text-[10px] font-bold rounded-full border border-amber-200 shrink-0">Draft</span>
                    </div>
                @empty
                    <div class="text-center py-6 md:py-8 text-slate-400">
                        <div class="mx-auto w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                            <svg class="w-5 h-5 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <p class="text-xs md:text-sm font-medium">Belum ada laporan</p>
                        <p class="text-[10px] md:text-xs mt-1">Laporan bulanan akan muncul di sini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection