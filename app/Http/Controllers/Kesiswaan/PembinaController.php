<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Pembina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembinaController extends Controller
{
    private const SORTABLE = ['nama', 'nip', 'jenis_kelamin', 'created_at'];

    public function index(Request $request): View
    {
        $sort      = in_array($request->input('sort'), self::SORTABLE, true)
            ? $request->input('sort')
            : 'nama';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $pembinas = Pembina::with(['ekskuls'])
            ->withCount('ekskuls')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%' . $request->input('q') . '%';
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama', 'like', $q)
                        ->orWhere('nip', 'like', $q)
                        ->orWhere('email', 'like', $q)
                        ->orWhere('no_telp', 'like', $q);
                });
            })
            ->when($request->filled('jenis_kelamin'), fn ($query) => $query->where('jenis_kelamin', $request->input('jenis_kelamin')))
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        $ekskulList = Ekskul::orderBy('nama_ekskul')->get();

        return view('kesiswaan.pembina.index', [
            'pembinas'   => $pembinas,
            'ekskulList' => $ekskulList,
            'sort'       => $sort,
            'direction'  => $direction,
        ]);
    }

    public function assignEkskul(Request $request, Pembina $pembina): RedirectResponse
    {
        $data = $request->validate([
            'ekskul_id' => ['required', 'exists:ekskuls,id'],
        ]);

        $ekskul = Ekskul::findOrFail($data['ekskul_id']);

        if ($ekskul->pembina_id !== null) {
            return back()->withErrors(['ekskul_id' => "Ekskul {$ekskul->nama_ekskul} sudah memiliki pembina."]);
        }

        // Cek batas maksimal 4 ekskul per pembina
        if ($pembina->ekskuls()->count() >= 4) {
            return back()->withErrors(['ekskul_id' => "Pembina {$pembina->nama} sudah membina 4 ekskul (batas maksimal)."]);
        }

        // Assign pembina ke ekskul
        $ekskul->update(['pembina_id' => $pembina->id]);

        return back()->with('success', "Ekskul {$ekskul->nama_ekskul} berhasil ditugaskan ke {$pembina->nama}.");
    }

    public function removeEkskul(Pembina $pembina, Ekskul $ekskul): RedirectResponse
    {
        if ($ekskul->pembina_id !== $pembina->id) {
            return back()->withErrors(['ekskul_id' => 'Ekskul ini tidak dibina oleh pembina tersebut.']);
        }

        $ekskul->update(['pembina_id' => null]);

        return back()->with('success', "Ekskul {$ekskul->nama_ekskul} berhasil dilepas dari {$pembina->nama}.");
    }

}
