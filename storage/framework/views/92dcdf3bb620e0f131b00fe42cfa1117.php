<?php $__env->startSection('title', 'Kelola FAQ'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Kelola FAQ</h1>
            <p class="text-xs text-slate-400 mt-1">Moderasi pertanyaan umum ekskul binaanmu</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-100 text-sky-700 border border-sky-200 rounded-full text-[11px] font-bold shrink-0">
                <?php echo e($totalCount); ?> total
            </span>
            <?php if($pendingCount > 0): ?>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-100 text-amber-700 border border-amber-200 rounded-full text-[11px] font-bold shrink-0">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <?php echo e($pendingCount); ?> menunggu jawaban
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[11px] font-bold shrink-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Semua sudah dijawab
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Ekskul -->
    <?php if($ekskuls->count() > 1): ?>
        <form method="GET" action="<?php echo e(route('pembina.faq.index')); ?>" class="flex items-center gap-2">
            <select name="ekskul" onchange="this.form.submit()"
                class="px-4 py-2.5 bg-white border border-sky-100 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                <option value="">Semua Ekskul</option>
                <?php $__currentLoopData = $ekskuls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ex->id); ?>" <?php echo e(($ekskulFilter ?? 0) == $ex->id ? 'selected' : ''); ?>><?php echo e($ex->nama_ekskul); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
    <?php endif; ?>

    <!-- Add Form Card -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl space-y-5 animate-fade-up" style="animation-delay: .1s">
        <h3 class="text-sm font-extrabold text-slate-900 mb-4">Tambah FAQ</h3>
        <form action="<?php echo e(route('pembina.faq.store')); ?>" method="POST" class="space-y-5">
            <?php echo csrf_field(); ?>
            <?php if($ekskuls->count() > 1): ?>
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Ekskul</label>
                    <select name="ekskul_id" required
                        class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                        <option value="">Pilih ekskul</option>
                        <?php $__currentLoopData = $ekskuls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ex->id); ?>" <?php echo e(old('ekskul_id', $ekskulFilter ?? '') == $ex->id ? 'selected' : ''); ?>><?php echo e($ex->nama_ekskul); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['ekskul_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-[10px] mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            <?php else: ?>
                <input type="hidden" name="ekskul_id" value="<?php echo e($ekskuls->first()?->id); ?>">
            <?php endif; ?>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Pertanyaan</label>
                <input type="text" name="pertanyaan" value="<?php echo e(old('pertanyaan')); ?>" required placeholder="Contoh: Apakah harus punya pengalaman sebelumnya?"
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                <?php $__errorArgs = ['pertanyaan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-[10px] mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Jawaban</label>
                <textarea name="jawaban" rows="3" required placeholder="Jawaban..."
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition"><?php echo e(old('jawaban')); ?></textarea>
                <?php $__errorArgs = ['jawaban'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-[10px] mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 transition w-full sm:w-auto">Simpan</button>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="card-table w-full text-left text-xs md:text-sm">
            <thead class="bg-gradient-to-r from-sky-50 to-blue-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">No</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Ekskul</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Pertanyaan</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Jawaban</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Status</th>
                    <th class="px-4 md:px-6 py-3 font-semibold text-slate-500 whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sky-50">
                <?php $__empty_1 = true; $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-sky-50/50 transition">
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap"><?php echo e($loop->iteration); ?></td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-sky-50 border border-sky-100 rounded-full text-[10px] font-bold text-sky-700"><?php echo e($faq->ekskul->nama_ekskul ?? '-'); ?></span>
                        </td>
                        <td class="px-4 md:px-6 py-3.5 font-medium max-w-sm leading-relaxed"><?php echo e($faq->pertanyaan); ?></td>
                        <td class="px-4 md:px-6 py-3.5 max-w-md">
                            <?php if($faq->status === 'pending'): ?>
                                <form action="<?php echo e(route('pembina.faq.answer', $faq)); ?>" method="POST" class="space-y-2">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <textarea name="jawaban" rows="3" required placeholder="Tulis jawaban lalu terbitkan..."
                                        class="w-full px-3 py-2 bg-amber-50/50 border border-amber-100 rounded-xl text-xs focus:outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition"></textarea>
                                    <?php $__errorArgs = ['jawaban'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-[10px] mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <button type="submit" class="px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-xl text-[10px] font-bold transition">Terbitkan Jawaban</button>
                                </form>
                            <?php else: ?>
                                <p class="text-slate-600 leading-relaxed"><?php echo e($faq->jawaban); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <?php if($faq->status === 'pending'): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-100 text-amber-700 border border-amber-200 rounded-full text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Menunggu
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Ditampilkan
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 md:px-6 py-3.5 whitespace-nowrap">
                            <form action="<?php echo e(route('pembina.faq.destroy', $faq)); ?>" method="POST" class="inline" onsubmit="return confirm('Hapus FAQ ini?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 border border-rose-200 rounded-xl text-[10px] font-bold transition">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-4 md:px-6 py-10 text-center text-slate-400">Belum ada FAQ. Tambahkan pertanyaan yang sering ditanyakan siswa.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pembina.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH X:\required\laragon\www\Soul\resources\views/pembina/faq/index.blade.php ENDPATH**/ ?>