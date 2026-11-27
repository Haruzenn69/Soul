<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\KategoriEkskul;
use App\Models\Pembina;
use App\Models\Pendaftaran;
use App\Support\AturanEkskul;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EkskulController extends Controller
{
    public function index(Request $request): View
    {
        $kategoriList = $this->kategoriList();
        $kategoriModels = KategoriEkskul::withCount('ekskuls')->orderBy('id')->get();
        $parentEkskuls = Ekskul::whereNull('parent_id')->orderBy('nama_ekskul')->get();

        return view('kesiswaan.ekskuls.index', [
            'kategoriList' => $kategoriList,
            'kategoriModels' => $kategoriModels,
            'pembinas' => Pembina::with(['ekskuls'])->withCount('ekskuls')->orderBy('nama')->get(),
            'parentEkskuls' => $parentEkskuls,
        ]);
    }

    public function byKategori(Request $request, string $kategori): View
    {
        $kategoriList = $this->kategoriList();
        abort_unless(in_array($kategori, $kategoriList, true), 404);

        $sort = in_array($request->input('sort'), ['nama_ekskul', 'status', 'created_at'], true) ? $request->input('sort') : 'nama_ekskul';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $ekskuls = Ekskul::with(['pembina', 'pelatih', 'subdivisis.pembina', 'subdivisis.pelatih', 'subdivisis.penguruses.siswa', 'penguruses.siswa'])
            ->where('kategori', $kategori)
            ->whereNull('parent_id')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where(function ($search) use ($q) {
                    $search->where('nama_ekskul', 'like', "%{$q}%")
                        ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$q}%"))
                        ->orWhereHas('subdivisis', fn ($sub) => $sub->where('nama_ekskul', 'like', "%{$q}%"));
                });
            })
            ->when($request->input('status') === 'aktif', fn ($q) => $q->where('status', true))
            ->when($request->input('status') === 'nonaktif', fn ($q) => $q->where('status', false))
            ->orderByDesc('status')
            ->orderByRaw('CASE WHEN pembina_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy($sort, $direction)
            ->paginate(50)
            ->withQueryString();

        $parentEkskuls = Ekskul::where('kategori', $kategori)
            ->whereNull('parent_id')
            ->orderBy('nama_ekskul')
            ->get();

        return view('kesiswaan.ekskuls.by-kategori', [
            'ekskuls' => $ekskuls,
            'pembinas' => Pembina::with(['ekskuls'])->withCount('ekskuls')->orderBy('nama')->get(),
            'parentEkskuls' => $parentEkskuls,
            'kategori' => $kategori,
            'kategoriList' => $kategoriList,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function edit(Ekskul $ekskul): View
    {
        $kategoriList = $this->kategoriList();
        $pembinas = Pembina::with(['ekskuls'])->withCount('ekskuls')->orderBy('nama')->get();

        return view('kesiswaan.ekskuls.edit', compact('ekskul', 'kategoriList', 'pembinas'));
    }

    public function show(Request $request, Ekskul $ekskul): View
    {
        $ekskul->load([
            'pembina',
            'pelatih',
            'parent',
            'subdivisis' => function ($q) {
                $q->withCount('pendaftarans');
            },
            'penguruses.siswa',
            'penguruses.subdivisi',
        ]);

        $sort = in_array($request->input('sort'), ['nama', 'nis', 'status', 'tanggal_daftar'], true) ? $request->input('sort') : 'status';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $anggotaQuery = $ekskul->pendaftarans()
            ->with('siswa.kelas')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->input('q').'%';
                $query->whereHas('siswa', fn ($s) => $s->where('nama', 'like', $q)->orWhere('nis', 'like', $q));
            })
            ->when($request->filled('status_anggota'), fn ($query) => $query->where('status', $request->input('status_anggota')))
            ->whereNotIn('status', [Pendaftaran::STATUS_KELUAR, Pendaftaran::STATUS_LULUS]);

        if (in_array($sort, ['nama', 'nis'], true)) {
            $anggotaQuery->join('siswas', 'siswas.id', '=', 'pendaftarans.siswa_id')
                ->select('pendaftarans.*')
                ->orderBy("siswas.{$sort}", $direction);
        } else {
            $anggotaQuery->orderBy($sort, $direction);
        }

        $anggota = $anggotaQuery->paginate(20)->withQueryString();

        return view('kesiswaan.ekskuls.show', compact('ekskul', 'anggota', 'sort', 'direction'));
    }

    public function store(Request $request): RedirectResponse
    {
        $kategoriList = $this->kategoriList();
        if (AturanEkskul::isWajib($request->input('kategori'))) {
            $request->merge(['kategori' => AturanEkskul::KATEGORI_WAJIB]);
        }
        $data = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'kategori'    => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::in($kategoriList)],
            'pembina_id'  => ['nullable', 'exists:pembinas,id'],
            'parent_id'   => ['nullable', 'exists:ekskuls,id'],
            'deskripsi'   => ['nullable', 'string'],
            'jadwal'      => ['nullable', 'string', 'max:255'],
        ]);

        if (! empty($data['parent_id'])) {
            $parent = Ekskul::findOrFail($data['parent_id']);
            $data['tipe'] = $parent->tipe ?? Ekskul::TIPE_EKSKUL;
            $data['kategori'] = $parent->kategori;
            $data['pembina_id'] = $data['pembina_id'] ?? $parent->pembina_id;
        } else {
            $data['pelatih_id'] = null;
            $data['tipe'] = Ekskul::TIPE_KOMUNITAS;
        }

        if (! empty($data['pembina_id'])) {
            $pembina = Pembina::findOrFail($data['pembina_id']);
            $maksPembina = AturanEkskul::MAKS_EKSKUL_PEMBINA;
            $count = Ekskul::where('pembina_id', $pembina->id)->whereNull('parent_id')->count();
            if ($count >= $maksPembina && empty($data['parent_id'])) {
                return back()->withErrors([
                    'pembina_id' => "Pembina {$pembina->nama} sudah membina {$maksPembina} ekskul (batas maksimal {$maksPembina} ekskul per pembina).",
                ])->withInput();
            }
            $namaPembinaLower = strtolower(trim($pembina->nama ?? ''));
            if (str_contains($namaPembinaLower, 'nurianti') || str_contains($namaPembinaLower, 'nuri anti') || str_contains($namaPembinaLower, 'bu nuri')) {
                return back()->withErrors(['pembina_id' => 'Bu Nurianti tidak diperbolehkan membina ekskul.'])->withInput();
            }
            $pembina->update(['kategori_pernah_dibina' => $data['kategori']]);
        }

        Ekskul::create($data);

        return back()->with('success', "Ekskul {$data['nama_ekskul']} berhasil ditambahkan.");
    }

    public function storeSubdivisi(Request $request, Ekskul $ekskul): RedirectResponse
    {
        if ($ekskul->isSubdivisi()) {
            return back()->withErrors(['nama_ekskul' => 'Sub divisi tidak dapat memiliki sub divisi lagi.'])->withInput();
        }

        $validated = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'deskripsi'   => ['nullable', 'string'],
            'jadwal'      => ['nullable', 'string', 'max:255'],
        ], [
            'nama_ekskul.required' => 'Nama sub divisi wajib diisi.',
            'nama_ekskul.max'      => 'Nama sub divisi maksimal 255 karakter.',
        ]);

        $subdivisi = Ekskul::create([
            'nama_ekskul' => $validated['nama_ekskul'],
            'kategori'    => $ekskul->kategori,
            'tipe'        => $ekskul->tipe ?? Ekskul::TIPE_EKSKUL,
            'parent_id'   => $ekskul->id,
            'pembina_id'  => $ekskul->pembina_id,
            'deskripsi'   => $validated['deskripsi'] ?? null,
            'jadwal'      => $validated['jadwal'] ?? null,
            'status'      => true,
        ]);

        return back()->with('success', "Sub divisi '{$subdivisi->nama_ekskul}' berhasil ditambahkan ke {$ekskul->nama_ekskul}.");
    }

    public function destroySubdivisi(Ekskul $ekskul, Ekskul $subdivisi): RedirectResponse
    {
        abort_unless($subdivisi->parent_id === $ekskul->id, 404);

        $nama = $subdivisi->nama_ekskul;

        // Unlink or clean up any pengurus linked to this sub divisi
        \App\Models\Pengurus::where('subdivisi_id', $subdivisi->id)->update([
            'subdivisi_id' => null,
            'status'       => \App\Models\Pengurus::STATUS_SELESAI,
            'catatan'      => 'Sub divisi telah dihapus',
        ]);

        $subdivisi->delete();

        return back()->with('success', "Sub divisi '{$nama}' berhasil dihapus dari {$ekskul->nama_ekskul}.");
    }

    public function update(Request $request, Ekskul $ekskul): RedirectResponse
    {
        $kategoriList = $this->kategoriList();
        // Proteksi kategori Wajib dari perubahan
        if ($ekskul->isWajib()) {
            // Ekskul Wajib: paksa kategori tetap Wajib, tidak bisa diubah
            $request->merge(['kategori' => AturanEkskul::KATEGORI_WAJIB]);
        } elseif (AturanEkskul::isWajib($request->input('kategori'))) {
            // Ekskul non-Wajib tidak bisa diubah menjadi Wajib lewat form
            return back()->withErrors([
                'kategori' => 'Kategori Wajib hanya bisa ditetapkan saat ekskul pertama kali dibuat.',
            ])->withInput();
        }

        $data = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'kategori'    => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::in($kategoriList)],
            'deskripsi'   => ['nullable', 'string'],
            'jadwal'      => ['nullable', 'string', 'max:255'],
            'status'      => ['required', 'boolean'],
        ]);

        $ekskul->update($data);

        return redirect()->route('kesiswaan.ekskuls.index')->with('success', "Ekskul {$ekskul->nama_ekskul} berhasil diperbarui.");
    }

    public function destroy(Ekskul $ekskul): RedirectResponse
    {
        $nama = $ekskul->nama_ekskul;
        $ekskul->update(['status' => ! $ekskul->status]);

        $state = $ekskul->status ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Ekskul {$nama} berhasil {$state}.");
    }

    /** @return list<string> */
    private function kategoriList(): array
    {
        if (! KategoriEkskul::query()->exists()) {
            foreach (AturanEkskul::DEFAULT_KATEGORI as $nama) {
                KategoriEkskul::firstOrCreate(['nama' => $nama]);
            }
        }

        return KategoriEkskul::query()->orderBy('id')->pluck('nama')->all();
    }
}
