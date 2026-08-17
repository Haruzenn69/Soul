@extends('layouts.sidebar-ketua')
@section('title', 'Detail Laporan Bulanan')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Detail Laporan Bulanan</h1>

    <div class="bg-white p-6 rounded shadow max-w-lg">
        <div class="mb-3">
            <p class="text-sm text-gray-500">Bulan</p>
            <p class="font-medium">{{ $laporanBulanan->bulan }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Ekskul</p>
            <p class="font-medium">{{ $laporanBulanan->ekskul->nama_ekskul }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Materi Kegiatan</p>
            <p class="font-medium">{{ $laporanBulanan->materi_kegiatan ?? '-' }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Ringkasan</p>
            <p class="font-medium">{{ $laporanBulanan->ringkasan ?? '-' }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Dokumentasi</p>
            <p class="font-medium">{{ $laporanBulanan->dokumentasi ?? '-' }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Status</p>
            @if($laporanBulanan->status === 'draft')
                <span class="text-gray-600 font-medium">Draft</span>
            @elseif($laporanBulanan->status === 'disetujui')
                <span class="text-green-600 font-medium">Disetujui</span>
            @else
                <span class="text-red-600 font-medium">Ditolak</span>
            @endif
        </div>
        @if($laporanBulanan->catatan_pembina)
            <div class="mb-3">
                <p class="text-sm text-gray-500">Catatan Pembina</p>
                <p class="font-medium">{{ $laporanBulanan->catatan_pembina }}</p>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('ketua.laporan-bulanan.index') }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Kembali</a>
        </div>
    </div>
@endsection
