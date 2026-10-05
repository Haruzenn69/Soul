@extends('layouts.kesiswaan')

@section('title', 'Buat Akun')

@section('content')
@php
    $initialStep = 1;
    if ($errors->has('kelas_id')) {
        $initialStep = 2;
    } elseif ($errors->hasAny(['jenis_kelamin', 'agama', 'tempat_lahir', 'tanggal_lahir'])) {
        $initialStep = 3;
    } elseif ($errors->hasAny(['no_telp', 'medsos', 'alamat'])) {
        $initialStep = 4;
    }

    $currentRole = old('role', old('jenis') === 'pembina' ? 'pembina' : 'siswa');
    $currentJenisKelamin = old('jenis_kelamin', '');
@endphp

<div class="space-y-5 animate-fade-up"
     x-data="{
        role: '{{ $currentRole }}',
        step: {{ $initialStep }},
        totalSteps: 4,
        jenisKelamin: '{{ $currentJenisKelamin }}',
        nama: '{{ old('nama', '') }}',
        nis: '{{ old('nis', '') }}',
        email: '{{ old('email', '') }}',
        noTelp: '{{ old('no_telp', '') }}',
        tanggalLahir: '{{ old('tanggal_lahir', '') }}',
        tempatLahir: '{{ old('tempat_lahir', '') }}',
        agama: '{{ old('agama', '') }}',
        medsos: '{{ old('medsos', '') }}',
        alamat: `{{ old('alamat', '') }}`,
        kelasText: '',
        photoPreview: null,
        pembinaPhotoPreview: null,
        pembinaEmail: '{{ old('email', '') }}',
        pembinaNip: '{{ old('nip', '') }}',
        pembinaNama: '{{ old('pembina_nama', '') }}',
        pembinaJenisKelamin: '{{ old('pembina_jenis_kelamin', '') }}',
        pembinaAgama: '{{ old('pembina_agama', '') }}',
        pembinaNoTelp: '{{ old('pembina_no_telp', '') }}',
        pembinaTempatLahir: '{{ old('pembina_tempat_lahir', '') }}',
        pembinaTanggalLahir: '{{ old('pembina_tanggal_lahir', '') }}',
        pembinaMedsos: '{{ old('pembina_medsos', '') }}',
        pembinaAlamat: `{{ old('pembina_alamat', '') }}`,
        calculatedAge: '',

        init() {
            this.calculateAge();
            this.updateKelasText();
        },

        updateKelasText() {
            const sel = document.getElementById('kelas_id_select');
            if (sel && sel.selectedIndex > 0) {
                this.kelasText = sel.options[sel.selectedIndex].text;
            } else {
                this.kelasText = '';
            }
        },

        calculateAge() {
            if (!this.tanggalLahir) {
                this.calculatedAge = '';
                return;
            }
            const birth = new Date(this.tanggalLahir);
            const today = new Date();
            let age = today.getFullYear() - birth.getFullYear();
            const m = today.getMonth() - birth.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            this.calculatedAge = age > 0 ? age + ' Tahun' : '';
        },

        formatTanggalLahir() {
            if (!this.tanggalLahir) return '';
            const d = new Date(this.tanggalLahir);
            const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        },

        formatPembinaTanggalLahir() {
            if (!this.pembinaTanggalLahir) return '';
            const d = new Date(this.pembinaTanggalLahir);
            const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        },

        validateStep(targetStep) {
            if (this.step === 1 && targetStep > 1) {
                if (!this.email || !this.email.includes('@')) {
                    alert('Mohon masukkan alamat Email yang valid.');
                    document.getElementById('input-siswa-email')?.focus();
                    return false;
                }
                if (!this.nis || this.nis.length !== 10) {
                    alert('NIS harus tepat 10 digit angka.');
                    document.getElementById('input-siswa-nis')?.focus();
                    return false;
                }
                if (!this.nama || this.nama.trim().length < 2) {
                    alert('Mohon masukkan Nama Lengkap siswa.');
                    document.getElementById('input-siswa-nama')?.focus();
                    return false;
                }
            }
            if (this.step === 2 && targetStep > 2) {
                const selKelas = document.getElementById('kelas_id_select');
                if (!selKelas || !selKelas.value) {
                    alert('Mohon pilih Kelas siswa.');
                    selKelas?.focus();
                    return false;
                }
            }
            if (this.step === 3 && targetStep > 3) {
                if (!this.jenisKelamin) {
                    alert('Mohon pilih Jenis Kelamin siswa.');
                    return false;
                }
            }
            return true;
        },

        goToStep(targetStep) {
            if (targetStep > this.step) {
                if (!this.validateStep(targetStep)) return;
            }
            this.step = targetStep;
            window.scrollTo({ top: 80, behavior: 'smooth' });
        },

        nextStep() {
            if (this.step < this.totalSteps) {
                this.goToStep(this.step + 1);
            }
        },

        prevStep() {
            if (this.step > 1) {
                this.goToStep(this.step - 1);
            }
        },

        handlePhotoChange(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran foto terlalu besar. Maksimal 2MB.');
                    event.target.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        removePhoto() {
            this.photoPreview = null;
            const input = document.getElementById('input-foto-siswa');
            if (input) input.value = '';
        },

        handlePembinaPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran foto terlalu besar. Maksimal 2MB.');
                    event.target.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.pembinaPhotoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        removePembinaPhoto() {
            this.pembinaPhotoPreview = null;
            const input = document.getElementById('input-foto-pembina');
            if (input) input.value = '';
        }
     }">

    {{-- HERO CARD BIRU (STYLE SAMA DENGAN INDEX AKUN PENGGUNA) --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        {{-- Ambient blur circles --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                    Buat Akun Baru
                </h1>
                <p class="text-xs text-white/80 mt-1.5 max-w-xl leading-relaxed">
                    Buat akun pengguna baru untuk siswa, guru/pembina, atau staf. Password default: <span class="font-bold text-white">password</span>
                </p>
            </div>

            <div class="flex gap-2.5 shrink-0 items-center flex-wrap">
                <a href="{{ route('kesiswaan.users.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/10 backdrop-blur border border-white/20 text-white font-bold text-xs rounded-2xl hover:bg-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Akun
                </a>
            </div>
        </div>
    </div>

    {{-- VALIDATION ERRORS --}}
    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-semibold">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-semibold" role="status">
            {{ session('error') }}
            @foreach (session('import_errors', []) as $importError)
                <p class="mt-1">{{ $importError }}</p>
            @endforeach
        </div>
@endif

    {{-- FORM --}}
    <form action="{{ route('kesiswaan.users.store') }}" method="POST" enctype="multipart/form-data" id="form-create-user">
        @csrf

        {{-- Hidden Jabatan Default: Siswa --}}
        <input type="hidden" name="jabatan" value="siswa" :disabled="role !== 'siswa'">

        {{-- PILIH ROLE --}}
        <div class="bg-white rounded-3xl p-6 border border-sky-100 shadow-sm space-y-4 mb-5">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Pilih Role</h2>
                <p class="text-xs text-slate-400 mt-0.5">Tentukan jenis akun yang akan dibuat.</p>
            </div>

            @php
                $roles = [
                    'siswa' => ['label' => 'Siswa', 'desc' => 'Akun siswa dengan biodata & kelas'],
                    'pembina' => ['label' => 'Guru / Pembina', 'desc' => 'Akun pembina ekskul dengan NIP'],
                    'kesiswaan' => ['label' => 'Kesiswaan', 'desc' => 'Staf admin kesiswaan'],
                ];
                if (auth()->user()->role === 'admin') {
                    $roles['admin'] = ['label' => 'Administrator', 'desc' => 'Akses penuh manajemen sistem'];
                }
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach ($roles as $rVal => $rData)
                    <label class="relative flex flex-col p-3 rounded-2xl border-2 cursor-pointer transition-all"
                           :class="role === '{{ $rVal }}' ? 'border-sky-500 bg-sky-50/60' : 'border-slate-100 hover:border-slate-200 bg-slate-50/40'">
                        <input type="radio" name="role" value="{{ $rVal }}" x-model="role" class="sr-only">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold" :class="role === '{{ $rVal }}' ? 'text-sky-900' : 'text-slate-800'">
                                {{ $rData['label'] }}
                            </span>
                            <span class="w-4 h-4 rounded-full border flex items-center justify-center transition-colors"
                                  :class="role === '{{ $rVal }}' ? 'border-sky-500 bg-sky-500' : 'border-slate-300'">
                                <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="role === '{{ $rVal }}'"></span>
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5 leading-snug">{{ $rData['desc'] }}</p>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- =============================================================== --}}
        {{-- ROLE: SISWA (TWO-COLUMN: FORM + PREVIEW)                        --}}
        {{-- =============================================================== --}}
        <div x-show="role === 'siswa'" x-cloak>
            <div class="flex flex-col lg:flex-row gap-5 items-stretch">

                {{-- LEFT: FORM COLUMN --}}
                <div class="flex-1 min-w-0 space-y-5">

                    <a href="{{ route('kesiswaan.users.import-form', 'siswa') }}" class="inline-flex items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-xs font-bold text-emerald-800 hover:bg-emerald-100">
                        Import Akun Siswa dengan Excel
                    </a>

                    {{-- STEPPER NAV --}}
                    <div class="bg-white rounded-3xl p-4 md:p-5 border border-sky-100 shadow-sm">
                        <div class="mb-4">
                            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-sky-500 rounded-full transition-all duration-500"
                                     :style="`width: ${(step / totalSteps) * 100}%`"></div>
                            </div>
                            <div class="flex justify-between items-center mt-1.5 text-[11px] text-slate-400">
                                <span>Langkah <b class="text-sky-600" x-text="step"></b> dari <span x-text="totalSteps"></span></span>
                                <span class="font-semibold text-sky-600" x-text="Math.round((step / totalSteps) * 100) + '%'"></span>
                            </div>
                        </div>

                        @php
                            $steps = [
                                1 => 'Akun & Identitas',
                                2 => 'Data Akademik',
                                3 => 'Biodata Diri',
                                4 => 'Kontak & Alamat',
                            ];
                        @endphp
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            @foreach ($steps as $sNum => $sLabel)
                                <button type="button" @click="goToStep({{ $sNum }})"
                                        class="flex items-center gap-2 p-2.5 rounded-xl text-left transition-all border text-xs"
                                        :class="step === {{ $sNum }} ? 'bg-sky-50 border-sky-300 font-bold text-sky-900' : (step > {{ $sNum }} ? 'bg-emerald-50/60 border-emerald-200 text-slate-700' : 'bg-slate-50/70 border-slate-100 text-slate-400')">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 text-[10px] font-bold"
                                         :class="step === {{ $sNum }} ? 'bg-sky-500 text-white' : (step > {{ $sNum }} ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600')">
                                        <template x-if="step > {{ $sNum }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </template>
                                        <template x-if="step <= {{ $sNum }}">
                                            <span>{{ $sNum }}</span>
                                        </template>
                                    </div>
                                    <span class="truncate">{{ $sLabel }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- STEP 1: AKUN & IDENTITAS --}}
                    <div x-show="step === 1" x-transition.opacity class="bg-white rounded-3xl p-6 border border-sky-100 shadow-sm space-y-5">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="text-sm font-extrabold text-slate-900">Akun & Identitas Utama</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Kredensial login dan identitas pokok siswa.</p>
                        </div>

                        {{-- Foto Profil --}}
                        <div class="flex items-center gap-4 p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80">
                            <div class="relative">
                                <div class="w-14 h-14 rounded-2xl border-2 border-white shadow-sm overflow-hidden bg-sky-100 flex items-center justify-center shrink-0">
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" alt="Foto" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!photoPreview">
                                        <span class="text-sm font-black text-sky-600 uppercase" x-text="nama ? nama.trim().substring(0, 2) : 'SW'"></span>
                                    </template>
                                </div>
                                <template x-if="photoPreview">
                                    <button type="button" @click="removePhoto()" title="Hapus Foto"
                                            class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center shadow hover:bg-rose-600 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </template>
                            </div>
                            <div class="flex-1">
                                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Foto Profil <span class="font-normal text-slate-400">(Opsional)</span></label>
                                <input type="file" name="foto" id="input-foto-siswa" accept="image/jpeg,image/png,image/jpg,image/webp"
                                       @change="handlePhotoChange($event)"
                                       class="w-full px-3 py-1.5 bg-white border border-slate-200/80 rounded-xl text-xs file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-sky-500 file:text-white hover:file:bg-sky-600 transition">
                                <p class="text-[10px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                            </div>
                        </div>

                        {{-- Fields: Email, NIS, Nama --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                                    Email <span class="text-rose-500">*</span>
                                    <span class="text-[10px] text-sky-600 font-semibold normal-case float-right">Untuk login</span>
                                </label>
                                <input type="email" name="email" id="input-siswa-email" x-model="email"
                                       :disabled="role !== 'siswa'"
                                       placeholder="siswa@sekolah.sch.id"
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                                    NIS <span class="text-rose-500">*</span>
                                    <span class="text-[10px] font-bold normal-case float-right px-1.5 py-0.5 rounded-full transition-colors"
                                          :class="nis.length === 10 ? 'bg-emerald-100 text-emerald-700' : 'text-slate-400'"
                                          x-text="nis.length + '/10'"></span>
                                </label>
                                <input type="text" name="nis" id="input-siswa-nis" x-model="nis"
                                       @input="nis = $event.target.value.replace(/\D/g, '').slice(0, 10)"
                                       placeholder="10 digit angka"
                                       class="w-full px-4 py-2.5 bg-slate-50 border rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:ring-2 transition"
                                       :class="nis.length === 10 ? 'border-emerald-300 focus:border-emerald-500 focus:ring-emerald-100' : 'border-slate-200/80 focus:border-sky-400 focus:ring-sky-100'">
                                <p class="text-[10px] text-slate-400 mt-1">Harus tepat 10 angka.</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama" id="input-siswa-nama" x-model="nama"
                                       placeholder="Nama lengkap sesuai rapor"
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex justify-end">
                            <button type="button" @click="nextStep()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                                <span>Lanjut</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: DATA AKADEMIK --}}
                    <div x-show="step === 2" x-transition.opacity class="bg-white rounded-3xl p-6 border border-sky-100 shadow-sm space-y-5">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="text-sm font-extrabold text-slate-900">Data Akademik</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Pilih kelas siswa saat ini.</p>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Kelas <span class="text-rose-500">*</span></label>
                            <select name="kelas_id" id="kelas_id_select" @change="updateKelasText()"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                <option value="">Pilih kelas...</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama }} ({{ config("kelas.tingkat.{$k->tingkat}") }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <button type="button" @click="prevStep()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                Kembali
                            </button>
                            <button type="button" @click="nextStep()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                                Lanjut
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: BIODATA PRIBADI --}}
                    <div x-show="step === 3" x-transition.opacity class="bg-white rounded-3xl p-6 border border-sky-100 shadow-sm space-y-5">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="text-sm font-extrabold text-slate-900">Biodata Pribadi</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Data diri siswa untuk verifikasi dan pelaporan.</p>
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex items-center gap-3 p-3 rounded-2xl border-2 cursor-pointer transition-all"
                                       :class="jenisKelamin === 'laki-laki' ? 'border-sky-500 bg-sky-50/60' : 'border-slate-100 hover:border-slate-200 bg-slate-50/40'">
                                    <input type="radio" name="jenis_kelamin" value="laki-laki" x-model="jenisKelamin" class="sr-only">
                                    <span class="text-xs font-bold flex-1" :class="jenisKelamin === 'laki-laki' ? 'text-sky-900' : 'text-slate-700'">Laki-laki</span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                          :class="jenisKelamin === 'laki-laki' ? 'border-sky-500 bg-sky-500' : 'border-slate-300'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="jenisKelamin === 'laki-laki'"></span>
                                    </span>
                                </label>
                                <label class="relative flex items-center gap-3 p-3 rounded-2xl border-2 cursor-pointer transition-all"
                                       :class="jenisKelamin === 'perempuan' ? 'border-sky-500 bg-sky-50/60' : 'border-slate-100 hover:border-slate-200 bg-slate-50/40'">
                                    <input type="radio" name="jenis_kelamin" value="perempuan" x-model="jenisKelamin" class="sr-only">
                                    <span class="text-xs font-bold flex-1" :class="jenisKelamin === 'perempuan' ? 'text-sky-900' : 'text-slate-700'">Perempuan</span>
                                    <span class="w-4 h-4 rounded-full border flex items-center justify-center"
                                          :class="jenisKelamin === 'perempuan' ? 'border-sky-500 bg-sky-500' : 'border-slate-300'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white" x-show="jenisKelamin === 'perempuan'"></span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Agama</label>
                                <select name="agama" x-model="agama"
                                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                    <option value="">Pilih Agama...</option>
                                    @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                                        <option value="{{ $agm }}" {{ old('agama') === $agm ? 'selected' : '' }}>{{ $agm }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" x-model="tempatLahir"
                                       placeholder="Kota / Kabupaten"
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                                    Tanggal Lahir
                                    <span x-show="calculatedAge" class="text-[10px] text-sky-600 font-semibold normal-case float-right" x-text="calculatedAge"></span>
                                </label>
                                <input type="date" name="tanggal_lahir" x-model="tanggalLahir" @change="calculateAge()"
                                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <button type="button" @click="prevStep()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                Kembali
                            </button>
                            <button type="button" @click="nextStep()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm transition">
                                Lanjut
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 4: KONTAK & ALAMAT --}}
                    <div x-show="step === 4" x-transition.opacity class="space-y-5">
                        <div class="bg-white rounded-3xl p-6 border border-sky-100 shadow-sm space-y-5">
                            <div class="border-b border-slate-100 pb-3">
                                <h3 class="text-sm font-extrabold text-slate-900">Kontak & Alamat</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Informasi kontak dan tempat tinggal siswa.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">No. Telp / WA</label>
                                    <input type="text" name="no_telp" x-model="noTelp"
                                           placeholder="08xxxxxxxxxx"
                                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Media Sosial</label>
                                    <input type="text" name="medsos" x-model="medsos"
                                           placeholder="@username / link medsos"
                                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Alamat</label>
                                    <textarea name="alamat" rows="2" x-model="alamat" placeholder="Alamat domisili lengkap"
                                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="button" @click="prevStep()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                Kembali
                            </button>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('kesiswaan.users.index') }}"
                                   class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">Batal</a>
                                <button type="submit"
                                        class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                                    Simpan Akun Siswa
                                </button>
                            </div>
                        </div>
                    </div>

                </div>{{-- END LEFT COLUMN --}}

                {{-- RIGHT: LIVE PREVIEW CARD (MATCH HEIGHT WITH FORM) --}}
                <div class="lg:w-80 xl:w-96 shrink-0 flex flex-col">
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden flex flex-col h-full">
                            {{-- Header --}}
                            <div class="px-5 py-3 bg-slate-50/80 border-b border-slate-100">
                                <h4 class="text-xs font-extrabold text-slate-700">Pratinjau Akun</h4>
                                <p class="text-[10px] text-slate-400">Data terisi otomatis dari form</p>
                            </div>

                            {{-- Avatar + Name --}}
                            <div class="px-5 pt-5 pb-4 flex items-center gap-3 border-b border-slate-100">
                                <div class="w-14 h-14 rounded-2xl border-2 border-sky-100 overflow-hidden bg-sky-50 flex items-center justify-center shrink-0">
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" alt="Preview" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!photoPreview">
                                        <span class="text-base font-black text-sky-400 uppercase" x-text="nama ? nama.trim().substring(0, 2) : '?'"></span>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-extrabold text-slate-900 truncate" x-text="nama || 'Nama Siswa'"></p>
                                    <p class="text-[11px] text-slate-400 truncate" x-text="email || 'email@contoh.com'"></p>
                                    <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-50 text-sky-600 border border-sky-100">Siswa</span>
                                </div>
                            </div>

                            {{-- Detail Rows --}}
                            <div class="px-5 py-3 divide-y divide-slate-100 text-xs flex-1 flex flex-col justify-between">
                                {{-- NIS --}}
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="text-slate-400 font-semibold">NIS</span>
                                    <span class="font-bold text-slate-700 text-right" x-text="nis || '-'"></span>
                                </div>
                                {{-- Kelas --}}
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="text-slate-400 font-semibold">Kelas</span>
                                    <span class="font-bold text-slate-700 text-right truncate ml-3 max-w-[60%]" x-text="kelasText || '-'"></span>
                                </div>

                                {{-- Jenis Kelamin --}}
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="text-slate-400 font-semibold">Jenis Kelamin</span>
                                    <span class="font-bold text-slate-700 text-right capitalize" x-text="jenisKelamin || '-'"></span>
                                </div>
                                {{-- Agama --}}
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="text-slate-400 font-semibold">Agama</span>
                                    <span class="font-bold text-slate-700 text-right" x-text="agama || '-'"></span>
                                </div>
                                {{-- TTL --}}
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="text-slate-400 font-semibold">TTL</span>
                                    <span class="font-bold text-slate-700 text-right truncate ml-3 max-w-[60%]"
                                          x-text="(tempatLahir || tanggalLahir) ? ((tempatLahir || '-') + ', ' + (formatTanggalLahir() || '-')) : '-'"></span>
                                </div>
                                {{-- No. Telp --}}
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="text-slate-400 font-semibold">No. Telp</span>
                                    <span class="font-bold text-slate-700 text-right" x-text="noTelp || '-'"></span>
                                </div>
                                {{-- Medsos --}}
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="text-slate-400 font-semibold">Medsos</span>
                                    <span class="font-bold text-slate-700 text-right truncate ml-3 max-w-[60%]" x-text="medsos || '-'"></span>
                                </div>
                                {{-- Alamat --}}
                                <div class="flex items-start justify-between py-2.5" x-show="alamat">
                                    <span class="text-slate-400 font-semibold shrink-0">Alamat</span>
                                    <span class="font-bold text-slate-700 text-right ml-3 text-[11px] leading-relaxed" x-text="alamat"></span>
                                </div>
                            </div>

                            {{-- Completeness indicator --}}
                            <div class="px-5 py-3.5 bg-slate-50/80 border-t border-slate-100 mt-auto">
                                <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1.5">
                                    <span class="font-semibold">Kelengkapan data</span>
                                    <span class="font-bold"
                                          :class="{
                                              'text-emerald-600': [nama, nis, email, kelasText, jenisKelamin].filter(Boolean).length === 5,
                                              'text-amber-600': [nama, nis, email, kelasText, jenisKelamin].filter(Boolean).length >= 3 && [nama, nis, email, kelasText, jenisKelamin].filter(Boolean).length < 5,
                                              'text-slate-400': [nama, nis, email, kelasText, jenisKelamin].filter(Boolean).length < 3
                                          }"
                                          x-text="[nama, nis, email, kelasText, jenisKelamin].filter(Boolean).length + '/5 wajib'">
                                    </span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         :class="{
                                             'bg-emerald-500': [nama, nis, email, kelasText, jenisKelamin].filter(Boolean).length === 5,
                                             'bg-amber-500': [nama, nis, email, kelasText, jenisKelamin].filter(Boolean).length >= 3 && [nama, nis, email, kelasText, jenisKelamin].filter(Boolean).length < 5,
                                             'bg-slate-300': [nama, nis, email, kelasText, jenisKelamin].filter(Boolean).length < 3
                                         }"
                                         :style="`width: ${([nama, nis, email, kelasText, jenisKelamin].filter(Boolean).length / 5) * 100}%`">
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>{{-- END RIGHT COLUMN --}}

            </div>
        </div>

        {{-- =============================================================== --}}
        {{-- ROLE: PEMBINA (GURU) - SIMPLIFIED                               --}}
        {{-- =============================================================== --}}
        <div x-show="role === 'pembina'" x-cloak>
            <div class="flex flex-col lg:flex-row gap-5 items-stretch">

                {{-- LEFT: FORM --}}
                <div class="flex-1 min-w-0">
                    <a href="{{ route('kesiswaan.users.import-form', 'pembina') }}" class="mb-5 inline-flex items-center gap-2 rounded-2xl border border-blue-200 bg-blue-50 px-5 py-3 text-xs font-bold text-blue-800 hover:bg-blue-100">
                        Import Akun Guru dengan Excel
                    </a>
                    <div class="bg-white rounded-3xl p-6 border border-sky-100 shadow-sm space-y-5">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="text-sm font-extrabold text-slate-900">Data Guru / Pembina Ekskul</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Lengkapi seluruh biodata pembina. Username akan dibuat sendiri oleh pembina saat login pertama.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Nama Lengkap Beserta Gelar *</label>
                                <input required type="text" name="pembina_nama" x-model="pembinaNama" :disabled="role !== 'pembina'" placeholder="Contoh: Dra. Siti Fatimah, M.Pd" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">NIP (18 angka) *</label>
                                <input required type="text" inputmode="numeric" name="nip" :disabled="role !== 'pembina'" value="{{ old('nip') }}" minlength="18" maxlength="18" pattern="[0-9]{18}" placeholder="18 digit NIP" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Email *</label>
                                <input required type="email" name="email" :disabled="role !== 'pembina'" value="{{ old('email') }}" placeholder="guru@sekolah.sch.id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Jenis Kelamin *</label>
                                <select required name="pembina_jenis_kelamin" :disabled="role !== 'pembina'" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="laki-laki" {{ old('pembina_jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="perempuan" {{ old('pembina_jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tempat Lahir *</label>
                                <input required type="text" name="pembina_tempat_lahir" :disabled="role !== 'pembina'" value="{{ old('pembina_tempat_lahir') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Lahir *</label>
                                <input required type="date" name="pembina_tanggal_lahir" :disabled="role !== 'pembina'" value="{{ old('pembina_tanggal_lahir') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Agama *</label>
                                <select required name="pembina_agama" :disabled="role !== 'pembina'" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                                    <option value="">Pilih agama</option>
                                    @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ old('pembina_agama') === $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">No. Telp / WA *</label>
                                <input required type="text" name="pembina_no_telp" :disabled="role !== 'pembina'" value="{{ old('pembina_no_telp') }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Media Sosial (opsional)</label>
                                <input type="text" name="pembina_medsos" :disabled="role !== 'pembina'" value="{{ old('pembina_medsos') }}" placeholder="@username / tautan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Alamat *</label>
                                <textarea required name="pembina_alamat" rows="2" :disabled="role !== 'pembina'" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">{{ old('pembina_alamat') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Foto Profil (opsional)</label>
                                <input type="file" name="pembina_foto" accept="image/jpeg,image/png,image/jpg,image/webp" :disabled="role !== 'pembina'" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                                <p class="text-[10px] text-slate-400 mt-1">Format JPG, PNG, WEBP. Maksimal 2MB.</p>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                            <a href="{{ route('kesiswaan.users.index') }}"
                               class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">Batal</a>
                            <button type="submit"
                                    class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                                Simpan Akun Pembina
                            </button>
                        </div>
                    </div>
                </div>{{-- END LEFT COLUMN --}}

                {{-- RIGHT: LIVE PREVIEW CARD --}}
                <div class="lg:w-72 shrink-0">
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-700">Pratinjau Akun</h4>
                                <p class="text-[10px] text-slate-400">Data terisi otomatis</p>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-100">Pembina</span>
                        </div>

                        <div class="px-5 pt-5 pb-4 flex items-center gap-3 border-b border-slate-100">
                            <div class="w-14 h-14 rounded-2xl border-2 border-indigo-100 overflow-hidden bg-indigo-50 flex items-center justify-center shrink-0">
                                <span class="text-base font-black text-indigo-400 uppercase" x-text="pembinaNama ? pembinaNama.trim().substring(0, 2) : 'PB'"></span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-extrabold text-slate-900 truncate" x-text="pembinaNama || 'Nama Guru / Pembina'"></p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Biodata diisi kesiswaan</p>
                                <span class="inline-block mt-1 text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">Role: Pembina Ekskul</span>
                            </div>
                        </div>

                        <div class="px-5 py-4">
                            <div class="p-3 bg-sky-50 border border-sky-100 rounded-2xl text-[11px] text-sky-700 leading-relaxed">
                                <span class="font-bold block mb-1">â„¹ï¸ Info akun pembina:</span>
                                Username dan password baru akan dibuat pembina saat login pertama kali.
                            </div>
                        </div>
                    </div>
                </div>{{-- END RIGHT COLUMN --}}

            </div>
        </div>


        {{-- =============================================================== --}}
        {{-- ROLE: STAF (KESISWAAN / ADMIN)                                  --}}
        {{-- =============================================================== --}}
        <div x-show="role === 'kesiswaan' || role === 'admin'" x-cloak class="space-y-5">
            <div class="bg-white rounded-3xl p-6 border border-sky-100 shadow-sm space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900">Data Login Staf / Administrator</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Akun staf menggunakan kombinasi username dan email.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Username <span class="text-rose-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="Username untuk login"
                               :required="role === 'kesiswaan' || role === 'admin'"
                               :disabled="role !== 'kesiswaan' && role !== 'admin'"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="staf@sekolah.sch.id"
                               :disabled="role !== 'kesiswaan' && role !== 'admin'"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <a href="{{ route('kesiswaan.users.index') }}"
                       class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl transition">Batal</a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold text-xs rounded-2xl shadow-sm shadow-sky-200 transition">
                        Simpan Akun Staf
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection
