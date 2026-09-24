{{-- Isi halaman "Nilai" untuk siswa & ketua (dipakai oleh siswa.nilai & ketua.nilai) --}}
@php
    $bobotLabel = [
        ['label' => 'Kehadiran', 'value' => $penilaian?->persentase_kehadiran ?? 0, 'bobot' => '30%', 'color' => 'text-blue-600', 'bar' => 'bg-blue-600'],
        ['label' => 'Sikap', 'value' => $penilaian?->nilai_sikap ?? 0, 'bobot' => '25%', 'color' => 'text-amber-600', 'bar' => 'bg-amber-400'],
        ['label' => 'Keaktifan', 'value' => $penilaian?->nilai_keaktifan ?? 0, 'bobot' => '25%', 'color' => 'text-emerald-600', 'bar' => 'bg-emerald-500'],
        ['label' => 'Keterampilan', 'value' => $penilaian?->nilai_keterampilan ?? 0, 'bobot' => '20%', 'color' => 'text-rose-600', 'bar' => 'bg-rose-500'],
    ];
@endphp

<div class="bg-[#F8FAFC] -mx-4 md:-mx-8 px-4 md:px-8 py-6 md:py-8 min-h-[calc(100vh-5rem)] space-y-6 animate-fade-up">
    <div>
        <h1 class="text-lg md:text-2xl font-extrabold text-slate-900">Nilai Ekskul</h1>
        <p class="text-xs text-slate-400 mt-0.5">Nilai akhir kamu pada periode berjalan</p>
    </div>

    @if (! $pendaftaran || ! $ekskul)
        <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-12 md:p-16 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/>
                </svg>
            </div>
            <h2 class="text-sm font-bold text-slate-900">Belum Terdaftar di Ekskul</h2>
            <p class="text-xs text-slate-400 mt-1">Nilai akan muncul setelah kamu menjadi anggota aktif sebuah ekskul dan pembina menilai.</p>
            <a href="{{ route('siswa.katalog') }}"
               class="inline-flex items-center gap-2 mt-4 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-600/25 transition">
                Jelajahi Katalog
            </a>
        </div>
    @elseif (! $penilaian)
        <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 text-center">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-500 mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-slate-900">Nilai Belum Diumumkan</h2>
                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">Pembina ekskul <span class="font-semibold text-slate-600">{{ $ekskul->nama_ekskul }}</span> belum mengirimkan nilai periode <span class="font-semibold text-blue-600">{{ $periode['label'] }}</span>. Kamu akan dapat melihatnya di sini begitu nilai dirilis.</p>
            </div>

            @php $hadir = \App\Models\Penilaian::kehadiran($pendaftaran, $periode); @endphp
            <div class="border-t border-slate-200/70 bg-slate-50/60 p-4 md:p-5">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Rekap kehadiran sementara</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $hadir['total_pertemuan'] }} pertemuan &middot; {{ number_format($hadir['persentase_kehadiran'], 1) }}% hadir</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Kehadiran: H{{ $hadir['total_hadir'] }} I{{ $hadir['total_izin'] }} S{{ $hadir['total_sakit'] }} A{{ $hadir['total_alpha'] }}
                    </span>
                </div>
            </div>
        </div>
    @else
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center font-extrabold text-sm shadow-lg shadow-blue-600/30">
                    {{ strtoupper(mb_substr($ekskul->nama_ekskul, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-bold text-slate-900 text-sm">{{ $ekskul->nama_ekskul }}</h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Periode <span class="font-semibold text-blue-600">{{ $periode['label'] }}</span></p>
                </div>
            </div>
            <span class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Dikeluarkan {{ $penilaian->dikirim_at?->translatedFormat('d M Y') }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-5">
            <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-200/70 shadow-sm p-5 md:p-6 flex flex-col items-center justify-center text-center">
                <div class="w-24 h-24 rounded-[28px] bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex flex-col items-center justify-center shadow-xl shadow-blue-600/30">
                    <span class="text-3xl font-extrabold leading-none">{{ number_format((float) $penilaian->nilai_akhir, 2, '.', '') }}</span>
                    <span class="text-[9px] font-bold tracking-widest text-blue-100 mt-1">NILAI AKHIR</span>
                </div>
                <div class="mt-4">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl border-2 font-extrabold text-base {{ \App\Models\Penilaian::warnaPredikat((string) $penilaian->predikat) }}">
                        {{ $penilaian->predikat }}
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">Predikat</p>
            </div>

            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-5">
                    <h3 class="text-xs font-bold text-slate-900 mb-4">Rincian Komponen</h3>
                    <div class="space-y-4">
                        @foreach ($bobotLabel as $komponen)
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[11px] font-semibold text-slate-600">{{ $komponen['label'] }}</span>
                                    <span class="text-[11px] font-bold {{ $komponen['color'] }}">
                                        {{ number_format((float) $komponen['value'], 1) }}
                                        <span class="text-slate-300 font-medium">/ Bobot {{ $komponen['bobot'] }}</span>
                                    </span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full {{ $komponen['bar'] }}" style="width: {{ min(100, max(0, (float) $komponen['value'])) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-200/70 grid grid-cols-2 sm:grid-cols-5 gap-2 text-center">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <p class="text-sm font-extrabold text-slate-900">{{ $penilaian->total_pertemuan }}</p>
                            <p class="text-[9px] font-bold text-slate-400 mt-0.5">Pertemuan</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-100">
                            <p class="text-sm font-extrabold text-emerald-600">{{ $penilaian->total_hadir }}</p>
                            <p class="text-[9px] font-bold text-slate-400 mt-0.5">Hadir</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-sky-50 border border-sky-100">
                            <p class="text-sm font-extrabold text-sky-600">{{ $penilaian->total_izin }}</p>
                            <p class="text-[9px] font-bold text-slate-400 mt-0.5">Izin</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-amber-50 border border-amber-100">
                            <p class="text-sm font-extrabold text-amber-600">{{ $penilaian->total_sakit }}</p>
                            <p class="text-[9px] font-bold text-slate-400 mt-0.5">Sakit</p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-red-50 border border-red-100">
                            <p class="text-sm font-extrabold text-red-600">{{ $penilaian->total_alpha }}</p>
                            <p class="text-[9px] font-bold text-slate-400 mt-0.5">Alpha</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-5">
                    <h3 class="text-xs font-bold text-slate-900 mb-2">Catatan Pembina</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $penilaian->catatan ?: 'Tidak ada catatan dari pembina.' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4 md:p-5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Dinilai oleh {{ $penilaian->penilai?->nama ?? 'Pembina Ekskul' }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Nilai akhir dihitung otomatis dari kehadiran dan komponen penilaian.</p>
                </div>
            </div>
        </div>
    @endif
</div>