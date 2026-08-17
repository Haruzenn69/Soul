<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ketua Ekskul') - Soul</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="w-64 bg-gray-900 text-white flex flex-col fixed h-full">
            <div class="p-4 border-b border-gray-700">
                <h1 class="text-xl font-bold">SOUL</h1>
                <p class="text-xs text-gray-400">Ketua Ekskul</p>
            </div>
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('ketua.dashboard') }}"
                   class="block px-3 py-2 rounded {{ request()->routeIs('ketua.dashboard') ? 'bg-gray-700' : 'hover:bg-gray-800' }}">
                    Dashboard
                </a>
                <a href="{{ route('ketua.kegiatan.index') }}"
                   class="block px-3 py-2 rounded {{ request()->routeIs('ketua.kegiatan.*') ? 'bg-gray-700' : 'hover:bg-gray-800' }}">
                    Kegiatan
                </a>
                <a href="{{ route('ketua.pendaftaran.index') }}"
                   class="block px-3 py-2 rounded {{ request()->routeIs('ketua.pendaftaran.*') ? 'bg-gray-700' : 'hover:bg-gray-800' }}">
                    Pendaftaran
                </a>
                <a href="{{ route('ketua.pengajuan-keluar.index') }}"
                   class="block px-3 py-2 rounded {{ request()->routeIs('ketua.pengajuan-keluar.*') ? 'bg-gray-700' : 'hover:bg-gray-800' }}">
                    Pengajuan Keluar
                </a>
                <a href="{{ route('ketua.laporan-bulanan.index') }}"
                   class="block px-3 py-2 rounded {{ request()->routeIs('ketua.laporan-bulanan.*') ? 'bg-gray-700' : 'hover:bg-gray-800' }}">
                    Laporan Bulanan
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <p class="text-xs text-gray-400">{{ auth()->user()->username }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="mt-2 w-full text-left text-sm text-red-400 hover:text-red-300">Logout</button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 ml-64 p-6">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
