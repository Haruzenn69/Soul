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
        $sort = in_array($request->input('sort'), ['nama_ekskul', 'status', 'rekrutmen', 'created_at'], true) ? $request->input('sort') : 'nama_ekskul';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $sortColumn = match ($sort) {
            'rekrutmen' => 'is_open_recruitment',
            default => $sort,
        };

        $kategoriList = AturanEkskul::kategoriList();
        $kategoriModels = KategoriEkskul::withCount('ekskuls')->orderBy('id')->get();

        $ekskuls = Ekskul::with(['pembina', 'pelatih'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where(function ($search) use ($q) {
                    $search->where('nama_ekskul', 'like', "%{$q}%")
                        ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$q}%"));
                });
            })
            ->when(in_array($request->input('kategori'), $kategoriList, true), fn ($q) => $q->where('kategori', $request->input('kategori')))
            ->when($request->input('status') === 'aktif', fn ($q) => $q->where('status', true))
            ->when($request->input('status') === 'nonaktif', fn ($q) => $q->where('status', false))
            ->when($request->input('rekrutmen') === 'buka', fn ($q) => $q->where('is_open_recruitment', true))
            ->when($request->input('rekrutmen') === 'tutup', fn ($q) => $q->where('is_open_recruitment', false))
            ->orderByDesc('status')
            ->orderBy($sortColumn, $direction)
            ->paginate(50)
            ->withQueryString();

        return view('kesiswaan.ekskuls.index', [
            'ekskuls' => $ekskuls,
            'pembinas' => Pembina::with(['ekskuls'])->withCount('ekskuls')->orderBy('nama')->get(),
            'kategoriList' => $kategoriList,
            'kategoriModels' => $kategoriModels,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function edit(Ekskul $ekskul): View
    {
        $kategoriList = AturanEkskul::kategoriList();
        $pembinas = Pembina::with(['ekskuls'])->withCount('ekskuls')->orderBy('nama')->get();

        return view('kesiswaan.ekskuls.edit', compact('ekskul', 'kategoriList', 'pembinas'));
    }

    public function show(Request $request, Ekskul $ekskul): View
    {
        $ekskul->load(['pembina', 'pelatih']);

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
        if (AturanEkskul::isWajib($request->input('kategori'))) {
            $request->merge(['kategori' => AturanEkskul::KATEGORI_WAJIB]);
        }
        $data = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'kategori'    => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::in(\App\Support\AturanEkskul::kategoriList())],
            'pembina_id' => ['nullable', 'exists:pembinas,id'],
            'deskripsi' => ['nullable', 'string'],
            'jadwal' => ['nullable', 'string', 'max:255'],
        ]);
        if (! empty($data['pembina_id'])) {
            $pembina = Pembina::findOrFail($data['pembina_id']);
            $count = Ekskul::where('pembina_id', $pembina->id)->count();
            if ($count >= 4) {
                return back()->withErrors([
                    'pembina_id' => "Pembina {$pembina->nama} sudah membina 4 ekskul (batas maksimal 4 ekskul per pembina).",
                ])->withInput();
            }
            $namaPembinaLower = strtolower(trim($pembina->nama ?? ''));
            if (str_contains($namaPembinaLower, 'nurianti') || str_contains($namaPembinaLower, 'nuri anti') || str_contains($namaPembinaLower, 'bu nuri')) {
                return back()->withErrors(['pembina_id' => 'Bu Nurianti tidak diperbolehkan membina ekskul.'])->withInput();
            }
            $pembina->update(['kategori_pernah_dibina' => $data['kategori']]);
        }

        $data['is_open_recruitment'] = true;

        Ekskul::create($data);

        return back()->with('success', "Ekskul {$data['nama_ekskul']} berhasil ditambahkan.");
    }

    public function update(Request $request, Ekskul $ekskul): RedirectResponse
    {
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
            'kategori'    => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::in(\App\Support\AturanEkskul::kategoriList())],
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
}
