<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SiswaRiwayatKelas;
use App\Models\TahunAjaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KenaikanKelasController extends Controller
{
    public function index(Request $request): View
    {
        $activeYear = TahunAjaran::query()->where('status', 'aktif')->first();
        $sourceYear = TahunAjaran::query()->where('status', 'historis')->orderByDesc('id')->first();
        $classes = $activeYear && $sourceYear
            ? Kelas::query()->where('tahun_ajaran_id', $sourceYear->id)
                ->with(['siswas' => fn ($query) => $query->where('status', 'aktif')->orderBy('nama')])
                ->orderByRaw("FIELD(tingkat, 'x', 'xi', 'xii')")->orderBy('nama')->get()
            : collect();
        $targetClasses = $activeYear
            ? Kelas::query()->where('tahun_ajaran_id', $activeYear->id)->orderBy('jurusan')->orderBy('rombel')->get()->groupBy('tingkat')
            : collect();

        return view('kesiswaan.kenaikan-kelas.index', compact('activeYear', 'sourceYear', 'classes', 'targetClasses'));

    }

    public function processRollover(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'naik_xi' => ['sometimes', 'array'], 'naik_xi.*' => ['integer', 'distinct', 'exists:siswas,id'],
            'naik_xii' => ['sometimes', 'array'], 'naik_xii.*' => ['integer', 'distinct', 'exists:siswas,id'],
            'lulus' => ['sometimes', 'array'], 'lulus.*' => ['integer', 'distinct', 'exists:siswas,id'],
            'kelas_tujuan' => ['sometimes', 'array'], 'kelas_tujuan.*' => ['integer', 'exists:kelas,id'],
        ]);
        $activeYear = TahunAjaran::query()->where('status', 'aktif')->firstOrFail();
        $sourceYear = TahunAjaran::query()->where('status', 'historis')->orderByDesc('id')->firstOrFail();
        $result = app(\App\Services\AcademicYearRolloverService::class)->processPromotionChoices($sourceYear, $activeYear, $data);

        return back()->with('success', "Pilihan tersimpan: {$result['promoted']} siswa naik tingkat, {$result['graduated']} siswa lulus, dan {$result['repeated']} siswa tinggal kelas.");
    }

    public function assign(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kelas_tujuan_id' => ['required', 'integer', 'exists:kelas,id'],
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => ['required', 'integer', 'distinct', 'exists:siswas,id'],
        ]);

        $result = DB::transaction(function () use ($data) {
            $activeYear = TahunAjaran::query()->where('status', 'aktif')->lockForUpdate()->first();
            $target = Kelas::query()->lockForUpdate()->findOrFail($data['kelas_tujuan_id']);

            if (! $activeYear || (int) $target->tahun_ajaran_id !== (int) $activeYear->id || $target->tingkat !== 'xi') {
                throw ValidationException::withMessages(['kelas_tujuan_id' => 'Pilih kelas 11 pada tahun ajaran aktif.']);
            }

            $siswas = Siswa::query()->with('kelas')->whereIn('id', $data['siswa_ids'])->lockForUpdate()->get();
            if ($siswas->count() !== count($data['siswa_ids'])) {
                throw ValidationException::withMessages(['siswa_ids' => 'Sebagian siswa tidak ditemukan.']);
            }

            foreach ($siswas as $siswa) {
                if ($siswa->status !== 'menunggu_penempatan' || $siswa->kelas?->tingkat !== 'x') {
                    throw ValidationException::withMessages(['siswa_ids' => "{$siswa->nama} tidak lagi menunggu penempatan kelas 11."]);
                }
                $allowedJurusan = [$siswa->kelas?->jurusan];
                if ($siswa->kelas?->jurusan === 'mp') {
                    $allowedJurusan[] = 'mlog';
                }
                if (! in_array($target->jurusan, $allowedJurusan, true)) {
                    throw ValidationException::withMessages(['siswa_ids' => "{$siswa->nama} hanya dapat ditempatkan pada kelas dengan jurusan yang sesuai."]);
                }
            }

            $now = now('Asia/Jakarta');
            foreach ($siswas as $siswa) {
                SiswaRiwayatKelas::query()->create([
                    'siswa_id' => $siswa->id,
                    'tahun_ajaran' => $siswa->kelas->tahunAjaran?->nama ?? '-',
                    'kelas_asal' => $siswa->kelas->nama,
                    'kelas_tujuan' => $target->nama,
                    'jenis' => 'penempatan',
                    'diproses_pada' => $now,
                ]);

                $siswa->update(['kelas_id' => $target->id, 'status' => 'aktif']);
            }

            return ['count' => $siswas->count(), 'class_name' => $target->nama];
        }, 3);

        return back()->with('success', "{$result['count']} siswa berhasil ditempatkan ke kelas {$result['class_name']}.");
    }
}
