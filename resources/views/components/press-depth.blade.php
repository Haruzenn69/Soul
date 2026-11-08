@props([
    'as' => 'button',
    'href' => null,
    'type' => 'button',
    'depth' => 4,
    'tilt' => 7,
    'disabled' => false,
    'variant' => 'default',
    'size' => 'md',
    'active' => false,
    'align' => 'center',
    'block' => false,
])

@php
    $faces = [
        'default' => 'border-stone-200 bg-white text-stone-700 dark:border-white/15 dark:bg-[#1D1D1A] dark:text-stone-200',
        'primary' => 'border-sky-600 bg-sky-600 text-white dark:border-sky-500 dark:bg-sky-500',
        'danger' => 'border-rose-600 bg-rose-600 text-white dark:border-rose-500 dark:bg-rose-500',
        'success' => 'border-emerald-600 bg-emerald-600 text-white dark:border-emerald-500 dark:bg-emerald-500',
    ];
    $face = $faces[$variant] ?? $faces['default'];
    $baseClass = 'bg-stone-300 dark:bg-white/20';
    if ($variant === 'primary') { $baseClass = 'bg-sky-800'; }
    if ($variant === 'danger') { $baseClass = 'bg-rose-800'; }
    if ($variant === 'success') { $baseClass = 'bg-emerald-800'; }
    if ($variant === 'filter') {
        $baseClass = $active ? 'bg-stone-300' : 'bg-sky-800';
        $face = $active
            ? 'border-white bg-white text-sky-700'
            : 'border-sky-500 bg-sky-600 text-white';
    }
    $sizes = [
        'sm' => 'h-8 px-3 text-xs',
        'md' => 'h-9 px-3.5 text-[13px]',
        'lg' => 'h-11 px-5 text-sm',
        'auto' => 'min-h-9 px-3.5 py-2 text-[13px]',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $alignClass = $align === 'start' ? 'justify-start text-left' : 'justify-center';
    $blockClass = $block ? 'w-full' : '';
    $outerClass = 'group relative inline-flex select-none rounded-xl align-middle outline-none disabled:opacity-50';
    $disabledAttr = $disabled ? 'true' : 'false';
@endphp

@if ($as === 'a')
    <a href="{{ $href }}"
       x-data="{
            pressed: false,
            ox: 0,
            oy: 0,
            reduce: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
            start(e) {
                if ({{ $disabledAttr }}) return;
                if (e.pointerType === 'mouse' && e.button !== 0) return;
                const r = e.currentTarget.getBoundingClientRect();
                this.ox = Math.max(-1, Math.min(1, ((e.clientX - r.left) / r.width) * 2 - 1));
                this.oy = Math.max(-1, Math.min(1, ((e.clientY - r.top) / r.height) * 2 - 1));
                this.pressed = true;
            },
            end() { this.pressed = false; },
            keyStart(e) {
                if (e.repeat) return;
                if (e.key === ' ' || e.key === 'Enter') this.pressed = true;
            },
            keyEnd(e) {
                if (e.key === ' ' || e.key === 'Enter' || e.key === 'Escape') this.pressed = false;
            }
       }"
       x-on:pointerdown="start($event)"
       x-on:pointerup="end()"
       x-on:pointercancel="end()"
       x-on:pointerleave="end()"
       x-on:keydown="keyStart($event)"
       x-on:keyup="keyEnd($event)"
       x-on:blur="end()"
       style="padding-bottom: {{ $depth }}px; touch-action: manipulation; -webkit-tap-highlight-color: transparent;"
       {{ $attributes->class([$outerClass]) }}>
        <span aria-hidden="true" style="top: {{ $depth }}px" class="absolute inset-x-0 bottom-0 rounded-xl {{ $baseClass }}"></span>
        <span
            
            x-bind:style="`transition: transform 180ms cubic-bezier(.2,.9,.3,1.2); transform: perspective(340px) translateY(${pressed ? {{ $depth }} : 0}px) rotateX(${pressed && !reduce ? -oy * {{ $tilt }} : 0}deg) rotateY(${pressed && !reduce ? ox * {{ $tilt }} : 0}deg)`"
            class="relative inline-flex items-center {{ $alignClass }} {{ $blockClass }} gap-2 rounded-xl border font-semibold {{ $face }} {{ $sizeClass }}">
            <span aria-hidden="true" class="pointer-events-none absolute inset-0 rounded-xl shadow-[inset_0_1.5px_0_rgba(255,255,255,0.55),inset_0_-1px_0_rgba(28,25,23,0.08)] transition-opacity" x-bind:class="pressed ? 'opacity-0' : 'opacity-100'"></span>
            {{ $slot }}
        </span>
    </a>
@else
    <button type="{{ $type }}"
            @if ($disabled) disabled @endif
            x-data="{
                pressed: false,
                ox: 0,
                oy: 0,
                reduce: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
                start(e) {
                    if ({{ $disabledAttr }}) return;
                    if (e.pointerType === 'mouse' && e.button !== 0) return;
                    const r = e.currentTarget.getBoundingClientRect();
                    this.ox = Math.max(-1, Math.min(1, ((e.clientX - r.left) / r.width) * 2 - 1));
                    this.oy = Math.max(-1, Math.min(1, ((e.clientY - r.top) / r.height) * 2 - 1));
                    this.pressed = true;
                },
                end() { this.pressed = false; },
                keyStart(e) {
                    if (e.repeat) return;
                    if (e.key === ' ' || e.key === 'Enter') this.pressed = true;
                },
                keyEnd(e) {
                    if (e.key === ' ' || e.key === 'Enter' || e.key === 'Escape') this.pressed = false;
                }
            }"
            x-on:pointerdown="start($event)"
            x-on:pointerup="end()"
            x-on:pointercancel="end()"
            x-on:pointerleave="end()"
            x-on:keydown="keyStart($event)"
            x-on:keyup="keyEnd($event)"
            x-on:blur="end()"
            style="padding-bottom: {{ $depth }}px; touch-action: manipulation; -webkit-tap-highlight-color: transparent;"
            {{ $attributes->class([$outerClass]) }}>
        <span aria-hidden="true" style="top: {{ $depth }}px" class="absolute inset-x-0 bottom-0 rounded-xl {{ $baseClass }}"></span>
        <span
            
            x-bind:style="`transition: transform 180ms cubic-bezier(.2,.9,.3,1.2); transform: perspective(340px) translateY(${pressed ? {{ $depth }} : 0}px) rotateX(${pressed && !reduce ? -oy * {{ $tilt }} : 0}deg) rotateY(${pressed && !reduce ? ox * {{ $tilt }} : 0}deg)`"
            class="relative inline-flex items-center {{ $alignClass }} {{ $blockClass }} gap-2 rounded-xl border font-semibold {{ $face }} {{ $sizeClass }}">
            <span aria-hidden="true" class="pointer-events-none absolute inset-0 rounded-xl shadow-[inset_0_1.5px_0_rgba(255,255,255,0.55),inset_0_-1px_0_rgba(28,25,23,0.08)] transition-opacity" x-bind:class="pressed ? 'opacity-0' : 'opacity-100'"></span>
            {{ $slot }}
        </span>
    </button>
@endif
