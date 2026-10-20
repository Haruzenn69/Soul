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
        kolomKosong: [],
        // Dropdown provinsi -> kota/kabupaten tempat lahir (laravolt/indonesia)
        siswaProvinsi: '{{ $siswaProvinceCode ?? '' }}',
        siswaCities: [],
        siswaCitiesLoading: false,
        pembinaProvinsi: '{{ $pembinaProvinceCode ?? '' }}',
        pembinaCities: [],
        pembinaCitiesLoading: false,
        peringatan: {
            isOpen: false,
            title: 'Peringatan Validasi',
            message: '',
            items: [],
            focusId: null,
            targetStep: null,
            actionLabel: 'Periksa Sekarang',
        },
        pembinaRequiredFields: [
            { id: 'input-pembina-nama', label: 'Nama Lengkap Beserta Gelar' },
            { id: 'input-pembina-nip', label: 'NIP (18 angka)' },
            { id: 'input-pembina-email', label: 'Email' },
            { id: 'input-pembina-jenis-kelamin', label: 'Jenis Kelamin' },
            { id: 'input-pembina-provinsi', label: 'Provinsi Tempat Lahir' },
            { id: 'input-pembina-tempat-lahir', label: 'Kota / Kabupaten Tempat Lahir' },

            { id: 'input-pembina-tanggal-lahir', label: 'Tanggal Lahir' },
            { id: 'input-pembina-agama', label: 'Agama' },
            { id: 'input-pembina-no-telp', label: 'No. Telp / WA' },
            { id: 'input-pembina-alamat', label: 'Alamat' },
        ],

        init() {
            this.calculateAge();
            this.updateKelasText();
            if (this.siswaProvinsi) this.loadCities('siswa', this.siswaProvinsi);
            if (this.pembinaProvinsi) this.loadCities('pembina', this.pembinaProvinsi);
            @if ($errors->any())
                this.$nextTick(() => {
                    this.showWarning({
                        title: 'Terjadi Kesalahan Validasi',
                        message: 'Terdapat data yang belum sesuai dengan ketentuan sistem:',
                        items: [
                            @foreach ($errors->all() as $error)
                                { label: '{{ addslashes($error) }}' },
                            @endforeach
                        ],
                        actionLabel: 'Periksa Sekarang'
                    });
                });
            @elseif (session('error'))
                this.$nextTick(() => {
                    this.showWarning({
                        title: 'Pemberitahuan Sistem',
                        message: '{{ addslashes(session('error')) }}',
                        @if (session('import_errors'))
                            items: [
                                @foreach (session('import_errors') as $imErr)
                                    { label: '{{ addslashes($imErr) }}' },
                                @endforeach
                            ],
                        @endif
                        actionLabel: 'Mengerti'
                    });
                });
            @endif
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

        loadCities(target, provinceCode) {
            const isP = target === 'pembina';
            if (isP) { this.pembinaCities = []; this.pembinaCitiesLoading = true; }
            else { this.siswaCities = []; this.siswaCitiesLoading = true; }

            fetch(`{{ route('api.cities') }}?province_code=${encodeURIComponent(provinceCode)}`)
                .then(r => r.json())
                .then(data => {
                    if (isP) { this.pembinaCities = data; this.pembinaCitiesLoading = false; }
                    else { this.siswaCities = data; this.siswaCitiesLoading = false; }
                    this.syncCitySelect(target);
                })
                .catch(() => {
                    if (isP) this.pembinaCitiesLoading = false;
                    else this.siswaCitiesLoading = false;
                });
        },

        syncCitySelect(target) {
            this.$nextTick(() => {
                const isP = target === 'pembina';
                const el = document.getElementById(isP ? 'input-pembina-tempat-lahir' : 'input-tempat-lahir');
                if (el) el.value = isP ? this.pembinaTempatLahir : this.tempatLahir;
            });
        },

        onProvinsiChange(target) {
            const isP = target === 'pembina';
            if (isP) {
                this.pembinaTempatLahir = '';
                if (this.pembinaProvinsi) this.loadCities('pembina', this.pembinaProvinsi);
                else this.pembinaCities = [];
            } else {
                this.tempatLahir = '';
                if (this.siswaProvinsi) this.loadCities('siswa', this.siswaProvinsi);
                else this.siswaCities = [];
            }
        },

        showWarning(options) {
            this.peringatan = {
                isOpen: true,
                title: options.title || 'Peringatan Validasi',
                message: options.message || '',
                items: options.items || [],
                focusId: options.focusId || null,
                targetStep: options.targetStep || null,
                actionLabel: options.actionLabel || 'Periksa Sekarang',
            };
            this.$nextTick(() => {
                const dialog = document.getElementById('modalPeringatan');
                if (dialog) {
                    if (typeof dialog.showModal === 'function') {
                        dialog.showModal();
                    } else {
                        dialog.setAttribute('open', '');
                    }
                }
            });
        },

        closeWarning() {
            const dialog = document.getElementById('modalPeringatan');
            if (dialog) {
                if (typeof dialog.close === 'function') {
                    dialog.close();
                } else {
                    dialog.removeAttribute('open');
                }
            }
            this.peringatan.isOpen = false;
        },

        confirmWarningAction() {
            const targetId = this.peringatan.focusId;
            const targetStep = this.peringatan.targetStep;
            this.closeWarning();
            if (targetStep && this.role === 'siswa') {
                this.step = targetStep;
            }
            if (targetId) {
                this.$nextTick(() => {
                    const el = document.getElementById(targetId);
                    if (el) {
                        el.focus();
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }
        },

        validateStep(targetStep) {
            if (this.step === 1 && targetStep > 1) {
                if (!this.email || !this.email.includes('@')) {
                    if (!this.email.includes('@')) {
                        this.showWarning({
                            title: 'Format Email Tidak Valid',
                            message: 'Alamat email harus menggunakan tanda @ (contoh: siswa@sekolah.sch.id atau user@gmail.com).',
                            focusId: 'input-siswa-email',
                            targetStep: 1,
                            actionLabel: 'Perbaiki Email'
                        });
                        return false;
                    }
                    this.showWarning({
                        title: 'Email Siswa Tidak Valid',
                        message: 'Pastikan alamat email siswa terisi lengkap dan valid (contoh: siswa@sekolah.sch.id).',
                        focusId: 'input-siswa-email',
                        targetStep: 1,
                        actionLabel: 'Perbaiki Email'
                    });
                    document.getElementById('input-siswa-email')?.focus();
                    return false;
                }
                if (!this.nis || this.nis.length !== 10) {
                    this.showWarning({
                        title: 'Format NIS Tidak Sesuai',
                        message: 'NIS harus terdiri dari tepat 10 digit angka (saat ini terisi ' + (this.nis ? this.nis.length : 0) + ' angka).',
                        focusId: 'input-siswa-nis',
                        targetStep: 1,
                        actionLabel: 'Perbaiki NIS'
                    });
                    document.getElementById('input-siswa-nis')?.focus();
                    return false;
                }
                if (!this.nama || this.nama.trim().length < 2) {
                    this.showWarning({
                        title: 'Nama Lengkap Belum Valid',
                        message: 'Nama lengkap siswa wajib diisi minimal 2 karakter.',
                        focusId: 'input-siswa-nama',
                        targetStep: 1,
                        actionLabel: 'Isi Nama'
                    });
                    document.getElementById('input-siswa-nama')?.focus();
                    return false;
                }
            }
            if (this.step === 2 && targetStep > 2) {
                const selKelas = document.getElementById('kelas_id_select');
                if (!selKelas || !selKelas.value) {
                    this.showWarning({
                        title: 'Kelas Belum Dipilih',
                        message: 'Mohon pilih kelas siswa terlebih dahulu sebelum melanjutkan.',
                        focusId: 'kelas_id_select',
                        targetStep: 2,
                        actionLabel: 'Pilih Kelas'
                    });
                    selKelas?.focus();
                    return false;
                }
            }
            if (this.step === 3 && targetStep > 3) {
                if (!this.jenisKelamin) {
                    this.showWarning({
                        title: 'Jenis Kelamin Belum Dipilih',
                        message: 'Mohon pilih jenis kelamin siswa (Laki-laki atau Perempuan).',
                        focusId: 'input-siswa-gender-l',
                        targetStep: 3,
                        actionLabel: 'Pilih Jenis Kelamin'
                    });
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
                    this.showWarning({
                        title: 'Ukuran Foto Terlalu Besar',
                        message: 'Ukuran file foto melebihi batas maksimal 2MB. Silakan pilih foto dengan ukuran lebih kecil.',
                        focusId: 'input-foto-siswa',
                        actionLabel: 'Pilih Foto Lain'
                    });
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
                    this.showWarning({
                        title: 'Ukuran Foto Terlalu Besar',
                        message: 'Ukuran file foto melebihi batas maksimal 2MB. Silakan pilih foto dengan ukuran lebih kecil.',
                        focusId: 'input-foto-pembina',
                        actionLabel: 'Pilih Foto Lain'
                    });
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
        },

        validatePembina() {
            this.kolomKosong = this.pembinaRequiredFields.filter(f => {
                const el = document.getElementById(f.id);
                return !el || !String(el.value ?? '').trim();
            });
            if (this.kolomKosong.length > 0) {
                this.showWarning({
                    title: 'Ada Kolom Belum Terisi',
                    message: 'Kolom berikut masih kosong. Lengkapi terlebih dahulu sebelum menyimpan akun pembina:',
                    items: this.kolomKosong,
                    focusId: this.kolomKosong[0].id,
                    actionLabel: 'Isi Sekarang'
                });
                return false;
            }
            const namaEl = document.getElementById('input-pembina-nama');
            const namaVal = namaEl ? namaEl.value.trim() : this.pembinaNama;
            if (!namaVal || namaVal.length < 2) {
                this.showWarning({
                    title: 'Nama Pembina Belum Valid',
                    message: 'Nama lengkap pembina wajib diisi minimal 2 karakter.',
                    focusId: 'input-pembina-nama',
                    actionLabel: 'Perbaiki Nama'
                });
                return false;
            }

            const nipEl = document.getElementById('input-pembina-nip');
            const nipVal = nipEl ? nipEl.value.trim() : this.pembinaNip;
            if (!nipVal || nipVal.length !== 18 || !/^\d{18}$/.test(nipVal)) {
                this.showWarning({
                    title: 'Format NIP Tidak Sesuai',
                    message: 'NIP pembina harus terdiri dari tepat 18 digit angka (saat ini terisi ' + (nipVal ? nipVal.length : 0) + ' angka).',
                    focusId: 'input-pembina-nip',
                    actionLabel: 'Perbaiki NIP'
                });
                return false;
            }

            const emailEl = document.getElementById('input-pembina-email');
            const emailVal = emailEl ? emailEl.value.trim() : this.pembinaEmail;
            if (!emailVal) {
                this.showWarning({
                    title: 'Email Pembina Belum Diisi',
                    message: 'Alamat email pembina wajib diisi.',
                    focusId: 'input-pembina-email',
                    actionLabel: 'Isi Email'
                });
                return false;
            }
            if (!emailVal.includes('@')) {
                this.showWarning({
                    title: 'Format Email Tidak Valid',
                    message: 'Alamat email pembina harus menggunakan tanda @ (contoh: guru@sekolah.sch.id atau user@gmail.com).',
                    focusId: 'input-pembina-email',
                    actionLabel: 'Perbaiki Email'
                });
                return false;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                this.showWarning({
                    title: 'Format Email Tidak Lengkap',
                    message: 'Alamat email pembina harus memiliki domain yang valid (contoh: guru@sekolah.sch.id).',
                    focusId: 'input-pembina-email',
                    actionLabel: 'Perbaiki Email'
                });
                return false;
            }

            return true;
        },

        validateStaf() {
            const usernameEl = document.getElementById('input-staf-username');
            if (usernameEl && (!usernameEl.value || usernameEl.value.trim().length < 3)) {
                this.showWarning({
                    title: 'Username Belum Diisi',
                    message: 'Username untuk akun staf minimal 3 karakter.',
                    focusId: 'input-staf-username',
                    actionLabel: 'Isi Username'
                });
                return false;
            }

            const emailEl = document.getElementById('input-staf-email');
            if (emailEl) {
                const val = emailEl.value.trim();
                if (!val) {
                    this.showWarning({
                        title: 'Email Staf Belum Diisi',
                        message: 'Alamat email staf wajib diisi untuk login.',
                        focusId: 'input-staf-email',
                        actionLabel: 'Isi Email'
                    });
                    return false;
                }
                if (!val.includes('@')) {
                    this.showWarning({
                        title: 'Format Email Tidak Valid',
                        message: 'Alamat email staf harus menggunakan tanda @ (contoh: staf@sekolah.sch.id atau user@gmail.com).',
                        focusId: 'input-staf-email',
                        actionLabel: 'Perbaiki Email'
                    });
                    return false;
                }
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                    this.showWarning({
                        title: 'Format Email Tidak Lengkap',
                        message: 'Alamat email staf harus memiliki domain yang valid.',
                        focusId: 'input-staf-email',
                        actionLabel: 'Perbaiki Email'
                    });
                    return false;
                }
            }
            return true;
        },

        handleSubmit(event) {
            if (this.role === 'siswa') {
                if (!this.email || !this.email.trim() || !this.email.includes('@') || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email.trim()) ||
                    !this.nis || this.nis.length !== 10 || !this.nama || this.nama.trim().length < 2) {
                    this.step = 1;
                    if (!this.validateStep(2)) {
                        event.preventDefault();
                        return false;
                    }
                }
                const selKelas = document.getElementById('kelas_id_select');
                if (!selKelas || !selKelas.value) {
                    this.step = 2;
                    if (!this.validateStep(3)) {
                        event.preventDefault();
                        return false;
                    }
                }
                if (!this.jenisKelamin) {
                    this.step = 3;
                    if (!this.validateStep(4)) {
                        event.preventDefault();
                        return false;
                    }
                }
            } else if (this.role === 'pembina') {
                if (!this.validatePembina()) {
                    event.preventDefault();
                    return false;
                }
            } else if (this.role === 'kesiswaan' || this.role === 'admin') {
                if (!this.validateStaf()) {
                    event.preventDefault();
                    return false;
                }
            }






        },

        fokusKolomKosong() {
            const pertama = this.kolomKosong[0];
            if (pertama) {
                document.getElementById(pertama.id)?.focus();
            }
            this.confirmWarningAction();
        },
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
    <form action="{{ route('kesiswaan.users.store') }}" method="POST" enctype="multipart/form-data" id="form-create-user" novalidate x-on:submit="handleSubmit($event)">
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
                                    <input type="radio" name="jenis_kelamin" id="input-siswa-gender-l" value="laki-laki" x-model="jenisKelamin" class="sr-only">
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
                                <div class="space-y-3">
                                    <select id="select-siswa-provinsi" x-model="siswaProvinsi" @change="onProvinsiChange('siswa')" aria-label="Provinsi"
                                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                                        <option value="">Pilih provinsi...</option>
                                        @foreach ($provinces as $province)
                                            <option value="{{ $province->code }}" {{ ($siswaProvinceCode ?? null) === $province->code ? 'selected' : '' }}>{{ ucwords(strtolower($province->name)) }}</option>
                                        @endforeach
                                    </select>
                                    <select name="tempat_lahir" id="input-tempat-lahir" x-model="tempatLahir" aria-label="Kota / Kabupaten"
                                            :disabled="!siswaProvinsi"
                                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition disabled:opacity-60 disabled:cursor-not-allowed">
                                        <option value="" x-text="!siswaProvinsi ? 'Pilih provinsi terlebih dahulu' : (siswaCitiesLoading ? 'Memuat kota / kabupaten...' : 'Pilih kota / kabupaten')"></option>
                                        <template x-for="city in siswaCities" :key="city.id">
                                            <option :value="city.name" x-text="city.name"></option>
                                        </template>
                                    </select>
                                </div>
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
                                <input type="text" name="pembina_nama" id="input-pembina-nama" x-model="pembinaNama" :disabled="role !== 'pembina'" placeholder="Contoh: Dra. Siti Fatimah, M.Pd" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">NIP (18 angka) *</label>
                                <input type="text" inputmode="numeric" name="nip" id="input-pembina-nip" :disabled="role !== 'pembina'" value="{{ old('nip') }}" placeholder="18 digit NIP" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Email *</label>
                                <input type="email" name="email" id="input-pembina-email" :disabled="role !== 'pembina'" value="{{ old('email') }}" placeholder="guru@sekolah.sch.id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Jenis Kelamin *</label>
                                <select name="pembina_jenis_kelamin" id="input-pembina-jenis-kelamin" :disabled="role !== 'pembina'" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="laki-laki" {{ old('pembina_jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="perempuan" {{ old('pembina_jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Provinsi Tempat Lahir *</label>
                                <select id="input-pembina-provinsi" x-model="pembinaProvinsi" @change="onProvinsiChange('pembina')" :disabled="role !== 'pembina'" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs disabled:opacity-60 disabled:cursor-not-allowed">
                                    <option value="">Pilih provinsi...</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->code }}" {{ ($pembinaProvinceCode ?? null) === $province->code ? 'selected' : '' }}>{{ ucwords(strtolower($province->name)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Kota / Kabupaten Tempat Lahir *</label>
                                <select name="pembina_tempat_lahir" id="input-pembina-tempat-lahir" x-model="pembinaTempatLahir"
                                        :disabled="role !== 'pembina' || !pembinaProvinsi"
                                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs disabled:opacity-60 disabled:cursor-not-allowed">
                                    <option value="" x-text="!pembinaProvinsi ? 'Pilih provinsi terlebih dahulu' : (pembinaCitiesLoading ? 'Memuat kota / kabupaten...' : 'Pilih kota / kabupaten')"></option>
                                    <template x-for="city in pembinaCities" :key="city.id">
                                        <option :value="city.name" x-text="city.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tanggal Lahir *</label>
                                <input type="date" name="pembina_tanggal_lahir" id="input-pembina-tanggal-lahir" :disabled="role !== 'pembina'" value="{{ old('pembina_tanggal_lahir') }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Agama *</label>
                                <select name="pembina_agama" id="input-pembina-agama" :disabled="role !== 'pembina'" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                                    <option value="">Pilih agama</option>
                                    @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agama)
                                        <option value="{{ $agama }}" {{ old('pembina_agama') === $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">No. Telp / WA *</label>
                                <input type="text" name="pembina_no_telp" id="input-pembina-no-telp" :disabled="role !== 'pembina'" value="{{ old('pembina_no_telp') }}" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Media Sosial (opsional)</label>
                                <input type="text" name="pembina_medsos" :disabled="role !== 'pembina'" value="{{ old('pembina_medsos') }}" placeholder="@username / tautan" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Alamat *</label>
                                <textarea name="pembina_alamat" id="input-pembina-alamat" rows="2" :disabled="role !== 'pembina'" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">{{ old('pembina_alamat') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Foto Profil (opsional)</label>
                                <input type="file" name="pembina_foto" id="input-foto-pembina" @change="handlePembinaPhoto($event)" accept="image/jpeg,image/png,image/jpg,image/webp" :disabled="role !== 'pembina'" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs">
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
                        <input type="text" name="username" id="input-staf-username" value="{{ old('username') }}" placeholder="Username untuk login"
                               :required="role === 'kesiswaan' || role === 'admin'"
                               :disabled="role !== 'kesiswaan' && role !== 'admin'"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-800 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-2 focus:ring-sky-100 transition">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="input-staf-email" value="{{ old('email') }}" placeholder="staf@sekolah.sch.id"
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

    {{-- MODAL PERINGATAN (UNIVERSAL WARNING MODAL) --}}
    <dialog id="modalPeringatan"
            class="rounded-3xl backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm p-0 w-full max-w-md shadow-2xl border border-amber-200/80 m-auto"
            @click.self="closeWarning()">
        <div class="bg-white rounded-3xl p-6 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-start justify-between pb-3.5 border-b border-slate-100 gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100/90 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200/80 shadow-sm shadow-amber-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 leading-snug" x-text="peringatan.title || 'Peringatan Validasi'"></h3>
                        <p class="text-[11px] text-slate-400 font-medium">Periksa kembali data yang dimasukkan</p>
                    </div>
                </div>
                <button type="button" @click="closeWarning()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition text-xs font-bold shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="mt-4 space-y-3">
                <p class="text-xs text-slate-600 leading-relaxed font-medium" x-text="peringatan.message"></p>

                {{-- List of items if multiple (e.g. empty fields or server validation errors) --}}
                <template x-if="peringatan.items && peringatan.items.length > 0">
                    <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                        <template x-for="(item, index) in peringatan.items" :key="index">
                            <div class="flex items-start gap-2.5 rounded-xl bg-amber-50/80 border border-amber-200/60 px-3 py-2 text-xs font-semibold text-amber-900">
                                <span class="w-4 h-4 rounded-full bg-amber-200/80 text-amber-800 text-[10px] font-bold flex items-center justify-center shrink-0 mt-0.5" x-text="index + 1"></span>
                                <span class="flex-1 leading-snug" x-text="typeof item === 'object' ? item.label : item"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Footer Buttons --}}
            <div class="flex items-center justify-end gap-2.5 pt-4 mt-4 border-t border-slate-100">
                <button type="button" @click="closeWarning()"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Tutup
                </button>
                <button type="button" @click="confirmWarningAction()"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold text-xs transition shadow-md shadow-amber-500/20 flex items-center gap-1.5">
                    <span x-text="peringatan.actionLabel || 'Periksa Sekarang'"></span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </div>
    </dialog>
    <dialog id="modalKolomKosong" class="rounded-3xl backdrop:bg-slate-900/40 p-0 w-full max-w-md shadow-2xl border border-amber-100">
        <div class="bg-white rounded-3xl border border-amber-100 p-6">
            <div class="flex items-start justify-between pb-3 border-b border-slate-100 gap-3">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span class="hidden sm:inline">Ada Kolom Belum Terisi</span>
                </h3>
                <button type="button" @click="document.getElementById('modalKolomKosong')?.close()" class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 hover:text-amber-700 flex items-center justify-center transition text-xs font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mt-4 space-y-4">
                <p class="text-xs text-slate-600">Kolom berikut masih kosong. Lengkapi dulu sebelum menyimpan akun pembina.</p>
                <ul class="max-h-56 overflow-y-auto space-y-1.5 pr-1">
                    <template x-for="(kolom, index) in kolomKosong" :key="kolom.id">
                        <li class="flex items-start gap-2 rounded-xl bg-amber-50 border border-amber-100 px-3 py-2 text-xs font-semibold text-amber-800">
                            <span class="w-2" x-text="index + 1"></span>
                            <span class="flex-1" x-text="kolom.label"></span>
                        </li>
                    </template>
                </ul>
            </div>
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" @click="document.getElementById('modalKolomKosong')?.close()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">Tutup</button>
                <button type="button" @click="document.getElementById('modalKolomKosong')?.close(); fokusKolomKosong()" class="px-4 py-2 rounded-xl bg-amber-600 text-white font-bold text-xs hover:bg-amber-700 transition shadow-md shadow-amber-600/20">Isi Sekarang</button>
            </div>
        </div>
    </dialog>

</div>
@endsection
