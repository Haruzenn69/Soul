<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pembina - SOUL</title>
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

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between p-5 hidden md:flex shrink-0">
        <div>
            <div class="flex items-center gap-3 mb-8 px-2">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-500/20">
                    SOUL
                </div>
                <div>
                    <h1 class="font-bold text-sm tracking-tight text-slate-900 leading-none">SOUL</h1>
                    <span class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Panel Pembina</span>
                </div>
            </div>

            <div class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-2 px-3">Menu</div>
            <nav class="space-y-1">
                <a href="{{ route('pembina.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 bg-blue-50 text-blue-600 rounded-xl font-semibold text-xs transition-colors">
                    Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    Data Anggota
                </a>
                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    Pendaftaran
                </a>
                <a href="#" class="flex items-center gap-3 px-3.5 py-2.5 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-xl font-medium text-xs transition-colors">
                    Cetak Laporan
                </a>
            </nav>
        </div>

        <div class="bg-slate-50 p-3 rounded-xl flex items-center justify-between border border-slate-200/60">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs">
                    {{ strtoupper(substr($pembina->nama ?? 'P', 0, 2)) }}
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ $pembina->nama ?? 'Pembina' }}</h4>
                    <p class="text-[10px] text-slate-400 font-medium">Pembina</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-red-500 text-xs font-bold transition-colors">Keluar</button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <header class="px-8 py-4 bg-white border-b border-slate-200/80 flex items-center justify-between gap-4">
            <div class="relative w-full max-w-md">
                <input type="text" placeholder="Cari data siswa, presensi, laporan..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200/80 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">🔍</span>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <h4 class="text-xs font-bold text-slate-800 leading-tight">{{ $pembina->nama ?? auth()->user()->username }}</h4>
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
            
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Selamat datang, {{ $pembina->nama ?? 'Pembina' }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                </div>
            </div>

            <!-- STATS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Eskul Dibina</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $ekskul ? 1 : 0 }}</h3>
                        <p class="text-[11px] font-medium text-blue-600 mt-1">{{ $ekskul->nama_ekskul ?? 'Belum ada ekskul' }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-slate-600 text-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Anggota Aktif</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ count($anggota ?? []) }}</h3>
                        <p class="text-[11px] font-medium text-emerald-600 mt-1">Siswa terdaftar</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-slate-600 text-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Pendaftaran Baru</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ count($pendaftaranPending ?? []) }}</h3>
                        <p class="text-[11px] font-medium text-amber-600 mt-1">Menunggu verifikasi</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-slate-600 text-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- DAFTAR ANGGOTA -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Daftar Anggota Ekskul</h2>
                    <span class="text-xs font-medium text-slate-400">Total {{ count($anggota ?? []) }} anggota</span>
                </div>

                @if(isset($anggota) && count($anggota) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="text-left p-3 font-semibold text-slate-500 rounded-l-xl">No</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">NIS</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">Nama</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">Kelas</th>
                                    <th class="text-left p-3 font-semibold text-slate-500">Jabatan</th>
                                    <th class="text-left p-3 font-semibold text-slate-500 rounded-r-xl">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($anggota as $key => $item)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="p-3">{{ $key + 1 }}</td>
                                    <td class="p-3 font-medium">{{ $item->siswa->nis ?? '-' }}</td>
                                    <td class="p-3 font-medium">{{ $item->siswa->nama ?? '-' }}</td>
                                    <td class="p-3">{{ $item->siswa->kelas->nama ?? '-' }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-semibold 
                                            {{ $item->siswa->jabatan == 'ketua' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ ucfirst($item->siswa->jabatan ?? 'anggota') }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">
                                            Aktif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <p class="text-sm">Belum ada anggota di ekskul ini.</p>
                    </div>
                @endif
            </div>

            <!-- PENDAFTARAN PENDING -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Pendaftaran Siswa</h2>
                    <span class="text-[10px] font-medium text-amber-600 bg-amber-50 px-3 py-1 rounded-full">Verifikasi Manual</span>
                </div>

                @if(isset($pendaftaranPending) && count($pendaftaranPending) > 0)
                    @foreach($pendaftaranPending as $item)
                    <div class="p-4 bg-slate-50 rounded-xl flex items-center justify-between mb-3 border border-slate-200/60">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs uppercase">
                                {{ substr($item->siswa->nama ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">{{ $item->siswa->nama ?? '-' }}</h4>
                                <p class="text-[10px] text-slate-400">
                                    {{ $item->siswa->kelas->nama ?? '-' }} · 
                                    Mendaftar {{ \Carbon\Carbon::parse($item->tanggal_daftar)->isoFormat('DD MMM Y') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-[10px] font-semibold rounded-full">
                                Pending
                            </span>
                        </div>
                    </div>
                    @endforeach
                    <div class="mt-4 p-3 bg-blue-50 rounded-xl border border-blue-200">
                        <p class="text-xs text-blue-700">Verifikasi pendaftaran dilakukan secara manual melalui proses offline.</p>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <p class="text-sm">Tidak ada pendaftaran baru.</p>
                    </div>
                @endif
            </div>

            <!-- CETAK LAPORAN -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Cetak Laporan</h2>
                    <span class="text-[10px] font-medium text-slate-400">{{ $ekskul->nama_ekskul ?? 'Ekskul' }}</span>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <a href="#" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                        Rekap Anggota
                    </a>
                    <a href="#" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                        Laporan Kegiatan
                    </a>
                    <a href="#" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                        Presensi
                    </a>
                </div>
            </div>

        </main>
    </div>

</body>
</html>