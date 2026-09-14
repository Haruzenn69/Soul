@extends('pembina.layout')
@section('title', 'Detail Laporan')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 animate-fade-up">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Detail Laporan Bulanan</h1>
            <p class="text-xs text-slate-400 mt-0.5">{{ $laporan->ekskul->nama_ekskul ?? 'Ekskul' }} · {{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->translatedFormat('F Y') }}</p>
        </div>
        <a href="{{ route('pembina.laporan.index') }}" class="px-4 py-2 bg-white hover:bg-sky-50 text-slate-600 text-xs font-semibold rounded-xl border border-sky-100 shadow-sm transition w-full sm:w-auto text-center">
            Kembali
        </a>
    </div>

    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-3xl space-y-4 animate-fade-up" style="animation-delay: .1s">
        <div>
            <p class="text-[11px] text-slate-400 font-bold uppercase">Status</p>
            <span class="inline-block px-3 py-1 mt-1 text-xs font-semibold rounded-full border
                @if($laporan->status == 'draft') bg-slate-100 text-slate-600 border-slate-200
                @elseif($laporan->status == 'disetujui') bg-emerald-100 text-emerald-700 border-emerald-200
                @else bg-red-100 text-red-700 border-red-200 @endif">
                {{ ucfirst($laporan->status) }}
            </span>
        </div>

        @if($laporan->materi_kegiatan)
        <div class="p-4 rounded-xl bg-gradient-to-r from-sky-50 to-white border border-sky-100">
            <p class="text-[11px] text-slate-400 font-bold uppercase">Materi dan Kegiatan</p>
            <p class="text-sm whitespace-pre-line mt-1">{{ $laporan->materi_kegiatan }}</p>
        </div>
        @endif

        @if($laporan->tujuan)
        <div class="p-4 rounded-xl bg-gradient-to-r from-sky-50 to-white border border-sky-100">
            <p class="text-[11px] text-slate-400 font-bold uppercase">Tujuan Kegiatan</p>
            <p class="text-sm whitespace-pre-line mt-1">{{ $laporan->tujuan }}</p>
        </div>
        @endif

        @if($laporan->kehadiran)
        <div class="p-4 rounded-xl bg-gradient-to-r from-amber-50 to-white border border-amber-100">
            <p class="text-[11px] text-slate-400 font-bold uppercase">Kehadiran Peserta</p>
            <p class="text-sm whitespace-pre-line mt-1">{{ $laporan->kehadiran }}</p>
        </div>
        @endif

        @if($laporan->evaluasi_keberhasilan || $laporan->evaluasi_kendala || $laporan->evaluasi_solusi)
        <div class="p-4 rounded-xl bg-gradient-to-r from-sky-50 to-white border border-sky-100">
            <p class="text-[11px] text-slate-400 font-bold uppercase mb-2">Evaluasi Kegiatan</p>
            @if($laporan->evaluasi_keberhasilan)
                <p class="text-sm mt-1"><span class="font-bold">Keberhasilan:</span> {{ $laporan->evaluasi_keberhasilan }}</p>
            @endif
            @if($laporan->evaluasi_kendala)
                <p class="text-sm mt-1"><span class="font-bold">Kendala:</span> {{ $laporan->evaluasi_kendala }}</p>
            @endif
            @if($laporan->evaluasi_solusi)
                <p class="text-sm mt-1"><span class="font-bold">Solusi:</span> {{ $laporan->evaluasi_solusi }}</p>
            @endif
        </div>
        @endif

        @if($laporan->dokumentasi_kegiatan)
        <div>
            <p class="text-[11px] text-slate-400 font-bold uppercase mb-2">Dokumentasi</p>
            <div class="grid grid-cols-2 gap-2">
                @foreach($laporan->dokumentasi_kegiatan as $dokPath)
                    <img src="{{ asset('storage/' . $dokPath) }}" alt="Dokumentasi" class="w-full rounded-xl border border-sky-100 shadow-sm">
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-6 pt-4 border-t border-sky-100">
            <a href="{{ route('pembina.laporan.download', $laporan) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-400 to-red-500 hover:from-red-500 hover:to-red-600 text-white text-xs font-semibold rounded-lg transition shadow-lg shadow-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download PDF
            </a>
        </div>
    </div>
@endsection