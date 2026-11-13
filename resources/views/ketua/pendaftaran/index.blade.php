@extends('ketua.layout')
@section('title', 'Daftar Pendaftaran' . (isset($ekskul) ? ' - ' . $ekskul->nama_ekskul : ''))

@section('content')
@if(isset($isCatalog) && $isCatalog)
    @include('partials.ketua-card-catalog', [
        'title' => 'Pendaftaran Anggota Ekskul',
        'subtitle' => 'Pilih salah satu ekstrakurikuler di bawah ini untuk melihat daftar siswa yang mendaftar dan memverifikasi status pendaftarannya.',
        'featureBadge' => 'Operasional Ketua • Pendaftaran',
        'icon' => 'fa-solid fa-clipboard-list',
        'cards' => $cards,
    ])
@else
<div class="space-y-6">
    @if(isset($isKesiswaan) && $isKesiswaan)
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('ketua.pendaftaran.index') }}"
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

    <!-- Page Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-5 md:p-7 text-white soul-timbul shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>
        <div class="relative">
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight">Daftar Pendaftaran: {{ $ekskul->nama_ekskul }}</h1>
            <p class="mt-1 text-xs md:text-sm text-white/80">Ringkasan status pendaftar ekskul</p>
        </div>
    </div>

    <div class="inline-flex items-center gap-4 rounded-2xl border border-sky-100 bg-white px-5 py-4 shadow-md shadow-sky-100/60 dark:bg-neutral-800 dark:border-neutral-700">
        <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-900/30 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Pendaftar</p>
            <p class="text-2xl font-extrabold tracking-tight text-slate-800 dark:text-white">{{ $total }}</p>
        </div>
    </div>

    @include('partials.table-filters', [
        'action' => route('ketua.pendaftaran.index'),
        'placeholder' => 'Cari nama / NIS...',
        'filters' => [],
    ])

    <!-- Table Card dengan overflow-x-auto -->
    <div class="ketua-card-list bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="card-table ketua-card-pendaftaran w-full text-left text-xs md:text-sm">
                <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                    <tr>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap rounded-l-xl">No</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">NIS</th>
                        @include('partials.th-sort', ['label' => 'Nama', 'key' => 'nama', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Kelas</th>
                        @include('partials.th-sort', ['label' => 'Tanggal Daftar', 'key' => 'tanggal_daftar', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                        @include('partials.th-sort', ['label' => 'Status', 'key' => 'status', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @forelse($pendaftarans as $pendaftaran)
                        <tr class="hover:bg-sky-50/50 transition" data-card-href="{{ route('ketua.pendaftaran.show', $pendaftaran) }}">
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap font-medium text-slate-700">{{ $pendaftaran->siswa->nis }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap font-medium text-slate-800">{{ $pendaftaran->siswa->nama }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-600">{{ $pendaftaran->siswa->kelas->nama ?? '-' }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-600">{{ $pendaftaran->tanggal_daftar->format('d/m/Y') }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap">
                                @if($pendaftaran->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        Pending
                                    </span>
                                @elseif($pendaftaran->status === 'diterima')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Diterima
                                    </span>
                                @elseif($pendaftaran->status === 'ditolak')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">{{ ucfirst($pendaftaran->status) }}</span>
                                @endif
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap">
                                <a href="{{ route('ketua.pendaftaran.show', $pendaftaran) }}" class="card-detail-link inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-700 font-semibold rounded-xl hover:from-sky-200 hover:to-blue-200 transition text-xs">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 md:px-6 py-8 md:py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 md:w-12 md:h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm font-medium">Belum ada pendaftaran</p>
                                    <p class="text-xs mt-1">Belum ada siswa yang mendaftar ke ekskul ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.table-pagination', ['rows' => $pendaftarans, 'label' => 'pendaftaran'])
    </div>
</div>
@endif
@endsection
