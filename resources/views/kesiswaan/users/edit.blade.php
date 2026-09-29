@extends('layouts.kesiswaan')

@section('title', 'Edit Akun')

@section('content')
<div class="space-y-5 animate-fade-up max-w-3xl">
    <div>
        <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">Edit Akun: {{ $user->username }}</h1>
        <p class="text-xs text-slate-400 mt-1">Ubah data login, ganti role, atau kelola status akun ini.</p>
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

    <form action="{{ route('kesiswaan.users.update', $user) }}" method="POST" enctype="multipart/form-data"
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
            </div>
        </div>

        <!-- Data Siswa -->
        <div id="fields-siswa" class="hidden space-y-4">
            <h2 class="text-sm font-extrabold text-slate-900">Data Siswa</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis', $user->siswa?->nis) }}"
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
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Jabatan</label>
                    <select name="jabatan" id="jabatan"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        @foreach (['siswa' => 'Siswa', 'ketua' => 'Ketua Ekskul'] as $value => $label)
                            <option value="{{ $value }}" {{ old('jabatan', $user->siswa?->jabatan ?? 'siswa') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @php
                    $currentEkskulId = $user->siswa?->pendaftarans->first()?->ekskul_id;
                @endphp
                <div id="field-ekskul" class="hidden">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Ekskul</label>
                    <select name="ekskul_id"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="">Pilih ekskul...</option>
                        @foreach ($ekskuls as $ek)
                            <option value="{{ $ek['id'] }}" {{ old('ekskul_id', $currentEkskulId) == $ek['id'] ? 'selected' : '' }}
                                {{ $ek['has_ketua'] ? 'disabled' : '' }}>
                                {{ $ek['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        Ekskul yang sudah ada ketua tidak bisa dipilih.
                    </p>
                </div>
                <div class="md:col-span-3 flex items-center gap-4 p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80">
                    <img src="{{ $user->siswa?->foto_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->siswa?->nama ?? 'Siswa').'&background=0284c7&color=fff' }}"
                         alt="Foto Profil Siswa"
                         class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-sm flex-shrink-0">
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
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip', $user->pembina?->nip) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                    <p class="text-[10px] text-gray-400 mt-1">Harus tepat 18 angka.</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Nama Lengkap</label>
                    <input type="text" name="pembina_nama" value="{{ old('pembina_nama', $user->pembina?->nama) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Jenis Kelamin</label>
                    <select name="pembina_jenis_kelamin"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="" {{ !old('pembina_jenis_kelamin', $user->pembina?->jenis_kelamin) ? 'selected' : '' }} disabled>Pilih...</option>
                        <option value="laki-laki" {{ old('pembina_jenis_kelamin', $user->pembina?->jenis_kelamin) === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('pembina_jenis_kelamin', $user->pembina?->jenis_kelamin) === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Agama</label>
                    <select name="pembina_agama"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                        <option value="">Pilih Agama...</option>
                        @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                            <option value="{{ $agm }}" {{ old('pembina_agama', $user->pembina?->agama) === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Tempat Lahir</label>
                    <input type="text" name="pembina_tempat_lahir" value="{{ old('pembina_tempat_lahir', $user->pembina?->tempat_lahir) }}" placeholder="Kota kelahiran"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="pembina_tanggal_lahir" value="{{ old('pembina_tanggal_lahir', $user->pembina?->tanggal_lahir?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">No. Telp / WA</label>
                    <input type="text" name="pembina_no_telp" value="{{ old('pembina_no_telp', $user->pembina?->no_telp) }}" placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Media Sosial</label>
                    <input type="text" name="pembina_medsos" value="{{ old('pembina_medsos', $user->pembina?->medsos) }}" placeholder="@username / link medsos"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                </div>
                <div class="md:col-span-3 flex items-center gap-4 p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80">
                    <img src="{{ $user->pembina?->foto_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->pembina?->nama ?? 'Pembina').'&background=0284c7&color=fff' }}"
                         alt="Foto Profil Pembina"
                         class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-sm flex-shrink-0">
                    <div class="flex-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Ganti Foto Profil Pembina</label>
                        <input type="file" name="pembina_foto" accept="image/*"
                               class="w-full px-3 py-1.5 bg-white border border-slate-200/80 rounded-xl text-xs file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-sky-500 file:text-white hover:file:bg-sky-600 transition">
                        <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Alamat</label>
                    <textarea name="pembina_alamat" rows="2" placeholder="Alamat domisili lengkap"
                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">{{ old('pembina_alamat', $user->pembina?->alamat) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS + DELETE OPTION --}}
        <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100 flex-wrap">
            <div class="flex items-center gap-2">
                <button type="submit" class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('kesiswaan.users.index') }}"
                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">Batal</a>
            </div>

            @if ($user->id !== auth()->id())
                <button type="button" onclick="confirmDeleteUser()"
                        class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 font-bold text-xs rounded-2xl transition flex items-center gap-1.5"
                        title="Hapus akun ini secara permanen">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Akun
                </button>
            @endif
        </div>
    </form>

    @if ($user->id !== auth()->id())
        <form id="delete-user-form" action="{{ route('kesiswaan.users.destroy', $user) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>

<script>
    function toggleRoleFields() {
        const role = document.getElementById('role').value;
        document.getElementById('fields-siswa').classList.toggle('hidden', role !== 'siswa');
        document.getElementById('fields-pembina').classList.toggle('hidden', role !== 'pembina');
        toggleEkskulField();
    }
    function toggleEkskulField() {
        const jabatan = document.getElementById('jabatan')?.value;
        document.getElementById('field-ekskul').classList.toggle('hidden', jabatan !== 'ketua');
    }
    function confirmDeleteUser() {
        if (confirm('Yakin ingin menghapus akun {{ $user->username }}? Seluruh data yang terkait akan terhapus dan tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('delete-user-form').submit();
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        toggleRoleFields();
        document.getElementById('jabatan')?.addEventListener('change', toggleEkskulField);
    });
</script>
@endsection
