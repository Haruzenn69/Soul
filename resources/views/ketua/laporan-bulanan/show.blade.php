@extends('ketua.layout')
@section('title', 'Detail Laporan Bulanan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Detail Laporan Bulanan</h1>
            <p class="text-xs text-slate-400 mt-1">{{ $laporan->bulan }}</p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('ketua.laporan-bulanan.download-pdf', $laporan) }}" class="px-5 py-2.5 bg-gradient-to-r from-red-400 to-red-500 hover:from-red-500 hover:to-red-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-red-200 transition inline-flex w-full sm:w-auto items-center justify-center gap-1 shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                PDF
            </a>
            <a href="{{ route('ketua.laporan-bulanan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition w-full sm:w-auto items-center justify-center gap-2">Kembali</a>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl space-y-5">
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bulan</p>
            <p class="font-medium text-sm">{{ $laporan->bulan }}</p>
        </div>
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status</p>
            @if($laporan->status === 'draft')
                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full">Draft</span>
            @elseif($laporan->status === 'menunggu')
                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-600 text-xs font-semibold rounded-full">Menunggu Pembina</span>
            @elseif($laporan->status === 'disetujui')
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">Disetujui</span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-100 text-red-700 border border-red-200">Ditolak</span>
            @endif
        </div>

        @if($laporan->materi_kegiatan)
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Materi dan Kegiatan</p>
            <p class="font-medium text-sm whitespace-pre-line">{{ $laporan->materi_kegiatan }}</p>
        </div>
        @endif

        @if($laporan->tujuan)
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tujuan Kegiatan</p>
            <p class="font-medium text-sm whitespace-pre-line">{{ $laporan->tujuan }}</p>
        </div>
        @endif

        @if($laporan->kehadiran)
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kehadiran Peserta</p>
            <p class="font-medium text-sm whitespace-pre-line">{{ $laporan->kehadiran }}</p>
        </div>
        @endif

        @if($laporan->evaluasi_keberhasilan || $laporan->evaluasi_kendala || $laporan->evaluasi_solusi)
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Evaluasi Kegiatan</p>
            @if($laporan->evaluasi_keberhasilan)
                <p class="font-medium text-sm mt-2"><span class="font-bold">Keberhasilan:</span> {{ $laporan->evaluasi_keberhasilan }}</p>
            @endif
            @if($laporan->evaluasi_kendala)
                <p class="font-medium text-sm mt-2"><span class="font-bold">Kendala:</span> {{ $laporan->evaluasi_kendala }}</p>
            @endif
            @if($laporan->evaluasi_solusi)
                <p class="font-medium text-sm mt-2"><span class="font-bold">Solusi/Tindak Lanjut:</span> {{ $laporan->evaluasi_solusi }}</p>
            @endif
        </div>
        @endif

        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Dokumentasi</p>
            @php
                $dokPaths = [];
                if ($laporan->dokumentasi) {
                    $dokPaths[] = $laporan->dokumentasi;
                }
                foreach ($laporan->dokumentasi_kegiatan ?? [] as $dk) {
                    $dokPaths[] = $dk;
                }
            @endphp
            @if(count($dokPaths) > 0)
                <div class="grid grid-cols-2 gap-2 mt-2">
                    @foreach($dokPaths as $dokPath)
                        <img src="{{ asset('storage/' . $dokPath) }}" alt="Dokumentasi" class="max-w-full rounded-2xl border border-sky-100 shadow-sm">
                    @endforeach
                </div>
            @else
                <p class="font-medium text-sm text-slate-400">Tidak ada dokumentasi</p>
            @endif
        </div>

        @if($laporan->catatan_pembina)
        <div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Catatan Pembina</p>
            <p class="font-medium text-sm whitespace-pre-line">{{ $laporan->catatan_pembina }}</p>
        </div>
        @endif

        <div class="mt-4 flex gap-2">
            @if(in_array($laporan->status, ['draft', 'ditolak']))
                <a href="{{ route('ketua.laporan-bulanan.edit', $laporan) }}" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-full transition">
                    ✏️ Edit Laporan
                </a>
                <form action="{{ route('ketua.laporan-bulanan.submit', $laporan) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">
                        🚀 Serahkan ke Pembina
                    </button>
                </form>
            @endif
            <a href="{{ route('ketua.laporan-bulanan.download-pdf', $laporan) }}" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-full transition flex items-center gap-1">
                📄 Download PDF
            </a>
            <a href="{{ route('ketua.laporan-bulanan.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Kembali</a>
        </div>
    </div>
@endsection