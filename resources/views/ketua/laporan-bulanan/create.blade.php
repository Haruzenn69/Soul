@extends('ketua.layout')
@section('title', 'Buat Laporan Bulanan')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-lg">
        <form action="{{ route('ketua.laporan-bulanan.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Bulan (YYYY-MM)</label>
                <input type="month" name="bulan" value="{{ old('bulan') }}" required
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                @error('bulan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Materi Kegiatan</label>
                <input type="text" name="materi_kegiatan" value="{{ old('materi_kegiatan') }}"
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Ringkasan</label>
                <textarea name="ringkasan" rows="4"
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">{{ old('ringkasan') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Dokumentasi (URL/Link)</label>
                <input type="text" name="dokumentasi" value="{{ old('dokumentasi') }}"
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
            </div>
            <div class="flex gap-2 mt-6">
                <button type="submit" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Simpan</button>
                <a href="{{ route('ketua.laporan-bulanan.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Batal</a>
            </div>
        </form>
    </div>
@endsection
