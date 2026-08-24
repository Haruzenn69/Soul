@extends('layouts.kesiswaan')

@section('title', 'Data Kelas')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-theme-dark">Data Kelas</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola daftar kelas per tingkat dan tahun ajaran.</p>
        </div>
        @if ($tahunAjarans->isEmpty())
            <span class="px-4 py-2 bg-amber-50 text-amber-600 rounded-full text-[11px] font-bold">
                ⚠ Jalankan seeder untuk membuat data tahun ajaran terlebih dahulu.
            </span>
        @else
            <button onclick="document.getElementById('modal-create').showModal()"
                    class="px-6 py-3 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full shadow-lg shadow-blue-500/20 transition flex items-center gap-2 shrink-0">
                <span>+</span> Tambah Kelas
            </button>
        @endif
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('kesiswaan.kelas.index') }}" class="flex flex-wrap gap-3 items-center bg-white p-4 rounded-3xl border border-gray-100 shadow-sm">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kelas..."
               class="flex-1 min-w-[180px] px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
        <select name="tingkat" class="px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
            <option value="">Semua Tingkat</option>
            @foreach (['x' => 'X', 'xi' => 'XI', 'xii' => 'XII'] as $value => $label)
                <option value="{{ $value }}" {{ request('tingkat') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 bg-theme-dark text-white font-bold text-xs rounded-2xl hover:bg-black transition">Filter</button>
    </form>

    <!-- Tabel Kelas -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[11px] font-bold text-gray-400 tracking-wider uppercase border-b border-gray-100">
                    <th class="py-3 px-2">Nama Kelas</th>
                    <th class="py-3 px-2">Tingkat</th>
                    <th class="py-3 px-2">Tahun Ajaran</th>
                    <th class="py-3 px-2">Jumlah Siswa</th>
                    <th class="py-3 px-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($kelas as $k)
                    <tr class="hover:bg-gray-50/60 transition text-xs">
                        <td class="py-3.5 px-2 font-bold">{{ $k->nama }}</td>
                        <td class="py-3.5 px-2">
                            <span class="px-3 py-1 rounded-full font-bold text-[11px] bg-blue-50 text-theme-blue uppercase">{{ $k->tingkat }}</span>
                        </td>
                        <td class="py-3.5 px-2 text-gray-500">{{ $k->tahunAjaran?->nama ?? '-' }}
                            @if ($k->tahunAjaran?->is_active)
                                <span class="ml-1 px-2 py-0.5 rounded-full font-bold text-[10px] bg-emerald-50 text-emerald-600">Aktif</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-2 text-gray-500">{{ $k->siswas->count() }} siswa</td>
                        <td class="py-3.5 px-2">
                            <div class="flex gap-2 justify-end items-center">
                                <button onclick='openEdit({{ json_encode([
                                    "id" => $k->id,
                                    "nama" => $k->nama,
                                    "tingkat" => $k->tingkat,
                                    "tahun_ajaran_id" => $k->tahun_ajaran_id,
                                ]) }})'
                                        class="px-4 py-1.5 bg-blue-50 text-theme-blue font-bold rounded-full hover:bg-blue-100 transition">Edit</button>
                                <form action="{{ route('kesiswaan.kelas.destroy', $k) }}" method="POST"
                                      onsubmit="return confirm('Hapus kelas {{ $k->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-1.5 bg-red-50 text-red-500 font-bold rounded-full hover:bg-red-100 transition">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400 text-xs">Belum ada kelas. Klik "Tambah Kelas" untuk membuat baru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $kelas->links() }}
        </div>
    </div>

    <!-- Modal Create -->
    <dialog id="modal-create" class="rounded-3xl backdrop:bg-black/40 p-0 w-full max-w-md">
        <form method="POST" action="{{ route('kesiswaan.kelas.store') }}" class="p-8 space-y-4">
            @csrf
            <h2 class="text-base font-extrabold text-theme-dark mb-2">Tambah Kelas</h2>
            <input type="text" name="nama" placeholder="Nama kelas, misal: XI RPL 1" required
                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
            <select name="tingkat" required
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                <option value="" disabled selected>Pilih tingkat...</option>
                <option value="x">X</option>
                <option value="xi">XI</option>
                <option value="xii">XII</option>
            </select>
            <select name="tahun_ajaran_id" required
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                <option value="" disabled selected>Pilih tahun ajaran...</option>
                @foreach ($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" {{ $ta->is_active ? 'selected' : '' }}>
                        {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full transition">Simpan</button>
                <button type="button" onclick="this.closest('dialog').close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-full transition">Batal</button>
            </div>
        </form>
    </dialog>

    <!-- Modal Edit -->
    <dialog id="modal-edit" class="rounded-3xl backdrop:bg-black/40 p-0 w-full max-w-md">
        <form id="form-edit" method="POST" class="p-8 space-y-4">
            @csrf
            @method('PUT')
            <h2 class="text-base font-extrabold text-theme-dark mb-2">Edit Kelas</h2>
            <input type="text" name="nama" placeholder="Nama kelas" required
                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
            <select name="tingkat" required
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                <option value="x">X</option>
                <option value="xi">XI</option>
                <option value="xii">XII</option>
            </select>
            <select name="tahun_ajaran_id" required
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                @foreach ($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full transition">Simpan</button>
                <button type="button" onclick="this.closest('dialog').close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-full transition">Batal</button>
            </div>
        </form>
    </dialog>

    <script>
        function openEdit(data) {
            const form = document.getElementById('form-edit');
            form.action = '{{ url('kesiswaan/kelas') }}/' + data.id;
            form.querySelector('[name=nama]').value = data.nama || '';
            form.querySelector('[name=tingkat]').value = data.tingkat || '';
            form.querySelector('[name=tahun_ajaran_id]').value = data.tahun_ajaran_id || '';
            document.getElementById('modal-edit').showModal();
        }
    </script>
@endsection
