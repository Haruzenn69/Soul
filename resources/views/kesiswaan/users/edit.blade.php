@extends('layouts.kesiswaan')

@section('title', 'Edit Akun')

@section('content')
<div class="space-y-5 animate-fade-up">

    {{-- HERO CARD BIRU (STYLE SAMA DENGAN HALAMAN LAIN) --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white soul-timbul">
        {{-- Ambient blur circles --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                    Edit Akun: {{ $user->username ?: $user->email }}
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Ubah data login, ganti role, atau kelola status akun ini melalui panel kesiswaan.
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.users.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-2xl hover:bg-white/20 transition-all">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Kembali ke Daftar Akun
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-semibold">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kesiswaan.users.update', $user) }}" method="POST" enctype="multipart/form-data" id="form-edit-user" novalidate
          class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <!-- Data Login -->
        <div>
            <h2 class="text-sm font-extrabold text-slate-900 mb-4">Data Login</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                        Username
                        @if(!$user->username)
                            <span class="text-amber-500 font-normal text-[10px] lowercase">(belum diset oleh user)</span>
                        @endif
                    </label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" placeholder="Belum diatur (diisi saat login pertama)"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Role</label>
                    <select name="role" id="role" required onchange="toggleRoleFields()"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        @php
                            $roles = ['siswa' => 'Siswa', 'pembina' => 'Guru / Pembina', 'kesiswaan' => 'Kesiswaan'];
                            if (auth()->user()->role === 'admin') {
                                $roles = ['admin' => 'Admin'] + $roles;
                            }
                        @endphp
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Status Akun</label>
                    <select name="is_active" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="1" @selected((string) old('is_active', (int) $user->is_active) === '1')>Aktif</option>
                        <option value="0" @selected((string) old('is_active', (int) $user->is_active) === '0')>Nonaktif</option>
                    </select>
                    <p class="mt-1 text-[10px] text-slate-400">Akun nonaktif tidak dapat digunakan untuk login.</p>
                </div>
            </div>
        </div>

        <!-- Data Siswa -->
        <div id="fields-siswa" class="hidden space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Data Siswa</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">NIS</label>
                    <input type="text" inputmode="numeric" maxlength="10" name="nis" value="{{ old('nis', $user->siswa?->nis) }}"
                           oninput="this.value = this.value.replace(/\D/g, '').slice(0, 10)"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                    <p class="text-[10px] text-gray-400 mt-1">Harus tepat 10 angka.</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->siswa?->nama) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Kelas</label>
                    <select name="kelas_id"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="">Pilih kelas...</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id', $user->siswa?->kelas_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }} ({{ config("kelas.tingkat.{$k->tingkat}") }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Angkatan</label>
                    <input type="text" name="angkatan" value="{{ old('angkatan', $user->siswa?->angkatan) }}" placeholder="Contoh: 2024 atau 2024/2025"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Jenis Kelamin</label>
                    <select name="jenis_kelamin"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="" {{ !old('jenis_kelamin', $user->siswa?->jenis_kelamin) ? 'selected' : '' }} disabled>Pilih...</option>
                        <option value="laki-laki" {{ old('jenis_kelamin', $user->siswa?->jenis_kelamin) === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('jenis_kelamin', $user->siswa?->jenis_kelamin) === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Agama</label>
                    <select name="agama"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="">Pilih Agama...</option>
                        @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                            <option value="{{ $agm }}" {{ old('agama', $user->siswa?->agama) === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $user->siswa?->tempat_lahir) }}" placeholder="Kota kelahiran"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->siswa?->tanggal_lahir?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">No. Telp / WA</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp', $user->siswa?->no_telp) }}" placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Media Sosial</label>
                    <input type="text" name="medsos" value="{{ old('medsos', $user->siswa?->medsos) }}" placeholder="@username / link medsos"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div class="md:col-span-3 flex items-center gap-4 p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80">
                    <img src="{{ $user->siswa?->foto_url ?? asset('images/default.png') }}"
                         alt="Foto Profil Siswa"
                         class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-sm flex-shrink-0"
                         onerror="this.onerror=null;this.src='{{ asset('images/default.png') }}';">
                    <div class="flex-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Ganti Foto Profil Siswa</label>
                        <input type="file" name="foto" accept="image/*"
                               class="w-full px-3 py-1.5 bg-white border border-slate-200/80 rounded-xl text-xs file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-sky-500 file:text-white hover:file:bg-sky-600 transition">
                        <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Alamat</label>
                    <textarea name="alamat" rows="2" placeholder="Alamat domisili lengkap"
                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">{{ old('alamat', $user->siswa?->alamat) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Data Pembina -->
        <div id="fields-pembina" class="hidden space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Data Pembina</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">NIP *</label>
                    <input required type="text" inputmode="numeric" maxlength="18" name="nip" value="{{ old('nip', $user->pembina?->nip) }}"
                           oninput="this.value = this.value.replace(/\D/g, '').slice(0, 18)"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                    <p class="text-[10px] text-gray-400 mt-1">Harus tepat 18 angka.</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Nama Lengkap *</label>
                    <input required type="text" name="pembina_nama" value="{{ old('pembina_nama', $user->pembina?->nama) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Jenis Kelamin *</label>
                    <select required name="pembina_jenis_kelamin"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="" {{ !old('pembina_jenis_kelamin', $user->pembina?->jenis_kelamin) ? 'selected' : '' }} disabled>Pilih...</option>
                        <option value="laki-laki" {{ old('pembina_jenis_kelamin', $user->pembina?->jenis_kelamin) === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('pembina_jenis_kelamin', $user->pembina?->jenis_kelamin) === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Agama *</label>
                    <select required name="pembina_agama"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="">Pilih Agama...</option>
                        @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                            <option value="{{ $agm }}" {{ old('pembina_agama', $user->pembina?->agama) === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Tempat Lahir *</label>
                    <input required type="text" name="pembina_tempat_lahir" value="{{ old('pembina_tempat_lahir', $user->pembina?->tempat_lahir) }}" placeholder="Kota kelahiran"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Tanggal Lahir *</label>
                    <input required type="date" name="pembina_tanggal_lahir" value="{{ old('pembina_tanggal_lahir', $user->pembina?->tanggal_lahir?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">No. Telp / WA *</label>
                    <input required type="text" name="pembina_no_telp" value="{{ old('pembina_no_telp', $user->pembina?->no_telp) }}" placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Media Sosial (opsional)</label>
                    <input type="text" name="pembina_medsos" value="{{ old('pembina_medsos', $user->pembina?->medsos) }}" placeholder="@username / link medsos"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div class="md:col-span-3 flex items-center gap-4 p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80">
                    <img src="{{ $user->pembina?->foto_url ?? asset('images/default.png') }}"
                         alt="Foto Profil Pembina"
                         class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-sm flex-shrink-0"
                         onerror="this.onerror=null;this.src='{{ asset('images/default.png') }}';">
                    <div class="flex-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Ganti Foto Profil Pembina (opsional)</label>
                        <input type="file" name="pembina_foto" accept="image/*"
                               class="w-full px-3 py-1.5 bg-white border border-slate-200/80 rounded-xl text-xs file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-sky-500 file:text-white hover:file:bg-sky-600 transition">
                        <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Alamat *</label>
                    <textarea required name="pembina_alamat" rows="2" placeholder="Alamat domisili lengkap"
                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">{{ old('pembina_alamat', $user->pembina?->alamat) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS + DEACTIVATE OPTION --}}
        <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100 flex-wrap">
            <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('kesiswaan.users.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                    Batal
                </a>
            </div>

            @if ($user->id !== auth()->id())
                @if ($user->is_active)
                    <button type="button" onclick="toggleAccountActive(0)"
                            class="px-4 py-2.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/80 font-bold text-xs rounded-2xl transition flex items-center gap-1.5 shadow-sm"
                            title="Nonaktifkan akun ini">
                        <i class="fa-solid fa-user-slash text-xs text-amber-600"></i>
                        Nonaktifkan Akun Ini
                    </button>
                @else
                    <button type="button" onclick="toggleAccountActive(1)"
                            class="px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 font-bold text-xs rounded-2xl transition flex items-center gap-1.5 shadow-sm"
                            title="Aktifkan kembali akun ini">
                        <i class="fa-solid fa-user-check text-xs text-emerald-600"></i>
                        Aktifkan Akun Ini
                    </button>
                @endif
            @endif
        </div>
    </form>

    {{-- MODAL KONFIRMASI NONAKTIFKAN AKUN DENGAN BINGUNG.PNG --}}
    <dialog id="modalKonfirmasiNonaktif"
            class="rounded-3xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm p-0 w-full max-w-sm shadow-2xl border border-sky-100 m-auto"
            onclick="if (event.target === this) closeConfirmNonaktifModal()">
        <div class="bg-white rounded-3xl p-6 text-center space-y-4">
            <div class="flex justify-center">
                <img src="{{ asset('images/bingung.png') }}" alt="Konfirmasi" class="w-24 h-24 object-contain">
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900 leading-snug">
                    Yakin ingin menonaktifkan akun ini?
                </h3>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Akun <strong>{{ $user->username ?: $user->email }}</strong> tidak akan bisa login ke dalam sistem dan akan ditampilkan dengan warna abu-abu di posisi paling bawah.
                </p>
            </div>
            <div class="flex items-center justify-center gap-2.5 pt-2">
                <button type="button" onclick="closeConfirmNonaktifModal()"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="button" onclick="confirmDeactivateAccount()"
                        class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs transition shadow-md shadow-sky-500/20 inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-user-slash text-xs"></i>
                    Ya, Nonaktifkan
                </button>
            </div>
        </div>
    </dialog>

    {{-- MODAL PERINGATAN (UNIVERSAL WARNING MODAL) --}}
    <dialog id="modalPeringatan"
            class="rounded-3xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm p-0 w-full max-w-md shadow-2xl border border-sky-100 m-auto"
            onclick="if (event.target === this) closeEditWarning()">
        <div class="bg-white rounded-3xl p-6 overflow-hidden">
            <div class="flex justify-center">
                <img src="{{ asset('images/bingung.png') }}" alt="Peringatan" class="w-24 h-24 object-contain">
            </div>

            <div class="text-center mt-2">
                <h3 class="text-sm font-extrabold text-slate-900 leading-snug" id="modalPeringatanTitle">Peringatan Validasi</h3>
                <p class="text-[11px] text-slate-400 font-medium">Periksa kembali data yang dimasukkan</p>
            </div>

            {{-- Body --}}
            <div class="mt-4 space-y-3 text-left">
                <p class="text-xs text-slate-600 leading-relaxed font-medium" id="modalPeringatanMessage"></p>
                <div id="modalPeringatanList" class="space-y-1.5 max-h-56 overflow-y-auto pr-1 hidden"></div>
            </div>

            {{-- Footer Buttons --}}
            <div class="flex items-center justify-end gap-2.5 pt-4 mt-4 border-t border-slate-100">
                <button type="button" onclick="closeEditWarning()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Tutup
                </button>
                <button type="button" onclick="confirmEditWarningAction()"
                        class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs transition shadow-md shadow-sky-500/20 inline-flex items-center gap-1.5">
                    <span id="modalPeringatanActionLabel">Periksa Sekarang</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>
    </dialog>
</div>

<script>
    let currentFocusTarget = null;
    function showEditWarning(title, message, items = [], focusTarget = null, actionLabel = 'Periksa Sekarang') {
        document.getElementById('modalPeringatanTitle').textContent = title || 'Peringatan Validasi';
        document.getElementById('modalPeringatanMessage').textContent = message || '';
        document.getElementById('modalPeringatanActionLabel').textContent = actionLabel;
        currentFocusTarget = focusTarget;

        const listEl = document.getElementById('modalPeringatanList');
        if (items && items.length > 0) {
            listEl.innerHTML = '';
            items.forEach((item, idx) => {
                const row = document.createElement('div');
                row.className = 'flex items-start gap-2.5 rounded-xl bg-sky-50/80 border border-sky-200/60 px-3 py-2 text-xs font-semibold text-sky-900';
                row.innerHTML = `<span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-800 text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5">${idx + 1}</span><span class="flex-1 leading-snug">${item}</span>`;
                listEl.appendChild(row);
            });
            listEl.classList.remove('hidden');
        } else {
            listEl.classList.add('hidden');
        }

        const dlg = document.getElementById('modalPeringatan');
        if (dlg) {
            if (typeof dlg.showModal === 'function') dlg.showModal();
            else dlg.setAttribute('open', '');
        }
    }

    function closeEditWarning() {
        const dlg = document.getElementById('modalPeringatan');
        if (dlg) {
            if (typeof dlg.close === 'function') dlg.close();
            else dlg.removeAttribute('open');
        }
    }

    function confirmEditWarningAction() {
        closeEditWarning();
        if (currentFocusTarget) {
            setTimeout(() => {
                const el = typeof currentFocusTarget === 'string' ? document.querySelector(currentFocusTarget) : currentFocusTarget;
                if (el) {
                    el.focus();
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 50);
        }
    }
    function toggleRoleFields() {
        const role = document.getElementById('role').value;
        const siswaFields = document.getElementById('fields-siswa');
        const pembinaFields = document.getElementById('fields-pembina');
        siswaFields.classList.toggle('hidden', role !== 'siswa');
        pembinaFields.classList.toggle('hidden', role !== 'pembina');
        siswaFields.querySelectorAll('input, select, textarea').forEach(field => field.disabled = role !== 'siswa');
        pembinaFields.querySelectorAll('input, select, textarea').forEach(field => field.disabled = role !== 'pembina');
    }
    let isDeactivationConfirmed = false;

    function openConfirmNonaktifModal() {
        const dlg = document.getElementById('modalKonfirmasiNonaktif');
        if (dlg) {
            if (typeof dlg.showModal === 'function') dlg.showModal();
            else dlg.setAttribute('open', '');
        }
    }

    function closeConfirmNonaktifModal() {
        const dlg = document.getElementById('modalKonfirmasiNonaktif');
        if (dlg) {
            if (typeof dlg.close === 'function') dlg.close();
            else dlg.removeAttribute('open');
        }
    }

    function confirmDeactivateAccount() {
        isDeactivationConfirmed = true;
        const select = document.querySelector('select[name="is_active"]');
        if (select) select.value = '0';
        closeConfirmNonaktifModal();
        const form = document.getElementById('form-edit-user');
        if (form) form.submit();
    }

    function toggleAccountActive(targetStatus) {
        if (targetStatus === 0) {
            openConfirmNonaktifModal();
        } else {
            const select = document.querySelector('select[name="is_active"]');
            if (select) select.value = '1';
            const form = document.getElementById('form-edit-user');
            if (form) form.submit();
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        toggleRoleFields();
        document.getElementById('form-edit-user')?.addEventListener('submit', function (e) {
            const isActiveSelect = this.querySelector('select[name="is_active"]');
            if (isActiveSelect && isActiveSelect.value === '0' && !isDeactivationConfirmed && {{ $user->is_active ? 'true' : 'false' }}) {
                e.preventDefault();
                openConfirmNonaktifModal();
                return false;
            }
            const emailEl = this.querySelector('input[name="email"]');
            if (emailEl) {
                const val = emailEl.value.trim();
                if (!val) {
                    e.preventDefault();
                    showEditWarning('Email Wajib Diisi', 'Alamat email wajib diisi untuk login akun.', [], emailEl, 'Isi Email');
                    return false;
                }
                if (!val.includes('@')) {
                    e.preventDefault();
                    showEditWarning('Format Email Tidak Valid', 'Alamat email harus menggunakan tanda "@" (contoh: user@sekolah.sch.id atau user@gmail.com).', [], emailEl, 'Perbaiki Email');
                    return false;
                }
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                    e.preventDefault();
                    showEditWarning('Format Email Tidak Lengkap', 'Pastikan alamat email memiliki domain yang valid (contoh: user@gmail.com).', [], emailEl, 'Perbaiki Email');
                    return false;
                }
            }

            const role = document.getElementById('role')?.value;
            if (role === 'siswa') {
                const nisEl = this.querySelector('input[name="nis"]');
                if (nisEl) {
                    const nisVal = nisEl.value.trim();
                    if (!nisVal) {
                        e.preventDefault();
                        showEditWarning('NIS Wajib Diisi', 'NIS siswa wajib diisi tepat 10 angka.', [], nisEl, 'Isi NIS');
                        return false;
                    }
                    if (nisVal.length !== 10) {
                        e.preventDefault();
                        showEditWarning('Format NIS Tidak Sesuai', 'NIS harus terdiri dari tepat 10 digit angka (saat ini terisi ' + nisVal.length + ' angka).', [], nisEl, 'Perbaiki NIS');
                        return false;
                    }
                }
            } else if (role === 'pembina') {
                const nipEl = this.querySelector('input[name="nip"]');
                if (nipEl) {
                    const nipVal = nipEl.value.trim();
                    if (!nipVal) {
                        e.preventDefault();
                        showEditWarning('NIP Wajib Diisi', 'NIP pembina wajib diisi tepat 18 angka.', [], nipEl, 'Isi NIP');
                        return false;
                    }
                    if (nipVal.length !== 18 || !/^\d{18}$/.test(nipVal)) {
                        e.preventDefault();
                        showEditWarning('Format NIP Tidak Sesuai', 'NIP pembina harus terdiri dari tepat 18 digit angka (saat ini terisi ' + nipVal.length + ' angka).', [], nipEl, 'Perbaiki NIP');
                        return false;
                    }
                }
            }
        });

        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function () {
                const file = this.files[0];
                if (file && file.size > 2 * 1024 * 1024) {
                    showEditWarning('Ukuran File Terlalu Besar', 'Ukuran file foto maksimal adalah 2MB. Silakan gunakan file gambar dengan ukuran lebih kecil.', [], this, 'Pilih File Lain');
                    this.value = '';
                }
            });
        });

        @if ($errors->any())
            showEditWarning('Terjadi Kesalahan Validasi', 'Terdapat data yang belum sesuai dengan ketentuan sistem:', [
                @foreach ($errors->all() as $err)
                    '{{ addslashes($err) }}',
                @endforeach
            ], null, 'Periksa Sekarang');
        @endif
    });
</script>
@endsection
