<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - SOUL</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        coffee: {
                            dark: '#561C24',
                            maroon: '#6D2932',
                            mocha: '#3D2314', // WARNA KE-3 (Mocha / Warm Brown)
                            beige: '#C7B7A3',
                            light: '#E8D8C4'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-coffee-light text-coffee-mocha font-sans antialiased">

    <!-- HEADER / NAVBAR (DENGAN EFEK SCROLL TRANSPARAN) -->
    <header id="main-header" class="px-6 py-4 flex items-center justify-between sticky top-0 z-50 bg-coffee-maroon border-b border-transparent transition-all duration-300">
        <!-- KIRI: Logo Aplikasi Bulat -->
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-coffee-light p-0.5 overflow-hidden flex items-center justify-center border-2 border-coffee-beige shadow-sm">
                <img src="{{ asset('images/logo.png') }}" alt="Logo SOUL" class="w-full h-full object-cover rounded-full" onerror="this.onerror=null; this.src='https://via.placeholder.com/150/561C24/E8D8C4?text=SOUL';">
            </div>
            <span class="text-white font-extrabold tracking-wider text-lg hidden sm:block">SOUL</span>
        </div>

        <!-- TENGAH: Fitur Pencarian (Search Bar) -->
        <div class="flex-1 max-w-md mx-4">
            <div class="relative flex items-center">
                <input type="text" placeholder="Cari ekstrakurikuler..." class="w-full pl-9 pr-4 py-1.5 text-xs bg-coffee-light/90 border border-coffee-beige rounded-full text-coffee-mocha placeholder-coffee-maroon/70 focus:outline-none focus:ring-2 focus:ring-coffee-beige transition shadow-inner">
                <svg class="w-4 h-4 absolute left-3 text-coffee-maroon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- KANAN: Tombol Login -->
        <div>
            <a href="/login" class="px-5 py-1.5 bg-coffee-beige hover:bg-coffee-light text-coffee-mocha text-xs font-bold rounded-full transition shadow-sm border border-coffee-light/50">
                Login
            </a>
        </div>
    </header>

    <!-- 1. HERO SECTION -->
    <section class="bg-coffee-beige/50 pt-10 pb-0 relative overflow-hidden flex flex-col items-center text-center min-h-[420px]">
        <h1 class="text-xl md:text-2xl font-extrabold tracking-wider mb-2 text-coffee-mocha uppercase">Wadah Komunitas Sekolah</h1>
        <p class="text-[9px] md:text-[11px] text-coffee-maroon font-medium max-w-md mx-auto uppercase leading-relaxed mb-8 px-4">
            LOREM IPSUM DOLOR SIT AMET CONSECTETUR ADIPISICING ELIT. MAGNI, MAXIME NULLA QUIA AMET.<br>
            LOREM IPSUM DOLOR SIT AMET CONSECTETUR ADIPISICING ELIT. VOLUPTATE.
        </p>

        <!-- Ilustrasi Bawah Hero -->
        <div class="relative w-full max-w-3xl flex justify-center mt-auto">
            <!-- Mockup HP (Tengah) menggunakan aksen Mocha Brown -->
            <div class="w-32 h-48 border-4 border-coffee-mocha rounded-[20px] bg-white relative z-10 translate-y-4 shadow-lg">
                <div class="w-12 h-1 bg-coffee-mocha rounded-b-xl mx-auto absolute top-0 left-0 right-0"></div>
            </div>
            <!-- Placeholder Ilustrasi Anak Sekolah (Kanan) -->
            <div class="absolute right-6 md:right-32 bottom-0 w-32 h-32 bg-contain bg-bottom bg-no-repeat opacity-90" style="background-image: url('https://img.freepik.com/free-vector/students-concept-illustration_114360-8450.jpg');"></div>
        </div>
    </section>

    <!-- 2. SECTION: TEMUKAN KOMUNITAS TERBAIK -->
    <section class="py-14 max-w-4xl mx-auto px-6">
        <h2 class="text-center text-lg font-bold tracking-wide mb-8 uppercase text-coffee-mocha">Temukan Komunitas Terbaik</h2>
        
        <!-- Grid 6 Kotak (Variasi 3 Warna) -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
            <div class="w-full aspect-[4/5] bg-coffee-beige rounded-lg border border-coffee-mocha/20 shadow-sm hover:shadow-md transition"></div>
            <div class="w-full aspect-[4/5] bg-coffee-mocha/10 rounded-lg border border-coffee-maroon/20 shadow-sm hover:shadow-md transition"></div>
            <div class="w-full aspect-[4/5] bg-coffee-beige rounded-lg border border-coffee-mocha/20 shadow-sm hover:shadow-md transition"></div>
            <div class="w-full aspect-[4/5] bg-coffee-mocha/10 rounded-lg border border-coffee-maroon/20 shadow-sm hover:shadow-md transition"></div>
            <div class="w-full aspect-[4/5] bg-coffee-beige rounded-lg border border-coffee-mocha/20 shadow-sm hover:shadow-md transition"></div>
            <div class="w-full aspect-[4/5] bg-coffee-mocha/10 rounded-lg border border-coffee-maroon/20 shadow-sm hover:shadow-md transition"></div>
        </div>

        <!-- Tombol Lihat Semua -->
        <div class="flex justify-center mt-10">
            <button class="px-8 py-2.5 bg-coffee-maroon hover:bg-coffee-dark text-white text-xs font-bold rounded-full transition uppercase tracking-wider shadow-md">
                Lihat Semua Eskul
            </button>
        </div>
    </section>

    <!-- 3. SECTION: GUNAKAN FITUR TERBAIK KAMI -->
    <section class="py-6 max-w-4xl mx-auto px-6">
        <div class="border-2 border-coffee-mocha/30 bg-coffee-beige/20 rounded-xl p-8 shadow-sm">
            <h2 class="text-center text-sm font-bold tracking-wide mb-6 uppercase text-coffee-mocha">Gunakan Fitur Terbaik Kami</h2>
            
            <!-- Grid 2 Kotak Besar -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="w-full aspect-square bg-coffee-beige rounded-md border border-coffee-mocha/20"></div>
                <div class="w-full aspect-square bg-coffee-mocha/15 rounded-md border border-coffee-maroon/20"></div>
            </div>
        </div>
    </section>

    <!-- 4. SECTION: TENTANG KAMI -->
    <section class="py-14 max-w-4xl mx-auto px-6 flex flex-col md:flex-row items-center gap-12">
        <!-- Teks Kiri -->
        <div class="w-full md:w-1/2">
            <h2 class="text-base font-bold tracking-wide mb-4 uppercase text-coffee-mocha">Tentang Kami</h2>
            <div class="text-[9px] leading-relaxed text-coffee-mocha/90 space-y-3 font-semibold uppercase text-justify">
                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nesciunt distinctio tempora perferendis sequi error laboriosam? Voluptatibus sapiente asperiores, accusamus quasi laudantium reiciendis optio.</p>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit expedita aliquid accusamus debitis reiciendis ex assumenda, minus qui! Voluptate, praesentium! Necessitatibus sed vel iure assumenda iste quo hic soluta id tempore error voluptatibus.</p>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestias laboriosam id corrupti temporibus eaque. Suscipit, dolor illum.</p>
            </div>
        </div>

        <!-- Ilustrasi Kanan (Kartu Numpuk dengan Kombinasi 3 Warna) -->
        <div class="w-full md:w-1/2 relative flex justify-center items-center h-64">
            <!-- Kartu 1: Mocha Brown -->
            <div class="absolute w-40 h-56 bg-coffee-mocha text-white border-2 border-coffee-mocha shadow-md transform -rotate-12 -translate-x-12 rounded-md"></div>
            <!-- Kartu 2: Cream -->
            <div class="absolute w-40 h-56 bg-coffee-beige border-2 border-coffee-maroon/30 shadow-lg transform -rotate-3 rounded-md"></div>
            <!-- Kartu 3: Maroon -->
            <div class="absolute w-40 h-56 bg-coffee-dark border-2 border-coffee-maroon shadow-xl transform rotate-6 translate-x-12 rounded-md"></div>
        </div>
    </section>

    <!-- 5. FOOTER (Menggunakan Warna Ke-3 Mocha Brown) -->
    <footer class="bg-coffee-mocha py-12 flex items-center justify-center mt-12 text-coffee-light">
        <h2 class="text-xs font-extrabold tracking-widest uppercase">Kontak</h2>
    </footer>

    <!-- JAVASCRIPT UNTUK DETEKSI SCROLL HEADER TRANSPARAN -->
    <script>
        window.addEventListener('scroll', function() {
            const header = document.getElementById('main-header');
            if (window.scrollY > 30) {
                // Pas di-scroll ke bawah: jadi transparan, ada blur, dan border tipis
                header.classList.remove('bg-coffee-maroon');
                header.classList.add('bg-coffee-maroon/80', 'backdrop-blur-md', 'shadow-md', 'border-coffee-beige/20');
            } else {
                // Pas di posisi paling atas: balik ke marun solid
                header.classList.add('bg-coffee-maroon');
                header.classList.remove('bg-coffee-maroon/80', 'backdrop-blur-md', 'shadow-md', 'border-coffee-beige/20');
            }
        });
    </script>

</body>
</html>