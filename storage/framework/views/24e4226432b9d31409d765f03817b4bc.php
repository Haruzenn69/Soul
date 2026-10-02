<?php $__env->startSection('title', 'Daftarkan Pelatih Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6 animate-fade-up">

    
    <div class="flex items-center justify-between">
        <a href="<?php echo e(route('pembina.pelatih.index')); ?>"
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-sky-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Data Pelatih
        </a>
    </div>

    
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-500 via-blue-600 to-indigo-700 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur border border-white/20 text-[11px] font-bold uppercase tracking-wider text-white mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Form Pendaftaran Pelatih
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-white">
                Daftarkan Pelatih Ekskul
            </h1>
            <p class="text-xs text-white/80 mt-1 max-w-xl leading-relaxed">
                Lengkapi biodata dan unggah berkas pendukung pelatih. Berkas sertifikat dalam format PDF bersifat wajib dan akan diverifikasi langsung oleh pihak Kesiswaan sebelum tampil di katalog ekskul.
            </p>
        </div>
    </div>

    
    <?php if($errors->any()): ?>
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs animate-shake">
            <div class="font-bold flex items-center gap-2 mb-1.5">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Mohon periksa kembali formulir berikut:
            </div>
            <ul class="list-disc list-inside space-y-1 text-[11px]">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <form action="<?php echo e(route('pembina.pelatih.store')); ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm space-y-6">
        <?php echo csrf_field(); ?>

        
        <div>
            <h2 class="text-sm font-extrabold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 font-black text-xs flex items-center justify-center">1</span>
                Informasi Ekskul & Identitas
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Ekskul Binaan <span class="text-rose-500">*</span>
                    </label>
                    <select name="ekskul_id" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                        <option value="">-- Pilih Ekskul yang Dilatih --</option>
                        <?php $__currentLoopData = $ekskuls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ekskul): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ekskul->id); ?>" <?php echo e(old('ekskul_id') == $ekskul->id ? 'selected' : ''); ?>>
                                <?php echo e($ekskul->nama_ekskul); ?> (Bidang: <?php echo e($ekskul->bidang); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Pilih ekskul binaanmu yang akan dibina oleh pelatih ini.</p>
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nama Lengkap Pelatih <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama" value="<?php echo e(old('nama')); ?>" required placeholder="Contoh: Coach Hendra Setiawan, S.Pd."
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 cursor-pointer hover:bg-sky-50 transition">
                            <input type="radio" name="jenis_kelamin" value="laki-laki" <?php echo e(old('jenis_kelamin', 'laki-laki') === 'laki-laki' ? 'checked' : ''); ?> class="text-sky-600 focus:ring-sky-500">
                            <span>Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 cursor-pointer hover:bg-sky-50 transition">
                            <input type="radio" name="jenis_kelamin" value="perempuan" <?php echo e(old('jenis_kelamin') === 'perempuan' ? 'checked' : ''); ?> class="text-sky-600 focus:ring-sky-500">
                            <span>Perempuan</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        
        <div>
            <h2 class="text-sm font-extrabold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 font-black text-xs flex items-center justify-center">2</span>
                Kontak & Alamat Domisili
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nomor HP / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="no_hp" value="<?php echo e(old('no_hp')); ?>" required placeholder="Contoh: 081234567890"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required placeholder="Contoh: pelatih@gmail.com"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Kota Domisili <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="domisili" value="<?php echo e(old('domisili')); ?>" required placeholder="Contoh: Jakarta Selatan / Bandung / Surabaya"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>

                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Media Sosial <span class="text-slate-400 font-normal">(opsional, IG / LinkedIn)</span>
                    </label>
                    <input type="text" name="sosmed" value="<?php echo e(old('sosmed')); ?>" placeholder="Contoh: @coach.hendra atau linkedin.com/in/coach-hendra"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>

                
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Alamat Lengkap Tempat Tinggal <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="alamat" rows="2.5" required placeholder="Tuliskan jalan, nomor rumah, RT/RW, kelurahan, dan kecamatan..."
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"><?php echo e(old('alamat')); ?></textarea>
                </div>
            </div>
        </div>

        
        <div>
            <h2 class="text-sm font-extrabold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 font-black text-xs flex items-center justify-center">3</span>
                Dokumen Berkas (Keduanya Wajib Format PDF)
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div class="p-4 rounded-2xl bg-sky-50/60 border-2 border-dashed border-sky-300">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded-md bg-sky-600 text-white font-black text-[10px] uppercase">Wajib</span>
                        <label class="text-xs font-extrabold text-slate-800">
                            Curriculum Vitae / CV (PDF) <span class="text-rose-500">*</span>
                        </label>
                    </div>
                    <p class="text-[11px] text-slate-500 mb-3 leading-relaxed">
                        Unggah riwayat hidup & rekam jejak kepelatihan dalam format <strong class="text-sky-800">PDF</strong> (Maksimal 5MB).
                    </p>
                    <input type="file" name="cv" accept="application/pdf" required
                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-600 file:text-white hover:file:bg-sky-700 file:cursor-pointer transition">
                </div>

                
                <div class="p-4 rounded-2xl bg-amber-50/60 border-2 border-dashed border-amber-300">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded-md bg-amber-500 text-white font-black text-[10px] uppercase">Wajib</span>
                        <label class="text-xs font-extrabold text-slate-800">
                            Sertifikat Pelatih (PDF) <span class="text-rose-500">*</span>
                        </label>
                    </div>
                    <p class="text-[11px] text-slate-500 mb-3 leading-relaxed">
                        Unggah sertifikat lisensi kepelatihan resmi dalam format <strong class="text-amber-800">PDF</strong> (Maksimal 5MB).
                    </p>
                    <input type="file" name="sertifikat" accept="application/pdf" required
                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-white hover:file:bg-amber-600 file:cursor-pointer transition">
                </div>
            </div>
        </div>

        
        <div class="p-4 rounded-2xl bg-sky-50 border border-sky-100 flex items-start gap-3">
            <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-[11px] text-sky-800 leading-relaxed">
                <strong class="font-bold">Alur Verifikasi:</strong> Setelah pendaftaran diajukan, notifikasi konfirmasi akan masuk ke akunmu dan notifikasi verifikasi akan otomatis terkirim ke Kesiswaan. Pelatih baru akan tampil di <strong>Katalog Ekskul</strong> segera setelah disetujui kesiswaan.
            </div>
        </div>

        
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="<?php echo e(route('pembina.pelatih.index')); ?>"
               class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-sky-500/25 hover:from-sky-600 hover:to-blue-700 transition transform hover:-translate-y-0.5">
                Kirim Pendaftaran Pelatih
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pembina.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ASUS\Soul\resources\views/pembina/pelatih/create.blade.php ENDPATH**/ ?>