<?php $__env->startSection('title', 'Kelola Anggota'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Kelola Anggota</h1>
            <p class="text-xs text-slate-400 mt-1">Total: <?php echo e($totalAnggotas); ?> anggota</p>
        </div>
    </div>

    <?php echo $__env->make('partials.table-filters', [
        'action' => route('ketua.anggota.index'),
        'placeholder' => 'Cari nama / NIS anggota...',
        'filters' => [
            ['name' => 'status', 'allLabel' => 'Semua Status', 'options' => ['diterima' => 'Aktif', 'peringatan' => 'Peringatan', 'nonaktif' => 'Nonaktif', 'keluar' => 'Keluar']],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="bg-white p-4 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 mb-4">
        <p class="text-xs font-bold text-slate-500 mb-3 flex items-center gap-2">
            <span class="w-7 h-7 rounded-xl bg-gradient-to-br from-amber-100 to-yellow-100 border border-amber-200 flex items-center justify-center text-amber-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-8.24 14a2 2 0 001.73 3h16.44a2 2 0 001.73-3l-8.24-14a2 2 0 00-3.42 0z"/>
                </svg>
            </span>
            Status Keanggotaan
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-[11px]">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"></span>
                <span class="text-slate-500"><strong class="text-emerald-600">Aktif</strong> — anggota yang masih bergabung & mengikuti ekskul.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shadow-sm shadow-amber-200"></span>
                <span class="text-slate-500"><strong class="text-amber-600">Peringatan</strong> (<?php echo e($peringatanCount); ?>) — anggota yang diberi notifikasi korektif; tetap terhitung aktif.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-sm shadow-rose-200"></span>
                <span class="text-slate-500"><strong class="text-rose-500">Nonaktif</strong> (<?php echo e($nonaktifCount); ?>) — anggota yang dinonaktifkan oleh ketua.</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-400 shadow-sm shadow-slate-200"></span>
                <span class="text-slate-500"><strong class="text-slate-600">Keluar</strong> (<?php echo e($keluarCount); ?>) — anggota yang mengajukan keluar dan disetujui.</span>
            </div>
        </div>
    </div>

    <div class="ketua-card-list bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="card-table ketua-card-anggota w-full text-left text-xs md:text-sm">
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                <tr>
                    <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    <?php echo $__env->make('partials.th-sort', ['label' => 'NIS', 'key' => 'nis', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('partials.th-sort', ['label' => 'Nama', 'key' => 'nama', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Kelas</th>
                    <?php echo $__env->make('partials.th-sort', ['label' => 'Status', 'key' => 'status', 'sort' => $sort, 'direction' => $direction, 'class' => 'px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <th class="px-3 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                <?php $__empty_1 = true; $__currentLoopData = $anggotas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anggota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $isSelf = $anggota->siswa_id === (auth()->user()->siswa->id ?? null);
                    ?>
                    <tr class="hover:bg-sky-50/50 transition <?php echo e(in_array($anggota->status, ['nonaktif', 'keluar']) ? 'opacity-60' : ''); ?>">
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-500"><?php echo e($loop->iteration); ?></td>
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-600"><?php echo e($anggota->siswa->nis); ?></td>
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-800">
                            <?php echo e($anggota->siswa->nama); ?>

                            <?php if($isSelf): ?>
                                <span class="ml-1 px-1.5 py-0.5 bg-sky-100 text-sky-700 border border-sky-200 rounded-md text-[9px] font-bold">Kamu</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap text-slate-600"><?php echo e($anggota->siswa->kelas->nama ?? '-'); ?></td>
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap">
                            <?php if($anggota->status === 'diterima'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            <?php elseif($anggota->status === 'peringatan'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-amber-50 to-yellow-50 text-amber-700 border border-amber-200 text-xs font-semibold rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-8.24 14a2 2 0 001.73 3h16.44a2 2 0 001.73-3l-8.24-14a2 2 0 00-3.42 0z"/>
                                    </svg>
                                    Peringatan
                                </span>
                            <?php elseif($anggota->status === 'keluar'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Keluar
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-600 border border-rose-200 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Nonaktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 md:px-6 py-3 md:py-3.5 whitespace-nowrap">
                            <?php if($isSelf): ?>
                                <span class="text-slate-300 text-[11px]">—</span>
                            <?php else: ?>
                                <div class="flex flex-wrap items-center gap-2">
                                    <?php if($anggota->status === 'diterima'): ?>
                                        <form action="<?php echo e(route('ketua.anggota.update-status', $anggota)); ?>" method="POST"
                                            onsubmit="return confirm('Beri peringatan kepada <?php echo e(addslashes($anggota->siswa->nama)); ?>? Siswa akan menerima alert peringatan di akunnya. Status tetap aktif.');">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="peringatan">
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 md:px-3 py-1.5 bg-gradient-to-r from-amber-100 to-yellow-100 text-amber-700 border border-amber-200 font-semibold rounded-full hover:from-amber-200 hover:to-yellow-200 transition text-xs">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-8.24 14a2 2 0 001.73 3h16.44a2 2 0 001.73-3l-8.24-14a2 2 0 00-3.42 0z"/>
                                                </svg>
                                                Peringatan
                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('ketua.anggota.update-status', $anggota)); ?>" method="POST"
                                            onsubmit="return confirm('Nonaktifkan <?php echo e(addslashes($anggota->siswa->nama)); ?>? Siswa tidak akan terhitung anggota aktif ekskul.');">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="nonaktif">
                                            <button type="submit" class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-200 text-xs font-semibold rounded-full transition">Nonaktifkan</button>
                                        </form>
                                    <?php elseif($anggota->status === 'peringatan'): ?>
                                        <form action="<?php echo e(route('ketua.anggota.update-status', $anggota)); ?>" method="POST">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="diterima">
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-full transition">Aktifkan</button>
                                        </form>
                                        <form action="<?php echo e(route('ketua.anggota.update-status', $anggota)); ?>" method="POST"
                                            onsubmit="return confirm('Nonaktifkan <?php echo e(addslashes($anggota->siswa->nama)); ?>? Siswa tidak akan terhitung anggota aktif ekskul.');">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="nonaktif">
                                            <button type="submit" class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-200 text-xs font-semibold rounded-full transition">Nonaktifkan</button>
                                        </form>
                                    <?php else: ?>
                                        <form action="<?php echo e(route('ketua.anggota.update-status', $anggota)); ?>" method="POST">
                                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                            <input type="hidden" name="status" value="diterima">
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-full transition">Aktifkan</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada anggota aktif atau nonaktif.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        <?php echo $__env->make('partials.table-pagination', ['rows' => $anggotas, 'label' => 'anggota'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('ketua.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH X:\required\laragon\www\Soul\resources\views/ketua/anggota/index.blade.php ENDPATH**/ ?>