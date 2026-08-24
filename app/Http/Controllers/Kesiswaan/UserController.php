<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pembina;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::with(['siswa.kelas', 'pembina'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where(function ($sub) use ($q) {
                    $sub->where('username', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhereHas('siswa', fn ($s) => $s->where('nama', 'like', "%{$q}%"))
                        ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->input('role')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('kesiswaan.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('kesiswaan.users.create', [
            'kelas' => Kelas::with('tahunAjaran')->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            // Role admin hanya bisa dibuat oleh admin (kesiswaan tidak boleh)
            'role' => ['required', 'in:'.$this->allowedRoles()],
        ];

        if ($request->input('role') === 'siswa') {
            $rules += [
                'nis' => ['required', 'string', 'max:255', 'unique:siswas,nis'],
                'nama' => ['required', 'string', 'max:255'],
                'kelas_id' => ['required', 'exists:kelas,id'],
                'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
                'jabatan' => ['required', 'in:siswa,anggota,ketua'],
            ];
        } elseif ($request->input('role') === 'pembina') {
            $rules += [
                'nip' => ['required', 'string', 'max:255', 'unique:pembinas,nip'],
                'pembina_nama' => ['required', 'string', 'max:255'],
                'pembina_jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            ];
        }

        $data = $request->validate($rules);

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make('password'),
            'role' => $data['role'],
            'email_verified_at' => now(),
        ]);

        if ($data['role'] === 'siswa') {
            $user->siswa()->create([
                'nis' => $data['nis'],
                'nama' => $data['nama'],
                'kelas_id' => $data['kelas_id'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'jabatan' => $data['jabatan'],
            ]);
        } elseif ($data['role'] === 'pembina') {
            $user->pembina()->create([
                'nip' => $data['nip'],
                'nama' => $data['pembina_nama'],
                'jenis_kelamin' => $data['pembina_jenis_kelamin'],
            ]);
        }

        return redirect()
            ->route('kesiswaan.users.index')
            ->with('success', "Akun {$user->username} berhasil dibuat dengan password default: password");
    }

    public function edit(User $user): View
    {
        $this->authorizeManage($user);
        $user->load(['siswa.kelas', 'pembina']);

        return view('kesiswaan.users.edit', [
            'user' => $user,
            'kelas' => Kelas::with('tahunAjaran')->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManage($user);

        $rules = [
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            // Role admin hanya bisa dipilih oleh admin (kesiswaan tidak boleh)
            'role' => ['required', 'in:'.$this->allowedRoles()],
        ];

        if ($request->input('role') === 'siswa') {
            $nisIgnore = $user->siswa ? ','.$user->siswa->id : '';
            $rules += [
                'nis' => ['required', 'string', 'max:255', 'unique:siswas,nis'.$nisIgnore],
                'nama' => ['required', 'string', 'max:255'],
                'kelas_id' => ['required', 'exists:kelas,id'],
                'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
                'jabatan' => ['required', 'in:siswa,anggota,ketua'],
            ];
        } elseif ($request->input('role') === 'pembina') {
            $nipIgnore = $user->pembina ? ','.$user->pembina->id : '';
            $rules += [
                'nip' => ['required', 'string', 'max:255', 'unique:pembinas,nip'.$nipIgnore],
                'pembina_nama' => ['required', 'string', 'max:255'],
                'pembina_jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            ];
        }

        $data = $request->validate($rules);

        $user->update([
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);

        if ($data['role'] === 'siswa') {
            Siswa::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nis' => $data['nis'],
                    'nama' => $data['nama'],
                    'kelas_id' => $data['kelas_id'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'jabatan' => $data['jabatan'],
                ]
            );
            $user->pembina()->delete();
        } elseif ($data['role'] === 'pembina') {
            Pembina::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $data['nip'],
                    'nama' => $data['pembina_nama'],
                    'jenis_kelamin' => $data['pembina_jenis_kelamin'],
                ]
            );
            $user->siswa()->delete();
        } else {
            $user->siswa()->delete();
            $user->pembina()->delete();
        }

        return redirect()
            ->route('kesiswaan.users.index')
            ->with('success', "Akun {$user->username} berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeManage($user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $username = $user->username;
        $user->delete();

        return redirect()
            ->route('kesiswaan.users.index')
            ->with('success', "Akun {$username} berhasil dihapus.");
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $this->authorizeManage($user);

        $user->update(['password' => Hash::make('password')]);

        return back()->with('success', "Password akun {$user->username} direset ke: password");
    }

    /**
     * Role admin hanya bisa dikelola oleh admin (programmer).
     */
    private function authorizeManage(User $target): void
    {
        if (auth()->user()->role !== 'admin' && $target->role === 'admin') {
            abort(403, 'Akun admin hanya dapat dikelola oleh admin.');
        }
    }

    private function allowedRoles(): string
    {
        // Kesiswaan tidak bisa membuat akun admin — admin adalah role programmer.
        return auth()->user()->role === 'admin'
            ? 'admin,kesiswaan,pembina,siswa'
            : 'kesiswaan,pembina,siswa';
    }
}
