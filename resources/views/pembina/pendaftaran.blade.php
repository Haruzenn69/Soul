@extends('pembina.layout')
@section('title', 'Pendaftaran Ekskul')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 animate-fade-up">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Riwayat Pendaftaran</h1>
            <p class="text-xs text-slate-400 mt-0.5">Daftar siswa yang mengajukan pendaftaran ke ekskul yang anda bina</p>
        </div>
    </div>

    <!-- RINGKASAN STATUS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .1s">
            <p class="text-2xl font-extrabold text-slate-900">{{ $pendaftarans->count() }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase">Total</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .15s">
            <p class="text-2xl font-extrabold text-amber-600">{{ $grouped->get('pending', collect())->count() }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase">Pending</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .2s">
            <p class="text-2xl font-extrabold text-emerald-600">{{ $grouped->get('diterima', collect())->count() }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase">Diterima</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .25s">
            <p class="text-2xl font-extrabold text-red-600">{{ $grouped->get('ditolak', collect())->count() }}</p>
            <p class="text-[10px] font-bold text-slate-400 uppercase">Ditolak</p>
        </div>
    </div>

    <!-- FILTER -->
    <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .3s">
        <form method="GET" action="{{ route('pembina.pendaftaran') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau NIS..."
                    class="w-full pl-10 pr-4 py-2 bg-sky-50/60 border border-sky-100 rounded-xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
            </div>
            <select name="status" class="px-3 py-2 bg-sky-50/60 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                <option value="">Semua Status</option>
                @foreach(['pending' => 'Pending', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak', 'nonaktif' => 'Nonaktif'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') == $val)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-semibold rounded-xl transition shadow-lg shadow-sky-200">Filter</button>
        </form>
    </div>

    <!-- DAFTAR PENDAFTARAN -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .35s">
        @if(count($pendaftarans) > 0)
            <div class="overflow-x-auto">
                <table class="card-table w-full text-xs">
                    <thead class="bg-sky-50">
                        <tr>
                            <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">No</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Nama</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Kelas</th>
                            @if($ekskuls->count() > 1)<th class="text-left p-3 font-semibold text-slate-500">Ekskul</th>@endif
                            <th class="text-left p-3 font-semibold text-slate-500">Alasan</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Tanggal Daftar</th>
                            <th class="text-left p-3 font-semibold text-slate-500 rounded-r-xl">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftarans as $key => $item)
                        <tr class="border-b border-sky-50 hover:bg-sky-50/50 transition">
                            <td class="p-3">{{ $key + 1 }}</td>
                            <td class="p-3 font-medium">{{ $item->siswa->nama ?? '-' }}</td>
                            <td class="p-3">{{ $item->siswa->kelas->nama ?? '-' }}</td>
                            @if($ekskuls->count() > 1)<td class="p-3">{{ $item->ekskul->nama_ekskul ?? '-' }}</td>@endif
                            <td class="p-3 max-w-[220px]">
                                <p class="truncate" title="{{ $item->alasan }}">{{ $item->alasan ?? '-' }}</p>
                            </td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($item->tanggal_daftar)->isoFormat('DD MMM Y') }}</td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded-full text-[10px] font-semibold border
                                    @if($item->status == 'pending') bg-amber-100 text-amber-700 border-amber-200
                                    @elseif($item->status == 'diterima') bg-emerald-100 text-emerald-700 border-emerald-200
                                    @elseif($item->status == 'ditolak') bg-red-100 text-red-700 border-red-200
                                    @else bg-slate-100 text-slate-600 border-slate-200 @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-slate-400">
                <p class="text-sm">Belum ada riwayat pendaftaran.</p>
            </div>
        @endif
    </div>
@endsection