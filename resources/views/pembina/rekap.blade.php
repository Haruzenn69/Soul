@extends('pembina.layout')
@section('title', 'Rekap Absensi')

@section('content')
    <div class="animate-fade-up">
        <h1 class="text-xl font-extrabold text-slate-900">Rekap Absensi</h1>
        <p class="text-xs text-slate-400 mt-0.5">Rekap kehadiran anggota per bulan pada ekskul yang anda bina</p>
    </div>

    {{-- Filter Bulan & Ekskul --}}
    <form method="GET" action="{{ route('pembina.rekap') }}" class="bg-white p-4 md:p-5 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 flex flex-wrap items-center gap-3 animate-fade-up" style="animation-delay: .1s">
        <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Periode Bulan
        </span>
        <select name="bulan" class="px-3 py-1.5 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
            @foreach($availableMonths as $option)
                @php
                    $optionLabel = \Carbon\Carbon::createFromFormat('Y-m', $option)->translatedFormat('F Y');
                @endphp
                <option value="{{ $option }}" {{ $bulan === $option ? 'selected' : '' }}>{{ $optionLabel }}</option>
            @endforeach
        </select>

        @if(count($ekskuls) > 1)
            <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Ekskul
            </span>
            <select name="ekskul" class="px-3 py-1.5 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                @foreach($ekskuls as $ekskul)
                    <option value="{{ $ekskul->id }}" {{ (int) $ekskulId === (int) $ekskul->id ? 'selected' : '' }}>{{ $ekskul->nama_ekskul }}</option>
                @endforeach
            </select>
        @endif

        <button type="submit" class="px-4 py-1.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 transition-all hover:-translate-y-0.5">Tampilkan Rekap</button>
        <span class="ml-auto text-[11px] font-semibold text-slate-400 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full">{{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}</span>
    </form>

    @include('partials.rekap-summary')
    @include('partials.rekap-matriks')
@endsection