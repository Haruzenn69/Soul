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
        $sort = in_array($request->input('sort'), self::SORTABLE, true)
            ? $request->input('sort')
            : 'nama';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $pembinas = Pembina::with(['ekskuls'])
            ->withCount('ekskuls')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->input('q').'%';
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama', 'like', $q)
                        ->orWhere('nip', 'like', $q)
                        ->orWhere('email', 'like', $q)
                        ->orWhere('no_telp', 'like', $q);
                });
            })
            ->when($request->filled('jenis_kelamin'), fn ($query) => $query->where('jenis_kelamin', $request->input('jenis_kelamin')))
            ->when(in_array($request->input('kategori'), ['Olahraga', 'Seni', 'Bahasa', 'Lainnya'], true), function ($query) use ($request) {
                $kat = $request->input('kategori');
                $query->where(function ($sub) use ($kat) {
                    $sub->whereHas('ekskuls', fn ($e) => $e->where('kategori', $kat))
                        ->orWhere(function ($q2) use ($kat) {
                            $q2->doesntHave('ekskuls')
                                ->where('kategori_pernah_dibina', $kat);
                        });
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        $ekskulList = Ekskul::orderBy('nama_ekskul')->get();

        return view('kesiswaan.pembina.index', [
            'pembinas' => $pembinas,
            'ekskulList' => $ekskulList,
            'sort' => $sort,
            'direction' => $direction,
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

        $kategori = $ekskul->kategori;

        $namaPembinaLower = strtolower(trim($pembina->nama ?? ''));
        if (str_contains($namaPembinaLower, 'nurianti') || str_contains($namaPembinaLower, 'nuri anti') || str_contains($namaPembinaLower, 'bu nuri')) {
            return back()->withErrors(['ekskul_id' => 'Bu Nurianti tidak diperbolehkan membina ekskul.']);
        }

        if ($kategori === 'Olahraga') {
            if (! (str_contains($namaPembinaLower, 'ahmad') && str_contains($namaPembinaLower, 'pak'))) {
                return back()->withErrors(['ekskul_id' => 'Kategori Olahraga hanya boleh dibina oleh Pak Ahmad.']);
            }
        }

        $pembinaLainKategori = Pembina::whereHas('ekskuls', fn ($q) => $q->where('kategori', $kategori))
            ->where('id', '!=', $pembina->id)
            ->exists();
        if ($pembinaLainKategori) {
            return back()->withErrors(['ekskul_id' => "Kategori {$kategori} sudah dibina oleh pembina lain. Setiap kategori hanya boleh memiliki 1 pembina."]);
        }

        $existingKategori = $pembina->ekskuls->first()?->kategori;
        if ($existingKategori && $kategori && $existingKategori !== $kategori) {
            return back()->withErrors([
                'ekskul_id' => "Pembina {$pembina->nama} membina kategori {$existingKategori}. Hanya dapat membina ekskul dengan kategori yang sama ({$existingKategori}).",
            ]);
        }

        $ekskul->update(['pembina_id' => $pembina->id]);

        if ($kategori) {
            $pembina->update(['kategori_pernah_dibina' => $kategori]);
        }

        return back()->with('success', "Ekskul {$ekskul->nama_ekskul} ({$kategori}) berhasil ditugaskan ke {$pembina->nama}.");
    }

    public function removeEkskul(Pembina $pembina, Ekskul $ekskul): RedirectResponse
    {
        if ($ekskul->pembina_id !== $pembina->id) {
            return back()->withErrors(['ekskul_id' => 'Ekskul ini tidak dibina oleh pembina tersebut.']);
        }

        // Simpan kategori terakhir yang pernah dibina
        if ($ekskul->kategori) {
            $pembina->update(['kategori_pernah_dibina' => $ekskul->kategori]);
        }

        $ekskul->update(['pembina_id' => null]);

        return back()->with('success', "Ekskul {$ekskul->nama_ekskul} berhasil dilepas dari {$pembina->nama}. Status kategori tercatat sebagai riwayat.");
    }

    public function riwayatProfil(Pembina $pembina): View
    {
        $riwayat = $pembina->profileHistories()
            ->with('changedBy.pembina')
            ->paginate(30);

        return view('kesiswaan.pembina.riwayat-profil', compact('pembina', 'riwayat'));
    }
}
