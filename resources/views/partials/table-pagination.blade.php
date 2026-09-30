@php
    $paginator = $rows ?? collect();
    $label = $label ?? 'data';
@endphp
@if(method_exists($paginator, 'hasPages') && $paginator->hasPages())
    <div class="px-3 md:px-6 py-3 border-t border-sky-50 flex flex-col sm:flex-row justify-between items-center gap-2">
        <span class="text-[10px] md:text-xs text-slate-400">
            Menampilkan {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} dari {{ $paginator->total() }} {{ $label }}
        </span>
        <div class="flex gap-1">
            {{ $paginator->links() }}
        </div>
    </div>
@endif