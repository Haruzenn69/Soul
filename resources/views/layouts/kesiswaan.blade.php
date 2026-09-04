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
<body class="bg-[#F8FAFC] text-slate-800 font-sans antialiased flex min-h-screen">

    <!-- SIDEBAR LEFT (KESISWAAN) -->
    <aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between p-5 hidden md:flex shrink-0">
        <div>
            <!-- Logo SOUL -->
            <div class="flex items-center gap-3 mb-8 px-2">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-500/20">
                    SOUL
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Panel Kesiswaan</span>
                </div>
            </div>

            <!-- Navigation Menu -->
            <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3">Menu Utama</div>
            <nav class="space-y-1">
                <a href="{{ route('kesiswaan.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.dashboard') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </span> 
                    Dashboard
                </a>
                <a href="{{ route('kesiswaan.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.users.*') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </span> 
                    Akun Pengguna
                </a>
                <a href="{{ route('kesiswaan.ekskuls.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.ekskuls.*') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </span> 
                    Data Ekskul
                </a>
                <a href="{{ route('kesiswaan.kelas.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('kesiswaan.kelas.*') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span> 
                    Data Kelas
                </a>
            </nav>
        </div>

        <!-- User Profile Card Bottom (Tanpa Logout) -->
        <div class="bg-slate-50 p-3 rounded-xl flex items-center border border-slate-200/60">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-yellow-400 text-slate-900 font-bold flex items-center justify-center text-xs">
                    {{ substr(auth()->user()->username, 0, 2) }}
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->username }}</h4>
                    <p class="text-[10px] text-slate-400 font-medium">Staf Kesiswaan</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Logout" class="text-gray-400 hover:text-red-500 text-xs font-bold flex items-center gap-1">⟶ Logout</button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT CONTAINER -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- TOP NAVBAR HEADER (DENGAN LOGOUT) -->
        <header class="px-8 py-4 bg-white border-b border-slate-200/80 flex items-center justify-between gap-4">
            <div class="relative w-full max-w-md">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" placeholder="Cari akun, ekskul, kelas..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all">
            </div>

            <div class="flex items-center gap-3">
                <div class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-lg text-xs font-semibold">
                    Kesiswaan
                </div>
                <!-- TOMBOL LOGOUT DI NAVBAR -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-xs font-semibold border border-red-100 transition shadow-sm flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
                <button onclick="document.getElementById('sidebar-mobile').classList.toggle('hidden')" class="md:hidden w-9 h-9 rounded-full bg-slate-50 border border-slate-200/80 flex items-center justify-center text-xs text-slate-500">
                    ☰
                </button>
            </div>
        </header>

        <!-- DASHBOARD BODY CONTENT -->
        <main class="p-8 space-y-6 overflow-y-auto">

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-semibold flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">✕</button>
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-xs font-semibold flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">✕</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>