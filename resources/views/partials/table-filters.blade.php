@php
    $filters = $filters ?? [];
    $hasActive = request()->filled('cari') || collect($filters)->contains(fn ($f) => request($f['name']) && request($f['name']) !== 'semua');
@endphp
<form method="GET" action="{{ $action }}" class="white-card-filter bg-white dark:bg-neutral-900 p-4 rounded-2xl border border-sky-100 dark:border-neutral-800 shadow-lg shadow-sky-100/60 dark:shadow-none flex flex-wrap items-center gap-3">
    <div class="relative flex-1 min-w-[220px]">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-neutral-500 pointer-events-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <input type="search" name="cari" value="{{ request('cari') }}" placeholder="{{ $placeholder ?? 'Cari...' }}"
            class="w-full pl-9 pr-4 py-2 bg-sky-50/70 dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-800 dark:text-neutral-100 placeholder-slate-400 dark:placeholder-neutral-500 focus:outline-none focus:bg-white dark:focus:bg-neutral-800 focus:border-sky-400 dark:focus:border-neutral-600 focus:ring-4 focus:ring-sky-100 dark:focus:ring-neutral-700 transition-all">
    </div>
    @foreach ($filters as $filter)
        <select name="{{ $filter['name'] }}" onchange="this.form.submit()" class="px-3 py-2 bg-sky-50/70 dark:bg-neutral-800 border border-sky-100 dark:border-neutral-700 rounded-xl text-xs text-slate-700 dark:text-neutral-200 focus:outline-none focus:border-sky-400 dark:focus:border-neutral-600 focus:ring-4 focus:ring-sky-100 dark:focus:ring-neutral-700 transition-all">
            <option value="semua">{{ $filter['allLabel'] ?? 'Semua' }}</option>
            @foreach ($filter['options'] as $value => $label)
                <option value="{{ $value }}" {{ request($filter['name']) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    @endforeach
    @if($hasActive)
        <a href="{{ $action }}" class="px-3 py-2 text-xs font-bold text-slate-500 dark:text-neutral-400 hover:text-slate-700 dark:hover:text-neutral-200 transition">Reset</a>
    @endif
</form>
