@extends('layouts.kesiswaan')

@section('title', 'Data Ekskul')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-theme-dark">Data Ekskul</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola ekstrakurikuler beserta pembina dan pelatihnya.</p>
        </div>
        @if ($pembinas->isEmpty() || $pelatihs->isEmpty())
            <span class="px-4 py-2 bg-amber-50 text-amber-600 rounded-full text-[11px] font-bold">
                ⚠ {{ $pembinas->isEmpty() ? 'Buat akun pembina dulu di menu Akun Pengguna.' : 'Tambahkan data pelatih terlebih dahulu (seeder).' }}
            </span>
        @else
            <button onclick="document.getElementById('modal-create').showModal()"
                    class="px-6 py-3 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full shadow-lg shadow-blue-500/20 transition flex items-center gap-2 shrink-0">
                <span>+</span> Tambah Ekskul
            </button>
        @endif
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('kesiswaan.ekskuls.index') }}" class="flex flex-wrap gap-3 items-center bg-white p-4 rounded-3xl border border-gray-100 shadow-sm">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama ekskul atau pembina..."
               class="flex-1 min-w-[200px] px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
        <button type="submit" class="px-5 py-2.5 bg-theme-dark text-white font-bold text-xs rounded-2xl hover:bg-black transition">Cari</button>
    </form>

    <!-- Grid Ekskul -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($ekskuls as $ekskul)
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-11 h-11 rounded-2xl bg-theme-blue text-white font-extrabold flex items-center justify-center text-xs shadow-md shadow-blue-500/20 uppercase shrink-0">
                            {{ substr($ekskul->nama_ekskul, 0, 2) }}
                        </div>
                        <span class="px-3 py-1 rounded-full font-bold text-[10px] {{ $ekskul->is_open_recruitment ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-400' }}">
                            {{ $ekskul->is_open_recruitment ? '● Buka Pendaftaran' : '○ Tutup' }}
                        </span>
                    </div>
                    <h2 class="text-sm font-extrabold text-theme-dark">{{ $ekskul->nama_ekskul }}</h2>
                    @if (!$ekskul->status)
                        <span class="px-3 py-1 rounded-full bg-red-50 text-red-500 font-bold text-[10px]">Nonaktif</span>
                    @endif
                    <p class="text-[11px] text-gray-400 mt-1 line-clamp-2">{{ $ekskul->deskripsi ?? 'Tanpa deskripsi' }}</p>

                    <div class="mt-4 space-y-1.5 text-[11px]">
                        <p class="text-gray-500"> Pembina: <span class="font-bold text-theme-dark">{{ $ekskul->pembina?->nama ?? '-' }}</span></p>
                        <p class="text-gray-500"> Pelatih: <span class="font-bold text-theme-dark">{{ $ekskul->pelatih?->nama ?? '-' }}</span></p>
                        <p class="text-gray-500"> {{ $ekskul->jadwal ?? 'Jadwal belum diatur' }}</p>
                    </div>
                </div>

                <div class="flex gap-2 mt-5">
                    <button onclick='openEdit({{ json_encode([
                        "id" => $ekskul->id,
                        "nama_ekskul" => $ekskul->nama_ekskul,
                        "pembina_id" => $ekskul->pembina_id,
                        "pelatih_id" => $ekskul->pelatih_id,
                        "deskripsi" => $ekskul->deskripsi,
                        "jadwal" => $ekskul->jadwal,
                    ]) }})' 
                            class="flex-1 px-3 py-2 bg-blue-50 text-theme-blue font-bold rounded-full hover:bg-blue-100 transition text-[11px]">Edit</button>
                    <form action="{{ route('kesiswaan.ekskuls.destroy', $ekskul) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('{{ $ekskul->status ? 'Nonaktifkan ekskul ' . $ekskul->nama_ekskul . '?' : 'Aktifkan ekskul ' . $ekskul->nama_ekskul . '?' }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-3 py-2 bg-red-50 text-red-500 font-bold rounded-full hover:bg-red-100 transition text-[11px]">{{ $ekskul->status ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-10 rounded-3xl border border-dashed border-gray-200 text-center">
                <p class="text-xs text-gray-400">Belum ada ekskul. Klik "Tambah Ekskul" untuk membuat baru.</p>
            </div>
        @endforelse
    </div>

    <div>{{ $ekskuls->links() }}</div>

    <!-- Modal Create -->
    <dialog id="modal-create" class="rounded-3xl backdrop:bg-black/40 p-0 w-full max-w-lg">
        <form method="POST" action="{{ route('kesiswaan.ekskuls.store') }}" class="p-8 space-y-4">
            @csrf
            <h2 class="text-base font-extrabold text-theme-dark mb-2">Tambah Ekskul</h2>
            <input type="text" name="nama_ekskul" placeholder="Nama ekskul" required
                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
            <select name="pembina_id" required
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                <option value="" disabled selected>Pilih pembina...</option>
                @foreach ($pembinas as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
            <select name="pelatih_id" required
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                <option value="" disabled selected>Pilih pelatih...</option>
                @foreach ($pelatihs as $pl)
                    <option value="{{ $pl->id }}">{{ $pl->nama }} ({{ $pl->status }})</option>
                @endforeach
            </select>
            <textarea name="deskripsi" rows="2" placeholder="Deskripsi (opsional)"
                      class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition"></textarea>
            <input type="text" name="jadwal" placeholder="Jadwal, misal: Senin & Rabu, 15:30 - 17:00"
                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full transition">Simpan</button>
                <button type="button" onclick="this.closest('dialog').close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-full transition">Batal</button>
            </div>
        </form>
    </dialog>

    <!-- Modal Edit -->
    <dialog id="modal-edit" class="rounded-3xl backdrop:bg-black/40 p-0 w-full max-w-lg">
        <form id="form-edit" method="POST" class="p-8 space-y-4">
            @csrf
            @method('PUT')
            <h2 class="text-base font-extrabold text-theme-dark mb-2">Edit Ekskul</h2>
            <input type="text" name="nama_ekskul" placeholder="Nama ekskul" required
                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
            <select name="pembina_id" required
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                <option value="" disabled>Pilih pembina...</option>
                @foreach ($pembinas as $p)
                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                @endforeach
            </select>
            <select name="pelatih_id" required
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                <option value="" disabled>Pilih pelatih...</option>
                @foreach ($pelatihs as $pl)
                    <option value="{{ $pl->id }}">{{ $pl->nama }} ({{ $pl->status }})</option>
                @endforeach
            </select>
            <textarea name="deskripsi" rows="2" placeholder="Deskripsi (opsional)"
                      class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition"></textarea>
            <input type="text" name="jadwal" placeholder="Jadwal"
                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full transition">Simpan</button>
                <button type="button" onclick="this.closest('dialog').close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-full transition">Batal</button>
            </div>
        </form>
    </dialog>

    <script>
        function openEdit(data) {
            const form = document.getElementById('form-edit');
            form.action = '{{ url('kesiswaan/ekskuls') }}/' + data.id;
            form.querySelector('[name=nama_ekskul]').value = data.nama_ekskul || '';
            form.querySelector('[name=pembina_id]').value = data.pembina_id || '';
            form.querySelector('[name=pelatih_id]').value = data.pelatih_id || '';
            form.querySelector('[name=deskripsi]').value = data.deskripsi || '';
            form.querySelector('[name=jadwal]').value = data.jadwal || '';
            document.getElementById('modal-edit').showModal();
        }
    </script>
@endsection
