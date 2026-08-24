@extends('ketua.layout')
@section('title', 'Daftar Pengajuan Keluar')

@section('content')
    <p class="text-xs text-gray-400 mb-4">Total: {{ $pengajuanKeluars->count() }} pengajuan</p>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Alasan</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pengajuanKeluars as $pengajuan)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">{{ $pengajuan->siswa->nama }}</td>
                        <td class="px-6 py-4">{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">{{ Str::limit($pengajuan->alasan, 30) }}</td>
                        <td class="px-6 py-4">
                            @if($pengajuan->status === 'pending')
                                <span class="text-yellow-600 font-medium">Pending</span>
                            @elseif($pengajuan->status === 'diterima')
                                <span class="text-green-600 font-medium">Diterima</span>
                            @else
                                <span class="text-red-600 font-medium">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('ketua.pengajuan-keluar.show', $pengajuan) }}" class="text-theme-blue hover:underline font-medium">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada pengajuan keluar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
