@extends('layouts.sidebar-ketua')
@section('title', 'Detail Pengajuan Keluar')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Detail Pengajuan Keluar</h1>

    <div class="bg-white p-6 rounded shadow max-w-lg">
        <div class="mb-3">
            <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
            <p class="font-medium">{{ $pengajuanKeluar->tanggal_pengajuan->format('d/m/Y') }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">NIS</p>
            <p class="font-medium">{{ $pengajuanKeluar->siswa->nis }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Nama</p>
            <p class="font-medium">{{ $pengajuanKeluar->siswa->nama }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Alasan</p>
            <p class="font-medium">{{ $pengajuanKeluar->alasan }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Status</p>
            @if($pengajuanKeluar->status === 'pending')
                <span class="text-yellow-600 font-medium">Pending</span>
            @elseif($pengajuanKeluar->status === 'diterima')
                <span class="text-green-600 font-medium">Diterima</span>
            @else
                <span class="text-red-600 font-medium">Ditolak</span>
            @endif
        </div>

        @if($pengajuanKeluar->status === 'pending')
            <div class="mt-4 flex gap-2">
                <form action="{{ route('ketua.pengajuan-keluar.update', $pengajuanKeluar) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="diterima">
                    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Setuju</button>
                </form>
                <form action="{{ route('ketua.pengajuan-keluar.update', $pengajuanKeluar) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="ditolak">
                    <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Tolak</button>
                </form>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('ketua.pengajuan-keluar.index') }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Kembali</a>
        </div>
    </div>
@endsection
