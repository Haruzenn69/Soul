@extends('ketua.layout')
@section('title', 'Buat Kegiatan Baru')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-lg">
        <form action="{{ route('ketua.kegiatan.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Materi</label>
                <input type="text" name="materi" value="{{ old('materi') }}" required
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                @error('materi') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Tanggal Kegiatan</label>
                <input type="date" name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan') }}" required
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                @error('tanggal_kegiatan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2 mt-6">
                <button type="submit" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Simpan</button>
                <a href="{{ route('ketua.kegiatan.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Batal</a>
            </div>
        </form>
    </div>
@endsection
