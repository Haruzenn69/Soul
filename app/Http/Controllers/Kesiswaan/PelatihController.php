<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Ekskul;
use App\Models\Pelatih;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\Request;

class PelatihController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        [$sort, $direction] = TableKit::sort(['nama', 'created_at', 'status_verifikasi'], 'created_at', 'desc');

        $query = Pelatih::query()->with(['pembina', 'ekskul', 'verifiedBy']);

        if ($status !== 'all' && in_array($status, ['pending', 'terverifikasi', 'ditolak'])) {
            $query->where('status_verifikasi', $status);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhereHas('ekskul', fn ($e) => $e->where('nama_ekskul', 'like', "%{$search}%"))
                  ->orWhereHas('pembina', fn ($p) => $p->where('nama', 'like', "%{$search}%"));
            });
        }

        $pelatihs = $query->orderBy($sort, $direction)->paginate(12)->withQueryString();

        $stats = [
            'total' => Pelatih::count(),
            'pending' => Pelatih::where('status_verifikasi', Pelatih::VERIFIKASI_PENDING)->count(),
            'terverifikasi' => Pelatih::where('status_verifikasi', Pelatih::VERIFIKASI_TERVERIFIKASI)->count(),
            'ditolak' => Pelatih::where('status_verifikasi', Pelatih::VERIFIKASI_DITOLAK)->count(),
        ];

        return view('kesiswaan.pelatih.index', compact('pelatihs', 'status', 'stats', 'sort', 'direction'));
    }

    public function verifikasi(Request $request, Pelatih $pelatih)
    {
        $pelatih->update([
            'status_verifikasi' => Pelatih::VERIFIKASI_TERVERIFIKASI,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'catatan_verifikasi' => null,
        ]);

        // Hubungkan pelatih secara resmi ke ekskul terkait jika ada
        if ($pelatih->ekskul_id) {
            $ekskul = Ekskul::find($pelatih->ekskul_id);
            if ($ekskul) {
                $ekskul->update(['pelatih_id' => $pelatih->id]);
            }
        }

        NotifikasiService::pelatihDiverifikasi($pelatih);

        return back()->with('success', "Pelatih {$pelatih->nama} berhasil diverifikasi dan kini aktif di katalog ekskul.");
    }

    public function tolak(Request $request, Pelatih $pelatih)
    {
        $request->validate([
            'catatan' => ['required', 'string', 'max:1000'],
        ], [
            'catatan.required' => 'Berikan alasan / catatan penolakan untuk pembina.',
        ]);

        $pelatih->update([
            'status_verifikasi' => Pelatih::VERIFIKASI_DITOLAK,
            'catatan_verifikasi' => $request->catatan,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        // Jika sebelumnya terpasang di ekskul, lepaskan
        if ($pelatih->ekskul_id) {
            $ekskul = Ekskul::find($pelatih->ekskul_id);
            if ($ekskul && $ekskul->pelatih_id === $pelatih->id) {
                $ekskul->update(['pelatih_id' => null]);
            }
        }

        NotifikasiService::pelatihDitolak($pelatih, $request->catatan);

        return back()->with('success', "Pengajuan pelatih {$pelatih->nama} telah ditolak.");
    }
}
