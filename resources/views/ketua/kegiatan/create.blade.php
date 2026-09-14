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
                @error('materi') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" rows="4" placeholder="Deskripsi kegiatan..."
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('deskripsi') }}</textarea>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Dokumentasi (Opsional)</label>
                <input type="file" name="dokumentasi" accept="image/*"
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gradient-to-r file:from-sky-400 file:to-blue-500 file:text-white hover:file:from-sky-500 hover:file:to-blue-600">
                <p class="text-[10px] text-slate-400 mt-1">Format: JPG, JPEG, PNG. Maks 2MB.</p>
                @error('dokumentasi') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Kegiatan</label>
                <input type="date" name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan') }}" required
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                @error('tanggal_kegiatan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2 pt-2 flex-wrap">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 transition">Simpan</button>
                <a href="{{ route('ketua.kegiatan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Batal</a>
            </div>
        </form>
    </div>
@endsection