<?php

namespace App\Services;

use App\Models\Ekskul;
use App\Models\Pendaftaran;
use App\Models\RiwayatJabatan;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Sumber bersama pencatatan periode jabatan dan penyusunan data arsip
 * ekskul (ketua sebelumnya + anggota nonaktif) untuk pembina dan ketua.
 *
 * `siswa.jabatan` hanya menyimpan jabatan saat ini, sehingga begitu ketua
 * digantikan atau dicopot jejaknya hilang. Tabel `riwayat_jabatans`
 * menyimpan satu baris per periode jabatan agar arsip tetap bisa ditampilkan.
 */
class ArsipEkskulService
{
    /**
     * Tutup periode ketua yang masih berjalan pada ekskul ini.
     *
     * @return RiwayatJabatan|null baris yang ditutup, null bila tidak ada periode aktif
     */
    public function tutupPeriodeKetua(Ekskul $ekskul, string $alasan, ?Carbon $tanggal = null): ?RiwayatJabatan
    {
        $periode = $ekskul->periodeKetuaAktif();

        if (! $periode) {
            return null;
        }

        $periode->update([
            'selesai' => $tanggal ?? now()->toDateString(),
            'alasan_selesai' => $alasan,
        ]);

        return $periode->refresh();
    }

    /**
     * Angkat siswa menjadi ketua ekskul: periode lama ditutup, periode baru dibuka.
     *
     * Pemanggil tetap wajib mengesahkan `siswa.jabatan = 'ketua'` agar keduanya sinkron.
     */
    public function mulaiPeriodeKetua(Ekskul $ekskul, Siswa $siswa, ?Carbon $mulai = null): RiwayatJabatan
    {
        $this->tutupPeriodeKetua($ekskul, RiwayatJabatan::ALASAN_DIGANTI, $mulai);

        return RiwayatJabatan::create([
            'siswa_id' => $siswa->id,
            'ekskul_id' => $ekskul->id,
            'jabatan' => RiwayatJabatan::JABATAN_KETUA,
            'mulai' => ($mulai ?? now())->toDateString(),
            'selesai' => null,
        ]);
    }

    /**
     * Copot jabatan ketua ekskul dan tutup periodenya sebagai arsip.
     */
    public function akhiriPeriodeKetua(Ekskul $ekskul, Siswa $siswa, ?Carbon $tanggal = null, ?string $alasan = null): ?RiwayatJabatan
    {
        $periode = $ekskul->periodeKetuaAktif();

        if ($periode && $periode->siswa_id !== $siswa->id) {
            $periode = null;
        }

        if (! $periode) {
            return null;
        }

        $periode->update([
            'selesai' => ($tanggal ?? now())->toDateString(),
            'alasan_selesai' => $alasan ?? RiwayatJabatan::ALASAN_DICOPOT,
        ]);

        return $periode->refresh();
    }

    /**
     * Periodenya masih berjalan?
     */
    public function periodeAktif(Ekskul $ekskul, Siswa $siswa): ?RiwayatJabatan
    {
        return RiwayatJabatan::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $siswa->id)
            ->where('jabatan', RiwayatJabatan::JABATAN_KETUA)
            ->whereNull('selesai')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Jumlah data arsip tanpa memuat detailnya, dipakai untuk label filter.
     *
     * @param  Collection<int, Ekskul>|Ekskul  $ekskuls
     */
    public function hitungTotal(Collection|Ekskul $ekskuls): int
    {
        $ekskuls = $ekskuls instanceof Ekskul ? collect([$ekskuls]) : $ekskuls;
        $ekskulIds = $ekskuls->pluck('id');

        if ($ekskulIds->isEmpty()) {
            return 0;
        }

        $ketuaSelesai = RiwayatJabatan::whereIn('ekskul_id', $ekskulIds)
            ->where('jabatan', RiwayatJabatan::JABATAN_KETUA)
            ->whereNotNull('selesai')
            ->count();

        $anggotaNonaktif = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->whereIn('status', [Pendaftaran::STATUS_NONAKTIF, Pendaftaran::STATUS_KELUAR])
            ->count();

        return $ketuaSelesai + $anggotaNonaktif;
    }

    /**
     * Susun data arsip untuk satu ekskul atau sekumpulan ekskul.
     *
     * `ketuaSelesai` = periode ketua yang sudah berakhir,
     * `anggotaNonaktif` = anggota berstatus nonaktif / keluar.
     *
     * @param  Collection<int, Ekskul>|Ekskul  $ekskuls
     * @return array{
     *     ketuaSelesai: Collection<int, RiwayatJabatan>,
     *     anggotaNonaktif: Collection<int, Pendaftaran>,
     *     total: int
     * }
     */
    public function arsip(Collection|Ekskul $ekskuls): array
    {
        $ekskuls = $ekskuls instanceof Ekskul ? collect([$ekskuls]) : $ekskuls;
        $ekskulIds = $ekskuls->pluck('id');

        $ketuaSelesai = RiwayatJabatan::whereIn('ekskul_id', $ekskulIds)
            ->where('jabatan', RiwayatJabatan::JABATAN_KETUA)
            ->whereNotNull('selesai')
            ->with(['siswa.kelas', 'ekskul'])
            ->orderByDesc('selesai')
            ->orderByDesc('id')
            ->get();

        $anggotaNonaktif = Pendaftaran::whereIn('ekskul_id', $ekskulIds)
            ->whereIn('status', [Pendaftaran::STATUS_NONAKTIF, Pendaftaran::STATUS_KELUAR])
            ->with(['siswa.kelas', 'ekskul'])
            ->orderByDesc('tanggal_daftar')
            ->get();

        return [
            'ketuaSelesai' => $ketuaSelesai,
            'anggotaNonaktif' => $anggotaNonaktif,
            'total' => $ketuaSelesai->count() + $anggotaNonaktif->count(),
        ];
    }
}
