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
                            blue: '#2563EB',
                            darkBlue: '#1D4ED8',
                            yellow: '#EAB308',
                            lightBg: '#E2E8F0',
                            dark: '#0F172A'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark min-h-screen flex items-center justify-center p-4 font-sans antialiased relative overflow-hidden">

    <!-- Background Hero Section - Desktop -->
    <div class="absolute inset-0">
        <img src="{{ asset('images/firefly.jpg') }}" class="absolute inset-0 w-full h-full object-cover opacity-30" alt="Hero background">
    </div>

    <!-- Background Hero Section - Mobile -->
    <img src="{{ asset('images/firefly-mobile.jpeg') }}" class="hidden w-full h-full object-cover opacity-30" alt="Hero background mobile" style="max-width: 100%;">

    <!-- Abstract shapes -->
    <div class="absolute -top-16 -left-16 w-80 h-80 bg-slate-300/40 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-20 -right-20 w-96 h-96 bg-white/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- MAIN CONTAINER CARD -->
    <div class="w-full max-w-7xl bg-white/80 rounded-3xl shadow-2xl overflow-hidden grid grid-cols-2 min-h-[650px] relative z-10 backdrop-blur-lg">

        <!-- SISI KIRI: Banner Biru & Kartu Miring -->
        <div class="bg-theme-blue p-8 md:p-16 flex flex-col justify-center items-center text-white relative overflow-hidden min-h-[350px] md:min-h-full">
            <div class="relative w-48 h-48 mb-8 flex items-center justify-center">
                <div class="absolute w-28 h-36 bg-white rounded-2xl shadow-lg transform -rotate-12 -translate-x-6"></div>
                <div class="absolute w-28 h-36 bg-theme-yellow rounded-2xl shadow-2xl transform rotate-6 translate-x-4 border-2 border-yellow-400/30"></div>
            </div>

            <h2 class="text-xl md:text-2xl font-bold tracking-wide text-center leading-snug">
                Bergabung bersama<br>kami
            </h2>
        </div>

        <!-- SISI KANAN: Form Login -->
        <div class="p-8 md:p-16 flex flex-col justify-center items-center text-center bg-white min-h-[400px]">
            
            <div class="w-12 h-12 rounded-2xl bg-theme-yellow mb-4 flex items-center justify-center shadow-md">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain rounded-xl" onerror="this.onerror=null; this.classList.add('hidden');">
            </div>

            <h1 class="text-lg md:text-xl font-extrabold tracking-wide text-theme-dark uppercase">
                HELLO SOULERS
            </h1>
            <p class="text-xs text-gray-400 font-medium mb-6">
                Masuk dan akses semua fitur kami
            </p>

            <!-- ALERT ERROR (DITAMBAHKAN) -->
            @if ($errors->any())
                <div class="w-full max-w-xs mb-4 p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 text-xs font-medium text-left">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form Input -->
            <form action="{{ route('login') }}" method="POST" class="w-full space-y-4">
                @csrf
                
                <!-- Input Email -->
                <div>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required autofocus class="w-full px-4 py-3 rounded-full bg-gray-100/80 border border-transparent text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:border-theme-blue focus:outline-none transition shadow-inner">
                </div>

                <!-- Input Password -->
                <div>
                    <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-3 rounded-full bg-gray-100/80 border border-transparent text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:border-theme-blue focus:outline-none transition shadow-inner">
                </div>

                <!-- Tombol Submit -->
                <div>
                    <button type="submit" class="w-full py-3 rounded-full bg-theme-yellow hover:bg-yellow-500 text-gray-900 font-bold text-xs transition shadow-md hover:shadow-lg active:scale-[0.98]">
                        Masuk
                    </button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>