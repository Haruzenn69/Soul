@extends('layouts.kesiswaan')
@section('title', 'Tahun Ajaran')

@section('content')
    <div class="space-y-6 animate-fade-up">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Tahun Ajaran</h1>
                <p class="text-xs text-slate-400 mt-0.5">Kelola tahun ajaran, pantau kelas, dan proses kenaikan kelas siswa</p>
            </div>
            <a href="{{ route('kesiswaan.kelas.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-sky-100 rounded-xl text-xs font-bold text-sky-700 hover:bg-sky-50 shadow-sm transition">
                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Kelola & Buat Kelas di Data Kelas
            </a>
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
                <p class="text-xl font-extrabold {{ $pendingSiswa > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $pendingSiswa }}</p>
                <p class="text-xs font-bold text-slate-400">Menunggu Penempatan</p>
            </div>
        </div>

        {{-- BUAT TAHUN AJARAN BARU --}}
        <div class="bg-white rounded-2xl border border-sky-100 shadow-sm p-5 animate-fade-up" style="animation-delay: .25s">
            <h2 class="text-sm font-extrabold text-slate-900 mb-3">Buat Tahun Ajaran Baru</h2>
            <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.store') }}" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="text" name="nama" placeholder="Contoh: 2027/2028" required
                    class="flex-1 px-4 py-2.5 bg-sky-50/60 border border-sky-100 rounded-xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition"
                    pattern="\d{4}/\d{4}">
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 transition hover:-translate-y-0.5 whitespace-nowrap">
                    + Buat Tahun Ajaran
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
                    $statusColor = match($ta->status) {
                        'aktif' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                        'nonaktif' => 'bg-amber-50 border-amber-200 text-amber-700',
                        'historis' => 'bg-slate-50 border-slate-200 text-slate-500',
                    };
                    $statusLabel = match($ta->status) {
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        'historis' => 'Historis',
                    };
                    $statusIcon = match($ta->status) {
                        'aktif' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        'nonaktif' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        'historis' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                    };

                    $kelasForTA = ($ta->status === 'aktif')
                        ? $activeKelas
                        : \App\Models\Kelas::where('tahun_ajaran_id', $ta->id)
                            ->withCount('siswas')
                            ->orderByRaw("FIELD(tingkat, 'x', 'xi', 'xii')")
                            ->orderBy('jurusan')->orderBy('rombel')->get();
                @endphp

                <div class="bg-white rounded-2xl border border-sky-100 shadow-sm overflow-hidden animate-fade-up">
                    {{-- TA Header --}}
                    <div class="px-5 py-4 border-b border-sky-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-sky-200">
                                TA
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900">{{ $ta->nama }}</h3>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $statusColor }}">
                                        {!! $statusIcon !!} {{ $statusLabel }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">{{ $ta->kelas_count }} kelas</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($ta->status === 'aktif')
                                <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.deactivate', $ta) }}"
                                    onsubmit="return confirm('Yakin ingin menonaktifkan tahun ajaran {{ $ta->nama }}? Tahun ajaran ini akan menjadi arsip historis.')">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold rounded-lg border border-amber-200 shadow-sm transition">
                                        Nonaktifkan (Jadikan Historis)
                                    </button>
                                </form>
                            @elseif($ta->status === 'nonaktif')
                                <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.activate', $ta) }}"
                                    onsubmit="return confirm('Yakin aktifkan tahun ajaran {{ $ta->nama }}? Proses ini akan memindahkan siswa kelas 11 ke kelas 12, meluluskan siswa kelas 12, dan menandai siswa kelas 10 untuk penempatan kelas 11.')">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                        Aktifkan
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('kesiswaan.tahun-ajaran.destroy', $ta) }}" onsubmit="return confirm('Hapus tahun ajaran {{ $ta->nama }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3.5 py-2 bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold rounded-lg border border-rose-200 shadow-sm transition">
                                        Hapus
                                    </button>
                                </form>
                            @elseif($ta->status === 'historis')
                                <a href="{{ route('kesiswaan.tahun-ajaran.historis', $ta) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg border border-slate-200 shadow-sm transition flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    Lihat Data Historis
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Kelas Grid --}}
                    @if($kelasForTA->isNotEmpty())
                        <div class="p-4">
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
                                                    $cardBg = $isEmpty && $ta->status === 'aktif' && $kelas->tingkat === 'xi'
                                                        ? 'bg-amber-50 border-amber-200 hover:border-amber-300'
                                                        : ($isEmpty ? 'bg-slate-50 border-slate-200 hover:border-slate-300' : 'bg-sky-50 border-sky-200 hover:border-sky-300');
                                                @endphp
                                                <a href="{{ route('kesiswaan.tahun-ajaran.kelas.show', [$ta, $kelas]) }}"
                                                   class="p-3 rounded-xl border {{ $cardBg }} transition-all hover:shadow-sm group">
                                                    <p class="text-xs font-bold text-slate-800 group-hover:text-sky-700 transition-colors">{{ $kelas->nama }}</p>
                                                    <div class="flex items-center gap-1 mt-1">
                                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        </svg>
                                                        <span class="text-[10px] font-semibold {{ $isEmpty ? 'text-slate-400' : 'text-sky-600' }}">{{ $kelas->siswas_count }} siswa</span>
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
                        </div>
                    @else
                        <div class="p-8 text-center text-xs text-slate-400">
                            Belum ada kelas di tahun ajaran ini.
                        </div>
                    @endif
                </div>
            @endforeach

            @if($tahunAjarans->isEmpty())
                <div class="p-12 text-center bg-white rounded-2xl border border-sky-100 shadow-sm">
                    <div class="w-14 h-14 mx-auto bg-gradient-to-br from-sky-100 to-blue-100 border border-sky-200 rounded-2xl flex items-center justify-center text-sky-400 mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-600">Belum ada tahun ajaran</p>
                    <p class="text-xs text-slate-400 mt-1">Buat tahun ajaran pertama menggunakan form di atas.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
