@extends('ketua.layout')
@section('title', 'Buat Kegiatan Baru')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Buat Kegiatan Baru</h1>
            <p class="text-xs text-slate-400 mt-1">Tambah kegiatan dan jadwal presensi untuk ekskul</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-lg space-y-5">
        <form action="{{ route('ketua.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Materi</label>
                <input type="text" name="materi" value="{{ old('materi') }}" required
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                @error('kegiatan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" rows="4" placeholder="Deskripsi kegiatan..."
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('deskripsi') }}</textarea>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Dokumentasi (Opsional)</label>
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