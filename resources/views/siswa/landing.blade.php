<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - KOMUNITAS</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: {
                            blue: '#2563EB',      /* Biru Utama */
                            darkBlue: '#1D4ED8',  /* Biru Hover */
                            yellow: '#FACC15',    /* Kuning Akses Accent */
                            dark: '#0F172A',      /* Gelap Teks */
                            light: '#F8FAFC'     /* Putih/Latar Belakang */
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white text-theme-dark font-sans antialiased selection:bg-theme-yellow selection:text-theme-dark">

    <!-- HEADER / NAVBAR (Sembunyi saat top, muncul transparan saat scroll) -->
    <header id="main-header" class="fixed top-0 left-0 right-0 z-50 px-8 py-4 flex items-center justify-between opacity-0 -translate-y-full pointer-events-none transition-all duration-300">
        <!-- KIRI: Logo & Nama -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-theme-blue flex items-center justify-center text-white font-bold text-sm shadow-md">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover rounded-full" onerror="this.onerror=null; this.src='https://via.placeholder.com/150/2563EB/FFFFFF?text=K';">
            </div>
            <span class="font-extrabold tracking-tight text-lg text-theme-dark uppercase">KOMUNITAS</span>
        </div>

        <!-- TENGAH: Menu Navigasi -->
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-700">
            <a href="#komunitas" class="hover:text-theme-blue transition">Komunitas</a>
            <a href="#fitur" class="hover:text-theme-blue transition">Fitur</a>
            <a href="#tentang" class="hover:text-theme-blue transition">Tentang</a>
        </nav>

        <!-- KANAN: Tombol Masuk -->
        <div>
            <a href="/login" class="px-6 py-2 bg-theme-dark hover:bg-gray-800 text-white text-xs font-semibold rounded-full transition flex items-center gap-2 shadow-sm">
                Masuk <span>&rarr;</span>
            </a>
        </div>
    </header>

    <!-- 1. HERO SECTION -->
    <section class="relative pt-12 md:pt-16 bg-white overflow-hidden flex flex-col items-center text-center min-h-screen justify-between">
        <!-- Top Nav Static di Hero Awal (Sesuai Referensi Gambar) -->
        <div class="w-full max-w-6xl px-8 flex items-center justify-between mb-12">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-theme-blue flex items-center justify-center text-white font-bold text-sm">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover rounded-full" onerror="this.onerror=null; this.src='https://via.placeholder.com/150/2563EB/FFFFFF?text=K';">
                </div>
                <span class="font-extrabold tracking-tight text-lg text-theme-dark uppercase">KOMUNITAS</span>
            </div>
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#komunitas" class="hover:text-theme-blue transition">Komunitas</a>
                <a href="#fitur" class="hover:text-theme-blue transition">Fitur</a>
                <a href="#tentang" class="hover:text-theme-blue transition">Tentang</a>
            </nav>
            <a href="/login" class="px-6 py-2 bg-theme-dark hover:bg-gray-800 text-white text-xs font-semibold rounded-full transition flex items-center gap-2">
                Masuk <span>&rarr;</span>
            </a>
        </div>

        <!-- Teks Utama Hero -->
        <div class="max-w-3xl px-6 flex flex-col items-center">
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-theme-dark leading-tight mb-4">
                Temukan <span class="relative inline-block">komunitas<span class="absolute bottom-1 left-0 w-full h-1.5 bg-theme-yellow -z-10 rounded-full"></span></span> terbaikmu di sini
            </h1>
            <p class="text-xs md:text-sm text-gray-500 max-w-md mx-auto mb-8 font-normal leading-relaxed">
                Bergabung dengan komunitas sekolah yang sesuai dengan minat, bakat, dan passion-mu.
            </p>
            <a href="#komunitas" class="px-7 py-3 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full shadow-lg shadow-blue-500/20 transition flex items-center gap-2">
                Jelajahi Komunitas <span>&rarr;</span>
            </a>
        </div>

        <!-- Gambar Siswa & Gelombang Biru Bawah -->
        <div class="w-full relative mt-8 flex justify-center items-end">
            <!-- Background Latar Biru di Bawah -->
            <div class="absolute bottom-0 w-full h-24 md:h-36 bg-theme-blue -z-0"></div>
            
            <!-- Gambar Karakter Siswa -->
            <div class="relative z-10 max-w-4xl px-4 w-full flex justify-center">
                <img src="https://img.freepik.com/free-photo/group-diverse-grads-holding-their-diplomas_23-2148943343.jpg" alt="Siswa Komunitas" class="max-h-[350px] md:max-h-[420px] object-contain drop-shadow-xl">
            </div>
        </div>
    </section>

    <!-- 2. SECTION: TEMUKAN KOMUNITAS TERBAIK -->
    <section id="komunitas" class="py-20 bg-white max-w-6xl mx-auto px-6">
        <h2 class="text-center text-xl md:text-2xl font-bold tracking-tight mb-12 text-theme-dark">
            Eksplorasi Komunitas Populer
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <div class="aspect-[4/3] bg-theme-light rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition"></div>
            <div class="aspect-[4/3] bg-theme-light rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition"></div>
            <div class="aspect-[4/3] bg-theme-light rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition"></div>
            <div class="aspect-[4/3] bg-theme-light rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition"></div>
            <div class="aspect-[4/3] bg-theme-light rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition"></div>
            <div class="aspect-[4/3] bg-theme-light rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition"></div>
        </div>
    </section>

    <!-- 3. SECTION: FITUR -->
    <section id="fitur" class="py-16 bg-theme-light border-y border-gray-100">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-center text-xl md:text-2xl font-bold tracking-tight mb-12 text-theme-dark">
                Fitur Unggulan
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="h-64 bg-white rounded-2xl border border-gray-200/60 shadow-sm"></div>
                <div class="h-64 bg-white rounded-2xl border border-gray-200/60 shadow-sm"></div>
            </div>
        </div>
    </section>

    <!-- 4. SECTION: TENTANG -->
    <section id="tentang" class="py-20 max-w-6xl mx-auto px-6">
        <div class="flex flex-col md:flex-row items-center gap-12">
            <div class="w-full md:w-1/2 text-left">
                <h2 class="text-2xl font-bold mb-4 text-theme-dark">Tentang Platform Kami</h2>
                <p class="text-xs md:text-sm text-gray-600 leading-relaxed">
                    Wadah resmi untuk memudahkan siswa menemukan kegiatan ekstrakurikuler, berkolaborasi dengan sesama anggota, dan mengorganisir kegiatan komunitas sekolah dengan praktis.
                </p>
            </div>
            <div class="w-full md:w-1/2 h-64 bg-theme-blue/10 rounded-2xl border border-theme-blue/20"></div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-theme-dark py-10 text-center text-white text-xs">
        <p>&copy; 2026 KOMUNITAS. All rights reserved.</p>
    </footer>

    <!-- SCRIPT TAMPILKAN HEADER TRANSPARAN SAAT SCROLL -->
    <script>
        window.addEventListener('scroll', function() {
            const header = document.getElementById('main-header');
            if (window.scrollY > 100) {
                // Saat scroll melampaui 100px: munculkan header dengan latar transparan blur
                header.classList.remove('opacity-0', '-translate-y-full', 'pointer-events-none');
                header.classList.add('opacity-100', 'translate-y-0', 'bg-white/80', 'backdrop-blur-md', 'shadow-sm', 'border-b', 'border-gray-100');
            } else {
                // Saat berada paling atas: sembunyikan header melayang
                header.classList.add('opacity-0', '-translate-y-full', 'pointer-events-none');
                header.classList.remove('opacity-100', 'translate-y-0', 'bg-white/80', 'backdrop-blur-md', 'shadow-sm', 'border-b', 'border-gray-100');
            }
        });
    </script>

</body>
</html>