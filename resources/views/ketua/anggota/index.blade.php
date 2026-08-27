@extends('ketua.layout')
@section('title', 'Kelola Anggota')

@section('content')
    <p class="text-xs text-gray-400 mb-4">Total: {{ $anggotas->count() }} anggota</p>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3">No</th>
                    <th class="px-6 py-3">NIS</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Kelas</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($anggotas as $anggota)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">{{ $anggota->siswa->nis }}</td>
                        <td class="px-6 py-4">{{ $anggota->siswa->nama }}</td>
                        <td class="px-6 py-4">{{ $anggota->siswa->kelas->nama ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($anggota->status === 'diterima')
                                <span class="text-green-600 font-medium">Aktif</span>
                            @else
                                <span class="text-red-500 font-medium">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('ketua.anggota.toggle', $anggota) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-theme-blue hover:underline font-medium">
                                    {{ $anggota->status === 'diterima' ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada anggota.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
