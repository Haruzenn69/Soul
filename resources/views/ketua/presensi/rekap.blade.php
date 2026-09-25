@extends('ketua.layout')
@section('title', 'Rekap Absensi - ' . $ekskul->nama_ekskul)

@section('content')
    <div class="space-y-6">
        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 animate-fade-up">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Rekap Absensi</h1>
                <p class="text-xs text-slate-400 mt-1">Rekapitulasi kehadiran anggota {{ $ekskul->nama_ekskul }} untuk {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('ketua.presensi.rekap') }}" class="flex items-center gap-2">
                    <label for="bulan" class="text-xs font-semibold text-slate-500">Bulan</label>
                    <select id="bulan" name="bulan" required onchange="this.form.submit()"
                        class="rounded-xl border-slate-200 text-xs focus:border-sky-400 focus:ring-sky-400">
                        @foreach ($availableMonths as $bulanOption)
                            <option value="{{ $bulanOption }}" @selected($bulan === $bulanOption)>
                                {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $bulanOption)->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('ketua.kegiatan.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Kegiatan</a>
                <a href="{{ route('ketua.dashboard') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">Dashboard</a>
            </div>
        </div>

        @include('partials.rekap-summary')
        @include('partials.rekap-matriks')
    </div>
@endsection