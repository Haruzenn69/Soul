<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Kegiatan;
use App\Services\NotifikasiService;
use App\Support\TableKit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    use KetuaEkskul;

    public function index(Request $request)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);

        if ($isKesiswaan && ! $request->filled('ekskul')) {
            $ekskuls = \App\Models\Ekskul::whereNull('parent_id')->orderBy('nama_ekskul')->get();
            $cards = $ekskuls->map(function (\App\Models\Ekskul $e) {
                $unitIds = array_merge([$e->id], $e->subdivisis()->pluck('id')->all());
                $totalKegiatan = Kegiatan::whereIn('ekskul_id', $unitIds)->count();
                $eventCount = Kegiatan::whereIn('ekskul_id', $unitIds)->whereNotNull('jenis_kegiatan')->count();
                $kegiatanTerakhir = Kegiatan::whereIn('ekskul_id', $unitIds)->latest('tanggal_kegiatan')->first();
                $totalAnggota = \App\Models\Pendaftaran::whereIn('ekskul_id', $unitIds)->where('status', 'diterima')->count();

                $stats = [
                    ['label' => 'Total Kegiatan', 'value' => $totalKegiatan.' Kegiatan', 'class' => 'font-bold text-slate-800 dark:text-slate-200'],
                    ['label' => 'Event Khusus', 'value' => $eventCount.' Event', 'class' => 'font-bold text-amber-600 dark:text-amber-400'],
                    ['label' => 'Anggota Aktif', 'value' => $totalAnggota.' Siswa', 'class' => 'font-semibold text-slate-600 dark:text-slate-300'],
                ];
                if ($kegiatanTerakhir) {
                    $stats[] = ['label' => 'Terakhir', 'value' => $kegiatanTerakhir->tanggal_kegiatan->translatedFormat('d M Y'), 'class' => 'font-medium text-slate-500 text-[11px]'];
                }

                return (object) [
                    'ekskul' => $e,
                    'stats' => $stats,
                    'badge' => null,
                    'actionText' => $e->subdivisis()->exists() ? 'Lihat Subdivisi' : 'Buka Kegiatan',
                    'actionUrl' => route('ketua.kegiatan.index', ['ekskul' => $e->id]),
                ];
            });

            return view('ketua.kegiatan.index', [
                'isCatalog' => true,
                'cards' => $cards,
                'isKesiswaan' => true,
            ]);
        }

        if ($isKesiswaan && $request->filled('ekskul')) {
            $pusat = \App\Models\Ekskul::findOrFail($request->integer('ekskul'));
            $subdivisis = $pusat->subdivisis()->orderBy('nama_ekskul')->get();

            if (! $pusat->isSubdivisi() && $subdivisis->isNotEmpty()) {
                $cards = $subdivisis->map(function (\App\Models\Ekskul $subdivisi) {
                    $totalKegiatan = Kegiatan::where('ekskul_id', $subdivisi->id)->count();
                    $eventCount = Kegiatan::where('ekskul_id', $subdivisi->id)->whereNotNull('jenis_kegiatan')->count();
                    $totalAnggota = $subdivisi->pendaftarans()->where('status', 'diterima')->count();
                    $kegiatanTerakhir = Kegiatan::where('ekskul_id', $subdivisi->id)->latest('tanggal_kegiatan')->first();

                    $stats = [
                        ['label' => 'Total Kegiatan', 'value' => $totalKegiatan.' Kegiatan', 'class' => 'font-bold text-slate-800 dark:text-slate-200'],
                        ['label' => 'Event Khusus', 'value' => $eventCount.' Event', 'class' => 'font-bold text-amber-600 dark:text-amber-400'],
                        ['label' => 'Anggota Aktif', 'value' => $totalAnggota.' Siswa', 'class' => 'font-semibold text-slate-600 dark:text-slate-300'],
                    ];
                    if ($kegiatanTerakhir) {
                        $stats[] = ['label' => 'Terakhir', 'value' => $kegiatanTerakhir->tanggal_kegiatan->translatedFormat('d M Y'), 'class' => 'font-medium text-slate-500 text-[11px]'];
                    }

                    return (object) [
                        'ekskul' => $subdivisi,
                        'stats' => $stats,
                        'badge' => null,
                        'actionText' => 'Buka Kegiatan',
                        'actionUrl' => route('ketua.kegiatan.index', ['ekskul' => $subdivisi->id]),
                    ];
                });

                return view('ketua.kegiatan.index', [
                    'isCatalog' => true,
                    'cards' => $cards,
                    'isKesiswaan' => true,
                    'catalogTitle' => 'Subdivisi '.$pusat->nama_ekskul,
                    'catalogSubtitle' => 'Pilih subdivisi untuk melihat jadwal dan presensinya.',
                    'backUrl' => route('ketua.kegiatan.index'),
                ]);
            }
        }

        $ekskul = $this->kegiatanEkskul();
        $isMonitoring = $this->isKetuaUtamaMonitoring($ekskul);

        if (! $isKesiswaan && $isMonitoring && ! $ekskul->isSubdivisi()) {
            $subdivisis = $ekskul->subdivisis()->orderBy('nama_ekskul')->get();
            $cards = $subdivisis->map(function (\App\Models\Ekskul $subdivisi) {
                $unitIds = [$subdivisi->id];
                $totalKegiatan = Kegiatan::whereIn('ekskul_id', $unitIds)->count();
                $eventCount = Kegiatan::whereIn('ekskul_id', $unitIds)->whereNotNull('jenis_kegiatan')->count();
                $totalAnggota = \App\Models\Pendaftaran::whereIn('ekskul_id', $unitIds)->where('status', 'diterima')->count();
                $kegiatanTerakhir = Kegiatan::whereIn('ekskul_id', $unitIds)->latest('tanggal_kegiatan')->first();
                $stats = [
                    ['label' => 'Total Kegiatan', 'value' => $totalKegiatan.' Kegiatan', 'class' => 'font-bold text-slate-800 dark:text-slate-200'],
                    ['label' => 'Event Khusus', 'value' => $eventCount.' Event', 'class' => 'font-bold text-amber-600 dark:text-amber-400'],
                    ['label' => 'Anggota Aktif', 'value' => $totalAnggota.' Siswa', 'class' => 'font-semibold text-slate-600 dark:text-slate-300'],
                ];
                if ($kegiatanTerakhir) {
                    $stats[] = ['label' => 'Terakhir', 'value' => $kegiatanTerakhir->tanggal_kegiatan->translatedFormat('d M Y'), 'class' => 'font-medium text-slate-500 text-[11px]'];
                }

                return (object) [
                    'ekskul' => $subdivisi,
                    'stats' => $stats,
                    'badge' => null,
                    'actionText' => 'Lihat Kegiatan',
                    'actionUrl' => route('ketua.kegiatan.index', ['ekskul' => $subdivisi->id]),
                ];
            });

            return view('ketua.kegiatan.index', [
                'isCatalog' => true,
                'cards' => $cards,
                'catalogTitle' => 'Subdivisi '.$ekskul->nama_ekskul,
                'catalogSubtitle' => 'Pilih subdivisi untuk melihat daftar kegiatannya.',
                'backUrl' => route('ketua.dashboard'),
            ]);
        }

        $subdivisis = $isKesiswaan
            ? $ekskul->subdivisis()->orderBy('nama_ekskul')->get()
            : collect();
        $activityIds = $this->kegiatanEkskulIds($ekskul);
        $showUnitColumn = $isMonitoring && ! $ekskul->isSubdivisi();
        if ($isKesiswaan && ! $ekskul->isSubdivisi()) {
            $subdivisiIds = $ekskul->subdivisis()->pluck('id')->all();
            $activityIds = array_merge([$ekskul->id], $subdivisiIds);
            $showUnitColumn = $subdivisiIds !== [];
        }
        $base = Kegiatan::whereIn('ekskul_id', $activityIds);
        $total = (clone $base)->count();

        [$sort, $direction] = TableKit::sort(['tanggal_kegiatan', 'materi'], 'tanggal_kegiatan', 'desc');

        $kegiatans = (clone $base)
            ->when($request->filled('cari'), function ($query) use ($request) {
                $cari = $request->input('cari');
                $query->where(function ($sub) use ($cari) {
                    $sub->where('materi', 'like', '%'.$cari.'%')
                        ->orWhere('deskripsi', 'like', '%'.$cari.'%');
                });
            })
            ->when($request->filled('jenis') && $request->input('jenis') !== 'semua', function ($query) use ($request) {
                if ($request->input('jenis') === 'event') {
                    $query->whereNotNull('jenis_kegiatan');
                } else {
                    $query->whereNull('jenis_kegiatan');
                }
            })
            ->with('ekskul:id,nama_ekskul')
            ->withCount('presensis')
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('ketua.kegiatan.index', compact('kegiatans', 'ekskul', 'total', 'sort', 'direction', 'isKesiswaan', 'isMonitoring', 'showUnitColumn', 'subdivisis'));
    }

    public function create()
    {
        $this->pastikanBolehKelolaKegiatan();

        return view('ketua.kegiatan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'materi' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:event',
            'tanggal_kegiatan' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_kegiatan',
            'deskripsi' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $ekskul = $this->pastikanBolehKelolaKegiatan();

        $dokumentasiPath = null;
        if ($request->hasFile('dokumentasi')) {
            $dokumentasiPath = $request->file('dokumentasi')->store('dokumentasi-kegiatan', 'public');
        }

        $kegiatan = Kegiatan::create([
            'ekskul_id' => $ekskul->id,
            'materi' => $validated['materi'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'dokumentasi' => $dokumentasiPath,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? null,
        ]);

        NotifikasiService::kegiatanDibuat($kegiatan);

        return redirect()->route('ketua.kegiatan.index')->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $this->ensureKegiatanEkskul($kegiatan);
        $ekskul = $this->kegiatanEkskul();

        $kegiatan->load(['presensis.pendaftaran.siswa.kelas', 'presensiPelatihs', 'ekskul.pelatih']);

        $presensiPelatih = $kegiatan->presensiPelatihs->first();
        $isMonitoring = $this->isKetuaUtamaMonitoring($ekskul);

        return view('ketua.kegiatan.show', compact('kegiatan', 'ekskul', 'presensiPelatih', 'isMonitoring'));
    }

    public function edit(Kegiatan $kegiatan)
    {
        $this->ensureKegiatanEkskul($kegiatan);
        $this->pastikanBolehKelolaKegiatan();

        return view('ketua.kegiatan.edit', compact('kegiatan'));
    }

    public function update(Request $request, Kegiatan $kegiatan)
    {
        $this->ensureKegiatanEkskul($kegiatan);
        $this->pastikanBolehKelolaKegiatan();

        $validated = $request->validate([
            'materi' => 'required|string|max:255',
            'jenis_kegiatan' => 'nullable|in:event',
            'tanggal_kegiatan' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_kegiatan',
            'deskripsi' => 'nullable|string',
            'dokumentasi' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $dokumentasiPath = $kegiatan->dokumentasi;
        if ($request->hasFile('dokumentasi')) {
            if ($kegiatan->dokumentasi) {
                Storage::disk('public')->delete($kegiatan->dokumentasi);
            }
            $dokumentasiPath = $request->file('dokumentasi')->store('dokumentasi-kegiatan', 'public');
        }

        $kegiatan->update([
            'materi' => $validated['materi'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'] ?? null,
            'deskripsi' => $validated['deskripsi'] ?? null,
            'dokumentasi' => $dokumentasiPath,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? null,
        ]);

        return redirect()->route('ketua.kegiatan.show', $kegiatan)
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        $this->ensureKegiatanEkskul($kegiatan);
        $this->pastikanBolehKelolaKegiatan();

        if ($kegiatan->dokumentasi) {
            Storage::disk('public')->delete($kegiatan->dokumentasi);
        }

        $kegiatan->delete();

        return redirect()->route('ketua.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
