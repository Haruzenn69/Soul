@extends('ketua.layout')
@section('title', 'Detail Pengajuan Keluar')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-lg">
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Tanggal Pengajuan</p>
            <p class="font-medium text-sm">{{ $pengajuanKeluar->tanggal_pengajuan->format('d/m/Y') }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Nama</p>
            <p class="font-medium text-sm">{{ $pengajuanKeluar->siswa->nama }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Alasan</p>
            <p class="font-medium text-sm">{{ $pengajuanKeluar->alasan }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Status</p>
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
                    <button class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-full transition">Setuju</button>
                </form>
                <form action="{{ route('ketua.pengajuan-keluar.update', $pengajuanKeluar) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="ditolak">
                    <button class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-full transition">Tolak</button>
                </form>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('ketua.pengajuan-keluar.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Kembali</a>
        </div>
    </div>
@endsection
