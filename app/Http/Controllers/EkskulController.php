<?php

namespace App\Http\Controllers;

use App\Models\Ekskul;
use App\Models\Pembina;
use App\Models\Pelatih;
use Illuminate\Http\Request;

class EkskulController extends Controller
{
    public function index()
    {
        $ekskuls = Ekskul::with(['pembina', 'pelatih'])->get();
        return view('kesiswaan.ekskul.index', compact('ekskuls'));
    }

    public function create()
    {
        $pembinas = Pembina::all();
        $pelatihs = Pelatih::all();
        return view('kesiswaan.ekskul.create', compact('pembinas', 'pelatihs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pembina_id'            => 'required|exists:pembinas,id',
            'pelatih_id'            => 'required|exists:pelatihs,id',
            'nama_ekskul'           => 'required|string|max:255',
            'deskripsi'             => 'nullable|string',
            'jadwal'                => 'nullable|string',
            'is_open_recruitment'   => 'boolean',
        ]);

        Ekskul::create($validated);

        return redirect()->route('ekskul.index')->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
    }

    public function show(Ekskul $ekskul)
    {
        $ekskul->load(['pembina', 'pelatih', 'pendaftarans.siswa', 'kegiatans']);
        return view('kesiswaan.ekskul.show', compact('ekskul'));
    }

    public function edit(Ekskul $ekskul)
    {
        $pembinas = Pembina::all();
        $pelatihs = Pelatih::all();
        return view('kesiswaan.ekskul.edit', compact('ekskul', 'pembinas', 'pelatihs'));
    }

    public function update(Request $request, Ekskul $ekskul)
    {
        $validated = $request->validate([
            'pembina_id'            => 'required|exists:pembinas,id',
            'pelatih_id'            => 'required|exists:pelatihs,id',
            'nama_ekskul'           => 'required|string|max:255',
            'deskripsi'             => 'nullable|string',
            'jadwal'                => 'nullable|string',
            'is_open_recruitment'   => 'boolean',
        ]);

        $ekskul->update($validated);

        return redirect()->route('ekskul.index')->with('success', 'Ekstrakurikuler berhasil diupdate.');
    }

    public function destroy(Ekskul $ekskul)
    {
        $ekskul->delete();

        return redirect()->route('ekskul.index')->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}