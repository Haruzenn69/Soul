@extends('ketua.layout')
@section('title', 'Kelola Prestasi')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-2xl mb-6">
        <h3 class="text-sm font-bold text-theme-dark mb-4">Tambah Prestasi</h3>
        <form action="{{ route('ketua.prestasi.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 mb-1">Judul</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Juara 1"
                        class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                    @error('judul') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Tahun</label>
                    <input type="text" name="tahun" value="{{ old('tahun') }}" placeholder="2025"
                        class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Kategori / Perlombaan</label>
                <input type="text" name="kategori" value="{{ old('kategori') }}" placeholder="Contoh: DBL School Competition"
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Foto (Opsional)</label>
                <input type="file" name="foto" accept="image/*"
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-theme-blue file:text-white hover:file:bg-theme-darkBlue">
                @error('foto') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Simpan</button>
        </form>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Judul</th>
                    <th class="px-6 py-3">Kategori</th>
                    <th class="px-6 py-3">Tahun</th>
                    <th class="px-6 py-3">Foto</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($prestasis as $prestasi)
                    <tr>
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium">{{ $prestasi->judul }}</td>
                        <td class="px-6 py-4">{{ $prestasi->kategori ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $prestasi->tahun ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($prestasi->foto)
                                <img src="{{ asset('storage/' . $prestasi->foto) }}" alt="Foto" class="w-14 h-10 object-cover rounded-lg border border-gray-100">
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('ketua.prestasi.destroy', $prestasi) }}" method="POST" class="inline" onsubmit="return confirm('Hapus prestasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada prestasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection