<?php
    $paginator = $rows ?? collect();
    $label = $label ?? 'data';
?>
<?php if(method_exists($paginator, 'hasPages') && $paginator->hasPages()): ?>
    <div class="px-3 md:px-6 py-3 border-t border-sky-50 flex flex-col sm:flex-row justify-between items-center gap-2">
        <span class="text-[10px] md:text-xs text-slate-400">
            Menampilkan <?php echo e($paginator->firstItem()); ?>&ndash;<?php echo e($paginator->lastItem()); ?> dari <?php echo e($paginator->total()); ?> <?php echo e($label); ?>

        </span>
        <div class="flex gap-1">
            <?php echo e($paginator->links()); ?>

        </div>
    </div>
<?php endif; ?><?php /**PATH X:\required\laragon\www\Soul\resources\views/partials/table-pagination.blade.php ENDPATH**/ ?>