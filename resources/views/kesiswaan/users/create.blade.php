@extends('layouts.kesiswaan')

@section('title', 'Buat Akun')

@section('content')
    <div>
        <h1 class="text-xl md:text-2xl font-extrabold text-theme-dark">Buat Akun Baru</h1>
        <p class="text-xs text-gray-400 mt-1">Password default otomatis diisi <span class="font-bold text-theme-dark">password</span>. User bisa langsung login dengan email + password.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-xs font-semibold">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kesiswaan.users.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-3xl space-y-6">

        <!-- Data Login -->
        <div>
            <h2 class="text-sm font-extrabold text-theme-dark mb-4">Data Login</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Role <span class="text-rose-500">*</span></label>
                    <select name="role" id="role" required onchange="toggleRoleFields()"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        <option value="" {{ old('role') === null ? 'selected' : '' }} disabled>Pilih role...</option>
                        @php
                            $roles = ['siswa' => 'Siswa', 'pembina' => 'Guru / Pembina', 'kesiswaan' => 'Kesiswaan'];
                            if (auth()->user()->role === 'admin') {
                                $roles = ['admin' => 'Admin'] + $roles;
                            }
                        @endphp
                        @foreach ($roles as $value => $label)
                            <option value="{{ $value }}" {{ old('role') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                {{-- Username hanya untuk Staff / Admin --}}
                <div id="field-username-admin" class="hidden md:col-span-2">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Username <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" id="input-username" value="{{ old('username') }}" placeholder="Masukkan username login staf/admin"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                    <p class="text-[11px] text-gray-400 mt-1">Username ini khusus untuk login akun Kesiswaan atau Admin.</p>
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-2.5 flex items-start gap-1.5">
                <svg class="w-3.5 h-3.5 flex-shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9.663 17h4.674M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.814 12.839a6 6 0 01-.789-1.736 5.045 5.045 0 019.696 0 6 6 0 01-.789 1.736m-8.118 0h8.118"/>
                </svg>
                <span>Siswa & Ketua sama-sama role "siswa" — bedanya hanya jabatan. Pilih jabatan "Ketua" agar diarahkan ke dashboard ketua.</span>
            </p>
        </div>

        <!-- Data Siswa -->
        <div id="fields-siswa" class="hidden space-y-4">
            <h2 class="text-sm font-extrabold text-theme-dark">Data Siswa</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis') }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                    <p class="text-[10px] text-gray-400 mt-1">Harus tepat 10 angka.</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama') }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Foto Profil</label>
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full px-3 py-1.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-sky-500 file:text-white">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Kelas</label>
                    <select name="kelas_id"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        <option value="">Pilih kelas...</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }} ({{ config("kelas.tingkat.{$k->tingkat}") }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Angkatan</label>
                    <input type="text" name="angkatan" value="{{ old('angkatan') }}" placeholder="Contoh: 2024 atau 2024/2025"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Jenis Kelamin</label>
                    <select name="jenis_kelamin"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        <option value="" {{ !old('jenis_kelamin') ? 'selected' : '' }} disabled>Pilih...</option>
                        <option value="laki-laki" {{ old('jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Agama</label>
                    <select name="agama"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        <option value="">Pilih Agama...</option>
                        @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                            <option value="{{ $agm }}" {{ old('agama') === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Kota kelahiran"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">No. Telp / WA</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp') }}" placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Media Sosial</label>
                    <input type="text" name="medsos" value="{{ old('medsos') }}" placeholder="@username / link medsos"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Jabatan</label>
                    <select name="jabatan" id="jabatan"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                        @foreach (['siswa' => 'Siswa', 'ketua' => 'Ketua Ekskul'] as $value => $label)
                            <option value="{{ $value }}" {{ old('jabatan', 'siswa') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="field-ekskul" class="hidden">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Ekskul</label>
                    <select name="ekskul_id"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                        <option value="">Pilih ekskul...</option>
                        @foreach ($ekskuls as $ek)
                            <option value="{{ $ek['id'] }}" {{ old('ekskul_id') == $ek['id'] ? 'selected' : '' }}
                                {{ $ek['has_ketua'] ? 'disabled' : '' }}>
                                {{ $ek['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        Ekskul yang sudah ada ketua tidak bisa dipilih.
                    </p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Foto Profil</label>
                    <input type="file" name="foto" accept="image/*"
                           class="w-full px-3 py-1.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-sky-500 file:text-white hover:file:bg-sky-600 transition">
                </div>
                <div class="md:col-span-3">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Alamat</label>
                    <textarea name="alamat" rows="2" placeholder="Alamat domisili lengkap"
                              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">{{ old('alamat') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Data Pembina -->
        <div id="fields-pembina" class="hidden space-y-4">
            <h2 class="text-sm font-extrabold text-theme-dark">Data Pembina</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip') }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                    <p class="text-[10px] text-gray-400 mt-1">Harus tepat 18 angka.</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Nama Lengkap</label>
                    <input type="text" name="pembina_nama" value="{{ old('pembina_nama') }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Foto Profil</label>
                    <input type="file" name="pembina_foto" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full px-3 py-1.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-sky-500 file:text-white">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Jenis Kelamin</label>
                    <select name="pembina_jenis_kelamin"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        <option value="" {{ !old('pembina_jenis_kelamin') ? 'selected' : '' }} disabled>Pilih...</option>
                        <option value="laki-laki" {{ old('pembina_jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('pembina_jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Agama</label>
                    <select name="pembina_agama"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        <option value="">Pilih Agama...</option>
                        @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                            <option value="{{ $agm }}" {{ old('pembina_agama') === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Tempat Lahir</label>
                    <input type="text" name="pembina_tempat_lahir" value="{{ old('pembina_tempat_lahir') }}" placeholder="Kota kelahiran"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="pembina_tanggal_lahir" value="{{ old('pembina_tanggal_lahir') }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">No. Telp / WA</label>
                    <input type="text" name="pembina_no_telp" value="{{ old('pembina_no_telp') }}" placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Media Sosial</label>
                    <input type="text" name="pembina_medsos" value="{{ old('pembina_medsos') }}" placeholder="@username / link medsos"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Foto Profil</label>
                    <input type="file" name="pembina_foto" accept="image/*"
                           class="w-full px-3 py-1.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-sky-500 file:text-white hover:file:bg-sky-600 transition">
                </div>
                <div class="md:col-span-3">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Alamat</label>
                    <textarea name="pembina_alamat" rows="2" placeholder="Alamat domisili lengkap"
                              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">{{ old('pembina_alamat') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                Simpan Akun
            </button>
            <a href="{{ route('kesiswaan.users.index') }}"
               class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">Batal</a>
        </div>
    </form>

    <script>
        function toggleRoleFields() {
            const role = document.getElementById('role').value;
            const isStaff = (role === 'admin' || role === 'kesiswaan');

            document.getElementById('fields-siswa').classList.toggle('hidden', role !== 'siswa');
            document.getElementById('fields-pembina').classList.toggle('hidden', role !== 'pembina');

            const usernameAdmin = document.getElementById('field-username-admin');
            const inputUsername = document.getElementById('input-username');

            if (usernameAdmin && inputUsername) {
                usernameAdmin.classList.toggle('hidden', !isStaff);
                inputUsername.required = isStaff;
            }

            toggleEkskulField();
        }
        function toggleEkskulField() {
            const jabatan = document.getElementById('jabatan')?.value;
            document.getElementById('field-ekskul').classList.toggle('hidden', jabatan !== 'ketua');
        }
        document.addEventListener('DOMContentLoaded', () => {
            toggleRoleFields();
            document.getElementById('jabatan')?.addEventListener('change', toggleEkskulField);
        });
    </script>
@endsection
