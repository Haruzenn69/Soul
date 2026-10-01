<?php $__env->startSection('title', 'Data Siswa'); ?>

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

    $hasFilter = request()->filled(['q', 'kelas_id', 'angkatan', 'jenis_kelamin', 'jabatan']);
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
                    Data Siswa
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Daftar seluruh siswa di sekolah. Gunakan pencarian, filter kelas, angkatan, dan urutan kolom untuk menemukan data yang dibutuhkan.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="<?php echo e(route('kesiswaan.users.create')); ?>"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white text-sky-700 font-bold text-xs rounded-2xl shadow-lg shadow-sky-900/10 hover:bg-sky-50 hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Siswa
                </a>
            </div>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('kesiswaan.siswa.index')); ?>" class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm">
        <input type="hidden" name="sort" value="<?php echo e($sort); ?>">
        <input type="hidden" name="direction" value="<?php echo e($direction); ?>">

        <div class="flex flex-col lg:flex-row gap-3 items-stretch">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Cari nama, NIS, atau email..."
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 flex-1">
                <select name="kelas_id" class="px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="">Semua Kelas</option>
                    <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->id); ?>" <?php echo e(request('kelas_id') == $k->id ? 'selected' : ''); ?>><?php echo e($k->nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="angkatan" class="px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="">Semua Angkatan</option>
                    <?php $__currentLoopData = $angkatanList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($a); ?>" <?php echo e(request('angkatan') === $a ? 'selected' : ''); ?>><?php echo e($a); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="jenis_kelamin" class="px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="laki-laki" <?php echo e(request('jenis_kelamin') === 'laki-laki' ? 'selected' : ''); ?>>Laki-laki</option>
                    <option value="perempuan" <?php echo e(request('jenis_kelamin') === 'perempuan' ? 'selected' : ''); ?>>Perempuan</option>
                </select>

                <select name="jabatan" class="px-3 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    <option value="">Semua Jabatan</option>
                    <option value="siswa" <?php echo e(request('jabatan') === 'siswa' ? 'selected' : ''); ?>>Siswa Reguler</option>
                    <option value="ketua" <?php echo e(request('jabatan') === 'ketua' ? 'selected' : ''); ?>>Ketua Ekskul</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                    Filter
                </button>
                <?php if($hasFilter || $sort !== 'nama' || $direction !== 'asc'): ?>
                    <a href="<?php echo e(route('kesiswaan.siswa.index')); ?>"
                       class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition"
                       title="Reset filter">
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
                                Siswa
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($sortIcon('nama')); ?>"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="<?php echo e($sortLink('nis')); ?>" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                NIS
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($sortIcon('nis')); ?>"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="<?php echo e($sortLink('kelas')); ?>" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Kelas
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($sortIcon('kelas')); ?>"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">
                            <a href="<?php echo e($sortLink('angkatan')); ?>" class="inline-flex items-center gap-1.5 hover:text-sky-600 transition">
                                Angkatan
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($sortIcon('angkatan')); ?>"/>
                                </svg>
                            </a>
                        </th>
                        <th class="py-3.5 px-5">Jenis Kelamin</th>
                        <th class="py-3.5 px-5">Jabatan</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <?php $__empty_1 = true; $__currentLoopData = $siswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-sky-50/30 transition">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 font-extrabold flex items-center justify-center text-xs uppercase shrink-0 overflow-hidden">
                                        <?php if($s->foto_url): ?>
                                            <img src="<?php echo e($s->foto_url); ?>" alt="<?php echo e($s->nama); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <?php echo e(strtoupper(substr($s->nama, 0, 2))); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 leading-snug truncate"><?php echo e($s->nama); ?></div>
                                        <div class="text-[11px] text-slate-400 truncate"><?php echo e($s->email ?? '-'); ?></div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap font-semibold text-slate-700"><?php echo e($s->nis); ?></td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full font-bold text-[11px] bg-sky-50 text-sky-700 border border-sky-100">
                                    <?php echo e($s->kelas?->nama ?? '-'); ?>

                                </span>
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap text-slate-600"><?php echo e($s->angkatan ?: '-'); ?></td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-semibold <?php echo e($s->jenis_kelamin === 'perempuan' ? 'text-rose-600' : 'text-sky-700'); ?>">
                                    <span class="text-sm leading-none"><?php echo e($s->jenis_kelamin === 'perempuan' ? '♀' : '♂'); ?></span>
                                    <?php echo e(ucfirst($s->jenis_kelamin ?? '-')); ?>

                                </span>
                            </td>

                            <td class="py-3.5 px-5 whitespace-nowrap">
                                <?php if($s->jabatan === 'ketua'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-bold text-[11px] bg-amber-50 text-amber-700 border border-amber-100">
                                        Ketua Ekskul
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-600">Siswa Reguler</span>
                                <?php endif; ?>
                            </td>

                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <?php if($s->user_id): ?>
                                    <a href="<?php echo e(route('kesiswaan.users.edit', $s->user_id)); ?>"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-semibold rounded-xl transition text-xs"
                                       title="Edit Akun <?php echo e($s->nama); ?>">
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
                            <td colspan="7" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-10 h-10 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-xs font-semibold text-slate-600">Tidak ada siswa yang sesuai kriteria.</p>
                                    <?php if($hasFilter): ?>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Coba ubah kata kunci atau hapus filter yang diterapkan.</p>
                                        <a href="<?php echo e(route('kesiswaan.siswa.index')); ?>" class="mt-3 text-xs font-bold text-sky-600 hover:text-sky-700">
                                            Tampilkan semua siswa
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
                Menampilkan <?php echo e($siswas->firstItem() ?? 0); ?>-<?php echo e($siswas->lastItem() ?? 0); ?> dari <?php echo e($siswas->total()); ?> siswa
            </p>
            <?php echo e($siswas->links()); ?>

        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.kesiswaan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH X:\required\laragon\www\Soul\resources\views/kesiswaan/siswa/index.blade.php ENDPATH**/ ?>