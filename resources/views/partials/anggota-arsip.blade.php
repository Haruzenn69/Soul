@php
    /**
     * Arsip anggota & ketua sebelumnya.
     *
     * Bagian ini hanya dirender saat filter arsip aktif (`?arsip=1`), sehingga
     * data lama tidak mengganggu daftar utama.
     *
     * @var array $arsip            ['ketuaSelesai', 'anggotaNonaktif', 'total']
     * @var bool  $tampilArsip
     * @var string $arsipAction     URL daftar anggota (tanpa parameter arsip)
     * @var bool  $arsipShowEkskul  Tampilkan kolom ekskul (dipakai modul pembina)
     * @var string|null $arsipPeriodeRoute Route update periode, null = tidak bisa diedit
     */
    $arsipShowEkskul = $arsipShowEkskul ?? false;
    $arsipPeriodeRoute = $arsipPeriodeRoute ?? null;
    $arsipTab = request('arsip_tab') === 'anggota' ? 'anggota' : 'ketua';
    $arsipUrl = fn (string $tab) => $arsipAction.'?'.http_build_query(
        array_merge(request()->query(), ['arsip' => 1, 'arsip_tab' => $tab])
    );

    $arsipKetua = $arsip['ketuaSelesai'] ?? collect();
    $arsipAnggota = $arsip['anggotaNonaktif'] ?? collect();

    $arsipTabs = [
        'ketua' => ['label' => 'Ketua Sebelumnya', 'icon' => 'star', 'count' => $arsipKetua->count()],
        'anggota' => ['label' => 'Anggota Nonaktif & Keluar', 'icon' => 'user-off', 'count' => $arsipAnggota->count()],
    ];
@endphp

<div class="bg-white rounded-3xl border border-amber-100 shadow-sm overflow-hidden animate-fade-up">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl bg-amber-100 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8v10m0-3h10a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </span>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Arsip Anggota &amp; Ketua Sebelumnya</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    {{ $arsip['total'] ?? 0 }} data arsip — riwayat anggota dan ketua yang sudah tidak aktif lagi.
                </p>
            </div>
        </div>
        <a href="{{ $arsipAction }}"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[11px] font-bold rounded-xl transition self-start">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4l4 16m8-16l-4 16M4 9h16M3 19h18"/>
            </svg>
            Sembunyikan Arsip
        </a>
    </div>

    {{-- SUB-TAB ARSIP --}}
    <div class="px-5 pt-4 flex flex-wrap gap-2">
        @foreach ($arsipTabs as $key => $tab)
            <a href="{{ $arsipUrl($key) }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-[11px] font-bold transition border
                    {{ $arsipTab === $key
                        ? 'bg-amber-500 text-white border-amber-500 shadow-sm shadow-amber-200'
                        : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                @if ($tab['icon'] === 'star')
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M2 19l2-9 5 5 3-8 3 8 5-5 2 9H2z"/></svg>
                @else
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.36 18.36A9 9 0 005.64 5.64m12.72 12.72A9 9 0 015.64 5.64m12.72 12.72L5.64 5.64"/>
                    </svg>
                @endif
                {{ $tab['label'] }}
                <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold
                    {{ $arsipTab === $key ? 'bg-white/25 text-white' : 'bg-white text-slate-500 border border-slate-200' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    <div class="p-5">
        @if ($arsipTab === 'ketua')
            {{-- ARSIP: KETUA YANG PERIODENYA SUDAH SELESAI --}}
            @if ($arsipKetua->isEmpty())
                <div class="text-center py-10 text-slate-400">
                    <p class="text-xs font-semibold">Belum ada riwayat Ketua sebelumnya.</p>
                    <p class="text-[11px] mt-1">Arsip terisi otomatis saat seorang ketua digantikan atau dicopot.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="card-table w-full text-xs">
                        <thead>
                            <tr class="bg-amber-50/60 text-slate-500 border-b border-amber-100">
                                <th class="p-3.5 font-bold rounded-l-2xl text-left">No</th>
                                <th class="p-3.5 font-bold text-left">Identitas</th>
                                @if ($arsipShowEkskul)
                                    <th class="p-3.5 font-bold text-left">Ekskul</th>
                                @endif
                                <th class="p-3.5 font-bold text-left">Periode Ketua</th>
                                <th class="p-3.5 font-bold text-left">Durasi</th>
                                <th class="p-3.5 font-bold text-left">Berakhir Karena</th>
                                @if ($arsipPeriodeRoute)
                                    <th class="p-3.5 font-bold text-center rounded-r-2xl">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($arsipKetua as $key => $periode)
                                <tr class="hover:bg-amber-50/30 transition">
                                    <td class="p-3.5 text-slate-400 font-semibold">{{ $key + 1 }}</td>
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0 flex items-center justify-center font-bold text-xs bg-amber-100 text-amber-800 border-2 border-amber-300">
                                                @if ($periode->siswa?->foto_url)
                                                    <img src="{{ $periode->siswa->foto_url }}" alt="{{ $periode->siswa->nama }}" class="w-full h-full object-cover">
                                                @else
                                                    <span>{{ strtoupper(substr($periode->siswa->nama ?? 'S', 0, 2)) }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="font-extrabold text-slate-900 block">{{ $periode->siswa->nama ?? '-' }}</span>
                                                <span class="text-[11px] text-slate-400">NIS: {{ $periode->siswa->nis ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    @if ($arsipShowEkskul)
                                        <td class="p-3.5">
                                            <span class="px-2.5 py-1 rounded-xl bg-sky-50 text-sky-700 font-semibold text-[11px] border border-sky-100">
                                                {{ $periode->ekskul->nama_ekskul ?? '-' }}
                                            </span>
                                        </td>
                                    @endif
                                    <td class="p-3.5">
                                        <span class="px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 font-bold text-[11px] border border-amber-200">
                                            {{ $periode->periodeText() }}
                                        </span>
                                        <span class="block text-[10px] text-slate-400 mt-1">{{ $periode->mulai->isoFormat('DD MMM YYYY') }} &ndash; {{ $periode->selesai->isoFormat('DD MMM YYYY') }}</span>
                                    </td>
                                    <td class="p-3.5 text-slate-600 font-semibold whitespace-nowrap">{{ $periode->durasiHari() }} hari</td>
                                    <td class="p-3.5">
                                        @php $alasan = $periode->alasan_selesai; @endphp
                                        @if ($alasan === \App\Models\RiwayatJabatan::ALASAN_DIGANTI)
                                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">Digantikan</span>
                                        @elseif ($alasan === \App\Models\RiwayatJabatan::ALASAN_DICOPOT)
                                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">Jabatan Dicopot</span>
                                        @elseif ($alasan === \App\Models\RiwayatJabatan::ALASAN_KELUAR)
                                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-violet-50 text-violet-700 border border-violet-200">Anggota Keluar</span>
                                        @else
                                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Periode Berakhir</span>
                                        @endif
                                    </td>
                                    @if ($arsipPeriodeRoute)
                                        <td class="p-3.5 text-center">
                                            <button type="button" onclick="document.getElementById('periode-{{ $periode->id }}').showModal()"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-[11px] rounded-xl border border-amber-200 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Koreksi Periode
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            {{-- ARSIP: ANGGOTA YANG SUDAH NONAKTIF / KELUAR --}}
            @if ($arsipAnggota->isEmpty())
                <div class="text-center py-10 text-slate-400">
                    <p class="text-xs font-semibold">Belum ada anggota nonaktif atau keluar.</p>
                    <p class="text-[11px] mt-1">Anggota yang dinonaktifkan atau mengajukan keluar akan muncul di sini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="card-table w-full text-xs">
                        <thead>
                            <tr class="bg-amber-50/60 text-slate-500 border-b border-amber-100">
                                <th class="p-3.5 font-bold rounded-l-2xl text-left">No</th>
                                <th class="p-3.5 font-bold text-left">Identitas</th>
                                <th class="p-3.5 font-bold text-left">Kelas</th>
                                @if ($arsipShowEkskul)
                                    <th class="p-3.5 font-bold text-left">Ekskul</th>
                                @endif
                                <th class="p-3.5 font-bold text-left">Bergabung</th>
                                <th class="p-3.5 font-bold text-left">Status Terakhir</th>
                                <th class="p-3.5 font-bold text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($arsipAnggota as $key => $item)
                                @php $pernahKetua = $item->siswa?->selesaiSebagaiKetua($item->ekskul_id); @endphp
                                <tr class="hover:bg-amber-50/30 transition opacity-90">
                                    <td class="p-3.5 text-slate-400 font-semibold">{{ $key + 1 }}</td>
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0 flex items-center justify-center font-bold text-xs bg-slate-100 text-slate-600">
                                                @if ($item->siswa?->foto_url)
                                                    <img src="{{ $item->siswa->foto_url }}" alt="{{ $item->siswa->nama }}" class="w-full h-full object-cover">
                                                @else
                                                    <span>{{ strtoupper(substr($item->siswa->nama ?? 'S', 0, 2)) }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="font-extrabold text-slate-900 block">
                                                    {{ $item->siswa->nama ?? '-' }}
                                                    @if ($pernahKetua)
                                                        <span class="ml-1 px-1.5 py-0.5 rounded-md bg-amber-100 text-amber-700 border border-amber-200 text-[9px] font-bold">ex-Ketua</span>
                                                    @endif
                                                </span>
                                                <span class="text-[11px] text-slate-400">NIS: {{ $item->siswa->nis ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3.5 font-semibold text-slate-700">{{ $item->siswa->kelas->nama ?? '-' }}</td>
                                    @if ($arsipShowEkskul)
                                        <td class="p-3.5">
                                            <span class="px-2.5 py-1 rounded-xl bg-sky-50 text-sky-700 font-semibold text-[11px] border border-sky-100">
                                                {{ $item->ekskul->nama_ekskul ?? '-' }}
                                            </span>
                                        </td>
                                    @endif
                                    <td class="p-3.5 text-slate-400 font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_daftar)->isoFormat('DD MMM Y') }}</td>
                                    <td class="p-3.5">
                                        @if ($item->status === \App\Models\Pendaftaran::STATUS_NONAKTIF)
                                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Nonaktif</span>
                                        @else
                                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">Keluar</span>
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-slate-500 text-[11px]">
                                        {{ $item->alasan ?: '—' }}
                                        @if ($pernahKetua)
                                            <span class="block text-[10px] text-amber-600/90 font-semibold mt-0.5">
                                                Pernah ketua {{ $pernahKetua->periodeText() }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif
    </div>
</div>

{{-- MODAL KOREKSI PERIODE --}}
@if ($arsipPeriodeRoute)
    @foreach ($arsipKetua as $periode)
        <dialog id="periode-{{ $periode->id }}" class="p-0 m-auto w-[calc(100%-2rem)] max-w-md rounded-3xl backdrop:bg-slate-900/40">
            <form method="POST" action="{{ route($arsipPeriodeRoute, $periode) }}" class="bg-white rounded-3xl overflow-hidden">
                @csrf
                @method('PATCH')
                <div class="p-5 border-b border-slate-100">
                    <h4 class="text-sm font-extrabold text-slate-900">Koreksi Periode Ketua</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        {{ $periode->siswa->nama ?? '-' }} &middot; {{ $periode->ekskul->nama_ekskul ?? '-' }}
                    </p>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-[11px] font-bold text-slate-600 block mb-1.5">Mulai</span>
                            <input type="date" name="mulai" value="{{ $periode->mulai?->format('Y-m-d') }}" required
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition-all">
                        </label>
                        <label class="block">
                            <span class="text-[11px] font-bold text-slate-600 block mb-1.5">Selesai</span>
                            <input type="date" name="selesai" value="{{ $periode->selesai?->format('Y-m-d') }}"
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition-all">
                        </label>
                    </div>
                    @error('mulai')
                        <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                    @error('selesai')
                        <p class="text-[11px] text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
                <div class="p-5 border-t border-slate-100 flex gap-2 justify-end">
                    <button type="button" onclick="document.getElementById('periode-{{ $periode->id }}').close()"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold rounded-xl transition shadow-sm shadow-amber-200">Simpan</button>
                </div>
            </form>
        </dialog>
    @endforeach
@endif