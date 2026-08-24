<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Pembina;
use App\Models\Pelatih;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EkskulController extends Controller
{
    public function index(Request $request): View
    {
        $ekskuls = Ekskul::with(['pembina.user', 'pelatih'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where('nama_ekskul', 'like', "%{$q}%")
                    ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$q}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('kesiswaan.ekskuls.index', [
            'ekskuls' => $ekskuls,
            'pembinas' => Pembina::with('user')->orderBy('nama')->get(),
            'pelatihs' => Pelatih::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'pembina_id' => ['required', 'exists:pembinas,id'],
            'pelatih_id' => ['required', 'exists:pelatihs,id'],
            'deskripsi' => ['nullable', 'string'],
            'jadwal' => ['nullable', 'string', 'max:255'],
            'is_open_recruitment' => ['nullable', 'boolean'],
        ]);

        Ekskul::create($data);

        return back()->with('success', "Ekskul {$data['nama_ekskul']} berhasil ditambahkan.");
    }

    public function update(Request $request, Ekskul $ekskul): RedirectResponse
    {
        $data = $request->validate([
            'nama_ekskul' => ['required', 'string', 'max:255'],
            'pembina_id' => ['required', 'exists:pembinas,id'],
            'pelatih_id' => ['required', 'exists:pelatihs,id'],
            'deskripsi' => ['nullable', 'string'],
            'jadwal' => ['nullable', 'string', 'max:255'],
            'is_open_recruitment' => ['nullable', 'boolean'],
        ]);

        $ekskul->update($data);

        return back()->with('success', "Ekskul {$ekskul->nama_ekskul} berhasil diperbarui.");
    }

    public function destroy(Ekskul $ekskul): RedirectResponse
    {
        $nama = $ekskul->nama_ekskul;
        $ekskul->delete();

        return back()->with('success', "Ekskul {$nama} berhasil dihapus.");
    }
}
