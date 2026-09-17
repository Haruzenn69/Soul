@extends('ketua.layout')
@section('title', 'Daftar Pendaftaran')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Daftar Pendaftaran</h1>
            <p class="text-xs text-slate-400 mt-1">Total: {{ $pendaftarans->count() }} pendaftar</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Pending: {{ $pendaftarans->where('status', 'pending')->count() }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Diterima: {{ $pendaftarans->where('status', 'diterima')->count() }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-700 border border-rose-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    Ditolak: {{ $pendaftarans->where('status', 'ditolak')->count() }}
                </span>
            </div>
        </div>
    </div>

    <!-- Table Card dengan overflow-x-auto -->
    <div class="ketua-card-list bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="card-table ketua-card-pendaftaran w-full text-left text-xs md:text-sm">
                <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                    <tr>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap rounded-l-xl">No</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">NIS</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Nama</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Kelas</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Tanggal Daftar</th>
                        <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Status</th>
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
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        Pending
                                    </span>
                                @elseif($pendaftaran->status === 'diterima')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Diterima
                                    </span>
                                @elseif($pendaftaran->status === 'ditolak')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">{{ ucfirst($pendaftaran->status) }}</span>
                                @endif
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap">
                                <a href="{{ route('ketua.pendaftaran.show', $pendaftaran) }}" class="card-detail-link inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-700 font-semibold rounded-xl hover:from-sky-200 hover:to-blue-200 transition text-[10px] md:text-[11px]">
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

        <!-- Footer Tabel dengan Total -->
        <div class="ketua-card-footer px-3 md:px-6 py-3 border-t border-sky-50 flex justify-between items-center">
            <span class="text-[10px] md:text-xs text-slate-400">Menampilkan {{ $pendaftarans->count() }} data</span>
            @if(method_exists($pendaftarans, 'hasPages') && $pendaftarans->hasPages())
                <div class="flex gap-1">
                    {{ $pendaftarans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection