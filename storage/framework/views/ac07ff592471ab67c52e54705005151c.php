<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Portal Siswa'); ?> - SOUL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        #sidebar-mobile { position: fixed; }
    </style>
    <?php echo $__env->make('partials.responsive-tables', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
    <?php echo $__env->yieldContent('styles'); ?>
</head>
<body class="siswa-layout bg-gradient-to-br from-sky-50 via-white to-amber-50 text-slate-800 font-sans antialiased min-h-screen md:flex selection:bg-sky-100 selection:text-sky-700 overflow-x-hidden">
    <?php echo $__env->make('partials.pill-sidebar', [
        'psTitle' => 'Menu Siswa',
        'psLogoBrand' => 'SOUL',
        'psDashboardUrl' => route('siswa.dashboard'),
        'psNotifUrl' => route('siswa.notifikasi'),
        'psProfileUrl' => route('profile.edit'),
        'psItems' => [
            ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => route('siswa.dashboard'), 'is' => 'siswa.dashboard'],
            ['icon' => 'document', 'label' => 'Katalog Ekskul', 'url' => route('siswa.katalog'), 'is' => ['siswa.katalog', 'ekskul.detail']],
            ['icon' => 'calendar', 'label' => 'Presensi & Kegiatan', 'url' => route('siswa.presensi'), 'is' => 'siswa.presensi'],
            ['icon' => 'bars', 'label' => 'Rekap Absensi', 'url' => route('siswa.rekap'), 'is' => 'siswa.rekap'],
            ['icon' => 'user', 'label' => 'Profil Saya', 'url' => route('profile.edit'), 'is' => 'profile.edit'],
        ],
        'psMore' => [
            ['label' => 'Daftar Ekskul', 'url' => route('siswa.daftar-ekskul'), 'is' => 'siswa.daftar-ekskul'],
            ['label' => 'Ajukan Keluar', 'url' => route('siswa.pengajuan-keluar'), 'is' => ['siswa.pengajuan-keluar', 'siswa.pengajuan-keluar.store']],
        ],
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="w-full flex-1 flex flex-col min-w-0 ps-page">

        <!-- TOP NAVBAR HEADER -->
        <header class="px-4 md:px-8 py-4 bg-white/70 backdrop-blur-lg border-b border-sky-100 flex items-center justify-between gap-3 md:gap-4 sticky top-0 z-30">
            <!-- KIRI: Hamburger (Mobile) + Logo + Search -->
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <button onclick="openSidebar()" aria-label="Buka menu navigasi" class="w-9 h-9 rounded-full bg-white border border-sky-100 flex items-center justify-center text-slate-500 md:hidden shadow-sm shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex items-center gap-2 md:hidden shrink-0">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-sky-300">S</div>
                    <span class="font-extrabold text-sm tracking-tight text-slate-900">SOUL</span>
                </div>

                <?php $__env->startSection('search'); ?>
                    <form method="GET" action="<?php echo e(route('siswa.katalog')); ?>" class="relative w-full max-w-md hidden sm:block">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                        <input type="text" name="cari" value="<?php echo e(request('cari')); ?>" placeholder="Cari ekskul..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
                    </form>
                <?php echo $__env->yieldSection(); ?>
            </div>

            <!-- KANAN: Role Badge + Notifikasi + Logout -->
            <div class="flex items-center gap-2 md:gap-3 shrink-0">
                <div class="bg-sky-50 text-sky-700 border border-sky-100 px-3 py-1 rounded-lg text-xs font-semibold flex items-center gap-2 hidden sm:block">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span> Siswa
                </div>

                <a href="<?php echo e(route('siswa.notifikasi')); ?>" aria-label="Notifikasi" class="w-9 h-9 rounded-xl <?php echo e(request()->routeIs('siswa.notifikasi') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-700 border-sky-200' : 'bg-white text-slate-600 hover:bg-sky-50 hover:border-sky-200'); ?> border border-sky-100 flex items-center justify-center text-xs relative transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <?php if(($unreadNotifCount ?? 0) > 0): ?>
                        <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-br from-amber-400 to-yellow-500 absolute top-1.5 right-1.5 border-2 border-white shadow-sm"></span>
                    <?php endif; ?>
                </a>

                <form method="POST" action="<?php echo e(route('logout')); ?>" class="hidden sm:block">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-500 px-4 py-2 rounded-lg text-xs font-semibold border border-red-100 transition shadow-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- MAIN PAGE CONTENT -->
        <main class="<?php echo $__env->yieldContent('main-class', 'p-4 md:p-8 space-y-6 overflow-y-auto'); ?>">
            <?php if(session('success') && !request()->routeIs('siswa.dashboard')): ?>
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-xl animate-fade-up">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error') && !request()->routeIs('siswa.dashboard')): ?>
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl animate-fade-up">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function openSidebar() {
            const sb = document.getElementById('sidebar-mobile');
            const ov = document.getElementById('sidebar-overlay');
            if (sb) sb.classList.remove('hidden');
            if (ov) ov.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        function closeSidebar() {
            const sb = document.getElementById('sidebar-mobile');
            const ov = document.getElementById('sidebar-overlay');
            if (sb) sb.classList.add('hidden');
            if (ov) ov.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    </script>

    <?php echo $__env->make('partials.onboarding', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\Soul\resources\views/layouts/siswa.blade.php ENDPATH**/ ?>