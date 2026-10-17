@extends('pembina.layout')
@section('title', 'Pilih Ketua Ekskul - ' . $ekskul->nama_ekskul)

@section('content')
<div class="space-y-6 animate-fade-up max-w-7xl mx-auto">

    {{-- Breadcrumb & Back Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="{{ route('pembina.anggota', ['ekskul' => $ekskul->id]) }}"
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-sky-600 transition group">
            <div class="w-8 h-8 rounded-xl bg-white border border-sky-100 flex items-center justify-center text-slate-500 group-hover:bg-sky-50 group-hover:border-sky-200 transition shadow-xs">
                <svg class="w-4 h-4 group-hover:-translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </div>
            <span>Kembali ke Data Anggota</span>
        </a>

        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M2 19l2-9 5 5 3-8 3 8 5-5 2 9H2z"/>
                </svg>
                <span>{{ $ekskul->nama_ekskul }}</span>
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-2xl bg-sky-50 border border-sky-100 text-sky-700 text-xs font-bold">
                <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>{{ $anggota->count() }} Anggota Aktif</span>
            </span>
        </div>
    </div>

    {{-- HERO BANNER --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-500 via-amber-600 to-yellow-600 p-6 md:p-8 text-white shadow-xl shadow-amber-200/50">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-[11px] font-extrabold uppercase tracking-wider text-white mb-3 shadow-xs">
                    <svg class="w-3.5 h-3.5 text-amber-200" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2 19l2-9 5 5 3-8 3 8 5-5 2 9H2z"/>
                    </svg>
                    Penunjukan Ketua Ekskul
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">
                    Pilih Ketua Ekskul {{ $ekskul->nama_ekskul }}
                </h1>
                <p class="text-xs md:text-sm text-white/90 mt-2 leading-relaxed">
                    Silakan pilih salah satu siswa dari daftar anggota aktif di bawah ini untuk ditetapkan sebagai <strong>Ketua Ekskul</strong>. Ketua yang terpilih akan memperoleh hak akses menu Ketua Ekskul untuk mengelola kegiatan, presensi, dan laporan bulanan.
                </p>
            </div>

            @if($currentKetua)
                <div class="bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl p-4 text-white shrink-0 min-w-[260px] shadow-sm">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-200 block mb-2">Ketua Saat Ini</span>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 border-2 border-white/40 overflow-hidden flex items-center justify-center shrink-0 shadow-xs">
                            @if($currentKetua->foto_url)
                                <img src="{{ $currentKetua->foto_url }}" alt="{{ $currentKetua->nama }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm font-black text-white uppercase">
                                    {{ strtoupper(substr($currentKetua->nama, 0, 2)) }}
                                </span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-extrabold truncate text-white" title="{{ $currentKetua->nama }}">{{ $currentKetua->nama }}</h4>
                            <p class="text-[11px] text-white/80">NIS: {{ $currentKetua->nis }}</p>
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md bg-emerald-500/80 text-[10px] font-bold text-white">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Sedang Menjabat
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ALERT INFO JIKA BELUM ADA KETUA --}}
    @if(!$currentKetua)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 flex items-start gap-3 text-amber-800 text-xs">
            <div class="w-8 h-8 rounded-xl bg-amber-100 border border-amber-300 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-slate-900">Ekskul ini belum memiliki Ketua aktif</p>
                <p class="text-[11px] text-slate-600 mt-0.5">
                    Klik tombol <strong>"Pilih Ketua"</strong> di sebelah nama siswa yang Anda pilih di tabel bawah untuk mengangkatnya menjadi Ketua Ekskul {{ $ekskul->nama_ekskul }}.
                </p>
            </div>
        </div>
    @else
        <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 flex items-start gap-3 text-sky-800 text-xs">
            <div class="w-8 h-8 rounded-xl bg-sky-100 border border-sky-300 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-slate-900">Pergantian Ketua</p>
                <p class="text-[11px] text-slate-600 mt-0.5">
                    Jika Anda memilih siswa baru di bawah, jabatan <strong>{{ $currentKetua->nama }}</strong> akan dialihkan kembali menjadi anggota biasa, dan riwayat kepengurusannya akan tersimpan rapi di arsip.
                </p>
            </div>
        </div>
    @endif

    {{-- SEARCH & FILTER BAR --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="relative w-full sm:w-80">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <input type="text"
                   id="search-anggota-input"
                   placeholder="Cari nama atau NIS siswa..."
                   value="{{ $cari }}"
                   class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition-all">
        </div>

        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium self-end sm:self-auto">
            <span>Daftar Anggota Ekskul:</span>
            <span class="font-bold text-slate-800 px-2 py-0.5 rounded-lg bg-slate-100 border border-slate-200">
                <span id="count-visible">{{ $anggota->count() }}</span> dari {{ $anggota->count() }} siswa
            </span>
        </div>
    </div>

    {{-- TABEL DAFTAR ANGGOTA & TOMBOL PILIH KETUA --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        @if($anggota->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="card-table w-full text-xs text-left">
                    <thead class="bg-slate-50/80 text-slate-500 font-bold border-b border-slate-100 uppercase text-[10px] tracking-wider">
                        <tr>
                            <th class="p-3.5 text-center w-12">No</th>
                            <th class="p-3.5">Nama Anggota</th>
                            <th class="p-3.5">Kelas & Jurusan</th>
                            <th class="p-3.5 text-center">Kehadiran</th>
                            <th class="p-3.5 text-center">Status Jabatan</th>
                            <th class="p-3.5 text-center w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="anggota-table-body">
                        @foreach($anggota as $item)
                        @php
                            $siswa = $item->siswa;
                            $isCurrentKetua = $item->is_current_ketua;
                            $isKetuaDiLain = $item->is_ketua_di_lain;
                            $searchKey = strtolower(($siswa->nama ?? '') . ' ' . ($siswa->nis ?? '') . ' ' . ($siswa->kelas->nama ?? ''));
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition anggota-row {{ $isCurrentKetua ? 'bg-amber-50/40' : '' }}" data-search="{{ $searchKey }}">
                            {{-- Nomor --}}
                            <td class="p-3.5 text-center font-bold text-slate-400 text-xs">
                                {{ $loop->iteration }}
                            </td>

                            {{-- Nama Siswa & NIS --}}
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl {{ $isCurrentKetua ? 'bg-amber-100 border-2 border-amber-300 shadow-sm shadow-amber-100' : 'bg-slate-100 border border-slate-200' }} overflow-hidden flex items-center justify-center shrink-0">
                                        @if($siswa && $siswa->foto_url)
                                            <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-xs font-black {{ $isCurrentKetua ? 'text-amber-700' : 'text-slate-500' }} uppercase">
                                                {{ strtoupper(substr($siswa->nama ?? 'A', 0, 2)) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-extrabold text-slate-900 hover:text-sky-600 transition truncate nama-siswa">
                                                {{ $siswa->nama ?? '-' }}
                                            </span>
                                            @if($siswa && $siswa->jenis_kelamin)
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold {{ $siswa->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-pink-50 text-pink-600 border border-pink-200' }}">
                                                    {{ $siswa->jenis_kelamin }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5">NIS: {{ $siswa->nis ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Kelas & Jurusan --}}
                            <td class="p-3.5">
                                <div class="font-bold text-slate-800 text-xs">
                                    {{ $siswa->kelas->nama ?? '-' }}
                                </div>
                                <span class="text-[10px] text-slate-400">
                                    {{ $siswa->kelas->jurusan ?? 'Umum' }}
                                </span>
                            </td>

                            {{-- Kehadiran --}}
                            <td class="p-3.5 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-xs font-extrabold {{ $item->persentase_kehadiran >= 80 ? 'text-emerald-600' : ($item->persentase_kehadiran >= 50 ? 'text-sky-600' : 'text-amber-600') }}">
                                        {{ $item->persentase_kehadiran }}%
                                    </span>
                                    <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden mt-1">
                                        <div class="h-full {{ $item->persentase_kehadiran >= 80 ? 'bg-emerald-500' : ($item->persentase_kehadiran >= 50 ? 'bg-sky-500' : 'bg-amber-500') }} rounded-full"
                                             style="width: {{ $item->persentase_kehadiran }}%"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status Jabatan --}}
                            <td class="p-3.5 text-center">
                                @if($isCurrentKetua)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-extrabold bg-gradient-to-r from-amber-400 to-amber-500 text-white shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M2 19l2-9 5 5 3-8 3 8 5-5 2 9H2z"/></svg>
                                        <span>Ketua Ekskul (Aktif)</span>
                                    </span>
                                @elseif($isKetuaDiLain)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200" title="Sudah menjadi ketua di ekskul {{ $item->ekskul_dipimpin_lain->nama_ekskul }}">
                                        <span>Ketua di {{ $item->ekskul_dipimpin_lain->nama_ekskul }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-100">
                                        <span>Anggota Aktif</span>
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi: Tombol Pilih Ketua --}}
                            <td class="p-3.5 text-center">
                                @if($isCurrentKetua)
                                    <div class="inline-flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>Ketua Terpilih</span>
                                        </span>
                                        <form action="{{ route('pembina.ekskul.copot-ketua', ['ekskul' => $ekskul->id, 'siswa' => $item->siswa_id]) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin mencopot jabatan Ketua Ekskul {{ $ekskul->nama_ekskul }} dari {{ $siswa->nama }}?')">
                                            @csrf
                                            <button type="submit"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition"
                                                    title="Copot jabatan ketua">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @elseif($isKetuaDiLain)
                                    <button disabled
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs border border-slate-200 cursor-not-allowed opacity-75"
                                            title="Siswa sudah menjabat sebagai Ketua di ekskul {{ $item->ekskul_dipimpin_lain->nama_ekskul }}">
                                        <span>Ketua Ekskul Lain</span>
                                    </button>
                                @else
                                    <form action="{{ route('pembina.ekskul.pilih-ketua', ['ekskul' => $ekskul->id, 'siswa' => $item->siswa_id]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Tetapkan {{ $siswa->nama }} sebagai Ketua Ekskul {{ $ekskul->nama_ekskul }}?{{ $currentKetua ? ' (Ketua lama ' . $currentKetua->nama . ' akan digantikan)' : '' }}')">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-extrabold text-xs shadow-sm shadow-amber-200 hover:shadow-md transition-all">
                                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M2 19l2-9 5 5 3-8 3 8 5-5 2 9H2z"/>
                                            </svg>
                                            <span>Pilih Ketua</span>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Empty search result element --}}
            <div id="no-search-results" class="hidden text-center py-12 text-slate-400">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold">Tidak ada nama anggota yang cocok dengan pencarian.</p>
                <button type="button" onclick="resetSearch()" class="text-[11px] text-amber-600 hover:underline mt-1 inline-block font-bold">
                    Reset pencarian
                </button>
            </div>
        @else
            <div class="text-center py-16 text-slate-400">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-700">Belum Ada Anggota Aktif</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Ekskul ini belum memiliki anggota dengan status diterima. Tunggu siswa mendaftar dan verifikasi pendaftaran terlebih dahulu.
                </p>
                <div class="mt-4">
                    <a href="{{ route('pembina.pendaftaran') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs transition shadow-sm">
                        <span>Lihat Pendaftaran Masuk</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    const searchInput = document.getElementById('search-anggota-input');
    const rows = document.querySelectorAll('.anggota-row');
    const noResults = document.getElementById('no-search-results');
    const countVisible = document.getElementById('count-visible');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                if (searchData.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (countVisible) {
                countVisible.textContent = visibleCount;
            }

            if (noResults) {
                if (visibleCount === 0 && rows.length > 0) {
                    noResults.classList.remove('hidden');
                } else {
                    noResults.classList.add('hidden');
                }
            }
        });
    }

    function resetSearch() {
        if (searchInput) {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        }
    }
</script>
@endpush
@endsection
