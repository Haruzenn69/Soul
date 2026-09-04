@extends('ketua.layout')
@section('title', 'Detail Pendaftaran')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-lg">
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Tanggal Daftar</p>
            <p class="font-medium text-sm">{{ $pendaftaran->tanggal_daftar->format('d/m/Y') }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">NIS</p>
            <p class="font-medium text-sm">{{ $pendaftaran->siswa->nis }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Nama</p>
            <p class="font-medium text-sm">{{ $pendaftaran->siswa->nama }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Kelas</p>
            <p class="font-medium text-sm">{{ $pendaftaran->siswa->kelas->nama ?? '-' }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Alasan Bergabung</p>
            <p class="font-medium text-sm text-gray-700 bg-gray-50 p-3 rounded-2xl border border-gray-100 mt-1">{{ $pendaftaran->alasan ?? 'Tidak mencantumkan alasan.' }}</p>
        </div>
        <div class="mb-3">
            <p class="text-[11px] text-gray-400 font-bold uppercase">Status</p>
            @if($pendaftaran->status === 'pending')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Pending</span>
            @elseif($pendaftaran->status === 'diterima')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Diterima</span>
            @elseif($pendaftaran->status === 'ditolak')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Ditolak</span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ ucfirst($pendaftaran->status) }}</span>
            @endif
        </div>

        @if($pendaftaran->status === 'pending')
            <div class="mt-6 flex gap-2">
                <form action="{{ route('ketua.pendaftaran.update', $pendaftaran) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="diterima">
                    <button class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-full transition shadow-sm">Terima</button>
                </form>
                <form action="{{ route('ketua.pendaftaran.update', $pendaftaran) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="ditolak">
                    <button class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-full transition shadow-sm">Tolak</button>
                </form>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('ketua.pendaftaran.index') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-full transition">Kembali</a>
        </div>
    </div>
@endsection
