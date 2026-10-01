<?php $__env->startSection('title', 'Daftar Pengajuan Keluar'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Daftar Pengajuan Keluar</h1>
            <p class="text-xs text-slate-400 mt-1">Total: <?php echo e($total); ?> pengajuan</p>
        </div>
    </div>

    <?php echo $__env->make('partials.table-filters', [
        'action' => route('ketua.pengajuan-keluar.index'),
        'placeholder' => 'Cari nama / alasan...',
        'filters' => [
            ['name' => 'status', 'allLabel' => 'Semua Status', 'options' => ['pending' => 'Pending', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak']],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Table Card -->
    <div class="ketua-card-list bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="card-table ketua-card-pengajuan w-full text-left text-xs md:text-sm">
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    <?php echo $__env->make('partials.th-sort', ['label' => 'Nama', 'key' => 'nama', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('partials.th-sort', ['label' => 'Tanggal', 'key' => 'tanggal_pengajuan', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Alasan</th>
                    <?php echo $__env->make('partials.th-sort', ['label' => 'Status', 'key' => 'status', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                <?php $__empty_1 = true; $__currentLoopData = $pengajuanKeluars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pengajuan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-sky-50/50 transition" data-card-href="<?php echo e(route('ketua.pengajuan-keluar.show', $pengajuan)); ?>">
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap"><?php echo e($loop->iteration); ?></td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap"><?php echo e($pengajuan->siswa->nama); ?></td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap"><?php echo e($pengajuan->tanggal_pengajuan->format('d/m/Y')); ?></td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap max-w-xs truncate"><?php echo e(Str::limit($pengajuan->alasan, 30)); ?></td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <?php if($pengajuan->status === 'pending'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">Pending</span>
                            <?php elseif($pengajuan->status === 'diterima'): ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">Diterima</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">Ditolak</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <a href="<?php echo e(route('ketua.pengajuan-keluar.show', $pengajuan)); ?>" class="card-detail-link inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-700 font-semibold rounded-xl hover:from-sky-200 hover:to-blue-200 transition text-xs">Detail</a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-4 md:px-6 py-10 text-center text-slate-400">
                            <p class="text-sm font-medium">Belum ada pengajuan keluar</p>
                            <p class="text-xs mt-1">Pengajuan siswa akan muncul di halaman ini.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php echo $__env->make('partials.table-pagination', ['rows' => $pengajuanKeluars, 'label' => 'pengajuan'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('ketua.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH X:\required\laragon\www\Soul\resources\views/ketua/pengajuan-keluar/index.blade.php ENDPATH**/ ?>