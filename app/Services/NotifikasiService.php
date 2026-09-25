<?php

namespace App\Services;

use App\Models\Ekskul;
use App\Models\Faq;
use App\Models\Kegiatan;
use App\Models\LaporanBulanan;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\PengajuanKeluar;
use App\Models\Testimoni;
use App\Models\User;
use Carbon\Carbon;

/**
 * Satu-satunya pintu pembuatan notifikasi agar pesan & target konsisten.
 */
class NotifikasiService
{
    public static function pendaftaranMasuk(Pendaftaran $pendaftaran): void
    {
        $siswa = $pendaftaran->siswa;
        $ekskul = $pendaftaran->ekskul;
        $asal = "{$siswa->nama} dari ".($siswa->kelas->nama ?? '-');

        Notifikasi::create([
            'siswa_id' => $siswa->id,
            'pendaftaran_id' => $pendaftaran->id,
            'judul' => 'Pendaftaran Terkirim',
            'pesan' => 'Pendaftaran kamu ke ekskul telah terkirim kepada ketua ekskul. Menunggu konfirmasi dari ketua ekskul.',
            'tipe' => 'info',
        ]);

        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'pendaftaran_id' => $pendaftaran->id,
                'judul' => 'Pendaftaran Baru Masuk',
                'pesan' => "{$asal} baru saja mendaftar ke ekskul {$ekskul->nama_ekskul}.",
                'tipe' => 'info',
            ]);
        }

        $ketua = $ekskul->ketua();
        if ($ketua) {
            Notifikasi::create([
                'siswa_id' => $ketua->id,
                'pendaftaran_id' => $pendaftaran->id,
                'judul' => 'Pendaftaran Baru Masuk',
                'pesan' => "{$asal} baru saja mendaftar ke ekskul {$ekskul->nama_ekskul}. Silakan verifikasi.",
                'tipe' => 'info',
            ]);
        }

        self::untukKesiswaan([
            'pendaftaran_id' => $pendaftaran->id,
            'judul' => 'Pendaftaran Baru Masuk',
            'pesan' => "{$asal} baru saja mendaftar ke ekskul {$ekskul->nama_ekskul}.",
            'tipe' => 'info',
        ]);
    }

    public static function pengajuanKeluarMasuk(PengajuanKeluar $pengajuan): void
    {
        $siswa = $pengajuan->siswa;
        $ekskul = $pengajuan->ekskul;
        $namaEkskul = $ekskul->nama_ekskul;

        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'judul' => 'Pengajuan Keluar Masuk',
                'pesan' => "{$siswa->nama} mengajukan keluar dari ekskul {$namaEkskul}. Menunggu keputusan ketua ekskul.",
                'tipe' => 'info',
            ]);
        }

        $ketua = $ekskul->ketua();
        if ($ketua) {
            Notifikasi::create([
                'siswa_id' => $ketua->id,
                'pengajuan_keluar_id' => $pengajuan->id,
                'judul' => 'Pengajuan Keluar Masuk',
                'pesan' => "{$siswa->nama} mengajukan keluar dari ekskul {$namaEkskul}. Silakan keputusan.",
                'tipe' => 'info',
            ]);
        }

        self::untukKesiswaan([
            'pengajuan_keluar_id' => $pengajuan->id,
            'judul' => 'Pengajuan Keluar Masuk',
            'pesan' => "{$siswa->nama} mengajukan keluar dari ekskul {$namaEkskul}.",
            'tipe' => 'info',
        ]);
    }

    public static function pendaftaranDiterima(Pendaftaran $pendaftaran): void
    {
        $ekskul = $pendaftaran->ekskul;

        Notifikasi::create([
            'siswa_id' => $pendaftaran->siswa_id,
            'pendaftaran_id' => $pendaftaran->id,
            'judul' => 'Pendaftaran Diterima',
            'pesan' => 'Selamat! Pendaftaran kamu ke ekskul '.$ekskul->nama_ekskul.' telah diterima oleh ketua ekskul.',
            'tipe' => 'diterima',
        ]);

        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'pendaftaran_id' => $pendaftaran->id,
                'judul' => 'Anggota Baru Diterima',
                'pesan' => $pendaftaran->siswa->nama.' telah diterima menjadi anggota ekskul '.$ekskul->nama_ekskul.'.',
                'tipe' => 'diterima',
            ]);
        }
    }

    public static function pendaftaranDitolak(Pendaftaran $pendaftaran): void
    {
        $ekskul = $pendaftaran->ekskul;

        Notifikasi::create([
            'siswa_id' => $pendaftaran->siswa_id,
            'pendaftaran_id' => $pendaftaran->id,
            'judul' => 'Pendaftaran Ditolak',
            'pesan' => 'Maaf, pendaftaran kamu ke ekskul '.$ekskul->nama_ekskul.' ditolak oleh ketua ekskul. Kamu dapat mencoba mendaftar ke ekskul lain.',
            'tipe' => 'ditolak',
        ]);

        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'pendaftaran_id' => $pendaftaran->id,
                'judul' => 'Pendaftaran Ditolak',
                'pesan' => 'Pendaftaran '.$pendaftaran->siswa->nama.' ke ekskul '.$ekskul->nama_ekskul.' ditolak oleh ketua ekskul.',
                'tipe' => 'ditolak',
            ]);
        }
    }

    public static function pengajuanKeluarDiterima(PengajuanKeluar $pengajuan): void
    {
        $ekskul = $pengajuan->ekskul;

        Notifikasi::create([
            'siswa_id' => $pengajuan->siswa_id,
            'pengajuan_keluar_id' => $pengajuan->id,
            'judul' => 'Pengajuan Keluar Diterima',
            'pesan' => 'Pengajuan keluar kamu dari ekskul '.$ekskul->nama_ekskul.' telah disetujui oleh ketua ekskul.',
            'tipe' => 'diterima',
        ]);

        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'pengajuan_keluar_id' => $pengajuan->id,
                'judul' => 'Anggota Keluar Diterima',
                'pesan' => $pengajuan->siswa->nama.' telah keluar dari ekskul '.$ekskul->nama_ekskul.' sesuai persetujuan ketua ekskul.',
                'tipe' => 'info',
            ]);
        }
    }

    public static function pengajuanKeluarDitolak(PengajuanKeluar $pengajuan): void
    {
        $ekskul = $pengajuan->ekskul;

        Notifikasi::create([
            'siswa_id' => $pengajuan->siswa_id,
            'pengajuan_keluar_id' => $pengajuan->id,
            'judul' => 'Pengajuan Keluar Ditolak',
            'pesan' => 'Permohonan keluar kamu dari ekskul '.$ekskul->nama_ekskul.' ditolak oleh ketua ekskul. Kamu tetap terdaftar sebagai anggota.',
            'tipe' => 'ditolak',
        ]);

        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'pengajuan_keluar_id' => $pengajuan->id,
                'judul' => 'Pengajuan Keluar Ditolak',
                'pesan' => 'Pengajuan keluar '.$pengajuan->siswa->nama.' dari ekskul '.$ekskul->nama_ekskul.' ditolak oleh ketua ekskul.',
                'tipe' => 'info',
            ]);
        }
    }

    public static function statusAnggotaDiubah(Pendaftaran $pendaftaran, string $status, string $judul, string $pesan, string $tipe): void
    {
        Notifikasi::create([
            'siswa_id' => $pendaftaran->siswa_id,
            'pendaftaran_id' => $pendaftaran->id,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
        ]);
    }

    public static function kegiatanDibuat(Kegiatan $kegiatan): void
    {
        $ekskul = $kegiatan->ekskul;
        $tanggalLabel = now()->isoFormat('dddd, DD MMM Y');

        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'judul' => 'Kegiatan Mendatang',
                'pesan' => 'Kegiatan baru "'.$kegiatan->materi.'" dijadwalkan pada '.$tanggalLabel.' untuk ekskul '.$ekskul->nama_ekskul.'.',
                'tipe' => 'info',
            ]);
        }

        $ekskul->pendaftarans()
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->get()
            ->each(function (Pendaftaran $anggota) use ($kegiatan, $ekskul, $tanggalLabel) {
                Notifikasi::create([
                    'siswa_id' => $anggota->siswa_id,
                    'judul' => 'Kegiatan Mendatang',
                    'pesan' => 'Ada kegiatan "'.$kegiatan->materi.'" di ekskul '.$ekskul->nama_ekskul.' pada '.$tanggalLabel.'. Jangan lupa hadir!',
                    'tipe' => 'info',
                ]);
            });
    }

    public static function laporanDiserahkan(LaporanBulanan $laporan): void
    {
        $ekskul = $laporan->ekskul;

        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'laporan_bulanan_id' => $laporan->id,
                'judul' => 'Laporan Bulanan Diserahkan',
                'pesan' => 'Ketua ekskul menyerahkan laporan bulanan untuk periode '.self::bulanLabel($laporan->bulan).' ekskul '.$ekskul->nama_ekskul.'. Silakan tinjau.',
                'tipe' => 'info',
            ]);
        }
    }

    public static function laporanDitinjau(LaporanBulanan $laporan, string $status): void
    {
        $ketua = $laporan->ekskul->ketua();

        if (! $ketua) {
            return;
        }

        $disetujui = $status === LaporanBulanan::STATUS_DISETUJUI;

        Notifikasi::create([
            'siswa_id' => $ketua->id,
            'laporan_bulanan_id' => $laporan->id,
            'judul' => $disetujui ? 'Laporan Disetujui' : 'Laporan Ditolak',
            'pesan' => 'Laporan bulanan periode '.self::bulanLabel($laporan->bulan).' ekskul '.$laporan->ekskul->nama_ekskul.' telah ditinjau oleh pembina.',
            'tipe' => $disetujui ? 'diterima' : 'ditolak',
        ]);
    }

    public static function penilaianDikirim(Ekskul $ekskul): void
    {
        self::untukKesiswaan([
            'judul' => 'Laporan Penilaian Dikumpulkan',
            'pesan' => 'Laporan penilaian ekstrakurikuler '.$ekskul->nama_ekskul.' sudah dikumpulkan, cek di laporan penilaian.',
            'tipe' => 'info',
        ]);
    }

    public static function testimoniDiajukan(Testimoni $testimoni): void
    {
        $ketua = $testimoni->ekskul->ketua();

        if (! $ketua) {
            return;
        }

        Notifikasi::create([
            'siswa_id' => $ketua->id,
            'judul' => 'Testimoni Baru Masuk',
            'pesan' => "{$testimoni->nama} mengirim testimoni untuk ekskul {$testimoni->ekskul->nama_ekskul}. Silakan moderasi.",
            'tipe' => 'info',
        ]);
    }

    public static function testimoniDipublish(Testimoni $testimoni): void
    {
        $siswa = $testimoni->user?->siswa;

        if (! $siswa) {
            return;
        }

        Notifikasi::create([
            'siswa_id' => $siswa->id,
            'judul' => 'Testimoni Diterbitkan',
            'pesan' => 'Testimoni kamu untuk ekskul '.$testimoni->ekskul->nama_ekskul.' telah disetujui dan tampil di katalog.',
            'tipe' => 'diterima',
        ]);
    }

    public static function faqDiajukan(Faq $faq): void
    {
        $ketua = $faq->ekskul->ketua();

        if (! $ketua) {
            return;
        }

        Notifikasi::create([
            'siswa_id' => $ketua->id,
            'judul' => 'Pertanyaan Baru dari Siswa',
            'pesan' => 'Ada pertanyaan baru untuk ekskul '.$faq->ekskul->nama_ekskul.': "'.$faq->pertanyaan.'". Silakan tulis jawabannya.',
            'tipe' => 'info',
        ]);
    }

    public static function faqTerjawab(Faq $faq): void
    {
        $siswa = $faq->user?->siswa;

        if (! $siswa) {
            return;
        }

        Notifikasi::create([
            'siswa_id' => $siswa->id,
            'judul' => 'Pertanyaan Kamu Terjawab',
            'pesan' => 'Pertanyaan kamu untuk ekskul '.$faq->ekskul->nama_ekskul.' sudah dijawab dan tampil di halaman katalog.',
            'tipe' => 'diterima',
        ]);
    }

    private static function untukKesiswaan(array $data): void
    {
        User::whereIn('role', ['kesiswaan', 'admin'])->each(function (User $user) use ($data) {
            Notifikasi::create($data + ['user_id' => $user->id]);
        });
    }

    private static function bulanLabel(string $bulan): string
    {
        return Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y');
    }
}
