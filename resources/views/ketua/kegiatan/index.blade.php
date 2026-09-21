@extends('ketua.layout')
@section('title', 'Daftar Kegiatan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Daftar Kegiatan</h1>
            <p class="text-xs text-slate-400 mt-1">Total: {{ $kegiatans->count() }} kegiatan</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <form method="GET" action="{{ route('ketua.kegiatan.index') }}" class="flex gap-2 min-w-0">
                <label for="cari-kegiatan" class="sr-only">Cari kegiatan</label>
                <input id="cari-kegiatan" type="search" name="cari" value="{{ request('cari') }}"
                    placeholder="Cari kegiatan..." class="w-full sm:w-56 px-3 py-2 border border-slate-200 rounded-xl text-sm">
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 transition-all">Cari</button>
            </form>
            <a href="{{ route('ketua.kegiatan.create') }}" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 transition inline-flex items-center justify-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Kegiatan baru
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="ketua-card-list bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="card-table ketua-card-kegiatan w-full text-left text-xs md:text-sm">
                <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                    <tr>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap rounded-l-xl">No</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Tanggal</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Hari</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Kegiatan</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Presensi</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @forelse($kegiatans as $kegiatan)
                        <tr class="hover:bg-sky-50/50 transition" data-card-href="{{ route('ketua.kegiatan.show', $kegiatan) }}">
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-500">{{ $loop->iteration }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap font-medium text-slate-700">{{ $kegiatan->tanggal_kegiatan->format('d/m/Y') }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-600">
                                {{ $kegiatan->tanggal_kegiatan->isoFormat('dddd') }}
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap font-medium text-slate-800">{{ $kegiatan->materi }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-sky-100 text-sky-700 border border-sky-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    {{ $kegiatan->presensis_count }} orang
                                </span>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 md:gap-2">
                                    <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="card-detail-link inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-700 font-semibold rounded-full hover:from-sky-200 hover:to-blue-200 transition text-[10px] md:text-[11px]">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Detail
                                    </a>
                                    <a href="{{ route('ketua.presensi.create', $kegiatan) }}" class="inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-700 font-semibold rounded-full hover:from-emerald-200 hover:to-teal-200 transition text-[10px] md:text-[11px]">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Absensi
                                    </a>
                                    <a href="{{ route('ketua.kegiatan.edit', $kegiatan) }}" class="inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 font-semibold rounded-full hover:from-amber-200 hover:to-yellow-200 transition text-[10px] md:text-[11px]">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-9.5a2.121 2.121 0 013 3L12 16l-4 1 1-4 8.5-8.5z"/>
                                        </svg>
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 md:px-6 py-8 md:py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 md:w-12 md:h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <!-- Footer Tabel dengan Total -->
        <div class="ketua-card-footer px-3 md:px-6 py-3 border-t border-sky-50 flex justify-between items-center">
            <span class="text-[10px] md:text-xs text-slate-400">Menampilkan {{ $kegiatans->count() }} kegiatan</span>
            @if(method_exists($kegiatans, 'hasPages') && $kegiatans->hasPages())
                <div class="flex gap-1">
                    {{ $kegiatans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
