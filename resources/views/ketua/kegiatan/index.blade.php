@extends('ketua.layout')
@section('title', 'Daftar Kegiatan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white">Daftar Kegiatan</h1>
            <p class="text-xs text-slate-400 dark:text-neutral-400 mt-1">Total: {{ $total }} kegiatan</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="{{ route('ketua.kegiatan.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 dark:shadow-none transition inline-flex items-center justify-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Kegiatan baru
            </a>
        </div>
    </div>

    @include('partials.table-filters', [
        'action' => route('ketua.kegiatan.index'),
        'placeholder' => 'Cari kegiatan...',
        'filters' => [
            ['name' => 'jenis', 'allLabel' => 'Semua Jenis', 'options' => ['rutin' => 'Rutin', 'event' => 'Event']],
        ],
    ])

    <!-- Table Card -->
    <div class="ketua-card-list bg-white dark:bg-neutral-900 rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="card-table ketua-card-kegiatan w-full text-left text-xs md:text-sm">
                <thead class="bg-slate-50 dark:bg-neutral-800 text-slate-500 dark:text-neutral-400">
                    <tr>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 dark:text-neutral-400 whitespace-nowrap rounded-l-xl">No</th>
                        @include('partials.th-sort', ['label' => 'Tanggal', 'key' => 'tanggal_kegiatan', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 dark:text-neutral-400 whitespace-nowrap'])
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 dark:text-neutral-400 whitespace-nowrap">Hari</th>
                        @include('partials.th-sort', ['label' => 'Kegiatan', 'key' => 'materi', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 dark:text-neutral-400 whitespace-nowrap'])
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 dark:text-neutral-400 whitespace-nowrap">Presensi</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 dark:text-neutral-400 whitespace-nowrap rounded-r-xl text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50 dark:divide-neutral-800">
                    @forelse($kegiatans as $kegiatan)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-neutral-800/60 transition" data-card-href="{{ route('ketua.kegiatan.show', $kegiatan) }}">
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-500 dark:text-neutral-400">{{ $loop->iteration }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap font-medium text-slate-700 dark:text-neutral-300">
                                @if($kegiatan->isEvent() && $kegiatan->tanggal_berakhir)
                                    {{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }} &ndash; {{ $kegiatan->tanggal_berakhir->format('d/m/Y') }}
                                @else
                                    {{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }}
                                @endif
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-600 dark:text-neutral-400">
                                {{ $kegiatan->tanggal_kegiatan->isoFormat('dddd') }}
                                @if($kegiatan->isEvent())
                                    <span class="ml-1.5 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">Event</span>
                                @endif
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 font-medium text-slate-800 dark:text-neutral-200 max-w-xs truncate" title="{{ $kegiatan->materi }}">
                                <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="font-bold text-slate-800 dark:text-neutral-100 hover:text-sky-600 dark:hover:text-sky-400 transition">
                                    {{ $kegiatan->materi }}
                                </a>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-100 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800/50">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    {{ $kegiatan->presensis_count }} orang
                                </span>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-center">
                                <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="card-detail-link inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/40 dark:hover:bg-sky-900/50 text-sky-600 dark:text-sky-400 font-semibold rounded-xl border border-sky-200 dark:border-sky-800/50 transition text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 md:px-6 py-8 md:py-12 text-center text-slate-400 dark:text-neutral-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 md:w-12 md:h-12 text-slate-300 dark:text-neutral-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm font-medium">Belum ada kegiatan</p>
                                    <p class="text-xs mt-1">Klik "Kegiatan Baru" untuk membuat kegiatan pertama</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.table-pagination', ['rows' => $kegiatans, 'label' => 'kegiatan'])
    </div>
</div>
@endsection
