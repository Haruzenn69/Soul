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
</div>
@endsection