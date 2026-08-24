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
                                <span class="text-yellow-600 font-medium">Pending</span>
                            @else
                                <span class="text-green-600 font-medium">Diterima</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('ketua.pendaftaran.show', $pendaftaran) }}" class="text-theme-blue hover:underline font-medium">Detail</a>
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
