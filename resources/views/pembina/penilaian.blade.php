@extends('pembina.layout')
@section('title', 'Penilaian Ekskul')

@section('content')
    <div class="bg-[#F8FAFC] -mx-4 md:-mx-8 px-4 md:px-8 py-6 md:py-8 min-h-[calc(100vh-5rem)] space-y-6 animate-fade-up">
        @if (! $ekskul)
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-10 md:p-16 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-slate-900">Belum Ada Ekskul</h2>
                <p class="text-xs text-slate-400 mt-1">Akun pembina ini belum memiliki ekskul untuk dinilai.</p>
            </div>
        @else
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-lg md:text-2xl font-extrabold text-slate-900">Penilaian Ekskul</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Nilai akhir untuk anggota ekskul pada periode berjalan</p>
                </div>
                <form method="GET" action="{{ route('pembina.penilaian') }}" class="w-full lg:w-auto">
                    <label for="ekskul" class="sr-only">Pilih ekskul</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                        <select name="ekskul" id="ekskul" onchange="this.form.submit()"
                                class="w-full lg:w-64 pl-9 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all shadow-sm appearance-none">
                            @foreach ($ekskuls as $e)
                                <option value="{{ $e->id }}" @selected(($ekskul->id ?? null) === $e->id)>{{ $e->nama_ekskul }}</option>
                            @endforeach
                        </select>
                    </div>
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
                    <a href="{{ route('pembina.penilaian.download-pdf', ['ekskul' => $ekskul->id]) }}"
                       class="inline-flex items-center gap-2 px-3.5 py-2 bg-white hover:bg-blue-50 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 shadow-sm transition">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-3.5 md:p-4">
                    <p class="text-lg md:text-2xl font-extrabold text-slate-900">{{ $rows->count() }}</p>
                    <p class="text-[10px] md:text-[11px] font-bold text-slate-400 mt-0.5">Total Anggota</p>
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

                <div class="p-4 md:p-5 border-b border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <h3 class="text-sm font-bold text-slate-900">Daftar Anggota</h3>
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
                                        <span class="block text-[9px] text-slate-400 font-medium">H:{{ $row->total_hadir }} I:{{ $row->total_izin }} S:{{ $row->total_sakit }} A:{{ $row->total_alpha }}</span>
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
                                        <p class="text-sm font-semibold text-slate-500">Belum ada anggota aktif</p>
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