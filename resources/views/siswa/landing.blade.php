<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - SOUL</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    <!-- 1. HERO SECTION -->
    <section class="bg-[#e5e5e5] pt-12 pb-0 relative overflow-hidden flex flex-col items-center text-center h-[450px]">
        <!-- Placeholder Search / Pill di atas -->
        <div class="w-48 h-6 border-2 border-gray-400 rounded-full mb-8"></div>
        
        <h1 class="text-xl md:text-2xl font-extrabold tracking-wider mb-2">WADAH KOMUNITAS SEKOLAH</h1>
        <p class="text-[8px] md:text-[10px] text-gray-600 max-w-md mx-auto uppercase leading-tight mb-8">
            LOREM IPSUM DOLOR SIT AMET CONSECTETUR ADIPISICING ELIT. MAGNI, MAXIME NULLA QUIA AMET.<br>
            LOREM IPSUM DOLOR SIT AMET CONSECTETUR ADIPISICING ELIT. VOLUPTATE.
        </p>

        <!-- Ilustrasi Bawah Hero -->
        <div class="relative w-full max-w-3xl flex justify-center mt-auto">
            <!-- Mockup HP (Tengah) -->
            <div class="w-32 h-48 border-4 border-gray-800 rounded-[20px] bg-white relative z-10 translate-y-4">
                <div class="w-12 h-1 bg-gray-800 rounded-b-xl mx-auto absolute top-0 left-0 right-0"></div>
            </div>
            <!-- Placeholder Ilustrasi Anak Sekolah (Kanan) -->
            <div class="absolute right-10 md:right-32 bottom-0 w-32 h-32 bg-contain bg-bottom bg-no-repeat" style="background-image: url('https://img.freepik.com/free-vector/students-concept-illustration_114360-8450.jpg');"></div>
        </div>
    </section>

    <!-- 2. SECTION: TEMUKAN KOMUNITAS TERBAIK -->
    <section class="py-16 max-w-4xl mx-auto px-6">
        <h2 class="text-center text-lg font-bold tracking-wide mb-8 uppercase">Temukan Komunitas Terbaik</h2>
        
        <!-- Grid 6 Kotak -->
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 md:gap-6">
            <!-- Kotak 1-6 -->
            <div class="w-full aspect-[4/5] bg-[#d9d9d9] rounded-lg"></div>
            <div class="w-full aspect-[4/5] bg-[#d9d9d9] rounded-lg"></div>
            <div class="w-full aspect-[4/5] bg-[#d9d9d9] rounded-lg"></div>
            <div class="w-full aspect-[4/5] bg-[#d9d9d9] rounded-lg"></div>
            <div class="w-full aspect-[4/5] bg-[#d9d9d9] rounded-lg"></div>
            <div class="w-full aspect-[4/5] bg-[#d9d9d9] rounded-lg"></div>
        </div>

        <!-- Tombol Lihat Semua -->
        <div class="flex justify-center mt-10">
            <button class="px-8 py-2 bg-[#e5e5e5] text-gray-700 text-xs font-bold rounded-full hover:bg-gray-300 transition uppercase tracking-wider">
                Lihat Semua Eskul
            </button>
        </div>
    </section>

    <!-- 3. SECTION: GUNAKAN FITUR TERBAIK KAMI -->
    <section class="py-8 max-w-4xl mx-auto px-6">
        <div class="border border-gray-400 rounded-xl p-8">
            <h2 class="text-center text-sm font-bold tracking-wide mb-6 uppercase">Gunakan Fitur Terbaik Kami</h2>
            
            <!-- Grid 2 Kotak Besar -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="w-full aspect-square bg-[#d9d9d9] rounded-md"></div>
                <div class="w-full aspect-square bg-[#d9d9d9] rounded-md"></div>
            </div>
        </div>
    </section>

    <!-- 4. SECTION: TENTANG KAMI -->
    <section class="py-16 max-w-4xl mx-auto px-6 flex flex-col md:flex-row items-center gap-12">
        <!-- Teks Kiri -->
        <div class="w-full md:w-1/2">
            <h2 class="text-base font-bold tracking-wide mb-4 uppercase">Tentang Kami</h2>
            <div class="text-[9px] leading-relaxed text-gray-700 space-y-3 font-medium uppercase text-justify">
                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nesciunt distinctio tempora perferendis sequi error laboriosam? Voluptatibus sapiente asperiores, accusamus quasi laudantium reiciendis optio.</p>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit expedita aliquid accusamus debitis reiciendis ex assumenda, minus qui! Voluptate, praesentium! Necessitatibus sed vel iure assumenda iste quo hic soluta id tempore error voluptatibus.</p>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestias laboriosam id corrupti temporibus eaque. Suscipit, dolor illum.</p>
            </div>
        </div>

        <!-- Ilustrasi Kanan (Kartu Numpuk) -->
        <div class="w-full md:w-1/2 relative flex justify-center items-center h-64">
            <!-- Kartu Belakang -->
            <div class="absolute w-40 h-56 bg-white border border-gray-200 shadow-md transform -rotate-12 -translate-x-12 rounded-sm"></div>
            <!-- Kartu Tengah -->
            <div class="absolute w-40 h-56 bg-white border border-gray-200 shadow-lg transform -rotate-3 rounded-sm"></div>
            <!-- Kartu Depan -->
            <div class="absolute w-40 h-56 bg-white border border-gray-200 shadow-xl transform rotate-6 translate-x-12 rounded-sm"></div>
        </div>
    </section>

    <!-- 5. FOOTER -->
    <footer class="bg-[#d9d9d9] py-16 flex items-center justify-center mt-12">
        <h2 class="text-xs font-extrabold tracking-widest text-gray-800 uppercase">Kontak</h2>
    </footer>

</body>
</html>