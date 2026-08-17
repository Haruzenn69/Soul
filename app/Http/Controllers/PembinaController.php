<?php

namespace App\Http\Controllers;

use App\Models\Pembina;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PembinaController extends Controller
{
    public function index()
    {
        $pembinas = Pembina::all();
        return view('kesiswaan.pembina.index', compact('pembinas'));
    }

    public function create()
    {
        return view('kesiswaan.pembina.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip'             => 'required|string|unique:pembinas,nip',
            'nama'            => 'required|string|max:255',
            'jenis_kelamin'   => 'required|in:laki-laki,perempuan',
            'email'           => 'required|email|unique:users,email',
        ]);

        $user = User::create([
            'username' => $validated['nip'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['nip']),
            'role'     => 'pembina',
        ]);

        $user->pembina()->create([
            'nip'             => $validated['nip'],
            'nama'            => $validated['nama'],
            'jenis_kelamin'   => $validated['jenis_kelamin'],
        ]);

        return redirect()->route('pembina.index')->with('success', 'Pembina berhasil ditambahkan.');
    }

    public function show(Pembina $pembina)
    {
        $pembina->load('ekskuls');
        return view('kesiswaan.pembina.show', compact('pembina'));
    }

    public function edit(Pembina $pembina)
    {
        return view('kesiswaan.pembina.edit', compact('pembina'));
    }

    public function update(Request $request, Pembina $pembina)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'jenis_kelamin'   => 'required|in:laki-laki,perempuan',
        ]);

        $pembina->update($validated);

        return redirect()->route('pembina.index')->with('success', 'Pembina berhasil diupdate.');
    }

    public function destroy(Pembina $pembina)
    {
        $pembina->user->delete();
        $pembina->delete();

        return redirect()->route('pembina.index')->with('success', 'Pembina berhasil dihapus.');
    }
}