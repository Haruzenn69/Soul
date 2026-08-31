<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SOUL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: { blue: '#2563EB', darkBlue: '#1D4ED8', yellow: '#FACC15', dark: '#0F172A', light: '#F8FAFC' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-theme-light text-theme-dark font-sans antialiased flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-gray-100 flex flex-col justify-between p-6 hidden md:flex shrink-0">
        <div>
            <div class="flex items-center gap-3 mb-10">
                <div class="w-10 h-10 rounded-2xl bg-theme-dark text-theme-yellow flex items-center justify-center font-bold text-lg shadow-md">S</div>
                <div>
                    <h1 class="font-extrabold text-base tracking-wide leading-none">SOUL</h1>
                    <span class="text-[10px] text-gray-400 font-semibold tracking-wider uppercase">Panel Ketua</span>
                </div>
            </div>

            <div class="text-[11px] font-bold text-gray-400 tracking-wider uppercase mb-3 px-3">Menu</div>
            <nav class="space-y-1">
                <a href="{{ route('ketua.dashboard') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('ketua.dashboard') ? 'bg-blue-50 text-theme-blue rounded-2xl font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium' }} text-xs transition">
                    <span class="text-base">📂</span> Dashboard
                </a>
                <a href="{{ route('ketua.kegiatan.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('ketua.kegiatan.*') ? 'bg-blue-50 text-theme-blue rounded-2xl font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium' }} text-xs transition">
                    <span class="text-base">📋</span> Kegiatan
                </a>
                <a href="{{ route('ketua.pendaftaran.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('ketua.pendaftaran.*') ? 'bg-blue-50 text-theme-blue rounded-2xl font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium' }} text-xs transition">
                    <span class="text-base">📝</span> Pendaftaran
                </a>
                <a href="{{ route('ketua.pengajuan-keluar.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('ketua.pengajuan-keluar.*') ? 'bg-blue-50 text-theme-blue rounded-2xl font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium' }} text-xs transition">
                    <span class="text-base">🚪</span> Pengajuan Keluar
                </a>
                <a href="{{ route('ketua.anggota.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('ketua.anggota.*') ? 'bg-blue-50 text-theme-blue rounded-2xl font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium' }} text-xs transition">
                    <span class="text-base">👥</span> Kelola Anggota
                </a>
                <a href="{{ route('ketua.laporan-bulanan.index') }}" class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('ketua.laporan-bulanan.*') ? 'bg-blue-50 text-theme-blue rounded-2xl font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-theme-dark rounded-2xl font-medium' }} text-xs transition">
                    <span class="text-base">📊</span> Laporan Bulanan
                </a>
            </nav>
        </div>

        <div class="bg-gray-50 p-3.5 rounded-2xl flex items-center justify-between border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-theme-blue text-white font-extrabold flex items-center justify-center text-xs shadow-sm">
                    {{ substr(auth()->user()->siswa->nama ?? 'K', 0, 2) }}
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold leading-tight">{{ auth()->user()->siswa->nama ?? '-' }}</h4>
                    <p class="text-[10px] text-gray-400">Ketua Eskul</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-gray-400 hover:text-red-500 text-xs font-bold">✕</button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="px-8 py-5 bg-white border-b border-gray-100 flex items-center justify-between">
            <h1 class="text-lg font-extrabold text-theme-dark">@yield('title')</h1>
            <div class="flex items-center gap-3">
                <div class="bg-gray-900 text-white px-4 py-1.5 rounded-full text-xs font-bold flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-theme-yellow"></span> Ketua ▾
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-4 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-full transition">Logout</button>
                </form>
            </div>
        </header>

        <main class="p-8 space-y-8 overflow-y-auto">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
