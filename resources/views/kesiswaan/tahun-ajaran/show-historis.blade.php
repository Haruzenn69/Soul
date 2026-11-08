@extends('layouts.kesiswaan')
@section('title', 'Data Historis ' . $kela->nama . ' - Tahun Ajaran ' . $tahunAjaran->nama)

@section('content')
<div class="space-y-6 animate-fade-up">

    {{-- HEADER CARD HISTORIS --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-700 via-slate-800 to-slate-900 p-6 md:p-8 text-white shadow-xl shadow-slate-900/20">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/5 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <a href="{{ route('kesiswaan.tahun-ajaran.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs text-slate-300 hover:text-white font-bold transition">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Tahun Ajaran
                    </a>
                    <span class="text-slate-500">/</span>
                    <a href="{{ route('kesiswaan.tahun-ajaran.historis', $tahunAjaran) }}" class="text-xs text-slate-300 hover:text-white font-medium transition">
                        Historis {{ $tahunAjaran->nama }}
                    </a>
                    <span class="text-slate-500">/</span>
                    <span class="text-xs text-white font-bold">{{ $kela->nama }}</span>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        Arsip Kelas {{ $kela->nama }}
                    </h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-400/30">
                        Historis (Read-Only)
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-slate-200 border border-white/15">
                        TA {{ $tahunAjaran->nama }}
                    </span>
                </div>

                <p class="text-xs text-slate-300 mt-2 max-w-xl leading-relaxed">
                    Data riwayat siswa pada tahun ajaran <span class="font-bold text-white">{{ $tahunAjaran->nama }}</span> untuk kelas <span class="font-bold text-white">{{ $kela->nama }}</span>.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.tahun-ajaran.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-xl hover:bg-white/20 transition-all">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Daftar TA
                </a>
                <a href="{{ route('kesiswaan.tahun-ajaran.historis', $tahunAjaran) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs rounded-xl transition-all border border-slate-600">
                    <i class="fa-solid fa-school text-xs"></i>
                    Semua Kelas TA Ini
                </a>
            </div>
        </div>
    </div>

    {{-- PENCARIAN --}}
    <form method="GET" action="{{ route('kesiswaan.tahun-ajaran.kelas.show', [$tahunAjaran, $kela]) }}" class="bg-white p-4 rounded-3xl border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama siswa..."
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-slate-400 transition">
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    Cari
                </button>
                @if (request()->filled('q'))
                    <a href="{{ route('kesiswaan.tahun-ajaran.kelas.show', [$tahunAjaran, $kela]) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
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
                            <td class="py-3.5 px-5">
                                <span class="font-bold text-slate-900 block">{{ $s->nama }}</span>
                                <span class="font-mono text-[11px] text-slate-400">NIS: {{ $s->nis }}</span>
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-slate-700">
                                {{ $kela->nama }}
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
                                    <i class="fa-solid fa-box-archive text-3xl mb-2 text-slate-300"></i>
                                    <p class="text-xs font-semibold text-slate-600">Tidak ada data historis siswa untuk kelas ini.</p>
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
