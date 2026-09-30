<?php $__env->startSection('title', 'Data Anggota & Ketua Ekskul'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 animate-fade-up">

    
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">Data Anggota & Penunjukan Ketua</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola anggota aktif dan tentukan Ketua Ekskul untuk memimpin kegiatan ekskul binaan Anda.</p>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-sky-50 border border-sky-100 text-sky-700 text-xs font-bold">
            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <span>Membina <?php echo e($ekskuls->count()); ?> / 4 Ekskul</span>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm animate-fade-up">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-semibold flex items-center gap-2 shadow-sm animate-fade-up">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    
    <div>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ketua Ekskul Binaan Saat Ini</h2>
            <span class="text-[11px] text-slate-400">Dipilih langsung dari siswa aktif di ekskul</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php $__currentLoopData = $ekskuls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $ketua = $eks->ketua();
                    $anggotaEkskulCount = \App\Models\Pendaftaran::where('ekskul_id', $eks->id)->where('status', 'diterima')->count();
                ?>
                <div class="bg-white rounded-3xl p-5 border border-sky-100 shadow-sm flex flex-col justify-between relative overflow-hidden transition-all hover:shadow-md">
                    
                    <?php if($ketua): ?>
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-amber-400/10 blur-xl pointer-events-none"></div>
                    <?php else: ?>
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-slate-200/50 blur-xl pointer-events-none"></div>
                    <?php endif; ?>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-xl bg-sky-50 text-sky-700 border border-sky-100">
                                <?php echo e($eks->nama_ekskul); ?>

                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold">
                                <?php echo e($anggotaEkskulCount); ?> Anggota
                            </span>
                        </div>

                        <?php if($ketua): ?>
                            <div class="flex items-center gap-3.5 my-2">
                                <div class="w-12 h-12 rounded-2xl bg-amber-100 border-2 border-amber-300 overflow-hidden flex items-center justify-center shrink-0 shadow-sm shadow-amber-100">
                                    <?php if($ketua->foto_url): ?>
                                        <img src="<?php echo e($ketua->foto_url); ?>" alt="<?php echo e($ketua->nama); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <span class="text-sm font-black text-amber-700 uppercase">
                                            <?php echo e(strtoupper(substr($ketua->nama, 0, 2))); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-extrabold uppercase px-1.5 py-0.5 rounded-md bg-amber-500 text-white">Ketua</span>
                                    </div>
                                    <h4 class="text-xs font-extrabold text-slate-900 truncate mt-1" title="<?php echo e($ketua->nama); ?>"><?php echo e($ketua->nama); ?></h4>
                                    <p class="text-[10px] text-slate-400">NIS: <?php echo e($ketua->nis); ?> · <?php echo e($ketua->kelas->nama ?? '-'); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="my-3 p-3 bg-amber-50/60 border border-dashed border-amber-300/80 rounded-2xl text-center">
                                <span class="text-xl block mb-1">👑</span>
                                <span class="text-[11px] font-bold text-amber-800 block">Belum Ada Ketua</span>
                                <p class="text-[10px] text-amber-700/80 mt-0.5 leading-snug">
                                    Pilih salah satu siswa dari tabel anggota di bawah untuk dijadikan Ketua.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if($ketua): ?>
                        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[10px] text-slate-400">Status Aktif</span>
                            <form action="<?php echo e(route('pembina.ekskul.copot-ketua', ['ekskul' => $eks->id, 'siswa' => $ketua->id])); ?>" method="POST"
                                  onsubmit="return confirm('Apakah Anda yakin ingin mencopot jabatan Ketua Ekskul <?php echo e($eks->nama_ekskul); ?> dari <?php echo e($ketua->nama); ?>? Siswa akan kembali berstatus anggota biasa.')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-500 hover:text-rose-700 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Copot Ketua</span>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="bg-white p-4 rounded-3xl border border-sky-100 shadow-sm animate-fade-up">
        <form method="GET" action="<?php echo e(route('pembina.anggota')); ?>" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" name="cari" value="<?php echo e(request('cari')); ?>" placeholder="Cari berdasarkan nama siswa atau NIS..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
            </div>
            <?php if($ekskuls->count() > 1): ?>
            <div class="relative">
                <select name="ekskul" onchange="this.form.submit()" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all appearance-none pr-8">
                    <option value="">Semua Ekskul Binaan</option>
                    <?php $__currentLoopData = $ekskuls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($e->id); ?>" <?php if(request('ekskul') == $e->id): echo 'selected'; endif; ?>><?php echo e($e->nama_ekskul); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
            </div>
            <?php endif; ?>
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-2xl transition shadow-sm shadow-sky-200 shrink-0">
                Filter Anggota
            </button>
        </form>
    </div>

    
    <div class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm animate-fade-up">
        <div class="flex flex-wrap justify-between items-center gap-2 mb-5">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Daftar Anggota Aktif</h3>
                <p class="text-xs text-slate-400 mt-0.5">Siswa biasa dapat diangkat menjadi Ketua Ekskul untuk membantu presensi dan kegiatan.</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 bg-slate-100 text-slate-600 rounded-xl">Total <?php echo e(count($anggota)); ?> Anggota</span>
        </div>

        <?php if(count($anggota) > 0): ?>
            <div class="overflow-x-auto">
                <table class="card-table w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-100">
                            <th class="p-3.5 font-bold rounded-l-2xl">No</th>
                            <th class="p-3.5 font-bold">Identitas Siswa</th>
                            <th class="p-3.5 font-bold">Kelas</th>
                            <?php if($ekskuls->count() > 1): ?><th class="p-3.5 font-bold">Ekskul</th><?php endif; ?>
                            <th class="p-3.5 font-bold">Jabatan</th>
                            <th class="p-3.5 font-bold">Bergabung</th>
                            <th class="p-3.5 font-bold text-center rounded-r-2xl">Aksi Penetapan Ketua</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isKetua = $item->siswa?->jabatan === 'ketua';
                            $currentKetuaEkskul = $item->ekskul?->ketua();
                            $isKetuaInThisEkskul = $isKetua && ($currentKetuaEkskul?->id === $item->siswa_id);
                        ?>
                        <tr class="hover:bg-sky-50/40 transition">
                            <td class="p-3.5 text-slate-400 font-semibold"><?php echo e($key + 1); ?></td>
                            <td class="p-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0 flex items-center justify-center font-bold text-xs <?php echo e($isKetuaInThisEkskul ? 'bg-amber-100 text-amber-800 border-2 border-amber-300' : 'bg-slate-100 text-slate-600'); ?>">
                                        <?php if($item->siswa?->foto_url): ?>
                                            <img src="<?php echo e($item->siswa->foto_url); ?>" alt="<?php echo e($item->siswa->nama); ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span><?php echo e(strtoupper(substr($item->siswa->nama ?? 'S', 0, 2))); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-900 block"><?php echo e($item->siswa->nama ?? '-'); ?></span>
                                        <span class="text-[11px] text-slate-400">NIS: <?php echo e($item->siswa->nis ?? '-'); ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5 font-semibold text-slate-700"><?php echo e($item->siswa->kelas->nama ?? '-'); ?></td>
                            <?php if($ekskuls->count() > 1): ?>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-xl bg-sky-50 text-sky-700 font-semibold text-[11px] border border-sky-100">
                                        <?php echo e($item->ekskul->nama_ekskul ?? '-'); ?>

                                    </span>
                                </td>
                            <?php endif; ?>
                            <td class="p-3.5">
                                <?php if($isKetuaInThisEkskul): ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300 shadow-xs">
                                        <span>👑</span> Ketua Ekskul
                                    </span>
                                <?php elseif($isKetua): ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[11px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                        Ketua di Ekskul Lain
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[11px] font-bold bg-slate-100 text-slate-600">
                                        Siswa Biasa
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 text-slate-400 font-medium">
                                <?php echo e(\Carbon\Carbon::parse($item->tanggal_daftar)->isoFormat('DD MMM Y')); ?>

                            </td>
                            <td class="p-3.5 text-center">
                                <?php if($isKetuaInThisEkskul): ?>
                                    <form action="<?php echo e(route('pembina.ekskul.copot-ketua', ['ekskul' => $item->ekskul_id, 'siswa' => $item->siswa_id])); ?>" method="POST"
                                          onsubmit="return confirm('Copot jabatan Ketua Ekskul <?php echo e($item->ekskul->nama_ekskul); ?> dari <?php echo e($item->siswa->nama); ?>?')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-[11px] rounded-xl border border-rose-200 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span>Copot Ketua</span>
                                        </button>
                                    </form>
                                <?php elseif(!$isKetua): ?>
                                    <form action="<?php echo e(route('pembina.ekskul.pilih-ketua', ['ekskul' => $item->ekskul_id, 'siswa' => $item->siswa_id])); ?>" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin mengangkat <?php echo e($item->siswa->nama); ?> (<?php echo e($item->siswa->nis); ?>) sebagai KETUA di ekskul <?php echo e($item->ekskul->nama_ekskul); ?>?<?php echo e($currentKetuaEkskul ? ' (Ketua saat ini: ' . $currentKetuaEkskul->nama . ' akan otomatis kembali menjadi anggota biasa)' : ''); ?>')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 hover:text-amber-800 font-bold text-[11px] rounded-xl border border-amber-300 transition shadow-xs">
                                            <span>⭐</span>
                                            <span>Jadikan Ketua</span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-400 italic">Sudah memimpin ekskul lain</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-12 text-slate-400">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <p class="text-xs font-semibold">Belum ada anggota yang terdaftar atau aktif pada kriteria pencarian ini.</p>
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('pembina.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Soul\resources\views/pembina/anggota.blade.php ENDPATH**/ ?>