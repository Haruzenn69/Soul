<?php

namespace Database\Seeders;

use App\Models\Ekskul;
use App\Models\Kegiatan;
use App\Models\Pengurus;
use App\Models\Pendaftaran;
use App\Models\RiwayatJabatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubdivisionSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $subdivisis = Ekskul::query()
            ->whereNotNull('parent_id')
            ->with('parent')
            ->orderBy('parent_id')
            ->orderBy('nama_ekskul')
            ->get();

        $ketuaDitambahkan = 0;
        $ketuaSudahAda = 0;
        $subdivisiTanpaKetua = 0;
        $kegiatanDitambahkan = 0;
        $ringkasan = [];

        DB::transaction(function () use ($subdivisis, &$ketuaDitambahkan, &$ketuaSudahAda, &$subdivisiTanpaKetua, &$kegiatanDitambahkan, &$ringkasan) {
            foreach ($subdivisis as $subdivisi) {
                $parent = $subdivisi->parent;
                if (! $parent) {
                    continue;
                }

                $ketua = Pengurus::query()
                    ->where('ekskul_id', $parent->id)
                    ->where('subdivisi_id', $subdivisi->id)
                    ->where('jabatan', Pengurus::JABATAN_WAKIL_KETUA)
                    ->where('status', Pengurus::STATUS_AKTIF)
                    ->first();

                if (! $ketua) {
                    $calon = Pendaftaran::query()
                        ->where('ekskul_id', $parent->id)
                        ->where('status', Pendaftaran::STATUS_DITERIMA)
                        ->when($parent->ketua(), fn ($query, $ketuaPusat) => $query->where('siswa_id', '!=', $ketuaPusat->id))
                        ->whereHas('siswa', fn ($query) => $query
                            ->where('status', 'aktif')
                            ->whereHas('user', fn ($userQuery) => $userQuery->where('is_active', true)))
                        ->whereDoesntHave('siswa.penguruses', fn ($query) => $query
                            ->where('ekskul_id', $parent->id)
                            ->whereIn('status', [Pengurus::STATUS_AKTIF, Pengurus::STATUS_MENUNGGU]))
                        ->orderBy('siswa_id')
                        ->first();

                    if ($calon) {
                        $ketua = Pengurus::create([
                            'ekskul_id' => $parent->id,
                            'subdivisi_id' => $subdivisi->id,
                            'siswa_id' => $calon->siswa_id,
                            'jabatan' => Pengurus::JABATAN_WAKIL_KETUA,
                            'status' => Pengurus::STATUS_AKTIF,
                            'diusulkan_oleh' => 'pembina',
                            'catatan' => 'Data contoh ketua sub divisi.',
                        ]);
                        $ketuaDitambahkan++;
                    } else {
                        $subdivisiTanpaKetua++;
                    }
                } else {
                    $ketuaSudahAda++;
                }

                $namaSubdivisi = Str::limit($subdivisi->nama_ekskul, 150, '');
                foreach ([
                    ['materi' => "Latihan rutin - {$namaSubdivisi}", 'hari' => 14],
                    ['materi' => "Evaluasi kegiatan - {$namaSubdivisi}", 'hari' => 7],
                ] as $data) {
                    $kegiatan = Kegiatan::firstOrCreate(
                        ['ekskul_id' => $subdivisi->id, 'materi' => $data['materi']],
                        [
                            'tanggal_kegiatan' => Carbon::today()->subDays($data['hari'])->toDateString(),
                            'deskripsi' => "Kegiatan contoh untuk sub divisi {$subdivisi->nama_ekskul}.",
                        ]
                    );

                    if ($kegiatan->wasRecentlyCreated) {
                        $kegiatanDitambahkan++;
                    }
                }

                $ringkasan[] = [
                    'subdivisi' => $subdivisi->nama_ekskul,
                    'ketua' => $ketua?->siswa?->nama ?? 'Belum ditetapkan',
                    'total_kegiatan' => Kegiatan::where('ekskul_id', $subdivisi->id)->count(),
                ];
            }
        });

        foreach ($ringkasan as $item) {
            $this->command?->line("{$item['subdivisi']} | Ketua: {$item['ketua']} | {$item['total_kegiatan']} kegiatan");
        }

        $this->command?->info("Sub divisi: {$subdivisis->count()}, ketua sudah ada: {$ketuaSudahAda}, ketua baru: {$ketuaDitambahkan}, belum ada kandidat ketua: {$subdivisiTanpaKetua}, kegiatan baru: {$kegiatanDitambahkan}.");
    }
}
