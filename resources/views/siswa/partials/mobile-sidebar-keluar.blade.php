{{-- Mobile sidebar overlay + drawer untuk panel siswa (versi Pengajuan Keluar) --}}
<div id="sidebar-overlay" class="hidden fixed inset-0 z-40 bg-black/50 md:hidden" onclick="closeSidebar()"></div>

<div id="sidebar-mobile" class="hidden fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-sky-100 overflow-hidden flex flex-col justify-between p-5 md:hidden shadow-2xl">
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-24 -right-16 w-64 h-64 rounded-full bg-sky-100/80 blur-3xl animate-blob"></div>
        <div class="absolute -bottom-24 -left-16 w-64 h-64 rounded-full bg-amber-100/80 blur-3xl animate-blob" style="animation-delay: 3s"></div>
    </div>

    <div class="relative h-full flex flex-col overflow-y-auto">
        <div class="flex items-center justify-between mb-8 px-2 mt-1">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-500 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-sky-300">SOUL</div>
                <div>
                    <h1 class="font-extrabold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Ekskul Manager</span>
                </div>
            </div>
            <button onclick="closeSidebar()" class="w-8 h-8 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3">Menu Utama</div>
        <nav class="space-y-1.5">
            <a href="{{ route('siswa.dashboard') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                <span class="flex items-center justify-center w-4 h-4">
                    <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard Icon" class="w-full h-full object-contain">
                </span>
                Dashboard
            </a>
            <a href="{{ route('siswa.katalog') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                <span class="flex items-center justify-center w-4 h-4">
                    <img src="{{ asset('images/catalog.png') }}" alt="Katalog Icon" class="w-full h-full object-contain">
                </span>
                Katalog Ekskul
            </a>
            <a href="{{ route('siswa.presensi') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                <span class="flex items-center justify-center w-4 h-4">
                    <img src="{{ asset('images/presensi.png') }}" alt="Presensi Icon" class="w-full h-full object-contain">
                </span>
                Presensi & Kegiatan
            </a>
            <a href="{{ route('siswa.rekap') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                <span class="flex items-center justify-center w-4 h-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                Rekap Absensi
            </a>
            <a href="{{ route('siswa.pengajuan-keluar') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 bg-gradient-to-r from-sky-100 to-blue-100 text-sky-800 rounded-xl font-semibold text-xs shadow-sm shadow-sky-100 transition-all">
                <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gradient-to-b from-sky-400 to-blue-500"></span>
                <span class="flex items-center justify-center w-4 h-4">
                    <img src="{{ asset('images/pengajuan-keluar.png') }}" alt="Pengajuan Keluar Icon" class="w-full h-full object-contain">
                </span>
                Pengajuan Keluar
            </a>
            <a href="{{ route('profile.edit') }}" class="relative flex items-center gap-3 px-3.5 py-2.5 text-slate-500 hover:bg-sky-50 hover:text-sky-700 rounded-xl font-medium text-xs transition-all">
                <span class="flex items-center justify-center w-4 h-4">
                    <img src="{{ asset('images/profile.png') }}" alt="Profile Icon" class="w-full h-full object-contain">
                </span>
                Profile
            </a>
        </nav>
    </div>

    <div class="relative mt-auto pt-6">
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