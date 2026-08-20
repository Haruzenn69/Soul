<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SOULERS</title>
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
                            yellow: '#EAB308',    /* Kuning Akses Accent */
                            lightBg: '#E2E8F0',   /* Latar Luar Soft Gray-Blue */
                            dark: '#0F172A'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-theme-lightBg min-h-screen flex items-center justify-center p-4 font-sans antialiased relative overflow-hidden">

    <!-- Latar Belakang Elemen Lingkaran Soft/Abstrak -->
    <div class="absolute -top-16 -left-16 w-80 h-80 bg-slate-300/40 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-blue-200/30 rounded-full blur-3xl pointer-events-none"></div>

    <!-- MAIN CONTAINER CARD -->
    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2 min-h-[500px] relative z-10">
        
        <!-- SISI KIRI: Banner Biru & Kartu Miring -->
        <div class="bg-theme-blue p-8 md:p-12 flex flex-col justify-center items-center text-white relative overflow-hidden min-h-[300px] md:min-h-full">
            
            <!-- Kartu Bertumpuk (Putih & Kuning Miring) -->
            <div class="relative w-48 h-48 mb-8 flex items-center justify-center">
                <!-- Kartu Putih Belakang (Miring Kiri) -->
                <div class="absolute w-28 h-36 bg-white rounded-2xl shadow-lg transform -rotate-12 -translate-x-6"></div>
                <!-- Kartu Kuning Depan (Miring Kanan) -->
                <div class="absolute w-28 h-36 bg-theme-yellow rounded-2xl shadow-2xl transform rotate-6 translate-x-4 border-2 border-yellow-400/30"></div>
            </div>

            <!-- Teks Sisi Kiri -->
            <h2 class="text-xl md:text-2xl font-bold tracking-wide text-center leading-snug">
                Bergabung bersama<br>kami
            </h2>
        </div>

        <!-- SISI KANAN: Form Login -->
        <div class="p-8 md:p-12 flex flex-col justify-center items-center text-center bg-white">
            
            <!-- Logo Bulat Kuning di Atas -->
            <div class="w-12 h-12 rounded-2xl bg-theme-yellow mb-4 flex items-center justify-center shadow-md">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain rounded-xl" onerror="this.onerror=null; this.classList.add('hidden');">
            </div>

            <!-- Header Teks -->
            <h1 class="text-lg md:text-xl font-extrabold tracking-wide text-theme-dark uppercase">
                HELLO SOULERS
            </h1>
            <p class="text-xs text-gray-400 font-medium mb-8">
                Masuk dan akses semua fitur kami
            </p>

            <!-- Form Input -->
            <form action="/login" method="POST" class="w-full max-w-xs space-y-4">
                @csrf
                
                <!-- Input Email / Username -->
                <div>
                    <input type="text" name="email" placeholder="Email / Username" required class="w-full px-5 py-3.5 rounded-full bg-gray-100/80 border border-transparent text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-theme-blue focus:outline-none transition shadow-inner">
                </div>

                <!-- Input Password -->
                <div>
                    <input type="password" name="password" placeholder="Password" required class="w-full px-5 py-3.5 rounded-full bg-gray-100/80 border border-transparent text-xs text-gray-800 placeholder-gray-400 focus:bg-white focus:border-theme-blue focus:outline-none transition shadow-inner">
                </div>

                <!-- Tombol Submit (Kuning Soft) -->
                <div class="pt-4">
                    <button type="submit" class="w-full py-3.5 bg-theme-yellow hover:bg-yellow-500 text-gray-900 font-bold text-xs rounded-full transition shadow-md hover:shadow-lg active:scale-[0.98]">
                        Masuk
                    </button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>