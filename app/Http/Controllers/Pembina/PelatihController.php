<?php

namespace App\Http\Controllers\Pembina;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Pelatih;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PelatihController extends Controller
{
    private function getPembina()
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
            return null;
        }

        $pembina = $user?->pembina;
        abort_unless($pembina, 403, 'Akses khusus pembina.');
        return $pembina;
    }

    private function getEkskuls()
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
            return Ekskul::orderBy('nama_ekskul')->get();
        }

        return $this->getPembina()->ekskuls;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);
        $pembina = $this->getPembina();
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        [$sort, $direction] = TableKit::sort(['nama', 'created_at', 'status_verifikasi'], 'created_at', 'desc');

        $query = Pelatih::query()
            ->when(! $isKesiswaan, function ($q) use ($pembina, $ekskulIds) {
                $q->where(function ($sub) use ($pembina, $ekskulIds) {
                    $sub->where('pembina_id', $pembina?->id)
                        ->orWhereIn('ekskul_id', $ekskulIds);
                });
            })
            ->with(['ekskul', 'verifiedBy']);

        if ($request->filled('status_verifikasi') && in_array($request->status_verifikasi, ['pending', 'terverifikasi', 'ditolak'])) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        if ($request->filled('ekskul_id')) {
            $query->where('ekskul_id', $request->ekskul_id);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $pelatihs = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        $statsQuery = Pelatih::query()
            ->when(! $isKesiswaan, function ($q) use ($pembina, $ekskulIds) {
                $q->where(function ($sub) use ($pembina, $ekskulIds) {
                    $sub->where('pembina_id', $pembina?->id)->orWhereIn('ekskul_id', $ekskulIds);
                });
            });

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'pending' => (clone $statsQuery)->where('status_verifikasi', Pelatih::VERIFIKASI_PENDING)->count(),
            'terverifikasi' => (clone $statsQuery)->where('status_verifikasi', Pelatih::VERIFIKASI_TERVERIFIKASI)->count(),
            'ditolak' => (clone $statsQuery)->where('status_verifikasi', Pelatih::VERIFIKASI_DITOLAK)->count(),
        ];

        return view('pembina.pelatih.index', compact('pelatihs', 'ekskuls', 'stats', 'sort', 'direction'));
    }

    public function create()
    {
        $ekskuls = $this->getEkskuls();
        abort_if($ekskuls->isEmpty(), 403, 'Kamu belum memiliki ekskul binaan untuk didaftarkan pelatih.');

        return view('pembina.pelatih.create', compact('ekskuls'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);
        $pembina = $this->getPembina();
        $ekskulIds = $this->getEkskuls()->pluck('id')->toArray();

        $validated = $request->validate([
            'ekskul_id' => ['required', 'in:'.implode(',', $ekskulIds)],
            'nama' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'no_hp' => ['required', 'string', 'max:25'],
            'email' => ['required', 'email', 'max:255'],
            'sosmed' => ['nullable', 'string', 'max:255'],
            'domisili' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:1000'],
            'cv' => ['required', 'file', 'mimes:pdf', 'max:5120'], // CV Wajib PDF
            'sertifikat' => ['required', 'file', 'mimes:pdf', 'max:5120'], // Sertifikat Wajib PDF
        ], [
            'ekskul_id.required' => 'Pilih salah satu ekskul binaanmu.',
            'email.required' => 'Alamat email pelatih wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'domisili.required' => 'Domisili / kota tempat tinggal pelatih wajib diisi.',
            'alamat.required' => 'Alamat lengkap tempat tinggal pelatih wajib diisi.',
            'cv.required' => 'Curriculum Vitae (CV) dalam format PDF wajib diunggah.',
            'cv.mimes' => 'File CV harus berupa dokumen dengan format PDF.',
            'cv.max' => 'Ukuran file CV maksimal 5MB.',
            'sertifikat.required' => 'Sertifikat pelatih dalam format PDF wajib diunggah.',
            'sertifikat.mimes' => 'Sertifikat pelatih harus berupa file dengan format PDF.',
            'sertifikat.max' => 'Ukuran file sertifikat maksimal 5MB.',
        ]);

        $cvPath = $request->file('cv')->store('pelatih/cv', 'public');
        $sertifikatPath = $request->file('sertifikat')->store('pelatih/sertifikat', 'public');

        $pembinaId = $pembina?->id ?? Ekskul::find($validated['ekskul_id'])?->pembina_id;

        $pelatih = Pelatih::create([
            'pembina_id' => $pembinaId,
            'ekskul_id' => $validated['ekskul_id'],
            'nama' => $validated['nama'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'no_hp' => $validated['no_hp'],
            'email' => $validated['email'],
            'sosmed' => $validated['sosmed'] ?? null,
            'domisili' => $validated['domisili'],
            'alamat' => $validated['alamat'],
            'cv' => $cvPath,
            'sertifikat' => $sertifikatPath,
            'status' => 'aktif',
            'status_verifikasi' => $isKesiswaan ? Pelatih::VERIFIKASI_TERVERIFIKASI : Pelatih::VERIFIKASI_PENDING,
        ]);

        // Kirim notifikasi pendaftaran pelatih jika diajukan pembina
        if (! $isKesiswaan) {
            NotifikasiService::pelatihDiajukan($pelatih);
        }

        return redirect()->route('pembina.pelatih.index')->with('success', $isKesiswaan ? "Pelatih {$pelatih->nama} berhasil didaftarkan langsung ke ekskul." : "Pendaftaran pelatih {$pelatih->nama} berhasil diajukan dan sedang menunggu verifikasi kesiswaan.");
    }
}
