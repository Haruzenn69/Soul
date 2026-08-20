<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOUL - Sistem Ekstrakurikuler</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: {
                            blue: '#2563EB',
                            darkBlue: '#1D4ED8',
                            yellow: '#FACC15',
                            dark: '#0F172A',
                            light: '#F8FAFC'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white text-theme-dark font-sans antialiased selection:bg-theme-yellow selection:text-theme-dark">

    <!-- HEADER -->
    <header id="main-header" class="fixed top-0 left-0 right-0 z-50 px-8 py-4 flex items-center justify-between opacity-0 -translate-y-full pointer-events-none transition-all duration-300">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-theme-blue flex items-center justify-center text-white font-bold text-sm shadow-md">S</div>
            <span class="font-extrabold tracking-tight text-lg text-theme-dark uppercase">SOUL</span>
        </div>
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-700">
            <a href="#ekskul" class="hover:text-theme-blue transition">Ekskul</a>
            <a href="#tentang" class="hover:text-theme-blue transition">Tentang</a>
        </nav>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">
                    Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-theme-dark text-xs font-semibold rounded-full transition">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2 bg-theme-dark hover:bg-gray-800 text-white text-xs font-semibold rounded-full transition">
                    Masuk <span>&rarr;</span>
                </a>
            @endauth
        </div>
    </header>

    <!-- HERO -->
    <section class="relative pt-12 md:pt-16 bg-white overflow-hidden flex flex-col items-center text-center min-h-screen justify-between">
        <div class="w-full max-w-6xl px-8 flex items-center justify-between mb-12">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-theme-blue flex items-center justify-center text-white font-bold text-sm">S</div>
                <span class="font-extrabold tracking-tight text-lg text-theme-dark uppercase">SOUL</span>
            </div>
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                <a href="#ekskul" class="hover:text-theme-blue transition">Ekskul</a>
                <a href="#tentang" class="hover:text-theme-blue transition">Tentang</a>
            </nav>
            @auth
                <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2 bg-theme-dark hover:bg-gray-800 text-white text-xs font-semibold rounded-full transition">
                    Masuk <span>&rarr;</span>
                </a>
            @endauth
        </div>

        <div class="max-w-3xl px-6 flex flex-col items-center">
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-theme-dark leading-tight mb-4">
                Temukan <span class="relative inline-block">ekstrakurikuler<span class="absolute bottom-1 left-0 w-full h-1.5 bg-theme-yellow -z-10 rounded-full"></span></span> terbaikmu di sini
            </h1>
            <p class="text-xs md:text-sm text-gray-500 max-w-md mx-auto mb-8 font-normal leading-relaxed">
                Jelajahi kegiatan ekstrakurikuler yang sesuai dengan minat, bakat, dan passion-mu.
            </p>
            <a href="#ekskul" class="px-7 py-3 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full shadow-lg shadow-blue-500/20 transition flex items-center gap-2">
                Jelajahi Ekskul <span>&rarr;</span>
            </a>
        </div>

        <div class="w-full relative mt-8 flex justify-center items-end">
            <div class="absolute bottom-0 w-full h-24 md:h-36 bg-theme-blue -z-0"></div>
            <div class="relative z-10 max-w-4xl px-4 w-full flex justify-center">
                <img src="https://img.freepik.com/free-photo/group-diverse-grads-holding-their-diplomas_23-2148943343.jpg" alt="Siswa" class="max-h-[350px] md:max-h-[420px] object-contain drop-shadow-xl">
            </div>
        </div>
    </section>

    <!-- KATALOG EKSKUL -->
    <section id="ekskul" class="py-20 bg-white max-w-6xl mx-auto px-6">
        <h2 class="text-center text-xl md:text-2xl font-bold tracking-tight mb-12 text-theme-dark">
            Ekstrakurikuler
        </h2>

        @if($ekskuls->isEmpty())
            <p class="text-center text-gray-400">Belum ada ekstrakurikuler yang tersedia.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($ekskuls as $ekskul)
                    <div class="bg-theme-light rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition p-6 flex flex-col">
                        <h3 class="font-bold text-lg text-theme-dark mb-2">{{ $ekskul->nama_ekskul }}</h3>
                        <p class="text-xs text-gray-500 mb-1">Pembina: {{ $ekskul->pembina->nama ?? '-' }}</p>
                        <p class="text-xs text-gray-500 mb-1">Pelatih: {{ $ekskul->pelatih->nama ?? '-' }}</p>
                        <p class="text-xs text-gray-500 mb-3">Jadwal: {{ $ekskul->jadwal ?? '-' }}</p>
                        <p class="text-sm text-gray-600 flex-1 mb-4">{{ $ekskul->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                        <div class="mt-auto">
                            @if($ekskul->is_open_recruitment)
                                <span class="w-full text-center block py-2 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    Pendaftaran Dibuka
                                </span>
                            @else
                                <span class="w-full text-center block py-2 bg-gray-200 text-gray-500 text-xs font-semibold rounded-full">
                                    Pendaftaran Ditutup
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- TENTANG -->
    <section id="tentang" class="py-20 max-w-6xl mx-auto px-6">
        <div class="flex flex-col md:flex-row items-center gap-12">
            <div class="w-full md:w-1/2 text-left">
                <h2 class="text-2xl font-bold mb-4 text-theme-dark">Tentang SOUL</h2>
                <p class="text-xs md:text-sm text-gray-600 leading-relaxed">
                    SOUL adalah platform manajemen ekstrakurikuler yang memudahkan siswa menemukan, mendaftar, dan mengelola kegiatan ekstrakurikuler di sekolah secara digital.
                </p>
            </div>
            <div class="w-full md:w-1/2 h-64 bg-theme-blue/10 rounded-2xl border border-theme-blue/20"></div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-theme-dark py-10 text-center text-white text-xs">
        <p>&copy; 2026 SOUL. All rights reserved.</p>
    </footer>

    <script>
        window.addEventListener('scroll', function() {
            const header = document.getElementById('main-header');
            if (window.scrollY > 100) {
                header.classList.remove('opacity-0', '-translate-y-full', 'pointer-events-none');
                header.classList.add('opacity-100', 'translate-y-0', 'bg-white/80', 'backdrop-blur-md', 'shadow-sm', 'border-b', 'border-gray-100');
            } else {
                header.classList.add('opacity-0', '-translate-y-full', 'pointer-events-none');
                header.classList.remove('opacity-100', 'translate-y-0', 'bg-white/80', 'backdrop-blur-md', 'shadow-sm', 'border-b', 'border-gray-100');
            }
        });
    </script>
</body>
</html>
