<?php $__env->startSection('title', 'Katalog Ekskul'); ?>

<?php $__env->startSection('search'); ?>
    <form method="GET" action="<?php echo e(route('siswa.katalog')); ?>" class="relative w-full max-w-md hidden sm:block">
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </span>
        <input type="text" name="cari" value="<?php echo e(request('cari')); ?>" placeholder="Cari ekskul..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Header dengan Tombol Daftar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-up">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900">Katalog Ekskul</h1>
            <p class="text-xs text-slate-400 mt-1 font-medium">Temukan ekskul yang sesuai dengan minatmu</p>
        </div>
        <?php if(!$isRegistered && !$isPending): ?>
            <a href="<?php echo e(route('siswa.daftar-ekskul')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-2xl shadow-lg shadow-sky-200 transition-all hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Daftar Ekskul
            </a>
        <?php elseif($isPending): ?>
            <span class="px-4 py-2.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-2xl border border-amber-200 flex items-center gap-1.5 shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                Menunggu Verifikasi
            </span>
        <?php else: ?>
            <span class="px-4 py-2.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-2xl border border-emerald-200 shadow-sm">
                Sudah Terdaftar
            </span>
        <?php endif; ?>
    </div>

    <!-- Filter -->
    <div class="flex gap-2 flex-wrap animate-fade-up" style="animation-delay: .1s">
        <button class="px-4 py-2 bg-gradient-to-r from-sky-400 to-blue-500 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200">Semua</button>
        <button class="catalog-filter px-4 py-2 bg-white text-slate-600 text-xs font-bold rounded-xl border border-sky-100 shadow-sm hover:bg-sky-50 transition-all">Olahraga</button>
        <button class="catalog-filter px-4 py-2 bg-white text-slate-600 text-xs font-bold rounded-xl border border-sky-100 shadow-sm hover:bg-sky-50 transition-all">Seni</button>
        <button class="catalog-filter px-4 py-2 bg-white text-slate-600 text-xs font-bold rounded-xl border border-sky-100 shadow-sm hover:bg-sky-50 transition-all">Akademik</button>
    </div>

    <!-- Daftar Ekskul -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
        <?php $__empty_1 = true; $__currentLoopData = $ekskuls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ekskul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="catalog-card h-full flex flex-col bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 p-5 hover:shadow-xl hover:-translate-y-1 hover:border-sky-200 transition-all duration-300 animate-fade-up" style="animation-delay: <?php echo e($index * 0.05); ?>s">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 shrink-0 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white font-extrabold flex items-center justify-center text-sm shadow-lg shadow-sky-200 uppercase">
                    <?php echo e(substr($ekskul->nama_ekskul, 0, 2)); ?>

                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-extrabold text-slate-900 truncate"><?php echo e($ekskul->nama_ekskul); ?></h3>
                    <p class="text-xs text-slate-400 truncate font-medium">Pembina: <?php echo e($ekskul->pembina->nama ?? '-'); ?></p>
                </div>
            </div>
            <p class="text-xs text-slate-600 mb-2 line-clamp-2 leading-relaxed"><?php echo e($ekskul->deskripsi ?? 'Deskripsi belum tersedia'); ?></p>

            <?php if($ekskul->is_open_recruitment): ?>
                <div class="mb-4">
                    <span class="recruitment-open-badge text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-1 rounded-full">Buka Pendaftaran</span>
                    <p class="text-xs text-slate-400 mt-2 font-medium"><?php echo e($ekskul->jadwal ?? 'Jadwal belum diatur'); ?></p>
                </div>
            <?php else: ?>
                <div class="mb-4">
                    <span class="catalog-closed-badge text-xs font-bold text-red-500 bg-red-50 border border-red-100 px-2 py-1 rounded-full">Tidak Membuka Pendaftaran</span>
                    <p class="text-xs text-slate-400 mt-2 font-medium"><?php echo e($ekskul->jadwal ?? 'Jadwal belum diatur'); ?></p>
                </div>
            <?php endif; ?>

            <div class="mt-auto pt-4 border-t border-sky-50 space-y-2">
                <a href="<?php echo e(route('ekskul.detail', $ekskul)); ?>" class="flex w-full items-center justify-center gap-1 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-sky-200 text-center hover:-translate-y-0.5">
                    Lihat Ekskul
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <?php if($ekskul->is_open_recruitment): ?>
                    <a href="<?php echo e(route('siswa.form-daftar', $ekskul->id)); ?>" class="block w-full py-2 text-center text-sky-600 hover:text-sky-700 text-xs font-bold rounded-xl border border-sky-200 hover:bg-sky-50 transition-all">
                        Daftar
                    </a>
                <?php else: ?>
                    <span class="catalog-unavailable block w-full py-2 text-center text-slate-400 text-xs font-bold rounded-xl border border-slate-200 cursor-not-allowed">
                        Pendaftaran Ditutup
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="catalog-card col-span-2 lg:col-span-3 bg-white rounded-3xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center py-12">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-300 mb-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <p class="text-xs font-semibold text-slate-500">Belum ada ekskul yang tersedia.</p>
        </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.siswa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH X:\required\laragon\www\Soul\resources\views/siswa/katalog.blade.php ENDPATH**/ ?>