<?php

namespace App\Http\Controllers\Api\Ketua;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Http\Resources\EkskulGaleriResource;
use App\Http\Resources\EkskulResource;
use App\Http\Resources\PrestasiResource;
use App\Models\EkskulGaleri;
use App\Models\Prestasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilEkskulController extends ApiController
{
    use KetuaEkskul;

    public function show(): JsonResponse
    {
        $ekskul = $this->ekskul()->load('galeris');

        return $this->ok([
            'ekskul' => (new EkskulResource($ekskul))->resolve(),
            'galeris' => EkskulGaleriResource::collection($ekskul->galeris)->resolve(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();

        $validated = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tujuan' => ['nullable', 'string'],
            'jadwal' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'nama_ekskul.required' => 'Nama ekskul wajib diisi.',
            'nama_ekskul.max' => 'Nama ekskul maksimal 255 karakter.',
            'logo.max' => 'Logo maksimal 2MB.',
            'cover.max' => 'Cover maksimal 2MB.',
        ]);

        $data = [
            'nama_ekskul' => $validated['nama_ekskul'],
            'tagline' => $validated['tagline'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tujuan' => $validated['tujuan'] ?? null,
            'jadwal' => $validated['jadwal'] ?? null,
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('ekskul/logo', 'public');
        }

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('ekskul/cover', 'public');
        }

        $ekskul->update($data);

        return $this->ok(
            ['ekskul' => (new EkskulResource($ekskul->fresh()))->resolve()],
            'Profil ekskul berhasil diperbarui.',
        );
    }

    public function toggleRecruitment(): JsonResponse
    {
        $ekskul = $this->ekskul();
        $ekskul->update(['is_open_recruitment' => ! $ekskul->is_open_recruitment]);

        return $this->noContent(
            $ekskul->is_open_recruitment ? 'Pendaftaran ekskul telah dibuka.' : 'Pendaftaran ekskul telah ditutup.',
        );
    }

    public function storeGaleri(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();

        $validated = $request->validate([
            'foto' => ['required', 'array', 'max:10'],
            'foto.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'caption' => ['nullable', 'string', 'max:255'],
        ], [
            'foto.required' => 'Pilih foto terlebih dahulu.',
            'foto.max' => 'Maksimal 10 foto dalam sekali upload.',
            'foto.*.max' => 'Setiap foto maksimal 4MB.',
        ]);

        $galeris = collect();

        foreach ($request->file('foto', []) as $file) {
            $galeris->push($ekskul->galeris()->create([
                'foto' => $file->store('ekskul/galeri', 'public'),
                'caption' => $validated['caption'] ?? null,
            ]));
        }

        return $this->created(
            ['galeris' => EkskulGaleriResource::collection($galeris)->resolve()],
            'Foto galeri berhasil ditambahkan.',
        );
    }

    public function destroyGaleri(EkskulGaleri $galeri): JsonResponse
    {
        $this->ensureEkskul($galeri);

        Storage::disk('public')->delete($galeri->foto);
        $galeri->delete();

        return $this->noContent('Foto galeri berhasil dihapus.');
    }

    public function prestasi(): JsonResponse
    {
        $ekskul = $this->ekskul();

        $query = $ekskul->prestasis();
        $total = (clone $query)->count();

        if ($cari = request('cari')) {
            $query->where(fn ($q) => $q
                ->where('judul', 'like', "%{$cari}%")
                ->orWhere('kategori', 'like', "%{$cari}%"));
        }

        if ($kategori = request('kategori')) {
            if ($kategori !== 'semua') {
                $query->where('kategori', $kategori);
            }
        }

        $prestasis = $query->orderBy('tahun', 'desc')->paginate(10);

        return $this->ok([
            'total' => $total,
            'prestasis' => PrestasiResource::collection($prestasis)->resolve(),
            'kategori_options' => $ekskul->prestasis()->select('kategori')->distinct()->pluck('kategori')->filter()->values(),
            'pagination' => [
                'current' => $prestasis->currentPage(),
                'last' => $prestasis->lastPage(),
                'per_page' => $prestasis->perPage(),
            ],
        ]);
    }

    public function storePrestasi(Request $request): JsonResponse
    {
        $ekskul = $this->ekskul();

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:255'],
            'tahun' => ['nullable', 'string', 'max:4'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'judul.required' => 'Judul prestasi wajib diisi.',
            'foto.max' => 'Foto prestasi maksimal 2MB.',
        ]);

        $prestasi = $ekskul->prestasis()->create([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'] ?? null,
            'tahun' => $validated['tahun'] ?? null,
            'foto' => $request->hasFile('foto') ? $request->file('foto')->store('ekskul/prestasi', 'public') : null,
        ]);

        return $this->created(
            ['prestasi' => (new PrestasiResource($prestasi))->resolve()],
            'Prestasi berhasil ditambahkan.',
        );
    }

    public function destroyPrestasi(Prestasi $prestasi): JsonResponse
    {
        $this->ensureEkskul($prestasi);

        $prestasi->delete();

        return $this->noContent('Prestasi berhasil dihapus.');
    }
}