@extends('ketua.layout')
@section('title', 'Profil Ekskul')

@section('content')
    {{-- Toggle Recruitment --}}
    <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm max-w-2xl flex items-center justify-between">
        <div>
            <h3 class="text-xs font-bold text-theme-dark">Status Pendaftaran</h3>
            <p class="text-[11px] text-gray-400 mt-0.5">{{ $ekskul->is_open_recruitment ? 'Pendaftaran sedang dibuka. Siswa bisa mendaftar.' : 'Pendaftaran sedang ditutup.' }}</p>
        </div>
        <form action="{{ route('ketua.profil-ekskul.toggle-recruitment') }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-5 py-2.5 text-xs font-bold rounded-full transition shadow-sm {{ $ekskul->is_open_recruitment ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-200 text-gray-500 hover:bg-gray-300' }}">
                {{ $ekskul->is_open_recruitment ? 'Dibuka ✓' : 'Ditutup' }}
            </button>
        </form>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-2xl">
        <form action="{{ route('ketua.profil-ekskul.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Nama Ekskul</label>
                <input type="text" name="nama_ekskul" value="{{ old('nama_ekskul', $ekskul->nama_ekskul) }}" required
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                @error('nama_ekskul') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Tagline</label>
                <input type="text" name="tagline" value="{{ old('tagline', $ekskul->tagline) }}" placeholder="Contoh: Grow Together, Play Better."
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                @error('tagline') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Deskripsi Singkat</label>
                <textarea name="deskripsi" rows="3" placeholder="1-2 kalimat tentang ekskul ini"
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">{{ old('deskripsi', $ekskul->deskripsi) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Tujuan</label>
                <textarea name="tujuan" rows="4" placeholder="Tujuan / fokus kegiatan ekskul"
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">{{ old('tujuan', $ekskul->tujuan) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Jadwal</label>
                <input type="text" name="jadwal" value="{{ old('jadwal', $ekskul->jadwal) }}" placeholder="Contoh: Senin & Kamis, 15.30–17.00"
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Logo</label>
                    @if($ekskul->logo)
                        <img src="{{ asset('storage/' . $ekskul->logo) }}" alt="Logo" class="w-20 h-20 object-contain rounded-2xl border border-gray-100 mb-2">
                    @endif
                    <input type="file" name="logo" accept="image/*"
                        class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-theme-blue file:text-white hover:file:bg-theme-darkBlue">
                    @error('logo') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Cover / Foto Hero</label>
                    @if($ekskul->cover)
                        <img src="{{ asset('storage/' . $ekskul->cover) }}" alt="Cover" class="w-32 h-20 object-cover rounded-2xl border border-gray-100 mb-2">
                    @endif
                    <input type="file" name="cover" accept="image/*"
                        class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-theme-blue file:text-white hover:file:bg-theme-darkBlue">
                    @error('cover') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Simpan</button>
            </div>
        </form>
    </div>
@endsection