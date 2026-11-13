@extends('pembina.layout')
@section('title', 'Penilaian Ekskul')

@section('content')
    <div class="bg-[#F8FAFC] -mx-4 md:-mx-8 px-4 md:px-8 py-6 md:py-8 min-h-[calc(100vh-5rem)] space-y-6 animate-fade-up">
        @if (! $ekskul)
            {{-- STEP 1: PILIH EKSKUL TERLEBIH DAHULU --}}
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Input Penilaian Ekskul</h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Pilih ekstrakurikuler terlebih dahulu untuk mulai mengisi atau meninjau penilaian anggota periode <span class="font-bold text-sky-600 dark:text-sky-400">{{ $periode['label'] }}</span>.
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-50 dark:bg-neutral-800 border border-sky-200/60 dark:border-neutral-700 text-sky-700 dark:text-sky-300 text-xs font-semibold self-start sm:self-auto">
                        <i class="fa-solid fa-shapes text-xs"></i>
                        <span>{{ $ekskuls->count() }} Ekskul Tersedia</span>
                    </span>
                </div>

                @if($ekskuls->isEmpty())
                    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-slate-200/80 dark:border-neutral-800 shadow-sm p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 flex items-center justify-center text-slate-400 mb-4">
                            <i class="fa-solid fa-folder-open text-xl"></i>
                        </div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Belum Ada Ekskul</h2>
                        <p class="text-xs text-slate-400 mt-1">Anda belum memiliki ekstrakurikuler binaan untuk dinilai.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach($ekskulCards as $card)
                            <div class="bg-white dark:bg-[#1a1a1a] rounded-2xl p-5 border border-slate-200/80 dark:border-neutral-800 shadow-sm flex flex-col justify-between transition-all hover:border-sky-300 dark:hover:border-sky-700 hover:shadow-md group">
                                <div>
                                    <div class="flex items-start justify-between gap-2 mb-3">
                                        <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-lg bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/50">
                                            {{ $card->ekskul->kategori ?? 'Ekskul' }}
                                        </span>
                                        @if($card->terkirim)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-md border border-emerald-200/60 dark:border-emerald-800/50">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i> Terkirim
                                            </span>
                                        @elseif($card->sudahDinilai > 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50 px-2 py-0.5 rounded-md border border-amber-200/60 dark:border-amber-800/50">
                                                <i class="fa-solid fa-clock text-[9px]"></i> Draf
                                            </span>
                                        @else
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                                Belum Dinilai
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">
                                        {{ $card->ekskul->nama_ekskul }}
                                    </h3>

                                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-neutral-800 grid grid-cols-2 gap-2 text-xs">
                                        <div>
                                            <span class="text-[10px] text-slate-400 block font-medium">Anggota Aktif</span>
                                            <span class="font-extrabold text-slate-800 dark:text-slate-200">{{ $card->totalAnggota }} Siswa</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 block font-medium">Sudah Dinilai</span>
                                            <span class="font-extrabold text-sky-600 dark:text-sky-400">{{ $card->sudahDinilai }} / {{ $card->totalAnggota }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-5 pt-3 border-t border-slate-100 dark:border-neutral-800">
                                    <a href="{{ route('pembina.penilaian', ['ekskul' => $card->ekskul->id]) }}"
                                       class="w-full inline-flex items-center justify-center gap-2 py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:hover:bg-slate-100 dark:text-slate-900 text-xs font-bold transition shadow-sm">
                                        <span>Input Penilaian</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            {{-- STEP 2: FORM PENILAIAN EKSKUL TERPILIH --}}
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div class="flex items-center gap-3">
                    @if($ekskuls->count() > 1)
                        <a href="{{ route('pembina.penilaian') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-neutral-800 border border-slate-200 dark:border-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-neutral-700 transition shadow-xs"
                           title="Kembali memilih ekskul lain">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            <span class="hidden sm:inline">Pilih Ekskul Lain</span>
                        </a>
                    @endif
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-lg md:text-2xl font-extrabold text-slate-900 dark:text-white">Penilaian: {{ $ekskul->nama_ekskul }}</h1>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Nilai akhir untuk anggota ekskul pada periode berjalan</p>
                    </div>
                </div>
                <form method="GET" action="{{ route('pembina.penilaian') }}" class="w-full lg:w-auto flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <input type="hidden" name="ekskul" value="{{ $ekskul->id }}">

                    {{-- FILTER KELAS SISWA --}}
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                            </svg>
                        </span>
                        <select name="kelas_id" id="kelas_id" onchange="this.form.submit()"
                                class="w-full sm:w-44 pl-9 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all shadow-sm appearance-none">
                            <option value="">Semua Kelas</option>
                            @foreach ($kelasOptions as $k)
                                <option value="{{ $k->id }}" @selected((string)$selectedKelasId === (string)$k->id)>
                                    Kelas {{ $k->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- FILTER STATUS PENILAIAN --}}
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <i class="fa-solid fa-clipboard-check text-xs"></i>
                        </span>
                        <select name="status_penilaian" id="status_penilaian" onchange="this.form.submit()"
                                class="w-full sm:w-44 pl-9 pr-8 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all shadow-sm appearance-none">
                            <option value="">Semua Status Nilai</option>
                            <option value="sudah" @selected(($statusPenilaian ?? '') === 'sudah')>Sudah Dinilai</option>
                            <option value="belum" @selected(($statusPenilaian ?? '') === 'belum')>Belum Dinilai</option>
                        </select>
                    </div>

                    @if ($selectedKelasId || !empty($statusPenilaian))
                        <a href="{{ route('pembina.penilaian', ['ekskul' => $ekskul->id]) }}"
                           class="inline-flex items-center justify-center px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition shadow-sm"
                           title="Reset filter">
                            Reset Filter
                        </a>
                    @endif
                </form>
            </div>

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($sudahDikirim)
                <div class="p-4 bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold rounded-xl flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Laporan penilaian periode <span class="font-extrabold">{{ $periode['label'] }}</span> sudah dikirim ke kesiswaan untuk ekskul <span class="font-extrabold">{{ $ekskul->nama_ekskul }}</span>.
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4 md:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">{{ $ekskul->nama_ekskul }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Periode <span class="font-semibold text-blue-600">{{ $periode['label'] }}</span>
                            &middot; Rentang {{ \Carbon\Carbon::parse($periode['start'])->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($periode['end'])->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('pembina.penilaian.download-pdf', ['ekskul' => $ekskul->id, 'kelas_id' => $selectedKelasId, 'status_penilaian' => $statusPenilaian ?? null]) }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-blue-50 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 shadow-sm transition">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download PDF {{ $selectedKelasId ? '(Kelas Terpilih)' : '' }}
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-3.5 md:p-4">
                    <p class="text-lg md:text-2xl font-extrabold text-slate-900">{{ $rows->count() }}</p>
                    <p class="text-[10px] md:text-[11px] font-bold text-slate-400 mt-0.5">
                        {{ $selectedKelasId ? 'Anggota Kelas Ini' : 'Total Anggota' }}
                    </p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-3.5 md:p-4">
                    <p class="text-lg md:text-2xl font-extrabold text-amber-600">30%</p>
                    <p class="text-[10px] md:text-[11px] font-bold text-slate-400 mt-0.5">Bobot Kehadiran</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-3.5 md:p-4">
                    <p class="text-lg md:text-2xl font-extrabold text-blue-600">{{ $rows->where('penilaian', '!=', null)->count() }}</p>
                    <p class="text-[10px] md:text-[11px] font-bold text-slate-400 mt-0.5">Sudah Dinilai</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-3.5 md:p-4">
                    <p class="text-lg md:text-2xl font-extrabold text-emerald-600">{{ number_format($rows->where('nilai_akhir', '>', 0)->avg('nilai_akhir') ?? 0, 1) }}</p>
                    <p class="text-[10px] md:text-[11px] font-bold text-slate-400 mt-0.5">Rata-rata Akhir</p>
                </div>
            </div>

            <form method="POST" action="{{ route('pembina.penilaian.store') }}" id="form-penilaian" class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                @csrf
                <input type="hidden" name="ekskul" value="{{ $ekskul->id }}">
                @if ($selectedKelasId)
                    <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">
                @endif
                @if (!empty($statusPenilaian))
                    <input type="hidden" name="status_penilaian" value="{{ $statusPenilaian }}">
                @endif

                <div class="p-4 md:p-5 border-b border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-sm font-bold text-slate-900">Daftar Anggota</h3>
                        @if ($selectedKelasId)
                            @php $currentKelasModel = $kelasOptions->firstWhere('id', $selectedKelasId); @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                Filter: Kelas {{ $currentKelasModel?->nama ?? $selectedKelasId }}
                            </span>
                        @endif
                        @if (!empty($statusPenilaian))
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10px] font-bold {{ $statusPenilaian === 'sudah' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                <i class="fa-solid fa-clipboard-check text-[10px]"></i>
                                Status: {{ $statusPenilaian === 'sudah' ? 'Sudah Dinilai' : 'Belum Dinilai' }}
                            </span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-400">Kehadiran diisi otomatis dari presensi kegiatan ekskul.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs min-w-[1100px]">
                        <thead class="bg-slate-50/80 text-[10px] uppercase tracking-wider text-slate-400">
                            <tr>
                                <th class="px-4 py-3 font-bold">#</th>
                                <th class="px-4 py-3 font-bold">Anggota</th>
                                <th class="px-4 py-3 font-bold text-center">Pertemuan</th>
                                <th class="px-4 py-3 font-bold text-center">% Hadir</th>
                                <th class="px-4 py-3 font-bold text-center">Sikap <span class="normal-case text-blue-600">(25%)</span></th>
                                <th class="px-4 py-3 font-bold text-center">Keaktifan <span class="normal-case text-blue-600">(25%)</span></th>
                                <th class="px-4 py-3 font-bold text-center">Keterampilan <span class="normal-case text-blue-600">(20%)</span></th>
                                <th class="px-4 py-3 font-bold text-center">Nilai Akhir</th>
                                <th class="px-4 py-3 font-bold text-center">Predikat</th>
                                <th class="px-4 py-3 font-bold">Catatan</th>
                                <th class="px-4 py-3 font-bold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($rows as $row)
                                @php
                                    $locked = $row->status === \App\Models\Penilaian::STATUS_TERKIRIM;
                                @endphp
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-4 py-3 text-slate-400 font-semibold">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3 min-w-[180px]">
                                            <div class="w-9 h-9 rounded-full bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center font-extrabold text-xs shrink-0">
                                                {{ strtoupper(mb_substr($row->siswa?->nama ?? '?', 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 leading-tight truncate">{{ $row->siswa?->nama }}</p>
                                                <p class="text-[10px] text-slate-400">{{ $row->siswa?->nis }} &middot; {{ $row->kelas }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-slate-700 font-semibold">{{ $row->total_pertemuan }}
                                        <span class="block text-[9px] text-slate-400 font-medium">H:{{ $row->total_hadir }} S:{{ $row->total_sakit }} I:{{ $row->total_izin }} A:{{ $row->total_alpha }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $row->persentase_kehadiran >= 70 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-600 border border-red-200' }}">
                                            {{ number_format($row->persentase_kehadiran, 1) }}%
                                        </span>
                                        <input type="hidden" id="hadir-{{ $row->pendaftaran->id }}" data-hadir="{{ $row->persentase_kehadiran }}">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input id="sikap-{{ $row->pendaftaran->id }}" type="number" inputmode="decimal" min="0" max="100" step="0.5" name="penilaian[{{ $row->pendaftaran->id }}][nilai_sikap]"
                                               value="{{ $row->penilaian ? number_format((float) $row->nilai_sikap, (fmod((float) $row->nilai_sikap, 1) == 0 ? 0 : 1), '.', '') : '' }}"
                                               placeholder="1-100"
                                               {{ $locked ? 'readonly disabled' : '' }}
                                               class="w-20 px-2 py-2 text-center bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 placeholder-slate-300 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all {{ $locked ? 'opacity-50' : '' }}"
                                               oninput="updateRow({{ $row->pendaftaran->id }})">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input id="aktif-{{ $row->pendaftaran->id }}" type="number" inputmode="decimal" min="0" max="100" step="0.5" name="penilaian[{{ $row->pendaftaran->id }}][nilai_keaktifan]"
                                               value="{{ $row->penilaian ? number_format((float) $row->nilai_keaktifan, (fmod((float) $row->nilai_keaktifan, 1) == 0 ? 0 : 1), '.', '') : '' }}"
                                               placeholder="1-100"
                                               {{ $locked ? 'readonly disabled' : '' }}
                                               class="w-20 px-2 py-2 text-center bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 placeholder-slate-300 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all {{ $locked ? 'opacity-50' : '' }}"
                                               oninput="updateRow({{ $row->pendaftaran->id }})">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input id="skill-{{ $row->pendaftaran->id }}" type="number" inputmode="decimal" min="0" max="100" step="0.5" name="penilaian[{{ $row->pendaftaran->id }}][nilai_keterampilan]"
                                               value="{{ $row->penilaian ? number_format((float) $row->nilai_keterampilan, (fmod((float) $row->nilai_keterampilan, 1) == 0 ? 0 : 1), '.', '') : '' }}"
                                               placeholder="1-100"
                                               {{ $locked ? 'readonly disabled' : '' }}
                                               class="w-20 px-2 py-2 text-center bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-800 placeholder-slate-300 focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all {{ $locked ? 'opacity-50' : '' }}"
                                               oninput="updateRow({{ $row->pendaftaran->id }})">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span id="preview-{{ $row->pendaftaran->id }}" class="inline-block font-extrabold text-slate-900">
                                            {{ $row->penilaian ? number_format((float) $row->nilai_akhir, 2, '.', '') : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span id="pred-{{ $row->pendaftaran->id }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl border font-extrabold text-xs {{ $row->penilaian ? \App\Models\Penilaian::warnaPredikat((string) $row->predikat) : 'text-slate-400 bg-slate-50 border-slate-200' }}">
                                            {{ $row->penilaian ? $row->predikat : '&ndash;' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 min-w-[160px]">
                                        <textarea name="penilaian[{{ $row->pendaftaran->id }}][catatan]" rows="2"
                                                  placeholder="Catatan penilaian (opsional)"
                                                  {{ $locked ? 'readonly' : '' }}
                                                  class="w-40 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[11px] text-slate-700 placeholder-slate-300 resize-none focus:outline-none focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all {{ $locked ? 'opacity-50' : '' }}">{{ $row->penilaian?->catatan }}</textarea>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($locked)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Terkirim
                                            </span>
                                        @elseif ($row->penilaian)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0l3-3m-3 3l-3-3m9 5a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Draft
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200">Belum</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-12 text-center">
                                        <p class="text-sm font-semibold text-slate-500">
                                            @if ($selectedKelasId)
                                                Tidak ada anggota aktif di ekskul ini dari kelas yang dipilih.
                                            @else
                                                Belum ada anggota aktif
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 md:p-5 border-t border-slate-200/70 bg-slate-50/50 flex flex-col-reverse sm:flex-row sm:items-center justify-end gap-2.5">
                    <button type="button" onclick="previewSemua()"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold rounded-xl border border-slate-200 shadow-sm transition">
                        Hitung Ulang Preview
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-blue-50 text-blue-600 text-xs font-bold rounded-xl border border-blue-200 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Simpan Draft
                    </button>
                    <button type="submit" id="btn-kirim" formaction="{{ route('pembina.penilaian.kirim') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/25 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l6-6m0 0l-6-6m6 6H9a6 6 0 00-6 6v3"/>
                        </svg>
                        Kirim ke Kesiswaan
                    </button>
                </div>
            </form>

            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4 md:p-5">
                <h3 class="text-xs font-bold text-slate-900 mb-3">Rumus Penilaian</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-[11px] text-slate-500">
                    <div class="flex items-center gap-2.5 p-3 rounded-xl bg-blue-50/60 border border-blue-100">
                        <span class="text-blue-600 font-extrabold">30%</span> Kehadiran
                    </div>
                    <div class="flex items-center gap-2.5 p-3 rounded-xl bg-amber-50/60 border border-amber-100">
                        <span class="text-amber-600 font-extrabold">25%</span> Sikap
                    </div>
                    <div class="flex items-center gap-2.5 p-3 rounded-xl bg-emerald-50/60 border border-emerald-100">
                        <span class="text-emerald-600 font-extrabold">25%</span> Keaktifan
                    </div>
                    <div class="flex items-center gap-2.5 p-3 rounded-xl bg-rose-50/60 border border-rose-100">
                        <span class="text-rose-600 font-extrabold">20%</span> Keterampilan
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-3 leading-relaxed">
                    Nilai Akhir = (Persentase Kehadiran x 30%) + (Sikap x 25%) + (Keaktifan x 25%) + (Keterampilan x 20%).
                    Predikat: A (90-100), B (80-89), C (70-79), D (60-69), E (<60).
                </p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function angka(v) { var n = parseFloat(v); return isNaN(n) ? 0 : n; }

        function hitungPredikat(x) {
            if (x >= 90) return 'A';
            if (x >= 80) return 'B';
            if (x >= 70) return 'C';
            if (x >= 60) return 'D';
            return 'E';
        }

        function warnaPredikat(p) {
            switch (p) {
                case 'A': return 'text-emerald-600 bg-emerald-50 border-emerald-200';
                case 'B': return 'text-sky-600 bg-sky-50 border-sky-200';
                case 'C': return 'text-amber-600 bg-amber-50 border-amber-200';
                case 'D': return 'text-orange-600 bg-orange-50 border-orange-200';
                default: return 'text-red-600 bg-red-50 border-red-200';
            }
        }

        function updateRow(id) {
            var hadir = angka(document.getElementById('hadir-' + id).dataset.hadir);
            var sikap = angka(document.getElementById('sikap-' + id).value);
            var aktif = angka(document.getElementById('aktif-' + id).value);
            var skill = angka(document.getElementById('skill-' + id).value);
            var akhir = Math.round((hadir * 0.30 + sikap * 0.25 + aktif * 0.25 + skill * 0.20) * 100) / 100;

            var preview = document.getElementById('preview-' + id);
            var pred = document.getElementById('pred-' + id);

            if (sikap === 0 && aktif === 0 && skill === 0) {
                preview.textContent = '-';
                pred.textContent = '\u2013';
                pred.className = 'inline-flex items-center justify-center w-8 h-8 rounded-xl border font-extrabold text-xs text-slate-400 bg-slate-50 border-slate-200';
                return;
            }

            var p = hitungPredikat(akhir);
            preview.textContent = akhir.toFixed(2);
            pred.textContent = p;
            pred.className = 'inline-flex items-center justify-center w-8 h-8 rounded-xl border font-extrabold text-xs ' + warnaPredikat(p);
        }

        function previewSemua() {
            document.querySelectorAll('#form-penilaian tbody tr').forEach(function (tr) {
                var input = tr.querySelector('input[id^="sikap-"]');
                if (input) updateRow(input.id.replace('sikap-', ''));
            });
        }

        var btnKirim = document.getElementById('btn-kirim');
        if (btnKirim) {
            btnKirim.addEventListener('click', function (e) {
                e.preventDefault();
                var form = document.getElementById('form-penilaian');
                var action = btnKirim.getAttribute('formaction');
                Swal.fire({
                    title: 'Kirim laporan penilaian?',
                    text: 'Nilai yang dikirim tidak dapat diubah lagi. Kesiswaan akan menerima notifikasi.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Kirim',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#2563EB',
                    cancelButtonColor: '#E2E8F0'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.action = action;
                        form.submit();
                    }
                });
            });
        }
    </script>
@endpush
