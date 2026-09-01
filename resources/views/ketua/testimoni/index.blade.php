@extends('ketua.layout')
@section('title', 'Kelola Testimoni')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-2xl mb-6">
        <h3 class="text-sm font-bold text-theme-dark mb-4">Tambah Testimoni</h3>
        <form action="{{ route('ketua.testimoni.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Aulia"
                        class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                    @error('nama') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1">Kelas</label>
                    <input type="text" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: XI IPA 2"
                        class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Isi Testimoni</label>
                <textarea name="quote" rows="4" required placeholder='"Awalnya saya ikut karena penasaran, tapi akhirnya dapat banyak teman dan pengalaman baru."'
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">{{ old('quote') }}</textarea>
                @error('quote') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Simpan</button>
        </form>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Kelas</th>
                    <th class="px-6 py-3">Testimoni</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($testimoniss as $testimoni)
                    <tr>
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium">{{ $testimoni->nama }}</td>
                        <td class="px-6 py-4">{{ $testimoni->kelas ?? '-' }}</td>
                        <td class="px-6 py-4 max-w-md">{{ $testimoni->quote }}</td>
                        <td class="px-6 py-4">
                            <form action="{{ route('ketua.testimoni.destroy', $testimoni) }}" method="POST" class="inline" onsubmit="return confirm('Hapus testimoni ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada testimoni.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection