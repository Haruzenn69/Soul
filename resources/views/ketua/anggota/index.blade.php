@extends('ketua.layout')
@section('title', 'Kelola Anggota')

@section('content')
    <p class="text-xs text-gray-400 mb-4">Total: {{ $anggotas->count() }} anggota</p>

    <div class="bg-white p-4 rounded-3xl border border-gray-100 shadow-sm mb-4">
        <p class="text-xs font-bold text-gray-500 mb-2 flex items-center gap-2">
            <span class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center text-[11px]">⚠️</span>
            Status Keanggotaan
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-[11px]">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
                <span><strong class="text-green-600">Aktif</strong> — anggota yang masih bergabung & mengikuti ekskul.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                <span><strong class="text-amber-600">Peringatan</strong> ({{ $peringatanCount }}) — anggota yang diberi notifikasi korektif; tetap terhitung aktif.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                <span><strong class="text-red-500">Nonaktif</strong> ({{ $nonaktifCount }}) — anggota yang dinonaktifkan dari ekskul.</span>
            </div>
        </div>
    </div>

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
                    @php
                        $isSelf = $anggota->siswa_id === (auth()->user()->siswa->id ?? null);
                    @endphp
                    <tr class="hover:bg-gray-50/50 {{ $anggota->status === 'nonaktif' ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4">{{ $anggota->siswa->nis }}</td>
                        <td class="px-6 py-4">
                            {{ $anggota->siswa->nama }}
                            @if($isSelf)
                                <span class="ml-1 px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded-md text-[9px] font-bold uppercase">Kamu</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $anggota->siswa->kelas->nama ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($anggota->status === 'diterima')
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-600 text-[11px] font-semibold rounded-full">Aktif</span>
                            @elseif($anggota->status === 'peringatan')
                                <span class="inline-block px-3 py-1 bg-amber-100 text-amber-600 text-[11px] font-semibold rounded-full">Peringatan</span>
                            @else
                                <span class="inline-block px-3 py-1 bg-red-100 text-red-500 text-[11px] font-semibold rounded-full">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($isSelf)
                                <span class="text-gray-300 text-[11px]">—</span>
                            @else
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($anggota->status === 'diterima')
                                        <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST"
                                            onsubmit="return confirm('Beri peringatan kepada {{ addslashes($anggota->siswa->nama) }}? Siswa akan menerima alert peringatan di akunnya. Status tetap aktif.');">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="peringatan">
                                            <button type="submit" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-semibold rounded-full transition">⚠️ Peringatan</button>
                                        </form>
                                        <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST"
                                            onsubmit="return confirm('Nonaktifkan {{ addslashes($anggota->siswa->nama) }}? Siswa tidak akan terhitung anggota aktif ekskul.');">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="nonaktif">
                                            <button type="submit" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[10px] font-semibold rounded-full transition">Nonaktifkan</button>
                                        </form>
                                    @elseif($anggota->status === 'peringatan')
                                        <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="diterima">
                                            <button type="submit" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-[10px] font-semibold rounded-full transition">Aktifkan</button>
                                        </form>
                                        <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST"
                                            onsubmit="return confirm('Nonaktifkan {{ addslashes($anggota->siswa->nama) }}? Siswa tidak akan terhitung anggota aktif ekskul.');">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="nonaktif">
                                            <button type="submit" class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-[10px] font-semibold rounded-full transition">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <form action="{{ route('ketua.anggota.update-status', $anggota) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="diterima">
                                            <button type="submit" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-[10px] font-semibold rounded-full transition">Aktifkan</button>
                                        </form>
                                    @endif
                                </div>
                            @endif
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