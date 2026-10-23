<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Menggabungkan pendaftaran ganda (satu siswa di satu ekskul) menjadi satu
 * baris utama. Dipanggil dari migration sebelum unique index dipasang.
 *
 * Baris utama diprioritaskan berdasarkan riwayat terbanyak: presensi dulu,
 * lalu penilaian, lalu pendaftaran paling lama, lalu id terkecil. Presensi,
 * penilaian, dan notifikasi dari baris duplikat dipindahkan ke baris utama
 * sebelum barisnya dihapus (semua FK-nya cascade).
 */
class PendaftaranDedupService
{
    public function run(): void
    {
        $groups = DB::table('pendaftarans')
            ->select('siswa_id', 'ekskul_id')
            ->groupBy('siswa_id', 'ekskul_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($groups as $group) {
            $rows = DB::table('pendaftarans')
                ->where('siswa_id', $group->siswa_id)
                ->where('ekskul_id', $group->ekskul_id)
                ->orderBy('id')
                ->get();

            $main = $this->pickMain($rows);

            foreach ($rows as $row) {
                if ($row->id !== $main->id) {
                    $this->mergeInto($main->id, $row->id);
                }
            }
        }
    }

    private function pickMain($rows)
    {
        return $rows
            ->map(fn ($row) => [
                'row' => $row,
                'presensi' => DB::table('presensis')->where('pendaftaran_id', $row->id)->count(),
                'penilaian' => DB::table('penilaians')->where('pendaftaran_id', $row->id)->count(),
            ])
            ->sort(function (array $a, array $b) {
                return $b['presensi'] <=> $a['presensi']
                    ?: $b['penilaian'] <=> $a['penilaian']
                    ?: strcmp((string) $a['row']->tanggal_daftar, (string) $b['row']->tanggal_daftar)
                    ?: $a['row']->id <=> $b['row']->id;
            })
            ->first()['row'];
    }

    private function mergeInto(int $mainId, int $dupId): void
    {
        $this->pindahPresensi($mainId, $dupId);
        $this->pindahPenilaian($mainId, $dupId);

        DB::table('notifikasis')
            ->where('pendaftaran_id', $dupId)
            ->update(['pendaftaran_id' => $mainId]);

        DB::table('pendaftarans')->where('id', $dupId)->delete();
    }

    /**
     * presensis punya unique (kegiatan_id, pendaftaran_id): kegiatan yang
     * sudah tercatat di baris utama tidak boleh dipindahkan (bentrok) —
     * baris duplikatnya dihapus, sisanya dipindahkan.
     */
    private function pindahPresensi(int $mainId, int $dupId): void
    {
        $kegiatanUtama = DB::table('presensis')
            ->where('pendaftaran_id', $mainId)
            ->pluck('kegiatan_id');

        foreach (DB::table('presensis')->where('pendaftaran_id', $dupId)->get() as $row) {
            if ($kegiatanUtama->contains($row->kegiatan_id)) {
                DB::table('presensis')->where('id', $row->id)->delete();
            } else {
                DB::table('presensis')->where('id', $row->id)->update(['pendaftaran_id' => $mainId]);
            }
        }
    }

    /**
     * penilaians tidak punya unique, tapi satu periode hanya boleh satu
     * catatan per baris utama — periode yang sudah ada di baris utama
     * tidak dipindahkan (yang di baris utama menang).
     */
    private function pindahPenilaian(int $mainId, int $dupId): void
    {
        $periodeUtama = DB::table('penilaians')
            ->where('pendaftaran_id', $mainId)
            ->pluck('periode');

        foreach (DB::table('penilaians')->where('pendaftaran_id', $dupId)->get() as $row) {
            if ($periodeUtama->contains($row->periode)) {
                DB::table('penilaians')->where('id', $row->id)->delete();
            } else {
                DB::table('penilaians')->where('id', $row->id)->update(['pendaftaran_id' => $mainId]);
            }
        }
    }
}
