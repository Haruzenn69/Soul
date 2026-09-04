<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SOUL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
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
<body class="bg-[#F8FAFC] text-slate-800 font-sans antialiased flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between p-5 hidden md:flex shrink-0">
        <div>
            <div class="flex items-center gap-3 mb-8 px-2">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-500/20">
                    SOUL
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Panel Ketua</span>
                </div>
            </div>

            <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3">Menu</div>
            <nav class="space-y-1">
                <a href="{{ route('ketua.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.dashboard') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
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

                <a href="{{ route('ketua.kegiatan.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.kegiatan.*') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </span> 
                    Kegiatan
                </a>

                <a href="{{ route('ketua.pendaftaran.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.pendaftaran.*') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span> 
                    Pendaftaran
                </a>

                <a href="{{ route('ketua.pengajuan-keluar.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.pengajuan-keluar.*') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </span> 
                    Pengajuan Keluar
                </a>

                <a href="{{ route('ketua.anggota.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.anggota.*') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span> 
                    Kelola Anggota
                </a>

                @php $isKatalog = request()->routeIs('ketua.profil-ekskul.*','ketua.prestasi.*','ketua.testimoni.*','ketua.faq.*'); @endphp
                <div x-data="{ open: false }" @if($isKatalog) x-init="open = true" @endif>
                    <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.profil-ekskul.*','ketua.prestasi.*','ketua.testimoni.*','ketua.faq.*') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
                        <span class="flex items-center gap-3">
                            <span class="text-base flex items-center justify-center w-4 h-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </span> 
                            Kelola Katalog
                        </span>
                        <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="ml-4 mt-1 space-y-0.5 border-l-2 border-slate-200 pl-3">
                        <a href="{{ route('ketua.profil-ekskul.edit') }}" class="flex items-center gap-3 px-3 py-2 {{ request()->routeIs('ketua.profil-ekskul.*') ? 'text-blue-600 font-semibold' : 'text-slate-400 hover:text-slate-900' }} text-xs transition rounded-xl">
                            <span class="text-sm flex items-center justify-center w-4 h-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </span> 
                            Profil Ekskul
                        </a>
                        <a href="{{ route('ketua.prestasi.index') }}" class="flex items-center gap-3 px-3 py-2 {{ request()->routeIs('ketua.prestasi.*') ? 'text-blue-600 font-semibold' : 'text-slate-400 hover:text-slate-900' }} text-xs transition rounded-xl">
                            <span class="text-sm flex items-center justify-center w-4 h-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </span> 
                            Prestasi
                        </a>
                        <a href="{{ route('ketua.testimoni.index') }}" class="flex items-center gap-3 px-3 py-2 {{ request()->routeIs('ketua.testimoni.*') ? 'text-blue-600 font-semibold' : 'text-slate-400 hover:text-slate-900' }} text-xs transition rounded-xl">
                            <span class="text-sm flex items-center justify-center w-4 h-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </span> 
                            Testimoni
                        </a>
                        <a href="{{ route('ketua.faq.index') }}" class="flex items-center gap-3 px-3 py-2 {{ request()->routeIs('ketua.faq.*') ? 'text-blue-600 font-semibold' : 'text-slate-400 hover:text-slate-900' }} text-xs transition rounded-xl">
                            <span class="text-sm flex items-center justify-center w-4 h-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span> 
                            FAQ
                        </a>
                    </div>
                </div>

                <a href="{{ route('ketua.laporan-bulanan.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('ketua.laporan-bulanan.*') ? 'bg-blue-50 text-blue-600 rounded-xl font-semibold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium' }} text-xs transition-colors">
                    <span class="text-base flex items-center justify-center w-4 h-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                        </svg>
                    </span> 
                    Laporan Bulanan
                </a>
            </nav>
        </div>

        <div class="bg-slate-50 p-3 rounded-xl flex items-center justify-between border border-slate-200/60">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs">
                    {{ substr(auth()->user()->siswa->nama ?? 'K', 0, 2) }}
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->siswa->nama ?? 'Ketua' }}</h4>
                    <p class="text-[10px] text-slate-400 font-medium">Ketua</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-500 text-xs font-bold transition-colors">Keluar</button>
            </form>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col min-w-0">
        <header class="px-8 py-4 bg-white border-b border-slate-200/80 flex items-center justify-between">
            <div class="relative w-full max-w-md">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text" placeholder="Cari kegiatan, anggota, pendaftaran..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all">
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('ketua.notifikasi') }}" class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-xs relative text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(($unreadNotifCount ?? 0) > 0)
                        <span class="w-2 h-2 rounded-full bg-amber-400 absolute top-2 right-2 border-2 border-white"></span>
                    @endif
                </a>
                <div class="text-right hidden sm:block">
                    <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->siswa->nama ?? 'Ketua' }}</h4>
                    <p class="text-[10px] text-slate-400 uppercase font-semibold">{{ auth()->user()->role }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-xs font-semibold border border-red-100 transition shadow-sm">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="p-8 space-y-6 overflow-y-auto">
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-semibold flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-xs font-semibold flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">✕</button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>