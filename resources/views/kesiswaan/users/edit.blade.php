@extends('layouts.kesiswaan')

@section('title', 'Edit Akun')

@section('content')
    <div>
        <h1 class="text-2xl font-extrabold text-theme-dark">Edit Akun: {{ $user->username }}</h1>
        <p class="text-xs text-gray-400 mt-1">Ubah data login atau ganti role akun ini.</p>
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

    <form action="{{ route('kesiswaan.users.update', $user) }}" method="POST"
          class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        <!-- Data Login -->
        <div>
            <h2 class="text-sm font-extrabold text-theme-dark mb-4">Data Login</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Role</label>
                    <select name="role" id="role" required onchange="toggleRoleFields()"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        @php
                            $roles = ['siswa' => 'Siswa', 'pembina' => 'Pembina', 'kesiswaan' => 'Kesiswaan'];
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
        <div id="fields-siswa" class="hidden">
            <h2 class="text-sm font-extrabold text-theme-dark mb-4">Data Siswa</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis', $user->siswa?->nis) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->siswa?->nama) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Kelas</label>
                    <select name="kelas_id"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        <option value="">Pilih kelas...</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id', $user->siswa?->kelas_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }} ({{ strtoupper($k->tingkat) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Jenis Kelamin</label>
                    <select name="jenis_kelamin"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        <option value="" {{ !old('jenis_kelamin', $user->siswa?->jenis_kelamin) ? 'selected' : '' }} disabled>Pilih...</option>
                        <option value="laki-laki" {{ old('jenis_kelamin', $user->siswa?->jenis_kelamin) === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('jenis_kelamin', $user->siswa?->jenis_kelamin) === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Jabatan</label>
                    <select name="jabatan"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        @foreach (['siswa' => 'Siswa', 'anggota' => 'Anggota Ekskul', 'ketua' => 'Ketua Ekskul'] as $value => $label)
                            <option value="{{ $value }}" {{ old('jabatan', $user->siswa?->jabatan ?? 'siswa') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Pembina -->
        <div id="fields-pembina" class="hidden">
            <h2 class="text-sm font-extrabold text-theme-dark mb-4">Data Pembina</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip', $user->pembina?->nip) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Nama Lengkap</label>
                    <input type="text" name="pembina_nama" value="{{ old('pembina_nama', $user->pembina?->nama) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                </div>
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Jenis Kelamin</label>
                    <select name="pembina_jenis_kelamin"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                        <option value="" {{ !old('pembina_jenis_kelamin', $user->pembina?->jenis_kelamin) ? 'selected' : '' }} disabled>Pilih...</option>
                        <option value="laki-laki" {{ old('pembina_jenis_kelamin', $user->pembina?->jenis_kelamin) === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('pembina_jenis_kelamin', $user->pembina?->jenis_kelamin) === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-2 items-center flex-wrap">
            <button type="submit" class="px-6 py-3 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full shadow-lg shadow-blue-500/20 transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('kesiswaan.users.index') }}"
               class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-full transition">Batal</a>
        </div>
    </form>

    <script>
        function toggleRoleFields() {
            const role = document.getElementById('role').value;
            document.getElementById('fields-siswa').classList.toggle('hidden', role !== 'siswa');
            document.getElementById('fields-pembina').classList.toggle('hidden', role !== 'pembina');
        }
        document.addEventListener('DOMContentLoaded', toggleRoleFields);
    </script>
@endsection
