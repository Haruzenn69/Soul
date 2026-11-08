@extends('layouts.kesiswaan')
@section('title', 'Info Akun – ' . ($user->siswa?->nama ?? $user->pembina?->nama ?? $user->username ?? $user->email))

@section('content')
@php
    $backUrl = request()->headers->get('referer');
    if (! $backUrl || parse_url($backUrl, PHP_URL_HOST) !== request()->getHost()) {
        $backUrl = route('kesiswaan.users.index');
    }

    $histories = $user->siswa?->profileHistories ?? $user->pembina?->profileHistories ?? collect();

    $statusLabels = [
        'diterima'   => 'Aktif',
        'pending'    => 'Pending',
        'ditolak'    => 'Ditolak',
        'nonaktif'   => 'Nonaktif',
        'peringatan' => 'Peringatan',
        'keluar'     => 'Keluar',
    ];
    $totalPresensi = $presensi ? (int) $presensi->sum() : 0;
    $hadirPresensi = $presensi ? (int) ($presensi['hadir'] ?? 0) : 0;
    $persenHadir = $totalPresensi > 0 ? round(($hadirPresensi / $totalPresensi) * 100) : null;
@endphp

<div class="space-y-5 animate-fade-up">
    {{-- BACK BUTTON --}}
    <a href="{{ $backUrl }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition">
        <i class="fa-solid fa-arrow-left text-xs"></i>
        Kembali
    </a>

    {{-- PROFILE CARD BIRU --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        {{-- Ambient blur circles --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative space-y-6">
            {{-- BARIS ATAS: AVATAR, IDENTITAS UTAMA, DAN TOMBOL AKSI --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div class="flex items-center gap-4 sm:gap-5 min-w-0">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center overflow-hidden shrink-0 border-2 border-white/30 shadow-md">
                        @if($user->siswa?->foto_url)
                            <img src="{{ $user->siswa->foto_url }}" alt="{{ $user->siswa->nama }}" class="w-full h-full object-cover">
                        @elseif($user->pembina?->foto_url)
                            <img src="{{ $user->pembina->foto_url }}" alt="{{ $user->pembina->nama }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl font-black text-white">
                                {{ strtoupper(substr($user->siswa?->nama ?? $user->pembina?->nama ?? $user->username ?? $user->email ?? 'U', 0, 2)) }}
                            </span>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <h2 class="text-xl sm:text-2xl font-extrabold text-white truncate leading-tight">
                            {{ $user->siswa?->nama ?? $user->pembina?->nama ?? $user->username ?? $user->email }}
                        </h2>

                        {{-- Identitas Ringkas Tanpa Pil --}}
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-white/85 mt-1.5 font-medium">
                            <span class="capitalize">{{ $user->role === 'siswa' ? 'Siswa' : ($user->role === 'pembina' ? 'Guru Pembina' : ucfirst($user->role)) }}</span>
                            @if($user->role === 'siswa' && $user->siswa?->kelas)
                                <span>•</span>
                                <span>{{ $user->siswa->kelas->nama }}</span>
                            @endif
                            @if($user->siswa?->jabatan === 'ketua')
                                <span>•</span>
                                <span class="text-amber-200 font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-crown text-[10px]"></i>
                                    Ketua Ekskul
                                </span>
                            @endif
                            @if($user->siswa?->status === 'nonaktif')
                                <span>•</span>
                                <span class="text-white/70 italic">Alumni / Nonaktif</span>
                            @endif
                            <span>•</span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-300' : 'bg-white/40' }}"></span>
                                <span>{{ $user->is_active ? 'Akun Aktif' : 'Nonaktif' }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    @if($user->role === 'admin' && auth()->user()->role !== 'admin')
                        <span class="text-xs text-white/70 italic">
                            Dikelola Admin
                        </span>
                    @else
                        {{-- Reset Password --}}
                        <form method="POST" action="{{ route('kesiswaan.users.reset-password', $user) }}"
                              onsubmit="return confirm('Yakin ingin mereset password akun {{ $user->siswa?->nama ?? $user->pembina?->nama ?? $user->username ?? $user->email }} ke kata sandi default: password?')">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/15 hover:bg-white/25 backdrop-blur border border-white/30 text-white font-bold rounded-xl transition text-xs shadow-sm cursor-pointer">
                                <i class="fa-solid fa-key text-xs"></i>
                                Reset Password
                            </button>
                        </form>

                        {{-- Edit Akun --}}
                        <a href="{{ route('kesiswaan.users.edit', $user) }}"
                           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-sky-700 hover:bg-sky-50 font-bold rounded-xl shadow-lg shadow-sky-900/10 hover:-translate-y-0.5 transition text-xs">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                            Edit Akun
                        </a>
                    @endif
                </div>
            </div>

            {{-- BARIS BAWAH: DATA & STATISTIK MINIMALIS TANPA PIL --}}
            <div class="pt-5 border-t border-white/20 grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
                @if($user->role === 'siswa')
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">NIS</p>
                        <p class="text-sm font-extrabold text-white mt-0.5 break-all">{{ $user->siswa?->nis ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">Email Akun</p>
                        <p class="text-sm font-extrabold text-white mt-0.5 break-all truncate" title="{{ $user->email }}">{{ $user->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">Ekskul Aktif</p>
                        <p class="text-sm font-extrabold text-white mt-0.5">{{ $pendaftaranAktif }} Terdaftar</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">Kehadiran</p>
                        <p class="text-sm font-extrabold text-white mt-0.5">
                            @if($persenHadir !== null)
                                {{ $persenHadir }}% <span class="text-xs font-normal text-white/70">({{ $hadirPresensi }}/{{ $totalPresensi }})</span>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                @elseif($user->role === 'pembina')
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">NIP</p>
                        <p class="text-sm font-extrabold text-white mt-0.5 break-all">{{ $user->pembina?->nip ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">Email Akun</p>
                        <p class="text-sm font-extrabold text-white mt-0.5 break-all truncate" title="{{ $user->email }}">{{ $user->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">Ekskul Dibina</p>
                        <p class="text-sm font-extrabold text-white mt-0.5">{{ $pendaftaranAktif }} Ekskul</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">No. Telepon</p>
                        <p class="text-sm font-extrabold text-white mt-0.5">{{ $user->pembina?->no_telp ?? '—' }}</p>
                    </div>
                @else
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">Username</p>
                        <p class="text-sm font-extrabold text-white mt-0.5 break-all">{{ $user->username ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">Email</p>
                        <p class="text-sm font-extrabold text-white mt-0.5 break-all truncate" title="{{ $user->email }}">{{ $user->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">Role</p>
                        <p class="text-sm font-extrabold text-white mt-0.5 capitalize">{{ $user->role }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-white/75 uppercase tracking-wider">Terdaftar</p>
                        <p class="text-sm font-extrabold text-white mt-0.5">{{ $user->created_at?->isoFormat('D MMM Y') ?? '—' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 overflow-x-auto">
            <nav class="flex gap-1 px-4 -mb-px min-w-max" aria-label="Tabs">
                <button type="button" data-tab="profil" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-sky-600 text-sky-700 whitespace-nowrap">Profil Lengkap</button>
                @if($user->role === 'siswa')
                    <button type="button" data-tab="ekskul" class="tab-btn px-4 py-3 text-sm font-semibold rounded-t-2xl border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 whitespace-nowrap">Keanggotaan Ekskul</button>
                    <button type="button" data-tab="riwayat" class="tab-btn px-4 py-3 text-sm font-semibold rounded-t-2xl border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 whitespace-nowrap">Riwayat Jabatan</button>
                    <button type="button" data-tab="pengajuan" class="tab-btn px-4 py-3 text-sm font-semibold rounded-t-2xl border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 whitespace-nowrap">Pengajuan Keluar</button>
                    <button type="button" data-tab="kelas" class="tab-btn px-4 py-3 text-sm font-semibold rounded-t-2xl border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 whitespace-nowrap">Riwayat Kelas</button>
                    <button type="button" data-tab="nilai" class="tab-btn px-4 py-3 text-sm font-semibold rounded-t-2xl border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 whitespace-nowrap">Nilai</button>
                @elseif($user->role === 'pembina')
                    <button type="button" data-tab="ekskul" class="tab-btn px-4 py-3 text-sm font-semibold rounded-t-2xl border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 whitespace-nowrap">Ekskul Dibina</button>
                @endif
                <button type="button" data-tab="riwayat-profil" class="tab-btn px-4 py-3 text-sm font-semibold rounded-t-2xl border-b-2 border-transparent text-slate-500 hover:text-slate-800 hover:border-slate-300 whitespace-nowrap">Riwayat Profil</button>
            </nav>
        </div>

        <div class="p-6">
            {{-- TAB: PROFIL LENGKAP --}}
            <div id="tab-profil" class="tab-panel space-y-6">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-sky-500"></i>
                    Identitas
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    @if($user->role === 'siswa' && $user->siswa)
                        @php $s = $user->siswa; @endphp
                        @foreach([
                            'NIS' => $s->nis,
                            'Nama Lengkap' => $s->nama,
                            'Tempat Lahir' => $s->tempat_lahir,
                            'Tanggal Lahir' => $s->tanggal_lahir?->isoFormat('DD MMMM YYYY'),
                            'Jenis Kelamin' => $s->jenis_kelamin ? ucfirst($s->jenis_kelamin) : null,
                            'Agama' => $s->agama,
                            'Alamat' => $s->alamat,
                            'No. Telepon' => $s->no_telp,
                            'Email' => $s->email,
                            'MedSos' => $s->medsos,
                            'Jabatan' => ucfirst($s->jabatan ?? 'siswa'),
                            'Angkatan' => $s->angkatan,
                        ] as $label => $value)
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</dt>
                                <dd class="text-slate-900 font-medium mt-0.5 break-all">{{ filled($value) ? $value : '—' }}</dd>
                            </div>
                        @endforeach

                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 mt-6 pt-4 border-t border-slate-100 sm:col-span-2">
                            <i class="fa-solid fa-graduation-cap text-sky-500"></i>
                            Data Akademik
                        </h3>
                        @php
                            $akademik = [
                                'Kelas' => $s->kelas?->nama,
                                'Tingkat' => $s->kelas?->tingkat ? strtoupper($s->kelas->tingkat) : null,
                                'Jurusan' => $s->kelas?->jurusan_label ?? $s->kelas?->jurusan,
                                'Tahun Ajaran' => $s->kelas?->tahunAjaran?->nama,
                            ];
                        @endphp
                        @foreach($akademik as $label => $value)
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</dt>
                                <dd class="text-slate-900 font-medium mt-0.5">{{ filled($value) ? $value : '—' }}</dd>
                            </div>
                        @endforeach
                    @elseif($user->role === 'pembina' && $user->pembina)
                        @php $p = $user->pembina; @endphp
                        @foreach([
                            'NIP' => $p->nip,
                            'Nama Lengkap' => $p->nama,
                            'Tempat Lahir' => $p->tempat_lahir,
                            'Tanggal Lahir' => $p->tanggal_lahir?->isoFormat('DD MMMM YYYY'),
                            'Jenis Kelamin' => $p->jenis_kelamin ? ucfirst($p->jenis_kelamin) : null,
                            'Agama' => $p->agama,
                            'No. Telepon' => $p->no_telp,
                            'Email' => $p->email,
                            'MedSos' => $p->medsos,
                            'Alamat' => $p->alamat,
                        ] as $label => $value)
                            <div>
                                <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</dt>
                                <dd class="text-slate-900 font-medium mt-0.5 break-all">{{ filled($value) ? $value : '—' }}</dd>
                            </div>
                        @endforeach
                    @else
                        <p class="text-slate-400 text-sm">Data profil belum lengkap.</p>
                    @endif
                </dl>
            </div>

            {{-- TAB: KEANGGOTAAN EKSKUL (SISWA) --}}
            @if($user->role === 'siswa')
            <div id="tab-ekskul" class="tab-panel hidden space-y-6">
                @if($ekskuls && $ekskuls->isNotEmpty())
                    @foreach($ekskuls as $ek)
                        @php
                            $pendaftaran = $user->siswa?->pendaftarans->firstWhere('ekskul_id', $ek->id);
                            $isKetua = ($user->siswa?->jabatan === 'ketua') && $pendaftaran;
                        @endphp
                        <div class="rounded-2xl border {{ $isKetua ? 'border-sky-300 bg-sky-50/30' : 'border-slate-100' }} p-5 relative">
                            @if($isKetua)
                                <span class="absolute -top-2 -right-2 px-2 py-0.5 bg-slate-800 text-white text-[10px] font-bold rounded-md">Ketua Ekskul</span>
                            @endif
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-2xl overflow-hidden bg-slate-100 shrink-0 flex items-center justify-center font-black text-sky-600 uppercase text-xs">
                                        @if($ek->logo)
                                            <img src="{{ asset('storage/' . $ek->logo) }}" alt="{{ $ek->nama_ekskul }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($ek->nama_ekskul, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-slate-900">{{ $ek->nama_ekskul }}</h4>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $ek->kategori ?? '-' }}{{ $ek->pembina ? ' · Pembina: ' . $ek->pembina->nama : '' }}{{ $ek->pelatih ? ' · Pelatih: ' . $ek->pelatih->nama : '' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">{{ $statusLabels[$pendaftaran?->status] ?? ucfirst($pendaftaran?->status ?? 'Aktif') }}</span>
                                    <span class="text-xs text-slate-400 font-medium">Daftar: {{ $pendaftaran?->tanggal_daftar?->format('d M Y') ?? '-' }}</span>
                                    <a href="{{ route('kesiswaan.ekskuls.show', $ek) }}"
                                       class="px-3 py-1 rounded-lg bg-white border border-slate-200 hover:border-sky-300 hover:bg-sky-50 text-slate-600 hover:text-sky-700 text-xs font-medium transition">
                                        Lihat Ekskul &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-10 text-slate-400">
                        <p class="text-xs font-semibold">Belum bergabung ekskul manapun.</p>
                    </div>
                @endif
            </div>
            @endif

            {{-- TAB: EKSKUL DIBINA (PEMBINA) --}}
            @if($user->role === 'pembina')
            <div id="tab-ekskul" class="tab-panel hidden space-y-6">
                @if($ekskuls && $ekskuls->isNotEmpty())
                    @foreach($ekskuls as $ek)
                        <div class="rounded-2xl border border-slate-200/80 p-5 bg-white">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <h4 class="font-extrabold text-slate-900">{{ $ek->nama_ekskul }}</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $ek->kategori ?? '-' }}{{ $ek->pelatih ? ' · Pelatih: ' . $ek->pelatih->nama : '' }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">{{ $ek->status ? 'Aktif' : 'Nonaktif' }}</span>
                                    <span class="text-xs text-slate-400 font-medium">{{ $ek->pendaftarans()->where('status', 'diterima')->count() }} Anggota</span>
                                    <a href="{{ route('kesiswaan.ekskuls.show', $ek) }}"
                                       class="px-3 py-1 rounded-lg bg-white border border-slate-200 hover:border-sky-300 hover:bg-sky-50 text-slate-600 hover:text-sky-700 text-xs font-medium transition">
                                        Lihat Ekskul &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-10 text-slate-400">
                        <p class="text-xs font-semibold">Belum membina ekskul manapun.</p>
                    </div>
                @endif
            </div>
            @endif

            {{-- TAB: RIWAYAT JABATAN --}}
            @if($user->role === 'siswa' && $user->siswa)
            <div id="tab-riwayat" class="tab-panel hidden space-y-4">
                @php $riwayat = $user->siswa->riwayatJabatans()->with('ekskul')->latest('mulai')->latest('id')->get(); @endphp
                @if($riwayat->isNotEmpty())
                    @foreach($riwayat as $r)
                        <div class="bg-white border border-slate-200/80 rounded-2xl p-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $r->isActive() ? 'Berlangsung' : ucfirst($r->alasan_selesai ?? 'selesai') }}
                                        </span>
                                        @if($r->isActive())
                                            <span class="w-2 h-2 rounded-full bg-emerald-500" title="Periode aktif"></span>
                                        @endif
                                    </div>
                                    <p class="font-bold text-slate-900">Ketua Ekskul {{ $r->ekskul->nama_ekskul ?? '-' }}</p>
                                    <p class="text-sm text-slate-500 mt-0.5">{{ $r->periodeText() }} · {{ $r->durasiHari() }} hari</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-slate-400">
                        <i class="fa-solid fa-crown text-3xl mb-1 text-slate-300"></i>
                        <p class="mt-2 font-medium">Belum pernah menjabat Ketua.</p>
                    </div>
                @endif
            </div>
            @endif

            {{-- TAB: PENGAJUAN KELUAR --}}
            @if($user->role === 'siswa' && $user->siswa)
            <div id="tab-pengajuan" class="tab-panel hidden space-y-4">
                @php $pengajuan = $user->siswa->pengajuanKeluars()->with('ekskul')->latest()->get(); @endphp
                @if($pengajuan->isNotEmpty())
                    @foreach($pengajuan as $p)
                        <div class="bg-white border border-slate-200/80 rounded-2xl p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-bold text-slate-900">Pengajuan Keluar Ekskul</h5>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $p->ekskul->nama_ekskul ?? '-' }} · {{ $p->tanggal_pengajuan ? \Carbon\Carbon::parse($p->tanggal_pengajuan)->isoFormat('DD MMM Y') : '-' }}</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-600 mt-2">{{ $p->alasan }}</p>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-slate-400">
                        <i class="fa-solid fa-arrow-right-from-bracket text-3xl mb-1 text-slate-300"></i>
                        <p class="mt-2 font-medium">Belum ada pengajuan keluar.</p>
                    </div>
                @endif
            </div>
            @endif

            {{-- TAB: RIWAYAT KELAS --}}
            @if($user->role === 'siswa' && $user->siswa)
            <div id="tab-kelas" class="tab-panel hidden space-y-4">
                @php $riwayatKelas = $user->siswa->classHistories; @endphp
                @if($riwayatKelas->isNotEmpty())
                    <div class="overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="w-full min-w-[560px] text-left text-xs">
                            <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 font-bold">Tahun Ajaran</th>
                                    <th class="px-4 py-3 font-bold">Kelas Asal</th>
                                    <th class="px-4 py-3 font-bold">Kelas Tujuan</th>
                                    <th class="px-4 py-3 font-bold">Keterangan</th>
                                    <th class="px-4 py-3 font-bold">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($riwayatKelas as $history)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-slate-700">{{ $history->tahun_ajaran }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $history->kelas_asal }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $history->kelas_tujuan ?? 'Lulus / Alumni' }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ ['kenaikan' => 'Naik kelas otomatis', 'penempatan' => 'Penempatan oleh kesiswaan', 'kelulusan' => 'Lulus dan diarsipkan'][$history->jenis] ?? $history->jenis }}</td>
                                        <td class="px-4 py-3 text-slate-500">{{ $history->diproses_pada?->format('d-m-Y H:i') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <i class="fa-solid fa-chalkboard text-3xl mb-1 text-slate-300"></i>
                        <p class="mt-2 font-medium">Belum ada riwayat perpindahan atau kelulusan kelas.</p>
                    </div>
                @endif
            </div>
            @endif

            {{-- TAB: NILAI --}}
            @if($user->role === 'siswa')
            <div id="tab-nilai" class="tab-panel hidden space-y-4">
                @if($penilaians->isNotEmpty())
                    <div class="overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="w-full min-w-[620px] text-left text-xs">
                            <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3 font-bold">Periode</th>
                                    <th class="px-4 py-3 font-bold">Ekskul</th>
                                    <th class="px-4 py-3 text-center font-bold">Kehadiran</th>
                                    <th class="px-4 py-3 text-center font-bold">Nilai Akhir</th>
                                    <th class="px-4 py-3 text-center font-bold">Predikat</th>
                                    <th class="px-4 py-3 font-bold">Pembina</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($penilaians as $penilaian)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-4 py-3 font-semibold text-slate-700">{{ $penilaian->periode }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $penilaian->pendaftaran?->ekskul?->nama_ekskul ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center text-slate-600">{{ number_format((float) $penilaian->persentase_kehadiran, 1) }}%</td>
                                        <td class="px-4 py-3 text-center font-extrabold text-slate-900">{{ number_format((float) $penilaian->nilai_akhir, 2) }}</td>
                                        <td class="px-4 py-3 text-center"><span class="inline-flex items-center justify-center w-7 h-7 rounded-lg border font-extrabold {{ \App\Models\Penilaian::warnaPredikat((string) $penilaian->predikat) }}">{{ $penilaian->predikat ?? '-' }}</span></td>
                                        <td class="px-4 py-3 text-slate-600">{{ $penilaian->penilai?->nama ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-8 text-center text-slate-400">
                        <p class="font-medium">Belum ada nilai ekskul yang diterbitkan.</p>
                    </div>
                @endif
            </div>
            @endif

            {{-- TAB: RIWAYAT PROFIL --}}
            <div id="tab-riwayat-profil" class="tab-panel hidden space-y-4">
                @if($histories->isNotEmpty())
                    @foreach($histories as $riwayat)
                        <div class="bg-white border border-slate-200/80 rounded-2xl p-4">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $riwayat->field }}</p>
                                <span class="text-[10px] text-slate-500">{{ $riwayat->created_at->isoFormat('DD MMM YYYY HH:mm') }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70">
                                    <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sebelum</dt>
                                    <dd class="text-slate-700 font-medium mt-1 break-all">{{ filled($riwayat->old_value) ? $riwayat->old_value : '—' }}</dd>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70">
                                    <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sesudah</dt>
                                    <dd class="text-slate-700 font-medium mt-1 break-all">{{ filled($riwayat->new_value) ? $riwayat->new_value : '—' }}</dd>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-slate-400">
                        <i class="fa-solid fa-clock-rotate-left text-3xl mb-1 text-slate-300"></i>
                        <p class="mt-2 font-medium">Belum ada riwayat perubahan profil.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.tab-panel');

    tabs.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.dataset.tab;
            tabs.forEach(b => {
                b.classList.remove('border-sky-600', 'text-sky-700', 'font-bold');
                b.classList.add('border-transparent', 'text-slate-500', 'font-semibold');
            });
            btn.classList.add('border-sky-600', 'text-sky-700', 'font-bold');
            btn.classList.remove('border-transparent', 'text-slate-500', 'font-semibold');

            panels.forEach(p => p.classList.toggle('hidden', p.id !== 'tab-' + target));
        });
    });
});
</script>
@endsection
