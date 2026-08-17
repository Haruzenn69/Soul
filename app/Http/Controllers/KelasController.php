<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('tahunAjaran')->get();
        return view('kesiswaan.kelas.index', compact('kelas'));
    }

    public function create()
    {
        $tahunAjarans = TahunAjaran::all();
        return view('kesiswaan.kelas.create', compact('tahunAjarans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'tingkat'           => 'required|in:x,xi,xii',
            'tahun_ajaran_id'   => 'required|exists:tahun_ajarans,id',
        ]);

        Kelas::create($validated);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function show(Kelas $kelas)
    {
        $kelas->load('siswas', 'tahunAjaran');
        return view('kesiswaan.kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kelas)
    {
        $tahunAjarans = TahunAjaran::all();
        return view('kesiswaan.kelas.edit', compact('kelas', 'tahunAjarans'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama'              => 'required|string|max:255',
            'tingkat'           => 'required|in:x,xi,xii',
            'tahun_ajaran_id'   => 'required|exists:tahun_ajarans,id',
        ]);

        $kelas->update($validated);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diupdate.');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }
}