@extends('layouts.kesiswaan')
@section('title', 'Tahun Ajaran')

@section('content')
    <div class="space-y-6 animate-fade-up">

        {{-- HERO CARD BIRU --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white soul-timbul">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            </div>

            <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-5">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        Tahun Ajaran
                    </h1>
                    <p class="text-xs text-white/85 mt-1.5 max-w-xl leading-relaxed">
                        Kelola tahun ajaran, pantau kelas, dan proses kenaikan kelas siswa
                    </p>
                </div>
                <a href="{{ route('kesiswaan.kelas.index') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <i class="fa-solid fa-chalkboard-user text-sky-600 text-sm"></i>
                    Kelola & Buat Kelas di Data Kelas
                </a>
            </div>
        </div>

        {{-- STATISTIK --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .05s">
                <p class="text-xl font-extrabold text-slate-900">{{ $tahunAjarans->count() }}</p>
                <p class="text-xs font-bold text-slate-400">Total TA</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-lg shadow-emerald-100/60 text-center animate-fade-up" style="animation-delay: .1s">
                <p class="text-xl font-extrabold text-emerald-600">{{ $activeTA?->nama ?? '-' }}</p>
                <p class="text-xs font-bold text-slate-400">TA Aktif</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .15s">
                <p class="text-xl font-extrabold text-slate-900">{{ $activeKelas->count() }}</p>
                <p class="text-xs font-bold text-slate-400">Kelas Aktif</p>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-amber-100 shadow-lg shadow-amber-100/60 text-center animate-fade-up" style="animation-delay: .2s">
                <p class="text-xl font-extrabold {{ ($pendingCount12 ?? 0) > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $pendingCount12 ?? 0 }}</p>
                <p class="text-xs font-bold text-slate-400">Siswa Kelas 12 (Kelulusan)</p>
            </div>
        </div>

        {{-- PRIORITAS: PROSES KELULUSAN KELAS 12 (ANGKATAN AKHIR) --}}
        @if($activeTA && isset($pendingKelas12Classes) && $pendingKelas12Classes->isNotEmpty())
            <div class="bg-white rounded-2xl border border-sky-100 shadow-sm p-5 animate-fade-up">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-sky-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center shadow-xs">
                            <i class="fa-solid fa-graduation-cap text-sky-600 text-base"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Proses Kelulusan Siswa Kelas 12 (Angkatan Akhir)</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Pilih per kelas untuk menyeleksi siswa yang Lulus (Alumni) atau Tinggal Kelas melalui ceklis.</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold rounded-xl self-start sm:self-auto">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        {{ $pendingCount12 }} Siswa Menunggu Keputusan
                    </span>
                </div>

                {{-- Daftar Kartu Kelas 12 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mt-4">
                    @foreach($pendingKelas12Classes as $kelas12)
                        <div class="p-4 rounded-xl border border-sky-100 bg-sky-50/40 hover:bg-sky-50 transition flex flex-col justify-between gap-3 group">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-sm font-extrabold text-slate-900 group-hover:text-sky-700 transition">{{ $kelas12->nama }}</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $sourceYear?->nama ?? 'Tahun Sebelumnya' }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded-lg bg-sky-100 text-sky-700 text-[11px] font-bold">
                                    {{ $kelas12->siswas->count() }} Siswa
                                </span>
                            </div>
                            <button type="button"
                                    onclick="openModalKelulusan('{{ $kelas12->id }}')"
                                    class="w-full py-2 px-3 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl text-xs font-bold shadow-xs hover:shadow transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-list-check text-xs"></i>
                                Ceklis Kelulusan
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- MODAL CHECKLIST KELULUSAN PER KELAS --}}
            @foreach($pendingKelas12Classes as $kelas12)
                <div id="modalKelulusan{{ $kelas12->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5">
                    <div class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl border border-sky-100 overflow-hidden animate-scale-up flex flex-col max-h-[90vh]">
                        {{-- Modal Header --}}
                        <div class="p-5 sm:p-6 border-b border-sky-50 bg-gradient-to-r from-sky-50/50 to-white flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-sky-100 text-sky-800 text-xs font-extrabold uppercase tracking-wider">
                                        Kelas {{ $kelas12->nama }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-medium">TA {{ $sourceYear?->nama ?? '-' }}</span>
                                </div>
                                <h3 class="text-lg font-extrabold text-slate-900 mt-1">Ceklis Kelulusan Siswa Angkatan Akhir</h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Siswa yang dicentang (<span class="font-bold text-sky-700">Lulus</span>) akan dialihkan menjadi Alumni. Siswa yang tidak dicentang (<span class="font-bold text-amber-700">Tinggal</span>) akan ditempatkan di kelas 12 TA aktif ({{ $activeTA->nama }}).
                                </p>
                            </div>
                            <button type="button" onclick="closeModalKelulusan('{{ $kelas12->id }}')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>

                        {{-- Form --}}
                        <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.kelulusan', $activeTA) }}" class="flex flex-col flex-1 overflow-hidden" onsubmit="return confirmProsesKelulusan(this)" data-kelas-nama="{{ $kelas12->nama }}" data-kelas-id="{{ $kelas12->id }}">
                            @csrf

                            {{-- Search & Controls Bar --}}
                            <div class="p-4 border-b border-sky-50 bg-slate-50/60 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                                <div class="relative flex-1">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                    <input type="text"
                                           placeholder="Cari nama, NISN, atau NIS siswa..."
                                           oninput="filterSiswaList('{{ $kelas12->id }}', this.value)"
                                           class="w-full pl-9 pr-4 py-2 bg-white border border-sky-100 rounded-xl text-xs focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                </div>
                                <div class="flex items-center justify-between sm:justify-end gap-3 text-xs">
                                    <label class="inline-flex items-center gap-2 cursor-pointer select-none font-bold text-slate-700 hover:text-slate-900">
                                        <input type="checkbox"
                                               id="checkAll{{ $kelas12->id }}"
                                               checked
                                               onchange="toggleSelectAll('{{ $kelas12->id }}', this.checked)"
                                               class="w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500 cursor-pointer">
                                        <span>Pilih Semua Lulus</span>
                                    </label>
                                    <div class="h-4 w-px bg-slate-300 hidden sm:block"></div>
                                    <div class="flex items-center gap-2 font-semibold">
                                        <span id="counterLulus{{ $kelas12->id }}" class="text-sky-700">
                                            Lulus: <span class="counter-num font-extrabold">{{ $kelas12->siswas->count() }}</span>
                                        </span>
                                        <span class="text-slate-300">/</span>
                                        <span id="counterTinggal{{ $kelas12->id }}" class="text-slate-400">
                                            Tinggal: <span class="counter-num font-extrabold">0</span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Student List Checklist --}}
                            <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-2" id="listContainer{{ $kelas12->id }}">
                                @foreach($kelas12->siswas as $siswa)
                                    <input type="hidden" name="all_siswa_ids[]" value="{{ $siswa->id }}">
                                    <label id="rowSiswa{{ $kelas12->id }}_{{ $siswa->id }}"
                                           data-nama="{{ strtolower($siswa->nama) }}"
                                           data-nisn="{{ strtolower($siswa->nisn ?? '') }}"
                                           data-nis="{{ strtolower($siswa->nis ?? '') }}"
                                           class="siswa-row flex items-center justify-between p-3 rounded-2xl border border-sky-100/80 bg-white hover:bg-sky-50/50 cursor-pointer transition select-none">
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox"
                                                   name="lulus_ids[]"
                                                   value="{{ $siswa->id }}"
                                                   checked
                                                   onchange="onCheckboxChange('{{ $kelas12->id }}', this, '{{ $siswa->id }}')"
                                                   class="kelulusan-checkbox-{{ $kelas12->id }} w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500 cursor-pointer">
                                            <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-700 font-bold text-xs flex items-center justify-center">
                                                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800">{{ $siswa->nama }}</p>
                                                <p class="text-[11px] text-slate-400 font-medium">NISN: {{ $siswa->nisn ?? '-' }} | NIS: {{ $siswa->nis ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span id="badgeLulus{{ $kelas12->id }}_{{ $siswa->id }}" class="px-2.5 py-1 rounded-lg bg-sky-100 text-sky-700 text-[10px] font-extrabold uppercase">
                                                Lulus (Alumni)
                                            </span>
                                            <span id="badgeTinggal{{ $kelas12->id }}_{{ $siswa->id }}" class="hidden px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 text-[10px] font-extrabold uppercase">
                                                Tinggal di Kelas 12
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                                <div id="emptySearch{{ $kelas12->id }}" class="hidden py-8 text-center text-xs text-slate-400">
                                    Siswa dengan kata kunci tersebut tidak ditemukan.
                                </div>
                            </div>

                            {{-- Modal Footer --}}
                            <div class="p-4 sm:p-5 border-t border-sky-50 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                                <p class="text-xs text-slate-400 text-center sm:text-left">
                                    Pastikan ceklis sudah sesuai sebelum menekan tombol simpan.
                                </p>
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <button type="button" onclick="closeModalKelulusan('{{ $kelas12->id }}')" class="flex-1 sm:flex-none px-4 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs rounded-xl transition inline-flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                        Batal
                                    </button>
                                    <button type="submit" class="flex-1 sm:flex-none px-5 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-200 transition inline-flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-check text-xs"></i>
                                        Simpan & Proses Kelulusan Kelas {{ $kelas12->nama }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        @elseif($activeTA && isset($sourceYear) && $sourceYear)
            {{-- Status subtle jika kelulusan kelas 12 sudah selesai semua --}}
            <div class="bg-white rounded-2xl border border-sky-100 shadow-sm p-4 animate-fade-up flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-check text-emerald-600 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Kelulusan Kelas 12 Selesai</p>
                        <p class="text-[11px] text-slate-400">Seluruh siswa angkatan akhir tahun ajaran sebelumnya telah diproses kelulusannya.</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                    Selesai
                </span>
            </div>
        @endif

        {{-- BUAT TAHUN AJARAN BARU --}}
        <div class="bg-white rounded-2xl border border-sky-100 shadow-sm p-5 animate-fade-up" style="animation-delay: .25s">
            <h2 class="text-sm font-extrabold text-slate-900 mb-3">Buat Tahun Ajaran Baru</h2>
            <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.store') }}" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="text" name="nama" placeholder="Contoh: 2027/2028" required
                    class="flex-1 px-4 py-2.5 bg-sky-50/60 border border-sky-100 rounded-xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition"
                    pattern="\d{4}/\d{4}">
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 transition hover:-translate-y-0.5 whitespace-nowrap inline-flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Buat Tahun Ajaran
                </button>
            </form>
            @error('nama')
                <p class="text-xs text-rose-500 mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- DAFTAR TAHUN AJARAN --}}
        <div class="space-y-4">
            @foreach($tahunAjarans as $ta)
                @php
                    $isHistoris = $ta->status === 'historis';
                    $isAktif = $ta->status === 'aktif';

                    $statusColor = match($ta->status) {
                        'aktif' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                        'nonaktif' => 'bg-amber-50 border-amber-200 text-amber-700',
                        'historis' => 'bg-slate-100 border-slate-200 text-slate-500',
                    };
                    $statusLabel = match($ta->status) {
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        'historis' => 'Historis',
                    };
                    $statusIcon = match($ta->status) {
                        'aktif' => '<i class="fa-solid fa-circle-check text-[11px]"></i>',
                        'nonaktif' => '<i class="fa-solid fa-circle-pause text-[11px]"></i>',
                        'historis' => '<i class="fa-solid fa-box-archive text-[11px]"></i>',
                    };

                    $kelasForTA = $kelasByTA[$ta->id] ?? (($ta->status === 'aktif')
                        ? $activeKelas
                        : \App\Models\Kelas::where('tahun_ajaran_id', $ta->id)
                            ->withCount('siswas')
                            ->orderByRaw("FIELD(tingkat, 'x', 'xi', 'xii')")
                            ->orderBy('jurusan')->orderBy('rombel')->get());
                @endphp

                <div class="{{ $isHistoris ? 'bg-slate-50/80 border border-slate-200/90 shadow-sm' : 'bg-white border border-sky-100 shadow-sm' }} rounded-2xl overflow-hidden animate-fade-up">
                    {{-- TA Header --}}
                    <div class="px-5 py-4 {{ $isHistoris ? 'bg-slate-50' : 'bg-white border-b border-sky-50' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl {{ $isHistoris ? 'bg-slate-200 border border-slate-300/60 text-slate-600' : 'bg-gradient-to-br from-sky-400 to-blue-500 text-white shadow-md shadow-sky-200' }} flex items-center justify-center font-extrabold text-sm">
                                TA
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold {{ $isHistoris ? 'text-slate-700' : 'text-slate-900' }}">{{ $ta->nama }}</h3>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusColor }}">
                                        {!! $statusIcon !!} {{ $statusLabel }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">{{ $ta->kelas_count }} kelas</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($isAktif)
                                <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.deactivate', $ta) }}"
                                    onsubmit="return confirm('Yakin ingin menonaktifkan tahun ajaran {{ $ta->nama }}? Tahun ajaran ini akan menjadi arsip historis.')">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold rounded-lg border border-amber-200 shadow-sm transition inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-box-archive text-xs"></i>
                                        Nonaktifkan (Jadikan Historis)
                                    </button>
                                </form>
                            @elseif($ta->status === 'nonaktif')
                                <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.activate', $ta) }}"
                                    onsubmit="return confirm('Yakin aktifkan tahun ajaran {{ $ta->nama }}? Proses ini akan memindahkan siswa kelas 11 ke kelas 12, meluluskan siswa kelas 12, dan menandai siswa kelas 10 untuk penempatan kelas 11.')">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg shadow-sm transition inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-play text-xs"></i>
                                        Aktifkan
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.destroy', $ta) }}" onsubmit="return confirm('Hapus tahun ajaran {{ $ta->nama }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3.5 py-2 bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold rounded-lg border border-rose-200 shadow-sm transition inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                        Hapus
                                    </button>
                                </form>
                            @elseif($isHistoris)
                                {{-- Tombol Tampilkan / Sembunyikan Kelas untuk TA Historis --}}
                                <button type="button"
                                        onclick="toggleKelasHistoris('{{ $ta->id }}')"
                                        id="toggleBtn-{{ $ta->id }}"
                                        class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-lg border border-slate-200 shadow-xs transition flex items-center gap-1.5">
                                    <span id="toggleText-{{ $ta->id }}">Tampilkan Kelas ({{ $ta->kelas_count }})</span>
                                    <i id="toggleIcon-{{ $ta->id }}" class="fa-solid fa-chevron-down text-slate-400 text-[11px] transition-transform duration-200"></i>
                                </button>
                                <a href="{{ route('kesiswaan.tahun-ajaran.historis', $ta) }}" class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-lg border border-slate-200 shadow-xs transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock-rotate-left text-slate-400 text-xs"></i>
                                    Lihat Data Historis
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Kelas Grid (Untuk Historis default tersembunyi / collapsible) --}}
                    <div id="kelasContainer-{{ $ta->id }}"
                         data-count="{{ $ta->kelas_count }}"
                         class="{{ $isHistoris ? 'hidden border-t border-slate-200 bg-white/70' : '' }} p-4">
                        @if($kelasForTA->isNotEmpty())
                            {{-- Group by tingkat --}}
                            @foreach(['x' => 'Kelas 10', 'xi' => 'Kelas 11', 'xii' => 'Kelas 12'] as $tingkat => $tingkatLabel)
                                @php $kelasGroup = $kelasForTA->where('tingkat', $tingkat); @endphp
                                @if($kelasGroup->isNotEmpty())
                                    <div class="mb-4 last:mb-0">
                                        <h4 class="text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-2">{{ $tingkatLabel }}</h4>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2">
                                            @foreach($kelasGroup as $kelas)
                                                @php
                                                    $isEmpty = $kelas->siswas_count === 0;
                                                    if ($isHistoris) {
                                                        $cardBg = 'bg-white border-slate-200 hover:border-slate-300';
                                                    } else {
                                                        $cardBg = $isEmpty && $ta->status === 'aktif' && $kelas->tingkat === 'xi'
                                                            ? 'bg-amber-50 border-amber-200 hover:border-amber-300'
                                                            : ($isEmpty ? 'bg-slate-50 border-slate-200 hover:border-slate-300' : 'bg-sky-50 border-sky-200 hover:border-sky-300');
                                                    }
                                                @endphp
                                                <a href="{{ route('kesiswaan.tahun-ajaran.kelas.show', [$ta, $kelas]) }}"
                                                   class="p-3 rounded-xl border {{ $cardBg }} transition-all hover:shadow-xs group">
                                                    <p class="text-xs font-bold text-slate-800 group-hover:text-sky-700 transition-colors">{{ $kelas->nama }}</p>
                                                    <div class="flex items-center gap-1 mt-1">
                                                        <i class="fa-solid fa-users text-slate-400 text-[10px]"></i>
                                                        <span class="text-[10px] font-semibold {{ $isEmpty ? 'text-slate-400' : ($isHistoris ? 'text-slate-500' : 'text-sky-600') }}">{{ $kelas->siswas_count }} siswa</span>
                                                    </div>
                                                    @if($isEmpty && $ta->status === 'aktif' && $kelas->tingkat === 'xi')
                                                        <span class="inline-block mt-1.5 text-[9px] font-bold text-amber-600 bg-amber-100 px-1.5 py-0.5 rounded">Perlu penempatan</span>
                                                    @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="py-6 text-center text-xs text-slate-400">
                                Belum ada kelas di tahun ajaran ini.
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($tahunAjarans->isEmpty())
                <div class="p-12 text-center bg-white rounded-2xl border border-sky-100 shadow-sm">
                    <div class="w-14 h-14 mx-auto bg-gradient-to-br from-sky-100 to-blue-100 border border-sky-200 rounded-2xl flex items-center justify-center text-sky-400 mb-3">
                        <i class="fa-solid fa-calendar-xmark text-sky-400 text-2xl"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-600">Belum ada tahun ajaran</p>
                    <p class="text-xs text-slate-400 mt-1">Buat tahun ajaran pertama menggunakan form di atas.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function toggleKelasHistoris(taId) {
        const container = document.getElementById('kelasContainer-' + taId);
        const textSpan = document.getElementById('toggleText-' + taId);
        const icon = document.getElementById('toggleIcon-' + taId);
        if (!container) return;

        const isHidden = container.classList.contains('hidden');
        if (isHidden) {
            container.classList.remove('hidden');
            if (textSpan) textSpan.textContent = 'Sembunyikan Kelas';
            if (icon) icon.style.transform = 'rotate(180deg)';
        } else {
            container.classList.add('hidden');
            const count = container.getAttribute('data-count') || '';
            if (textSpan) textSpan.textContent = 'Tampilkan Kelas' + (count ? ' (' + count + ')' : '');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    }

    function openModalKelulusan(kelasId) {
        const modal = document.getElementById('modalKelulusan' + kelasId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            refreshKelulusanSummary(kelasId);
        }
    }

    function closeModalKelulusan(kelasId) {
        const modal = document.getElementById('modalKelulusan' + kelasId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function toggleSelectAll(kelasId, isChecked) {
        const checkboxes = document.querySelectorAll('.kelulusan-checkbox-' + kelasId);
        checkboxes.forEach(cb => {
            cb.checked = isChecked;
            const siswaId = cb.value;
            const badgeLulus = document.getElementById('badgeLulus' + kelasId + '_' + siswaId);
            const badgeTinggal = document.getElementById('badgeTinggal' + kelasId + '_' + siswaId);
            const row = document.getElementById('rowSiswa' + kelasId + '_' + siswaId);

            if (isChecked) {
                badgeLulus?.classList.remove('hidden');
                badgeTinggal?.classList.add('hidden');
                row?.classList.remove('bg-amber-50/50');
            } else {
                badgeLulus?.classList.add('hidden');
                badgeTinggal?.classList.remove('hidden');
                row?.classList.add('bg-amber-50/50');
            }
        });
        refreshKelulusanSummary(kelasId);
    }

    function onCheckboxChange(kelasId, cb, siswaId) {
        const badgeLulus = document.getElementById('badgeLulus' + kelasId + '_' + siswaId);
        const badgeTinggal = document.getElementById('badgeTinggal' + kelasId + '_' + siswaId);
        const row = document.getElementById('rowSiswa' + kelasId + '_' + siswaId);

        if (cb.checked) {
            badgeLulus?.classList.remove('hidden');
            badgeTinggal?.classList.add('hidden');
            row?.classList.remove('bg-amber-50/50');
        } else {
            badgeLulus?.classList.add('hidden');
            badgeTinggal?.classList.remove('hidden');
            row?.classList.add('bg-amber-50/50');
        }

        const checkboxes = Array.from(document.querySelectorAll('.kelulusan-checkbox-' + kelasId));
        const allChecked = checkboxes.length > 0 && checkboxes.every(c => c.checked);
        const checkAll = document.getElementById('checkAll' + kelasId);
        if (checkAll) {
            checkAll.checked = allChecked;
        }

        refreshKelulusanSummary(kelasId);
    }

    function refreshKelulusanSummary(kelasId) {
        const checkboxes = Array.from(document.querySelectorAll('.kelulusan-checkbox-' + kelasId));
        const total = checkboxes.length;
        const lulusCount = checkboxes.filter(c => c.checked).length;
        const tinggalCount = total - lulusCount;

        const counterLulus = document.getElementById('counterLulus' + kelasId);
        const counterTinggal = document.getElementById('counterTinggal' + kelasId);

        if (counterLulus) {
            const numSpan = counterLulus.querySelector('.counter-num');
            if (numSpan) numSpan.textContent = lulusCount;
        }
        if (counterTinggal) {
            const numSpan = counterTinggal.querySelector('.counter-num');
            if (numSpan) numSpan.textContent = tinggalCount;
            if (tinggalCount > 0) {
                counterTinggal.className = 'text-amber-700 font-bold transition';
            } else {
                counterTinggal.className = 'text-slate-400 transition';
            }
        }
    }

    function filterSiswaList(kelasId, query) {
        const term = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#listContainer' + kelasId + ' .siswa-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const nama = row.getAttribute('data-nama') || '';
            const nisn = row.getAttribute('data-nisn') || '';
            const nis = row.getAttribute('data-nis') || '';
            if (!term || nama.includes(term) || nisn.includes(term) || nis.includes(term)) {
                row.style.display = 'flex';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyMsg = document.getElementById('emptySearch' + kelasId);
        if (emptyMsg) {
            if (visibleCount === 0 && term) {
                emptyMsg.classList.remove('hidden');
            } else {
                emptyMsg.classList.add('hidden');
            }
        }
    }

    function confirmProsesKelulusan(form) {
        const kelasNama = form.getAttribute('data-kelas-nama') || 'Kelas 12';
        const kelasId = form.getAttribute('data-kelas-id');
        const checkboxes = Array.from(document.querySelectorAll('.kelulusan-checkbox-' + kelasId));
        const total = checkboxes.length;
        const lulusCount = checkboxes.filter(c => c.checked).length;
        const tinggalCount = total - lulusCount;

        let pesan = 'Konfirmasi Kelulusan Kelas ' + kelasNama + ':\n\n';
        pesan += '- ' + lulusCount + ' siswa DILULUSKAN (status Alumni / nonaktif)\n';
        if (tinggalCount > 0) {
            pesan += '- ' + tinggalCount + ' siswa TINGGAL KELAS (ditempatkan di kelas 12 tahun ajaran aktif)\n';
        } else {
            pesan += '- 0 siswa tinggal kelas (semua siswa dinyatakan lulus)\n';
        }
        pesan += '\nLanjutkan dan simpan data kelulusan ini?';

        return confirm(pesan);
    }
</script>
@endsection
