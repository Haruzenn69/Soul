<?php
    $current = isset($sort) && $sort === $key;
    $nextDir = $current && isset($direction) && $direction === 'asc' ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort' => $key, 'direction' => $nextDir]);
    $class = $class ?? 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap';
?>
<th class="<?php echo e($class); ?>">
    <a href="<?php echo e($url); ?>" class="inline-flex items-center gap-1.5 group hover:text-slate-800 transition" title="Urutkan: <?php echo e($label); ?>">
        <span><?php echo e($label); ?></span>
        <span class="text-[10px] leading-none <?php echo e($current ? 'text-sky-600' : 'text-slate-300 group-hover:text-slate-400'); ?>"><?php echo e($current ? ($direction === 'asc' ? '▲' : '▼') : '↕'); ?></span>
    </a>
</th><?php /**PATH X:\required\laragon\www\Soul\resources\views/partials/th-sort.blade.php ENDPATH**/ ?>