@extends('pembina.layout')
@section('title', 'Dashboard Pembina')

@section('content')
    {{-- HERO --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 lg:pb-14 text-white shadow-xl shadow-sky-200 animate-fade-up">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-20 -right-10 w-72 h-72 rounded-full bg-amber-200/40 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute top-10 right-1/4 w-16 h-16 rounded-full bg-amber-300/50 blur-2xl"></div>
        </div>

        <div class="relative grid lg:grid-cols-5 gap-8 items-center">
            <!-- TEXT + CTA -->
            <div class="lg:col-span-3">
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
                <p class="text-sm text-white/85 mt-1.5 font-medium">
                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }} · Semester Ganjil 2026/2027
                </p>
                <p class="text-xs text-white/70 mt-3 max-w-lg leading-relaxed">
                    Pantau keanggotaan, presensi, dan laporan ekskul binaanmu dengan mudah di page ini.
                </p>

                <div class="flex gap-3 flex-wrap mt-6">
                    <a href="{{ route('pembina.anggota') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Kelola Anggota
                    </a>
                    <a href="{{ route('pembina.presensi') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-2xl hover:bg-white/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Lihat Presensi
                    </a>
                </div>
            </div>

            <!-- ILLUSTRATIVE PANEL + FLOATING STATS (desktop only) -->
            <div class="lg:col-span-2 relative hidden lg:block">
                <div class="relative h-52 rounded-3xl bg-white/10 backdrop-blur border border-white/20 overflow-hidden flex items-center justify-center">
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-amber-300/30 blur-2xl"></div>
                    <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-white/20 blur-2xl"></div>
                    <div class="relative w-24 h-24 rounded-3xl bg-white/20 backdrop-blur border border-white/30 flex items-center justify-center text-white shadow-inner animate-floaty">
                        <svg class="w-11 h-11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- floating stat card overlapping bottom edge -->
                <div class="absolute -bottom-10 left-3 right-3 grid grid-cols-3 gap-2 bg-white rounded-2xl shadow-xl shadow-sky-900/10 p-3">
                    <div class="text-center border-r border-slate-100 pr-1">
                        <p class="text-base font-extrabold text-sky-700 leading-none">{{ $ekskul ? 1 : 0 }}</p>
                        <p class="text-[9px] text-slate-400 font-semibold mt-1">Ekskul</p>
                    </div>
                    <div class="text-center border-r border-slate-100 px-1">
                        <p class="text-base font-extrabold text-amber-600 leading-none">{{ $anggotaAktifCount ?? 0 }}</p>
                        <p class="text-[9px] text-slate-400 font-semibold mt-1">Anggota</p>
                    </div>
                    <div class="text-center pl-1">
                        <p class="text-base font-extrabold text-sky-700 leading-none">{{ count($pendaftaranPending ?? []) }}</p>
                        <p class="text-[9px] text-slate-400 font-semibold mt-1">Pendaftaran</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="relative mt-6 pt-5 border-t border-white/15 grid grid-cols-3 gap-3">
            <a href="{{ route('pembina.anggota') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Anggota</p>
                    <p class="text-[10px] text-white/70 mt-1 leading-none">Data Keanggotaan</p>
                </div>
            </a>
            <a href="{{ route('pembina.presensi') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Presensi</p>
                    <p class="text-[10px] text-white/70 mt-1 leading-none">Rekap Kehadiran</p>
                </div>
            </a>
            <a href="{{ route('pembina.laporan.index') }}" class="group flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-white/10 backdrop-blur border border-white/10 hover:bg-white/20 transition-all">
                <div class="w-8 h-8 rounded-lg bg-amber-300/40 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold leading-none">Laporan</p>
                    <p class="text-[10px] text-white/70 mt-1 leading-none">Cetak & Approve</p>
                </div>
            </a>
        </div>
    </div>

    {{-- STATS CARDS - MOBILE/TABLET --}}
    <div class="grid grid-cols-3 gap-3 lg:hidden">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 animate-fade-up" style="animation-delay: .1s">
            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Ekskul Dibina</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ $ekskul ? 1 : 0 }}</h3>
            <p class="text-[9px] md:text-[11px] font-semibold {{ $ekskul ? 'text-emerald-600' : 'text-amber-600' }} mt-0.5 md:mt-1">{{ $ekskul ? 'Aktif' : 'Belum' }}</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-100 to-yellow-50 border border-amber-200 p-3 md:p-5 shadow-md shadow-amber-100 animate-fade-up" style="animation-delay: .2s">
            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Anggota Aktif</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-amber-600">{{ $anggotaAktifCount ?? 0 }}</h3>
            <p class="text-[9px] md:text-[11px] font-semibold text-amber-700 mt-0.5 md:mt-1">Siswa</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-100 to-white border border-sky-200 p-3 md:p-5 shadow-md shadow-sky-100 animate-fade-up" style="animation-delay: .3s">
            <p class="text-[8px] md:text-[10px] font-bold text-slate-400 tracking-wider uppercase">Pendaftaran</p>
            <h3 class="text-xl md:text-3xl font-extrabold mt-0.5 md:mt-1.5 text-sky-700">{{ count($pendaftaranPending ?? []) }}</h3>
            <p class="text-[9px] md:text-[11px] font-semibold text-sky-600 mt-0.5 md:mt-1">Menunggu</p>
        </div>
    </div>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:pt-6">

        <!-- LEFT COLUMN -->
        <div class="lg:col-span-2 space-y-6">
            <!-- DAFTAR ANGGOTA -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .15s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Daftar Anggota Ekskul</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Kelola keanggotaan ekskul binaan</p>
                    </div>
                    <span class="text-[11px] font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ $anggotaAktifCount ?? 0 }} Aktif</span>
                </div>

                @if(isset($anggota) && count($anggota) > 0)
                    <div class="overflow-x-auto">
                        <table class="card-table w-full text-xs">
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
                                            @if($item->siswa->jabatan == 'ketua') Ketua @else Anggota @endif
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
                    <div class="text-center py-10">
                        <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-300 border border-sky-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-500 mt-3">Belum ada anggota</p>
                        <p class="text-[11px] text-slate-400 mt-1">Belum ada siswa yang terdaftar di ekskul ini</p>
                    </div>
                @endif
            </div>

            <!-- AGENDA KEGIATAN MENDATANG -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .2s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Kegiatan Mendatang</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Semua agenda ekskul yang akan datang</p>
                    </div>
                    <span class="text-[11px] font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ $kegiatanMendatang->count() ?? 0 }} Agenda</span>
                </div>

                @if(($kegiatanMendatang ?? collect())->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="px-6 py-3">Kegiatan</th>
                                    <th class="px-6 py-3">Tanggal</th>
                                    <th class="px-6 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kegiatanMendatang as $index => $kegiatan)
                                    <tr class="border-t border-sky-50 hover:bg-sky-50/50 transition-colors">
                                        <td class="px-6 py-3.5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 shrink-0 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex flex-col items-center justify-center shadow-sm shadow-sky-200">
                                                    <span class="text-xs font-extrabold leading-none">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('d') }}</span>
                                                    <span class="text-[7px] font-bold uppercase leading-tight opacity-80">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->format('M') }}</span>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="text-xs font-bold text-slate-800">{{ $kegiatan->materi ?? 'Kegiatan' }}</span>
                                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $kegiatan->ekskul->nama_ekskul ?? 'Ekskul' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3.5">
                                            <span class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('dddd, DD MMM Y') }}</span>
                                        </td>
                                        <td class="px-6 py-3.5">
                                            @if($index === 0)
                                                <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-100 border border-amber-200 text-amber-700 text-[10px] font-bold">Terdekat</span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 rounded-full bg-sky-50 border border-sky-100 text-sky-600 text-[10px] font-bold">Terjadwal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-300 border border-sky-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-500 mt-3">Belum ada agenda mendatang</p>
                        <p class="text-[11px] text-slate-400 mt-1">Agenda ekskul akan muncul di sini.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="space-y-6">

            <!-- PENDAFTARAN SISWA -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .25s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Pendaftaran Siswa</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Menunggu verifikasi</p>
                    </div>
                    <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 border border-amber-100 px-3 py-1.5 rounded-full">{{ count($pendaftaranPending ?? []) }} Pending</span>
                </div>

                @if(isset($pendaftaranPending) && count($pendaftaranPending) > 0)
                    <div class="p-5">
                        @foreach($pendaftaranPending as $item)
                        <div class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-xl flex items-center justify-between mb-3 border border-sky-100 gap-3 hover:border-sky-200 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-sky-400 to-blue-500 text-white font-extrabold flex items-center justify-center text-xs uppercase shadow-md shadow-sky-200 shrink-0">
                                    {{ substr($item->siswa->nama ?? 'A', 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">{{ $item->siswa->nama ?? '-' }}</h4>
                                    <p class="text-[10px] text-slate-400">{{ $item->siswa->kelas->nama ?? '-' }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200 shrink-0">Pending</span>
                        </div>
                        @endforeach
                        <div class="p-3 bg-gradient-to-r from-sky-50 to-blue-50 rounded-xl border border-sky-200">
                            <p class="text-xs text-sky-700">Verifikasi pendaftaran dilakukan secara manual oleh ketua ekskul.</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-300 border border-sky-100">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-slate-500 mt-3">Tidak ada pendaftaran baru</p>
                        <p class="text-[11px] text-slate-400 mt-1">Semua pendaftaran sudah diproses</p>
                    </div>
                @endif
            </div>

            <!-- LAPORAN BULANAN -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .3s">
                <div class="px-6 py-5 flex justify-between items-center border-b border-sky-50">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Laporan Bulanan</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Draft laporan ekskul</p>
                    </div>
                    <a href="{{ route('pembina.laporan.index') }}" class="text-xs font-bold text-sky-600 hover:underline flex items-center gap-1">
                        Cetak
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="p-5">
                    @forelse($laporanDraft ?? [] as $laporan)
                        <div class="p-3.5 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl border border-amber-100 flex items-center justify-between mb-3 gap-3 hover:border-amber-200 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 flex items-center justify-center text-xs font-extrabold shadow-md shadow-amber-200 shrink-0">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->format('m') }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-slate-800 truncate">{{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->translatedFormat('F Y') }}</h4>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $laporan->ekskul->nama_ekskul ?? 'Ekskul' }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 text-[10px] font-bold rounded-full border border-amber-200 shrink-0">Draft</span>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 flex items-center justify-center text-sky-300 border border-sky-100">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-slate-500 mt-3">Belum ada laporan</p>
                            <p class="text-[11px] text-slate-400 mt-1">Laporan bulanan akan muncul di sini</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
@endsection