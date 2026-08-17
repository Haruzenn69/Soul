@extends('layouts.sidebar-ketua')
@section('title', 'Pendaftaran')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Pendaftaran Masuk</h1>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-left">NIS</th>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftarans as $pendaftaran)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $pendaftaran->tanggal_daftar->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $pendaftaran->siswa->nis }}</td>
                        <td class="px-4 py-2">{{ $pendaftaran->siswa->nama }}</td>
                        <td class="px-4 py-2">
                            @if($pendaftaran->status === 'pending')
                                <span class="text-yellow-600">Pending</span>
                            @else
                                <span class="text-green-600">Diterima</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            @if($pendaftaran->status === 'pending')
                                <form action="{{ route('ketua.pendaftaran.update', $pendaftaran) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="diterima">
                                    <button class="text-green-600 hover:underline">Terima</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-400">Tidak ada pendaftaran</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
