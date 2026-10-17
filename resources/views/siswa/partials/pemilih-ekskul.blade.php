{{-- Pemilih ekskul untuk siswa yang mengikuti lebih dari satu ekskul. --}}
@if(isset($pendaftarans) && $pendaftarans->count() > 1)
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Pilih Ekskul</span>
        @foreach($pendaftarans as $pilihan)
            @php($aktif = ($pendaftaran?->ekskul_id ?? null) === $pilihan->ekskul_id)
            <a href="{{ request()->fullUrlWithQuery(['ekskul' => $pilihan->ekskul_id]) }}"
               class="px-3 py-1.5 rounded-full text-xs font-semibold border transition-all {{ $aktif ? 'bg-sky-500 border-sky-500 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:border-sky-300 hover:text-sky-600' }}">
                {{ $pilihan->ekskul->nama_ekskul ?? 'Ekskul' }}
            </a>
        @endforeach
    </div>
@endif
