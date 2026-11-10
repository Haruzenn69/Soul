@extends('layouts.kesiswaan')
@section('title', 'Kelas ' . $kela->nama . ' - Tahun Ajaran ' . $tahunAjaran->nama)

@section('content')
@php
    $sortLink = function (string $column) use ($sort, $direction) {
        $nextDirection = $sort === $column && $direction === 'asc' ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => null]);
    };

    $sortIcon = function (string $column) use ($sort, $direction) {
        if ($sort !== $column) {
            return 'M8 9l4-4 4 4M8 15l4 4 4-4';
        }
        return $direction === 'asc' ? 'M8 15l4 4 4-4' : 'M8 9l4-4 4 4';
    };

    $isAktif = $tahunAjaran->isAktif();
    $hasPending = $availableSiswa->isNotEmpty();
    $isEmpty = $siswas->total() === 0;
@endphp

<div class="space-y-6 animate-fade-up">

    <a href="{{ route('kesiswaan.tahun-ajaran.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-sky-100 text-sky-700 text-xs font-bold shadow-sm hover:bg-sky-50 transition">
        <i class="fa-solid fa-arrow-left text-[10px]"></i>
        Kembali
    </a>

    {{-- HERO CARD --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-500 to-blue-600 p-6 md:p-8 text-white soul-timbul">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <a href="{{ route('kesiswaan.tahun-ajaran.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs text-white/80 hover:text-white font-bold transition">
                        
                        Tahun Ajaran
                    </a>
                    <span class="text-white/40">/</span>
                    <span class="text-xs text-white/90 font-medium">{{ $tahunAjaran->nama }}</span>
                    <span class="text-white/40">/</span>
                    <span class="text-xs text-white font-bold">{{ $kela->nama }}</span>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        Kelas {{ $kela->nama }}
                    </h1>
                </div>

                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Tahun Ajaran: <span class="font-bold text-white">{{ $tahunAjaran->nama }}</span>
                    &bull; Jurusan: <span class="font-bold text-white">{{ strtoupper($kela->jurusan) }}</span>
                    &bull; Rombel: <span class="font-bold text-white">{{ $kela->rombel }}</span>
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                @if($isAktif && $hasPending)
                    <button type="button" onclick="document.getElementById('penempatan-section')?.scrollIntoView({behavior: 'smooth'})"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-slate-900 font-extrabold text-xs rounded-xl shadow-lg transition-all">
                        
                        Atur Siswa ({{ $availableSiswa->count() }})
                    </button>
                @endif
                <a href="{{ route('kesiswaan.kelas.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-sky-700 font-bold text-xs rounded-xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 transition-all">
                    
                    Data Kelas
                </a>
            </div>
        </div>

        {{-- QUICK STATS PILLS --}}
        <div class="relative mt-6 pt-5 border-t border-white/15">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/15">
                    <span class="text-[10px] font-bold text-white/75 uppercase tracking-wider block">Siswa Aktif</span>
                    <span class="text-xl font-black text-white mt-0.5 block">{{ $stats['total'] }}</span>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/15">
                    <span class="text-[10px] font-bold text-white/75 uppercase tracking-wider block">Laki-Laki (L)</span>
                    <span class="text-xl font-black text-white mt-0.5 block">{{ $stats['laki'] }}</span>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/15">
                    <span class="text-[10px] font-bold text-white/75 uppercase tracking-wider block">Perempuan (P)</span>
                    <span class="text-xl font-black text-white mt-0.5 block">{{ $stats['perempuan'] }}</span>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-2xl p-3 border border-white/15">
                    <span class="text-[10px] font-bold text-white/75 uppercase tracking-wider block">
                        @if($kela->tingkat === 'xi') Sumber Kelas 10
                        @elseif($kela->tingkat === 'xii') Sumber Kelas 11
                        @else Penempatan Baru
                        @endif
                    </span>
                    <span class="text-sm font-extrabold text-amber-300 mt-1 block truncate">
                        @if($kela->tingkat === 'xi') Jurusan {{ strtoupper($kela->jurusan) }}
                        @elseif($kela->tingkat === 'xii') {{ $matchingSourceClass?->nama ?? '11 '.$kela->jurusan.' '.$kela->rombel }}
                        @else Kelas 10 Baru
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>



    {{-- SEARCH & FILTER FORM --}}
    <form method="GET" action="{{ route('kesiswaan.tahun-ajaran.kelas.show', [$tahunAjaran, $kela]) }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">

        <div class="flex flex-col md:flex-row gap-3 items-stretch">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIS siswa di kelas ini..."
                       class="w-full pl-4 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Cari
                </button>
                @if (request()->filled('q') || $sort !== 'nama' || $direction !== 'asc')
                    <a href="{{ route('kesiswaan.tahun-ajaran.kelas.show', [$tahunAjaran, $kela]) }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- TABEL SISWA KELAS --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nama') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Siswa
                                
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="{{ $sortLink('nis') }}" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                NIS
                                
                            </a>
                        </th>
                        <th class="py-3.5 px-5">Jenis Kelamin</th>
                        <th class="py-3.5 px-5">No. Telp / WA</th>
                        <th class="py-3.5 px-5">Jabatan</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($siswas as $s)
                        <tr class="transition hover:bg-sky-50/30">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0 overflow-hidden">
                                        @if ($s->foto_url)
                                            <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($s->nama, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 leading-snug truncate">{{ $s->nama }}</div>
                                        <div class="text-[11px] text-slate-400 truncate">{{ $s->email ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap font-mono font-semibold text-slate-700">{{ $s->nis }}</td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-semibold {{ $s->jenis_kelamin === 'perempuan' ? 'text-rose-600' : 'text-sky-700' }}">
                                    <span class="text-sm leading-none">{{ $s->jenis_kelamin === 'perempuan' ? 'P' : 'L' }}</span>
                                    {{ ucfirst($s->jenis_kelamin ?? '-') }}
                                </span>
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap text-slate-600">{{ $s->no_telp ?: '-' }}</td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if ($s->jabatan === 'ketua')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-[11px] bg-amber-50 text-amber-700 border border-amber-100">
                                        Ketua Ekskul
                                    </span>
                                @elseif($s->jabatan === 'anggota')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-[11px] bg-sky-50 text-sky-700 border border-sky-100">
                                        Anggota Ekskul
                                    </span>
                                @else
                                    <span class="text-slate-600">Siswa Reguler</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                @if ($s->user_id)
                                    <a href="{{ route('kesiswaan.users.edit', $s->user_id) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold rounded-xl transition text-xs"
                                       title="Edit Akun {{ $s->nama }}">
                                        
                                        Edit Akun
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum ada akun</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    
                                    <p class="text-xs font-semibold text-slate-600">Belum ada siswa aktif di kelas {{ $kela->nama }} ini.</p>
                                    @if($isAktif)
                                        <p class="text-[11px] text-slate-400 mt-1">Gunakan panel penempatan siswa di bawah untuk memasukkan siswa ke kelas ini.</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-2 justify-between items-center">
            <p class="text-[11px] text-slate-400 font-semibold">
                Menampilkan {{ $siswas->firstItem() ?? 0 }}-{{ $siswas->lastItem() ?? 0 }} dari {{ $siswas->total() }} siswa aktif
            </p>
            {{ $siswas->links() }}
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- SECTION PENEMPATAN / KENAIKAN SISWA KE KELAS INI (AKTIF) --}}
    {{-- ======================================================== --}}
    @if($isAktif)
        <div id="penempatan-section" class="space-y-4 animate-fade-up">

            {{-- 1. KELAS 11: PENEMPATAN DARI KELAS 10 JURUSAN SAMA --}}
            @if($kela->tingkat === 'xi')
                <div class="bg-gradient-to-br from-sky-50 via-white to-blue-50/60 rounded-3xl border border-sky-200/80 shadow-md p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b border-sky-100">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-sky-500 text-white flex items-center justify-center font-extrabold text-base shadow-md shadow-sky-200 shrink-0">
                                11
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900">Penempatan Siswa ke Kelas {{ $kela->nama }}</h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Pilih siswa kelas 10 di jurusan <strong class="text-sky-700">{{ strtoupper($kela->jurusan) }}</strong> yang akan ditempatkan di kelas ini. Siswa yang tidak naik dapat ditandai tinggal kelas.
                                </p>
                            </div>
                        </div>

                        @if($sourceKelasOptions->isNotEmpty())
                            <form method="GET" action="{{ route('kesiswaan.tahun-ajaran.kelas.show', [$tahunAjaran, $kela]) }}" class="flex items-center gap-2">
                                <input type="hidden" name="sort" value="{{ $sort }}">
                                <input type="hidden" name="direction" value="{{ $direction }}">
                                @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                                <label class="text-[11px] font-bold text-slate-500 whitespace-nowrap">Filter Asal:</label>
                                <select name="source_kelas_id" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-sky-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-200 shadow-sm">
                                    <option value="">Semua Kelas 10 ({{ $sourceKelasOptions->sum('waiting_siswas_count') }} siswa)</option>
                                    @foreach($sourceKelasOptions as $sko)
                                        <option value="{{ $sko->id }}" {{ request('source_kelas_id') == $sko->id ? 'selected' : '' }}>
                                            {{ $sko->nama }} ({{ $sko->waiting_siswas_count }} siswa)
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </div>

                    @if($availableSiswa->isNotEmpty())
                        <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.kelas.assign', [$tahunAjaran, $kela]) }}" class="mt-4">
                            @csrf
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" id="check-all-assign" class="w-4 h-4 text-sky-600 rounded border-sky-300 focus:ring-sky-500">
                                        <span class="text-xs font-bold text-slate-700">Pilih Semua ({{ $availableSiswa->count() }} siswa)</span>
                                    </label>
                                    <span id="assign-counter" class="text-xs font-semibold text-sky-700 bg-sky-100/60 px-2.5 py-0.5 rounded-full">
                                        0 siswa dipilih
                                    </span>
                                </div>
                                <div class="relative w-full sm:w-64">
                                    <input type="text" id="filter-student-search" placeholder="Cari nama siswa..." class="w-full px-3 py-1.5 bg-white border border-sky-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-200 shadow-sm">
                                </div>
                            </div>

                            <div class="max-h-80 overflow-y-auto rounded-2xl border border-sky-200/80 bg-white divide-y divide-sky-100" id="student-list-container">
                                @foreach($availableSiswa as $asw)
                                    <label class="student-item flex items-center justify-between p-3.5 hover:bg-sky-50/60 cursor-pointer transition select-none" data-name="{{ strtolower($asw->nama) }}" data-nis="{{ $asw->nis }}">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="siswa_ids[]" value="{{ $asw->id }}" class="assign-checkbox w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500">
                                            <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0">
                                                {{ strtoupper(substr($asw->nama, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-900 leading-snug">{{ $asw->nama }}</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5">
                                                    NIS: <span class="font-mono text-slate-600">{{ $asw->nis }}</span>
                                                    &bull; Asal: <span class="font-bold text-sky-700">{{ $asw->kelas?->nama ?? 'Kelas 10' }}</span>
                                                    &bull; {{ ucfirst($asw->jenis_kelamin ?? '-') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">Menunggu Penempatan</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="mt-4 flex flex-wrap justify-end gap-2.5">
                                <button type="submit" name="keputusan" value="tinggal" onclick="return confirm('Tetapkan siswa terpilih tinggal di kelas 10?')" class="rounded-xl border border-amber-300 bg-white px-5 py-2.5 text-xs font-extrabold text-amber-800 shadow-sm transition hover:bg-amber-50 disabled:cursor-not-allowed disabled:opacity-50" disabled data-assign-submit>
                                    Tetapkan Tinggal di Kelas 10
                                </button>
                                <button type="submit" name="keputusan" value="naik" class="rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-6 py-2.5 text-xs font-extrabold text-white shadow-md shadow-sky-200 transition hover:from-sky-600 hover:to-blue-700 disabled:cursor-not-allowed disabled:opacity-50" disabled data-assign-submit>
                                    + Tempatkan Terpilih ke {{ $kela->nama }}
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="py-8 text-center text-xs font-semibold text-slate-500">
                            Tidak ada siswa kelas 10 jurusan {{ strtoupper($kela->jurusan) }} yang menunggu penempatan ke kelas ini.
                        </div>
                    @endif
                </div>

            {{-- 2. KELAS 12: KENAIKAN DARI KELAS 11 ROMBEL TETAP --}}
            @elseif($kela->tingkat === 'xii')
                <div class="bg-gradient-to-br from-indigo-50 via-white to-blue-50/60 rounded-3xl border border-indigo-200/80 shadow-md p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b border-indigo-100">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-extrabold text-base shadow-md shadow-indigo-200 shrink-0">
                                12
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900">Kenaikan Siswa dari Kelas {{ $matchingSourceClass?->nama ?? 'Kelas 11' }} ke {{ $kela->nama }}</h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Perubahan tingkat dari rombel yang sama (<strong class="text-indigo-700">{{ $matchingSourceClass?->nama ?? '11 '.$kela->jurusan.' '.$kela->rombel }}</strong> -> <strong class="text-indigo-700">{{ $kela->nama }}</strong>). Semua siswa tercentang otomatis untuk naik. Hapus centang jika ada yang tinggal kelas.
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($availableSiswa->isNotEmpty())
                        <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.kelas.assign', [$tahunAjaran, $kela]) }}" class="mt-4">
                            @csrf
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" id="check-all-assign" checked class="w-4 h-4 text-indigo-600 rounded border-indigo-300 focus:ring-indigo-500">
                                        <span class="text-xs font-bold text-slate-700">Pilih Semua ({{ $availableSiswa->count() }} siswa)</span>
                                    </label>
                                    <span id="assign-counter" class="text-xs font-semibold text-indigo-700 bg-indigo-100/60 px-2.5 py-0.5 rounded-full">
                                        {{ $availableSiswa->count() }} siswa dipilih
                                    </span>
                                </div>
                                <div class="relative w-full sm:w-64">
                                    <input type="text" id="filter-student-search" placeholder="Cari nama siswa..." class="w-full px-3 py-1.5 bg-white border border-indigo-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-200 shadow-sm">
                                </div>
                            </div>

                            <div class="max-h-80 overflow-y-auto rounded-2xl border border-indigo-200/80 bg-white divide-y divide-indigo-100" id="student-list-container">
                                @foreach($availableSiswa as $asw)
                                    <label class="student-item flex items-center justify-between p-3.5 hover:bg-indigo-50/60 cursor-pointer transition select-none" data-name="{{ strtolower($asw->nama) }}" data-nis="{{ $asw->nis }}">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="siswa_ids[]" value="{{ $asw->id }}" checked class="assign-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                                            <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0">
                                                {{ strtoupper(substr($asw->nama, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-900 leading-snug">{{ $asw->nama }}</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5">
                                                    NIS: <span class="font-mono text-slate-600">{{ $asw->nis }}</span>
                                                    &bull; Asal: <span class="font-bold text-indigo-700">{{ $asw->kelas?->nama ?? 'Kelas 11' }}</span>
                                                    &bull; {{ ucfirst($asw->jenis_kelamin ?? '-') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">Siap Naik ke 12</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="mt-4 flex flex-wrap justify-end gap-2.5">
                                <button type="submit" name="keputusan" value="tinggal" onclick="return confirm('Tetapkan siswa terpilih tinggal di kelas 11?')" class="rounded-xl border border-amber-300 bg-white px-5 py-2.5 text-xs font-extrabold text-amber-800 shadow-sm transition hover:bg-amber-50 disabled:cursor-not-allowed disabled:opacity-50" data-assign-submit>
                                    Tetapkan Tinggal di Kelas 11
                                </button>
                                <button type="submit" name="keputusan" value="naik" class="rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-2.5 text-xs font-extrabold text-white shadow-md shadow-indigo-200 transition hover:from-indigo-700 hover:to-blue-700 disabled:cursor-not-allowed disabled:opacity-50" data-assign-submit>
                                    + Naikkan Terpilih ke {{ $kela->nama }}
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="py-8 text-center text-xs font-semibold text-slate-500">
                            Semua siswa dari kelas {{ $matchingSourceClass?->nama ?? '11 '.$kela->jurusan.' '.$kela->rombel }} sudah dinaikkan atau tidak ada siswa yang menunggu.
                        </div>
                    @endif
                </div>

            {{-- 3. KELAS 10: PENEMPATAN SISWA BARU / PPDB --}}
            @elseif($kela->tingkat === 'x')
                <div class="bg-gradient-to-br from-emerald-50 via-white to-teal-50/60 rounded-3xl border border-emerald-200/80 shadow-md p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b border-emerald-100">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-extrabold text-base shadow-md shadow-emerald-200 shrink-0">
                                10
                            </div>
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-900">Penempatan Siswa Baru ke Kelas {{ $kela->nama }}</h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Centang siswa baru (atau siswa yang tinggal di kelas 10) untuk ditempatkan pada rombel ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($availableSiswa->isNotEmpty())
                        <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.kelas.assign', [$tahunAjaran, $kela]) }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="keputusan" value="naik">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" id="check-all-assign" class="w-4 h-4 text-emerald-600 rounded border-emerald-300 focus:ring-emerald-500">
                                        <span class="text-xs font-bold text-slate-700">Pilih Semua ({{ $availableSiswa->count() }} siswa)</span>
                                    </label>
                                    <span id="assign-counter" class="text-xs font-semibold text-emerald-700 bg-emerald-100/60 px-2.5 py-0.5 rounded-full">
                                        0 siswa dipilih
                                    </span>
                                </div>
                                <div class="relative w-full sm:w-64">
                                    <input type="text" id="filter-student-search" placeholder="Cari nama siswa..." class="w-full px-3 py-1.5 bg-white border border-emerald-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-200 shadow-sm">
                                </div>
                            </div>

                            <div class="max-h-80 overflow-y-auto rounded-2xl border border-emerald-200/80 bg-white divide-y divide-emerald-100" id="student-list-container">
                                @foreach($availableSiswa as $asw)
                                    <label class="student-item flex items-center justify-between p-3.5 hover:bg-emerald-50/60 cursor-pointer transition select-none" data-name="{{ strtolower($asw->nama) }}" data-nis="{{ $asw->nis }}">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox" name="siswa_ids[]" value="{{ $asw->id }}" class="assign-checkbox w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0">
                                                {{ strtoupper(substr($asw->nama, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-900 leading-snug">{{ $asw->nama }}</p>
                                                <p class="text-[11px] text-slate-400 mt-0.5">
                                                    NIS: <span class="font-mono text-slate-600">{{ $asw->nis }}</span>
                                                    &bull; Status: <span class="font-bold text-emerald-700">Siswa Baru / Menunggu</span>
                                                    &bull; {{ ucfirst($asw->jenis_kelamin ?? '-') }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Belum Ada Kelas</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="mt-4 flex justify-end">
                                <button type="submit" class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-2.5 text-xs font-extrabold text-white shadow-md shadow-emerald-200 transition hover:from-emerald-700 hover:to-teal-700 disabled:cursor-not-allowed disabled:opacity-50" disabled data-assign-submit>
                                    + Tempatkan Terpilih ke {{ $kela->nama }}
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="py-8 text-center">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
                                
                            </div>
                            <p class="text-xs font-bold text-slate-700">Belum ada siswa baru yang menunggu penempatan</p>
                            <p class="text-[11px] text-slate-400 mt-1 max-w-sm mx-auto">Tambahkan siswa baru melalui menu Data Siswa (input manual atau import excel PPDB).</p>
                            <a href="{{ route('kesiswaan.siswa.index') }}" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-200 transition">
                                + Buka Data Siswa
                            </a>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('check-all-assign');
        const checkboxes = document.querySelectorAll('.assign-checkbox');
        const counter = document.getElementById('assign-counter');
        const submitBtns = document.querySelectorAll('[data-assign-submit]');
        const searchInput = document.getElementById('filter-student-search');
        const studentItems = document.querySelectorAll('.student-item');

        function updateState() {
            const visibleCheckboxes = Array.from(checkboxes).filter(cb => cb.closest('.student-item').style.display !== 'none');
            const checkedCount = visibleCheckboxes.filter(cb => cb.checked).length;

            if (counter) counter.textContent = `${checkedCount} siswa dipilih`;
            submitBtns.forEach(btn => btn.disabled = checkedCount === 0);

            if (checkAll && visibleCheckboxes.length > 0) {
                checkAll.checked = checkedCount === visibleCheckboxes.length;
                checkAll.indeterminate = checkedCount > 0 && checkedCount < visibleCheckboxes.length;
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                const visibleCheckboxes = Array.from(checkboxes).filter(cb => cb.closest('.student-item').style.display !== 'none');
                visibleCheckboxes.forEach(cb => cb.checked = checkAll.checked);
                updateState();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateState);
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const term = this.value.trim().toLowerCase();
                studentItems.forEach(item => {
                    const name = item.dataset.name || '';
                    const nis = item.dataset.nis || '';
                    const matches = name.includes(term) || nis.includes(term);
                    item.style.display = matches ? '' : 'none';
                });
                updateState();
            });
        }

        updateState();
    });
</script>
@endsection
