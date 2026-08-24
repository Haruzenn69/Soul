@extends('ketua.layout')
@section('title', 'Detail Laporan Bulanan')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-lg">
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Bulan</p>
            <p class="font-medium text-sm">{{ $laporan->bulan }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Status</p>
            @if($laporan->status === 'draft')
                <span class="text-gray-500 font-medium">Draft</span>
            @elseif($laporan->status === 'disetujui')
                <span class="text-green-600 font-medium">Disetujui</span>
            @else
                <span class="text-red-600 font-medium">Ditolak</span>
            @endif
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Materi Kegiatan</p>
            <p class="font-medium text-sm">{{ $laporan->materi_kegiatan ?? '-' }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Ringkasan</p>
            <p class="font-medium text-sm">{{ $laporan->ringkasan ?? '-' }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Dokumentasi</p>
            <p class="font-medium text-sm">{{ $laporan->dokumentasi ?? '-' }}</p>
        </div>

        <div class="mt-4">
            <a href="{{ route('ketua.laporan-bulanan.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Kembali</a>
        </div>
    </div>
@endsection
