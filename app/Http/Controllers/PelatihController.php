<?php

namespace App\Http\Controllers;

use App\Models\Pelatih;
use Illuminate\Http\Request;

class PelatihController extends Controller
{
    public function index()
    {
        $pelatihs = Pelatih::all();
        return view('kesiswaan.pelatih.index', compact('pelatihs'));
    }

    public function create()
    {
        return view('kesiswaan.pelatih.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'jenis_kelamin'   => 'required|in:laki-laki,perempuan',
            'no_hp'           => 'required|string|max:12',
        ]);

        Pelatih::create($validated);

        return redirect()->route('pelatih.index')->with('success', 'Pelatih berhasil ditambahkan.');
    }

    public function show(Pelatih $pelatih)
    {
        $pelatih->load('ekskuls');
        return view('kesiswaan.pelatih.show', compact('pelatih'));
    }

    public function edit(Pelatih $pelatih)
    {
        return view('kesiswaan.pelatih.edit', compact('pelatih'));
    }

    public function update(Request $request, Pelatih $pelatih)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'jenis_kelamin'   => 'required|in:laki-laki,perempuan',
            'no_hp'           => 'required|string|max:12',
            'status'          => 'required|in:aktif,nonaktif',
        ]);

        $pelatih->update($validated);

        return redirect()->route('pelatih.index')->with('success', 'Pelatih berhasil diupdate.');
    }

    public function destroy(Pelatih $pelatih)
    {
        $pelatih->delete();

        return redirect()->route('pelatih.index')->with('success', 'Pelatih berhasil dihapus.');
    }
}<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PelatihController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
