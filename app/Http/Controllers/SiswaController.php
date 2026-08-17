<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SiswaController extends Controller
{
    public function index()
    {
        $siswas = Siswa::with('kelas')->get();
        return view('kesiswaan.siswa.index', compact('siswas'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('kesiswaan.siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis'             => 'required|string|unique:siswas,nis',
            'nama'            => 'required|string|max:255',
            'kelas_id'        => 'required|exists:kelas,id',
            'jenis_kelamin'   => 'required|in:laki-laki,perempuan',
            'email'           => 'required|email|unique:users,email',
        ]);

        $user = User::create([
            'username' => $validated['nis'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['nis']),
            'role'     => 'siswa',
        ]);

        $user->siswa()->create([
            'nis'             => $validated['nis'],
            'nama'            => $validated['nama'],
            'kelas_id'        => $validated['kelas_id'],
            'jenis_kelamin'   => $validated['jenis_kelamin'],
        ]);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function show(Siswa $siswa)
    {
        $siswa->load('kelas', 'pendaftarans.ekskul');
        return view('kesiswaan.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        return view('kesiswaan.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nama'            => 'required|string|max:255',
            'kelas_id'        => 'required|exists:kelas,id',
            'jenis_kelamin'   => 'required|in:laki-laki,perempuan',
            'jabatan'         => 'required|in:siswa,anggota,ketua',
        ]);

        $siswa->update($validated);

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil diupdate.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->user->delete();
        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil dihapus.');
    }
}