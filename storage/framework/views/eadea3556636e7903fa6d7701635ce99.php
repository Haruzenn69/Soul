<?php if(auth()->guard()->check()): ?>
    <?php if(auth()->user()->needsOnboarding()): ?>
        <?php
            $authUser = auth()->user();
            $roleLabel = $authUser->role === 'pembina' ? 'Guru / Pembina' : 'Siswa';
            $currentModel = $authUser->siswa ?? $authUser->pembina;
            $namaLengkap = $currentModel?->nama ?? $authUser->username;
        ?>

        <style>
            @keyframes modalPopIn {
                from { opacity: 0; transform: translateY(20px) scale(0.96); }
                to { opacity: 1; transform: translateY(0) scale(1); }
            }
            .modal-pop-in {
                animation: modalPopIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
            }
        </style>

        <div id="onboarding-setup-modal" class="fixed inset-0 z-[120] flex items-center justify-center p-4 overflow-y-auto">
            
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity"></div>

            
            <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-sky-100 overflow-hidden modal-pop-in my-8 z-10">
                
                
                <div class="relative px-6 pt-6 pb-5 bg-gradient-to-br from-sky-500 via-sky-600 to-blue-600 text-white overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-36 h-36 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="absolute -left-6 -bottom-6 w-32 h-32 bg-sky-300/20 rounded-full blur-xl pointer-events-none"></div>

                    <div class="relative flex items-center justify-between">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-white/20 backdrop-blur text-white text-[11px] font-bold tracking-wide uppercase">
                            <span>✨ Login Pertama Kali</span>
                            <span class="opacity-60">•</span>
                            <span><?php echo e($roleLabel); ?></span>
                        </div>
                    </div>

                    <h2 class="relative text-lg md:text-xl font-extrabold mt-3 leading-tight">
                        Selamat Datang, <?php echo e($namaLengkap); ?>!
                    </h2>
                    <p class="relative text-xs text-sky-100 mt-1 leading-relaxed">
                        Silakan atur <b>Username Anda sendiri</b>, unggah <b>Foto Profil</b>, dan cantumkan <b>Media Sosial</b> untuk kemudahan interaksi dan kegiatan ekskul.
                    </p>
                </div>

                
                <?php if($errors->any()): ?>
                    <div class="mx-6 mt-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-semibold">
                        <ul class="list-disc list-inside space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                
                <form method="POST" action="<?php echo e(route('onboarding.setup')); ?>" enctype="multipart/form-data" class="p-6 space-y-5">
                    <?php echo csrf_field(); ?>

                    
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">
                            Foto Profil
                        </label>
                        <div class="flex items-center gap-4 p-3 bg-slate-50 rounded-2xl border border-slate-200/80">
                            
                            <div class="relative w-16 h-16 rounded-full bg-gradient-to-br from-sky-100 to-blue-200 border-2 border-sky-400 flex items-center justify-center shrink-0 overflow-hidden shadow-sm shadow-sky-100">
                                <img id="onboarding-avatar-preview" src="" alt="Preview Foto" class="hidden w-full h-full object-cover">
                                <span id="onboarding-avatar-fallback" class="text-xl font-black text-sky-600 uppercase">
                                    <?php echo e(strtoupper(substr($namaLengkap ?? 'U', 0, 1))); ?>

                                </span>
                            </div>

                            <div class="flex-1">
                                <input type="file" name="foto" id="onboarding-foto-input" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="previewOnboardingPhoto(this)">
                                <label for="onboarding-foto-input" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white hover:bg-sky-50 text-sky-700 border border-sky-200 rounded-xl text-xs font-bold cursor-pointer transition shadow-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Pilih Foto
                                </label>
                                <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, atau WebP (Maks. 2MB). Foto formal atau sopan.</p>
                            </div>
                        </div>
                    </div>

                    
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">
                                Tentukan Username Anda <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[10px] text-sky-600 font-semibold">Wajib diisi</span>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">@</span>
                            <input type="text" name="username" id="onboarding-username" required
                                   value="<?php echo e(old('username', $authUser->username ?? '')); ?>"
                                   placeholder="misal: fajar_rpl / ahmad.spd"
                                   class="w-full pl-8 pr-4 py-2.5 bg-slate-50 border <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rose-300 <?php else: ?> border-slate-200/80 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-2xl text-xs text-slate-800 font-semibold focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">
                            Tentukan username yang mudah diingat untuk login berikutnya. Minimal 3 karakter, hanya huruf, angka, tanda hubung (-) dan garis bawah (_).
                        </p>
                    </div>

                    
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">
                            Media Sosial (Instagram / Kontak)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </span>
                            <input type="text" name="medsos" id="onboarding-medsos"
                                   value="<?php echo e(old('medsos', $currentModel?->medsos)); ?>"
                                   placeholder="@nama_akun (Instagram) atau no. WhatsApp aktif"
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                        </div>

                        
                        <div class="mt-2 p-3 bg-gradient-to-r from-sky-50 to-blue-50 border border-sky-100 rounded-2xl text-[11px] text-sky-800 leading-relaxed flex items-start gap-2.5">
                            <span class="text-sm shrink-0">💡</span>
                            <div>
                                <span class="font-extrabold text-sky-900 block mb-0.5">Petunjuk Pengisian Media Sosial:</span>
                                <span>Masukkan akun media sosial aktif (misal Instagram: <code class="bg-white/80 px-1 py-0.5 rounded text-sky-700 font-mono font-bold">@nama_kamu</code> atau link profil). Informasi ini mempermudah pembina dan ketua ekskul menghubungi Anda untuk konfirmasi jadwal dan kegiatan ekskul.</span>
                            </div>
                        </div>
                    </div>

                    
                    <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-2.5">
                        <button type="submit"
                                class="w-full sm:flex-1 py-3 px-5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-sky-200 transition hover:-translate-y-0.5 text-center">
                            Simpan & Mulai Jelajahi
                        </button>

                        <?php if(!empty($authUser->username)): ?>
                            <button type="button" onclick="skipOnboardingSetup()"
                                    class="w-full sm:w-auto py-3 px-5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition text-center">
                                Lewati Dulu
                            </button>
                        <?php endif; ?>
                    </div>
                </form>

                
                <form id="onboarding-skip-form" action="<?php echo e(route('onboarding.complete')); ?>" method="POST" class="hidden">
                    <?php echo csrf_field(); ?>
                </form>

            </div>
        </div>

        <script>
            function previewOnboardingPhoto(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var img = document.getElementById('onboarding-avatar-preview');
                        var fallback = document.getElementById('onboarding-avatar-fallback');
                        if (img && fallback) {
                            img.src = e.target.result;
                            img.classList.remove('hidden');
                            fallback.classList.add('hidden');
                        }
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function skipOnboardingSetup() {
                if (confirm('Anda yakin ingin melewati pengaturan awal? Anda dapat mengubah username dan foto profil kapan saja di menu Profil.')) {
                    document.getElementById('onboarding-skip-form').submit();
                }
            }
        </script>
    <?php endif; ?>
<?php endif; ?><?php /**PATH X:\required\laragon\www\Soul\resources\views/partials/onboarding.blade.php ENDPATH**/ ?>