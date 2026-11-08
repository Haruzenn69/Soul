@extends('layouts.kesiswaan')

@section('title', 'Data Ekskul')

@section('content')
<div class="space-y-5 animate-fade-up">

    {{-- HERO CARD BIRU --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white soul-timbul">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                    Data Ekskul
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Pilih kategori untuk melihat dan mengelola ekstrakurikuler, anggota, pelatih, serta pembinanya.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <button onclick="document.getElementById('modal-kategori').showModal()"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/15 hover:bg-white/25 text-white font-bold text-xs rounded-2xl border border-white/25 shadow-sm backdrop-blur-sm hover:-translate-y-0.5 transition-all">
                    
                    Kelola Kategori
                </button>
                <button onclick="document.getElementById('modal-create').showModal()"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    
                    Tambah Ekskul
                </button>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section aria-labelledby="kategori-heading" class="space-y-4">
        <div class="flex items-end justify-between gap-4">
            <div>
                <h2 id="kategori-heading" class="text-lg font-extrabold text-slate-900">Pilih kategori</h2>
                <p class="mt-1 text-xs text-slate-500">Buka kategori untuk melihat dan mengelola daftar ekskulnya.</p>
            </div>
            <span class="shrink-0 text-xs font-semibold text-slate-400">{{ $kategoriModels->count() }} kategori</span>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($kategoriModels as $kategori)
                <a href="{{ route('kesiswaan.ekskuls.by-kategori', $kategori->nama) }}"
                   class="group flex min-h-36 items-center justify-between gap-4 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2">
                    <div class="min-w-0">
                        <h3 class="truncate text-base font-extrabold text-slate-900 group-hover:text-sky-700">{{ $kategori->nama }}</h3>
                        <p class="mt-1 text-xs text-slate-500">{{ $kategori->ekskuls_count }} ekskul</p>
                    </div>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700 transition group-hover:bg-sky-600 group-hover:text-white" aria-hidden="true">
                        <x-icon name="arrow-right" class="h-5 w-5" />
                    </span>
                </a>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center">
                    <p class="text-sm font-bold text-slate-700">Belum ada kategori ekskul.</p>
                    <p class="mt-1 text-xs text-slate-500">Tambahkan kategori untuk mulai mengelompokkan ekskul.</p>
                </div>
            @endforelse
        </div>
    </section>
    <dialog id="modal-create" class="rounded-3xl backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm p-0 w-full max-w-md shadow-2xl border border-sky-100">
        <form method="POST" action="{{ route('kesiswaan.ekskuls.store') }}" class="p-6 md:p-8 space-y-4 bg-white">
            @csrf
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Tambah Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Buat ekstrakurikuler baru</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">Tutup</button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Nama Ekskul <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_ekskul" placeholder="Contoh: Paskibra, OSIS, Pramuka..." required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                <p class="text-[10px] text-slate-400 mt-1">Pembina dapat ditentukan di halaman <a href="{{ route('kesiswaan.pembina.index') }}" class="text-sky-600 font-semibold hover:underline">Data Pembina</a>.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Kategori <span class="text-rose-500">*</span></label>
                <select name="kategori" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="" disabled {{ old('kategori') ? '' : 'selected' }}>Pilih kategori...</option>
                    @foreach ($kategoriList ?? \App\Support\AturanEkskul::KATEGORI_LIST as $kategori)
                        <option value="{{ $kategori }}" @selected(old('kategori') === $kategori)>{{ $kategori }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="this.closest('dialog').close()"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                    Simpan Ekskul
                </button>
            </div>
        </form>
    </dialog>

    {{-- MODAL EDIT --}}
    <dialog id="modal-edit" class="rounded-3xl backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm p-0 w-full max-w-md shadow-2xl border border-sky-100">
        <form id="form-edit" method="POST" class="p-6 md:p-8 space-y-4 bg-white">
            @csrf
            @method('PUT')
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Edit Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Perbarui data ekstrakurikuler</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">Tutup</button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Nama Ekskul <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_ekskul" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Status Ekskul <span class="text-rose-500">*</span></label>
                <select name="status" id="edit_status" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Ekskul nonaktif akan dipindah ke paling bawah di kategorinya dan berwarna abu-abu.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Kategori <span class="text-rose-500">*</span></label>
                {{-- Jika ekskul Wajib: tampilkan info locked (diisi via JS) --}}
                <div id="edit_kategori_locked" class="hidden w-full px-4 py-2.5 bg-sky-50 border border-sky-200 rounded-2xl text-xs text-sky-800 font-bold flex items-center gap-2">
                    <span id="edit_kategori_locked_text">Wajib</span>
                    <span class="font-normal text-sky-600 ml-1">&mdash; Kategori ekskul wajib tidak dapat diubah</span>
                    <input type="hidden" name="kategori" id="edit_kategori_hidden" value="Wajib">
                </div>
                {{-- Untuk ekskul non-Wajib: dropdown biasa, tanpa opsi Wajib --}}
                <select name="kategori" id="edit_kategori" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="" disabled>Pilih kategori...</option>
                    @foreach ($kategoriList ?? \App\Support\AturanEkskul::KATEGORI_LIST as $kategori)
                        @if (!\App\Support\AturanEkskul::isWajib($kategori))
                            <option value="{{ $kategori }}">{{ $kategori }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Deskripsi</label>
                <textarea name="deskripsi" rows="2"
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Jadwal</label>
                <input type="text" name="jadwal"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div class="flex gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="this.closest('dialog').close()"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </dialog>

    {{-- MODAL KELOLA KATEGORI --}}
    <dialog id="modal-kategori" class="rounded-3xl backdrop:bg-slate-900/40 backdrop:backdrop-blur-sm p-0 w-full max-w-lg shadow-2xl border border-sky-100">
        <div class="p-6 md:p-8 bg-white space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">Kelola Kategori Ekskul</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Tambah dan hapus kategori ekstrakurikuler</p>
                    </div>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">Tutup</button>
            </div>

            {{-- FORM TAMBAH KATEGORI --}}
            <form method="POST" action="{{ route('kesiswaan.kategori.store') }}" class="p-4 bg-sky-50/70 border border-sky-100 rounded-2xl space-y-3">
                @csrf
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-sky-900 uppercase tracking-wider">Tambah Kategori Baru</h3>
                </div>
                <div class="flex gap-2">
                    <input type="text" name="nama" required placeholder="Contoh: Robotik, Keagamaan, ..."
                           class="flex-1 px-3.5 py-2.5 bg-white border border-sky-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100">
                    <button type="submit"
                            class="px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm shadow-sky-200 transition shrink-0">
                        + Tambah
                    </button>
                </div>
            </form>

            {{-- DAFTAR KATEGORI SAAT INI --}}
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Daftar Kategori Saat Ini</h3>
                <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 border border-slate-100 rounded-2xl">
                    @forelse ($kategoriModels ?? [] as $kat)
                        <div class="p-3 flex items-center justify-between gap-3 hover:bg-slate-50 transition">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="font-bold text-xs text-slate-800 truncate">{{ $kat->nama }}</span>
                                @if ($kat->isFallback())
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200" title="Kategori fallback â€” ekskul yang kategorinya dihapus akan dipindah ke sini">
                                        Sistem Â· Fallback
                                    </span>
                                @endif
                                <span class="text-[10px] text-slate-400">
                                    ({{ $kat->ekskuls_count }} ekskul)
                                </span>
                            </div>
                            @if ($kat->isFallback())
                                <span class="text-[10px] text-slate-400 italic font-medium px-2 py-1">Tidak bisa dihapus</span>
                            @else
                                <form method="POST" action="{{ route('kesiswaan.kategori.destroy', $kat) }}"
                                      onsubmit="return confirm('Hapus kategori \'{{ $kat->nama }}\'? {{ $kat->ekskuls_count > 0 ? $kat->ekskuls_count . ' ekskul pada kategori ini akan otomatis dipindah ke kategori Lainnya.' : 'Tidak ada ekskul yang terdampak.' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-[11px] font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400">Belum ada kategori terdaftar.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </dialog>

</div>

<script>
    function openEdit(data) {
        const form = document.getElementById('form-edit');
        form.action = '{{ url('kesiswaan/ekskuls') }}/' + data.id;
        form.querySelector('[name=nama_ekskul]').value = data.nama_ekskul || '';

        // Penanganan status ekskul
        const selStatus = document.getElementById('edit_status');
        if (selStatus) {
            selStatus.value = (data.status !== undefined && data.status !== null) ? String(data.status) : '1';
        }

        // Penanganan dropdown kategori
        const selKategori   = document.getElementById('edit_kategori');
        const lockedBox     = document.getElementById('edit_kategori_locked');
        const lockedText    = document.getElementById('edit_kategori_locked_text');
        const hiddenKategori= document.getElementById('edit_kategori_hidden');
        const isWajib       = (data.kategori || '').toLowerCase() === 'wajib';

        if (isWajib) {
            // Ekskul Wajib: sembunyikan dropdown, tampilkan info locked
            selKategori.classList.add('hidden');
            selKategori.removeAttribute('name');      // jangan ikut submit
            selKategori.removeAttribute('required');
            lockedBox.classList.remove('hidden');
            lockedText.textContent = data.kategori;
            hiddenKategori.value = data.kategori;     // yang dikirim ke server
        } else {
            // Ekskul non-Wajib: tampilkan dropdown, sembunyikan locked
            selKategori.classList.remove('hidden');
            selKategori.setAttribute('name', 'kategori');
            selKategori.setAttribute('required', 'required');
            lockedBox.classList.add('hidden');
            hiddenKategori.value = '';
            selKategori.value = data.kategori || '';
        }

        form.querySelector('[name=deskripsi]').value = data.deskripsi || '';
        form.querySelector('[name=jadwal]').value = data.jadwal || '';
        document.getElementById('modal-edit').showModal();
    }

    // Auto-open modal create jika ada validation error
    @if ($errors->any() && old('nama_ekskul'))
        document.getElementById('modal-create').showModal();
    @endif
</script>
@endsection
