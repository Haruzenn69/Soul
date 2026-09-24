@extends('pembina.layout')
@section('title', 'Kelola Testimoni')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Kelola Testimoni</h1>
            <p class="text-xs text-slate-400 mt-1">Moderasi testimoni ekskul binaanmu</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-100 text-sky-700 border border-sky-200 rounded-full text-[11px] font-bold shrink-0">
                {{ $totalCount }} total
            </span>
            @if($pendingCount > 0)
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-100 text-amber-700 border border-amber-200 rounded-full text-[11px] font-bold shrink-0">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $pendingCount }} menunggu persetujuan
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[11px] font-bold shrink-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Semua sudah diproses
                </span>
            @endif
        </div>
    </div>

    <!-- Filter Ekskul -->
    @if($ekskuls->count() > 1)
        <form method="GET" action="{{ route('pembina.testimoni.index') }}" class="flex items-center gap-2">
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
        <h3 class="text-sm font-extrabold text-slate-900 mb-4">Tambah Testimoni</h3>
        <form action="{{ route('pembina.testimoni.store') }}" method="POST" class="space-y-5">
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Aulia"
                        class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                    @error('nama') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kelas</label>
                    <input type="text" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: 11 IPA 2"
                        class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Isi Testimoni</label>
                <textarea name="quote" rows="4" required placeholder='"Awalnya saya ikut karena penasaran, tapi akhirnya dapat banyak teman dan pengalaman baru."'
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('quote') }}</textarea>
                @error('quote') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
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
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Nama</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Kelas</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Testimoni</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Status</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                @forelse($testimoniss as $testimoni)
                    <tr class="hover:bg-sky-50/50 transition">
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-sky-50 border border-sky-100 rounded-full text-[10px] font-bold text-sky-700">{{ $testimoni->ekskul->nama_ekskul ?? '-' }}</span>
                        </td>
                        <td class="px-4 md:px-6 py-3.5 font-medium whitespace-nowrap">{{ $testimoni->nama }}</td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">{{ $testimoni->kelas ?? '-' }}</td>
                        <td class="px-4 md:px-6 py-3.5 max-w-md">
                            @if($testimoni->status === 'pending')
                                <p class="text-slate-500">{{ $testimoni->quote }}</p>
                            @else
                                {{ $testimoni->quote }}
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            @if($testimoni->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                </span>
                            @elseif($testimoni->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ditampilkan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-100 text-rose-700 border border-rose-200 rounded-full text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Ditolak
                                </span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            @if($testimoni->status === 'pending')
                                <form action="{{ route('pembina.testimoni.approve', $testimoni) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-xl text-[10px] font-bold transition">Terima</button>
                                </form>
                                <form action="{{ route('pembina.testimoni.reject', $testimoni) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-200 rounded-xl text-[10px] font-bold transition">Tolak</button>
                                </form>
                            @endif
                            <form action="{{ route('pembina.testimoni.destroy', $testimoni) }}" method="POST" class="inline" onsubmit="return confirm('Hapus testimoni ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-200 rounded-xl text-[10px] font-bold transition">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 md:px-6 py-10 text-center text-slate-400">Belum ada testimoni. Tambahkan suara anggota melalui form di atas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
@endsection