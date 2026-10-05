@extends('pembina.layout')
@section('title', 'Detail Anggota')

@section('content')
<div class="space-y-6 animate-fade-up">
    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">Detail Anggota</h1>
            <p class="text-xs text-slate-400 mt-0.5">Profil lengkap dan riwayat keanggotaan.</p>
        </div>
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition self-start">Kembali</a>
    </div>

    {{-- PROFILE CARD --}}
    <div class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-slate-100 flex items-center justify-center overflow-hidden shrink-0 border-2 border-sky-200">
                @if($pendaftaran->siswa?->foto_url)
                    <img src="{{ $pendaftaran->siswa->foto_url }}" alt="{{ $pendaftaran->siswa->nama }}" class="w-full h-full object-cover">
                @else
                    <span class="text-2xl font-black text-sky-600">{{ strtoupper(substr($pendaftaran->siswa->nama ?? 'S', 0, 2)) }}</span>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h2 class="text-xl font-extrabold text-slate-900 truncate">{{ $pendaftaran->siswa->nama ?? '-' }}</h2>
                    @if($pendaftaran->siswa?->jabatan === 'ketua')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 whitespace-nowrap">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M2 19l2-9 5 5 3-8 3 8 5-5 2 9H2z"/></svg> Ketua
                        </span>
                    @endif
                    @if($pendaftaran->status === 'peringatan')
                        <span class="px-2.5 py-1 rounded-xl bg-orange-50 text-orange-700 font-bold text-[10px] border border-orange-200">Peringatan</span>
                    @elseif($pendaftaran->status === 'nonaktif')
                        <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-500 font-bold text-[10px] border border-slate-200">Nonaktif</span>
                    @elseif($pendaftaran->status === 'keluar')
                        <span class="px-2.5 py-1 rounded-xl bg-rose-50 text-rose-600 font-bold text-[10px] border border-rose-200">Keluar</span>
                    @else
                        <span class="px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">Aktif</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-[11px]">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-slate-600 font-medium">NIS</span>
                        <span class="ml-auto font-bold text-slate-900">{{ $pendaftaran->siswa->nis ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-slate-600 font-medium">Kelas</span>
                        <span class="ml-auto font-bold text-slate-900">{{ $pendaftaran->siswa->kelas->nama ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-slate-600 font-medium">Bergabung</span>
                        <span class="ml-auto font-bold text-slate-900">{{ \Carbon\Carbon::parse($pendaftaran->tanggal_daftar)->isoFormat('DD MMM Y') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-slate-600 font-medium">Kehadiran</span>
                        <span class="ml-auto font-bold text-slate-900">{{ $persentase }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="border-b border-slate-100">
            <nav class="flex gap-1 px-4 -mb-px" aria-label="Tabs">
                <button type="button" data-tab="profil" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-amber-500 text-amber-600 bg-amber-50">Profil Lengkap</button>
                <button type="button" data-tab="ekskul" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200">Keanggotaan Ekskul</button>
                <button type="button" data-tab="presensi" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200">Presensi</button>
                <button type="button" data-tab="penilaian" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200">Penilaian</button>
                <button type="button" data-tab="riwayat" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200">Riwayat Jabatan</button>
            </nav>
        </div>

        <div class="p-6">
            {{-- TAB: PROFIL LENGKAP --}}
            <div id="tab-profil" class="tab-panel space-y-6">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Identitas Siswa
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    @foreach([
                        'NIS' => $pendaftaran->siswa->nis,
                        'Nama Lengkap' => $pendaftaran->siswa->nama,
                        'Tempat Lahir' => $pendaftaran->siswa->tempat_lahir,
                        'Tanggal Lahir' => $pendaftaran->siswa->tanggal_lahir ? \Carbon\Carbon::parse($pendaftaran->siswa->tanggal_lahir)->isoFormat('DD MMMM YYYY') : '-',
                        'Jenis Kelamin' => $pendaftaran->siswa->jenis_kelamin ? ucfirst($pendaftaran->siswa->jenis_kelamin) : '-',
                        'Agama' => $pendaftaran->siswa->agama,
                        'Alamat' => $pendaftaran->siswa->alamat,
                        'No. Telepon' => $pendaftaran->siswa->no_telp,
                        'Email' => $pendaftaran->siswa->email,
                        'MedSos' => $pendaftaran->siswa->medsos,
                    ] as $label => $value)
                        <div>
                            <dt class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</dt>
                            <dd class="text-slate-900 font-medium mt-0.5 break-all">{{ $value ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>

                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 mt-6 pt-4 border-t border-slate-100">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Data Akademik
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    @foreach([
                        'Kelas' => $pendaftaran->siswa->kelas->nama,
                        'Tingkat' => $pendaftaran->siswa->kelas->tingkat ? strtoupper($pendaftaran->siswa->kelas->tingkat) : '-',
                        'Jurusan' => $pendaftaran->siswa->kelas->jurusan_label ?? $pendaftaran->siswa->kelas->jurusan ?? '-',
                        'Angkatan' => $pendaftaran->siswa->angkatan ?? '-',
                    ] as $label => $value)
                        <div>
                            <dt class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</dt>
                            <dd class="text-slate-900 font-medium mt-0.5">{{ $value ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- TAB: KEANGGOTAAN EKSKUL --}}
            <div id="tab-ekskul" class="tab-panel hidden space-y-6">
                @foreach($pendaftaran->siswa->pendaftarans as $p)
                    @php $isCurrent = $p->id === $pendaftaran->id; @endphp
                    <div class="rounded-2xl border {{ $isCurrent ? 'border-sky-300 dark:border-sky-600 bg-sky-50/50 dark:bg-sky-950/30 ring-2 ring-sky-200 dark:ring-sky-800' : 'border-slate-100 dark:border-neutral-700' }} p-5 relative">
                        @if($isCurrent)
                            <span class="absolute -top-2 -right-2 px-2 py-0.5 bg-sky-100 dark:bg-sky-950/30 text-sky-700 dark:text-sky-300 text-[10px] font-bold rounded-full">EKSKUL INI</span>
                        @endif
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h4 class="font-extrabold text-slate-900 dark:text-white">{{ $p->ekskul->nama_ekskul ?? '-' }}</h4>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $p->ekskul->kategori ?? '-' }} {{ $p->ekskul->pembina ? '· Pembina: ' . $p->ekskul->pembina->nama : '' }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold
                                    @if($p->status === 'diterima') bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50
                                    @elseif($p->status === 'peringatan') bg-orange-50 dark:bg-orange-950/30 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800/50
                                    @elseif($p->status === 'nonaktif') bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-neutral-700
                                    @elseif($p->status === 'keluar') bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50
                                    @else bg-slate-50 dark:bg-neutral-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-neutral-700 @endif">
                                    {{ ucfirst($p->status) }}
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-400 text-[10px] font-bold">Daftar: {{ \Carbon\Carbon::parse($p->tanggal_daftar)->isoFormat('DD MMM Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- TAB: PRESENSI --}}
            <div id="tab-presensi" class="tab-panel hidden space-y-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-emerald-50 dark:bg-emerald-950/30 p-4 rounded-2xl border border-emerald-100 dark:border-emerald-800/50 text-center">
                        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase">Total Kegiatan</p>
                        <p class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-300">{{ $totalPresensi }}</p>
                    </div>
                    <div class="bg-sky-50 dark:bg-sky-950/30 p-4 rounded-2xl border border-sky-100 dark:border-sky-800/50 text-center">
                        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase">Hadir</p>
                        <p class="text-2xl font-extrabold text-sky-700 dark:text-sky-300">{{ $hadir }}</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-950/30 p-4 rounded-2xl border border-amber-100 dark:border-amber-800/50 text-center">
                        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase">Izin</p>
                        <p class="text-2xl font-extrabold text-amber-700 dark:text-amber-300">{{ (int) ($presensiStats->izin ?? 0) }}</p>
                    </div>
                    <div class="bg-rose-50 dark:bg-rose-950/30 p-4 rounded-2xl border border-rose-100 dark:border-rose-800/50 text-center">
                        <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase">Alpha</p>
                        <p class="text-2xl font-extrabold text-rose-600 dark:text-rose-400">{{ (int) ($presensiStats->alpha ?? 0) }}</p>
                    </div>
                </div>

                <div class="bg-slate-50 dark:bg-neutral-800 p-4 rounded-2xl border border-slate-100 dark:border-neutral-700">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-bold text-slate-900 dark:text-white">Persentase Kehadiran</p>
                        <span class="px-3 py-1.5 rounded-xl font-extrabold text-sm
                            @if($persentase >= 80) bg-emerald-100 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300
                            @elseif($persentase >= 50) bg-amber-100 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300
                            @else bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 @endif">
                            {{ $persentase }}%
                        </span>
                    </div>
                    <div class="h-2 bg-slate-200 dark:bg-neutral-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all
                            @if($persentase >= 80) bg-emerald-400
                            @elseif($persentase >= 50) bg-amber-400
                            @else bg-rose-400 @endif"
                            style="width: {{ $persentase }}%"></div>
                    </div>
                </div>

                {{-- Detail presensi per kegiatan --}}
                @php
                    $presensiDetail = \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                        ->with('kegiatan')
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->groupBy('kegiatan_id');
                @endphp
                @if($presensiDetail->isNotEmpty())
                    <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-6 mb-3">Detail Per Kegiatan</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-neutral-800 text-slate-500 dark:text-slate-400">
                                    <th class="p-3 text-left">Tanggal</th>
                                    <th class="p-3 text-left">Kegiatan</th>
                                    <th class="p-3 text-left">Jenis</th>
                                    <th class="p-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-neutral-700">
                                @foreach($presensiDetail as $kegiatanId => $items)
                                    @php $kegiatan = $items->first()->kegiatan; @endphp
                                    @foreach($items as $item)
                                        <tr class="hover:bg-sky-50/30 dark:hover:bg-sky-950/30">
                                            <td class="p-3 text-slate-600 dark:text-slate-400">{{ \Carbon\Carbon::parse($item->created_at)->isoFormat('DD MMM Y') }}</td>
                                            <td class="p-3 font-medium text-slate-900 dark:text-white">{{ $kegiatan->nama_kegiatan ?? '-' }}</td>
                                            <td class="p-3">
                                                <span class="px-2 py-1 rounded-lg text-[10px] font-bold
                                                    @if($kegiatan->isEvent()) bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/50
                                                    @else bg-sky-50 dark:bg-sky-950/30 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800/50 @endif">
                                                    {{ $kegiatan->isEvent() ? 'Event' : 'Rutin' }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-center">
                                                @php $s = $item->status; @endphp
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold
                                                    @if($s === 'hadir') bg-emerald-100 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50
                                                    @elseif($s === 'izin') bg-sky-100 dark:bg-sky-950/30 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800/50
                                                    @elseif($s === 'sakit') bg-amber-100 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50
                                                    @else bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50 @endif">
                                                    {{ ucfirst($s) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-slate-400 dark:text-slate-500 text-sm py-8">Belum ada data presensi.</p>
                @endif
            </div>

            {{-- TAB: PRESTASI --}}
            <div id="tab-penilaian" class="tab-panel hidden space-y-4">
                @php $penilaian = \App\Models\Prestasi::where('ekskul_id', $pendaftaran->ekskul_id)->latest()->get(); @endphp
                @if($penilaian->isNotEmpty())
                    @foreach($penilaian as $p)
                        <div class="bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-700 rounded-2xl p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-bold text-slate-900 dark:text-white">{{ $p->judul }}</h5>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $p->kategori ?? 'Kategori tidak ditentukan' }} · {{ $p->tahun ?? 'Tahun tidak ditentukan' }}</p>
                                </div>
                            </div>
                            @if($p->foto)
                                <img src="{{ asset('storage/'.$p->foto) }}" alt="{{ $p->judul }}" class="mt-3 rounded-xl max-h-40 object-cover">
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-slate-400 dark:text-slate-500">
                        <svg class="w-12 h-12 mx-auto text-slate-200 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <p class="mt-2 font-medium">Belum ada prestasi untuk ekskul ini.</p>
                    </div>
                @endif
            </div>

            {{-- TAB: RIWAYAT JABATAN --}}
            <div id="tab-riwayat" class="tab-panel hidden space-y-4">
                @php $riwayat = $pendaftaran->siswa->riwayatJabatans()->where('ekskul_id', $pendaftaran->ekskul_id)->latest('mulai')->get(); @endphp
                @if($riwayat->isNotEmpty())
                    @foreach($riwayat as $r)
                        <div class="bg-white dark:bg-neutral-900 border border-slate-100 dark:border-neutral-700 rounded-2xl p-4 {{ $r->isActive() ? 'ring-2 ring-amber-200 dark:ring-amber-800 bg-amber-50/30 dark:bg-amber-950/30' : '' }}">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold
                                            @if($r->isActive()) bg-amber-100 dark:bg-amber-950/30 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-800/50
                                            @elseif($r->alasan_selesai === 'diganti') bg-sky-100 dark:bg-sky-950/30 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800/50
                                            @elseif($r->alasan_selesai === 'dicopot') bg-rose-100 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/50
                                            @elseif($r->alasan_selesai === 'keluar') bg-violet-100 dark:bg-violet-950/30 text-violet-700 dark:text-violet-300 border border-violet-200 dark:border-violet-800/50
                                            @else bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-neutral-700 @endif">
                                            {{ $r->isActive() ? 'Berlangsung' : ucfirst($r->alasan_selesai) }}
                                        </span>
                                        @if($r->isActive())
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" title="Periode aktif"></span>
                                        @endif
                                    </div>
                                    <p class="font-bold text-slate-900 dark:text-white">Ketua Ekskul {{ $r->ekskul->nama_ekskul ?? '-' }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $r->periodeText() }} · {{ $r->durasiHari() }} hari</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-slate-400 dark:text-slate-500">
                        <svg class="w-12 h-12 mx-auto text-slate-200 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="mt-2 font-medium">Belum pernah menjabat Ketua.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.tab-panel');

    tabs.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.tab;
            tabs.forEach(b => b.classList.remove('border-amber-500', 'text-amber-600', 'bg-amber-50'));
            tabs.forEach(b => b.classList.add('border-transparent', 'text-slate-400'));
            btn.classList.add('border-amber-500', 'text-amber-600', 'bg-amber-50');
            btn.classList.remove('border-transparent', 'text-slate-400');

            panels.forEach(p => {
                p.classList.toggle('hidden', p.id !== 'tab-' + target);
            });
        });
    });
});
</script>
@endsection