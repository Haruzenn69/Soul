@extends('layouts.kesiswaan')
@section('title', 'Riwayat Nilai Siswa - ' . $siswa->nama)

@section('content')
    <div class="bg-[#F8FAFC] -mx-4 md:-mx-8 px-4 md:px-8 py-6 md:py-8 min-h-[calc(100vh-5rem)] space-y-6 animate-fade-up">
        
        {{-- BACK LINK & HEADER --}}
        <div>
            <a href="{{ route('kesiswaan.laporan-penilaian.historis-siswa') }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-blue-600 transition mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Riwayat Siswa
            </a>

            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-5 md:p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-sky-600 text-white flex items-center justify-center font-extrabold text-xl shadow-md shadow-sky-500/20 shrink-0">
                        @if($siswa->foto)
                            <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama }}" class="w-full h-full object-cover rounded-2xl">
                        @else
                            {{ strtoupper(mb_substr($siswa->nama ?? '?', 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-lg md:text-xl font-extrabold text-slate-900">{{ $siswa->nama }}</h1>
                            @if($tahunAjaranAktif)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    TA Aktif: {{ $tahunAjaranAktif->nama }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            NIS: <span class="font-bold text-slate-700">{{ $siswa->nis }}</span> &middot;
                            Kelas: <span class="font-bold text-slate-700">{{ $siswa->kelas?->nama ?? '-' }}</span> &middot;
                            Angkatan: <span class="font-bold text-slate-700">{{ $siswa->angkatan ?? '-' }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print()"
                            class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 shadow-sm transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak Riwayat
                    </button>
                </div>
            </div>
        </div>

        {{-- SUMMARY STATS --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-xl md:text-2xl font-extrabold text-slate-900">{{ $totalPenilaian }}</p>
                <p class="text-xs font-bold text-slate-400 mt-0.5">Total Penilaian Terbit</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-xl md:text-2xl font-extrabold text-blue-600">{{ number_format($rataNilaiKeseluruhan, 1) }}</p>
                <p class="text-xs font-bold text-slate-400 mt-0.5">Rata-rata Nilai Akhir</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-xl md:text-2xl font-extrabold text-emerald-600">{{ number_format((float) $nilaiTertinggi, 1) }}</p>
                <p class="text-xs font-bold text-slate-400 mt-0.5">Nilai Tertinggi</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-xl md:text-2xl font-extrabold text-amber-600">{{ $predikatTerbaik }}</p>
                <p class="text-xs font-bold text-slate-400 mt-0.5">Predikat Terbaik</p>
            </div>
        </div>

        {{-- HISTORIC NILAI GROUPED BY TAHUN AJARAN & SEMESTER --}}
        @if ($groupedPenilaians->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-12 md:p-16 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-500 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-slate-900">Belum Ada Riwayat Penilaian</h2>
                <p class="text-xs text-slate-400 mt-1">Siswa ini belum memiliki penilaian ekstrakurikuler yang telah dikirim oleh pembina di tahun ajaran manapun.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach ($groupedPenilaians as $tahunAjaran => $semesters)
                    @php
                        $isAktif = $tahunAjaranAktif && trim($tahunAjaranAktif->nama) === trim($tahunAjaran);
                    @endphp

                    <div class="bg-white rounded-2xl border {{ $isAktif ? 'border-sky-300 ring-1 ring-sky-100' : 'border-slate-200/70' }} shadow-sm overflow-hidden">
                        
                        {{-- CARD HEADER TAHUN AJARAN --}}
                        <div class="p-4 md:p-5 bg-gradient-to-r {{ $isAktif ? 'from-sky-50/60 to-white' : 'from-slate-50 to-white' }} border-b border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $isAktif ? 'bg-sky-500 text-white shadow-sm shadow-sky-500/20' : 'bg-slate-200 text-slate-600' }} flex items-center justify-center font-bold text-xs shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-base font-extrabold text-slate-900">Tahun Ajaran {{ $tahunAjaran }}</h2>
                                        @if($isAktif)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Aktif Saat Ini
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                Historis
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">Rekap penilaian ekstrakurikuler pada tahun ajaran ini</p>
                                </div>
                            </div>
                        </div>

                        {{-- SEMESTERS --}}
                        <div class="p-4 md:p-6 space-y-6">
                            @foreach ($semesters as $semester => $items)
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $semester === 'Ganjil' ? 'bg-amber-400' : 'bg-blue-500' }}"></span>
                                        <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">Semester {{ $semester }}</h3>
                                        <span class="text-xs text-slate-400">({{ $items->count() }} Ekskul Dinilai)</span>
                                    </div>

                                    <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                                        <table class="w-full text-left text-xs min-w-[880px]">
                                            <thead class="bg-slate-50/80 text-xs uppercase tracking-wider text-slate-400">
                                                <tr>
                                                    <th class="px-4 py-3 font-bold">Ekskul</th>
                                                    <th class="px-4 py-3 font-bold">Pembina Yang Menilai</th>
                                                    <th class="px-4 py-3 font-bold text-center">Kehadiran</th>
                                                    <th class="px-4 py-3 font-bold text-center">Sikap</th>
                                                    <th class="px-4 py-3 font-bold text-center">Keaktifan</th>
                                                    <th class="px-4 py-3 font-bold text-center">Keterampilan</th>
                                                    <th class="px-4 py-3 font-bold text-center">Nilai Akhir</th>
                                                    <th class="px-4 py-3 font-bold text-center">Predikat</th>
                                                    <th class="px-4 py-3 font-bold">Catatan Pembina</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach ($items as $p)
                                                    @php
                                                        $ekskul = $p->pendaftaran?->ekskul;
                                                        $pembina = $p->penilai ?? $ekskul?->pembina;
                                                    @endphp
                                                    <tr class="hover:bg-slate-50/50 transition">
                                                        <td class="px-4 py-3">
                                                            <div class="font-bold text-slate-900 leading-tight">
                                                                {{ $ekskul?->nama_ekskul ?? '-' }}
                                                            </div>
                                                            <div class="text-[11px] text-slate-400">
                                                                {{ $ekskul?->kategori?->nama_kategori ?? 'Umum' }}
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <div class="font-bold text-slate-800 leading-tight">
                                                                {{ $pembina?->nama ?? '-' }}
                                                            </div>
                                                            <div class="text-[11px] text-slate-400">
                                                                NIP: {{ $pembina?->nip ?? '-' }}
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3 text-center">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold {{ $p->persentase_kehadiran >= 70 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-600 border border-red-200' }}">
                                                                {{ number_format((float) $p->persentase_kehadiran, 1) }}%
                                                            </span>
                                                            <span class="block text-[10px] text-slate-400 mt-0.5">
                                                                {{ $p->total_hadir }}/{{ $p->total_pertemuan }} Hadir
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 text-center font-bold text-slate-900">
                                                            {{ number_format((float) $p->nilai_sikap, 1) }}
                                                        </td>
                                                        <td class="px-4 py-3 text-center font-bold text-slate-900">
                                                            {{ number_format((float) $p->nilai_keaktifan, 1) }}
                                                        </td>
                                                        <td class="px-4 py-3 text-center font-bold text-slate-900">
                                                            {{ number_format((float) $p->nilai_keterampilan, 1) }}
                                                        </td>
                                                        <td class="px-4 py-3 text-center font-extrabold text-slate-900 text-sm">
                                                            {{ number_format((float) $p->nilai_akhir, 2) }}
                                                        </td>
                                                        <td class="px-4 py-3 text-center">
                                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg border font-extrabold text-xs {{ \App\Models\Penilaian::warnaPredikat((string) $p->predikat) }}">
                                                                {{ $p->predikat }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 text-xs text-slate-600 max-w-[220px]">
                                                            {{ $p->catatan ?: '-' }}
                                                            @if($p->dikirim_at)
                                                                <span class="block text-[10px] text-slate-400 mt-0.5">
                                                                    Disahkan: {{ $p->dikirim_at->translatedFormat('d M Y') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
