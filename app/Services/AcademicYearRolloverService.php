<?php

namespace App\Services;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SiswaRiwayatKelas;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AcademicYearRolloverService
{
    /**
     * Activate a specific tahun ajaran and perform class promotion.
     * This is the new method used by TahunAjaranController.
     */
    public function activateTahunAjaran(TahunAjaran $newYear): array
    {
        return DB::transaction(function () use ($newYear): array {
            $currentYear = TahunAjaran::query()->where('status', 'aktif')->lockForUpdate()->first();

            if (!$currentYear) {
                // No active year, just activate this one
                $newYear->update(['status' => 'aktif']);
                return [
                    'from' => null,
                    'to' => $newYear->nama,
                    'promoted' => 0,
                    'pending' => 0,
                    'graduated' => 0,
                    'already_done' => false,
                ];
            }

            if ($currentYear->id === $newYear->id) {
                return [
                    'from' => $currentYear->nama,
                    'to' => $newYear->nama,
                    'promoted' => 0,
                    'pending' => 0,
                    'graduated' => 0,
                    'already_done' => true,
                ];
            }

            $currentClasses = Kelas::query()->where('tahun_ajaran_id', $currentYear->id)->orderBy('id')->get();

            // Create new classes in the new year
            foreach ($currentClasses->where('tingkat', 'x') as $class) {
                $this->copyClass($class, 'x', $newYear);
                $this->copyClass($class, 'xi', $newYear);
            }
            foreach ($currentClasses->where('tingkat', 'xi') as $class) {
                $this->copyClass($class, 'xii', $newYear);
            }

            $now = now('Asia/Jakarta');
            $promoted = 0;
            $pending = 0;
            $graduated = 0;

            foreach ($currentClasses as $class) {
                $students = Siswa::query()->where('kelas_id', $class->id)->where('status', 'aktif')->lockForUpdate()->get();

                if ($class->tingkat === 'xi') {
                    // 11 → 12 automatic
                    $target = $this->findTargetClass($class, 'xii', $newYear);
                    foreach ($students as $student) {
                        $this->recordHistory($student, $currentYear->nama, $class->nama, $target->nama, 'kenaikan', $now);
                        $student->update(['kelas_id' => $target->id, 'status' => 'aktif']);
                        $promoted++;
                    }
                } elseif ($class->tingkat === 'xii') {
                    // 12 → graduate
                    foreach ($students as $student) {
                        $this->recordHistory($student, $currentYear->nama, $class->nama, null, 'kelulusan', $now);
                        $student->update(['status' => 'nonaktif', 'angkatan' => $currentYear->nama]);
                        if ($student->user) {
                            $student->user->forceFill(['is_active' => false, 'remember_token' => null])->save();
                            $student->user->tokens()->delete();
                        }
                        $graduated++;
                    }
                } elseif ($class->tingkat === 'x') {
                    // 10 → waiting for manual placement
                    $pending += $students->count();
                    Siswa::query()->whereKey($students->modelKeys())->update(['status' => 'menunggu_penempatan', 'updated_at' => $now]);
                }
            }

            $currentYear->update(['status' => 'historis']);
            $newYear->update(['status' => 'aktif']);

            return [
                'from' => $currentYear->nama,
                'to' => $newYear->nama,
                'promoted' => $promoted,
                'pending' => $pending,
                'graduated' => $graduated,
                'already_done' => false,
            ];
        }, 3);
    }

    /**
     * Preview what will happen during activation.
     */
    public function previewActivation(TahunAjaran $newYear): array
    {
        $currentYear = TahunAjaran::getActive();
        if (!$currentYear) {
            return [
                'from' => null,
                'to' => $newYear->nama,
                'promoted' => 0,
                'pending' => 0,
                'graduated' => 0,
                'new_classes' => 0,
            ];
        }

        $classes = Kelas::query()->where('tahun_ajaran_id', $currentYear->id)->get();
        $counts = ['x' => 0, 'xi' => 0, 'xii' => 0];
        $targetIdentities = [];

        foreach ($classes as $class) {
            if (in_array($class->tingkat, ['x', 'xi', 'xii'], true)) {
                $counts[$class->tingkat] += Siswa::query()->where('kelas_id', $class->id)->where('status', 'aktif')->count();
            }

            if ($class->tingkat === 'x') {
                $targetIdentities[] = [$this->classIdentity($class), 'x'];
                $targetIdentities[] = [$this->classIdentity($class), 'xi'];
            } elseif ($class->tingkat === 'xi') {
                $targetIdentities[] = [$this->classIdentity($class), 'xii'];
            }
        }

        $newClasses = collect($targetIdentities)->unique(fn ($item) => $item[0][0].'|'.$item[0][1].'|'.$item[1])
            ->filter(function ($item) use ($newYear) {
                [$jurusan, $rombel] = $item[0];
                $tier = $item[1];
                return !Kelas::query()->where('tahun_ajaran_id', $newYear->id)
                    ->where('tingkat', $tier)->where('jurusan', $jurusan)->where('rombel', $rombel)->exists();
            })->count();

        return [
            'from' => $currentYear->nama,
            'to' => $newYear->nama,
            'promoted' => $counts['xi'],
            'pending' => $counts['x'],
            'graduated' => $counts['xii'],
            'new_classes' => $newClasses,
        ];
    }

    private function copyClass(Kelas $source, string $targetTier, TahunAjaran $targetYear): Kelas
    {
        [$jurusan, $rombel] = $this->classIdentity($source);

        return $this->findTargetClassByIdentity($jurusan, $rombel, $targetTier, $targetYear);
    }

    private function findTargetClass(Kelas $source, string $targetTier, TahunAjaran $targetYear): Kelas
    {
        [$jurusan, $rombel] = $this->classIdentity($source);

        return $this->findTargetClassByIdentity($jurusan, $rombel, $targetTier, $targetYear);
    }

    private function findTargetClassByIdentity(string $jurusan, string $rombel, string $targetTier, TahunAjaran $targetYear): Kelas
    {
        $target = Kelas::query()
            ->where('tahun_ajaran_id', $targetYear->id)
            ->where('tingkat', $targetTier)
            ->where('jurusan', $jurusan)
            ->where('rombel', $rombel)
            ->first();

        if ($target) {
            return $target;
        }

        $labelTingkat = config("kelas.tingkat.{$targetTier}");
        $labelJurusan = config("kelas.jurusan.{$jurusan}.{$targetTier}");
        if (! $labelTingkat || ! $labelJurusan) {
            throw new RuntimeException("Konfigurasi tingkat/jurusan tidak lengkap untuk kelas {$jurusan} {$rombel}.");
        }

        return Kelas::query()->create([
            'nama' => "{$labelTingkat} {$labelJurusan} {$rombel}",
            'tingkat' => $targetTier,
            'jurusan' => $jurusan,
            'rombel' => $rombel,
            'tahun_ajaran_id' => $targetYear->id,
        ]);
    }

    private function classIdentity(Kelas $class): array
    {
        if ($class->jurusan && $class->rombel) {
            return [(string) $class->jurusan, (string) $class->rombel];
        }

        foreach (config('kelas.jurusan', []) as $code => $labels) {
            $label = $labels[$class->tingkat] ?? null;
            if ($label && preg_match('/(?:^|\s)'.preg_quote($label, '/').'\s+(\d+)$/i', $class->nama, $matches)) {
                return [$code, $matches[1]];
            }
        }

        throw new RuntimeException("Jurusan atau rombel kelas {$class->nama} belum terisi. Lengkapi data kelas sebelum kenaikan tahun ajaran.");
    }

    private function recordHistory(Siswa $student, string $yearName, string $source, ?string $target, string $type, Carbon $at): void
    {
        SiswaRiwayatKelas::query()->create([
            'siswa_id' => $student->id,
            'tahun_ajaran' => $yearName,
            'kelas_asal' => $source,
            'kelas_tujuan' => $target,
            'jenis' => $type,
            'diproses_pada' => $at,
        ]);
    }
}
