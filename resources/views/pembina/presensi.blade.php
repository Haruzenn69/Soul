@extends('pembina.layout')
@section('title', 'Presensi Kegiatan')

@section('content')
@php
    $sort = $sort ?? 'tanggal_kegiatan';
    $direction = $direction ?? 'desc';
    $currentSortCombo = $sort . '_' . $direction;
    $hasActiveFilter = request()->filled('cari') || (request()->filled('ekskul') && request('ekskul') !== 'semua') || request()->filled('sort');
@endphp

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 animate-fade-up">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Presensi Kegiatan</h1>
            <p class="text-xs text-slate-400 mt-0.5">Riwayat kehadiran anggota pada setiap kegiatan ekskul yang anda bina</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pembina.rekap') }}" class="px-4 py-2 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-md shadow-sky-200 transition">
                Lihat Rekap Absensi
            </a>
        </div>
    </div>

    <!-- FILTER & PENCARIAN -->
    <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .1s">
        <form method="GET" action="{{ route('pembina.presensi') }}" id="filter-presensi-pembina-form" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center">
            <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">
            <input type="hidden" name="direction" id="direction-input" value="{{ $direction }}">

            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="search" name="cari" value="{{ request('cari') }}" placeholder="Cari materi kegiatan atau nama anggota..."
                    class="w-full pl-10 pr-4 py-2 bg-sky-50/60 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
            </div>

            @if(isset($ekskuls) && $ekskuls->count() > 1)
                <select name="ekskul" onchange="this.form.submit()" class="px-3 py-2 bg-sky-50/60 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                    <option value="semua">Semua Ekskul Binaan</option>
                    @foreach($ekskuls as $ex)
                        <option value="{{ $ex->id }}" @selected(request('ekskul') == $ex->id)>{{ $ex->nama_ekskul }}</option>
                    @endforeach
                </select>
            @endif

            <select id="sort-select" class="px-3 py-2 bg-sky-50/60 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                <option value="tanggal_kegiatan_desc" {{ $currentSortCombo === 'tanggal_kegiatan_desc' ? 'selected' : '' }}>Tanggal Terbaru</option>
                <option value="tanggal_kegiatan_asc" {{ $currentSortCombo === 'tanggal_kegiatan_asc' ? 'selected' : '' }}>Tanggal Terlama</option>
                <option value="materi_asc" {{ $currentSortCombo === 'materi_asc' ? 'selected' : '' }}>Materi (A-Z)</option>
                <option value="materi_desc" {{ $currentSortCombo === 'materi_desc' ? 'selected' : '' }}>Materi (Z-A)</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-semibold rounded-xl transition shadow-lg shadow-sky-200">Filter</button>

            @if($hasActiveFilter)
                <a href="{{ route('pembina.presensi') }}" class="px-3 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- LIST KEGIATAN & PRESENSI -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .2s">
        @if(count($kegiatans) > 0)
            <div class="space-y-6">
                @foreach($kegiatans as $kegiatan)
                    <div class="bg-gradient-to-br from-sky-50 via-white to-amber-50 rounded-2xl border border-sky-100 overflow-hidden shadow-sm">
                        <div class="p-4 bg-white/80 backdrop-blur border-b border-sky-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <h3 class="text-sm font-extrabold text-slate-900 truncate">{{ $kegiatan->materi }}</h3>
                                    @if($kegiatan->ekskul)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-700 border border-sky-200">
                                            {{ $kegiatan->ekskul->nama_ekskul }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('dddd, DD MMM Y') }}</p>
                            </div>
                            <span class="text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-100 px-2.5 py-1 rounded-lg shrink-0 self-start sm:self-auto">
                                {{ $kegiatan->presensis->count() }} anggota tercatat
                            </span>
                        </div>

                        @if($kegiatan->presensis->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="card-table w-full text-xs">
                                    <thead class="bg-sky-50">
                                        <tr>
                                            <th class="text-left p-3 font-semibold text-slate-500">Nama</th>
                                            <th class="text-left p-3 font-semibold text-slate-500">Kelas</th>
                                            <th class="text-left p-3 font-semibold text-slate-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-sky-50">
                                        @foreach($kegiatan->presensis as $presensi)
                                        <tr class="hover:bg-sky-50/30 transition bg-white">
                                            <td class="p-3 font-medium text-slate-800">{{ $presensi->pendaftaran->siswa->nama ?? '-' }}</td>
                                            <td class="p-3 text-slate-600">{{ $presensi->pendaftaran->siswa->kelas->nama ?? '-' }}</td>
                                            <td class="p-3">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border
                                                    @if($presensi->status == 'hadir') bg-emerald-100 text-emerald-700 border-emerald-200
                                                    @elseif($presensi->status == 'izin') bg-sky-100 text-sky-700 border-sky-200
                                                    @elseif($presensi->status == 'sakit') bg-amber-100 text-amber-700 border-amber-200
                                                    @else bg-red-100 text-red-700 border-red-200 @endif">
                                                    {{ ucfirst($presensi->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-6 text-center text-slate-400">
                                <p class="text-xs">Belum ada presensi dicatat untuk kegiatan ini.</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @include('partials.table-pagination', ['rows' => $kegiatans, 'label' => 'kegiatan'])
        @else
            <div class="text-center py-12 text-slate-400">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold text-slate-500">Tidak ada kegiatan yang ditemukan</p>
                <p class="text-[11px] text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter ekskul yang dipilih</p>
                @if($hasActiveFilter)
                    <div class="mt-4">
                        <a href="{{ route('pembina.presensi') }}" class="px-4 py-2 bg-sky-100 hover:bg-sky-200 text-sky-700 font-bold text-xs rounded-xl transition">Reset Filter</a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sortSelect = document.getElementById('sort-select');
            var sortInput = document.getElementById('sort-input');
            var directionInput = document.getElementById('direction-input');
            var form = document.getElementById('filter-presensi-pembina-form');

            if (sortSelect && sortInput && directionInput && form) {
                sortSelect.addEventListener('change', function () {
                    var val = this.value.split('_');
                    var dir = val.pop();
                    var col = val.join('_');
                    sortInput.value = col;
                    directionInput.value = dir || 'desc';
                    form.submit();
                });
            }
        });
    </script>
@endsection