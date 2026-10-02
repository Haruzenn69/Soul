<?php $__env->startSection('title', 'Data Pembina'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $sortLink = function (string $column) use ($sort, $direction) {
        $nextDirection = $sort === $column && $direction === 'asc' ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $nextDirection, 'page' => null]);
    };

    $sortIcon = function (string $column) use ($sort, $direction) {
        if ($sort !== $column) {
            return 'M8 9l4-4 4 4M8 15l4 4 4-4';
        }
        return $direction === 'asc' ? 'M8 15l4 4 4-4' : 'M8 9l4-4 4 4';
    };

    $hasFilter = request()->filled(['q', 'jenis_kelamin']);
?>

<div class="space-y-5 animate-fade-up">

    
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                    Data Pembina
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Daftar seluruh pembina ekskul. Kelola penugasan ekskul yang dibina oleh masing-masing pembina di sini.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="<?php echo e(route('kesiswaan.users.create')); ?>"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Pembina
                </a>
            </div>
        </div>
    </div>

    
    <?php if($errors->any()): ?>
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded-2xl shadow-sm">
            <ul class="list-disc list-inside space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('kesiswaan.pembina.index')); ?>" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm">
        <input type="hidden" name="sort" value="<?php echo e($sort); ?>">
        <input type="hidden" name="direction" value="<?php echo e($direction); ?>">

        <div class="flex flex-col lg:flex-row gap-3 items-stretch">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Cari nama, NIP, email, atau no. telp..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <select name="jenis_kelamin" class="w-full lg:w-44 px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                <option value="">Semua Jenis Kelamin</option>
                <option value="laki-laki" <?php echo e(request('jenis_kelamin') === 'laki-laki' ? 'selected' : ''); ?>>Laki-laki</option>
                <option value="perempuan" <?php echo e(request('jenis_kelamin') === 'perempuan' ? 'selected' : ''); ?>>Perempuan</option>
            </select>

            <div class="flex items-center gap-2 shrink-0">
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Filter
                </button>
                <?php if($hasFilter || $sort !== 'nama' || $direction !== 'asc'): ?>
                    <a href="<?php echo e(route('kesiswaan.pembina.index')); ?>"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </form>

    
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[11px] font-bold text-slate-400 tracking-wider uppercase">
                        <th class="py-3.5 px-5">
                            <a href="<?php echo e($sortLink('nama')); ?>" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Pembina
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($sortIcon('nama')); ?>"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="<?php echo e($sortLink('nip')); ?>" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                NIP
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($sortIcon('nip')); ?>"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="<?php echo e($sortLink('jenis_kelamin')); ?>" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Jenis Kelamin
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($sortIcon('jenis_kelamin')); ?>"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">No. Telp</th>
                        <th class="py-3.5 px-5">Ekskul Dibina</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <?php $__empty_1 = true; $__currentLoopData = $pembinas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-sky-50/30 transition">
                            
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0 overflow-hidden">
                                        <?php if($p->foto_url): ?>
                                            <img src="<?php echo e($p->foto_url); ?>" alt="<?php echo e($p->nama); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <?php echo e(strtoupper(substr($p->nama ?? '?', 0, 2))); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 leading-snug truncate"><?php echo e($p->nama ?? '-'); ?></div>
                                        <div class="text-[11px] text-slate-400 truncate"><?php echo e($p->email ?? '-'); ?></div>
                                    </div>
                                </div>
                            </td>

                            
                            <td class="py-3.5 px-5 whitespace-nowrap font-semibold text-slate-700">
                                <?php echo $p->nip ? e($p->nip) : '<span class="text-slate-400 italic font-normal">-</span>'; ?>

                            </td>

                            
                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-semibold <?php echo e($p->jenis_kelamin === 'perempuan' ? 'text-rose-600' : 'text-sky-700'); ?>">
                                    <span class="text-sm leading-none"><?php echo e($p->jenis_kelamin === 'perempuan' ? '♀' : '♂'); ?></span>
                                    <?php echo e(ucfirst($p->jenis_kelamin ?? '-')); ?>

                                </span>
                            </td>

                            
                            <td class="py-3.5 px-5 whitespace-nowrap text-slate-600">
                                <?php echo e($p->no_telp ?? '-'); ?>

                            </td>

                            
                            <td class="py-3.5 px-5">
                                <div class="flex flex-wrap gap-1 items-center">
                                    <button type="button" onclick="document.getElementById('modal-kelola-<?php echo e($p->id); ?>').showModal()"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                                        Kelola Ekskul
                                    </button>
                                </div>
                            </td>

                            
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <?php if($p->user_id): ?>
                                    <a href="<?php echo e(route('kesiswaan.users.edit', $p->user_id)); ?>"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold rounded-xl transition text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Akun
                                    </a>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-400 italic">Belum ada akun</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Tidak ada pembina yang sesuai kriteria.</p>
                                    <?php if($hasFilter): ?>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah kata kunci atau hapus filter.</p>
                                        <a href="<?php echo e(route('kesiswaan.pembina.index')); ?>" class="mt-3 text-xs font-bold text-sky-600 hover:text-sky-700">
                                            Tampilkan semua pembina
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-2 justify-between items-center">
            <p class="text-[11px] text-slate-400 font-semibold">
                Menampilkan <?php echo e($pembinas->firstItem() ?? 0); ?>-<?php echo e($pembinas->lastItem() ?? 0); ?> dari <?php echo e($pembinas->total()); ?> pembina
            </p>
            <?php echo e($pembinas->links()); ?>

        </div>
    </div>

</div>

<?php $__currentLoopData = $pembinas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <dialog id="modal-kelola-<?php echo e($p->id); ?>" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-lg shadow-2xl border border-sky-100">
        <div class="p-6 space-y-4 bg-white">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">Kelola Ekskul</h2>
                    <p class="text-xs text-slate-400 mt-0.5"><?php echo e($p->nama); ?> · <?php echo e($p->ekskuls->count()); ?>/4 ekskul</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center">×</button>
            </div>

            <div class="space-y-2">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide">Ekskul yang dibina</h3>
                <?php $__empty_1 = true; $__currentLoopData = $p->ekskuls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ekskul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <div>
                            <p class="text-xs font-bold text-slate-800"><?php echo e($ekskul->nama_ekskul); ?></p>
                            <p class="text-[10px] mt-0.5 text-slate-400">Ditugaskan ke pembina ini</p>
                        </div>
                        <form action="<?php echo e(route('kesiswaan.pembina.remove-ekskul', [$p, $ekskul])); ?>" method="POST" onsubmit="return confirm('Lepas penugasan <?php echo e($ekskul->nama_ekskul); ?> dari <?php echo e($p->nama); ?>?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="px-3 py-1.5 rounded-xl text-[11px] font-bold bg-rose-50 text-rose-600 hover:bg-rose-100">
                                Tidak Membina Lagi
                            </button>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-xs text-slate-400 py-2">Belum ada ekskul yang dibina.</p>
                <?php endif; ?>
            </div>

            <?php if($p->ekskuls->count() < 4): ?>
                <form action="<?php echo e(route('kesiswaan.pembina.assign-ekskul', $p)); ?>" method="POST" class="pt-3 border-t border-slate-100 space-y-3">
                    <?php echo csrf_field(); ?>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Tambah ekskul</label>
                    <select name="ekskul_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800">
                        <option value="" disabled selected>Pilih ekskul...</option>
                        <?php $__currentLoopData = $ekskulList->whereNull('pembina_id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ekskul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ekskul->id); ?>"><?php echo e($ekskul->nama_ekskul); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="w-full px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl">Tambah Ekskul</button>
                </form>
            <?php else: ?>
                <p class="text-[11px] text-amber-700 bg-amber-50 border border-amber-100 rounded-xl p-3">Batas maksimal 4 ekskul per pembina tercapai.</p>
            <?php endif; ?>
        </div>
    </dialog>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.kesiswaan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Soul\resources\views/kesiswaan/pembina/index.blade.php ENDPATH**/ ?>