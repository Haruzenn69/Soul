@extends('layouts.kesiswaan')

@section('title', 'Data Ekskul')

@section('content')
@php
    $hasFilter = request()->filled(['q', 'status']);
@endphp

<div class="space-y-5 animate-fade-up">

    {{-- HERO CARD BIRU --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
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
                    Kelola data ekstrakurikuler, lihat anggota, pelatih, dan pembinanya. Gunakan pencarian dan filter untuk menemukan ekskul yang diinginkan.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <div class="px-4 py-2.5 bg-white/15 backdrop-blur border border-white/20 rounded-2xl text-center">
                    <div class="text-lg font-black leading-none">{{ $ekskuls->total() }}</div>
                    <div class="text-[10px] font-bold text-white/80 uppercase tracking-wider mt-0.5">Total Ekskul</div>
                </div>

                <button onclick="document.getElementById('modal-create').showModal()"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Ekskul
                </button>
            </div>
        </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-2xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SEARCH & FILTER --}}
    <form method="GET" action="{{ route('kesiswaan.ekskuls.index') }}" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm">
        <div class="flex flex-col lg:flex-row gap-3 items-stretch">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama ekskul atau pembina..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <select name="status" class="w-full lg:w-48 px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <select name="rekrutmen" class="w-full lg:w-52 px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                <option value="">Semua Rekrutmen</option>
                <option value="buka" {{ request('rekrutmen') === 'buka' ? 'selected' : '' }}>Buka Pendaftaran</option>
                <option value="tutup" {{ request('rekrutmen') === 'tutup' ? 'selected' : '' }}>Tutup Pendaftaran</option>
            </select>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Filter
                </button>
                @if ($hasFilter)
                    <a href="{{ route('kesiswaan.ekskuls.index') }}"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- TABEL EKSKUL --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">Ekskul</th>
                        <th class="py-3.5 px-5">Pembina</th>
                        <th class="py-3.5 px-5">Pelatih</th>
                        <th class="py-3.5 px-5">Jadwal</th>
                        <th class="py-3.5 px-5">Rekrutmen</th>
                        <th class="py-3.5 px-5">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($ekskuls as $ekskul)
                        <tr class="hover:bg-sky-50/30 transition">
                            {{-- Ekskul --}}
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0">
                                        @if ($ekskul->logo)
                                            <img src="{{ asset('storage/' . $ekskul->logo) }}" alt="{{ $ekskul->nama_ekskul }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-sky-100 text-sky-700 font-extrabold flex items-center justify-center text-xs uppercase">
                                                {{ strtoupper(substr($ekskul->nama_ekskul, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 leading-snug truncate">{{ $ekskul->nama_ekskul }}</div>
                                        <div class="text-[11px] text-slate-400 truncate line-clamp-1">{{ $ekskul->deskripsi ?? 'Tanpa deskripsi' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Pembina --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if ($ekskul->pembina)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-[10px] shrink-0 uppercase">
                                            {{ strtoupper(substr($ekskul->pembina->nama, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-slate-700">{{ $ekskul->pembina->nama }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            {{-- Pelatih --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                @if ($ekskul->pelatih)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 font-bold flex items-center justify-center text-[10px] shrink-0 uppercase">
                                            {{ strtoupper(substr($ekskul->pelatih->nama, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-slate-700">{{ $ekskul->pelatih->nama }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            {{-- Jadwal --}}
                            <td class="py-3.5 px-5 whitespace-nowrap text-slate-600">
                                {{ $ekskul->jadwal ?? '-' }}
                            </td>

                            {{-- Rekrutmen --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] {{ $ekskul->is_open_recruitment ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $ekskul->is_open_recruitment ? '● Buka' : '○ Tutup' }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] {{ $ekskul->status ? 'bg-sky-50 text-sky-700 border border-sky-100' : 'bg-rose-50 text-rose-500 border border-rose-100' }}">
                                    {{ $ekskul->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="flex gap-2 justify-end items-center">
                                    {{-- Lihat Detail --}}
                                    <a href="{{ route('kesiswaan.ekskuls.show', $ekskul) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-sm shadow-sky-200 transition"
                                       title="Lihat Detail Ekskul">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </a>

                                    {{-- Edit --}}
                                    <button type="button"
                                            onclick='openEdit({{ json_encode(["id" => $ekskul->id, "nama_ekskul" => $ekskul->nama_ekskul, "pembina_id" => $ekskul->pembina_id, "deskripsi" => $ekskul->deskripsi, "jadwal" => $ekskul->jadwal]) }})'
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>

                                    {{-- Toggle Status --}}
                                    <form action="{{ route('kesiswaan.ekskuls.destroy', $ekskul) }}" method="POST"
                                          onsubmit="return confirm('{{ $ekskul->status ? 'Nonaktifkan ekskul ' . $ekskul->nama_ekskul . '?' : 'Aktifkan ekskul ' . $ekskul->nama_ekskul . '?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 {{ $ekskul->status ? 'bg-rose-50 hover:bg-rose-100 text-rose-600' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600' }} font-semibold rounded-xl text-xs transition">
                                            {{ $ekskul->status ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Belum ada ekskul yang ditemukan.</p>
                                    @if ($hasFilter)
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah kata kunci atau hapus filter.</p>
                                        <a href="{{ route('kesiswaan.ekskuls.index') }}" class="mt-3 text-xs font-bold text-sky-600 hover:text-sky-700">
                                            Tampilkan semua ekskul
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-2 justify-between items-center">
            <p class="text-[11px] text-slate-400 font-semibold">
                Menampilkan {{ $ekskuls->firstItem() ?? 0 }}-{{ $ekskuls->lastItem() ?? 0 }} dari {{ $ekskuls->total() }} ekskul
            </p>
            {{ $ekskuls->links() }}
        </div>
    </div>

    {{-- MODAL CREATE --}}
    <dialog id="modal-create" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-md shadow-2xl border border-sky-100">
        <form method="POST" action="{{ route('kesiswaan.ekskuls.store') }}" class="p-6 md:p-8 space-y-4 bg-white">
            @csrf
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Tambah Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Buat ekstrakurikuler baru</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Nama Ekskul <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_ekskul" placeholder="Contoh: Paskibra, OSIS, Pramuka..." required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                <p class="text-[10px] text-slate-400 mt-1">Pembina dapat ditentukan di halaman <a href="{{ route('kesiswaan.pembina.index') }}" class="text-sky-600 font-semibold hover:underline">Data Pembina</a>.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Deskripsi <span class="text-slate-300">(Opsional)</span></label>
                <textarea name="deskripsi" rows="2" placeholder="Deskripsi singkat ekskul..."
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Jadwal <span class="text-slate-300">(Opsional)</span></label>
                <input type="text" name="jadwal" placeholder="Contoh: Senin & Rabu, 15:30–17:00"
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
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
    <dialog id="modal-edit" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-md shadow-2xl border border-sky-100">
        <form id="form-edit" method="POST" class="p-6 md:p-8 space-y-4 bg-white">
            @csrf
            @method('PUT')
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Edit Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Perbarui data ekstrakurikuler</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Nama Ekskul <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_ekskul" required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase tracking-wide">Pembina <span class="text-rose-500">*</span></label>
                <select name="pembina_id" id="edit_pembina_id" required
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="" disabled>Pilih pembina...</option>
                    @foreach ($pembinas as $p)
                        <option value="{{ $p->id }}" data-count="{{ $p->ekskuls_count }}">
                            {{ $p->nama }} ({{ $p->ekskuls_count }}/4)
                        </option>
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

</div>

<script>
    function openEdit(data) {
        const form = document.getElementById('form-edit');
        form.action = '{{ url('kesiswaan/ekskuls') }}/' + data.id;
        form.querySelector('[name=nama_ekskul]').value = data.nama_ekskul || '';
        const selPembina = form.querySelector('[name=pembina_id]');
        selPembina.value = data.pembina_id || '';
        Array.from(selPembina.options).forEach(opt => {
            const count = parseInt(opt.getAttribute('data-count') || '0', 10);
            if (opt.value != data.pembina_id && count >= 4) {
                opt.disabled = true;
                if (!opt.text.includes('– Penuh')) opt.text += ' – Penuh';
            } else {
                opt.disabled = false;
                opt.text = opt.text.replace(' – Penuh', '');
            }
        });
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
