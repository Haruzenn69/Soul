<?php

namespace App\Http\Controllers\Api\Ketua;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Http\Resources\EkskulResource;
use App\Http\Resources\KegiatanResource;
use App\Http\Resources\PendaftaranResource;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\PresensiPelatih;
use App\Services\NotifikasiService;
use App\Services\RekapAbsensiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KegiatanController extends ApiController
{
    use KetuaEkskul;

    public function index(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();

        $query = Kegiatan::where('ekskul_id', $ekskul->id);

        if ($cari = $request->input('cari')) {
            $query->where(fn ($q) => $q
                ->where('materi', 'like', "%{$cari}%")
                ->orWhere('deskripsi', 'like', "%{$cari}%"));
        }

        if ($jenis = $request->input('jenis')) {
            if ($jenis === 'event') {
                $query->whereNotNull('jenis_kegiatan');
            } elseif ($jenis !== 'semua') {
                $query->whereNull('jenis_kegiatan');
            }
        }

        /** @param 'asc'|'desc' $direction */
        $sort = $request->input('sort', 'tanggal_kegiatan');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (! in_array($sort, ['tanggal_kegiatan', 'materi'], true)) {
            $sort = 'tanggal_kegiatan';
        }

        $kegiatans = $query->withCount('presensis')->orderBy($sort, $direction)->paginate(10);

        return $this->ok([
            'total' => $kegiatans->total(),
            'kegiatans' => KegiatanResource::collection($kegiatans)->resolve(),
            'pagination' => [
                'current' => $kegiatans->currentPage(),
                'last' => $kegiatans->lastPage(),
                'per_page' => $kegiatans->perPage(),
            ],
        ]);
    }

    public function show(Kegiatan $kegiatan): JsonResponse
    {
        $this->ensureEkskul($kegiatan);

        $kegiatan->load(['presensis.pendaftaran.siswa', 'presensiPelatihs', 'ekskul.pelatih']);

        return $this->ok([
            'kegiatan' => (new KegiatanResource($kegiatan))->resolve(),
            'ringkasan' => [
                'hadir' => $kegiatan->presensis->where('status', 'hadir')->count(),
                'izin' => $kegiatan->presensis->where('status', 'izin')->count(),
                'sakit' => $kegiatan->presensis->where('status', 'sakit')->count(),
                'alpha' => $kegiatan->presensis->where('status', 'alpha')->count(),
            ],
            'presensi' => $kegiatan->presensis->map(fn ($p) => [
                'pendaftaran_id' => $p->pendaftaran_id,
                'status' => $p->status,
                'nama' => $p->pendaftaran->siswa->nama ?? '-',
            ])->values(),
            'pelatih_presensi_status' => $kegiatan->presensiPelatihs->first()?->status,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();

        $validated = $request->validate($this->rules(), $this->messages());

        $path = null;
        if ($request->hasFile('dokumentasi')) {
            $path = $request->file('dokumentasi')->store('dokumentasi-kegiatan', 'public');
        }

        $kegiatan = $ekskul->kegiatans()->create([
            'materi' => $validated['materi'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'dokumentasi' => $path,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? null,
        ]);

        NotifikasiService::kegiatanDibuat($kegiatan);

        return $this->created(
            ['kegiatan' => (new KegiatanResource($kegiatan))->resolve()],
            'Kegiatan berhasil dibuat.',
        );
    }

    public function update(Request $request, Kegiatan $kegiatan): JsonResponse
    {
        $this->ensureEkskul($kegiatan);

        $validated = $request->validate($this->rules(), $this->messages());

        $path = $kegiatan->dokumentasi;
        if ($request->hasFile('dokumentasi')) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('dokumentasi')->store('dokumentasi-kegiatan', 'public');
        }

        $kegiatan->update([
            'materi' => $validated['materi'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'dokumentasi' => $path,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? null,
        ]);

        return $this->ok(
            ['kegiatan' => (new KegiatanResource($kegiatan->fresh()))->resolve()],
            'Kegiatan berhasil diperbarui.',
        );
    }

    public function destroy(Kegiatan $kegiatan): JsonResponse
    {
        $this->ensureEkskul($kegiatan);

        if ($kegiatan->dokumentasi) {
            Storage::disk('public')->delete($kegiatan->dokumentasi);
        }

        $kegiatan->delete();

        return $this->noContent('Kegiatan berhasil dihapus.');
    }

    public function presensiForm(Kegiatan $kegiatan): JsonResponse
    {
        $this->ensureEkskul($kegiatan);
        $ekskul = $this->ekskul();

        $anggotas = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->with('siswa.kelas')
            ->get();

        $presensiExisting = Presensi::where('kegiatan_id', $kegiatan->id)
            ->pluck('status', 'pendaftaran_id')
            ->toArray();

        $pelatih = $ekskul->pelatih;
        $pelatihStatus = $pelatih
            ? PresensiPelatih::where('kegiatan_id', $kegiatan->id)->where('pelatih_id', $pelatih->id)->value('status')
            : null;

        return $this->ok([
            'kegiatan' => (new KegiatanResource($kegiatan))->resolve(),
            'anggotas' => PendaftaranResource::collection($anggotas)->resolve(),
            'presensi_existing' => $presensiExisting,
            'pelatih' => $pelatih ? [
                'id' => $pelatih->id,
                'nama' => $pelatih->nama,
            ] : null,
            'pelatih_status' => $pelatihStatus,
        ]);
    }

    public function storePresensi(Request $request, Kegiatan $kegiatan): JsonResponse
    {
        $this->ensureEkskul($kegiatan);
        $ekskul = $this->ekskul();

        $validated = $request->validate([
            'presensi' => ['nullable', 'array'],
            'presensi.*.pendaftaran_id' => ['required', 'exists:pendaftarans,id'],
            'presensi.*.status' => ['required', 'in:hadir,sakit,izin,alpha'],
            'pelatih_presensi' => ['nullable', 'array'],
            'pelatih_presensi.pelatih_id' => ['nullable', 'exists:pelatihs,id'],
            'pelatih_presensi.status' => ['nullable', 'in:hadir,sakit,izin,alpha'],
        ], [
            'presensi.*.pendaftaran_id.required' => 'Anggota presensi wajib dipilih.',
            'presensi.*.status.in' => 'Status presensi tidak valid.',
        ]);

        $items = $validated['presensi'] ?? [];

        $pendaftaranIds = collect($items)->pluck('pendaftaran_id')->unique();

        $validCount = Pendaftaran::whereIn('id', $pendaftaranIds)
            ->where('ekskul_id', $kegiatan->ekskul_id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->count();

        if ($validCount !== $pendaftaranIds->count()) {
            return $this->error('Presensi hanya bisa diisi untuk anggota aktif ekskul kegiatan ini.', 422);
        }

        foreach ($items as $item) {
            Presensi::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'pendaftaran_id' => $item['pendaftaran_id']],
                ['status' => $item['status']],
            );
        }

        $pelatih = $validated['pelatih_presensi'] ?? null;

        if ($pelatih && isset($pelatih['pelatih_id'], $pelatih['status'])) {
            abort_unless(
                $ekskul->pelatih_id === (int) $pelatih['pelatih_id'],
                422,
                'Kehadiran pelatih tidak sesuai dengan ekskul kegiatan ini.',
            );

            PresensiPelatih::updateOrCreate(
                ['kegiatan_id' => $kegiatan->id, 'pelatih_id' => $pelatih['pelatih_id']],
                ['status' => $pelatih['status']],
            );
        }

        return $this->noContent('Presensi berhasil disimpan.');
    }

    public function rekap(Request $request): JsonResponse
    {
        $bulan = (new RekapAbsensiService)->normalizeBulan($request->input('bulan'));
        $data = (new RekapAbsensiService)->rekap($this->ekskul(), $bulan);

        $rows = $data['rows']->map(fn ($r) => [
            'pendaftaran_id' => $r->pendaftaran->id,
            'nama' => $r->pendaftaran->siswa->nama ?? '-',
            'kelas' => $r->pendaftaran->siswa->kelas->nama ?? '-',
            'sel' => $r->sel,
            'hadir' => $r->hadir,
            'izin' => $r->izin,
            'sakit' => $r->sakit,
            'alpha' => $r->alpha,
            'total' => $r->total,
            'persentase_kehadiran' => $r->persentaseKehadiran,
        ])->values();

        return $this->ok([
            'ekskul' => (new EkskulResource($data['ekskul']))->resolve(),
            'bulan' => $data['bulan'],
            'kegiatans' => KegiatanResource::collection($data['kegiatans'])->resolve(),
            'event_kegiatans' => KegiatanResource::collection($data['eventKegiatans'])->resolve(),
            'rows' => $rows,
            'total_hadir' => $data['totalHadir'],
            'total_izin' => $data['totalIzin'],
            'total_sakit' => $data['totalSakit'],
            'total_alpha' => $data['totalAlpha'],
            'available_months' => $data['availableMonths']->values()->all(),
            'pelatih' => $data['pelatih'] ? [
                'id' => $data['pelatih']->id,
                'nama' => $data['pelatih']->nama,
            ] : null,
            'presensi_pelatih' => $data['presensiPelatih'],
        ]);
    }

    public function downloadRekapPdf(Request $request): Response
    {
        $data = (new RekapAbsensiService)->rekap(
            $this->ekskul(),
            (new RekapAbsensiService)->normalizeBulan($request->input('bulan')),
        );
        $pdf = Pdf::loadView('ketua.presensi.rekap-pdf', $data);
        $filename = 'rekap-absensi-'.str_replace('/', '-', $data['bulan']).'-'.Str::slug($data['ekskul']->nama_ekskul ?? 'ekskul').'.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function rules(): array
    {
        return [
            'materi' => ['required', 'string', 'max:255'],
            'jenis_kegiatan' => ['nullable', 'in:event'],
            'tanggal_kegiatan' => ['required', 'date'],
            'tanggal_berakhir' => ['nullable', 'date', 'after_or_equal:tanggal_kegiatan'],
            'deskripsi' => ['nullable', 'string'],
            'dokumentasi' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    private function messages(): array
    {
        return [
            'materi.required' => 'Materi kegiatan wajib diisi.',
            'materi.max' => 'Materi kegiatan maksimal 255 karakter.',
            'tanggal_kegiatan.required' => 'Tanggal kegiatan wajib diisi.',
            'dokumentasi.image' => 'Dokumentasi harus berupa gambar.',
            'dokumentasi.max' => 'Dokumentasi maksimal 2MB.',
        ];
    }
}
