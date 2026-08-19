@extends('layouts.sidebar-ketua')
@section('title', 'Detail Pendaftaran')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Detail Pendaftaran</h1>

    <div class="bg-white p-6 rounded shadow max-w-lg">
        <div class="mb-3">
            <p class="text-sm text-gray-500">Tanggal Daftar</p>
            <p class="font-medium">{{ $pendaftaran->tanggal_daftar->format('d/m/Y') }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">NIS</p>
            <p class="font-medium">{{ $pendaftaran->siswa->nis }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Nama</p>
            <p class="font-medium">{{ $pendaftaran->siswa->nama }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Kelas</p>
            <p class="font-medium">{{ $pendaftaran->siswa->kelas->nama }}</p>
        </div>
        <div class="mb-3">
            <p class="text-sm text-gray-500">Status</p>
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
                    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Terima</button>
                </form>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('ketua.pendaftaran.index') }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Kembali</a>
        </div>
    </div>
@endsection
