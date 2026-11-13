@extends('ketua.layout')
@section('title', 'Daftar Pengajuan Keluar' . (isset($ekskul) ? ' - ' . $ekskul->nama_ekskul : ''))

@section('content')
@if(isset($isCatalog) && $isCatalog)
    @include('partials.ketua-card-catalog', [
        'title' => 'Pengajuan Keluar Anggota',
        'subtitle' => 'Pilih salah satu ekstrakurikuler di bawah ini untuk melihat dan menindaklanjuti permohonan keluar dari anggota.',
        'featureBadge' => 'Operasional Ketua • Pengajuan Keluar',
        'icon' => 'fa-solid fa-arrow-right-from-bracket',
        'cards' => $cards,
    ])
@else
<div class="space-y-6">
    @if(isset($isKesiswaan) && $isKesiswaan)
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('ketua.pengajuan-keluar.index') }}"
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
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight">Daftar Pengajuan Keluar: {{ $ekskul->nama_ekskul }}</h1>
            <p class="mt-1 text-xs md:text-sm text-white/80">Total: {{ $total }} pengajuan</p>
        </div>
    </div>

    @include('partials.table-filters', [
        'action' => route('ketua.pengajuan-keluar.index'),
        'placeholder' => 'Cari nama / alasan...',
        'filters' => [
            ['name' => 'status', 'allLabel' => 'Semua Status', 'options' => ['pending' => 'Pending', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak']],
        ],
    ])

    <!-- Table Card -->
    <div class="ketua-card-list bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="card-table ketua-card-pengajuan w-full text-left text-xs md:text-sm">
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    @include('partials.th-sort', ['label' => 'Nama', 'key' => 'nama', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                    @include('partials.th-sort', ['label' => 'Tanggal', 'key' => 'tanggal_pengajuan', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Alasan</th>
                    @include('partials.th-sort', ['label' => 'Status', 'key' => 'status', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                @forelse($pengajuanKeluars as $pengajuan)
                    <tr class="hover:bg-sky-50/50 transition" data-card-href="{{ route('ketua.pengajuan-keluar.show', $pengajuan) }}">
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $pengajuan->siswa->nama }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap max-w-xs truncate">{{ Str::limit($pengajuan->alasan, 30) }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            @if($pengajuan->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">Pending</span>
                            @elseif($pengajuan->status === 'diterima')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">Diterima</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <a href="{{ route('ketua.pengajuan-keluar.show', $pengajuan) }}" class="card-detail-link inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-700 font-semibold rounded-xl hover:from-sky-200 hover:to-blue-200 transition text-xs">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 md:px-6 py-10 text-center text-slate-400">
                            <p class="text-sm font-medium">Belum ada pengajuan keluar</p>
                            <p class="text-xs mt-1">Pengajuan siswa akan muncul di halaman ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @include('partials.table-pagination', ['rows' => $pengajuanKeluars, 'label' => 'pengajuan'])
    </div>
</div>
@endif
@endsection
