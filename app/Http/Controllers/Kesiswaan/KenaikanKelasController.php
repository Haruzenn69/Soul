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
        $sourceClasses = Kelas::query()
            ->where('tingkat', 'x')
            ->whereHas('siswas', fn ($query) => $query->where('status', 'menunggu_penempatan'))
            ->with('tahunAjaran')
            ->withCount(['siswas as pending_siswas_count' => fn ($query) => $query->where('status', 'menunggu_penempatan')])
            ->orderByDesc('tahun_ajaran_id')->orderBy('nama')->get();

        $sourceClass = $sourceClasses->firstWhere('id', (int) $request->input('kelas_asal_id'));
        $siswas = $sourceClass
            ? Siswa::query()->with('kelas')->where('kelas_id', $sourceClass->id)->where('status', 'menunggu_penempatan')->orderBy('nama')->get()
            : collect();

        $activeYear = TahunAjaran::query()->where('status', 'aktif')->first();
        $targetClasses = $activeYear && $sourceClass
            ? Kelas::query()->where('tahun_ajaran_id', $activeYear->id)->where('tingkat', 'xi')->where('jurusan', $sourceClass->jurusan)->orderBy('rombel')->get()
            : collect();

        return view('kesiswaan.kenaikan-kelas.index', compact('sourceClasses', 'sourceClass', 'siswas', 'activeYear', 'targetClasses'));
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
                if ($siswa->kelas?->jurusan !== $target->jurusan) {
                    throw ValidationException::withMessages(['siswa_ids' => "{$siswa->nama} hanya dapat ditempatkan pada kelas dengan jurusan yang sama."]);
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
