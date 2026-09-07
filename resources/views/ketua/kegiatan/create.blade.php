@extends('ketua.layout')
@section('title', 'Buat Kegiatan Baru')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-lg">
        <form action="{{ route('ketua.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Kegiatan</label>
                <input type="text" name="kegiatan" value="{{ old('kegiatan') }}" required
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                @error('kegiatan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4" placeholder="Deskripsi kegiatan..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Dokumentasi (Opsional)</label>
                <input type="file" name="dokumentasi" accept="image/*"
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-theme-blue file:text-white hover:file:bg-theme-darkBlue">
                <p class="text-[10px] text-gray-400 mt-1">Format: JPG, JPEG, PNG. Ukuran maksimal <strong>2 MB</strong>.</p>
                @error('dokumentasi') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2 mt-6">
                <button type="submit" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Simpan</button>
                <a href="{{ route('ketua.kegiatan.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Batal</a>
            </div>
        </form>
    </div>
@endsection
