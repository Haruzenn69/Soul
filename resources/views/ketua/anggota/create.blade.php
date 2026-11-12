@extends('ketua.layout')
@section('title', 'Tambahkan Anggota')

@section('content')
<div class="space-y-5">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-5 md:p-7 text-white soul-timbul shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold tracking-tight">Tambahkan Anggota</h1>
                <p class="mt-1 text-xs md:text-sm text-white/85">Pilih siswa dari akun yang sudah dibuat Kesiswaan untuk {{ $ekskul->nama_ekskul }}.</p>
            </div>
            <a href="{{ route('ketua.anggota.index') }}" class="inline-flex items-center gap-2 self-start rounded-xl border border-white/25 bg-white/10 px-3.5 py-2 text-xs font-semibold text-white transition hover:bg-white/20">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-medium text-rose-700">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('ketua.anggota.create') }}" class="flex flex-col sm:flex-row gap-2 rounded-2xl border border-sky-100 bg-white p-3 shadow-sm">
        <input type="search" name="cari" value="{{ $cari }}" placeholder="Cari nama atau NIS siswa..." class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-700 focus:border-sky-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-100">
        <select name="kelas_id" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs text-slate-700 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100">
            <option value="">Semua Kelas</option>
            @foreach($kelasOptions as $kelas)
                <option value="{{ $kelas->id }}" @selected($kelasId === $kelas->id)>{{ $kelas->nama }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-sky-700">Cari</button>
        @if($cari || $kelasId)
            <a href="{{ route('ketua.anggota.create') }}" class="rounded-xl border border-slate-200 px-4 py-2.5 text-center text-xs font-semibold text-slate-600 transition hover:bg-slate-50">Reset</a>
        @endif
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
        @forelse($siswas as $siswa)
            <article class="flex min-w-0 items-center gap-3 rounded-2xl border border-sky-100 bg-white p-4 shadow-sm">
                <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama }}" class="h-12 w-12 shrink-0 rounded-xl border border-slate-100 object-cover">
                <div class="min-w-0 flex-1">
                    <h2 class="truncate text-sm font-bold text-slate-800">{{ $siswa->nama }}</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">NIS {{ $siswa->nis }} · {{ $siswa->kelas?->nama ?? 'Kelas belum tersedia' }}</p>
                </div>
                <form method="POST" action="{{ route('ketua.anggota.store') }}" onsubmit="return confirm('Tambahkan {{ addslashes($siswa->nama) }} sebagai anggota {{ addslashes($ekskul->nama_ekskul) }}?')">
                    @csrf
                    <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                    <button type="submit" class="shrink-0 rounded-xl bg-sky-600 px-3 py-2 text-[11px] font-bold text-white transition hover:bg-sky-700">Tambahkan</button>
                </form>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-slate-200 bg-white p-8 text-center">
                <h2 class="text-sm font-bold text-slate-700">Tidak ada siswa yang cocok</h2>
                <p class="mt-1 text-xs text-slate-500">Periksa pencarian dan filter kelas, atau pastikan siswa sudah memiliki akun aktif.</p>
            </div>
        @endforelse
    </div>

    {{ $siswas->links() }}
</div>
@endsection
