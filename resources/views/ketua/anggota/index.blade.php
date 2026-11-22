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
        $hasFilter = request()->filled('cari') || request()->filled('status') || request()->filled('kelas_id') || $tampilArsip;
    @endphp
    <div x-data="{ openFilter: {{ (request()->filled('status') || request()->filled('kelas_id') || $tampilArsip) ? 'true' : 'false' }} }"
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
                 class="pt-3 border-t border-slate-100 dark:border-neutral-800 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">Kelas</label>
                    <select name="kelas_id" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-slate-200 focus:outline-none focus:border-sky-500 transition-all appearance-none font-medium">
                        <option value="">Semua Kelas</option>
                        @foreach (($daftarKelas ?? collect()) as $kelasOpt)
                            <option value="{{ $kelasOpt->id }}" {{ request('kelas_id') == $kelasOpt->id ? 'selected' : '' }}>{{ $kelasOpt->nama }}</option>
                        @endforeach
                    </select>
                </div>

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

    <div class="ketua-card-list bg-white dark:bg-[#1a1a1a] p-5 md:p-6 rounded-2xl border border-slate-200/80 dark:border-neutral-800 shadow-sm">
        <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Daftar Anggota</h3>
                <p class="text-xs text-slate-400 dark:text-neutral-500 mt-0.5">Pilih nama siswa untuk melihat profil dan riwayat keanggotaan.</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-400 rounded-xl">
                Total {{ $anggotas->total() }} Anggota
            </span>
        </div>
        <div class="overflow-x-auto md:overflow-visible">
        <table class="card-table ketua-card-anggota w-full text-xs">
            <thead>
                <tr>
                    <th class="p-3 font-semibold rounded-l-xl text-left bg-slate-50 dark:bg-neutral-800/80 text-slate-500 dark:text-neutral-400 border-b border-slate-100 dark:border-neutral-800">No</th>
                    @include('partials.th-sort', ['label' => 'Identitas Siswa', 'key' => 'nama', 'sort' => $sort, 'direction' => $direction, 'class' => 'p-3 font-semibold text-left bg-slate-50 dark:bg-neutral-800/80 text-slate-500 dark:text-neutral-400 border-b border-slate-100 dark:border-neutral-800'])
                    <th class="p-3 font-semibold text-left bg-slate-50 dark:bg-neutral-800/80 text-slate-500 dark:text-neutral-400 border-b border-slate-100 dark:border-neutral-800">Kelas</th>
                    <th class="p-3 font-semibold text-left bg-slate-50 dark:bg-neutral-800/80 text-slate-500 dark:text-neutral-400 border-b border-slate-100 dark:border-neutral-800">Jabatan</th>
                    @include('partials.th-sort', ['label' => 'Status', 'key' => 'status', 'sort' => $sort, 'direction' => $direction, 'class' => 'p-3 font-semibold text-left bg-slate-50 dark:bg-neutral-800/80 text-slate-500 dark:text-neutral-400 border-b border-slate-100 dark:border-neutral-800'])
                    <th class="p-3 font-semibold text-center rounded-r-xl bg-slate-50 dark:bg-neutral-800/80 text-slate-500 dark:text-neutral-400 border-b border-slate-100 dark:border-neutral-800">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                @forelse($anggotas as $anggota)
                    @php
                        $isSelf = $anggota->siswa_id === (auth()->user()->siswa->id ?? null);
                        $profileUrl = route('ketua.anggota.show', ['pendaftaran' => $anggota->id] + request()->query());
                        $isKetuaEkskul = $anggota->siswa?->jabatan === 'ketua'
                            && $ketuaId === $anggota->siswa_id;
                    @endphp
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-neutral-800/40 transition {{ in_array($anggota->status, ['nonaktif', 'keluar']) ? 'opacity-60' : '' }}">
                        <td class="p-3 text-slate-400 dark:text-neutral-500 font-medium">{{ $anggotas->firstItem() + $loop->index }}</td>
                        <td class="p-3">
                            <a href="{{ route('ketua.anggota.show', ['pendaftaran' => $anggota->id] + request()->query()) }}" class="flex items-center gap-2.5 group rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500">
                                <div class="w-8 h-8 rounded-xl overflow-hidden shrink-0 flex items-center justify-center font-bold text-xs bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-400 border border-slate-200/80 dark:border-neutral-700">
                                    @if($anggota->siswa?->foto_url)
                                        <img src="{{ $anggota->siswa->foto_url }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <span>{{ strtoupper(substr($anggota->siswa->nama ?? 'S', 0, 2)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-extrabold text-slate-900 dark:text-neutral-100 group-hover:text-sky-600 dark:group-hover:text-sky-400 block transition">{{ $anggota->siswa->nama ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-400 dark:text-neutral-500">NIS: {{ $anggota->siswa->nis ?? '-' }}</span>
                                </div>
                            </a>
                        </td>
                        <td class="p-3 font-medium text-slate-700 dark:text-neutral-300">{{ $anggota->siswa->kelas->nama ?? '-' }}</td>
                        <td class="p-3">
                            @if($isKetuaEkskul)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-slate-900 dark:bg-white text-white dark:text-slate-900">
                                    <i class="fa-solid fa-crown text-[9px]"></i> Ketua
                                </span>
                            @elseif($anggota->siswa?->jabatan === 'ketua')
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-neutral-400 text-[10px] font-medium">Ketua Lain</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-neutral-400 text-[10px] font-medium">Anggota</span>
                            @endif
                        </td>
                        <td class="p-3 whitespace-nowrap">
                            @if($anggota->status === 'diterima')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/50">Aktif</span>
                            @elseif($anggota->status === 'peringatan')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/50">Peringatan</span>
                            @elseif($anggota->status === 'nonaktif')
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-neutral-800 text-slate-500 dark:text-neutral-400 border border-slate-200 dark:border-neutral-700">Nonaktif</span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 border border-rose-200/80 dark:border-rose-800/50">Keluar</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <div class="flex flex-wrap items-center justify-center gap-2">
                                <a href="{{ $profileUrl }}"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-sky-200 dark:border-sky-800/60 bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 text-[11px] font-bold hover:bg-sky-100 dark:hover:bg-sky-900/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 transition"
                                   aria-label="Lihat profil {{ $anggota->siswa->nama ?? 'siswa' }}">
                                    <i class="fa-regular fa-id-card text-[10px]" aria-hidden="true"></i>
                                    Profil
                                </a>
                                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                                    <button type="button"
                                            @unless($isSelf) @click="open = !open" @endunless
                                            @disabled($isSelf)
                                            aria-haspopup="menu"
                                            aria-label="Aksi keanggotaan untuk {{ $anggota->siswa->nama ?? 'siswa' }}"
                                            :aria-expanded="open.toString()"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border text-[11px] font-bold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 {{ $isSelf ? 'border-slate-200 dark:border-neutral-700 bg-slate-100 dark:bg-neutral-800 text-slate-400 dark:text-neutral-500 cursor-not-allowed' : 'border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-neutral-800' }}">
                                        Aksi
                                        <i class="fa-solid fa-chevron-down text-[9px]" aria-hidden="true"></i>
                                    </button>
                                    @unless($isSelf)
                                        <div x-show="open" x-cloak x-transition
                                             role="menu"
                                             class="absolute right-0 z-30 mt-2 w-40 rounded-xl border border-slate-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-1.5 shadow-lg text-left">
                                            @if($anggota->status === 'diterima')
                                                <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST"
                                                      onsubmit="return confirm('Beri peringatan kepada {{ addslashes($anggota->siswa->nama) }}? Siswa akan menerima alert peringatan di akunnya. Status tetap aktif.');">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="peringatan">
                                                    <button type="submit" role="menuitem" class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-left text-xs font-semibold text-amber-700 dark:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-950/40 transition">
                                                        <i class="fa-solid fa-triangle-exclamation w-3 text-center" aria-hidden="true"></i>
                                                        Peringatan
                                                    </button>
                                                </form>
                                                <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST"
                                                      onsubmit="return confirm('Nonaktifkan {{ addslashes($anggota->siswa->nama) }}? Siswa tidak akan terhitung anggota aktif ekskul.');">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="nonaktif">
                                                    <button type="submit" role="menuitem" class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-left text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition">
                                                        <i class="fa-solid fa-user-slash w-3 text-center" aria-hidden="true"></i>
                                                        Nonaktifkan
                                                    </button>
                                                </form>
                                            @elseif($anggota->status === 'peringatan')
                                                <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="diterima">
                                                    <button type="submit" role="menuitem" class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-left text-xs font-semibold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition">
                                                        <i class="fa-solid fa-user-check w-3 text-center" aria-hidden="true"></i>
                                                        Aktifkan
                                                    </button>
                                                </form>
                                                <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST"
                                                      onsubmit="return confirm('Nonaktifkan {{ addslashes($anggota->siswa->nama) }}? Siswa tidak akan terhitung anggota aktif ekskul.');">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="nonaktif">
                                                    <button type="submit" role="menuitem" class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-left text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition">
                                                        <i class="fa-solid fa-user-slash w-3 text-center" aria-hidden="true"></i>
                                                        Nonaktifkan
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="diterima">
                                                    <button type="submit" role="menuitem" class="w-full flex items-center gap-2 px-2.5 py-2 rounded-lg text-left text-xs font-semibold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 transition">
                                                        <i class="fa-solid fa-user-check w-3 text-center" aria-hidden="true"></i>
                                                        Aktifkan
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endunless
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-neutral-800 flex items-center justify-center mx-auto mb-3 text-slate-400">
                                <i class="fa-solid fa-users text-lg"></i>
                            </div>
                            <p class="text-xs font-semibold">Tidak ada anggota yang sesuai dengan filter yang dipilih.</p>
                        </td>
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
