@extends('layouts.kesiswaan')

@section('title', 'Data Kelas')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-theme-dark">Data Kelas</h1>
            <p class="text-xs text-gray-400 mt-1">Kelola daftar kelas per tingkat dan tahun ajaran.</p>
        </div>
        @if ($tahunAjarans->isEmpty())
            <span class="px-4 py-2 bg-amber-50 text-amber-600 rounded-full text-[11px] font-bold flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                Jalankan seeder untuk membuat data tahun ajaran terlebih dahulu.
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
            @foreach (config('kelas.tingkat') as $value => $label)
                <option value="{{ $value }}" {{ request('tingkat') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-5 py-2.5 bg-theme-dark text-white font-bold text-xs rounded-2xl hover:bg-black transition">Filter</button>
    </form>

    <!-- Tabel Kelas -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm overflow-x-auto">
        <table class="card-table w-full text-left">
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
                            <span class="px-3 py-1 rounded-full font-bold text-[11px] bg-blue-50 text-theme-blue uppercase">{{ config("kelas.tingkat.{$k->tingkat}") }}</span>
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
                                    "jurusan" => $k->jurusan,
                                    "rombel" => $k->rombel,
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

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Tingkat</label>
                <select name="tingkat" required data-tingkat
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                    <option value="" disabled selected>Pilih tingkat...</option>
                    @foreach (config('kelas.tingkat') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Jurusan</label>
                <select name="jurusan" required data-jurusan disabled
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition disabled:opacity-60">
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Nomor Rombel</label>
                <input type="number" name="rombel" min="1" required data-rombel
                       placeholder="misal: 1, 2, 3..."
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
            </div>

            <select name="tahun_ajaran_id" required
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                <option value="" disabled selected>Pilih tahun ajaran...</option>
                @foreach ($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" {{ $ta->is_active ? 'selected' : '' }}>
                        {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>

            <div class="px-4 py-3 bg-blue-50/60 border border-blue-100 rounded-2xl">
                <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wide mb-1">Nama Kelas</span>
                <p data-preview class="text-sm font-extrabold text-theme-blue">Pilih tingkat, jurusan, dan rombel</p>
            </div>

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

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Tingkat</label>
                <select name="tingkat" required data-tingkat
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                    @foreach (config('kelas.tingkat') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Jurusan</label>
                <select name="jurusan" required data-jurusan
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 mb-1.5 uppercase tracking-wide">Nomor Rombel</label>
                <input type="number" name="rombel" min="1" required data-rombel
                       placeholder="misal: 1, 2, 3..."
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
            </div>

            <select name="tahun_ajaran_id" required
                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                @foreach ($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}">{{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>

            <div class="px-4 py-3 bg-blue-50/60 border border-blue-100 rounded-2xl">
                <span class="block text-[11px] font-bold text-gray-500 uppercase tracking-wide mb-1">Nama Kelas</span>
                <p data-preview class="text-sm font-extrabold text-theme-blue">Pilih tingkat, jurusan, dan rombel</p>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full transition">Simpan</button>
                <button type="button" onclick="this.closest('dialog').close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-full transition">Batal</button>
            </div>
        </form>
    </dialog>

    <script>
        const tingkatLabels = @json(config('kelas.tingkat'));

        const jurusanMap = (function () {
            const map = {};
            const jurusan = @json(config('kelas.jurusan'));
            Object.entries(jurusan).forEach(([kode, labels]) => {
                Object.keys(tingkatLabels).forEach((tingkat) => {
                    map[tingkat] = map[tingkat] || [];
                    map[tingkat].push({ value: kode, label: labels[tingkat] || kode });
                });
            });
            return map;
        })();

        function fillJurusan(select, tingkat, selectedValue) {
            select.innerHTML = '';
            if (!tingkat) {
                select.appendChild(placeholderOption('Pilih tingkat dulu...'));
                select.disabled = true;
                return;
            }

            select.disabled = false;
            (jurusanMap[tingkat] || []).forEach((j) => {
                const opt = document.createElement('option');
                opt.value = j.value;
                opt.textContent = j.label;
                select.appendChild(opt);
            });

            if (selectedValue && [...select.options].some((o) => o.value === selectedValue)) {
                select.value = selectedValue;
            } else if (!selectedValue) {
                select.appendChild(placeholderOption('Pilih jurusan...', true));
                select.selectedIndex = 0;
            }

            if (select.value && select.selectedIndex === -1) {
                select.selectedIndex = 0;
            }
        }

        function placeholderOption(text, isPlaceholder) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = text;
            if (isPlaceholder) {
                opt.disabled = true;
                opt.selected = true;
            }
            return opt;
        }

        function updatePreview(form) {
            const tingkat = form.querySelector('[name=tingkat]').value;
            const jurusan = form.querySelector('[name=jurusan]').value;
            const rombel = form.querySelector('[name=rombel]').value;
            const tLabel = tingkatLabels[tingkat] || '';
            const jLabel = (jurusanMap[tingkat] || []).find((j) => j.value === jurusan)?.label || '';
            const preview = form.querySelector('[data-preview]');
            preview.textContent = [tLabel, jLabel, rombel].filter(Boolean).join(' ') || 'Pilih tingkat, jurusan, dan rombel';
        }

        function bindFormEvents(form) {
            const tingkat = form.querySelector('[name=tingkat]');
            const jurusan = form.querySelector('[name=jurusan]');
            const rombel = form.querySelector('[name=rombel]');

            tingkat.addEventListener('change', () => {
                fillJurusan(jurusan, tingkat.value, '');
                updatePreview(form);
            });
            jurusan.addEventListener('change', () => updatePreview(form));
            rombel.addEventListener('input', () => updatePreview(form));

            updatePreview(form);
        }

        bindFormEvents(document.querySelector('#modal-create form'));
        bindFormEvents(document.querySelector('#modal-edit form'));

        function openEdit(data) {
            const form = document.getElementById('form-edit');
            form.action = '{{ url('kesiswaan/kelas') }}/' + data.id;
            form.querySelector('[name=tingkat]').value = data.tingkat || '';
            fillJurusan(form.querySelector('[name=jurusan]'), data.tingkat || '', data.jurusan || '');
            form.querySelector('[name=rombel]').value = data.rombel || '';
            form.querySelector('[name=tahun_ajaran_id]').value = data.tahun_ajaran_id || '';
            updatePreview(form);
            document.getElementById('modal-edit').showModal();
        }
    </script>
@endsection
