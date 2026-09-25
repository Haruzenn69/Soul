@extends('pembina.layout')
@section('title', 'Kelola FAQ')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Kelola FAQ</h1>
            <p class="text-xs text-slate-400 mt-1">Moderasi pertanyaan umum ekskul binaanmu</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-100 text-sky-700 border border-sky-200 rounded-full text-[11px] font-bold shrink-0">
                {{ $totalCount }} total
            </span>
            @if($pendingCount > 0)
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-100 text-amber-700 border border-amber-200 rounded-full text-[11px] font-bold shrink-0">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $pendingCount }} menunggu jawaban
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[11px] font-bold shrink-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Semua sudah dijawab
                </span>
            @endif
        </div>
    </div>

    <!-- Filter Ekskul -->
    @if($ekskuls->count() > 1)
        <form method="GET" action="{{ route('pembina.faq.index') }}" class="flex items-center gap-2">
            <select name="ekskul" onchange="this.form.submit()"
                class="px-4 py-2.5 bg-white border border-sky-100 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                <option value="">Semua Ekskul</option>
                @foreach($ekskuls as $ex)
                    <option value="{{ $ex->id }}" {{ ($ekskulFilter ?? 0) == $ex->id ? 'selected' : '' }}>{{ $ex->nama_ekskul }}</option>
                @endforeach
            </select>
        </form>
    @endif

    <!-- Add Form Card -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl space-y-5 animate-fade-up" style="animation-delay: .1s">
        <h3 class="text-sm font-extrabold text-slate-900 mb-4">Tambah FAQ</h3>
        <form action="{{ route('pembina.faq.store') }}" method="POST" class="space-y-5">
            @csrf
            @if($ekskuls->count() > 1)
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Ekskul</label>
                    <select name="ekskul_id" required
                        class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                        <option value="">Pilih ekskul</option>
                        @foreach($ekskuls as $ex)
                            <option value="{{ $ex->id }}" {{ old('ekskul_id', $ekskulFilter ?? '') == $ex->id ? 'selected' : '' }}>{{ $ex->nama_ekskul }}</option>
                        @endforeach
                    </select>
                    @error('ekskul_id') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            @else
                <input type="hidden" name="ekskul_id" value="{{ $ekskuls->first()?->id }}">
            @endif
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
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Ekskul</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Pertanyaan</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Jawaban</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Status</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                @forelse($faqs as $faq)
                    <tr class="hover:bg-sky-50/50 transition">
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-sky-50 border border-sky-100 rounded-full text-[10px] font-bold text-sky-700">{{ $faq->ekskul->nama_ekskul ?? '-' }}</span>
                        </td>
                        <td class="px-4 md:px-6 py-3.5 font-medium max-w-sm leading-relaxed">{{ $faq->pertanyaan }}</td>
                        <td class="px-4 md:px-6 py-3.5 max-w-md">
                            @if($faq->status === 'pending')
                                <form action="{{ route('pembina.faq.answer', $faq) }}" method="POST" class="space-y-2">
                                    @csrf
                                    @method('PATCH')
                                    <textarea name="jawaban" rows="3" required placeholder="Tulis jawaban lalu terbitkan..."
                                        class="w-full px-3 py-2 bg-amber-50/50 border border-amber-100 rounded-xl text-xs focus:outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition"></textarea>
                                    @error('jawaban') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-xl text-[10px] font-bold transition">Terbitkan Jawaban</button>
                                </form>
                            @else
                                <p class="text-slate-600 leading-relaxed">{{ $faq->jawaban }}</p>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            @if($faq->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ditampilkan
                                </span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <form action="{{ route('pembina.faq.destroy', $faq) }}" method="POST" class="inline" onsubmit="return confirm('Hapus FAQ ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-200 rounded-xl text-[10px] font-bold transition">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 md:px-6 py-10 text-center text-slate-400">Belum ada FAQ. Tambahkan pertanyaan yang sering ditanyakan siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
@endsection