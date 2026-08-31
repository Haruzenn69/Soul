@extends('ketua.layout')
@section('title', 'Kelola FAQ')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-2xl mb-6">
        <h3 class="text-sm font-bold text-theme-dark mb-4">Tambah FAQ</h3>
        <form action="{{ route('ketua.faq.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Pertanyaan</label>
                <input type="text" name="pertanyaan" value="{{ old('pertanyaan') }}" required placeholder="Contoh: Apakah harus punya pengalaman sebelumnya?"
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                @error('pertanyaan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Jawaban</label>
                <textarea name="jawaban" rows="3" required placeholder="Jawaban..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">{{ old('jawaban') }}</textarea>
                @error('jawaban') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Simpan</button>
        </form>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Pertanyaan</th>
                    <th class="px-6 py-3">Jawaban</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($faqs as $faq)
                    <tr>
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium max-w-sm">{{ $faq->pertanyaan }}</td>
                        <td class="px-6 py-4 max-w-md">{{ $faq->jawaban }}</td>
                        <td class="px-6 py-4">
                            <form action="{{ route('ketua.faq.destroy', $faq) }}" method="POST" class="inline" onsubmit="return confirm('Hapus FAQ ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada FAQ.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection