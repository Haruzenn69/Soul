@extends('ketua.layout')
@section('title', 'Kelola Prestasi')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Kelola Prestasi</h1>
            <p class="text-xs text-slate-400 mt-1">Tambah dan kelola prestasi ekskul</p>
        </div>
    </div>

    <!-- Add Form Card -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl space-y-5 animate-fade-up" style="animation-delay: .1s">
        <h3 class="text-sm font-extrabold text-slate-900 mb-4">Tambah Prestasi</h3>
        <form action="{{ route('ketua.prestasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Judul</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Juara 1"
                        class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                    @error('judul') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tahun</label>
                    <input type="text" name="tahun" value="{{ old('tahun') }}" placeholder="2025"
                        class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kategori / Perlombaan</label>
                <input type="text" name="kategori" value="{{ old('kategori') }}" placeholder="Contoh: DBL School Competition"
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Foto (Opsional)</label>
                <input type="file" name="foto" accept="image/*"
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gradient-to-r file:from-sky-400 file:to-blue-500 file:text-white hover:file:from-sky-500 hover:file:to-blue-600">
                @error('foto') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 transition w-full sm:w-auto">Simpan</button>
        </form>
    </div>

    <!-- Table Card -->
    <div class="ketua-card-list bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="card-table w-full text-left text-xs md:text-sm">
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Judul</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Kategori</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Tahun</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Foto</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                @forelse($prestasis as $prestasi)
                    <tr class="hover:bg-sky-50/50 transition">
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-4 md:px-6 py-3.5 font-medium whitespace-nowrap">{{ $prestasi->judul }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $prestasi->kategori ?? '-' }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $prestasi->tahun ?? '-' }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            @if($prestasi->foto)
                                <img src="{{ asset('storage/' . $prestasi->foto) }}" alt="Foto" class="w-14 h-10 object-cover rounded-lg border border-sky-100">
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <form action="{{ route('ketua.prestasi.destroy', $prestasi) }}" method="POST" class="inline" onsubmit="return confirm('Hapus prestasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-200 rounded-xl text-[10px] font-bold transition">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 md:px-6 py-10 text-center text-slate-400">Belum ada prestasi. Tambahkan pencapaian ekskul melalui form di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
@endsection