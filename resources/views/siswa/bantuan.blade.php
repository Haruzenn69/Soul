@extends('layouts.siswa')
@section('title', 'Pusat Bantuan & Panduan Siswa')

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

    <div class="-mb-4 flex justify-start relative z-10">
        <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-sky-100 text-sky-700 text-xs font-bold shadow-sm hover:bg-sky-50 transition">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            Kembali
        </a>
    </div>

    {{-- HERO CARD BIRU --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-400 via-blue-400 to-blue-600 p-6 md:p-8 text-white shadow-xl shadow-sky-200 dark:shadow-sky-900/30">
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
                        <span>Panduan Lengkap untuk Siswa</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight text-white">
                        Pusat Bantuan & Panduan Siswa
                    </h1>
                    <p class="text-xs md:text-sm text-white/85 mt-1.5 max-w-2xl leading-relaxed">
                        Temukan jawaban atas semua pertanyaan seputar fitur, alur pendaftaran, presensi, nilai, dan penggunaan platform SOUL.
                    </p>
                </div>
            </div>

            {{-- SEARCH BAR --}}
            <div class="pt-4 border-t border-white/20">
                <div class="relative max-w-xl">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-white/60 text-sm pointer-events-none"></i>
                    <input type="text"
                           x-model="search"
                           placeholder="Ketik kata kunci (contoh: daftar ekskul, presensi, nilai, profil)..."
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
                :class="activeTab === 'semua' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'bg-white dark:bg-neutral-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-700 border border-slate-200 dark:border-neutral-700'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Semua Topik
        </button>
        <button type="button"
                @click="activeTab = 'akun'"
                :class="activeTab === 'akun' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'bg-white dark:bg-neutral-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-700 border border-slate-200 dark:border-neutral-700'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Akun & Profil
        </button>
        <button type="button"
                @click="activeTab = 'ekskul'"
                :class="activeTab === 'ekskul' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'bg-white dark:bg-neutral-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-700 border border-slate-200 dark:border-neutral-700'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Ekstrakurikuler
        </button>
        <button type="button"
                @click="activeTab = 'presensi'"
                :class="activeTab === 'presensi' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'bg-white dark:bg-neutral-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-700 border border-slate-200 dark:border-neutral-700'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Presensi & Kegiatan
        </button>
        <button type="button"
                @click="activeTab = 'nilai'"
                :class="activeTab === 'nilai' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'bg-white dark:bg-neutral-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-700 border border-slate-200 dark:border-neutral-700'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Nilai & Penilaian
        </button>
        <button type="button"
                @click="activeTab = 'lainnya'"
                :class="activeTab === 'lainnya' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'bg-white dark:bg-neutral-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-neutral-700 border border-slate-200 dark:border-neutral-700'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap cursor-pointer">
            Lainnya
        </button>
    </div>

    {{-- BAGIAN 1: DIREKTORI FITUR SISWA --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-compass text-sky-600"></i>
                    Direktori Fitur & Kemampuan Siswa
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Panduan lengkap mengenai semua fitur yang dapat kamu akses di platform SOUL</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- 1. Dashboard --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('dashboard beranda ringkasan statistik kehadiran kegiatan ekskul')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 p-5 shadow-sm hover:border-slate-300 dark:hover:border-neutral-600 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-house"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Dashboard Siswa</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Halaman utama yang menampilkan ringkasan aktivitas ekskul kamu secara menyeluruh.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600 dark:text-slate-400">
                        <p class="font-bold text-slate-800 dark:text-slate-200">Yang bisa dilihat:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Statistik Kehadiran:</strong> Jumlah hadir, izin, sakit, dan alpha kamu di ekskul.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Kegiatan Mendatang:</strong> Jadwal kegiatan/latihan ekskul yang akan datang (hingga 6 kegiatan).</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Status Pendaftaran:</strong> Notifikasi jika pendaftaran kamu masih pending, ditolak, atau diterima.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Info Pengurus:</strong> Daftar pengurus ekskul (ketua, wakil ketua, sekretaris, dll).</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-neutral-700 flex items-center justify-between">
                    <a href="{{ route('siswa.dashboard') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Dashboard <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 2. Katalog Ekskul --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('katalog ekskul cari filter daftar rekrutmen komunitas')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 p-5 shadow-sm hover:border-slate-300 dark:hover:border-neutral-600 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-shapes"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Katalog Ekskul</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Jelajahi seluruh ekskul / komunitas yang tersedia di SMKN 11 Bandung.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600 dark:text-slate-400">
                        <p class="font-bold text-slate-800 dark:text-slate-200">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Cari Ekskul:</strong> Ketik nama, deskripsi, atau nama pembina di kolom pencarian.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Filter Rekrutmen:</strong> Lihat ekskul yang sedang buka atau tutup pendaftaran.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Lihat Detail:</strong> Klik ekskul untuk melihat profil lengkap, testimoni, dan FAQ ekskul.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-neutral-700 flex items-center justify-between">
                    <a href="{{ route('siswa.katalog') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Katalog <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 3. Pendaftaran Ekskul --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('daftar pendaftaran ekskul registrasi gabung ikut formulir kuota batas')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 p-5 shadow-sm hover:border-slate-300 dark:hover:border-neutral-600 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Pendaftaran Ekskul</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Daftarkan dirimu ke ekskul yang diminati melalui formulir pendaftaran online.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600 dark:text-slate-400">
                        <p class="font-bold text-slate-800 dark:text-slate-200">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Lihat Ekskul Tersedia:</strong> Hanya ekskul yang buka rekrutmen yang bisa didaftar.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Isi Formulir:</strong> Lengkapi data pendaftaran pada form khusus ekskul tujuan.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Batas Maksimal:</strong> Kamu hanya bisa mengikuti maksimal <strong>2 ekskul pilihan</strong> secara bersamaan.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Notifikasi Otomatis:</strong> Ketua ekskul akan menerima pemberitahuan pendaftaranmu.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-neutral-700 flex items-center justify-between">
                    <a href="{{ route('siswa.daftar-ekskul') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Daftar Ekskul <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 4. Presensi & Kegiatan --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'presensi') && matches('presensi kehadiran absensi hadir izin sakit alpha kegiatan materi')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 p-5 shadow-sm hover:border-slate-300 dark:hover:border-neutral-600 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Presensi & Kegiatan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Pantau seluruh riwayat kehadiran dan kegiatan ekskul yang pernah kamu ikuti.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600 dark:text-slate-400">
                        <p class="font-bold text-slate-800 dark:text-slate-200">Yang bisa dilihat:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Riwayat Kehadiran:</strong> Daftar seluruh pertemuan beserta status hadir/izin/sakit/alpha kamu.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Filter Bulan:</strong> Saring data berdasarkan bulan tertentu untuk melihat kehadiran spesifik.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Cari Materi:</strong> Cari kegiatan berdasarkan materi yang diajarkan.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Statistik:</strong> Total pertemuan dan rincian jumlah hadir, izin, sakit, alpha.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-neutral-700 flex items-center justify-between">
                    <a href="{{ route('siswa.presensi') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Presensi <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 5. Rekap Absensi --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'presensi') && matches('rekap absensi bulanan rangkuman kehadiran tabel')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 p-5 shadow-sm hover:border-slate-300 dark:hover:border-neutral-600 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-chart-bar"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Rekap Absensi Bulanan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Rekapitulasi kehadiran per bulan dalam format tabel lengkap bersama anggota lainnya.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600 dark:text-slate-400">
                        <p class="font-bold text-slate-800 dark:text-slate-200">Yang bisa dilihat:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Tabel Rekap:</strong> Tabel kehadiran seluruh anggota ekskul per bulan.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Data Kamu:</strong> Baris milik kamu ditandai (highlight) agar mudah ditemukan.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Perbandingan:</strong> Bandingkan kehadiranmu dengan anggota lain di ekskul yang sama.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-neutral-700 flex items-center justify-between">
                    <a href="{{ route('siswa.rekap') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Rekap <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 6. Nilai Ekskul --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'nilai') && matches('nilai penilaian skor predikat rapor semester pembina')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 p-5 shadow-sm hover:border-slate-300 dark:hover:border-neutral-600 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Nilai Ekskul</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Lihat hasil penilaian yang diberikan oleh Guru Pembina untuk kegiatan ekskul kamu.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600 dark:text-slate-400">
                        <p class="font-bold text-slate-800 dark:text-slate-200">Yang bisa dilihat:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Nilai Semester:</strong> Skor penilaian untuk periode semester aktif.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Status Terkirim:</strong> Nilai hanya muncul setelah pembina mengirimkan penilaian resmi.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Predikat:</strong> Predikat huruf (A, B, C, D) berdasarkan skor akhir.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-neutral-700 flex items-center justify-between">
                    <a href="{{ route('siswa.nilai') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Nilai <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 7. Profil Saya --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('profil akun edit username email telepon foto password sandi')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 p-5 shadow-sm hover:border-slate-300 dark:hover:border-neutral-600 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Profil Saya</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Kelola informasi pribadi dan keamanan akun SOUL milikmu.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600 dark:text-slate-400">
                        <p class="font-bold text-slate-800 dark:text-slate-200">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Edit Username:</strong> Ubah username unik untuk login dan tampilan profil.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Ubah Email & Telepon:</strong> Perbarui informasi kontak pribadi.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Ganti Foto Profil:</strong> Upload foto baru untuk identitas akunmu.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Ubah Password:</strong> Ganti kata sandi untuk keamanan akun.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-neutral-700 flex items-center justify-between">
                    <a href="{{ route('profile.edit') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Profil <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 8. Pengajuan Keluar Ekskul --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul' || activeTab === 'lainnya') && matches('pengajuan keluar ekskul resign berhenti alasan tinggalkan')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 p-5 shadow-sm hover:border-slate-300 dark:hover:border-neutral-600 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Pengajuan Keluar Ekskul</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Ajukan permintaan untuk keluar dari ekskul yang sedang kamu ikuti.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600 dark:text-slate-400">
                        <p class="font-bold text-slate-800 dark:text-slate-200">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Ajukan Keluar:</strong> Kirim permohonan keluar dari ekskul disertai alasan.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Lihat Riwayat:</strong> Pantau status pengajuan (pending, disetujui, ditolak).</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Satu Pengajuan:</strong> Tidak bisa mengirim pengajuan baru jika masih ada yang pending.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-neutral-700 flex items-center justify-between">
                    <a href="{{ route('siswa.pengajuan-keluar') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Pengajuan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            {{-- 9. Notifikasi --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'lainnya') && matches('notifikasi pesan pemberitahuan dibaca hapus bersihkan')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 p-5 shadow-sm hover:border-slate-300 dark:hover:border-neutral-600 transition flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-neutral-700 text-slate-700 dark:text-slate-300 flex items-center justify-center font-bold text-sm mb-3">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Notifikasi</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">
                        Terima pemberitahuan penting terkait aktivitas ekskul dan status pendaftaranmu.
                    </p>
                    <div class="mt-3 space-y-1.5 text-[11px] text-slate-600 dark:text-slate-400">
                        <p class="font-bold text-slate-800 dark:text-slate-200">Yang bisa dilakukan:</p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Lihat Notifikasi:</strong> Baca semua pemberitahuan yang masuk dari sistem.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Tandai Dibaca:</strong> Tandai satu atau semua notifikasi sebagai sudah dibaca.</span></p>
                        <p class="flex items-start gap-1.5"><i class="fa-solid fa-check text-sky-600 mt-0.5 text-[10px]"></i><span><strong>Hapus:</strong> Hapus notifikasi satu per satu atau bersihkan seluruhnya.</span></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 dark:border-neutral-700 flex items-center justify-between">
                    <a href="{{ route('siswa.notifikasi') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 inline-flex items-center gap-1">
                        Buka Notifikasi <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- BAGIAN 2: FAQ --}}
    <div class="space-y-4 pt-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-circle-question text-sky-600"></i>
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Jawaban praktis untuk situasi dan pertanyaan sehari-hari seputar penggunaan SOUL</p>
            </div>
        </div>

        <div class="space-y-3">

            {{-- FAQ 1 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('bagaimana cara login pertama kali masuk akun baru onboarding username')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(1)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        1. Bagaimana cara login pertama kali ke platform SOUL?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 1 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 1" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <p>
                        Saat akun kamu baru dibuat oleh staf Kesiswaan, kamu bisa login dengan:
                    </p>
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300 font-medium">
                        <li>Buka halaman login SOUL.</li>
                        <li>Masukkan <strong>Email</strong> atau <strong>NIS</strong> kamu sebagai identitas login.</li>
                        <li>Gunakan password awal: <code class="font-mono bg-slate-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded text-sky-700 dark:text-sky-300 font-bold">password</code>.</li>
                        <li>Setelah login pertama kali, kamu akan diarahkan ke halaman <strong>Onboarding</strong> untuk membuat <strong>username unik</strong> dan <strong>password baru</strong> pilihanmu sendiri.</li>
                        <li>Setelah selesai onboarding, kamu bisa login menggunakan username atau email.</li>
                    </ol>
                </div>
            </div>

            {{-- FAQ 2 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('lupa password kata sandi reset ganti tidak bisa login')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(2)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        2. Saya lupa password, bagaimana cara mengatasinya?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 2 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 2" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <p>Jika kamu lupa kata sandi, kamu perlu meminta bantuan staf <strong>Kesiswaan</strong> sekolah:</p>
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300 font-medium">
                        <li>Temui staf Kesiswaan atau guru BK dan minta agar password akunmu di-reset.</li>
                        <li>Staf Kesiswaan akan mereset password kamu ke default: <code class="font-mono bg-slate-100 dark:bg-neutral-700 px-1.5 py-0.5 rounded text-sky-700 dark:text-sky-300 font-bold">password</code>.</li>
                        <li>Login kembali dengan password tersebut, lalu segera ganti password baru di menu <strong>Profil Saya</strong>.</li>
                    </ol>
                    <p class="text-slate-500 dark:text-slate-400 italic">Catatan: Sistem SOUL tidak memiliki fitur "Lupa Password" mandiri karena keamanan data sekolah, sehingga reset hanya bisa dilakukan oleh Kesiswaan.</p>
                </div>
            </div>

            {{-- FAQ 3 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'akun') && matches('bagaimana cara mengganti username email foto profil ubah')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(3)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        3. Bagaimana cara mengubah username, email, atau foto profil saya?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 3 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 3" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300 font-medium">
                        <li>Klik menu <strong>Profil Saya</strong> di sidebar kiri.</li>
                        <li>Kamu akan melihat form edit dengan kolom Username, Email, No. Telepon, dan Foto Profil.</li>
                        <li>Ubah data yang diinginkan, lalu klik tombol <strong>Simpan Perubahan</strong>.</li>
                        <li>Untuk foto profil, klik area foto lalu pilih gambar baru dari perangkatmu.</li>
                    </ol>
                    <p class="text-slate-500 dark:text-slate-400 italic">
                        Catatan: Username harus unik. Data seperti NIS, Nama Lengkap, dan Kelas tidak bisa diubah oleh siswa karena dikelola oleh Kesiswaan.
                    </p>
                </div>
            </div>

            {{-- FAQ 4 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('bagaimana cara mendaftar ekskul daftar join ikut langkah')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(4)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        4. Bagaimana cara mendaftar ke ekskul yang saya minati?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 4 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 4" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300 font-medium">
                        <li>Pastikan data profil kamu sudah lengkap (terutama email dan nomor telepon).</li>
                        <li>Buka menu <strong>Daftar Ekskul</strong> melalui dropdown pengaturan di sidebar (ikon gerigi).</li>
                        <li>Pilih ekskul yang berstatus <strong>Buka Rekrutmen</strong>.</li>
                        <li>Klik tombol <strong>Daftar</strong> pada ekskul tujuan.</li>
                        <li>Isi formulir pendaftaran yang muncul, lalu klik <strong>Kirim Pendaftaran</strong>.</li>
                        <li>Pendaftaranmu akan berstatus <strong>Menunggu</strong> hingga diverifikasi oleh Ketua Ekskul.</li>
                    </ol>
                </div>
            </div>

            {{-- FAQ 5 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('berapa batas maksimal ekskul kuota pendaftaran ditolak tidak bisa daftar')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(5)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        5. Berapa maksimal ekskul yang boleh saya ikuti? Kenapa pendaftaran saya ditolak?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 5 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 5" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <p>Aturan pendaftaran ekskul di SOUL:</p>
                    <ul class="list-disc list-inside space-y-1 pl-1 text-slate-700 dark:text-slate-300">
                        <li>Setiap siswa boleh mengikuti maksimal <strong>2 ekskul pilihan</strong> secara bersamaan.</li>
                        <li>Jika kamu sudah terdaftar di 2 ekskul aktif, sistem akan <strong>otomatis menolak</strong> pendaftaran berikutnya.</li>
                        <li>Jika ingin pindah ekskul, kamu harus mengajukan <strong>keluar</strong> dari salah satu ekskul terlebih dahulu, lalu mendaftar ke ekskul baru setelah pengajuan disetujui.</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 6 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('bagaimana cara keluar ekskul resign berhenti pengajuan alasan')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(6)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        6. Bagaimana cara keluar dari ekskul yang sudah saya ikuti?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 6 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 6" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300 font-medium">
                        <li>Buka menu <strong>Ajukan Keluar</strong> melalui dropdown pengaturan di sidebar (ikon gerigi).</li>
                        <li>Pilih ekskul yang ingin kamu tinggalkan.</li>
                        <li>Tulis alasan pengajuan keluar dengan jelas.</li>
                        <li>Klik tombol <strong>Kirim Pengajuan</strong>.</li>
                        <li>Pengajuanmu akan diproses oleh pengurus ekskul. Kamu bisa memantau statusnya di halaman yang sama.</li>
                    </ol>
                    <p class="text-slate-500 dark:text-slate-400 italic">
                        Catatan: Kamu tidak bisa mengirim pengajuan keluar baru jika masih ada pengajuan yang berstatus Pending.
                    </p>
                </div>
            </div>

            {{-- FAQ 7 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'presensi') && matches('bagaimana cara melihat presensi kehadiran absensi dicatat siapa hadir alpha')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(7)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        7. Bagaimana sistem presensi bekerja? Siapa yang mencatat kehadiran saya?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 7 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 7" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <p>Sistem presensi di SOUL dikelola oleh <strong>Ketua Ekskul</strong> atau <strong>Pengurus</strong> ekskul:</p>
                    <ul class="list-disc list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300">
                        <li>Setiap kali ada kegiatan/latihan ekskul, pengurus membuat sesi presensi dengan materi dan tanggal.</li>
                        <li>Pengurus kemudian mengisi status kehadiran setiap anggota: <strong>Hadir</strong>, <strong>Izin</strong>, <strong>Sakit</strong>, atau <strong>Alpha</strong>.</li>
                        <li>Kamu bisa melihat hasil presensi di menu <strong>Presensi & Kegiatan</strong>.</li>
                        <li>Jika kamu merasa data presensi salah, hubungi Ketua Ekskul atau Guru Pembina untuk koreksi.</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 8 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'presensi') && matches('apa bedanya presensi dan rekap absensi tabel bulanan perbedaan')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(8)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        8. Apa perbedaan menu "Presensi & Kegiatan" dan "Rekap Absensi"?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 8 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 8" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <ul class="list-disc list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300">
                        <li><strong>Presensi & Kegiatan:</strong> Menampilkan daftar riwayat kehadiranmu secara detail per pertemuan/kegiatan. Kamu bisa melihat tanggal, materi, dan status kehadiran satu per satu. Cocok untuk mengecek kehadiran di kegiatan tertentu.</li>
                        <li><strong>Rekap Absensi:</strong> Menampilkan ringkasan kehadiran dalam format <strong>tabel bulanan</strong> yang mencakup seluruh anggota ekskul. Baris milik kamu akan ditandai (highlight) agar mudah ditemukan. Cocok untuk melihat gambaran besar kehadiranmu dalam satu bulan.</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 9 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'nilai') && matches('kapan nilai muncul keluar tampil kosong belum ada semester penilaian pembina')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(9)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        9. Kapan nilai ekskul saya muncul? Kenapa masih kosong?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 9 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 9" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <p>Nilai ekskul di menu <strong>Nilai</strong> akan muncul setelah memenuhi kondisi berikut:</p>
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300 font-medium">
                        <li><strong>Guru Pembina</strong> sudah menginput dan mengirimkan penilaian untuk ekskul kamu pada semester aktif.</li>
                        <li>Status penilaian harus <strong>"Terkirim"</strong> agar bisa dilihat oleh siswa.</li>
                        <li>Jika menu nilai masih kosong, artinya pembina belum menyelesaikan proses penilaian.</li>
                    </ol>
                    <p class="text-slate-500 dark:text-slate-400 italic">
                        Nilai biasanya diinput menjelang akhir semester. Jika sudah mendekati akhir semester dan nilai belum muncul, kamu bisa menanyakan langsung ke Guru Pembina.
                    </p>
                </div>
            </div>

            {{-- FAQ 10 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('apa itu switch dashboard ganti ekskul dua 2 berganti pindah tampilan')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(10)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        10. Apa itu tombol "Switch Dashboard" di header?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 10 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 10" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <p>Tombol <strong>Switch Dashboard</strong> hanya muncul jika kamu terdaftar aktif di <strong>2 ekskul</strong> sekaligus.</p>
                    <ul class="list-disc list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300">
                        <li>Fungsinya adalah untuk <strong>berpindah tampilan dashboard</strong> antar ekskul tanpa logout.</li>
                        <li>Data statistik, presensi, dan kegiatan yang ditampilkan akan berubah sesuai ekskul yang dipilih.</li>
                        <li>Jika kamu adalah <strong>Ketua</strong> di salah satu ekskul, tombol ini juga bisa mengarahkanmu ke <strong>Dashboard Ketua</strong> dengan fitur tambahan.</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 11 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'ekskul') && matches('bagaimana cara memberikan testimoni ulasan pendapat ekskul faq tanya')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(11)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        11. Bagaimana cara memberikan testimoni atau bertanya di halaman ekskul?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 11 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 11" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <ol class="list-decimal list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300 font-medium">
                        <li>Buka <strong>Katalog Ekskul</strong> lalu klik nama ekskul yang ingin kamu tulis testimoni/pertanyaan.</li>
                        <li>Di halaman detail ekskul, scroll ke bagian bawah.</li>
                        <li>Untuk <strong>Testimoni:</strong> Isi kolom testimoni dengan pengalaman dan kesan kamu, lalu klik Kirim.</li>
                        <li>Untuk <strong>FAQ:</strong> Tulis pertanyaan kamu di form FAQ, lalu kirim. Pertanyaanmu akan dijawab oleh pengurus ekskul.</li>
                    </ol>
                    <p class="text-slate-500 dark:text-slate-400 italic">
                        Catatan: Fitur testimoni dan FAQ dibatasi frekuensinya untuk mencegah spam.
                    </p>
                </div>
            </div>

            {{-- FAQ 12 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'lainnya') && matches('apa itu onboarding setup awal pertama kali login pengaturan awal')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(12)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        12. Apa itu halaman Onboarding yang muncul saat pertama kali login?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 12 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 12" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <p>
                        <strong>Onboarding</strong> adalah proses pengaturan awal akun yang muncul saat kamu pertama kali login ke SOUL. Tujuannya:
                    </p>
                    <ul class="list-disc list-inside space-y-1 pl-1 text-slate-700 dark:text-slate-300">
                        <li>Membuat <strong>username unik</strong> sebagai identitas personalmu di platform.</li>
                        <li>Mengganti <strong>password default</strong> (<code class="font-mono bg-slate-100 dark:bg-neutral-700 px-1 py-0.5 rounded text-slate-800 dark:text-slate-200 font-bold">password</code>) menjadi password pribadi yang lebih aman.</li>
                        <li>Setelah onboarding selesai, kamu bisa langsung masuk ke Dashboard Siswa.</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 13 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'lainnya') && matches('pendaftaran menunggu pending lama status belum diterima diproses')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(13)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        13. Pendaftaran ekskul saya sudah lama berstatus "Menunggu". Apa yang harus saya lakukan?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 13 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 13" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <p>Status "Menunggu" berarti pendaftaranmu sudah masuk dan sedang menunggu verifikasi dari <strong>Ketua Ekskul</strong>. Yang bisa kamu lakukan:</p>
                    <ul class="list-disc list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300">
                        <li>Cek halaman <strong>Dashboard</strong> untuk melihat informasi status pendaftaran terkini.</li>
                        <li>Hubungi <strong>Ketua Ekskul</strong> atau <strong>Guru Pembina</strong> secara langsung untuk menanyakan proses verifikasi.</li>
                        <li>Bersabar menunggu, karena proses verifikasi memerlukan pengecekan oleh pengurus ekskul.</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 14 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'lainnya') && matches('kenapa tidak bisa mengakses fitur halaman kosong error nonaktif keluar')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(14)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        14. Kenapa beberapa fitur tidak bisa saya akses atau halamannya kosong?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 14 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 14" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <p>Beberapa kemungkinan penyebab fitur tidak bisa diakses:</p>
                    <ul class="list-disc list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300">
                        <li><strong>Belum terdaftar di ekskul:</strong> Fitur Presensi, Rekap Absensi, dan Nilai hanya tersedia jika kamu sudah terdaftar aktif di minimal satu ekskul.</li>
                        <li><strong>Status Nonaktif:</strong> Jika akunmu dinonaktifkan oleh Kesiswaan (misalnya karena pindah sekolah atau lulus), kamu tidak bisa mengakses fitur apapun.</li>
                        <li><strong>Ekskul tidak aktif:</strong> Jika ekskul yang kamu ikuti sedang dalam status nonaktif, beberapa data mungkin tidak tampil.</li>
                        <li><strong>Profil belum lengkap:</strong> Beberapa fitur (seperti Daftar Ekskul) memerlukan profil yang sudah dilengkapi terlebih dahulu.</li>
                    </ul>
                </div>
            </div>

            {{-- FAQ 15 --}}
            <div x-show="(activeTab === 'semua' || activeTab === 'lainnya') && matches('bagaimana cara mencari ekskul search pencarian global cari')"
                 class="bg-white dark:bg-neutral-800 rounded-2xl border border-slate-200 dark:border-neutral-700 overflow-hidden shadow-sm transition">
                <button type="button"
                        @click="toggleFaq(15)"
                        class="w-full px-5 py-4 flex items-center justify-between text-left hover:bg-slate-50 dark:hover:bg-neutral-700 transition cursor-pointer select-none">
                    <span class="text-xs md:text-sm font-extrabold text-slate-800 dark:text-white">
                        15. Bagaimana cara menggunakan fitur pencarian di SOUL?
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"
                       :class="openFaq === 15 ? 'rotate-180 text-sky-600' : ''"></i>
                </button>
                <div x-show="openFaq === 15" x-collapse class="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-neutral-700 space-y-2">
                    <p>SOUL memiliki fitur pencarian yang bisa kamu gunakan dari header atas:</p>
                    <ul class="list-disc list-inside space-y-1.5 pl-1 text-slate-700 dark:text-slate-300">
                        <li>Kolom pencarian di <strong>header</strong> akan mencari ekskul berdasarkan nama, deskripsi, atau pembina.</li>
                        <li>Saat mengetik, akan muncul <strong>saran otomatis (autocomplete)</strong> yang langsung menampilkan hasil relevan.</li>
                        <li>Klik pada hasil saran untuk langsung menuju halaman ekskul tersebut.</li>
                        <li>Tekan Enter untuk melihat semua hasil pencarian di halaman Katalog Ekskul.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    {{-- BAGIAN 3: TIPS PENGGUNAAN --}}
    <div class="bg-white dark:bg-neutral-800 rounded-3xl border border-slate-200 dark:border-neutral-700 p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-3 pb-3 border-b border-slate-100 dark:border-neutral-700">
            <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/30 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-sm">
                <i class="fa-solid fa-lightbulb"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Tips & Panduan Cepat</h3>
                <p class="text-xs text-slate-400 mt-0.5">Hal-hal penting yang perlu kamu ketahui sebagai siswa</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
            {{-- Tip 1 --}}
            <div class="p-4 bg-slate-50 dark:bg-neutral-900/50 rounded-2xl border border-slate-200/70 dark:border-neutral-700 space-y-2">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-sky-100 dark:bg-sky-950/50 text-sky-800 dark:text-sky-300 border border-sky-200 dark:border-sky-800 uppercase">
                    Keamanan Akun
                </span>
                <h4 class="text-xs font-extrabold text-slate-900 dark:text-white pt-1">Jaga Keamanan Akunmu</h4>
                <ul class="text-[11px] text-slate-600 dark:text-slate-400 space-y-1.5">
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Segera ganti password default saat pertama kali login.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Jangan bagikan username dan password kamu ke orang lain.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-sky-600 mt-0.5 text-[10px]"></i><span>Gunakan password yang kuat dan mudah kamu ingat.</span></li>
                </ul>
            </div>

            {{-- Tip 2 --}}
            <div class="p-4 bg-slate-50 dark:bg-neutral-900/50 rounded-2xl border border-slate-200/70 dark:border-neutral-700 space-y-2">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 uppercase">
                    Pendaftaran
                </span>
                <h4 class="text-xs font-extrabold text-slate-900 dark:text-white pt-1">Siapkan Diri Sebelum Daftar</h4>
                <ul class="text-[11px] text-slate-600 dark:text-slate-400 space-y-1.5">
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-[10px]"></i><span>Lengkapi profil (email & telepon) sebelum mendaftar ekskul.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-[10px]"></i><span>Perhatikan batas maksimal 2 ekskul pilihan per siswa.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-[10px]"></i><span>Cek halaman Katalog untuk melihat info ekskul sebelum mendaftar.</span></li>
                </ul>
            </div>

            {{-- Tip 3 --}}
            <div class="p-4 bg-slate-50 dark:bg-neutral-900/50 rounded-2xl border border-slate-200/70 dark:border-neutral-700 space-y-2">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-amber-100 dark:bg-amber-950/50 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 uppercase">
                    Rutinitas
                </span>
                <h4 class="text-xs font-extrabold text-slate-900 dark:text-white pt-1">Pantau Aktivitasmu Secara Rutin</h4>
                <ul class="text-[11px] text-slate-600 dark:text-slate-400 space-y-1.5">
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-amber-600 mt-0.5 text-[10px]"></i><span>Cek Dashboard secara berkala untuk melihat kegiatan mendatang.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-amber-600 mt-0.5 text-[10px]"></i><span>Periksa notifikasi agar tidak ketinggalan informasi penting.</span></li>
                    <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-amber-600 mt-0.5 text-[10px]"></i><span>Pantau nilai di akhir semester untuk memastikan penilaian sudah masuk.</span></li>
                </ul>
            </div>
        </div>
    </div>

</div>
@endsection

