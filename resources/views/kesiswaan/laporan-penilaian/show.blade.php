@extends('layouts.kesiswaan')
@section('title', 'Detail Laporan Penilaian')

@section('content')
    <div class="bg-[#F8FAFC] -mx-4 md:-mx-8 px-4 md:px-8 py-6 md:py-8 min-h-[calc(100vh-5rem)] space-y-6 animate-fade-up">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <a href="{{ route('kesiswaan.laporan-penilaian.index') }}"
                   class="inline-flex items-center gap-1.5 text-[11px] font-bold text-slate-400 hover:text-blue-600 transition mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Laporan Penilaian
                </a>
                <h1 class="text-lg md:text-2xl font-extrabold text-slate-900">{{ $ekskul->nama_ekskul }}</h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    Pembina {{ $ekskul->pembina?->nama ?? '-' }} &middot; Pelatih {{ $ekskul->pelatih?->nama ?? '-' }}
                    &middot; Periode <span class="font-semibold text-blue-600">{{ $periode }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kesiswaan.laporan-penilaian.download-pdf', $ekskul->id) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-white hover:bg-red-50 text-slate-700 text-xs font-bold rounded-lg border border-slate-200 shadow-sm transition">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-2xl font-extrabold text-slate-900">{{ $summary['totalAnggota'] }}</p>
                <p class="text-[11px] font-bold text-slate-400 mt-0.5">Total Anggota</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-2xl font-extrabold text-amber-600">{{ $summary['sudahDinilai'] }}</p>
                <p class="text-[11px] font-bold text-slate-400 mt-0.5">Sudah Dinilai</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-2xl font-extrabold text-blue-600">{{ number_format((float) $summary['rataAkhir'], 1) }}</p>
                <p class="text-[11px] font-bold text-slate-400 mt-0.5">Rata-rata Nilai</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4">
                <p class="text-2xl font-extrabold text-emerald-600">{{ number_format((float) $summary['tertinggi'], 1) }}</p>
                <p class="text-[11px] font-bold text-slate-400 mt-0.5">Nilai Tertinggi</p>
            </div>
        </div>

        @php
            $dinilai = $rows->filter(fn ($r) => $r->penilaian !== null);
        @endphp

        @if ($dinilai->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-12 md:p-16 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-500 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-slate-900">Belum Ada Penilaian Terkirim</h2>
                <p class="text-xs text-slate-400 mt-1">Pembina belum mengirim laporan penilaian untuk ekskul ini.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="p-4 md:p-5 border-b border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Nilai Anggota</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Nilai sudah terkunci sejak dikirim pembina.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-lg border border-emerald-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $dinilai->count() }} dari {{ $rows->count() }} anggota dinilai
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[980px]">
                        <thead class="bg-slate-50/80 text-[10px] uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-bold">#</th>
                                <th class="px-4 py-3 font-bold">Anggota</th>
                                <th class="px-4 py-3 font-bold text-center">Pertemuan</th>
                                <th class="px-4 py-3 font-bold text-center">% Hadir</th>
                                <th class="px-4 py-3 font-bold text-center">Sikap</th>
                                <th class="px-4 py-3 font-bold text-center">Keaktifan</th>
                                <th class="px-4 py-3 font-bold text-center">Keterampilan</th>
                                <th class="px-4 py-3 font-bold text-center">Nilai Akhir</th>
                                <th class="px-4 py-3 font-bold text-center">Predikat</th>
                                <th class="px-4 py-3 font-bold">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($dinilai as $row)
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-4 py-3 text-slate-400 font-semibold">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3 min-w-[170px]">
                                            <div class="w-9 h-9 rounded-full bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center font-extrabold text-xs shrink-0">
                                                {{ strtoupper(mb_substr($row->siswa?->nama ?? '?', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 leading-tight truncate">{{ $row->siswa?->nama }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $row->siswa?->nis }} &middot; {{ $row->kelas }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-700 font-semibold">
                                        {{ $row->total_pertemuan }}
                                        <span class="block text-[9px] text-slate-400 font-medium">H:{{ $row->total_hadir }} I:{{ $row->total_izin }} S:{{ $row->total_sakit }} A:{{ $row->total_alpha }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $row->persentase_kehadiran >= 70 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-600 border border-red-200' }}">
                                            {{ number_format((float) $row->persentase_kehadiran, 1) }}%
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-900">{{ number_format((float) $row->nilai_sikap, 1) }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-900">{{ number_format((float) $row->nilai_keaktifan, 1) }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-900">{{ number_format((float) $row->nilai_keterampilan, 1) }}</td>
                                    <td class="px-4 py-3 text-center font-extrabold text-slate-900">{{ number_format((float) $row->nilai_akhir, 2) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl border font-extrabold text-xs {{ \App\Models\Penilaian::warnaPredikat((string) $row->predikat) }}">
                                            {{ $row->predikat }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-[11px] text-slate-600 max-w-[200px]">{{ $row->catatan }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4 md:p-5">
                <h3 class="text-xs font-bold text-slate-900 mb-3">Bobot Komponen</h3>
                <div class="flex flex-wrap gap-2 text-[11px] font-bold">
                    <span class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 border border-blue-100">Kehadiran 30%</span>
                    <span class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-100">Sikap 25%</span>
                    <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">Keaktifan 25%</span>
                    <span class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 border border-rose-100">Keterampilan 20%</span>
                </div>
            </div>
        @endif
    </div>
@endsection