@extends('ketua.layout')
@section('title', 'Detail Pendaftaran')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-lg">
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Tanggal Daftar</p>
            <p class="font-medium text-sm">{{ $pendaftaran->tanggal_daftar->format('d/m/Y') }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">NIS</p>
            <p class="font-medium text-sm">{{ $pendaftaran->siswa->nis }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Nama</p>
            <p class="font-medium text-sm">{{ $pendaftaran->siswa->nama }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Status</p>
            @if($pendaftaran->status === 'pending')
                <span class="text-yellow-600 font-medium">Pending</span>
            @else
                <span class="text-green-600 font-medium">Diterima</span>
            @endif
        </div>

        @if($pendaftaran->status === 'pending')
            <div class="mt-4 flex gap-2">
                <form action="{{ route('ketua.pendaftaran.update', $pendaftaran) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="diterima">
                    <button class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-full transition">Terima</button>
                </form>
                <form action="{{ route('ketua.pendaftaran.update', $pendaftaran) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="ditolak">
                    <button class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-full transition">Tolak</button>
                </form>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('ketua.pendaftaran.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Kembali</a>
        </div>
    </div>
@endsection
