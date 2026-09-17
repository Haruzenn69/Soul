@extends('ketua.layout')
@section('title', 'Edit Kegiatan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Edit Kegiatan</h1>
            <p class="text-xs text-slate-400 mt-1">Perbarui informasi kegiatan tanpa mengubah riwayat presensi.</p>
        </div>
        <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition text-center">Kembali ke detail</a>
    </div>

    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl">
        <form action="{{ route('ketua.kegiatan.update', $kegiatan) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama kegiatan <span class="text-rose-500">*</span></label>
                <input type="text" name="kegiatan" value="{{ old('kegiatan', $kegiatan->kegiatan) }}" required maxlength="255"
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                @error('kegiatan') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi <span class="font-normal text-slate-400">(opsional)</span></label>
                <textarea name="deskripsi" rows="4" placeholder="Deskripsi kegiatan..."
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
            </div>
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Dokumentasi <span class="font-normal text-slate-400">(opsional)</span></label>
                @if($kegiatan->dokumentasi)
                    <img src="{{ asset('storage/' . $kegiatan->dokumentasi) }}" alt="Dokumentasi" class="mb-3 max-w-xs rounded-2xl border border-sky-100 shadow-sm">
                @endif
                <input type="file" name="dokumentasi" accept="image/*"
                    class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 transition file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-sky-100 file:text-sky-700 hover:file:bg-sky-200">
                <p class="text-[10px] text-slate-400 mt-1.5">JPG, JPEG, atau PNG. Maksimal <strong>2 MB</strong>.</p>
                @error('dokumentasi') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
                <a href="{{ route('ketua.kegiatan.show', $kegiatan) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition text-center">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-sky-200 transition">Simpan perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection