@extends('layouts.kesiswaan')
@section('title', 'Riwayat Nilai Siswa')

@section('content')
    <div class="bg-[#F8FAFC] -mx-4 md:-mx-8 px-4 md:px-8 py-6 md:py-8 min-h-[calc(100vh-5rem)] space-y-6 animate-fade-up">
        
        {{-- HEADER & TABS --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 pb-2 border-b border-slate-200/80">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Riwayat Nilai Siswa</h1>
                    @if($tahunAjaranAktif)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            TA Aktif: {{ $tahunAjaranAktif->nama }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-1">Pantau rekam jejak nilai ekstrakurikuler setiap siswa di seluruh tahun ajaran</p>
            </div>
        </div>

        {{-- TAB NAVIGASI UTAMA --}}
        <div class="flex items-center gap-2 border-b border-slate-200">
            <a href="{{ route('kesiswaan.laporan-penilaian.index') }}"
               class="px-4 py-2.5 text-xs font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 transition flex items-center gap-2">
                
                Rekap Nilai Ekskul
            </a>
            <a href="{{ route('kesiswaan.laporan-penilaian.historis-siswa') }}"
               class="px-4 py-2.5 text-xs font-bold border-b-2 border-sky-500 text-sky-600 flex items-center gap-2">
                
                Riwayat Nilai Siswa
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-600 border border-sky-100">Historis</span>
            </a>
        </div>

        {{-- FILTER BAR --}}
        @php
            $hasActiveFilter = request()->filled('q') || request()->filled('kelas_id') || request()->filled('tahun_ajaran_id');
            $activeFilterCount = (request()->filled('kelas_id') ? 1 : 0) + (request()->filled('tahun_ajaran_id') ? 1 : 0);
        @endphp

        <div x-data="{ openFilter: false }" class="space-y-3">
            <form method="GET" action="{{ route('kesiswaan.laporan-penilaian.historis-siswa') }}" class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-sm flex flex-col md:flex-row gap-3 items-stretch md:items-center">
                
                {{-- Input Pencarian --}}
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama siswa atau NIS..."
                           class="w-full pl-4 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>

                {{-- Tombol Filter & Popover --}}
                <div class="relative flex items-center gap-2">
                    <button type="button" @click="openFilter = !openFilter"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 border {{ $activeFilterCount > 0 ? 'bg-sky-50 border-sky-300 text-sky-700 shadow-sm shadow-sky-100' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                        
                        <span>Filter Kelas & TA</span>
                        @if($activeFilterCount > 0)
                            <span class="w-5 h-5 rounded-full bg-sky-500 text-white text-[10px] font-black flex items-center justify-center">
                                {{ $activeFilterCount }}
                            </span>
                        @endif
                    </button>

                    <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm transition">
                        Cari
                    </button>

                    @if($hasActiveFilter)
                        <a href="{{ route('kesiswaan.laporan-penilaian.historis-siswa') }}"
                           class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition"
                           title="Reset filter">
                            Reset
                        </a>
                    @endif

                    {{-- DROPDOWN PANEL POPOVER --}}
                    <div x-show="openFilter"
                         @click.away="openFilter = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl p-5 border border-slate-200 shadow-xl shadow-slate-900/10 z-50 space-y-4"
                         style="display: none;">
                        
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h4 class="text-xs font-bold text-slate-800">Filter Riwayat Siswa</h4>
                            <button type="button" @click="openFilter = false" class="text-slate-400 hover:text-slate-600 text-sm font-bold">
                                &times;
                            </button>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kelas</label>
                                <select name="kelas_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                    <option value="">Semua Kelas</option>
                                    @foreach($daftarKelas as $kls)
                                        <option value="{{ $kls->id }}" {{ request('kelas_id') == $kls->id ? 'selected' : '' }}>
                                            {{ $kls->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tahun Ajaran Nilai</label>
                                <select name="tahun_ajaran_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                    <option value="">Semua Tahun Ajaran</option>
                                    @foreach($daftarTahunAjaran as $ta)
                                        <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                                            {{ $ta->nama }} {{ $ta->status === 'aktif' ? '(Aktif)' : '(Historis)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="{{ route('kesiswaan.laporan-penilaian.historis-siswa', array_filter(['q' => request('q')])) }}"
                               class="px-4 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition">
                                Reset
                            </a>
                            <button type="submit" @click="openFilter = false"
                                    class="px-5 py-2 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- ACTIVE FILTER CHIPS --}}
            @if($hasActiveFilter)
                <div class="flex flex-wrap items-center gap-2 pt-1 px-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Filter Aktif:</span>

                    @if(request()->filled('q'))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-xs font-semibold">
                            <span>Pencarian: "<strong>{{ request('q') }}</strong>"</span>
                            <a href="{{ route('kesiswaan.laporan-penilaian.historis-siswa', request()->except('q', 'page')) }}" class="hover:text-rose-500 transition">&times;</a>
                        </span>
                    @endif

                    @if(request()->filled('kelas_id'))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-xs font-semibold">
                            <span>Kelas: <strong>{{ $daftarKelas->firstWhere('id', request('kelas_id'))?->nama }}</strong></span>
                            <a href="{{ route('kesiswaan.laporan-penilaian.historis-siswa', request()->except('kelas_id', 'page')) }}" class="hover:text-rose-500 transition">&times;</a>
                        </span>
                    @endif

                    @if(request()->filled('tahun_ajaran_id'))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-sky-50 border border-sky-200 text-sky-700 rounded-xl text-xs font-semibold">
                            <span>Tahun Ajaran: <strong>{{ $daftarTahunAjaran->firstWhere('id', request('tahun_ajaran_id'))?->nama }}</strong></span>
                            <a href="{{ route('kesiswaan.laporan-penilaian.historis-siswa', request()->except('tahun_ajaran_id', 'page')) }}" class="hover:text-rose-500 transition">&times;</a>
                        </span>
                    @endif

                    <a href="{{ route('kesiswaan.laporan-penilaian.historis-siswa') }}" class="text-xs font-bold text-rose-500 hover:text-rose-600 hover:underline transition ml-1">
                        Hapus Filter
                    </a>
                </div>
            @endif
        </div>

        {{-- TABEL SISWA & HISTORIS NILAI --}}
        @if ($siswas->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-12 md:p-16 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-4">
                    
                </div>
                <h2 class="text-sm font-bold text-slate-900">Siswa Tidak Ditemukan</h2>
                <p class="text-xs text-slate-400 mt-1">Tidak ada data siswa yang cocok dengan filter pencarian.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="p-4 md:p-5 border-b border-slate-200/70">
                    <h3 class="text-sm font-bold text-slate-900">Daftar Rekam Jejak Nilai Siswa</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Klik tombol detail pada siswa untuk melihat rincian nilai per tahun ajaran & semester.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[860px]">
                        <thead class="bg-slate-50/80 text-xs uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-bold w-12">#</th>
                                <th class="px-4 py-3 font-bold">Siswa</th>
                                <th class="px-4 py-3 font-bold">Ekskul Diikuti</th>
                                <th class="px-4 py-3 font-bold text-center">Total Nilai</th>
                                <th class="px-4 py-3 font-bold text-center">Rata-rata</th>
                                <th class="px-4 py-3 font-bold text-center">Predikat Dominan</th>
                                <th class="px-4 py-3 font-bold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($siswas as $siswa)
                                @php
                                    // Kumpulkan semua penilaian terkirim dari seluruh pendaftaran siswa ini
                                    $allPenilaians = $siswa->pendaftarans->flatMap->penilaians->where('status', \App\Models\Penilaian::STATUS_TERKIRIM);
                                    $totalPenilaian = $allPenilaians->count();
                                    $avgNilai = $totalPenilaian > 0 ? round($allPenilaians->avg('nilai_akhir'), 2) : 0;
                                    $predikatDominan = $totalPenilaian > 0 ? \App\Models\Penilaian::hitungPredikat($avgNilai) : '-';

                                    // Ekskul yang aktif
                                    $ekskulAktif = $siswa->pendaftarans->whereIn('status', [\App\Models\Pendaftaran::STATUS_DITERIMA, \App\Models\Pendaftaran::STATUS_PERINGATAN]);
                                @endphp
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-4 py-3 text-slate-400 font-semibold">{{ $loop->iteration + ($siswas->currentPage() - 1) * $siswas->perPage() }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3 min-w-[200px]">
                                            <div class="w-9 h-9 rounded-full bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center font-extrabold text-xs shrink-0">
                                                {{ strtoupper(mb_substr($siswa->nama ?? '?', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 leading-tight truncate">{{ $siswa->nama }}</p>
                                                <p class="text-[11px] text-slate-400">NIS: {{ $siswa->nis }} &middot; {{ $siswa->kelas?->nama ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1 max-w-[260px]">
                                            @forelse($ekskulAktif as $p)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $p->ekskul?->nama_ekskul }}
                                                </span>
                                            @empty
                                                <span class="text-[11px] text-slate-400 italic">Tidak ada ekskul aktif</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($totalPenilaian > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                                {{ $totalPenilaian }} Periode
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-900">
                                        {{ $totalPenilaian > 0 ? number_format($avgNilai, 1) : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($totalPenilaian > 0)
                                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg border font-extrabold text-xs {{ \App\Models\Penilaian::warnaPredikat($predikatDominan) }}">
                                                {{ $predikatDominan }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('kesiswaan.laporan-penilaian.detail-siswa', $siswa->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm shadow-blue-600/25 transition">
                                            
                                            Riwayat Nilai
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if($siswas->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $siswas->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
