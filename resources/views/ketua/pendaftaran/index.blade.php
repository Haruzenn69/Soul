@extends('ketua.layout')
@section('title', 'Daftar Pendaftaran')

@section('content')
    <p class="text-xs text-gray-400 mb-4">Total: {{ $pendaftarans->count() }} pendaftar</p>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">NIS</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Tanggal Daftar</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pendaftarans as $pendaftaran)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">{{ $pendaftaran->siswa->nis }}</td>
                        <td class="px-6 py-4">{{ $pendaftaran->siswa->nama }}</td>
                        <td class="px-6 py-4">{{ $pendaftaran->tanggal_daftar->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @if($pendaftaran->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Pending</span>
                            @elseif($pendaftaran->status === 'diterima')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Diterima</span>
                            @elseif($pendaftaran->status === 'ditolak')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Ditolak</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">{{ ucfirst($pendaftaran->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('ketua.pendaftaran.show', $pendaftaran) }}" class="px-3 py-1 bg-blue-50 text-theme-blue hover:bg-blue-100 rounded-full font-semibold transition inline-block">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada pendaftaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
