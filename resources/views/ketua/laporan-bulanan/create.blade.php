@extends('layouts.sidebar-ketua')
@section('title', 'Buat Laporan Bulanan')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Buat Laporan Bulanan</h1>

    <form action="{{ route('ketua.laporan-bulanan.store') }}" method="POST" class="bg-white p-6 rounded shadow max-w-lg">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Bulan (YYYY-MM)</label>
            <input type="month" name="bulan" value="{{ old('bulan') }}" class="w-full border rounded px-3 py-2" required>
            @error('bulan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Materi Kegiatan</label>
            <input type="text" name="materi_kegiatan" value="{{ old('materi_kegiatan') }}" class="w-full border rounded px-3 py-2">
            @error('materi_kegiatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Ringkasan</label>
            <textarea name="ringkasan" rows="4" class="w-full border rounded px-3 py-2">{{ old('ringkasan') }}</textarea>
            @error('ringkasan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Dokumentasi (URL foto)</label>
            <input type="text" name="dokumentasi" value="{{ old('dokumentasi') }}" class="w-full border rounded px-3 py-2">
            @error('dokumentasi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
            <a href="{{ route('ketua.laporan-bulanan.index') }}" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</a>
        </div>
    </form>
@endsection
