@php
    $current = isset($sort) && $sort === $key;
    $nextDir = $current && isset($direction) && $direction === 'asc' ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort' => $key, 'direction' => $nextDir]);
    $class = $class ?? 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap';
@endphp
<th class="{{ $class }}">
    <a href="{{ $url }}" class="inline-flex items-center gap-1.5 group hover:text-slate-800 dark:hover:text-neutral-200 transition" title="Urutkan: {{ $label }}">
        <span>{{ $label }}</span>
        <span class="text-[10px] leading-none {{ $current ? 'text-sky-600 dark:text-sky-400' : 'text-slate-300 dark:text-neutral-500 group-hover:text-slate-400 dark:group-hover:text-neutral-300' }}">{{ $current ? ($direction === 'asc' ? '▲' : '▼') : '↕' }}</span>
    </a>
</th>
