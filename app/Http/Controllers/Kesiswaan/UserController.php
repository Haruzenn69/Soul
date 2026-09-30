<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Exports\PembinaTemplateExport;
use App\Exports\SiswaTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\PembinaImport;
use App\Imports\SiswaImport;
use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Pembina;
use App\Models\Pendaftaran;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\Failure;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $roleFilter = $request->input('role');
        if ($roleFilter === 'guru') {
            $roleFilter = 'pembina';
        }

        $users = User::with(['siswa.kelas', 'pembina.ekskuls'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where(function ($sub) use ($q) {
                    $sub->where('username', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhereHas('siswa', fn ($s) => $s->where('nama', 'like', "%{$q}%")->orWhere('nis', 'like', "%{$q}%"))
                        ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$q}%")->orWhere('nip', 'like', "%{$q}%"));
                });
            })
            ->when($roleFilter, function ($query) use ($roleFilter) {
                if ($roleFilter === 'staff') {
                    $query->whereIn('role', ['kesiswaan', 'admin']);
                } else {
                    $query->where('role', $roleFilter);
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'all' => User::count(),
            'siswa' => User::where('role', 'siswa')->count(),
            'guru' => User::where('role', 'pembina')->count(),
            'staff' => User::whereIn('role', ['kesiswaan', 'admin'])->count(),
        ];

        return view('kesiswaan.users.index', compact('users', 'counts', 'roleFilter'));
    }

    public function templateSiswa()
    {
        return Excel::download(new SiswaTemplateExport, 'template-akun-siswa.xlsx');
    }

    public function templatePembina()
    {
        return Excel::download(new PembinaTemplateExport, 'template-akun-pembina.xlsx');
    }

    public function importPage(): View
    {
        $kelas = Kelas::with('tahunAjaran')->orderBy('nama')->get();

        return view('kesiswaan.users.import', compact('kelas'));
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:siswa,pembina'],
            'kelas_id' => ['required_if:jenis,siswa', 'nullable', 'integer', 'exists:kelas,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ], [
            'jenis.required' => 'Pilih jenis akun (siswa/pembina) terlebih dahulu.',
            'kelas_id.required' => 'Pilih kelas untuk akun siswa.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid.',
            'file.required' => 'File excel wajib diunggah.',
            'file.mimes' => 'File harus berupa .xlsx, .xls, atau .csv.',
        ]);

        $import = $validated['jenis'] === 'siswa'
            ? new SiswaImport((int) $validated['kelas_id'])
            : new PembinaImport;

        Excel::import($import, $request->file('file'));

        $count = $import->getCount();
        $jenis = $validated['jenis'] === 'siswa' ? 'siswa' : 'pembina';

        $errors = collect($import->failures())
            ->map(function (Failure $failure) use ($jenis) {
                $messages = collect($failure->errors())->map(function (string $error) use ($jenis) {
                    if (str_contains(strtolower($error), 'the nip field is required')) {
                        return 'Kolom NIP kosong atau tidak ditemukan. Pastikan jenis akun Pembina dipilih dan gunakan template Pembina.';
                    }

                    if (str_contains(strtolower($error), 'the nip field format is invalid')) {
                        return 'Periksa kolom NIP pada file. NIP harus terdiri dari tepat 18 angka.';
                    }

                    if (str_contains(strtolower($error), 'the nip field must be 18 digits')) {
                        return 'Periksa kolom NIP pada file. NIP harus terdiri dari tepat 18 angka.';
                    }

                    if (str_contains(strtolower($error), 'the nis field is required')) {
                        return 'Kolom NIS kosong atau tidak ditemukan. Pastikan jenis akun Siswa dipilih dan gunakan template Siswa.';
                    }

                    if (str_contains(strtolower($error), 'the nis field format is invalid')) {
                        return 'Periksa kolom NIS pada file. NIS harus terdiri dari tepat 10 angka.';
                    }

                    if (str_contains(strtolower($error), 'the nis field must be 10 digits')) {
                        return 'Periksa kolom NIS pada file. NIS harus terdiri dari tepat 10 angka.';
                    }

                    if (str_contains(strtolower($error), 'the nama field is required')) {
                        return 'Kolom Nama wajib diisi.';
                    }

                    if (str_contains(strtolower($error), 'the username field is required')) {
                        return 'Kolom Username wajib diisi. Username digunakan untuk login dan harus berbeda dari NIP.';
                    }

                    if (str_contains(strtolower($error), 'the username and nip must be different')) {
                        return 'Username harus berbeda dari NIP. Isi username login pada kolom Username.';
                    }

                    if (str_contains(strtolower($error), 'the jabatan field is required')) {
                        return 'Kolom Jabatan wajib diisi dengan "siswa" atau "ketua".';
                    }

                    if (str_contains(strtolower($error), 'the jabatan field')) {
                        return 'Jabatan tidak valid. Isi dengan "siswa" atau "ketua".';
                    }

                    return $error;
                });

                return 'Baris '.$failure->row().': '.$messages->implode(' ');
            })
            ->values()
            ->all();

        if ($count === 0) {
            $message = 'Tidak ada akun yang berhasil diimport. Periksa kecocokan jenis akun dengan template dan lengkapi kolom yang ditandai di bawah.';

            return back()
                ->with('error', $message)
                ->with('import_errors', $errors)
                ->with('import_jenis', $jenis);
        }

        $message = "Berhasil import {$count} akun {$jenis}. Password default semua akun: password.";

        if ($errors) {
            $message .= ' Sebagian baris dilewati karena tidak valid:';
        }

        return back()
            ->with('success', $message)
            ->with('import_errors', $errors)
            ->with('import_jenis', $jenis);
    }

    public function create(): View
    {
        return view('kesiswaan.users.create', [
            'kelas' => Kelas::with('tahunAjaran')->orderBy('nama')->get(),
            'ekskuls' => $this->getEskulsWithKetuaStatus(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $isStaff = in_array($request->input('role'), ['admin', 'kesiswaan']);

        $isPembina = $request->input('role') === 'pembina';

        $rules = [
            'username' => [$isStaff ? 'required' : 'nullable', 'string', 'max:255', 'unique:users,username'],
            'email'    => [$isPembina ? 'nullable' : 'required', 'nullable', 'email', 'max:255', 'unique:users,email'],
            // Role admin hanya bisa dibuat oleh admin (kesiswaan tidak boleh)
            'role'     => ['required', 'in:' . $this->allowedRoles()],
        ];

        if ($request->input('role') === 'siswa') {
            $rules += [
                'nis' => ['required', 'string', 'digits:10', 'unique:siswas,nis'],
                'nama' => ['required', 'string', 'max:255'],
                'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
                'tempat_lahir' => ['nullable', 'string', 'max:100'],
                'tanggal_lahir' => ['nullable', 'date'],
                'agama' => ['nullable', 'string', 'max:50'],
                'kelas_id' => ['required', 'exists:kelas,id'],
                'angkatan' => ['nullable', 'string', 'max:20'],
                'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
                'no_telp' => ['nullable', 'string', 'max:25'],
                'alamat' => ['nullable', 'string'],
                'medsos' => ['nullable', 'string', 'max:255'],
                'jabatan' => ['required', 'in:siswa,anggota,ketua'],
            ];
            if ($request->input('jabatan') === 'ketua') {
                $rules['ekskul_id'] = ['required', 'exists:ekskuls,id'];
            }
        } elseif ($request->input('role') === 'pembina') {
            $rules += [
                'pembina_nama' => ['required', 'string', 'max:255'],
                'pembina_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
                'nip'          => ['nullable', 'string', 'digits:18', 'unique:pembinas,nip'],
                'pembina_tempat_lahir'    => ['nullable', 'string', 'max:100'],
                'pembina_tanggal_lahir'   => ['nullable', 'date'],
                'pembina_agama'           => ['nullable', 'string', 'max:50'],
                'pembina_jenis_kelamin'   => ['nullable', 'in:laki-laki,perempuan'],
                'pembina_no_telp'         => ['nullable', 'string', 'max:25'],
                'pembina_alamat'          => ['nullable', 'string'],
                'pembina_medsos'          => ['nullable', 'string', 'max:255'],
            ];
        }

        $data = $request->validate($rules, [
            'nis.digits' => 'Periksa NIS. NIS harus terdiri dari tepat 10 angka.',
            'nip.digits' => 'Periksa NIP. NIP harus terdiri dari tepat 18 angka.',
            'foto.image' => 'File foto siswa harus berupa gambar.',
            'foto.max' => 'Ukuran foto siswa maksimal 2MB.',
            'pembina_foto.image' => 'File foto pembina harus berupa gambar.',
            'pembina_foto.max' => 'Ukuran foto pembina maksimal 2MB.',
        ]);

        $user = DB::transaction(function () use ($data, $request, $isStaff) {
            if ($data['role'] === 'siswa' && $data['jabatan'] === 'ketua') {
                $this->ensureEkskulCanHaveKetua((int) $data['ekskul_id']);
            }

            $user = User::create([
                'username' => ($isStaff && !empty($data['username'])) ? $data['username'] : null,
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => $data['role'],
                'email_verified_at' => now(),
            ]);

            if ($data['role'] === 'siswa') {
                $fotoPath = $request->hasFile('foto')
                    ? $request->file('foto')->store('profile-photos', 'public')
                    : null;

                $siswa = $user->siswa()->create([
                    'nis' => $data['nis'],
                    'nama' => $data['nama'],
                    'foto' => $fotoPath,
                    'tempat_lahir' => $data['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                    'agama' => $data['agama'] ?? null,
                    'kelas_id' => $data['kelas_id'],
                    'angkatan' => $data['angkatan'] ?? null,
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'email' => $data['email'],
                    'no_telp' => $data['no_telp'] ?? null,
                    'alamat' => $data['alamat'] ?? null,
                    'medsos' => $data['medsos'] ?? null,
                    'jabatan' => $data['jabatan'],
                ]);

                if ($data['jabatan'] === 'ketua') {
                    Pendaftaran::create([
                        'siswa_id' => $siswa->id,
                        'ekskul_id' => $data['ekskul_id'],
                        'tanggal_daftar' => now()->toDateString(),
                        'status' => Pendaftaran::STATUS_DITERIMA,
                    ]);
                }
            } elseif ($data['role'] === 'pembina') {
                $pembinaFotoPath = $request->hasFile('pembina_foto')
                    ? $request->file('pembina_foto')->store('profile-photos', 'public')
                    : null;

                $user->pembina()->create([
                    'nip'           => $data['nip'] ?? null,
                    'nama'          => $data['pembina_nama'],
                    'foto'          => $pembinaFotoPath,
                    'tempat_lahir'  => $data['pembina_tempat_lahir'] ?? null,
                    'tanggal_lahir' => $data['pembina_tanggal_lahir'] ?? null,
                    'agama'         => $data['pembina_agama'] ?? null,
                    'jenis_kelamin' => $data['pembina_jenis_kelamin'] ?? null,
                    'email'         => $data['email'] ?? null,
                    'no_telp'       => $data['pembina_no_telp'] ?? null,
                    'alamat'        => $data['pembina_alamat'] ?? null,
                    'medsos'        => $data['pembina_medsos'] ?? null,
                ]);
            }

            return $user;
        });

        return redirect()
            ->route('kesiswaan.users.index')
            ->with('success', $user->username
                ? "Akun {$user->username} berhasil dibuat dengan password default: password"
                : "Akun berhasil dibuat. Siswa/Pembina akan mengisi username secara mandiri saat login pertama kali. Password default: password");
    }

    public function edit(User $user): View
    {
        $this->authorizeManage($user);
        $user->load(['siswa.kelas', 'pembina', 'siswa.pendaftarans' => fn ($q) => $q->where('status', 'diterima')]);

        return view('kesiswaan.users.edit', [
            'user' => $user,
            'kelas' => Kelas::with('tahunAjaran')->orderBy('nama')->get(),
            'ekskuls' => $this->getEskulsWithKetuaStatus($user->id),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManage($user);

        $isStaff = in_array($request->input('role'), ['admin', 'kesiswaan']);

        $rules = [
            'username' => [$isStaff ? 'required' : 'nullable', 'string', 'max:255', 'unique:users,username,'.$user->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            // Role admin hanya bisa dipilih oleh admin (kesiswaan tidak boleh)
            'role' => ['required', 'in:'.$this->allowedRoles()],
        ];

        if ($request->input('role') === 'siswa') {
            $nisIgnore = $user->siswa ? ','.$user->siswa->id : '';
            $rules += [
                'nis' => ['required', 'string', 'digits:10', 'unique:siswas,nis'.$nisIgnore],
                'nama' => ['required', 'string', 'max:255'],
                'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
                'tempat_lahir' => ['nullable', 'string', 'max:100'],
                'tanggal_lahir' => ['nullable', 'date'],
                'agama' => ['nullable', 'string', 'max:50'],
                'kelas_id' => ['required', 'exists:kelas,id'],
                'angkatan' => ['nullable', 'string', 'max:20'],
                'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
                'no_telp' => ['nullable', 'string', 'max:25'],
                'alamat' => ['nullable', 'string'],
                'medsos' => ['nullable', 'string', 'max:255'],
                'jabatan' => ['required', 'in:siswa,anggota,ketua'],
            ];
            if ($request->input('jabatan') === 'ketua') {
                $rules['ekskul_id'] = ['required', 'exists:ekskuls,id'];
            }
        } elseif ($request->input('role') === 'pembina') {
            $nipIgnore = $user->pembina ? ','.$user->pembina->id : '';
            $rules += [
                'nip' => ['required', 'string', 'digits:18', 'unique:pembinas,nip'.$nipIgnore],
                'pembina_nama' => ['required', 'string', 'max:255'],
                'pembina_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
                'pembina_tempat_lahir' => ['nullable', 'string', 'max:100'],
                'pembina_tanggal_lahir' => ['nullable', 'date'],
                'pembina_agama' => ['nullable', 'string', 'max:50'],
                'pembina_jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
                'pembina_no_telp' => ['nullable', 'string', 'max:25'],
                'pembina_alamat' => ['nullable', 'string'],
                'pembina_medsos' => ['nullable', 'string', 'max:255'],
            ];
        }

        $data = $request->validate($rules, [
            'nis.digits' => 'Periksa NIS. NIS harus terdiri dari tepat 10 angka.',
            'nip.digits' => 'Periksa NIP. NIP harus terdiri dari tepat 18 angka.',
            'foto.image' => 'File foto siswa harus berupa gambar.',
            'foto.max' => 'Ukuran foto siswa maksimal 2MB.',
            'pembina_foto.image' => 'File foto pembina harus berupa gambar.',
            'pembina_foto.max' => 'Ukuran foto pembina maksimal 2MB.',
        ]);

        DB::transaction(function () use ($user, $data, $request) {
            $existingSiswa = $user->siswa;
            $existingPembina = $user->pembina;
            $wasKetua = $existingSiswa?->jabatan === 'ketua';

            if ($data['role'] === 'siswa' && $data['jabatan'] === 'ketua') {
                $this->ensureEkskulCanHaveKetua((int) $data['ekskul_id'], $existingSiswa?->id);
            }

            $user->update([
                'username' => filled($data['username']) ? $data['username'] : null,
                'email' => $data['email'],
                'role' => $data['role'],
            ]);

            if ($data['role'] === 'siswa') {
                $siswaData = [
                    'nis' => $data['nis'],
                    'nama' => $data['nama'],
                    'tempat_lahir' => $data['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                    'agama' => $data['agama'] ?? null,
                    'kelas_id' => $data['kelas_id'],
                    'angkatan' => $data['angkatan'] ?? null,
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'email' => $data['email'],
                    'no_telp' => $data['no_telp'] ?? null,
                    'alamat' => $data['alamat'] ?? null,
                    'medsos' => $data['medsos'] ?? null,
                    'jabatan' => $data['jabatan'],
                ];

                if ($request->hasFile('foto')) {
                    if ($existingSiswa?->foto && Storage::disk('public')->exists($existingSiswa->foto)) {
                        Storage::disk('public')->delete($existingSiswa->foto);
                    }
                    $siswaData['foto'] = $request->file('foto')->store('profile-photos', 'public');
                }

                $siswa = Siswa::updateOrCreate(
                    ['user_id' => $user->id],
                    $siswaData
                );

                if ($existingPembina) {
                    if ($existingPembina->foto && Storage::disk('public')->exists($existingPembina->foto)) {
                        Storage::disk('public')->delete($existingPembina->foto);
                    }
                    $existingPembina->delete();
                }

                if ($data['jabatan'] === 'ketua') {
                    $target = Pendaftaran::where('siswa_id', $siswa->id)
                        ->where('ekskul_id', $data['ekskul_id'])
                        ->latest('id')
                        ->first();

                    Pendaftaran::where('siswa_id', $siswa->id)
                        ->when($target, fn ($query) => $query->where('id', '!=', $target->id))
                        ->whereIn('status', [Pendaftaran::STATUS_PENDING, Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                        ->update(['status' => Pendaftaran::STATUS_NONAKTIF]);

                    if ($target) {
                        $target->update(['status' => Pendaftaran::STATUS_DITERIMA]);
                    } else {
                        Pendaftaran::create([
                            'siswa_id' => $siswa->id,
                            'ekskul_id' => $data['ekskul_id'],
                            'tanggal_daftar' => now()->toDateString(),
                            'status' => Pendaftaran::STATUS_DITERIMA,
                        ]);
                    }
                } elseif ($wasKetua) {
                    Pendaftaran::where('siswa_id', $siswa->id)
                        ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
                        ->update(['status' => Pendaftaran::STATUS_NONAKTIF]);
                }
            } elseif ($data['role'] === 'pembina') {
                $pembinaData = [
                    'nip' => $data['nip'],
                    'nama' => $data['pembina_nama'],
                    'tempat_lahir' => $data['pembina_tempat_lahir'] ?? null,
                    'tanggal_lahir' => $data['pembina_tanggal_lahir'] ?? null,
                    'agama' => $data['pembina_agama'] ?? null,
                    'jenis_kelamin' => $data['pembina_jenis_kelamin'],
                    'email' => $data['email'],
                    'no_telp' => $data['pembina_no_telp'] ?? null,
                    'alamat' => $data['pembina_alamat'] ?? null,
                    'medsos' => $data['pembina_medsos'] ?? null,
                ];

                if ($request->hasFile('pembina_foto')) {
                    if ($existingPembina?->foto && Storage::disk('public')->exists($existingPembina->foto)) {
                        Storage::disk('public')->delete($existingPembina->foto);
                    }
                    $pembinaData['foto'] = $request->file('pembina_foto')->store('profile-photos', 'public');
                }

                Pembina::updateOrCreate(
                    ['user_id' => $user->id],
                    $pembinaData
                );

                if ($existingSiswa) {
                    if ($existingSiswa->foto && Storage::disk('public')->exists($existingSiswa->foto)) {
                        Storage::disk('public')->delete($existingSiswa->foto);
                    }
                    $existingSiswa->delete();
                }
            } else {
                if ($existingSiswa) {
                    if ($existingSiswa->foto && Storage::disk('public')->exists($existingSiswa->foto)) {
                        Storage::disk('public')->delete($existingSiswa->foto);
                    }
                    $existingSiswa->delete();
                }
                if ($existingPembina) {
                    if ($existingPembina->foto && Storage::disk('public')->exists($existingPembina->foto)) {
                        Storage::disk('public')->delete($existingPembina->foto);
                    }
                    $existingPembina->delete();
                }
            }
        });

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

        if ($user->siswa?->foto && Storage::disk('public')->exists($user->siswa->foto)) {
            Storage::disk('public')->delete($user->siswa->foto);
        }
        if ($user->pembina?->foto && Storage::disk('public')->exists($user->pembina->foto)) {
            Storage::disk('public')->delete($user->pembina->foto);
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

    private function ensureEkskulCanHaveKetua(int $ekskulId, ?int $exceptSiswaId = null): void
    {
        Ekskul::query()->lockForUpdate()->findOrFail($ekskulId);

        $hasKetua = Pendaftaran::query()
            ->where('ekskul_id', $ekskulId)
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->whereHas('siswa', fn ($query) => $query->where('jabatan', 'ketua'))
            ->when($exceptSiswaId, fn ($query) => $query->where('siswa_id', '!=', $exceptSiswaId))
            ->exists();

        abort_if($hasKetua, 422, 'Ekskul yang dipilih sudah memiliki ketua.');
    }

    /**
     * Get all ekskuls with ketua status.
     * If $currentUserId is provided, exclude that user's ketua assignment (for edit mode).
     */
    private function getEskulsWithKetuaStatus(?int $currentUserId = null): Collection
    {
        // Get ekskul_ids that already have a ketua (via pendaftaran diterima + siswa jabatan ketua)
        $ekskulWithKetua = Pendaftaran::where('status', 'diterima')
            ->whereHas('siswa', fn ($q) => $q->where('jabatan', 'ketua'))
            ->pluck('ekskul_id');

        return Ekskul::all()->map(function ($ekskul) use ($ekskulWithKetua, $currentUserId) {
            $hasKetua = $ekskulWithKetua->contains($ekskul->id);

            // If editing, check if this ekskul's ketua is the current user
            if ($hasKetua && $currentUserId) {
                $currentKetua = Pendaftaran::where('ekskul_id', $ekskul->id)
                    ->where('status', 'diterima')
                    ->whereHas('siswa', fn ($q) => $q->where('jabatan', 'ketua'))
                    ->first();
                if ($currentKetua && $currentKetua->siswa?->user_id === $currentUserId) {
                    $hasKetua = false; // This ekskul's ketua is the user being edited
                }
            }

            return [
                'id' => $ekskul->id,
                'nama_ekskul' => $ekskul->nama_ekskul,
                'has_ketua' => $hasKetua,
                'label' => $ekskul->nama_ekskul.($hasKetua ? ' ( sudah ada ketua )' : ''),
            ];
        });
    }
}
