@extends('layouts.sidebar-ketua')
@section('title', 'Dashboard')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Dashboard Ketua Ekskul</h1>

    @if($ekskul)
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded shadow">
                <p class="text-sm text-gray-500">Total Anggota</p>
                <p class="text-2xl font-bold">{{ $totalAnggota }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <p class="text-sm text-gray-500">Pendaftaran Pending</p>
                <p class="text-2xl font-bold">{{ $pendingCount }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <p class="text-sm text-gray-500">Pengajuan Keluar</p>
                <p class="text-2xl font-bold">{{ $pengajuanCount }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h2 class="text-lg font-bold mb-2">Ekskul: {{ $ekskul->nama_ekskul }}</h2>
            <p class="text-sm text-gray-600">Pembina: {{ $ekskul->pembina->nama }}</p>
            <p class="text-sm text-gray-600">Pelatih: {{ $ekskul->pelatih->nama }}</p>
            <p class="text-sm text-gray-600">Jadwal: {{ $ekskul->jadwal ?? '-' }}</p>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded">
            <p class="text-yellow-700">Anda belum terdaftar di ekstrakurikuler manapun.</p>
        </div>
    @endif
@endsection
