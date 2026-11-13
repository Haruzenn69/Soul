<?php

namespace App\Support;

class MenuKesiswaan
{
    /**
     * Daftar item navigasi lengkap untuk Panel Kesiswaan,
     * termasuk menu Manajemen Pembina dan Operasional Ketua.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function items(): array
    {
        return [
            ['icon' => 'dashboard', 'label' => 'Dashboard', 'url' => route('kesiswaan.dashboard'), 'is' => 'kesiswaan.dashboard'],
            [
                'icon' => 'users',
                'label' => 'Akun Pengguna',
                'is' => ['kesiswaan.users.*', 'kesiswaan.siswa.*', 'kesiswaan.pembina.*', 'kesiswaan.pelatih.*'],
                'children' => [
                    ['icon' => 'users', 'label' => 'Akun Pengguna', 'url' => route('kesiswaan.users.index'), 'is' => ['kesiswaan.users.*']],
                    ['icon' => 'users', 'label' => 'Data Siswa', 'url' => route('kesiswaan.siswa.index'), 'is' => ['kesiswaan.siswa.*']],
                    ['icon' => 'user', 'label' => 'Data Pembina', 'url' => route('kesiswaan.pembina.index'), 'is' => ['kesiswaan.pembina.*']],
                    ['icon' => 'user-check', 'label' => 'Data Pelatih', 'url' => route('kesiswaan.pelatih.index'), 'is' => ['kesiswaan.pelatih.*']],
                ],
            ],
            ['icon' => 'building', 'label' => 'Data Ekskul', 'url' => route('kesiswaan.ekskuls.index'), 'is' => 'kesiswaan.ekskuls.*'],
            [
                'icon' => 'clipboard-check',
                'label' => 'Fitur Pembina',
                'is' => ['pembina.*'],
                'children' => [
                    ['icon' => 'user-check', 'label' => 'Pilih Ketua & Pengurus', 'url' => route('pembina.pengurus.index'), 'is' => ['pembina.pengurus.*', 'pembina.ekskul.pilih-ketua*']],
                    ['icon' => 'users', 'label' => 'Data Anggota Ekskul', 'url' => route('pembina.anggota'), 'is' => ['pembina.anggota*']],
                    ['icon' => 'calendar', 'label' => 'Presensi & Rekap', 'url' => route('pembina.presensi'), 'is' => ['pembina.presensi*', 'pembina.rekap*']],
                    ['icon' => 'document', 'label' => 'Cetak Laporan Ekskul', 'url' => route('pembina.laporan.index'), 'is' => ['pembina.laporan.*']],
                    ['icon' => 'clipboard-check', 'label' => 'Input Penilaian Ekskul', 'url' => route('pembina.penilaian'), 'is' => ['pembina.penilaian*']],
                ],
            ],
            [
                'icon' => 'calendar',
                'label' => 'Operasional Ketua',
                'is' => ['ketua.*'],
                'children' => [
                    ['icon' => 'calendar', 'label' => 'Jadwal & Presensi Kegiatan', 'url' => route('ketua.kegiatan.index'), 'is' => ['ketua.kegiatan.*', 'ketua.presensi.*']],
                    ['icon' => 'users', 'label' => 'Kelola Anggota', 'url' => route('ketua.anggota.index'), 'is' => ['ketua.anggota.*']],
                    ['icon' => 'clipboard-list', 'label' => 'Pendaftaran Anggota', 'url' => route('ketua.pendaftaran.index'), 'is' => ['ketua.pendaftaran.*']],
                    ['icon' => 'logout', 'label' => 'Pengajuan Keluar', 'url' => route('ketua.pengajuan-keluar.index'), 'is' => ['ketua.pengajuan-keluar.*']],
                    ['icon' => 'columns', 'label' => 'Draft Laporan Bulanan', 'url' => route('ketua.laporan-bulanan.index'), 'is' => ['ketua.laporan-bulanan.*']],
                    ['icon' => 'building', 'label' => 'Profil & Galeri Ekskul', 'url' => route('ketua.profil-ekskul.edit'), 'is' => ['ketua.profil-ekskul.*']],
                    ['icon' => 'star', 'label' => 'Prestasi Ekskul', 'url' => route('ketua.prestasi.index'), 'is' => ['ketua.prestasi.*']],
                ],
            ],
            ['icon' => 'document', 'label' => 'Data Kelas', 'url' => route('kesiswaan.kelas.index'), 'is' => 'kesiswaan.kelas.*'],
            ['icon' => 'calendar', 'label' => 'Tahun Ajaran', 'url' => route('kesiswaan.tahun-ajaran.index'), 'is' => 'kesiswaan.tahun-ajaran.*'],
            ['icon' => 'clipboard-check', 'label' => 'Laporan Penilaian', 'url' => route('kesiswaan.laporan-penilaian.index'), 'is' => 'kesiswaan.laporan-penilaian.*'],
            ['icon' => 'user', 'label' => 'Profile', 'url' => route('kesiswaan.profile'), 'is' => 'kesiswaan.profile'],
        ];
    }
}

