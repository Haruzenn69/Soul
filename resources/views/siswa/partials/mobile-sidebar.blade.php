{{-- Mobile sidebar overlay + drawer untuk panel siswa --}}
<div id="sidebar-overlay" class="hidden fixed inset-0 z-40 bg-black/50 md:hidden" onclick="closeSidebar()"></div>

<div id="sidebar-mobile" class="hidden fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-sky-100 overflow-hidden flex flex-col md:hidden shadow-2xl">

    {{-- Dekorasi blob — pointer-events-none supaya tidak menutup tombol --}}
    <div class="absolute inset-0 pointer-events-none z-0">
        <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-sky-100/80 blur-3xl animate-blob pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-16 w-64 h-64 rounded-full bg-amber-100/80 blur-3xl animate-blob pointer-events-none" style="animation-delay: 3s"></div>
    </div>

    {{-- Wrapper isi sidebar --}}
    <div class="relative z-10 flex-1 flex flex-col p-5 overflow-y-auto">

        {{-- Header dengan tombol X — beri z-20 supaya paling atas --}}
        <div class="relative z-20 flex items-center justify-between mb-8 px-2 mt-1 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-sky-300">
                    SOUL
                </div>
                <div>
                    <h1 class="font-extrabold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                </div>
            </div>
            <button type="button" onclick="closeSidebar()" class="relative z-20 w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors cursor-pointer">
                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3 shrink-0">Menu Utama</div>
        <nav class="space-y-1.5">
            <a href="{{ route('siswa.dashboard') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('siswa.dashboard') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                @if(request()->routeIs('siswa.dashboard'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                @endif
                <span class="text-base flex items-center justify-center w-4 h-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </span>
                Dashboard
            </a>

            <a href="{{ route('siswa.katalog') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('siswa.katalog') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                @if(request()->routeIs('siswa.katalog'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                @endif
                <span class="text-base flex items-center justify-center w-4 h-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </span>
                Katalog Ekskul
            </a>

            <a href="{{ route('siswa.presensi') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 {{ request()->routeIs('siswa.presensi') ? 'bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold shadow-sm shadow-sky-100' : 'text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium' }} text-xs transition-all">
                @if(request()->routeIs('siswa.presensi'))
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                @endif
                <span class="text-base flex items-center justify-center w-4 h-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
                Presensi & Kegiatan
            </a>

            <a href="{{ route('profile.edit') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                <span class="text-base flex items-center justify-center w-4 h-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                Profile
            </a>
        </nav>

        <div class="mt-auto pt-6 shrink-0">
            <div class="bg-gradient-to-r from-sky-50 to-amber-50 p-3 rounded-2xl flex items-center justify-between border border-sky-100 shadow-sm">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-300 to-yellow-400 text-amber-900 font-extrabold flex items-center justify-center text-xs shadow-md shadow-amber-200">
                        {{ strtoupper(substr($siswa->nama ?? auth()->user()->username ?? 'S', 0, 1)) }}
                    </div>
                    <div class="text-left">
                        <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ $siswa->nama ?? auth()->user()->username }}</h4>
                        <p class="text-[10px] text-slate-400 font-medium">{{ $siswa->kelas->nama ?? 'Siswa' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 hover:text-red-500 text-xs font-bold transition-colors">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes blob { 0%, 100% { transform: translate(0, 0) scale(1); } 33% { transform: translate(24px, -18px) scale(1.08); } 66% { transform: translate(-16px, 12px) scale(.94); } }
    .animate-blob { animation: blob 10s ease-in-out infinite; }
</style>

<script>
    function openSidebar() {
        document.getElementById('sidebar-mobile').classList.remove('hidden');
        document.getElementById('sidebar-overlay').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }
    function closeSidebar() {
        document.getElementById('sidebar-mobile').classList.add('hidden');
        document.getElementById('sidebar-overlay').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
</script>