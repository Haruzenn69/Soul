<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Pembina;
use App\Models\Pendaftaran;
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

        $ekskuls = Ekskul::with(['pembina', 'pelatih'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where(function ($search) use ($q) {
                    $search->where('nama_ekskul', 'like', "%{$q}%")
                        ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$q}%"));
                });
            })
            ->when(in_array($request->input('kategori'), ['Olahraga', 'Seni', 'Bahasa', 'Lainnya'], true), fn ($q) => $q->where('kategori', $request->input('kategori')))
            ->when($request->input('status') === 'aktif', fn ($q) => $q->where('status', true))
            ->when($request->input('status') === 'nonaktif', fn ($q) => $q->where('status', false))
            ->when($request->input('rekrutmen') === 'buka', fn ($q) => $q->where('is_open_recruitment', true))
            ->when($request->input('rekrutmen') === 'tutup', fn ($q) => $q->where('is_open_recruitment', false))
            ->orderBy($sortColumn, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('kesiswaan.ekskuls.index', [
            'ekskuls' => $ekskuls,
            'pembinas' => Pembina::with(['ekskuls'])->withCount('ekskuls')->orderBy('nama')->get(),
            'sort' => $sort,
            'direction' => $direction,
        ]);
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
            ->whereNotIn('status', [Pendaftaran::STATUS_KELUAR]);

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
        $data = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:Olahraga,Seni,Bahasa,Lainnya'],
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
        }

        Ekskul::create($data);

        return back()->with('success', "Ekskul {$data['nama_ekskul']} berhasil ditambahkan.");
    }

    public function update(Request $request, Ekskul $ekskul): RedirectResponse
    {
        $data = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:Olahraga,Seni,Bahasa,Lainnya'],
            'pembina_id' => ['nullable', 'exists:pembinas,id'],
            'deskripsi' => ['nullable', 'string'],
            'jadwal' => ['nullable', 'string', 'max:255'],
        ]);
        if (! empty($data['pembina_id'])) {
            $pembina = Pembina::findOrFail($data['pembina_id']);

            // Skip count check if same pembina
            if ($ekskul->pembina_id != $pembina->id) {
                $count = Ekskul::where('pembina_id', $pembina->id)->count();
                if ($count >= 4) {
                    return back()->withErrors([
                        'pembina_id' => "Pembina {$pembina->nama} sudah membina 4 ekskul (batas maksimal 4 ekskul per pembina).",
                    ])->withInput();
                }
            }

            $kategoriBaru = $data['kategori'];
            $namaPembinaLower = strtolower(trim($pembina->nama ?? ''));
            if (str_contains($namaPembinaLower, 'nurianti') || str_contains($namaPembinaLower, 'nuri anti') || str_contains($namaPembinaLower, 'bu nuri')) {
                return back()->withErrors(['pembina_id' => 'Bu Nurianti tidak diperbolehkan membina ekskul.'])->withInput();
            }
            if ($kategoriBaru === 'Olahraga') {
                if (! (str_contains($namaPembinaLower, 'ahmad') && str_contains($namaPembinaLower, 'pak'))) {
                    return back()->withErrors(['pembina_id' => 'Kategori Olahraga hanya boleh dibina oleh Pak Ahmad.'])->withInput();
                }
            }
            $adaPembinaLain = Pembina::whereHas('ekskuls', fn ($q) => $q->where('kategori', $kategoriBaru))
                ->where('id', '!=', $pembina->id)
                ->exists();
            if ($adaPembinaLain) {
                return back()->withErrors(['pembina_id' => "Kategori {$kategoriBaru} sudah dibina oleh pembina lain. Setiap kategori hanya boleh memiliki 1 pembina."])->withInput();
            }
            $existingKategori = $pembina->ekskuls->first()?->kategori;
            if ($existingKategori && $existingKategori !== $kategoriBaru) {
                return back()->withErrors([
                    'pembina_id' => "Pembina {$pembina->nama} membina kategori {$existingKategori}. Ekskul yang ditugaskan harus berkategori sama ({$existingKategori}).",
                ])->withInput();
            }

            $pembina->update(['kategori_pernah_dibina' => $data['kategori']]);
        }

        $ekskul->update($data);

        return back()->with('success', "Ekskul {$ekskul->nama_ekskul} berhasil diperbarui.");
    }

    public function destroy(Ekskul $ekskul): RedirectResponse
    {
        $nama = $ekskul->nama_ekskul;
        $ekskul->update(['status' => ! $ekskul->status]);

        $state = $ekskul->status ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Ekskul {$nama} berhasil {$state}.");
    }
}
