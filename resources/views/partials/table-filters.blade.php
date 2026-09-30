@php
    $filters = $filters ?? [];
    $hasActive = request()->filled('cari') || collect($filters)->contains(fn ($f) => request($f['name']) && request($f['name']) !== 'semua');
@endphp
<form method="GET" action="{{ $action }}" class="white-card-filter bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 flex flex-wrap items-center gap-3">
    <div class="relative flex-1 min-w-[220px]">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <input type="search" name="cari" value="{{ request('cari') }}" placeholder="{{ $placeholder ?? 'Cari...' }}"
            class="w-full pl-9 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
    </div>
    @foreach ($filters as $filter)
        <select name="{{ $filter['name'] }}" onchange="this.form.submit()" class="px-3 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
            <option value="semua">{{ $filter['allLabel'] ?? 'Semua' }}</option>
            @foreach ($filter['options'] as $value => $label)
                <option value="{{ $value }}" {{ request($filter['name']) === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    @endforeach
    @if($hasActive)
        <a href="{{ $action }}" class="px-3 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 transition">Reset</a>
    @endif
</form>