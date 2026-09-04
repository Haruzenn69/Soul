@extends('pembina.layout')
@section('title', 'Data Anggota')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Data Anggota Ekskul</h1>
            <p class="text-xs text-slate-400 mt-0.5">Anggota aktif dari ekskul yang anda bina</p>
        </div>
    </div>

    <!-- FILTER -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/70 shadow-sm">
        <form method="GET" action="{{ route('pembina.anggota') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau NIS..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 transition-all">
            </div>
            @if($ekskuls->count() > 1)
            <select name="ekskul" class="px-3 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-all">
                <option value="">Semua Ekskul</option>
                @foreach($ekskuls as $e)
                    <option value="{{ $e->id }}" @selected(request('ekskul') == $e->id)>{{ $e->nama_ekskul }}</option>
                @endforeach
            </select>
            @endif
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition shadow-sm">Cari</button>
        </form>
    </div>

    <!-- TABEL ANGOTA -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-bold text-slate-900">Daftar Anggota</h2>
            <span class="text-xs font-medium text-slate-400">Total {{ count($anggota) }} anggota</span>
        </div>

        @if(count($anggota) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">No</th>
                            <th class="text-left p-3 font-semibold text-slate-500">NIS</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Nama</th>
                            <th class="text-left p-3 font-semibold text-slate-500">Kelas</th>
                            @if($ekskuls->count() > 1)<th class="text-left p-3 font-semibold text-slate-500">Ekskul</th>@endif
                            <th class="text-left p-3 font-semibold text-slate-500">Jabatan</th>
                            <th class="text-left p-3 font-semibold text-slate-500 rounded-r-xl">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($anggota as $key => $item)
                        <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                            <td class="p-3">{{ $key + 1 }}</td>
                            <td class="p-3 font-medium">{{ $item->siswa->nis ?? '-' }}</td>
                            <td class="p-3 font-medium">{{ $item->siswa->nama ?? '-' }}</td>
                            <td class="p-3">{{ $item->siswa->kelas->nama ?? '-' }}</td>
                            @if($ekskuls->count() > 1)<td class="p-3">{{ $item->ekskul->nama_ekskul ?? '-' }}</td>@endif
                            <td class="p-3">
                                <span class="px-2 py-1 rounded-full text-[10px] font-semibold 
                                    {{ $item->siswa->jabatan == 'ketua' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ ucfirst($item->siswa->jabatan ?? 'anggota') }}
                                </span>
                            </td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($item->tanggal_daftar)->isoFormat('DD MMM Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-slate-400">
                <p class="text-sm">Belum ada anggota di ekskul ini.</p>
            </div>
        @endif
    </div>
@endsection
