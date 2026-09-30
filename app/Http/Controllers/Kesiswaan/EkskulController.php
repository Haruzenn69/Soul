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
        $ekskuls = Ekskul::with(['pembina', 'pelatih'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where('nama_ekskul', 'like', "%{$q}%")
                    ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$q}%"));
            })
            ->when($request->input('status') === 'aktif', fn ($q) => $q->where('status', true))
            ->when($request->input('status') === 'nonaktif', fn ($q) => $q->where('status', false))
            ->when($request->input('rekrutmen') === 'buka', fn ($q) => $q->where('is_open_recruitment', true))
            ->when($request->input('rekrutmen') === 'tutup', fn ($q) => $q->where('is_open_recruitment', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('kesiswaan.ekskuls.index', [
            'ekskuls'  => $ekskuls,
            'pembinas' => Pembina::withCount('ekskuls')->orderBy('nama')->get(),
        ]);
    }

    public function show(Request $request, Ekskul $ekskul): View
    {
        $ekskul->load(['pembina', 'pelatih']);

        $anggota = $ekskul->pendaftarans()
            ->with('siswa.kelas')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%' . $request->input('q') . '%';
                $query->whereHas('siswa', fn ($s) => $s->where('nama', 'like', $q)->orWhere('nis', 'like', $q));
            })
            ->when($request->filled('status_anggota'), fn ($query) => $query->where('status', $request->input('status_anggota')))
            ->whereNotIn('status', [Pendaftaran::STATUS_KELUAR])
            ->orderBy('status')
            ->paginate(20)
            ->withQueryString();

        return view('kesiswaan.ekskuls.show', compact('ekskul', 'anggota'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'pembina_id'  => ['nullable', 'exists:pembinas,id'],
            'deskripsi'   => ['nullable', 'string'],
            'jadwal'      => ['nullable', 'string', 'max:255'],
        ]);

        Ekskul::create($data);

        return back()->with('success', "Ekskul {$data['nama_ekskul']} berhasil ditambahkan. Pembina dapat ditugaskan di halaman Data Pembina.");
    }

    public function update(Request $request, Ekskul $ekskul): RedirectResponse
    {
        $data = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'pembina_id' => ['required', 'exists:pembinas,id'],
            'deskripsi' => ['nullable', 'string'],
            'jadwal' => ['nullable', 'string', 'max:255'],
        ]);

        if ($ekskul->pembina_id != $data['pembina_id']) {
            $pembina = Pembina::findOrFail($data['pembina_id']);
            $pembinaEkskulCount = Ekskul::where('pembina_id', $pembina->id)->count();
            if ($pembinaEkskulCount >= 4) {
                return back()->withErrors([
                    'pembina_id' => "Pembina {$pembina->nama} sudah membina 4 ekskul (batas maksimal 4 ekskul per pembina)."
                ])->withInput();
            }
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
