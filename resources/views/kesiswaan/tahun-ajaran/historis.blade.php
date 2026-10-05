@extends('layouts.kesiswaan')
@section('title', 'Data Historis Tahun Ajaran ' . $tahunAjaran->nama)

@section('content')
<div class="space-y-6 animate-fade-up">

    {{-- HEADER CARD HISTORIS --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900 p-6 md:p-8 text-white shadow-xl shadow-slate-900/20">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <a href="{{ route('kesiswaan.tahun-ajaran.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs text-slate-300 hover:text-white font-bold transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        Tahun Ajaran
                    </a>
                    <span class="text-slate-500">/</span>
                    <span class="text-xs text-white font-bold">Historis {{ $tahunAjaran->nama }}</span>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        Arsip Tahun Ajaran {{ $tahunAjaran->nama }}
                    </h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-400/30">
                        Historis (Read-Only)
                    </span>
                </div>

                <p class="text-xs text-slate-300 mt-2 max-w-xl leading-relaxed">
                    Data riwayat siswa pada tahun ajaran <span class="font-bold text-white">{{ $tahunAjaran->nama }}</span>. Menampilkan nama siswa, kelas, ekskul yang diikuti, dan jabatan.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.tahun-ajaran.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-xl hover:bg-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Daftar TA
                </a>
            </div>
        </div>
    </div>

    {{-- PENCARIAN & FILTER KELAS --}}
    <form method="GET" action="{{ route('kesiswaan.tahun-ajaran.historis', $tahunAjaran) }}" class="bg-white p-4 rounded-3xl border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama siswa..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-slate-400 transition">
            </div>

            <div class="w-full sm:w-56">
                <select name="kelas_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-slate-400 transition">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kls)
                        <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>
                            {{ $kls->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Filter
                </button>
                @if (request()->filled('q') || request()->filled('kelas_id'))
                    <a href="{{ route('kesiswaan.tahun-ajaran.historis', $tahunAjaran) }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- TABEL HISTORIS: HANYA NAMA, KELAS, EKSKUL, JABATAN --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 tracking-wider uppercase">
                        <th class="py-3.5 px-5 w-16 text-center">No</th>
                        <th class="py-3.5 px-5">Nama Siswa</th>
                        <th class="py-3.5 px-5">Kelas</th>
                        <th class="py-3.5 px-5">Ekskul</th>
                        <th class="py-3.5 px-5">Jabatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($siswas as $index => $s)
                        @php
                            $userEkskuls = $ekskulData->get($s->id, collect());
                            $ekskulNames = $userEkskuls->map(fn($p) => $p->ekskul?->nama)->filter()->unique()->values();

                            // Determine class name for this historical TA
                            $history = $s->classHistories->where('tahun_ajaran', $tahunAjaran->nama)->first();
                            $kelasNama = $history?->kelas_asal ?? $s->kelas?->nama ?? '-';

                            $jabatanLabel = match($s->jabatan) {
                                'ketua' => 'Ketua',
                                'anggota' => 'Anggota',
                                default => 'Siswa',
                            };
                            $jabatanBadge = match($s->jabatan) {
                                'ketua' => 'bg-amber-100 text-amber-800 border-amber-200',
                                'anggota' => 'bg-sky-100 text-sky-800 border-sky-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-5 text-center font-bold text-slate-400">
                                {{ $siswas->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-5 font-bold text-slate-900">
                                {{ $s->nama }}
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-slate-700">
                                {{ $kelasNama }}
                            </td>
                            <td class="py-3.5 px-5">
                                @if($ekskulNames->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($ekskulNames as $eName)
                                            <span class="inline-block px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                                {{ $eName }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $jabatanBadge }}">
                                    {{ $jabatanLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Tidak ada data historis siswa pada tahun ajaran ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row gap-2 justify-between items-center">
            <p class="text-[11px] text-slate-400 font-semibold">
                Menampilkan {{ $siswas->firstItem() ?? 0 }}-{{ $siswas->lastItem() ?? 0 }} dari {{ $siswas->total() }} siswa historis
            </p>
            {{ $siswas->links() }}
        </div>
    </div>

</div>
@endsection
