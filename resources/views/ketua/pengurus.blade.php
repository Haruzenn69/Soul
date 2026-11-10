@extends('ketua.layout')
@section('title', 'Pengurus Ekskul - ' . $ekskul->nama_ekskul)

@section('content')
<div class="space-y-6 animate-fade-up max-w-7xl mx-auto">

    {{-- HEADER & TITLE --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Pengurus Ekskul</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Struktur kepengurusan inti {{ $ekskul->nama_ekskul }}.</p>
        </div>

        <button type="button" onclick="openModalUsulkan()" class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-extrabold text-xs shadow-lg shadow-sky-300/40 dark:shadow-none transition-all flex items-center gap-2 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Usulkan Pengurus</span>
        </button>
    </div>

    {{-- HERO BANNER --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white soul-timbul shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-3xl">
            <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                Pengurus Inti {{ $ekskul->nama_ekskul }}
            </h2>
            <p class="text-[11px] sm:text-sm text-white/85 mt-2 leading-relaxed max-w-[52rem]">
                Setiap ekskul memiliki 4 jabatan pokok: <strong>Ketua</strong>, <strong>Wakil Ketua</strong>, <strong>Sekretaris</strong>, dan <strong>Bendahara</strong> (1 orang per jabatan dari anggota berstatus diterima). Sebagai Ketua, Anda dapat mengusulkan calon untuk posisi Wakil Ketua, Sekretaris, dan Bendahara kepada Pembina untuk disetujui.
            </p>
        </div>
    </div>

    {{-- 4 KARTU JABATAN POKOK --}}
    @php
        $posisiStyles = [
            'ketua' => [
                'badge' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                'accent' => 'from-amber-400 to-yellow-500',
                'icon' => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',
            ],
            'wakil_ketua' => [
                'badge' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                'accent' => 'from-sky-400 to-blue-500',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            ],
            'sekretaris' => [
                'badge' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                'accent' => 'from-emerald-400 to-teal-500',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            ],
            'bendahara' => [
                'badge' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                'accent' => 'from-indigo-400 to-purple-500',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
        ];
        $pendingByJabatan = $usulanMenunggu->keyBy('jabatan');
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($daftarSemuaJabatan as $jabatanKey => $jabatanNama)
            @php
                $pengurus = $pengurusAktif->get($jabatanKey);
                $pending = $pendingByJabatan->get($jabatanKey);
                $style = $posisiStyles[$jabatanKey] ?? $posisiStyles['ketua'];
            @endphp

            <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-lg shadow-sky-100/50 dark:shadow-none p-5 flex flex-col justify-between transition hover:shadow-xl dark:hover:border-neutral-700">
                <div>
                    {{-- HEADER JABATAN --}}
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider text-sky-700 dark:text-sky-300 bg-sky-50 dark:bg-sky-950/50 border border-sky-100 dark:border-sky-800">
                            {{ $jabatanNama }}
                        </span>

                        @if($pengurus)
                            <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Aktif
                            </span>
                        @elseif($pending)
                            <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-amber-600 dark:text-amber-400">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu
                            </span>
                        @else
                            <span class="text-[10px] font-bold text-slate-400">Kosong</span>
                        @endif
                    </div>

                    {{-- KONTEN PENGURUS AKTIF --}}
                    @if($pengurus && $pengurus->siswa)
                        <div class="text-center py-2">
                            <div class="w-20 h-20 mx-auto rounded-2xl p-1 bg-gradient-to-br {{ $style['accent'] }} shadow-md mb-3">
                                <div class="w-full h-full rounded-xl overflow-hidden bg-white dark:bg-neutral-800 flex items-center justify-center">
                                    <img src="{{ $pengurus->siswa->foto_url }}" alt="{{ $pengurus->siswa->nama }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                            <h4 class="text-sm font-extrabold text-slate-900 dark:text-white truncate" title="{{ $pengurus->siswa->nama }}">
                                {{ $pengurus->siswa->nama }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                NIS: {{ $pengurus->siswa->nis }} • {{ $pengurus->siswa->kelas?->nama ?? '-' }}
                            </p>
                            <p class="text-[10px] text-slate-400 mt-2">
                                Ditetapkan: {{ $pengurus->created_at->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    @else
                        {{-- BELUM ADA AKTIF TAPI ADA PENDING USULAN --}}
                        @if($pending && $pending->siswa)
                            <div class="text-center py-4 px-2 bg-amber-50/70 dark:bg-neutral-950 border border-amber-200 dark:border-neutral-800 rounded-2xl my-2">
                                <div class="w-12 h-12 mx-auto rounded-xl overflow-hidden border border-amber-300 dark:border-neutral-700 mb-2">
                                    <img src="{{ $pending->siswa->foto_url }}" alt="{{ $pending->siswa->nama }}" class="w-full h-full object-cover">
                                </div>
                                <span class="inline-block px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider bg-amber-200 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 mb-1">
                                    Diusulkan ke Pembina
                                </span>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $pending->siswa->nama }}</h4>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400">Diajukan: {{ $pending->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                        @else
                            {{-- BENAR-BENAR KOSONG --}}
                            <div class="text-center py-8 px-2 border-2 border-dashed border-sky-200/70 dark:border-neutral-800 bg-sky-50/30 dark:bg-neutral-950/40 rounded-2xl my-2">
                                <div class="w-12 h-12 mx-auto rounded-full bg-sky-100/60 dark:bg-neutral-800 text-sky-500 dark:text-neutral-500 flex items-center justify-center mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-slate-600 dark:text-slate-300">Belum Ada Pengurus</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Posisi {{ $jabatanNama }} kosong.</p>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- ACTION FOOTER (KHUSUS KETUA) --}}
                <div class="mt-4 pt-4 border-t border-sky-100/80 dark:border-neutral-800">
                    @if($jabatanKey === 'ketua')
                        <div class="text-center py-1.5 px-3 rounded-xl bg-sky-50/50 dark:bg-neutral-950 text-slate-500 dark:text-slate-400 text-[11px] font-semibold border border-sky-100 dark:border-neutral-800">
                            Ketua Ekskul (Anda)
                        </div>
                    @else
                        @if($pending)
                            <div class="text-center py-1.5 px-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 text-[11px] font-bold border border-amber-200 dark:border-amber-800">
                                Menunggu Verifikasi Pembina
                            </div>
                        @else
                            <button type="button" onclick="openModalUsulkanJabatan('{{ $jabatanKey }}')" class="w-full py-2 px-3 rounded-xl bg-sky-50 dark:bg-neutral-800 hover:bg-sky-100 dark:hover:bg-neutral-700 text-sky-700 dark:text-sky-300 text-xs font-bold transition text-center border border-sky-200/60 dark:border-neutral-700 shadow-xs">
                                {{ $pengurus ? 'Usulkan Calon Lain' : 'Usulkan Siswa' }}
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- RIWAYAT USULAN OLEH KETUA --}}
    @if($riwayatUsulan->isNotEmpty())
        <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 p-6 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                Riwayat Usulan Pengurus
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-sky-100 dark:border-neutral-800 text-slate-400 uppercase text-[10px]">
                            <th class="pb-3 font-semibold">Nama Siswa</th>
                            <th class="pb-3 font-semibold">Jabatan Diusulkan</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold">Tanggal</th>
                            <th class="pb-3 font-semibold">Catatan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-50 dark:divide-neutral-800">
                        @foreach($riwayatUsulan as $item)
                            <tr class="hover:bg-sky-50/40 dark:hover:bg-neutral-800/40 transition">
                                <td class="py-3 font-bold text-slate-800 dark:text-slate-200">
                                    {{ $item->siswa?->nama ?? '-' }}
                                    <span class="block text-[10px] text-slate-400 font-normal">NIS: {{ $item->siswa?->nis }} • {{ $item->siswa?->kelas?->nama ?? '-' }}</span>
                                </td>
                                <td class="py-3 font-semibold text-slate-600 dark:text-slate-300">
                                    {{ \App\Models\Pengurus::labelJabatan($item->jabatan) }}
                                </td>
                                <td class="py-3">
                                    @if($item->isAktif())
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            Disetujui & Aktif
                                        </span>
                                    @elseif($item->isDitolak())
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-neutral-800 dark:text-slate-300">
                                            {{ \App\Models\Pengurus::labelStatus($item->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-500 dark:text-slate-400">
                                    {{ $item->created_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="py-3 text-slate-500 dark:text-slate-400 italic">
                                    {{ $item->catatan ?: '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

{{-- MODAL USULKAN PENGURUS (KETUA) --}}
<div id="modal-usulkan" class="fixed inset-0 z-50 hidden bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white dark:bg-neutral-900 w-full max-w-lg rounded-3xl p-6 shadow-2xl border border-sky-100 dark:border-neutral-800 animate-fade-up">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Usulkan Pengurus Baru</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Usulan akan dikirim ke Pembina untuk diverifikasi.</p>
            </div>
            <button type="button" onclick="closeModalUsulkan()" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-neutral-800 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 flex items-center justify-center transition">
                ✕
            </button>
        </div>

        <form method="POST" action="{{ route('ketua.pengurus.usulkan') }}" class="space-y-4">
            @csrf

            <div>
                <label for="usul-jabatan" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Jabatan yang Diusulkan</label>
                <select id="usul-jabatan" name="jabatan" required class="w-full px-3 py-2.5 bg-sky-50/60 dark:bg-neutral-950 border border-sky-100 dark:border-neutral-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white dark:focus:bg-neutral-900 transition">
                    @foreach($daftarJabatanUsulan as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Ketua hanya dapat mengusulkan posisi Wakil Ketua, Sekretaris, dan Bendahara.</p>
            </div>

            <div>
                <label for="usul-siswa" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pilih Anggota Ekskul (Status Diterima)</label>
                @if($anggotaEligible->isEmpty())
                    <p class="text-xs text-rose-500 italic">Belum ada anggota berstatus diterima di ekskul ini.</p>
                @else
                    <select id="usul-siswa" name="siswa_id" required class="w-full px-3 py-2.5 bg-sky-50/60 dark:bg-neutral-950 border border-sky-100 dark:border-neutral-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white dark:focus:bg-neutral-900 transition">
                        <option value="">-- Pilih Rekan Anggota --</option>
                        @foreach($anggotaEligible as $item)
                            <option value="{{ $item->siswa_id }}">
                                {{ $item->siswa?->nama }} (NIS: {{ $item->siswa?->nis }} • {{ $item->siswa?->kelas?->nama ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                @endif
            </div>

            <div>
                <label for="usul-catatan" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan / Catatan Rekomendasi (Opsional)</label>
                <textarea id="usul-catatan" name="catatan" rows="3" placeholder="Jelaskan alasan pengusulan rekan anggota ini kepada Pembina..." class="w-full px-3 py-2 bg-sky-50/60 dark:bg-neutral-950 border border-sky-100 dark:border-neutral-800 rounded-xl text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:bg-white dark:focus:bg-neutral-900 transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModalUsulkan()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 text-xs font-bold hover:bg-slate-200 dark:hover:bg-neutral-700 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-extrabold shadow-md transition">
                    Kirim Usulan ke Pembina
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalUsulkan() {
        document.getElementById('modal-usulkan').classList.remove('hidden');
    }
    function openModalUsulkanJabatan(jabatan) {
        document.getElementById('usul-jabatan').value = jabatan;
        openModalUsulkan();
    }
    function closeModalUsulkan() {
        document.getElementById('modal-usulkan').classList.add('hidden');
    }
</script>
@endsection
