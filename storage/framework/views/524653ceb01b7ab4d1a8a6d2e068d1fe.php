<?php
    $searchCols = $searchCols ?? [1];
    $filterCols = $filterCols ?? [];
    $filterOptions = $filterOptions ?? [];
    $defaultSize = $defaultSize ?? 10;
?>
<div class="px-4 md:px-6 py-3 bg-sky-50/40 border-b border-sky-100 flex flex-wrap items-center gap-2.5" id="<?php echo e($tableId); ?>-tools">
    <div class="relative flex-1 min-w-[200px]">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <input type="search" id="<?php echo e($tableId); ?>-search" placeholder="Cari..." autocomplete="off"
            class="w-full pl-9 pr-4 py-2 bg-white/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
    </div>
    <?php if($filterCols): ?>
        <select id="<?php echo e($tableId); ?>-filter" class="px-3 py-2 bg-white/70 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
            <option value="">Semua Status</option>
            <?php $__currentLoopData = $filterOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    <?php endif; ?>
    <select id="<?php echo e($tableId); ?>-size" class="px-3 py-2 bg-white/70 border border-sky-100 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
        <?php $__currentLoopData = [5, 10, 20, 50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionSize): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($optionSize); ?>" <?php if($optionSize === $defaultSize): echo 'selected'; endif; ?>><?php echo e($optionSize); ?>/hal</option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <span id="<?php echo e($tableId); ?>-info" class="text-[10px] md:text-xs text-slate-400 font-semibold"></span>
    <span class="hidden sm:inline-flex items-center gap-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cari · Filter · Sort</span>
    <div class="flex items-center gap-1 ml-auto">
        <button type="button" id="<?php echo e($tableId); ?>-prev" class="w-7 h-7 rounded-lg bg-white border border-sky-100 text-slate-500 hover:bg-sky-50 transition flex items-center justify-center text-xs font-bold" aria-label="Sebelumnya">&lsaquo;</button>
        <span id="<?php echo e($tableId); ?>-page" class="text-[10px] font-semibold text-slate-500 w-20 text-center"></span>
        <button type="button" id="<?php echo e($tableId); ?>-next" class="w-7 h-7 rounded-lg bg-white border border-sky-100 text-slate-500 hover:bg-sky-50 transition flex items-center justify-center text-xs font-bold" aria-label="Berikutnya">&rsaquo;</button>
    </div>
</div>

<script>
(function () {
    var table = document.getElementById('<?php echo e($tableId); ?>');
    if (!table) return;

    var tbody = table.querySelector('tbody');
    var rows = Array.from(tbody.querySelectorAll('tr')).filter(function (tr) {
        return !tr.querySelector('td[colspan]');
    });
    var searchCols = <?php echo e(json_encode($searchCols)); ?>;
    var filterCols = <?php echo e(json_encode($filterCols)); ?>;
    var size = <?php echo e($defaultSize); ?>;
    var page = 1;
    var search = '';
    var filter = '';
    var sortIndex = -1;
    var sortAsc = true;

    var searchInput = document.getElementById('<?php echo e($tableId); ?>-search');
    var filterSel = filterCols.length ? document.getElementById('<?php echo e($tableId); ?>-filter') : null;
    var sizeSel = document.getElementById('<?php echo e($tableId); ?>-size');
    var infoEl = document.getElementById('<?php echo e($tableId); ?>-info');
    var pageEl = document.getElementById('<?php echo e($tableId); ?>-page');
    var prevBtn = document.getElementById('<?php echo e($tableId); ?>-prev');
    var nextBtn = document.getElementById('<?php echo e($tableId); ?>-next');

    function cellValue(row, index) {
        var cell = row.cells[index];
        if (!cell) return '';
        var select = cell.querySelector('select');
        if (select) return (select.value || '').toLowerCase();
        return (cell.innerText || '').trim().toLowerCase();
    }

    function render() {
        var keyword = search.trim().toLowerCase();
        var list = rows.filter(function (row) {
            var okSearch = !keyword || searchCols.some(function (i) { return cellValue(row, i).indexOf(keyword) !== -1; });
            var okFilter = !filter || filterCols.some(function (i) { return cellValue(row, i) === filter; });
            return okSearch && okFilter;
        });

        if (sortIndex > -1) {
            list.sort(function (a, b) {
                var va = (a.cells[sortIndex]?.innerText || '').trim();
                var vb = (b.cells[sortIndex]?.innerText || '').trim();
                var na = parseFloat(String(va).replace(',', '.'));
                var nb = parseFloat(String(vb).replace(',', '.'));
                var cmp = (!isNaN(na) && !isNaN(nb)) ? na - nb : va.localeCompare(vb);
                return sortAsc ? cmp : -cmp;
            });
        }

        var pages = Math.max(1, Math.ceil(list.length / size));
        if (page > pages) page = pages;
        var start = (page - 1) * size;
        var slice = list.slice(start, start + size);

        rows.forEach(function (row) { row.style.display = 'none'; });
        slice.forEach(function (row, idx) {
            var noCell = row.cells[0];
            if (noCell && !noCell.querySelector('select, input')) noCell.textContent = start + idx + 1;
            row.style.display = '';
        });

        infoEl.textContent = list.length ? (start + 1) + '\u2013' + Math.min(start + size, list.length) + ' dari ' + list.length : 'Tidak ada data';
        pageEl.textContent = 'Hal ' + page + '/' + pages;
        prevBtn.disabled = page <= 1;
        nextBtn.disabled = page >= pages;
    }

    searchInput.addEventListener('input', function () { page = 1; search = searchInput.value; render(); });
    if (filterSel) filterSel.addEventListener('change', function () { page = 1; filter = filterSel.value; render(); });
    sizeSel.addEventListener('change', function () { page = 1; size = parseInt(sizeSel.value, 10) || 10; render(); });
    prevBtn.addEventListener('click', function () { if (page > 1) { page--; render(); } });
    nextBtn.addEventListener('click', function () { page++; render(); });

    table.querySelectorAll('th[data-sort-index]').forEach(function (th) {
        th.addEventListener('click', function () {
            var index = parseInt(th.dataset.sortIndex, 10);
            if (sortIndex === index) {
                sortAsc = !sortAsc;
            } else {
                sortIndex = index;
                sortAsc = true;
            }
            th.querySelectorAll('.ct-caret').forEach(function (el) {
                el.textContent = sortAsc ? '\u25B2' : '\u25BC';
            });
            render();
        });
        var caret = document.createElement('span');
        caret.className = 'ct-caret ml-1 text-[9px] leading-none text-sky-400';
        caret.textContent = '\u21C5';
        th.appendChild(caret);
    });

    render();
})();
</script><?php /**PATH C:\Users\ASUS\Soul\resources\views/partials/table-client-tools.blade.php ENDPATH**/ ?>