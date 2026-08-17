@extends('layouts.sidebar-ketua')
@section('title', 'Tambah Kegiatan')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Tambah Kegiatan</h1>

    <form action="{{ route('ketua.kegiatan.store') }}" method="POST" class="bg-white p-6 rounded shadow max-w-lg">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Materi</label>
            <input type="text" name="materi" value="{{ old('materi') }}" class="w-full border rounded px-3 py-2" required>
            @error('materi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Tanggal Kegiatan</label>
            <input type="date" name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan') }}" class="w-full border rounded px-3 py-2" required>
            @error('tanggal_kegiatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
            <a href="{{ route('ketua.kegiatan.index') }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
        </div>
    </form>
@endsection
