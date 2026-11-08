@extends('layouts.kesiswaan')
@section('title', 'Pusat Bantuan & Panduan Kesiswaan')

@section('content')
<div class="space-y-6 animate-fade-up" x-data="{
    search: '',
    activeTab: 'semua',
    openFaq: null,
    toggleFaq(id) {
        this.openFaq = this.openFaq === id ? null : id;
    },
    matches(keywords) {
        if (!this.search.trim()) return true;
        const query = this.search.toLowerCase().trim();
        return keywords.toLowerCase().includes(query);
    }
}">

    {{-- HERO CARD BIRU --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        {{-- Ambient blur circles --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative space-y-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur text-white text-[11px] font-bold mb-2 border border-white/20">
                        <i class="fa-solid fa-book-bookmark text-xs"></i>
                        <span>Dokumentasi Resmi & Panduan Operasional</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        Pusat Bantuan & Panduan Kesiswaan
                    </h1>
                    <p class="text-xs md:text-sm text-white/85 mt-1.5 max-w-2xl leading-relaxed">
                        Ketahui seluruh wewenang, alur operasional fitur, serta solusi atas berbagai pertanyaan dan kendala teknis di panel Kesiswaan.
                    </p>
                </div>
            </div>

            {{-- SEARCH BAR INTERAKTIF --}}
            <div class="pt-4 border-t border-white/20">
                <div class="relative max-w-xl">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-white/60 text-sm pointer-events-none"></i>
                    <input type="text"
                           x-model="search"
                           placeholder="Ketik kata kunci (contoh: import, reset password, kelulusan, nilai)..."
                           class="w-full pl-11 pr-4 py-3 bg-white/15 backdrop-blur border border-white/25 rounded-2xl text-xs md:text-sm text-white placeholder-white/60 focus:outline-none focus:bg-white focus:text-slate-900 focus:placeholder-slate-400 focus:ring-2 focus:ring-white/40 transition">
                </div>
                <p class="text-[11px] text-white/70 mt-2" x-show="search.trim()">
                    Menampilkan hasil pencarian untuk: <strong class="text-white" x-text="search"></strong>
                </p>
            </div>
        </div>
    </div>

    {{-- FILTER TABS KATEGORI --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <button type="button"
                @click="activeTab = 'semua'"
                :class="activeTab === 'semua' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Semua Topik
        </button>
        <button type="button"
                @click="activeTab = 'akun'"
                :class="activeTab === 'akun' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Akun & Data Siswa
        </button>
        <button type="button"
                @click="activeTab = 'ekskul'"
                :class="activeTab === 'ekskul' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Ekstrakurikuler
        </button>
        <button type="button"
                @click="activeTab = 'akademik'"
                :class="activeTab === 'akademik' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Kelas & Tahun Ajaran
        </button>
        <button type="button"
                @click="activeTab = 'penilaian'"
                :class="activeTab === 'penilaian' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Penilaian & Rapor
        </button>
        <button type="button"
                @click="activeTab = 'pelatih'"
                :class="activeTab === 'pelatih' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Pelatih Luar
        </button>
    </div>

    {{-- BAGIAN 1: APA SAJA YANG BISA DILAKUKAN DI ROLE KESISWAAN --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-compass text-sky-600"></i>
                    Direktori Fitur & Wewenang Role Kesiswaan
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Panduan lengkap mengenai fungsi setiap menu yang dapat Anda kelola</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- 1. Manajemen Akun & Reset Password --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('akun pengguna siswa pembina staf reset password kata sandi info akun edit nonaktif')"
                 class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-slate-300 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Manajemen Akun & Reset Sandi</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Kelola seluruh akun pengguna di sekolah (Siswa, Guru Pembina, Staf Kesiswaan).
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600">
                        <p class="font-bold text-slate-800">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Buat Akun Baru:</strong> Siswa, Guru Pembina, atau Staf dengan data lengkap.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Reset Password:</strong> Mengembalikan kata sandi akun yang lupa ke default: <code class="font-mono bg-slate-100 px-1 py-0.5 rounded text-slate-700 font-bold">password</code>.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Status Akun:</strong> Mengaktifkan atau menonaktifkan akun siswa yang pindah/skorsing.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Riwayat Profil:</strong> Melihat log audit perubahan data identitas.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('kesiswaan.users.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Akun Pengguna <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 2. Import Massal Excel --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('import massal excel spreadsheet upload template nis nip')"
                 class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-slate-300 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-file-excel"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Import Massal Excel</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Memasukkan ratusan data siswa dan pembina sekaligus dari file spreadsheet tanpa input manual.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600">
                        <p class="font-bold text-slate-800">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Unduh Template:</strong> Template Excel dengan kolom NIS, Nama, Kelas, Tanggal Lahir, Agama, dll.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Validasi Otomatis:</strong> Sistem mengecek duplikasi NIS/NIP dan format data sebelum disimpan.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Generate Akun Otomatis:</strong> Setiap baris siswa otomatis menjadi akun login aktif.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('kesiswaan.users.create') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Form Import <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 3. Kelola Ekskul & Pembina --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('ekskul ekstrakurikuler pembina pelatih kategori rekrutmen kuota')"
                 class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-slate-300 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-shapes"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Kelola Ekstrakurikuler</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Pusat kendali seluruh kegiatan ekstrakurikuler sekolah, pembina, serta jadwal kegiatan.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600">
                        <p class="font-bold text-slate-800">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Tambah/Edit Ekskul:</strong> Nama, deskripsi, logo, jadwal latihan, dan ruangan.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Tetapkan Pembina & Pelatih:</strong> Pasangkan guru pengampu ke ekskul.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Status Rekrutmen:</strong> Buka atau tutup pendaftaran siswa ke ekskul.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Kategori Ekskul:</strong> Olahraga, Seni & Budaya, Akademik, Keagamaan, Kepemimpinan.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('kesiswaan.ekskuls.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Data Ekskul <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 4. Data Kelas & Rombel --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akademik') && matches('kelas rombel jurusan tingkat x xi xii')"
                 class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-slate-300 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Data Kelas & Rombel</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Pengelompokan rombongan belajar sekolah berdasarkan tingkat, jurusan, dan tahun ajaran aktif.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600">
                        <p class="font-bold text-slate-800">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Buat Kelas Baru:</strong> Tingkat (10, 11, 12), Jurusan (RPL, TKJ, dll), dan Rombel (1, 2, dll).</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Daftar Anggota Kelas:</strong> Pantau seluruh siswa yang terdaftar di masing-masing kelas.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Integrasi Tahun Ajaran:</strong> Kelas otomatis terikat pada tahun ajaran aktif.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('kesiswaan.kelas.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Data Kelas <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 5. Tahun Ajaran & Kelulusan Kelas 12 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akademik') && matches('tahun ajaran kenaikan kelulusan kelas 12 alumni tinggal historis rollover')"
                 class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-slate-300 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Tahun Ajaran & Kelulusan</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Manajemen siklus tahun ajaran baru, kenaikan tingkat kelas, dan kelulusan massal siswa kelas 12.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600">
                        <p class="font-bold text-slate-800">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Buat & Aktifkan TA:</strong> Menjadikan tahun ajaran baru sebagai periode operasional aktif.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Kelulusan Kelas 12:</strong> Meluluskan siswa kelas 12 menjadi Alumni (Nonaktif) secara massal.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Penyesuaian Kenaikan:</strong> Menaikkan siswa kelas 10 ke 11, kelas 11 ke 12, dan siswa baru.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Siswa Tinggal Kelas:</strong> Menangani siswa yang tidak naik kelas agar tetap di kelas yang sama.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('kesiswaan.tahun-ajaran.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Tahun Ajaran <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 6. Laporan Penilaian & PDF --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'penilaian') && matches('laporan penilaian rapor nilai ekskul pdf cetak unduh predikat presensi')"
                 class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-slate-300 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Laporan Penilaian & Rapor</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Rekapitulasi nilai dan kehadiran seluruh siswa di kegiatan ekstrakurikuler per semester.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600">
                        <p class="font-bold text-slate-800">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Pantau Penilaian Pembina:</strong> Melihat ekskul mana yang sudah atau belum selesai dinilai.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Unduh Laporan PDF:</strong> Cetak lembar nilai resmi lengkap dengan predikat dan tanda tangan.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Historis Siswa:</strong> Melihat riwayat nilai ekskul siswa dari awal masuk hingga lulus.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('kesiswaan.laporan-penilaian.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Laporan Penilaian <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 7. Verifikasi Pelatih Luar --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'pelatih') && matches('pelatih luar verifikasi setujui tolak instruktur eksternal')"
                 class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-slate-300 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Verifikasi Pelatih Luar</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Pemeriksaan berkas dan persetujuan instruktur / pelatih eksternal yang mendaftar ke sekolah.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600">
                        <p class="font-bold text-slate-800">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Tinjau Berkas:</strong> Cek nama, kontak, foto, dan keahlian pelatih yang mendaftar.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Verifikasi / Setujui:</strong> Mengizinkan pelatih aktif melatih ekskul di sekolah.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Tolak Pendaftaran:</strong> Menolak pengajuan pelatih yang tidak memenuhi kriteria.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('kesiswaan.pelatih.index') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Data Pelatih <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- BAGIAN 2: PERTANYAAN YANG SERING DITANYAKAN / DI-BINGUNGKAN (EXHAUSTIVE FAQ) --}}
    <div class="space-y-4 pt-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-sky-600"></i>
                    Pertanyaan yang Sering Diajukan & Solusi Kendala
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Jawaban praktis untuk situasi dan pertanyaan operasional sekolah sehari-hari</p>
            </div>
        </div>

        <div class="space-y-3">

            {{-- FAQ 1: Reset Password --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('bagaimana cara reset password kata sandi lupa akun siswa pembina')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(1)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        1. Bagaimana cara mereset password akun siswa atau pembina yang lupa kata sandi?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 1 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 1" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <p>
                        Kesiswaan memiliki wewenang untuk mereset kata sandi akun pengguna:
                    </p>
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 font-medium">
                        <li>Buka menu <strong>Akun Pengguna</strong> (atau Data Siswa / Data Pembina).</li>
                        <li>Cari akun pengguna yang bersangkutan &rarr; klik tombol <strong>Info Akun</strong>.</li>
                        <li>Pada card profil biru bagian atas (di sebelah tombol *Edit Akun*), klik tombol <strong>Reset Password</strong>.</li>
                        <li>Klik <strong>OK</strong> pada konfirmasi dialog. Kata sandi akun tersebut akan langsung kembali ke default: <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-sky-700 font-bold">password</code>.</li>
                        <li>Siswa/pembina dapat langsung login menggunakan password tersebut dan disarankan segera menggantinya di menu Profil mereka.</li>
                    </ol>
                </div>
            </div>

            {{-- FAQ 2: Kenapa email soul.internal --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('mengapa kenapa email siswa soul internal placeholder otomatis excel import')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(2)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        2. Mengapa email siswa otomatis berakhiran <code class="font-mono text-sky-600">@soul.internal</code> setelah import Excel?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 2 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 2" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <p>
                        Format email <code class="bg-slate-100 px-1 py-0.5 rounded font-mono text-slate-800">siswa.[NIS]@soul.internal</code> adalah <strong>mekanisme pengaman (*fallback*)</strong> sistem saat import data Excel.
                    </p>
                    <p class="font-semibold text-slate-700">Penyebabnya:</p>
                    <ul class="list-disc list-inside space-y-1 pl-1">
                        <li>Kolom email di baris Excel siswa tersebut kosong, ATAU</li>
                        <li>Email yang dimasukkan duplikat (misalnya banyak baris dicopy-paste <code class="font-mono">siswa@gmail.com</code>).</li>
                    </ul>
                    <p class="mt-2 text-slate-700">
                        Karena database mewajibkan setiap akun memiliki email yang <strong>unik</strong> agar bisa dipakai login, sistem otomatis membuatkan email berbasis NIS tersebut agar proses import ratusan siswa tidak gagal/error di tengah jalan.
                    </p>
                    <p class="text-slate-700 font-semibold">
                        Solusi: Klik tombol <strong>Edit Akun</strong> pada profil siswa tersebut, lalu masukkan email asli siswa (misal: <code class="font-mono">siswa@gmail.com</code>) dan simpan.
                    </p>
                </div>
            </div>

            {{-- FAQ 3: Import Siswa Excel --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('bagaimana cara import memasukkan banyak data siswa sekaligus excel spreadsheet')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(3)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        3. Bagaimana cara memasukkan banyak data siswa baru sekaligus menggunakan Excel?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 3 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 3" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 font-medium">
                        <li>Buka menu <strong>Akun Pengguna</strong> &rarr; klik tombol <strong>Buat Akun</strong> di hero card biru.</li>
                        <li>Pilih tab <strong>Import Excel</strong> di bagian atas formulir.</li>
                        <li>Klik tombol <strong>Download Template Excel Siswa</strong> untuk mendapatkan struktur kolom yang sesuai standar sistem.</li>
                        <li>Isi data siswa: NIS (wajib 10 angka unik), Nama Lengkap, Kelas, Tanggal Lahir (YYYY-MM-DD), Jenis Kelamin (Laki-laki / Perempuan), dan Agama.</li>
                        <li>Unggah file Excel tersebut. Sistem akan memvalidasi data dan otomatis membuatkan akun login untuk seluruh siswa sekaligus.</li>
                    </ol>
                </div>
            </div>

            {{-- FAQ 4: Alur Kenaikan & Kelulusan Kelas --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akademik') && matches('bagaimana alur kenaikan kelas kelulusan tahun ajaran baru')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(4)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        4. Bagaimana alur pergantian Tahun Ajaran dan Kenaikan Kelas di awal tahun ajaran baru?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 4 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 4" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <p>
                        Di menu <strong>Tahun Ajaran</strong>, urutan langkah pergantian tahun ajaran baru adalah:
                    </p>
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 font-medium">
                        <li><strong>Buat Tahun Ajaran Baru:</strong> Masukkan format tahun (contoh: <code class="font-mono">2026/2027</code>) pada form di halaman Tahun Ajaran.</li>
                        <li><strong>Aktifkan TA Baru:</strong> Klik tombol <em>Aktifkan</em> pada tahun ajaran baru tersebut. Tahun ajaran lama otomatis beralih menjadi arsip <em>Historis</em>.</li>
                        <li><strong>Kelola Kelulusan Siswa Kelas 12:</strong> Pada kartu section kelulusan yang muncul, klik <em>Kelola Kelulusan</em>. Secara default semua siswa kelas 12 diceklis untuk diluluskan menjadi Alumni. Jika ada siswa yang tinggal kelas, cukup hapus centangnya.</li>
                        <li><strong>Penyesuaian Kenaikan Rombel:</strong> Buka kelas-kelas pada tahun ajaran aktif untuk menempatkan siswa naik dari Kelas 10 ke 11, Kelas 11 ke 12, dan memasukkan siswa baru ke Kelas 10.</li>
                    </ol>
                </div>
            </div>

            {{-- FAQ 5: Apa yang terjadi jika siswa lulus --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akademik') && matches('apa yang terjadi siswa lulus alumni nonaktif')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(5)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        5. Apa dampak pada data siswa ketika dinyatakan Lulus pada tahun ajaran?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 5 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 5" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <p>
                        Ketika proses kelulusan kelas 12 disimpan:
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 pl-1 text-slate-700">
                        <li>Status siswa otomatis berubah menjadi <strong>Alumni (Nonaktif)</strong>.</li>
                        <li>Akun login siswa dinonaktifkan sehingga siswa tidak dapat lagi login ke panel aktif.</li>
                        <li>Keanggotaan aktif siswa di seluruh ekskul ditutup dengan status riwayat selesai.</li>
                        <li>Seluruh arsip data nilai ekskul, kehadiran, dan riwayat jabatan siswa tetap tersimpan aman di database sekolah untuk keperluan buku induk dan rapor.</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 6: Batasan Jumlah Ekskul per Siswa --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('berapa batas maksimal ekskul siswa aturan kuota')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(6)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        6. Berapa batas maksimal ekskul yang boleh diikuti oleh seorang siswa?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 6 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 6" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <p>
                        Berdasarkan aturan akademik sistem:
                    </p>
                    <ul class="list-disc list-inside space-y-1 pl-1 text-slate-700">
                        <li>Siswa diperbolehkan mengikuti <strong>maksimal 2 ekskul pilihan</strong>.</li>
                        <li>Siswa wajib mengikuti <strong>1 ekskul wajib</strong> (contoh: Pramuka).</li>
                        <li>Jika kuota ekskul siswa sudah penuh, sistem akan otomatis menolak pendaftaran baru dan menampilkan peringatan bahwa siswa sudah mencapai kuota maksimal.</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 7: Cara Menetapkan Pembina dan Pelatih --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('bagaimana cara menetapkan memasangkan guru pembina pelatih ekskul')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(7)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        7. Bagaimana cara menetapkan Guru Pembina atau Pelatih pada suatu ekskul?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 7 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 7" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <p>
                        Anda dapat menetapkannya melalui 2 cara:
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 pl-1 text-slate-700">
                        <li><strong>Dari Data Ekskul:</strong> Masuk ke menu <em>Data Ekskul</em> &rarr; klik tombol <em>Edit</em> pada ekskul bersangkutan &rarr; pilih nama Pembina dan Pelatih dari dropdown &rarr; klik Simpan.</li>
                        <li><strong>Dari Data Pembina:</strong> Masuk ke menu <em>Akun Pengguna &gt; Data Pembina</em> &rarr; klik nama pembina &rarr; pilih ekskul yang ingin ditugaskan dan klik <em>Tugaskan Ekskul</em>. Seorang guru bisa membina lebih dari satu ekskul.</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 8: Verifikasi Pelatih Luar --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'pelatih') && matches('bagaimana verifikasi pelatih luar pending tolak setujui instruktur')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(8)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        8. Bagaimana cara memverifikasi atau menolak akun Pelatih luar yang baru mendaftar?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 8 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 8" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <ol class="list-decimal list-inside space-y-1 pl-1 text-slate-700 font-medium">
                        <li>Buka menu <strong>Akun Pengguna &gt; Data Pelatih</strong>.</li>
                        <li>Gunakan tab filter status <strong>Pending</strong> untuk melihat pelatih yang belum diverifikasi.</li>
                        <li>Periksa data diri, keahlian cabang, nomor HP, dan ekskul yang diajukan.</li>
                        <li>Klik tombol <strong>Verifikasi</strong> untuk menyetujui, atau <strong>Tolak</strong> jika tidak memenuhi syarat sekolah. Pelatih yang terverifikasi akan mendapatkan akses dan siap melatih ekskul.</li>
                    </ol>
                </div>
            </div>

            {{-- FAQ 9: Download PDF Laporan Nilai --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'penilaian') && matches('bagaimana cara unduh download cetak pdf laporan nilai rapor')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(9)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        9. Kapan dan bagaimana cara mengunduh laporan rekap nilai ekskul dalam format PDF?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 9 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 9" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <p>
                        Laporan PDF nilai ekskul biasanya dicetak pada akhir semester untuk lampiran buku rapor siswa:
                    </p>
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 font-medium">
                        <li>Buka menu <strong>Laporan Penilaian</strong>.</li>
                        <li>Pilih periode semester yang sesuai (misal: <em>2026/2027 Ganjil</em>).</li>
                        <li>Klik nama ekskul yang ingin dicetak laporannya.</li>
                        <li>Di halaman detail ekskul, klik tombol <strong>Download PDF</strong> di pojok kanan atas.</li>
                        <li>File PDF akan ter-download secara otomatis dengan format kop resmi sekolah, rekap presensi hadir, nilai angka akhir, predikat huruf (A, B, C, D), dan kolom tanda tangan Guru Pembina.</li>
                    </ol>
                </div>
            </div>

            {{-- FAQ 10: Pengangkatan Ketua Ekskul --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('bagaimana cara mengangkat mengganti ketua ekskul riwayat jabatan')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(10)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        10. Bagaimana alur pengangkatan dan pergantian Ketua Ekskul?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 10 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 10" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <p>
                        Ketua ekskul diangkat dari siswa yang sudah terdaftar aktif di ekskul tersebut:
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 pl-1 text-slate-700">
                        <li>Saat membuat atau mengedit akun siswa, Kesiswaan dapat memilih opsi <strong>Jabatan: Ketua</strong> dan memilih ekskul yang dipimpinnya.</li>
                        <li>Sistem secara otomatis mengaktifkan status ketua, memberikan hak akses dashboard ketua kepada siswa tersebut, dan mencatat tanggal mulai menjabat.</li>
                        <li>Bila terjadi pergantian ketua, ketua lama otomatis diarsipkan ke tab <strong>Riwayat Jabatan</strong> dengan keterangan periode dan alasan selesai (demisioner/diganti).</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 11: Nonaktifkan Akun vs Hapus Akun --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('apa bedanya nonaktifkan akun dengan hapus akun siswa')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(11)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        11. Apa perbedaan antara "Menonaktifkan Akun" dengan "Menghapus Akun"?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 11 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 11" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <ul class="list-disc list-inside space-y-1.5 pl-1 text-slate-700">
                        <li><strong>Menonaktifkan Akun (Disarankan):</strong> Akun tidak bisa login, nama siswa diberi tanda abu-abu di bagian bawah daftar, namun <em>seluruh riwayat absensi, nilai ekskul, dan catatan kelas tetap utuh</em> di database. Sangat direkomendasikan untuk siswa pindah, diskors, atau alumni.</li>
                        <li><strong>Menghapus Akun:</strong> Data akun dihapus permanen. Fitur ini hanya disarankan jika akun salah dibuat secara tidak sengaja (misal typo data input baru yang belum ada aktivitas sama sekali).</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 12: Kenapa Username Kosong --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('kenapa username siswa kosong belum diset oleh user')"
                 class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(12)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800">
                        12. Kenapa kolom Username siswa sering kosong bertuliskan "(belum diset oleh user)"?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 12 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 12" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 border-t border-slate-100 space-y-2">
                    <p>
                        Ini merupakan perilaku normal sistem:
                    </p>
                    <p class="text-slate-700">
                        Saat akun siswa baru dibuat (baik lewat form maupun import Excel), siswa belum memiliki username pribadi. Siswa melakukan login pertama kali menggunakan <strong>Email</strong> atau <strong>NIS</strong> dengan password awal <code class="font-mono bg-slate-100 px-1 py-0.5 rounded text-slate-800 font-bold">password</code>. Pada saat pertama kali login, siswa diarahkan ke layar *Onboarding* untuk membuat username unik dan password baru pilihan mereka sendiri.
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- BAGIAN 3: ALUR KERJA SIKLUS OPERASIONAL KESISWAAN (CHECKLIST) --}}
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Checklist Siklus Kerja Rutin Kesiswaan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Panduan timeline operasional sekolah dari awal semester hingga akhir tahun</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
            {{-- Awal Semester --}}
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/70 space-y-2">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-sky-100 text-sky-800 border border-sky-200 uppercase">
                    Awal Semester
                </span>
                <h4 class="text-xs font-extrabold text-slate-900 pt-1">Penerimaan & Registrasi</h4>
                <ul class="text-[11px] text-slate-600 space-y-1.5">
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Import data siswa baru ke kelas 10 via Excel.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Buka rekrutmen ekskul untuk pendaftaran anggota baru.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Verifikasi pelatih luar dan tetapkan pembina ekskul.</span></li>
                </ul>
            </div>

            {{-- Tengah Semester --}}
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/70 space-y-2">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-slate-200 text-slate-700 border border-slate-300 uppercase">
                    Tengah Semester
                </span>
                <h4 class="text-xs font-extrabold text-slate-900 pt-1">Monitoring & Pengawasan</h4>
                <ul class="text-[11px] text-slate-600 space-y-1.5">
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Pantau keaktifan kehadiran presensi mingguan tiap ekskul.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Bantu reset password bagi siswa yang lupa kata sandi.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Tinjau pengajuan keluar ekskul dari siswa jika ada.</span></li>
                </ul>
            </div>

            {{-- Akhir Semester / Akhir Tahun --}}
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/70 space-y-2">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase">
                    Akhir Semester / TA
                </span>
                <h4 class="text-xs font-extrabold text-slate-900 pt-1">Penilaian & Kenaikan</h4>
                <ul class="text-[11px] text-slate-600 space-y-1.5">
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Pantau rekap penilaian dari seluruh pembina ekskul.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Unduh dokumen PDF laporan nilai untuk rapor sekolah.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Proses kelulusan kelas 12 dan aktifkan Tahun Ajaran baru.</span></li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection
