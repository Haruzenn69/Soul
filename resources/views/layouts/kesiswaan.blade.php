<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SOUL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: { blue: '#2563EB', darkBlue: '#1D4ED8', yellow: '#FACC15', dark: '#0F172A', lightBg: '#F8FAFC' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-theme-lightBg text-theme-dark font-sans antialiased flex min-h-screen">

    <!-- SIDEBAR LEFT (KESISWAAN) -->
    <aside class="w-64 bg-white border-r border-gray-100 flex flex-col justify-between p-6 hidden md:flex shrink-0">
        <div>
            <!-- Logo SOUL -->
            <div class="flex items-center gap-3 mb-10">
                <div>
                    <h1 class="font-extrabold text-base tracking-wide leading-none">SOUL</h1>
                    <span class="text-[10px] text-gray-400 font-semibold tracking-wider uppercase">Panel Kesiswaan</span>
                </div>
            </div>

            <!-- Navigation Menu -->
            <div class="text-[11px] font-bold text-gray-400 tracking-wider uppercase mb-3 px-3">Menu Utama</div>
            <nav class="space-y-1">
                <a href="{{ route('kesiswaan.dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('kesiswaan.dashboard') ? 'bg-blue-50 text-theme-blue' : 'text-gray-500 hover:bg-gray-50 hover:text-theme-dark' }} rounded-2xl font-bold text-xs transition">
                    <span class="text-base"></span> Dashboard
                </a>
                <a href="{{ route('kesiswaan.users.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('kesiswaan.users.*') ? 'bg-blue-50 text-theme-blue' : 'text-gray-500 hover:bg-gray-50 hover:text-theme-dark' }} rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Akun Pengguna
                </a>
                <a href="{{ route('kesiswaan.ekskuls.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('kesiswaan.ekskuls.*') ? 'bg-blue-50 text-theme-blue' : 'text-gray-500 hover:bg-gray-50 hover:text-theme-dark' }} rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Data Ekskul
                </a>
                <a href="{{ route('kesiswaan.kelas.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('kesiswaan.kelas.*') ? 'bg-blue-50 text-theme-blue' : 'text-gray-500 hover:bg-gray-50 hover:text-theme-dark' }} rounded-2xl font-medium text-xs transition">
                    <span class="text-base"></span> Data Kelas
                </a>
            </nav>
        </div>

        <!-- User Profile Card Bottom -->
        <div class="bg-gray-50 p-3.5 rounded-2xl flex items-center justify-between border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-theme-yellow text-theme-dark font-extrabold flex items-center justify-center text-xs shadow-sm uppercase">
                    {{ substr(auth()->user()->username, 0, 2) }}
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold leading-tight">{{ auth()->user()->username }}</h4>
                    <p class="text-[10px] text-gray-400">Staf Kesiswaan</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Logout" class="text-gray-400 hover:text-red-500 text-xs font-bold">✕</button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- TOP NAVBAR HEADER -->
        <header class="px-8 py-5 bg-white border-b border-gray-100 flex items-center justify-between gap-4">
            <form action="#" class="relative w-full max-w-md" onsubmit="return false;">
                <input type="text" placeholder="Cari akun, ekskul, kelas..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs text-theme-dark placeholder-gray-400 focus:outline-none focus:bg-white focus:border-theme-blue transition">
                <span class="absolute left-3.5 top-2.5 text-gray-400 text-sm"></span>
            </form>

            <div class="flex items-center gap-3">
                <div class="bg-theme-yellow/20 text-yellow-600 px-4 py-1.5 rounded-full text-xs font-bold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-theme-yellow"></span> Kesiswaan
                </div>
                <button onclick="document.getElementById('sidebar-mobile').classList.toggle('hidden')" class="md:hidden w-9 h-9 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-xs text-gray-500">
                    ☰
                </button>
            </div>
        </header>

        <!-- DASHBOARD BODY CONTENT -->
        <main class="p-8 space-y-8 overflow-y-auto">

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-semibold flex items-center justify-between">
                    <span>✓ {{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-xs font-semibold flex items-center justify-between">
                    <span>⚠ {{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()">✕</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
