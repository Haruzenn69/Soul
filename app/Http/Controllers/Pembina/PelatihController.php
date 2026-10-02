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
        $pembina = auth()->user()?->pembina;
        abort_unless($pembina, 403, 'Akses khusus pembina.');
        return $pembina;
    }

    private function getEkskuls()
    {
        return $this->getPembina()->ekskuls;
    }

    public function index(Request $request)
    {
        $pembina = $this->getPembina();
        $ekskuls = $this->getEkskuls();
        $ekskulIds = $ekskuls->pluck('id');

        [$sort, $direction] = TableKit::sort(['nama', 'created_at', 'status_verifikasi'], 'created_at', 'desc');

        $query = Pelatih::query()
            ->where(function ($q) use ($pembina, $ekskulIds) {
                $q->where('pembina_id', $pembina->id)
                  ->orWhereIn('ekskul_id', $ekskulIds);
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

        $stats = [
            'total' => Pelatih::where(function ($q) use ($pembina, $ekskulIds) {
                $q->where('pembina_id', $pembina->id)->orWhereIn('ekskul_id', $ekskulIds);
            })->count(),
            'pending' => Pelatih::where(function ($q) use ($pembina, $ekskulIds) {
                $q->where('pembina_id', $pembina->id)->orWhereIn('ekskul_id', $ekskulIds);
            })->where('status_verifikasi', Pelatih::VERIFIKASI_PENDING)->count(),
            'terverifikasi' => Pelatih::where(function ($q) use ($pembina, $ekskulIds) {
                $q->where('pembina_id', $pembina->id)->orWhereIn('ekskul_id', $ekskulIds);
            })->where('status_verifikasi', Pelatih::VERIFIKASI_TERVERIFIKASI)->count(),
            'ditolak' => Pelatih::where(function ($q) use ($pembina, $ekskulIds) {
                $q->where('pembina_id', $pembina->id)->orWhereIn('ekskul_id', $ekskulIds);
            })->where('status_verifikasi', Pelatih::VERIFIKASI_DITOLAK)->count(),
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
        $pembina = $this->getPembina();
        $ekskulIds = $pembina->ekskuls->pluck('id')->toArray();

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

        $pelatih = Pelatih::create([
            'pembina_id' => $pembina->id,
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
            'status_verifikasi' => Pelatih::VERIFIKASI_PENDING,
        ]);

        // Kirim notifikasi pendaftaran pelatih
        NotifikasiService::pelatihDiajukan($pelatih);

        return redirect()->route('pembina.pelatih.index')->with('success', "Pendaftaran pelatih {$pelatih->nama} berhasil diajukan dan sedang menunggu verifikasi kesiswaan.");
    }
}
