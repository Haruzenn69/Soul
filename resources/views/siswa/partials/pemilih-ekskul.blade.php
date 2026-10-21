{{-- Pemilih ekskul untuk siswa yang mengikuti lebih dari satu ekskul. --}}
@if(isset($pendaftarans) && $pendaftarans->count() > 1)
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Pilih Ekskul:</span>
        @foreach($pendaftarans as $pilihan)
            @php
                $aktif = ($pendaftaran?->ekskul_id ?? null) === $pilihan->ekskul_id;
                $isKetuaPilihan = $siswa && $siswa->riwayatKetua($pilihan->ekskul)->whereNull('selesai')->exists();
            @endphp
            <a href="{{ request()->fullUrlWithQuery(['ekskul' => $pilihan->ekskul_id]) }}"
               class="px-3 py-1.5 rounded-full text-xs font-semibold border transition-all inline-flex items-center gap-1.5 {{ $aktif ? 'bg-sky-500 border-sky-500 text-white shadow-sm' : 'bg-white dark:bg-neutral-800 border-slate-200 dark:border-neutral-700 text-slate-600 dark:text-slate-300 hover:border-sky-300 hover:text-sky-600' }}">
                {{ $pilihan->ekskul->nama_ekskul ?? 'Ekskul' }}
                @if($isKetuaPilihan)
                    <span class="text-[9px] px-1 py-0.2 rounded {{ $aktif ? 'bg-white/20 text-white' : 'bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400' }} font-bold">&bull; Ketua</span>
                @endif
            </a>
        @endforeach
    </div>
@endif
