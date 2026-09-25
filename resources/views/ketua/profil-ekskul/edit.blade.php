@extends('ketua.layout')
@section('title', 'Profil Ekskul')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Profil Ekskul</h1>
        <p class="text-xs text-slate-400 mt-1">Atur informasi yang tampil di katalog ekskul.</p>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-bold text-slate-800">Status pendaftaran</h3>
            <p class="text-[11px] text-slate-400 mt-0.5">{{ $ekskul->is_open_recruitment ? 'Pendaftaran sedang dibuka. Siswa bisa mendaftar.' : 'Pendaftaran sedang ditutup.' }}</p>
        </div>
        <form action="{{ route('ketua.profil-ekskul.toggle-recruitment') }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="w-full sm:w-auto px-5 py-2.5 text-xs font-bold rounded-xl transition shadow-sm {{ $ekskul->is_open_recruitment ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 border border-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200 border border-slate-200' }}">
                {{ $ekskul->is_open_recruitment ? 'Dibuka' : 'Ditutup' }}
                @if($ekskul->is_open_recruitment)
                    <svg class="w-3.5 h-3.5 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                @endif
            </button>
        </form>
    </div>

    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl">
        <form action="{{ route('ketua.profil-ekskul.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Ekskul <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_ekskul" value="{{ old('nama_ekskul', $ekskul->nama_ekskul) }}" required
                    class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                @error('nama_ekskul') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tagline <span class="font-normal text-slate-400">(opsional)</span></label>
                <input type="text" name="tagline" value="{{ old('tagline', $ekskul->tagline) }}" placeholder="Contoh: Grow Together, Play Better."
                    class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                @error('tagline') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Deskripsi singkat</label>
                <textarea name="deskripsi" rows="3" placeholder="1-2 kalimat tentang ekskul ini"
                    class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('deskripsi', $ekskul->deskripsi) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tujuan</label>
                <textarea name="tujuan" rows="4" placeholder="Tujuan / fokus kegiatan ekskul"
                    class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('tujuan', $ekskul->tujuan) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Jadwal</label>
                <input type="text" name="jadwal" value="{{ old('jadwal', $ekskul->jadwal) }}" placeholder="Contoh: Senin & Kamis, 15.30–17.00"
                    class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Logo <span class="font-normal text-slate-400">(opsional)</span></label>
                    @if($ekskul->logo)
                        <img src="{{ asset('storage/' . $ekskul->logo) }}" alt="Logo" class="w-20 h-20 object-contain rounded-2xl border border-sky-100 bg-sky-50 mb-2">
                    @endif
                    <input type="file" name="logo" accept="image/*"
                        class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 transition file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-sky-100 file:text-sky-700 hover:file:bg-sky-200">
                    @error('logo') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Cover / foto hero <span class="font-normal text-slate-400">(opsional)</span></label>
                    @if($ekskul->cover)
                        <img src="{{ asset('storage/' . $ekskul->cover) }}" alt="Cover" class="w-32 h-20 object-cover rounded-2xl border border-sky-100 mb-2">
                    @endif
                    <input type="file" name="cover" accept="image/*"
                        class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 transition file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-sky-100 file:text-sky-700 hover:file:bg-sky-200">
                    @error('cover') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-sky-200 transition">Simpan perubahan</button>
            </div>
        </form>
    </div>

    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl">
        <div class="mb-4 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Galeri Momen</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Upload banyak foto yang tampil di katalog ekskul.</p>
            </div>
            <span class="text-[11px] font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ $ekskul->galeris->count() }} Foto</span>
        </div>

        @if($ekskul->galeris->isNotEmpty())
            <div class="grid grid-cols-3 md:grid-cols-4 gap-3 mb-5">
                @foreach($ekskul->galeris as $galeri)
                    <div class="group relative rounded-2xl overflow-hidden border border-sky-100">
                        <img src="{{ asset('storage/' . $galeri->foto) }}" alt="Dokumentasi" class="w-full h-24 object-cover">
                        <form action="{{ route('ketua.profil-ekskul.galeri-destroy', $galeri) }}" method="POST" onsubmit="return confirm('Hapus foto ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="absolute inset-0 flex items-center justify-center bg-black/60 text-white opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 bg-sky-50/50 rounded-2xl border border-dashed border-sky-200 mb-5">
                <svg class="w-10 h-10 text-sky-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <p class="text-xs font-semibold text-slate-500 mt-2">Belum ada foto galeri</p>
                <p class="text-[11px] text-slate-400 mt-1">Unggah foto kegiatan ekskulmu di sini.</p>
            </div>
        @endif

        <form action="{{ route('ketua.profil-ekskul.galeri-store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <label class="block text-xs font-bold text-slate-700 mb-1">Tambah Foto</label>
            <input type="file" name="foto[]" multiple accept="image/*" required
                class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 transition file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-sky-100 file:text-sky-700 hover:file:bg-sky-200">
            <input type="text" name="caption" placeholder="Keterangan foto (opsional)"
                class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 transition">
            @error('foto') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl transition">Upload Foto</button>
        </form>
    </div>
</div>
@endsection