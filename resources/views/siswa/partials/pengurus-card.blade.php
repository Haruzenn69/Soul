    {{-- CARD PENGURUS EKSKUL (HANYA CARD DI DASHBOARD SISWA) --}}
    @if($ekskul && isset($pengurusAktif))
        <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-sky-100 dark:border-neutral-800 shadow-sm p-5 md:p-6 animate-fade-up">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 mb-4 border-b border-sky-50 dark:border-neutral-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-black text-sm shadow-sm shadow-sky-200 dark:shadow-none shrink-0">
                        <i class="fa-solid fa-users text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm md:text-base font-extrabold text-slate-900 dark:text-white">
                            Pengurus {{ $ekskul->nama_ekskul }}
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Struktur pengurus inti yang memimpin organisasi ekskul</p>
                    </div>
                </div>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-100 dark:border-sky-900/50 self-start sm:self-auto">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    4 Posisi Pokok
                </span>
            </div>

            @php
                $jabatanLabels = [
                    'ketua' => 'Ketua',
                    'wakil_ketua' => 'Wakil Ketua',
                    'sekretaris' => 'Sekretaris',
                    'bendahara' => 'Bendahara',
                ];
                $jabatanStyles = [
                    'ketua' => [
                        'badge' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                        'accent' => 'from-sky-400 to-blue-500',
                        'icon' => 'fa-crown',
                    ],
                    'wakil_ketua' => [
                        'badge' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                        'accent' => 'from-sky-400 to-blue-500',
                        'icon' => 'fa-user-group',
                    ],
                    'sekretaris' => [
                        'badge' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                        'accent' => 'from-sky-400 to-blue-500',
                        'icon' => 'fa-file-signature',
                    ],
                    'bendahara' => [
                        'badge' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                        'accent' => 'from-sky-400 to-blue-500',
                        'icon' => 'fa-wallet',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                @foreach($jabatanLabels as $jKey => $jTitle)
                    @php
                        $pItem = $pengurusAktif->get($jKey);
                        $jStyle = $jabatanStyles[$jKey] ?? $jabatanStyles['ketua'];
                    @endphp

                    <div class="rounded-2xl border border-sky-100/80 dark:border-neutral-800 bg-sky-50/30 dark:bg-neutral-950/60 p-4 flex flex-col justify-between transition hover:border-sky-300 dark:hover:border-neutral-700">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $jStyle['badge'] }}">
                                    <i class="fa-solid {{ $jStyle['icon'] }} text-[10px]"></i>
                                    {{ $jTitle }}
                                </span>

                                @if($pItem && $pItem->siswa)
                                    <span class="w-2 h-2 rounded-full bg-sky-500 shadow-xs" title="Aktif"></span>
                                @endif
                            </div>

                            @if($pItem && $pItem->siswa)
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl p-0.5 bg-gradient-to-br {{ $jStyle['accent'] }} shrink-0 shadow-xs">
                                        <div class="w-full h-full rounded-[10px] overflow-hidden bg-white dark:bg-neutral-800 flex items-center justify-center">
                                            <img src="{{ $pItem->siswa->foto_url ?? asset('images/default.png') }}" alt="{{ $pItem->siswa->nama }}" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-xs font-extrabold text-slate-900 dark:text-white truncate" title="{{ $pItem->siswa->nama }}">
                                            {{ $pItem->siswa->nama }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                            {{ $pItem->siswa->kelas?->nama ?? 'Siswa' }} • NIS: {{ $pItem->siswa->nis }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="py-2.5 px-3 rounded-xl border border-dashed border-sky-200/60 dark:border-neutral-800 text-center">
                                    <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500">Belum Ada Pengurus</p>
                                    <p class="text-[10px] text-slate-400/80 dark:text-slate-600 mt-0.5">Posisi belum terisi</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

