@extends('layouts.kesiswaan')
@section('title', 'Detail Akun – ' . ($user->siswa?->nama ?? $user->pembina?->nama ?? $user->username ?? $user->email))

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
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>

    {{-- PROFILE CARD --}}
    <div class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-5">
            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl bg-slate-100 flex items-center justify-center overflow-hidden shrink-0 border-2 border-sky-200">
                @if($user->siswa?->foto_url)
                    <img src="{{ $user->siswa->foto_url }}" alt="{{ $user->siswa->nama }}" class="w-full h-full object-cover">
                @elseif($user->pembina?->foto_url)
                    <img src="{{ $user->pembina->foto_url }}" alt="{{ $user->pembina->nama }}" class="w-full h-full object-cover">
                @else
                    <span class="text-2xl font-black text-sky-600">
                        {{ strtoupper(substr($user->siswa?->nama ?? $user->pembina?->nama ?? $user->username ?? $user->email ?? 'U', 0, 2)) }}
                    </span>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <h2 class="text-xl font-extrabold text-slate-900 truncate">
                        {{ $user->siswa?->nama ?? $user->pembina?->nama ?? $user->username ?? $user->email }}
                    </h2>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold
                        @if($user->role === 'siswa') bg-sky-100 text-sky-700 border border-sky-200
                        @elseif($user->role === 'pembina') bg-emerald-100 text-emerald-700 border border-emerald-200
                        @elseif($user->role === 'kesiswaan') bg-indigo-100 text-indigo-700 border border-indigo-200
                        @else bg-amber-100 text-amber-700 border border-amber-200 @endif">
                        {{ ucfirst($user->role) }}
                    </span>
                    @if($user->siswa?->jabatan === 'ketua')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 whitespace-nowrap">
                            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M2 19l2-9 5 5 3-8 3 8 5-5 2 9H2z"/></svg> Ketua
                        </span>
                    @endif
                    @if($user->siswa?->status)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold whitespace-nowrap
                            @if($user->siswa->status === 'aktif') bg-emerald-100 text-emerald-700 border border-emerald-200
                            @elseif($user->siswa->status === 'menunggu_penempatan') bg-amber-100 text-amber-700 border border-amber-200
                            @else bg-slate-100 text-slate-500 border border-slate-200 @endif">
                            {{ ['aktif' => 'Aktif', 'menunggu_penempatan' => 'Menunggu Penempatan', 'nonaktif' => 'Alumni / Nonaktif'][$user->siswa->status] ?? ucfirst($user->siswa->status) }}
                        </span>
                    @endif
                </div>

                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                    <div>
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Username</dt>
                        <dd class="text-slate-900 font-medium mt-0.5 break-all">{{ filled($user->username) ? $user->username : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email</dt>
                        <dd class="text-slate-900 font-medium mt-0.5 break-all">{{ filled($user->email) ? $user->email : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Role</dt>
                        <dd class="text-slate-900 font-medium mt-0.5 capitalize">{{ $user->role }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Akun</dt>
                        <dd class="text-slate-900 font-medium mt-0.5">
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-bold
                                @if($user->is_active) bg-emerald-50 text-emerald-700 border border-emerald-200
                                @else bg-rose-50 text-rose-600 border border-rose-200 @endif">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </dd>
                    </div>
                </dl>

                {{-- QUICK STATS --}}
                <div class="flex flex-wrap gap-2 mt-4">
                    @if($user->role === 'siswa')
                        <div class="px-3 py-2 rounded-2xl bg-sky-50 border border-sky-100">
                            <div class="text-[10px] font-bold text-sky-500 uppercase tracking-wider">Ekskul Aktif</div>
                            <div class="text-sm font-extrabold text-slate-900">{{ $pendaftaranAktif }}</div>
                        </div>
                        <div class="px-3 py-2 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</div>
                            <div class="text-sm font-extrabold text-slate-900">{{ $user->siswa?->kelas?->nama ?? '—' }}</div>
                        </div>
                        @if($persenHadir !== null)
                            <div class="px-3 py-2 rounded-2xl bg-emerald-50 border border-emerald-100">
                                <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Kehadiran</div>
                                <div class="text-sm font-extrabold text-slate-900">{{ $persenHadir }}% <span class="text-[10px] font-semibold text-slate-400">({{ $hadirPresensi }}/{{ $totalPresensi }})</span></div>
                            </div>
                        @endif
                    @elseif($user->role === 'pembina')
                        <div class="px-3 py-2 rounded-2xl bg-emerald-50 border border-emerald-100">
                            <div class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Ekskul Dibina</div>
                            <div class="text-sm font-extrabold text-slate-900">{{ $pendaftaranAktif }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 shrink-0">
                @if($user->siswa?->user_id)
                    <a href="{{ route('kesiswaan.siswa.riwayat-profil', $user->siswa) }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-xl transition text-xs">
                        Riwayat Profil
                    </a>
                    <a href="{{ route('kesiswaan.siswa.riwayat-kelas', $user->siswa) }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl transition text-xs">
                        Riwayat Kelas
                    </a>
                @endif
                @if($user->role === 'admin' && auth()->user()->role !== 'admin')
                    <span class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-[11px] text-slate-400 italic">
                        Dikelola Admin
                    </span>
                @else
                    <a href="{{ route('kesiswaan.users.edit', $user) }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl transition text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Akun
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 overflow-x-auto">
            <nav class="flex gap-1 px-4 -mb-px min-w-max" aria-label="Tabs">
                <button type="button" data-tab="profil" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-amber-500 text-amber-600 bg-amber-50 whitespace-nowrap">Profil Lengkap</button>
                @if($user->role === 'siswa')
                    <button type="button" data-tab="ekskul" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200 whitespace-nowrap">Keanggotaan Ekskul</button>
                    <button type="button" data-tab="riwayat" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200 whitespace-nowrap">Riwayat Jabatan</button>
                    <button type="button" data-tab="pengajuan" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200 whitespace-nowrap">Pengajuan Keluar</button>
                    <button type="button" data-tab="kelas" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200 whitespace-nowrap">Riwayat Kelas</button>
                @elseif($user->role === 'pembina')
                    <button type="button" data-tab="ekskul" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200 whitespace-nowrap">Ekskul Dibina</button>
                @endif
                <button type="button" data-tab="riwayat-profil" class="tab-btn px-4 py-3 text-sm font-bold rounded-t-2xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:border-slate-200 whitespace-nowrap">Riwayat Profil</button>
            </nav>
        </div>

        <div class="p-6">
            {{-- TAB: PROFIL LENGKAP --}}
            <div id="tab-profil" class="tab-panel space-y-6">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
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
                            <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
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
                        <div class="rounded-2xl border {{ $isKetua ? 'border-sky-300 bg-sky-50/50 ring-2 ring-sky-200' : 'border-slate-100' }} p-5 relative">
                            @if($isKetua)
                                <span class="absolute -top-2 -right-2 px-2 py-0.5 bg-sky-100 text-sky-700 text-[10px] font-bold rounded-full">KETUA EKSKUL INI</span>
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
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ $statusLabels[$pendaftaran?->status] ?? ucfirst($pendaftaran?->status ?? 'Aktif') }}</span>
                                    <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-600 text-[10px] font-bold">Daftar: {{ $pendaftaran?->tanggal_daftar?->format('d M Y') ?? '-' }}</span>
                                    <a href="{{ route('kesiswaan.ekskuls.show', $ek) }}"
                                       class="px-2.5 py-1 rounded-xl bg-sky-50 text-sky-700 border border-sky-100 text-[10px] font-bold hover:bg-sky-100 transition">
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
                        <div class="rounded-2xl border border-slate-100 p-5">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <h4 class="font-extrabold text-slate-900">{{ $ek->nama_ekskul }}</h4>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $ek->kategori ?? '-' }}{{ $ek->pelatih ? ' · Pelatih: ' . $ek->pelatih->nama : '' }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-xl {{ $ek->status ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }} text-[10px] font-bold">{{ $ek->status ? 'Aktif' : 'Nonaktif' }}</span>
                                    <span class="px-2.5 py-1 rounded-xl bg-sky-50 text-sky-700 text-[10px] font-bold border border-sky-100">{{ $ek->pendaftarans()->where('status', 'diterima')->count() }} Anggota</span>
                                    <a href="{{ route('kesiswaan.ekskuls.show', $ek) }}"
                                       class="px-2.5 py-1 rounded-xl bg-sky-100 text-sky-700 text-[10px] font-bold hover:bg-sky-200 transition">
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
                        <div class="bg-white border border-slate-100 rounded-2xl p-4 {{ $r->isActive() ? 'ring-2 ring-amber-200 bg-amber-50/30' : '' }}">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold
                                            @if($r->isActive()) bg-amber-100 text-amber-800 border border-amber-300
                                            @elseif($r->alasan_selesai === 'diganti') bg-sky-100 text-sky-700 border border-sky-200
                                            @elseif($r->alasan_selesai === 'dicopot') bg-rose-100 text-rose-600 border border-rose-200
                                            @elseif($r->alasan_selesai === 'keluar') bg-violet-100 text-violet-700 border border-violet-200
                                            @else bg-slate-100 text-slate-500 border border-slate-200 @endif">
                                            {{ $r->isActive() ? 'Berlangsung' : ucfirst($r->alasan_selesai ?? 'selesai') }}
                                        </span>
                                        @if($r->isActive())
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" title="Periode aktif"></span>
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
                        <svg class="w-12 h-12 mx-auto text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
                        <div class="bg-white border border-slate-100 rounded-2xl p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-bold text-slate-900">Pengajuan Keluar Ekskul</h5>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $p->ekskul->nama_ekskul ?? '-' }} · {{ $p->tanggal_pengajuan ? \Carbon\Carbon::parse($p->tanggal_pengajuan)->isoFormat('DD MMM Y') : '-' }}</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold
                                    @if($p->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($p->status === 'diterima') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @else bg-rose-50 text-rose-600 border border-rose-200 @endif">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-600 mt-2">{{ $p->alasan }}</p>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
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
                    <a href="{{ route('kesiswaan.siswa.riwayat-kelas', $user->siswa) }}" class="inline-block text-xs font-bold text-sky-600 hover:text-sky-700">
                        Lihat riwayat kelas lengkap &rarr;
                    </a>
                @else
                    <div class="text-center py-8 text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <p class="mt-2 font-medium">Belum ada riwayat perpindahan atau kelulusan kelas.</p>
                    </div>
                @endif
            </div>
            @endif

            {{-- TAB: RIWAYAT PROFIL --}}
            <div id="tab-riwayat-profil" class="tab-panel hidden space-y-4">
                @if($histories->isNotEmpty())
                    @foreach($histories as $riwayat)
                        <div class="bg-white border border-slate-100 rounded-2xl p-4">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $riwayat->field }}</p>
                                <span class="text-[10px] text-slate-500">{{ $riwayat->created_at->isoFormat('DD MMM YYYY HH:mm') }}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="p-3 bg-rose-50 rounded-xl border border-rose-100">
                                    <dt class="text-[10px] font-bold text-rose-600 uppercase tracking-wider">Sebelum</dt>
                                    <dd class="text-slate-700 font-medium mt-1 break-all">{{ filled($riwayat->old_value) ? $riwayat->old_value : '—' }}</dd>
                                </div>
                                <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                                    <dt class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Sesudah</dt>
                                    <dd class="text-slate-700 font-medium mt-1 break-all">{{ filled($riwayat->new_value) ? $riwayat->new_value : '—' }}</dd>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/></svg>
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
                b.classList.remove('border-amber-500', 'text-amber-600', 'bg-amber-50');
                b.classList.add('border-transparent', 'text-slate-400');
            });
            btn.classList.add('border-amber-500', 'text-amber-600', 'bg-amber-50');
            btn.classList.remove('border-transparent', 'text-slate-400');

            panels.forEach(p => p.classList.toggle('hidden', p.id !== 'tab-' + target));
        });
    });
});
</script>
@endsection
