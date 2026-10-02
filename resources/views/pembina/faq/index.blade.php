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

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl border border-sky-100 shadow-sm overflow-hidden">
        <form method="GET" action="{{ route('pembina.faq.index') }}" id="faq-filter-form">
            <div class="p-4 flex flex-col sm:flex-row gap-3 border-b border-slate-100">
                {{-- Search --}}
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="cari" id="input-cari-faq" value="{{ request('cari') }}"
                        placeholder="Cari pertanyaan atau jawaban..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                </div>

                {{-- Filter Ekskul --}}
                @if($ekskuls->count() > 1)
                <div class="relative shrink-0">
                    <select name="ekskul" id="select-faq-ekskul" onchange="this.form.submit()"
                        class="pl-4 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all appearance-none">
                        <option value="">Semua Ekskul</option>
                        @foreach($ekskuls as $ex)
                            <option value="{{ $ex->id }}" {{ ($ekskulFilter ?? 0) == $ex->id ? 'selected' : '' }}>{{ $ex->nama_ekskul }}</option>
                        @endforeach
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </div>
                @endif

                {{-- Filter Status --}}
                <div class="relative shrink-0">
                    <select name="status" id="select-faq-status" onchange="this.form.submit()"
                        class="pl-4 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all appearance-none">
                        <option value="semua" @selected(request('status') === 'semua' || !request('status'))>Semua Status</option>
                        <option value="pending"  @selected(request('status') === 'pending')>Menunggu Jawaban</option>
                        <option value="answered" @selected(request('status') === 'answered')>Sudah Dijawab</option>
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </div>

                {{-- Sort --}}
                <div class="relative shrink-0">
                    <select name="sort" id="select-faq-sort" onchange="this.form.submit()"
                        class="pl-4 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all appearance-none">
                        <option value="status" @selected($sort === 'status' && $direction === 'asc')>Status (Pending Dulu)</option>
                        <option value="status_desc" @selected($sort === 'status' && $direction === 'desc')>Status (Dijawab Dulu)</option>
                        <option value="pertanyaan" @selected($sort === 'pertanyaan' && $direction === 'asc')>Pertanyaan A–Z</option>
                        <option value="pertanyaan_desc" @selected($sort === 'pertanyaan' && $direction === 'desc')>Pertanyaan Z–A</option>
                        <option value="created_at" @selected($sort === 'created_at' && $direction === 'asc')>Terlama</option>
                        <option value="created_at_desc" @selected($sort === 'created_at' && $direction === 'desc')>Terbaru</option>
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                    </span>
                </div>

                {{-- Submit + Reset --}}
                <div class="flex gap-2 shrink-0">
                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-2xl transition shadow-sm shadow-sky-200">
                        Cari
                    </button>
                    @if(request('cari') || request('ekskul') || (request('status') && request('status') !== 'semua'))
                        <a href="{{ route('pembina.faq.index') }}"
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-2xl transition">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="card-table w-full text-left text-xs md:text-sm">
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    @if($ekskuls->count() > 1)
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Ekskul</th>
                    @endif
                    @include('partials.th-sort', ['label' => 'Pertanyaan', 'key' => 'pertanyaan', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Jawaban</th>
                    @include('partials.th-sort', ['label' => 'Status', 'key' => 'status', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'])
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                @forelse($faqs as $faq)
                    <tr class="hover:bg-sky-50/50 transition">
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $loop->iteration }}</td>
                        @if($ekskuls->count() > 1)
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-sky-50 border border-sky-100 rounded-full text-[10px] font-bold text-sky-700">{{ $faq->ekskul->nama_ekskul ?? '-' }}</span>
                        </td>
                        @endif
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
                        <td colspan="{{ $ekskuls->count() > 1 ? 7 : 6 }}" class="px-4 md:px-6 py-10 text-center text-slate-400">
                            @if(request('cari') || request('status'))
                                <p class="text-sm font-medium">Tidak ada FAQ yang sesuai pencarian.</p>
                                <a href="{{ route('pembina.faq.index') }}" class="text-xs text-sky-500 hover:underline mt-1 inline-block">Reset filter</a>
                            @else
                                Belum ada FAQ. Tambahkan pertanyaan yang sering ditanyakan siswa.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @include('partials.table-pagination', ['rows' => $faqs, 'label' => 'FAQ'])
    </div>
</div>

<script>
// Handle sort select — encode sort+direction into a single value then split it on submit
document.getElementById('select-faq-sort')?.addEventListener('change', function() {
    const val = this.value;
    const form = document.getElementById('faq-filter-form');
    // Remove existing sort/direction hidden inputs
    form.querySelectorAll('input[name="sort"], input[name="direction"]').forEach(el => el.remove());

    const parts = val.split('_desc');
    const isDesc = val.endsWith('_desc');
    const sortKey = isDesc ? parts[0] : val;
    const dir = isDesc ? 'desc' : 'asc';

    const sortInput = document.createElement('input');
    sortInput.type = 'hidden';
    sortInput.name = 'sort';
    sortInput.value = sortKey;
    form.appendChild(sortInput);

    const dirInput = document.createElement('input');
    dirInput.type = 'hidden';
    dirInput.name = 'direction';
    dirInput.value = dir;
    form.appendChild(dirInput);

    // Remove the select's name so it doesn't conflict
    this.removeAttribute('name');
    form.submit();
});
</script>
@endsection