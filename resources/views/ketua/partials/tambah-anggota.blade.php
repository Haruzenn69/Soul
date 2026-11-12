    {{-- MODAL TAMBAH ANGGOTA --}}
    <dialog id="modal-tambah-anggota" aria-labelledby="modal-tambah-anggota-title" class="w-[min(56rem,96vw)] max-w-none max-h-[92dvh] rounded-3xl border border-sky-100 shadow-2xl shadow-slate-900/20 p-0 backdrop:bg-slate-950/50 backdrop:backdrop-blur-sm">
        <form id="form-tambah-anggota" method="POST" action="{{ route('ketua.anggota.store') }}" class="bg-white rounded-3xl overflow-hidden flex flex-col max-h-[92dvh]">
            @csrf
            <div class="flex items-start justify-between gap-4 px-5 sm:px-7 py-5 border-b border-slate-100">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-[.18em] text-sky-600">Kelola Keanggotaan</p>
                    <h2 id="modal-tambah-anggota-title" class="text-lg font-extrabold text-slate-900 mt-1">Tambah anggota</h2>
                    <p class="text-xs text-slate-500 mt-1">Cari berdasarkan nama, NIS, atau kelas. Pilihan tidak hilang saat kamu mengganti halaman hasil.</p>
                </div>
                <button type="button" onclick="document.getElementById('modal-tambah-anggota').close()" aria-label="Tutup" class="w-9 h-9 shrink-0 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition">
                    <span class="text-xl leading-none">&times;</span>
                </button>
            </div>

            <div class="grid md:grid-cols-[minmax(0,1fr)_17rem] min-h-0 flex-1">
                <section class="p-5 sm:p-7 min-h-0 flex flex-col">
                    <div class="grid sm:grid-cols-[minmax(0,1fr)_12rem] gap-3">
                        <div>
                            <label for="calon-siswa-search" class="text-[11px] font-bold text-slate-600 mb-2 block">Cari siswa</label>
                            <div class="relative">
                                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.3-4.3M10.8 18a7.2 7.2 0 1 1 0-14.4 7.2 7.2 0 0 1 0 14.4Z"/>
                                </svg>
                                <input id="calon-siswa-search" type="search" autocomplete="off" placeholder="Ketik nama atau NIS..."
                                       class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                            </div>
                        </div>
                        <div>
                            <label for="calon-siswa-kelas" class="text-[11px] font-bold text-slate-600 mb-2 block">Filter kelas</label>
                            <select id="calon-siswa-kelas" class="w-full py-3 px-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                                <option value="">Semua kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 mt-4 mb-2">
                        <p id="calon-siswa-meta" class="text-[11px] text-slate-500" aria-live="polite">Memuat daftar siswa...</p>
                        <button id="pilih-halaman-siswa" type="button" class="text-[11px] font-bold text-sky-700 hover:text-sky-900 disabled:text-slate-300 disabled:cursor-not-allowed">Pilih hasil ini</button>
                    </div>
                    <div id="calon-siswa-list" class="min-h-44 max-h-[45vh] md:max-h-[52vh] overflow-y-auto space-y-2 pr-1" aria-live="polite"></div>
                    <div id="calon-siswa-error" class="hidden mt-3 p-3 rounded-xl bg-rose-50 border border-rose-100 text-xs text-rose-700" role="alert"></div>
                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100">
                        <button id="halaman-sebelumnya" type="button" class="px-3 py-2 rounded-xl border border-slate-200 text-[11px] font-bold text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">Sebelumnya</button>
                        <span id="calon-siswa-page" class="text-[11px] text-slate-400"></span>
                        <button id="halaman-berikutnya" type="button" class="px-3 py-2 rounded-xl border border-slate-200 text-[11px] font-bold text-slate-600 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">Berikutnya</button>
                    </div>
                </section>

                <aside class="p-5 sm:p-6 bg-slate-50 border-t md:border-t-0 md:border-l border-slate-100 flex flex-col min-h-0">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <h3 class="text-xs font-extrabold text-slate-800">Pilihan anggota</h3>
                            <p id="jumlah-siswa-terpilih" class="text-[11px] text-slate-500 mt-1">0 siswa dipilih</p>
                        </div>
                        <button id="hapus-semua-pilihan" type="button" class="text-[10px] font-bold text-rose-600 hover:text-rose-700 disabled:text-slate-300 disabled:cursor-not-allowed">Kosongkan</button>
                    </div>
                    <div id="siswa-terpilih" class="mt-3 space-y-2 overflow-y-auto max-h-36 md:max-h-none md:flex-1"></div>
                    <div class="mt-4 p-3 rounded-2xl bg-white border border-slate-200">
                        <p class="text-[10px] leading-relaxed text-slate-500">Siswa yang pernah keluar atau dinonaktifkan bisa dipilih kembali. Status dan tanggal pendaftaran sebelumnya tetap disimpan sebagai histori.</p>
                    </div>
                </aside>
            </div>

            <div id="siswa-terpilih-inputs"></div>
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 px-5 sm:px-7 py-4 border-t border-slate-100 bg-white">
                <button type="button" onclick="document.getElementById('modal-tambah-anggota').close()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Batal</button>
                <button id="submit-tambah-anggota" type="submit" disabled class="px-5 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 disabled:from-slate-300 disabled:to-slate-300 disabled:shadow-none text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 transition disabled:cursor-not-allowed">
                    Tambahkan anggota terpilih
                </button>
            </div>
        </form>
    </dialog>

@push('scripts')
<script>
(() => {
    const dialog = document.getElementById('modal-tambah-anggota');
    const search = document.getElementById('calon-siswa-search');
    const classFilter = document.getElementById('calon-siswa-kelas');
    const list = document.getElementById('calon-siswa-list');
    const meta = document.getElementById('calon-siswa-meta');
    const pageLabel = document.getElementById('calon-siswa-page');
    const errorBox = document.getElementById('calon-siswa-error');
    const selectedList = document.getElementById('siswa-terpilih');
    const selectedInputs = document.getElementById('siswa-terpilih-inputs');
    const selectedCount = document.getElementById('jumlah-siswa-terpilih');
    const submit = document.getElementById('submit-tambah-anggota');
    const selectPage = document.getElementById('pilih-halaman-siswa');
    const clearSelection = document.getElementById('hapus-semua-pilihan');
    const previousPage = document.getElementById('halaman-sebelumnya');
    const nextPage = document.getElementById('halaman-berikutnya');
    const selected = new Map();
    const pageSize = 25;
    const selectionLimit = 100;
    let currentPage = 1;
    let lastPage = 1;
    let visibleItems = [];
    let requestId = 0;
    let debounceTimer;

    function renderSelected() {
        selectedList.replaceChildren();
        selectedInputs.replaceChildren();
        selected.forEach((siswa) => {
            const chip = document.createElement('div');
            chip.className = 'flex items-center gap-2 p-2 rounded-xl bg-white border border-sky-100';
            const label = document.createElement('span');
            label.className = 'text-[10px] font-bold text-slate-700 truncate flex-1';
            label.textContent = siswa.nama;
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'w-6 h-6 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 shrink-0';
            remove.setAttribute('aria-label', `Hapus ${siswa.nama} dari pilihan`);
            remove.textContent = '×';
            remove.addEventListener('click', () => {
                selected.delete(siswa.id);
                renderSelected();
                renderItems(visibleItems);
            });
            chip.append(label, remove);
            selectedList.append(chip);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'siswa_ids[]';
            input.value = siswa.id;
            selectedInputs.append(input);
        });

        selectedCount.textContent = `${selected.size} dari ${selectionLimit} siswa dipilih`;
        submit.disabled = selected.size === 0;
        clearSelection.disabled = selected.size === 0;
        selectPage.disabled = !visibleItems.some((siswa) => !siswa.sudah_aktif && !selected.has(siswa.id))
            || selected.size >= selectionLimit;
    }

    function renderItems(items) {
        visibleItems = items;
        list.replaceChildren();
        selectPage.disabled = !items.some((siswa) => !siswa.sudah_aktif && !selected.has(siswa.id))
            || selected.size >= selectionLimit;

        if (items.length === 0) {
            const empty = document.createElement('div');
            empty.className = 'py-12 text-center rounded-2xl border border-dashed border-slate-200';
            empty.innerHTML = '<p class="text-xs font-bold text-slate-600">Siswa tidak ditemukan</p><p class="text-[10px] text-slate-400 mt-1">Coba kata kunci atau kelas yang berbeda.</p>';
            list.append(empty);
            return;
        }

        items.forEach((siswa) => {
            const label = document.createElement('label');
            label.className = 'flex items-center gap-3 p-3 rounded-2xl border transition';
            label.classList.add(...(siswa.sudah_aktif
                ? ['border-slate-200', 'bg-slate-100', 'cursor-not-allowed', 'opacity-70']
                : selected.has(siswa.id)
                    ? ['border-sky-300', 'bg-sky-50', 'cursor-pointer']
                    : ['border-slate-100', 'bg-white', 'cursor-pointer']));

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.checked = selected.has(siswa.id);
            checkbox.disabled = siswa.sudah_aktif || (!checkbox.checked && selected.size >= selectionLimit);
            checkbox.className = 'w-4 h-4 rounded border-slate-300 text-sky-600 focus:ring-sky-300 shrink-0';
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    if (selected.size >= selectionLimit) {
                        checkbox.checked = false;
                        errorBox.textContent = `Maksimal ${selectionLimit} siswa dapat ditambahkan sekaligus.`;
                        errorBox.classList.remove('hidden');
                        return;
                    }
                    selected.set(siswa.id, siswa);
                } else {
                    selected.delete(siswa.id);
                }
                renderSelected();
                renderItems(visibleItems);
            });

            const initials = document.createElement('span');
            initials.className = siswa.sudah_aktif
                ? 'w-9 h-9 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center text-[10px] font-extrabold shrink-0'
                : 'w-9 h-9 rounded-xl bg-gradient-to-br from-sky-100 to-blue-100 text-sky-700 flex items-center justify-center text-[10px] font-extrabold shrink-0';
            initials.textContent = siswa.nama.trim().split(/\s+/).slice(0, 2).map((part) => part[0] || '').join('').toUpperCase();

            const details = document.createElement('span');
            details.className = 'min-w-0 flex-1';
            const name = document.createElement('span');
            name.className = `block text-xs font-bold truncate ${siswa.sudah_aktif ? 'text-slate-500' : 'text-slate-800'}`;
            name.textContent = siswa.nama;
            const subline = document.createElement('span');
            subline.className = 'block text-[10px] text-slate-500 mt-0.5 truncate';
            subline.textContent = `NIS ${siswa.nis || '-'} · ${siswa.kelas}`;
            details.append(name, subline);
            label.append(checkbox, initials, details);

            if (siswa.sudah_aktif) {
                const activeTag = document.createElement('span');
                activeTag.className = 'inline-flex px-2 py-1 rounded-lg bg-slate-200 text-slate-600 border border-slate-300 text-[9px] font-bold shrink-0';
                activeTag.textContent = 'Sudah aktif';
                label.append(activeTag);
            } else if (siswa.status_sebelumnya) {
                const historyTag = document.createElement('span');
                historyTag.className = 'hidden sm:inline-flex px-2 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-100 text-[9px] font-bold shrink-0';
                const statusLabel = {
                    nonaktif: 'Nonaktif',
                    keluar: 'Keluar',
                    ditolak: 'Ditolak',
                    lulus: 'Lulus',
                }[siswa.status_sebelumnya] || siswa.status_sebelumnya;
                historyTag.textContent = `${statusLabel} · ${siswa.tanggal_daftar_sebelumnya || 'Riwayat'}`;
                historyTag.title = 'Status dan tanggal pendaftaran sebelumnya';
                label.append(historyTag);
            }

            list.append(label);
        });
    }

    async function loadPage(page = 1) {
        const thisRequest = ++requestId;
        currentPage = page;
        errorBox.classList.add('hidden');
        list.innerHTML = '<div class="py-12 text-center text-xs text-slate-400">Memuat daftar siswa...</div>';

        const url = new URL(@json(route('ketua.anggota.calon-siswa')), window.location.origin);
        url.searchParams.set('page', String(page));
        if (search.value.trim()) url.searchParams.set('q', search.value.trim());
        if (classFilter.value) url.searchParams.set('kelas_id', classFilter.value);

        try {
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (!response.ok) throw new Error('Daftar siswa gagal dimuat. Muat ulang halaman lalu coba lagi.');
            const data = await response.json();
            if (thisRequest !== requestId) return;

            lastPage = data.last_page;
            renderItems(data.items);
            const start = data.total === 0 ? 0 : (data.current_page - 1) * pageSize + 1;
            const end = Math.min(data.current_page * pageSize, data.total);
            meta.textContent = search.value.trim() || classFilter.value
                ? `${start}–${end} dari ${data.total} hasil`
                : `${data.total} siswa tersedia · tampil ${start}–${end}`;
            pageLabel.textContent = `Halaman ${data.current_page} dari ${data.last_page}`;
            previousPage.disabled = data.current_page <= 1;
            nextPage.disabled = data.current_page >= data.last_page;
        } catch (error) {
            if (thisRequest !== requestId) return;
            list.replaceChildren();
            errorBox.textContent = error.message;
            errorBox.classList.remove('hidden');
            meta.textContent = 'Tidak dapat memuat daftar';
            pageLabel.textContent = '';
            previousPage.disabled = true;
            nextPage.disabled = true;
        }
    }

    dialog.addEventListener('close', () => {
        selected.clear();
        renderSelected();
        search.value = '';
        classFilter.value = '';
        currentPage = 1;
    });
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) dialog.close();
    });
    window.openAddMemberDialog = () => {
        dialog.showModal();
        loadPage();
        search.focus();
    };
    search.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => loadPage(1), 250);
    });
    classFilter.addEventListener('change', () => loadPage(1));
    selectPage.addEventListener('click', () => {
        for (const siswa of visibleItems) {
            if (siswa.sudah_aktif) continue;
            if (selected.size >= selectionLimit) break;
            selected.set(siswa.id, siswa);
        }
        renderSelected();
        renderItems(visibleItems);
    });
    clearSelection.addEventListener('click', () => {
        selected.clear();
        renderSelected();
        renderItems(visibleItems);
    });
    previousPage.addEventListener('click', () => loadPage(Math.max(1, currentPage - 1)));
    nextPage.addEventListener('click', () => loadPage(Math.min(lastPage, currentPage + 1)));
    document.getElementById('form-tambah-anggota').addEventListener('submit', (event) => {
        if (selected.size === 0) event.preventDefault();
    });

    renderSelected();
})();
</script>
@endpush
