@extends('layouts.sidebar-ketua')
@section('title', 'Pengajuan Keluar')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Pengajuan Keluar Ekskul</h1>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-left">NIS</th>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">Alasan</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuanKeluars as $pengajuan)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $pengajuan->siswa->nis }}</td>
                        <td class="px-4 py-2">{{ $pengajuan->siswa->nama }}</td>
                        <td class="px-4 py-2">{{ $pengajuan->alasan }}</td>
                        <td class="px-4 py-2">
                            @if($pengajuan->status === 'pending')
                                <span class="text-yellow-600">Pending</span>
                            @elseif($pengajuan->status === 'diterima')
                                <span class="text-green-600">Diterima</span>
                            @else
                                <span class="text-red-600">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if($pengajuan->status === 'pending')
                                <form action="{{ route('ketua.pengajuan-keluar.update', $pengajuan) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="diterima">
                                    <button class="text-green-600 hover:underline">Setuju</button>
                                </form>
                                <form action="{{ route('ketua.pengajuan-keluar.update', $pengajuan) }}" method="POST" class="inline ml-2">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="ditolak">
                                    <button class="text-red-600 hover:underline">Tolak</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-400">Tidak ada pengajuan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
