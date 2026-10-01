<?php
    /**
     * Sumbu minggu: kapan ekskul bertemu.
     *
     * Ini data, bukan hiasan — jadwallah yang menentukan seorang siswa bisa
     * bergabung atau tidak, jadi ia digambar, bukan ditulis sebagai teks abu.
     * Kalau format jadwal tidak dikenali, teks aslinya ditampilkan apa adanya.
     *
     * @var \App\Models\Ekskul $ekskul
     * @var bool $besar
     * @var string $kelas
     */
    $jadwal = \App\Support\EkskulInfo::jadwal($ekskul->jadwal);
    $besar = $besar ?? false;
    $kelas = $kelas ?? '';
?>

<div class="soul-jadwal <?php echo e($kelas); ?> <?php if($besar): ?> soul-minggu--besar <?php endif; ?>">
    <?php if($jadwal['hari']): ?>
        <div
            class="soul-minggu"
            role="img"
            aria-label="Bertemu setiap hari <?php echo e(\App\Support\EkskulInfo::hariTerbaca($jadwal['hari'])); ?>"
        >
            <?php $__currentLoopData = \App\Support\EkskulInfo::HARI; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $angka => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $aktif = in_array($angka, $jadwal['hari'], true); ?>
                <div class="soul-minggu-kolom">
                    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['soul-minggu-batang', 'soul-minggu-batang--aktif' => $aktif]); ?>"></span>
                    <span class="soul-minggu-sumbu"></span>
                    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['soul-minggu-label', 'soul-minggu-label--aktif' => $aktif]); ?>"><?php echo e($label); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <p class="mt-2.5 text-[13px] font-semibold tabular-nums text-slate-800">
            <?php echo e($jadwal['waktu'] ?? $jadwal['mentah']); ?>

        </p>
    <?php else: ?>
        <p class="text-[13px] leading-relaxed text-slate-500">
            <?php echo e($jadwal['mentah'] ?? 'Jadwal belum diatur'); ?>

        </p>
    <?php endif; ?>
</div>
<?php /**PATH X:\required\laragon\www\Soul\resources\views/partials/ekskul-minggu.blade.php ENDPATH**/ ?>