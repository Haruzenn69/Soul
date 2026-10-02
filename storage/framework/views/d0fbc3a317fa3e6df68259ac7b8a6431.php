
<?php $__env->startSection('title', 'Data Anggota & Ketua Ekskul'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 animate-fade-up">

    
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">Data Anggota & Penunjukan Ketua</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola anggota aktif dan tentukan Ketua Ekskul untuk memimpin kegiatan ekskul binaan Anda.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap justify-end">
            <?php if($selectedEkskul && $ekskuls->firstWhere('id', $selectedEkskul)): ?>
                <?php $activeEkskul = $ekskuls->firstWhere('id', $selectedEkskul); ?>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><?php echo e($activeEkskul->nama_ekskul); ?></span>
                </div>
            <?php endif; ?>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-sky-50 border border-sky-100 text-sky-700 text-xs font-bold">
                <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span>Membina <?php echo e($ekskuls->count()); ?> Ekskul</span>
            </div>
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
                                    <p class="text-[10px] text-slate-400">NIS: <?php echo e($ketua->nis); ?> Ã‚Â· <?php echo e($ketua->kelas->nama ?? '-'); ?></p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="my-3 p-3 bg-amber-50/60 border border-dashed border-amber-300/80 rounded-2xl text-center">
                                <span class="text-xl block mb-1">Ã°Å¸â€˜â€˜</span>
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

    
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm animate-fade-up overflow-hidden">
        <form method="GET" action="<?php echo e(route('pembina.anggota')); ?>" id="filter-form">

            
            <div class="p-4 flex flex-col sm:flex-row gap-3 border-b border-slate-100">
                
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" name="cari" id="input-cari" value="<?php echo e($cari); ?>"
                        placeholder="Cari nama, NIS, atau email..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                </div>

                
                <div class="relative shrink-0">
                    <select name="ekskul" id="select-ekskul" onchange="this.form.submit()"
                        class="pl-4 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all appearance-none">
                        <?php if($ekskuls->count() > 1): ?>
                            <option value="">ðŸ“‹ Semua Ekskul</option>
                        <?php endif; ?>
                        <?php $__currentLoopData = $ekskuls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($e->id); ?>" <?php if($selectedEkskul == $e->id || $ekskuls->count() === 1): echo 'selected'; endif; ?>>
                                <?php echo e($e->nama_ekskul); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </div>

                
                <div class="relative shrink-0">
                    <select name="sort" id="select-sort" onchange="this.form.submit()"
                        class="pl-4 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all appearance-none">
                        <option value="nama_asc"     <?php if($sort === 'nama_asc'): echo 'selected'; endif; ?>>Nama A&ndash;Z</option>
                        <option value="nama_desc"    <?php if($sort === 'nama_desc'): echo 'selected'; endif; ?>>Nama Z&ndash;A</option>
                        <option value="tanggal_daftar_desc" <?php if($sort === 'tanggal_daftar_desc'): echo 'selected'; endif; ?>>Bergabung Terbaru</option>
                        <option value="tanggal_daftar_asc"  <?php if($sort === 'tanggal_daftar_asc'): echo 'selected'; endif; ?>>Bergabung Terlama</option>
                        <option value="kehadiran_desc" <?php if($sort === 'kehadiran_desc'): echo 'selected'; endif; ?>>Kehadiran Tertinggi</option>
                        <option value="kehadiran_asc"  <?php if($sort === 'kehadiran_asc'): echo 'selected'; endif; ?>>Kehadiran Terendah</option>
                    </select>
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
                    </span>
                </div>

                
                <div class="flex gap-2 shrink-0">
                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-2xl transition shadow-sm shadow-sky-200">
                        Cari
                    </button>
                    <?php if($cari || $selectedEkskul || $selectedJurusan || $selectedJenisKelamin || $selectedTingkat || $selectedStatusKeaktifan || $statusKeanggotaan !== 'aktif'): ?>
                        <a href="<?php echo e(route('pembina.anggota')); ?>"
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-2xl transition">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="px-4 py-3 bg-slate-50/60 flex flex-wrap gap-3 items-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0">Filter:</span>

                
                <div class="relative">
                    <select name="status_keanggotaan" id="select-status-keanggotaan" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white border border-slate-200 rounded-xl text-[11px] text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all appearance-none font-semibold">
                        <option value="aktif"   <?php if($statusKeanggotaan === 'aktif'): echo 'selected'; endif; ?>>Aktif</option>
                        <option value="nonaktif" <?php if($statusKeanggotaan === 'nonaktif'): echo 'selected'; endif; ?>>Nonaktif / Keluar</option>
                        <option value="semua"   <?php if($statusKeanggotaan === 'semua'): echo 'selected'; endif; ?>>Semua Status</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                </div>

                
                <div class="relative">
                    <select name="status_keaktifan" id="select-status-keaktifan" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white border border-slate-200 rounded-xl text-[11px] text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all appearance-none font-semibold">
                        <option value=""           <?php if(!$selectedStatusKeaktifan): echo 'selected'; endif; ?>>Semua Keaktifan</option>
                        <option value="sangat_aktif" <?php if($selectedStatusKeaktifan === 'sangat_aktif'): echo 'selected'; endif; ?>>Sangat Aktif (Ã¢â€°Â¥80%)</option>
                        <option value="cukup_aktif"  <?php if($selectedStatusKeaktifan === 'cukup_aktif'): echo 'selected'; endif; ?>>Cukup Aktif (Ã¢â€°Â¥50%)</option>
                        <option value="kurang_aktif" <?php if($selectedStatusKeaktifan === 'kurang_aktif'): echo 'selected'; endif; ?>>Kurang Aktif (&lt;50%)</option>
                        <option value="pasif"         <?php if($selectedStatusKeaktifan === 'pasif'): echo 'selected'; endif; ?>>Pasif (0%)</option>
                        <option value="peringatan"    <?php if($selectedStatusKeaktifan === 'peringatan'): echo 'selected'; endif; ?>>Peringatan</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                </div>

                
                <div class="relative">
                    <select name="tingkat" id="select-tingkat" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white border border-slate-200 rounded-xl text-[11px] text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all appearance-none font-semibold">
                        <option value="">Semua Tingkat</option>
                        <option value="10" <?php if($selectedTingkat == '10' || $selectedTingkat == 'x'): echo 'selected'; endif; ?>>Kelas 10</option>
                        <option value="11" <?php if($selectedTingkat == '11' || $selectedTingkat == 'xi'): echo 'selected'; endif; ?>>Kelas 11</option>
                        <option value="12" <?php if($selectedTingkat == '12' || $selectedTingkat == 'xii'): echo 'selected'; endif; ?>>Kelas 12</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                </div>

                
                <div class="relative">
                    <select name="jurusan" id="select-jurusan" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white border border-slate-200 rounded-xl text-[11px] text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all appearance-none font-semibold">
                        <option value="">Semua Jurusan</option>
                        <?php $__currentLoopData = $jurusans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kode => $labels): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $label = is_array($labels) ? reset($labels) : $labels; ?>
                            <option value="<?php echo e($kode); ?>" <?php if($selectedJurusan === $kode): echo 'selected'; endif; ?>><?php echo e(strtoupper($kode)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                </div>

                
                <div class="relative">
                    <select name="jenis_kelamin" id="select-jenis-kelamin" onchange="this.form.submit()"
                        class="pl-3 pr-7 py-1.5 bg-white border border-slate-200 rounded-xl text-[11px] text-slate-700 focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition-all appearance-none font-semibold">
                        <option value=""         <?php if(!$selectedJenisKelamin): echo 'selected'; endif; ?>>Semua Gender</option>
                        <option value="laki-laki"  <?php if($selectedJenisKelamin === 'laki-laki'): echo 'selected'; endif; ?>>Laki-laki</option>
                        <option value="perempuan"  <?php if($selectedJenisKelamin === 'perempuan'): echo 'selected'; endif; ?>>Perempuan</option>
                    </select>
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></span>
                </div>
            </div>
        </form>
    </div>

    
    <div class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm animate-fade-up">
        <div class="flex flex-wrap justify-between items-center gap-2 mb-5">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">
                    Daftar Anggota
                    <?php if($selectedEkskul && $ekskuls->firstWhere('id', $selectedEkskul)): ?>
                        <span class="ml-2 text-xs font-bold px-2 py-0.5 rounded-lg bg-sky-100 text-sky-700"><?php echo e($ekskuls->firstWhere('id', $selectedEkskul)->nama_ekskul); ?></span>
                    <?php else: ?>
                        <span class="ml-2 text-xs font-semibold text-slate-400">Semua Ekskul</span>
                    <?php endif; ?>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Siswa biasa dapat diangkat menjadi Ketua Ekskul untuk membantu presensi dan kegiatan.</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 bg-slate-100 text-slate-600 rounded-xl">Total <?php echo e(count($anggota)); ?> Anggota</span>
        </div>

        <?php if(count($anggota) > 0): ?>
            <div class="overflow-x-auto">
                <table class="card-table w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-100">
                            <th class="p-3.5 font-bold rounded-l-2xl text-left">No</th>
                            <th class="p-3.5 font-bold text-left">Identitas Siswa</th>
                            <th class="p-3.5 font-bold text-left">Kelas</th>
                            <?php if($ekskuls->count() > 1): ?><th class="p-3.5 font-bold text-left">Ekskul</th><?php endif; ?>
                            <th class="p-3.5 font-bold text-left">Jabatan</th>
                            <th class="p-3.5 font-bold text-left">Bergabung</th>
                            <th class="p-3.5 font-bold text-left">Kehadiran</th>
                            <th class="p-3.5 font-bold text-left">Keaktifan</th>
                            <th class="p-3.5 font-bold text-left">Status</th>
                            <th class="p-3.5 font-bold text-center rounded-r-2xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isKetua = $item->siswa?->jabatan === 'ketua';
                            $currentKetuaEkskul = $item->ekskul?->ketua();
                            $isKetuaInThisEkskul = $isKetua && ($currentKetuaEkskul?->id === $item->siswa_id);
                            $pct = $item->persentase_kehadiran ?? 0;
                            $labelKeaktifan = $item->label_keaktifan ?? 'Belum Ada Presensi';
                            $statusKeaktifan = $item->status_keaktifan ?? 'pasif';
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
                                        <a href="<?php echo e(route('pembina.anggota.show', $item)); ?>" class="font-extrabold text-slate-900 hover:text-sky-600 block transition"><?php echo e($item->siswa->nama ?? '-'); ?></a>
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
                                        <svg class="w-3 h-3 inline-block" viewBox="0 0 24 24" fill="currentColor"><path d="M2 19l2-9 5 5 3-8 3 8 5-5 2 9H2z"/></svg> Ketua
                                    </span>
                                <?php elseif($isKetua): ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[11px] font-bold bg-purple-100 text-purple-800 border border-purple-200">Ketua Lain</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[11px] font-bold bg-slate-100 text-slate-600">Anggota</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3.5 text-slate-400 font-medium">
                                <?php echo e(\Carbon\Carbon::parse($item->tanggal_daftar)->isoFormat('DD MMM Y')); ?>

                            </td>
                            
                            <td class="p-3.5">
                                <div class="flex items-center gap-2 min-w-[80px]">
                                    <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-full rounded-full transition-all
                                            <?php echo e($pct >= 80 ? 'bg-emerald-400' : ($pct >= 50 ? 'bg-amber-400' : 'bg-rose-400')); ?>"
                                            style="width: <?php echo e($pct); ?>%">
                                        </div>
                                    </div>
                                    <span class="text-[11px] font-bold <?php echo e($pct >= 80 ? 'text-emerald-600' : ($pct >= 50 ? 'text-amber-600' : 'text-rose-500')); ?> shrink-0"><?php echo e($pct); ?>%</span>
                                </div>
                            </td>
                            
                            <td class="p-3.5">
                                <?php if($statusKeaktifan === 'sangat_aktif'): ?>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Sangat Aktif</span>
                                <?php elseif($statusKeaktifan === 'cukup_aktif'): ?>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">Cukup Aktif</span>
                                <?php elseif($statusKeaktifan === 'kurang_aktif'): ?>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Kurang Aktif</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Pasif</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="p-3.5">
                                <?php if($item->status === 'diterima'): ?>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                <?php elseif($item->status === 'peringatan'): ?>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">Peringatan</span>
                                <?php elseif($item->status === 'nonaktif'): ?>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Nonaktif</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">Keluar</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="p-3.5 text-center">
                                <div class="flex flex-wrap gap-2 justify-center">
                                    <a href="<?php echo e(route('pembina.anggota.show', $item)); ?>" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-600 font-bold text-[11px] rounded-xl border border-sky-200 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Detail
                                    </a>
                                    <?php if($isKetuaInThisEkskul): ?>
                                        <form action="<?php echo e(route('pembina.ekskul.copot-ketua', ['ekskul' => $item->ekskul_id, 'siswa' => $item->siswa_id])); ?>" method="POST"
                                              onsubmit="return confirm('Copot jabatan Ketua Ekskul <?php echo e($item->ekskul->nama_ekskul); ?> dari <?php echo e($item->siswa->nama); ?>?')">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-[11px] rounded-xl border border-rose-200 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Copot</span>
                                            </button>
                                        </form>
                                    <?php elseif(!$isKetua && $item->status === 'diterima'): ?>
                                        <form action="<?php echo e(route('pembina.ekskul.pilih-ketua', ['ekskul' => $item->ekskul_id, 'siswa' => $item->siswa_id])); ?>" method="POST"
                                              onsubmit="return confirm('Jadikan <?php echo e($item->siswa->nama); ?> sebagai Ketua di ekskul <?php echo e($item->ekskul->nama_ekskul); ?>?<?php echo e($currentKetuaEkskul ? ' (Ketua lama: ' . $currentKetuaEkskul->nama . ' akan diturunkan)' : ''); ?>')">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-[11px] rounded-xl border border-amber-300 transition shadow-xs">
                                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M2 19l2-9 5 5 3-8 3 8 5-5 2 9H2z"/></svg>
                                                <span>Jadikan Ketua</span>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
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
                <p class="text-xs font-semibold">Tidak ada anggota yang sesuai dengan filter yang dipilih.</p>
                <a href="<?php echo e(route('pembina.anggota')); ?>" class="text-[11px] text-sky-500 hover:underline mt-1 inline-block">Reset semua filter</a>
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pembina.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ASUS\Soul\resources\views/pembina/anggota.blade.php ENDPATH**/ ?>