<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Exports\PembinaTemplateExport;
use App\Exports\SiswaTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\PembinaImport;
use App\Imports\SiswaImport;
use App\Models\Kelas;
use App\Models\Pembina;
use App\Models\Penilaian;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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

        $sort = in_array($request->input('sort'), ['username', 'email', 'role', 'created_at'], true) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

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
            ->orderByRaw("CASE WHEN users.is_active = 0 OR (users.role = 'siswa' AND EXISTS (SELECT 1 FROM siswas WHERE siswas.user_id = users.id AND siswas.status = 'nonaktif')) THEN 2 WHEN users.role = 'pembina' AND EXISTS (SELECT 1 FROM pembinas INNER JOIN ekskuls ON ekskuls.pembina_id = pembinas.id WHERE pembinas.user_id = users.id) THEN 0 ELSE 1 END ASC")
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'all' => User::count(),
            'siswa' => User::where('role', 'siswa')->count(),
            'guru' => User::where('role', 'pembina')->count(),
            'staff' => User::whereIn('role', ['kesiswaan', 'admin'])->count(),
        ];

        return view('kesiswaan.users.index', compact('users', 'counts', 'roleFilter', 'sort', 'direction'));
    }

    public function templateSiswa()
    {
        return Excel::download(new SiswaTemplateExport, 'template-akun-siswa.xlsx');
    }

    public function templatePembina()
    {
        return Excel::download(new PembinaTemplateExport, 'template-akun-pembina.xlsx');
    }

    public function importPage(string $jenis): View
    {
        $kelas = Kelas::with('tahunAjaran')->whereHas('tahunAjaran', fn ($query) => $query->where('status', 'aktif'))->orderBy('nama')->get();

        return view('kesiswaan.users.import', compact('jenis', 'kelas'));
    }

    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:siswa,pembina'],
            'kelas_id' => ['required_if:jenis,siswa', 'nullable', 'integer', Rule::exists('kelas', 'id')->where('tahun_ajaran_id', TahunAjaran::where('status', 'aktif')->value('id'))],
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
            ->map(function (Failure $failure) {
                // Pesan error sudah dalam Bahasa Indonesia dari customValidationMessages()
                $messages = collect($failure->errors())->implode(' ');

                return 'Baris '.$failure->row().': '.$messages;
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
            'kelas' => Kelas::with('tahunAjaran')->whereHas('tahunAjaran', fn ($query) => $query->where('status', 'aktif'))->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $isStaff = in_array($request->input('role'), ['admin', 'kesiswaan']);

        $isPembina = $request->input('role') === 'pembina';

        $rules = [
            'username' => [$isStaff ? 'required' : 'nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            // Role admin hanya bisa dibuat oleh admin (kesiswaan tidak boleh)
            'role' => ['required', 'in:'.$this->allowedRoles()],
        ];

        if ($request->input('role') === 'siswa') {
            $rules += [
                'nis' => ['required', 'string', 'digits:10', 'unique:siswas,nis'],
                'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s.,\'-]+$/'],
                'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
                'tempat_lahir' => ['required', 'string', 'max:100'],
                'tanggal_lahir' => ['required', 'date'],
                'agama' => ['required', 'string', 'max:50', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu'],
                'kelas_id' => ['required', Rule::exists('kelas', 'id')->where('tahun_ajaran_id', TahunAjaran::where('status', 'aktif')->value('id'))],
                'angkatan' => ['nullable', 'string', 'max:20'],
                'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
                'no_telp' => ['nullable', 'string', 'max:25'],
                'alamat' => ['nullable', 'string'],
                'medsos' => ['nullable', 'string', 'max:255'],
            ];
        } elseif ($request->input('role') === 'pembina') {
            $rules += [
                'pembina_nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s.,\'-]+$/'],
                'pembina_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
                'nip' => ['required', 'string', 'digits:18', 'unique:pembinas,nip'],
                'pembina_tempat_lahir' => ['required', 'string', 'max:100'],
                'pembina_tanggal_lahir' => ['required', 'date'],
                'pembina_agama' => ['required', 'string', 'max:50', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu'],
                'pembina_jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
                'pembina_no_telp' => ['nullable', 'string', 'max:25'],
                'pembina_alamat' => ['nullable', 'string'],
                'pembina_medsos' => ['nullable', 'string', 'max:255'],
            ];
        }

        $data = $request->validate($rules, [
            // === Akun ===
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format Email tidak valid. Email harus mengandung tanda @.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah terdaftar pada sistem.',
            'username.required' => 'Username wajib diisi untuk akun staf.',
            'username.unique' => 'Username sudah digunakan.',
            // === Siswa ===
            'nis.required' => 'Kolom NIS wajib diisi.',
            'nis.digits' => 'NIS harus terdiri dari tepat 10 digit angka.',
            'nis.unique' => 'NIS sudah terdaftar pada sistem.',
            'nama.required' => 'Kolom Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'nama.regex' => 'Nama hanya boleh mengandung huruf alfabet, spasi, titik, koma, dan strip. Tidak boleh ada angka atau simbol lainnya.',
            'jenis_kelamin.required' => 'Kolom Jenis Kelamin wajib diisi.',
            'jenis_kelamin.in' => 'Jenis Kelamin harus "Laki-laki" atau "Perempuan".',
            'tempat_lahir.required' => 'Kolom Tempat Lahir wajib diisi.',
            'tempat_lahir.max' => 'Tempat Lahir maksimal 100 karakter.',
            'tanggal_lahir.required' => 'Kolom Tanggal Lahir wajib diisi.',
            'tanggal_lahir.date' => 'Tanggal Lahir harus berupa tanggal yang valid.',
            'agama.required' => 'Kolom Agama wajib diisi.',
            'agama.in' => 'Agama tidak valid. Pilih dari: Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu.',
            'no_telp.max' => 'No. Telp maksimal 25 karakter.',
            'medsos.max' => 'Media Sosial maksimal 255 karakter.',
            'kelas_id.required' => 'Kolom Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas yang dipilih tidak valid atau bukan dari tahun ajaran aktif.',
            'foto.image' => 'File foto siswa harus berupa gambar.',
            'foto.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto siswa maksimal 2MB.',
            // === Pembina ===
            'nip.required' => 'Kolom NIP wajib diisi.',
            'nip.digits' => 'NIP harus terdiri dari tepat 18 digit angka.',
            'nip.unique' => 'NIP sudah terdaftar pada sistem.',
            'pembina_nama.required' => 'Kolom Nama Lengkap wajib diisi.',
            'pembina_nama.max' => 'Nama Lengkap maksimal 255 karakter.',
            'pembina_nama.regex' => 'Nama Lengkap hanya boleh mengandung huruf alfabet, spasi, titik, koma, dan strip. Tidak boleh ada angka atau simbol lainnya.',
            'pembina_jenis_kelamin.required' => 'Kolom Jenis Kelamin wajib diisi.',
            'pembina_jenis_kelamin.in' => 'Jenis Kelamin harus "Laki-laki" atau "Perempuan".',
            'pembina_tempat_lahir.required' => 'Kolom Tempat Lahir wajib diisi.',
            'pembina_tempat_lahir.max' => 'Tempat Lahir maksimal 100 karakter.',
            'pembina_tanggal_lahir.required' => 'Kolom Tanggal Lahir wajib diisi.',
            'pembina_tanggal_lahir.date' => 'Tanggal Lahir harus berupa tanggal yang valid.',
            'pembina_agama.required' => 'Kolom Agama wajib diisi.',
            'pembina_agama.in' => 'Agama tidak valid. Pilih dari: Islam, Kristen, Katolik, Hindu, Buddha, Khonghucu.',
            'pembina_no_telp.max' => 'No. Telp maksimal 25 karakter.',
            'pembina_medsos.max' => 'Media Sosial maksimal 255 karakter.',
            'pembina_foto.image' => 'File foto pembina harus berupa gambar.',
            'pembina_foto.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'pembina_foto.max' => 'Ukuran foto pembina maksimal 2MB.',
        ]);

        $user = DB::transaction(function () use ($data, $request, $isStaff) {
            $user = User::create([
                'username' => ($isStaff && ! empty($data['username'])) ? $data['username'] : null,
                'email' => $data['email'] ?? null,
                'password' => Hash::make('password'),
                'role' => $data['role'],
                'email_verified_at' => now(),
                'onboarding_completed_at' => in_array($data['role'], ['siswa', 'pembina'], true) ? null : now(),
            ]);

            if ($data['role'] === 'siswa') {
                $fotoPath = $request->hasFile('foto')
                    ? $request->file('foto')->store('profile-photos', 'public')
                    : null;

                $siswa = $user->siswa()->create([
                    'nis' => $data['nis'],
                    'nama' => mb_strtoupper(trim($data['nama'])),
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
                    'jabatan' => 'siswa',
                ]);
            } elseif ($data['role'] === 'pembina') {
                $pembinaFotoPath = $request->hasFile('pembina_foto')
                    ? $request->file('pembina_foto')->store('profile-photos', 'public')
                    : null;

                $user->pembina()->create([
                    'nip' => $data['nip'] ?? null,
                    'nama' => mb_strtoupper(trim($data['pembina_nama'])),
                    'foto' => $pembinaFotoPath,
                    'tempat_lahir' => $data['pembina_tempat_lahir'] ?? null,
                    'tanggal_lahir' => $data['pembina_tanggal_lahir'] ?? null,
                    'agama' => $data['pembina_agama'] ?? null,
                    'jenis_kelamin' => $data['pembina_jenis_kelamin'] ?? null,
                    'email' => $data['email'] ?? null,
                    'no_telp' => $data['pembina_no_telp'] ?? null,
                    'alamat' => $data['pembina_alamat'] ?? null,
                    'medsos' => $data['pembina_medsos'] ?? null,
                ]);
            }

            return $user;
        });

        return redirect()
            ->route('kesiswaan.users.index')
            ->with('success', $user->username
                ? "Akun {$user->username} berhasil dibuat dengan password default: password"
                : 'Akun berhasil dibuat. Password default: password (akun dapat diubah sendiri lewat menu Profil).');
    }

    public function show(User $user): View
    {
        $user->load([
            'siswa.kelas.tahunAjaran',
            'siswa.pendaftarans.ekskul.pembina',
            'siswa.pendaftarans.ekskul.pelatih',
            'siswa.riwayatJabatans.ekskul',
            'siswa.pengajuanKeluars.ekskul',
            'siswa.classHistories',
            'siswa.profileHistories',
            'pembina.ekskuls.pelatih',
            'pembina.profileHistories',
        ]);

        $ekskuls = $user->role === 'siswa'
            ? $user->siswa?->pendaftarans()
                ->with('ekskul.pembina', 'ekskul.pelatih')
                ->where('status', 'diterima')
                ->get()
                ->pluck('ekskul')
                ->filter()
                ->values()
            : ($user->pembina?->ekskuls()->with('pelatih')->get() ?? collect());

        $pendaftaranAktif = $user->role === 'siswa'
            ? ($user->siswa?->pendaftarans()->where('status', 'diterima')->count() ?? 0)
            : ($user->pembina?->ekskuls()->count() ?? 0);

        $presensi = null;
        $penilaians = collect();
        if ($user->role === 'siswa' && $user->siswa) {
            $presensi = \App\Models\Presensi::whereIn(
                'pendaftaran_id',
                $user->siswa->pendaftarans()->pluck('id')
            )->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

            $penilaians = Penilaian::query()
                ->whereHas('pendaftaran', fn ($query) => $query->where('siswa_id', $user->siswa->id))
                ->where('status', Penilaian::STATUS_TERKIRIM)
                ->with(['pendaftaran.ekskul', 'penilai'])
                ->orderByDesc('dikirim_at')
                ->get();
        }

        return view('kesiswaan.users.show', compact('user', 'ekskuls', 'pendaftaranAktif', 'presensi', 'penilaians'));
    }

    public function edit(User $user): View
    {
        $this->authorizeManage($user);
        $user->load(['siswa.kelas', 'pembina', 'siswa.pendaftarans' => fn ($q) => $q->where('status', 'diterima')]);

        return view('kesiswaan.users.edit', [
            'user' => $user,
            'kelas' => Kelas::with('tahunAjaran')->whereHas('tahunAjaran', fn ($query) => $query->where('status', 'aktif'))->orderBy('nama')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManage($user);

        $isStaff = in_array($request->input('role'), ['admin', 'kesiswaan']);

        $rules = [
            'username' => [$isStaff ? 'required' : 'nullable', 'string', 'max:255', 'unique:users,username,'.$user->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'is_active' => ['required', 'boolean'],
            // Role admin hanya bisa dipilih oleh admin (kesiswaan tidak boleh)
            'role' => ['required', 'in:'.$this->allowedRoles()],
        ];

        if ($request->input('role') === 'siswa') {
            $nisIgnore = $user->siswa ? ','.$user->siswa->id : '';
            $rules += [
                'nis' => ['required', 'string', 'digits:10', 'unique:siswas,nis'.$nisIgnore],
                'nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s.,\'-]+$/'],
                'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
                'tempat_lahir' => ['nullable', 'string', 'max:100'],
                'tanggal_lahir' => ['nullable', 'date'],
                'agama' => ['nullable', 'string', 'max:50'],
                'kelas_id' => ['required', Rule::exists('kelas', 'id')->where('tahun_ajaran_id', TahunAjaran::where('status', 'aktif')->value('id'))],
                'angkatan' => ['nullable', 'string', 'max:20'],
                'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
                'no_telp' => ['nullable', 'string', 'max:25'],
                'alamat' => ['nullable', 'string'],
                'medsos' => ['nullable', 'string', 'max:255'],
            ];
        } elseif ($request->input('role') === 'pembina') {
            $nipIgnore = $user->pembina ? ','.$user->pembina->id : '';
            $rules += [
                'nip' => ['required', 'string', 'digits:18', 'unique:pembinas,nip'.$nipIgnore],
                'pembina_nama' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s.,\'-]+$/'],
                'pembina_foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
                'pembina_tempat_lahir' => ['required', 'string', 'max:100'],
                'pembina_tanggal_lahir' => ['required', 'date'],
                'pembina_agama' => ['required', 'string', 'max:50'],
                'pembina_jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
                'pembina_no_telp' => ['required', 'string', 'max:25'],
                'pembina_alamat' => ['required', 'string'],
                'pembina_medsos' => ['nullable', 'string', 'max:255'],
            ];
        }

        $data = $request->validate($rules, [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Terjadi kesalahan pada data Email. Format email harus mengandung tanda @.',
            'email.unique' => 'Email sudah terdaftar pada sistem.',
            'nis.required' => 'Kolom NIS wajib diisi.',
            'nis.digits' => 'NIS harus terdiri dari tepat 10 digit angka.',
            'nis.unique' => 'NIS sudah terdaftar pada sistem.',
            'nama.required' => 'Kolom Nama wajib diisi.',
            'nama.max' => 'Nama maksimal 255 karakter.',
            'nama.regex' => 'Nama hanya boleh mengandung huruf alfabet, spasi, titik, koma, dan strip. Tidak boleh ada angka atau simbol lainnya.',
            'nip.required' => 'Kolom NIP wajib diisi.',
            'nip.digits' => 'NIP harus terdiri dari tepat 18 digit angka.',
            'nip.unique' => 'NIP sudah terdaftar pada sistem.',
            'pembina_nama.required' => 'Kolom Nama Lengkap wajib diisi.',
            'pembina_nama.max' => 'Nama Lengkap maksimal 255 karakter.',
            'pembina_nama.regex' => 'Nama Lengkap hanya boleh mengandung huruf alfabet, spasi, titik, koma, dan strip. Tidak boleh ada angka atau simbol lainnya.',
            'foto.image' => 'File foto siswa harus berupa gambar.',
            'foto.max' => 'Ukuran foto siswa maksimal 2MB.',
            'pembina_foto.image' => 'File foto pembina harus berupa gambar.',
            'pembina_foto.max' => 'Ukuran foto pembina maksimal 2MB.',
        ]);

        DB::transaction(function () use ($user, $data, $request) {
            $existingSiswa = $user->siswa;
            $existingPembina = $user->pembina;
            $isActive = (bool) $data['is_active'];

            $user->update([
                'username' => filled($data['username']) ? $data['username'] : null,
                'email' => $data['email'],
                'role' => $data['role'],
                'is_active' => $isActive,
            ]);

            if ($data['role'] === 'siswa') {
                $siswaData = [
                    'nis' => $data['nis'],
                    'nama' => mb_strtoupper(trim($data['nama'])),
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
                    // Penetapan jabatan Ketua hanya dilakukan oleh Pembina.
                    'jabatan' => $existingSiswa?->jabatan ?? 'siswa',
                    'status' => $isActive ? ($existingSiswa && $existingSiswa->status === 'nonaktif' ? 'aktif' : ($existingSiswa->status ?? 'aktif')) : 'nonaktif',
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

            } elseif ($data['role'] === 'pembina') {
                $pembinaData = [
                    'nip' => $data['nip'],
                    'nama' => mb_strtoupper(trim($data['pembina_nama'])),
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

        return back()->with('error', 'Akun tidak dapat dihapus. Silakan gunakan opsi nonaktifkan akun.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $this->authorizeManage($user);

        $user->update(['password' => Hash::make('password')]);

        $displayName = $user->siswa?->nama ?? $user->pembina?->nama ?? $user->username ?? $user->email ?? 'Pengguna';

        return back()->with('success', "Password akun {$displayName} berhasil direset ke password default: password");
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
