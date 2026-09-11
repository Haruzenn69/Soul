{{-- Mobile sidebar overlay + drawer untuk panel siswa (versi Pengajuan Keluar) --}}
<div id="sidebar-overlay" class="hidden fixed inset-0 z-40 bg-black/50 md:hidden" onclick="closeSidebar()"></div>

<div id="sidebar-mobile" class="hidden fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between p-5 md:hidden shadow-xl">
    <div class="overflow-y-auto">
        <div class="flex items-center justify-between mb-8 px-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-500/20">
                    SOUL
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                </div>
            </div>
            <button onclick="closeSidebar()" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3">Menu Utama</div>
        <nav class="space-y-1">
            <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                <span class="flex items-center justify-center w-4 h-4">
                    <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard Icon" class="w-full h-full object-contain">
                </span> 
                Dashboard
            </a>
            <a href="{{ route('siswa.katalog') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                <span class="flex items-center justify-center w-4 h-4">
                    <img src="{{ asset('images/catalog.png') }}" alt="Katalog Icon" class="w-full h-full object-contain">
                </span> 
                Katalog Ekskul
            </a>
            <a href="{{ route('siswa.presensi') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                <span class="flex items-center justify-center w-4 h-4">
                    <img src="{{ asset('images/presensi.png') }}" alt="Presensi Icon" class="w-full h-full object-contain">
                </span> 
                Presensi & Kegiatan
            </a>
            <a href="{{ route('siswa.pengajuan-keluar') }}" class="flex items-center gap-3 px-3.5 py-2.5 bg-blue-50 text-blue-600 rounded-xl font-semibold text-xs transition-colors">
                <span class="flex items-center justify-center w-4 h-4">
                    <img src="{{ asset('images/pengajuan-keluar.png') }}" alt="Pengajuan Keluar Icon" class="w-full h-full object-contain">
                </span> 
                Pengajuan Keluar
            </a>
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                <span class="flex items-center justify-center w-4 h-4">
                    <img src="{{ asset('images/profile.png') }}" alt="Profile Icon" class="w-full h-full object-contain">
                </span> 
                Profile
            </a>
        </nav>
    </div>

    <div class="bg-slate-50 p-3 rounded-xl flex items-center justify-between border border-slate-200/60">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-amber-400 text-slate-900 font-bold flex items-center justify-center text-xs">
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
