@php
    $categories = $cards->map(fn($c) => $c->ekskul->kategori ?? 'Umum')->unique()->filter()->values();
@endphp

<div x-data="{
    search: '',
    kategori: 'semua',
    matches(name, cat) {
        let q = this.search.toLowerCase().trim();
        let matchSearch = !q || name.toLowerCase().includes(q) || cat.toLowerCase().includes(q);
        let matchCat = this.kategori === 'semua' || cat.toLowerCase() === this.kategori.toLowerCase();
        return matchSearch && matchCat;
    },
    reset() {
        this.search = '';
        this.kategori = 'semua';
    }
}" class="space-y-6 animate-fade-up">

    {{-- HEADER KATALOG KARTU EKSKUL --}}
    <div class="bg-white dark:bg-[#1a1a1a] rounded-3xl p-6 md:p-8 border border-slate-200/80 dark:border-neutral-800 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-neutral-800">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 text-xs font-bold border border-sky-200/60 dark:border-sky-800/50 mb-2">
                    <i class="{{ $icon ?? 'fa-solid fa-shapes' }} text-xs"></i> {{ $featureBadge ?? 'Operasional Ketua' }}
                </span>
                <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ $title ?? 'Pilih Ekstrakurikuler' }}
                </h1>
                <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 mt-1">
                    {{ $subtitle ?? 'Pilih salah satu ekstrakurikuler di bawah ini untuk mengelola fitur ini.' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 rounded-xl bg-slate-100 dark:bg-neutral-800 text-slate-600 dark:text-slate-300 text-xs font-bold border border-slate-200/80 dark:border-neutral-700">
                    Total {{ $cards->count() }} Ekskul
                </span>
            </div>
        </div>

        {{-- FILTER & SEARCH BAR UNTUK KATALOG KARTU --}}
        <div class="bg-slate-50 dark:bg-neutral-900/60 p-3.5 rounded-2xl border border-slate-200/80 dark:border-neutral-800 mb-6">
            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                {{-- Search Box --}}
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" x-model="search" placeholder="Cari nama ekskul atau kategori..."
                           class="w-full pl-10 pr-4 py-2 bg-white dark:bg-[#1a1a1a] border border-slate-200 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-sky-500 transition-all">
                    <button type="button" x-show="search.length > 0" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Filter Kategori --}}
                <div class="sm:w-56 relative shrink-0">
                    <select x-model="kategori"
                            class="w-full pl-3.5 pr-8 py-2 bg-white dark:bg-[#1a1a1a] border border-slate-200 dark:border-neutral-700 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300 focus:outline-none focus:border-sky-500 transition-all appearance-none">
                        <option value="semua">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[10px]">
                        <i class="fa-solid fa-chevron-down"></i>
                    </span>
                </div>

                {{-- Reset Filter Button --}}
                <button type="button" @click="reset()" x-show="search.length > 0 || kategori !== 'semua'"
                        class="px-3.5 py-2 rounded-xl bg-slate-200/80 hover:bg-slate-300 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition shrink-0 flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-rotate-left text-[11px]"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        {{-- GRID KARTU EKSKUL --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($cards as $card)
                @php
                    $ekskulName = $card->ekskul->nama_ekskul;
                    $ekskulCat = $card->ekskul->kategori ?? 'Umum';
                @endphp
                <div x-show="matches('{{ addslashes($ekskulName) }}', '{{ addslashes($ekskulCat) }}')"
                     x-transition
                     class="bg-slate-50/70 dark:bg-neutral-900/60 rounded-2xl p-5 border border-slate-200/80 dark:border-neutral-800 flex flex-col justify-between hover:border-sky-300 dark:hover:border-sky-700 hover:shadow-md transition group">
                    <div>
                        {{-- Top Badge Row --}}
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider bg-sky-100 text-sky-800 dark:bg-sky-950/70 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60">
                                {{ $ekskulCat }}
                            </span>
                            @if(isset($card->badge) && $card->badge)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $card->badge['class'] ?? 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                    {{ $card->badge['text'] }}
                                </span>
                            @endif
                        </div>

                        {{-- Ekskul Name --}}
                        <h3 class="text-base font-extrabold text-slate-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition mb-3">
                            {{ $ekskulName }}
                        </h3>

                        {{-- Stats / Info Box --}}
                        @if(isset($card->stats) && is_array($card->stats) && count($card->stats) > 0)
                            <div class="space-y-2 mb-4 bg-white dark:bg-neutral-800 p-3 rounded-xl border border-slate-200/60 dark:border-neutral-700/60">
                                @foreach($card->stats as $st)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500 dark:text-slate-400">{{ $st['label'] }}:</span>
                                        <span class="{{ $st['class'] ?? 'font-bold text-slate-800 dark:text-slate-200' }}">{{ $st['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Action Button --}}
                    <a href="{{ $card->actionUrl }}"
                       class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs">
                        <span>{{ $card->actionText ?? 'Kelola' }}</span>
                        <i class="fa-solid fa-arrow-right text-[11px]"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
