@extends('ketua.layout')
@section('title', 'Kelola Anggota' . (isset($ekskul) ? ' - ' . $ekskul->nama_ekskul : ''))

@section('content')
@if(isset($isCatalog) && $isCatalog)
    @include('partials.ketua-card-catalog', [
        'title' => 'Kelola Anggota Ekskul',
        'subtitle' => 'Pilih salah satu ekstrakurikuler di bawah ini untuk melihat daftar anggota, menambah anggota baru, atau memperbarui status keanggotaan.',
        'featureBadge' => 'Operasional Ketua • Keanggotaan',
        'icon' => 'fa-solid fa-users',
        'cards' => $cards,
    ])
@else
<div class="space-y-6">
    @if(isset($isKesiswaan) && $isKesiswaan)
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('ketua.anggota.index') }}"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition shrink-0"
               title="Kembali ke Pilihan Ekskul">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Pilih Ekskul Lain</span>
            </a>
            <span class="px-2.5 py-1 rounded-xl text-xs font-extrabold bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/50">
                Ekskul: {{ $ekskul->nama_ekskul }}
            </span>
        </div>
    @endif

    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-5 md:p-7 text-white soul-timbul shadow-xl shadow-sky-200 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-xl md:text-2xl font-extrabold text-white">Kelola Anggota: {{ $ekskul->nama_ekskul }}</h1>
                <p class="text-xs text-white/80 mt-1">Total: {{ $totalAnggotas }} anggota</p>
            </div>
        </div>
    </div>

    @php
        $statusOptions = ['diterima' => 'Aktif', 'peringatan' => 'Peringatan', 'nonaktif' => 'Nonaktif', 'keluar' => 'Keluar'];
        $hasFilter = request()->filled('cari') || request()->filled('status') || $tampilArsip;
    @endphp
    <div x-data="{ openFilter: {{ (request()->filled('status') || $tampilArsip) ? 'true' : 'false' }} }"
         class="bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm">
        <form method="GET" action="{{ route('ketua.anggota.index') }}" class="space-y-3">
            <input type="hidden" name="ekskul" value="{{ $ekskul->id }}">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="search" name="cari" value="{{ request('cari') }}" placeholder="Cari nama / NIS anggota..."
                        class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-500 transition-all">
                </div>

                <button type="button" @click="openFilter = !openFilter"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border text-xs font-bold transition shadow-xs"
                    :class="openFilter || {{ (request()->filled('status') || $tampilArsip) ? 'true' : 'false' }} ? 'bg-sky-50 dark:bg-sky-950/50 border-sky-300 dark:border-sky-700 text-sky-700 dark:text-sky-300' : 'bg-white dark:bg-neutral-800 border-slate-200 dark:border-neutral-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50'">
                    <i class="fa-solid fa-sliders text-xs"></i>
                    <span>Filter</span>
                    @if(request()->filled('status') || $tampilArsip)
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    @endif
                    <i class="fa-solid text-[10px] text-slate-400 transition-transform duration-200" :class="openFilter ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>

                <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold rounded-xl transition shadow-xs">
                    Cari
                </button>

                @if($hasFilter)
                    <a href="{{ route('ketua.anggota.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-500 hover:text-rose-600 transition text-center">Reset</a>
                @endif
            </div>

            <div x-show="openFilter" x-cloak x-transition
                 class="pt-3 border-t border-slate-100 dark:border-neutral-800 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Status Keanggotaan</label>
                    <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none font-medium">
                        <option value="">Anggota Aktif</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                        <option value="semua" {{ request('status') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Arsip Alumni / Nonaktif</label>
                    <select name="arsip" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none font-medium">
                        <option value="0" {{ ! $tampilArsip ? 'selected' : '' }}>Tanpa Arsip</option>
                        <option value="1" {{ $tampilArsip ? 'selected' : '' }}>Tampilkan Arsip ({{ $arsipTotal }})</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 mb-4">
        <p class="text-xs font-bold text-slate-500 mb-3 flex items-center gap-2">
            <span class="w-7 h-7 rounded-xl bg-gradient-to-br from-amber-100 to-yellow-100 border border-amber-200 flex items-center justify-center text-amber-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-8.24 14a2 2 0 001.73 3h16.44a2 2 0 001.73-3l-8.24-14a2 2 0 00-3.42 0z"/>
                </svg>
            </span>
            Status Keanggotaan
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-[11px]">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"></span>
                <span class="text-slate-500"><strong class="text-emerald-600">Aktif</strong> — anggota yang masih bergabung & mengikuti ekskul.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-sm shadow-amber-200"></span>
                <span class="text-slate-500"><strong class="text-amber-600">Peringatan</strong> ({{ $peringatanCount }}) — anggota yang diberi notifikasi korektif; tetap terhitung aktif.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-sm shadow-rose-200"></span>
                <span class="text-slate-500"><strong class="text-rose-500">Nonaktif</strong> ({{ $nonaktifCount }}) — anggota yang dinonaktifkan oleh ketua.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shadow-sm shadow-slate-200"></span>
                <span class="text-slate-500"><strong class="text-slate-600">Keluar</strong> ({{ $keluarCount }}) — anggota yang mengajukan keluar dan disetujui.</span>
            </div>
        </div>
    </div>

    <div class="ketua-card-list bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="card-table ketua-card-anggota w-full text-left text-xs md:text-sm">
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                <tr>
                    <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    @include('partials.th-sort', ['label' => 'NIS', 'key' => 'nis', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                    @include('partials.th-sort', ['label' => 'Nama', 'key' => 'nama', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                    <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Kelas</th>
                    @include('partials.th-sort', ['label' => 'Status', 'key' => 'status', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                    <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                @forelse($anggotas as $anggota)
                    @php
                        $isSelf = $anggota->siswa_id === (auth()->user()->siswa->id ?? null);
                    @endphp
                    <tr class="hover:bg-sky-50/50 transition {{ in_array($anggota->status, ['nonaktif', 'keluar']) ? 'opacity-60' : '' }}">
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-500">{{ $loop->iteration }}</td>
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-600">{{ $anggota->siswa->nis }}</td>
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-800">
                            {{ $anggota->siswa->nama }}
                            @if($isSelf)
                                <span class="ml-1 px-1.5 py-0.5 bg-sky-100 text-sky-700 border border-sky-200 rounded-md text-[9px] font-bold">Kamu</span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-600">{{ $anggota->siswa->kelas->nama ?? '-' }}</td>
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap">
                            @if($anggota->status === 'diterima')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @elseif($anggota->status === 'peringatan')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-amber-50 to-yellow-50 text-amber-700 border border-amber-200 text-xs font-semibold rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-8.24 14a2 2 0 001.73 3h16.44a2 2 0 001.73-3l-8.24-14a2 2 0 00-3.42 0z"/>
                                    </svg>
                                    Peringatan
                                </span>
                            @elseif($anggota->status === 'keluar')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Keluar
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-600 border border-rose-200 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap">
                            @if($isSelf)
                                <span class="text-slate-300 text-[11px]">—</span>
                            @else
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($anggota->status === 'diterima')
                                        <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST"
                                            onsubmit="return confirm('Beri peringatan kepada {{ addslashes($anggota->siswa->nama) }}? Siswa akan menerima alert peringatan di akunnya. Status tetap aktif.');">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="peringatan">
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 border border-amber-200 font-semibold rounded-full hover:from-amber-200 hover:to-yellow-200 transition text-xs">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-8.24 14a2 2 0 001.73 3h16.44a2 2 0 001.73-3l-8.24-14a2 2 0 00-3.42 0z"/>
                                                </svg>
                                                Peringatan
                                            </button>
                                        </form>
                                        <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST"
                                            onsubmit="return confirm('Nonaktifkan {{ addslashes($anggota->siswa->nama) }}? Siswa tidak akan terhitung anggota aktif ekskul.');">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="nonaktif">
                                            <button type="submit" class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-200 text-xs font-semibold rounded-full transition">Nonaktifkan</button>
                                        </form>
                                    @elseif($anggota->status === 'peringatan')
                                        <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="diterima">
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-full transition">Aktifkan</button>
                                        </form>
                                        <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST"
                                            onsubmit="return confirm('Nonaktifkan {{ addslashes($anggota->siswa->nama) }}? Siswa tidak akan terhitung anggota aktif ekskul.');">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="nonaktif">
                                            <button type="submit" class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-200 text-xs font-semibold rounded-full transition">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="diterima">
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-full transition">Aktifkan</button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400">Tidak ada anggota yang sesuai dengan filter yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @include('partials.table-pagination', ['rows' => $anggotas, 'label' => 'anggota'])
    </div>

    {{-- ARSIP: hanya muncul saat filter "Tampilkan Arsip" aktif --}}
    @if($tampilArsip)
        <div class="h-6"></div>
        @include('partials.anggota-arsip', [
            'arsip' => $arsip,
            'tampilArsip' => $tampilArsip,
            'arsipAction' => route('ketua.anggota.index'),
            'arsipShowEkskul' => false,
            'arsipPeriodeRoute' => null,
        ])
    @endif


</div>
@endif
@endsection
