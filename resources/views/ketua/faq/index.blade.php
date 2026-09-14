@extends('ketua.layout')
@section('title', 'Kelola FAQ')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Kelola FAQ</h1>
            <p class="text-xs text-slate-400 mt-1">Tambah dan kelola pertanyaan umum ekskul</p>
        </div>
    </div>

    <!-- Add Form Card -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl space-y-5 animate-fade-up" style="animation-delay: .1s">
        <h3 class="text-sm font-extrabold text-slate-900 mb-4">Tambah FAQ</h3>
        <form action="{{ route('ketua.faq.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Pertanyaan</label>
                <input type="text" name="pertanyaan" value="{{ old('pertanyaan') }}" required placeholder="Contoh: Apakah harus punya pengalaman sebelumnya?"
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                @error('pertanyaan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Jawaban</label>
                <textarea name="jawaban" rows="3" required placeholder="Jawaban..."
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('jawaban') }}</textarea>
                @error('jawaban') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 transition w-full sm:w-auto">Simpan</button>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="card-table w-full text-left text-xs md:text-sm">
            <thead class="bg-sky-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Pertanyaan</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Jawaban</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                @forelse($faqs as $faq)
                    <tr class="hover:bg-sky-50/50 transition">
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-4 md:px-6 py-3.5 font-medium max-w-sm whitespace-nowrap">{{ $faq->pertanyaan }}</td>
                        <td class="px-4 md:px-6 py-3.5 max-w-md">{{ $faq->jawaban }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <form action="{{ route('ketua.faq.destroy', $faq) }}" method="POST" class="inline" onsubmit="return confirm('Hapus FAQ ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-medium">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 md:px-6 py-8 text-center text-slate-400">Belum ada FAQ.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
@endsection