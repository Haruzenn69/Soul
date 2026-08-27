@extends('ketua.layout')
@section('title', 'Detail Laporan Bulanan')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-2xl">
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Bulan</p>
            <p class="font-medium text-sm">{{ $laporan->bulan }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Status</p>
            @if($laporan->status === 'draft')
                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full">Draft</span>
            @elseif($laporan->status === 'disetujui')
                <span class="inline-block px-3 py-1 bg-green-100 text-green-600 text-xs font-semibold rounded-full">Disetujui</span>
            @else
                <span class="inline-block px-3 py-1 bg-red-100 text-red-600 text-xs font-semibold rounded-full">Ditolak</span>
            @endif
        </div>

        @if($laporan->materi_kegiatan)
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Materi dan Kegiatan</p>
            <p class="font-medium text-sm whitespace-pre-line">{{ $laporan->materi_kegiatan }}</p>
        </div>
        @endif

        @if($laporan->tujuan)
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Tujuan Kegiatan</p>
            <p class="font-medium text-sm whitespace-pre-line">{{ $laporan->tujuan }}</p>
        </div>
        @endif

        @if($laporan->kehadiran)
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Kehadiran Peserta</p>
            <p class="font-medium text-sm whitespace-pre-line">{{ $laporan->kehadiran }}</p>
        </div>
        @endif

        @if($laporan->evaluasi_keberhasilan || $laporan->evaluasi_kendala || $laporan->evaluasi_solusi)
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Evaluasi Kegiatan</p>
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

        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Dokumentasi</p>
            @if($laporan->dokumentasi)
                <img src="{{ asset('storage/' . $laporan->dokumentasi) }}" alt="Dokumentasi" class="mt-2 max-w-sm rounded-2xl border border-gray-100 shadow-sm">
            @else
                <p class="font-medium text-sm text-gray-400">Tidak ada dokumentasi</p>
            @endif
        </div>

        @if($laporan->catatan_pembina)
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Catatan Pembina</p>
            <p class="font-medium text-sm whitespace-pre-line">{{ $laporan->catatan_pembina }}</p>
        </div>
        @endif

        <div class="mt-4 flex gap-2">
            <a href="{{ route('ketua.laporan-bulanan.download-pdf', $laporan) }}" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-full transition flex items-center gap-1">
                📄 Download PDF
            </a>
            <a href="{{ route('ketua.laporan-bulanan.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Kembali</a>
        </div>
    </div>
@endsection
