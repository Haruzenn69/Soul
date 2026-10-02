<?php
    $statusList = ['hadir', 'sakit', 'izin', 'alpha'];
    $statusBadge = [
        'hadir' => ['bg-emerald-100 text-emerald-700', 'H', 'Hadir'],
        'sakit' => ['bg-violet-100 text-violet-700', 'S', 'Sakit'],
        'izin'  => ['bg-amber-100 text-amber-700', 'I', 'Izin'],
        'alpha' => ['bg-rose-100 text-rose-700', 'A', 'Alpha'],
        null    => ['bg-slate-100 text-slate-400', '–', 'Belum diabsen'],
    ];
    $matriksTitle = $matriksTitle ?? 'Matriks Kehadiran';
    $eventCount = isset($eventKegiatans) ? $eventKegiatans->count() : 0;
    $matriksSubtitle = $matriksSubtitle ?? ($rows->count().' anggota · '.$kegiatans->count().' kegiatan rutin'.($eventCount > 0 ? ' · '.$eventCount.' event' : ''));
?>


<div class="flex items-center gap-4 flex-wrap bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 px-5 py-4 animate-fade-up">
    <span class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Keterangan</span>
    <?php $__currentLoopData = $statusList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php [$bg, $letter, $label] = $statusBadge[$status]; ?>
        <span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600">
            <span class="attendance-status attendance-status--<?php echo e($status); ?> w-5 h-5 rounded-md <?php echo e($bg); ?> flex items-center justify-center text-[10px] font-extrabold"><?php echo e($letter); ?></span>
            <?php echo e($label); ?>

        </span>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <span class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-600">
        <span class="attendance-status attendance-status--unmarked w-5 h-5 rounded-md bg-slate-100 text-slate-400 flex items-center justify-center text-[10px] font-extrabold">–</span>
        Belum diabsen
    </span>
</div>


<div class="bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden animate-fade-up" style="animation-delay: .1s">
    <div class="px-5 md:px-6 py-4 flex justify-between items-center border-b border-sky-50">
        <div>
            <h2 class="text-sm font-extrabold text-slate-900"><?php echo e($matriksTitle); ?></h2>
            <p class="text-[11px] text-slate-400 mt-0.5"><?php echo e($matriksSubtitle); ?></p>
        </div>
        <?php if(isset($ekskul) && $ekskul): ?>
            <span class="text-[10px] font-semibold text-sky-600 bg-sky-50 border border-sky-100 px-3 py-1.5 rounded-full"><?php echo e($ekskul->nama_ekskul); ?></span>
        <?php endif; ?>
    </div>

    <?php echo $__env->make('partials.table-client-tools', [
        'tableId' => 'rekap-matriks-table',
        'searchCols' => [1],
        'filterCols' => [],
        'filterOptions' => [],
        'defaultSize' => 10,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="overflow-x-auto">
        <table id="rekap-matriks-table" class="card-table w-full text-left text-xs md:text-sm min-w-max">
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                <tr>
                    <th class="px-4 md:px-5 py-3 font-semibold text-slate-500 sticky left-0 bg-gradient-to-r from-sky-50 to-blue-50 z-10" rowspan="2">No</th>
                    <th data-sort-index="1" class="px-4 md:px-5 py-3 font-semibold text-slate-500 sticky left-10 bg-gradient-to-r from-sky-50 to-blue-50 z-10 cursor-pointer select-none hover:text-slate-800 transition" rowspan="2" title="Klik untuk urutkan">Nama</th>
                    <th colspan="<?php echo e(max($kegiatans->count(), 1)); ?>" class="px-3 py-3 font-semibold text-slate-500 text-center">Pertemuan (tanggal & materi)</th>
                    <th colspan="5" class="px-3 py-3 font-semibold text-slate-500 text-center">Total</th>
                </tr>
                <tr>
                    <?php $__empty_1 = true; $__currentLoopData = $kegiatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <th class="px-2.5 py-2.5 font-bold text-slate-600 text-center whitespace-nowrap" title="<?php echo e($kegiatan->materi); ?>">
                            <?php echo e($kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('d M') : 'Keg. #'.$kegiatan->id); ?>

                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <th class="px-3 py-2.5 font-semibold text-slate-400 text-center">Belum ada kegiatan</th>
                    <?php endif; ?>
                    <th class="px-3 py-2.5 font-bold text-emerald-600 text-center">Hadir</th>
                    <th class="px-3 py-2.5 font-bold text-amber-600 text-center">Izin</th>
                    <th class="px-3 py-2.5 font-bold text-violet-600 text-center">Sakit</th>
                    <th class="px-3 py-2.5 font-bold text-rose-600 text-center">Alpha</th>
                    <th class="px-3 py-2.5 font-bold text-slate-600 text-center">% Kehadiran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $anggota = $row->pendaftaran->siswa;
                        $persen = (float) $row->persentaseKehadiran;
                    ?>
                    <tr class="hover:bg-sky-50/50 transition">
                        <td class="px-4 md:px-5 py-3 whitespace-nowrap sticky left-0 bg-white z-10"><?php echo e($loop->iteration); ?></td>
                        <td class="px-4 md:px-5 py-3 whitespace-nowrap sticky left-10 bg-white z-10">
                            <span class="font-bold text-slate-800"><?php echo e($anggota->nama); ?></span>
                            <span class="block text-[10px] text-slate-400 mt-0.5"><?php echo e($anggota->kelas?->nama ?? 'Tanpa kelas'); ?></span>
                        </td>
                        <?php $__empty_2 = true; $__currentLoopData = $kegiatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                            <?php
                                $status = $row->sel[$kegiatan->id] ?? null;
                                [$bg, $letter] = $statusBadge[$status];
                            ?>
                            <td class="px-2.5 py-3 text-center" title="<?php echo e($kegiatan->materi); ?> · <?php echo e($statusBadge[$status][2]); ?>">
                                <span class="attendance-status attendance-status--<?php echo e($status ?? 'unmarked'); ?> w-6 h-6 inline-flex items-center justify-center rounded-md <?php echo e($bg); ?> text-[10px] font-extrabold"><?php echo e($letter); ?></span>
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                            <td class="px-3 py-3 text-center text-slate-300">–</td>
                        <?php endif; ?>
                        <td class="px-3 py-3 text-center font-bold text-emerald-600"><?php echo e($row->hadir); ?></td>
                        <td class="px-3 py-3 text-center font-bold text-amber-600"><?php echo e($row->izin); ?></td>
                        <td class="px-3 py-3 text-center font-bold text-violet-600"><?php echo e($row->sakit); ?></td>
                        <td class="px-3 py-3 text-center font-bold text-rose-600"><?php echo e($row->alpha); ?></td>
                        <td class="px-3 py-3 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="font-bold text-slate-700"><?php echo e($row->persentaseKehadiran); ?>%</span>
                                <span class="w-10 h-1.5 rounded-full bg-slate-100 overflow-hidden inline-block">
                                    <span class="block h-full rounded-full <?php echo e($persen >= 75 ? 'bg-emerald-400' : ($persen >= 50 ? 'bg-amber-400' : 'bg-rose-400')); ?>" style="width: <?php echo e(min($persen, 100)); ?>%"></span>
                                </span>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="<?php echo e($kegiatans->count() + 6); ?>" class="px-4 py-12 text-center text-slate-400">
                            <?php echo e($emptyText ?? 'Belum ada anggota aktif untuk direkap.'); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div><?php /**PATH C:\Users\ASUS\Soul\resources\views/partials/rekap-matriks.blade.php ENDPATH**/ ?>