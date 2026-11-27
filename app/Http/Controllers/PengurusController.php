<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Ekskul;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\Pengurus;
use App\Models\RiwayatPendaftaran;
use App\Models\RiwayatJabatan;
use App\Models\Siswa;
use App\Services\ArsipEkskulService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PengurusController extends Controller
{
    use KetuaEkskul;

    /* =========================================================================
     | PEMBINA ACTIONS
     | ========================================================================= */

    private function getPembinaEkskuls(): Collection
    {
        $user = auth()->user();

        if ($user && in_array($user->role, ['kesiswaan', 'admin'], true)) {
            return Ekskul::whereNull('parent_id')->orderBy('nama_ekskul')->get();
        }

        $pembina = $user?->pembina;

        return $pembina ? $pembina->ekskuls()->get() : collect();
    }

    private function resolvePembinaEkskul(Request $request): ?Ekskul
    {
        $ekskuls = $this->getPembinaEkskuls();
        $id = $request->integer('ekskul');

        return $id && $ekskuls->pluck('id')->contains($id) ? $ekskuls->firstWhere('id', $id) : $ekskuls->first();
    }

    /**
     * Auto-sync Ketua yang sudah ada dari sistem lama ke tabel pengurus jika belum tercatat.
     */
    private function syncExistingKetua(Ekskul $ekskul): void
    {
        $currentKetua = $ekskul->ketua();
        if (! $currentKetua) {
            return;
        }

        $activeKetuaPengurus = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('jabatan', Pengurus::JABATAN_KETUA)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->first();

        if (! $activeKetuaPengurus) {
            Pengurus::create([
                'ekskul_id' => $ekskul->id,
                'siswa_id' => $currentKetua->id,
                'jabatan' => Pengurus::JABATAN_KETUA,
                'status' => Pengurus::STATUS_AKTIF,
                'diusulkan_oleh' => 'pembina',
                'catatan' => 'Sinkronisasi ketua aktif sistem',
            ]);
        } elseif ($activeKetuaPengurus->siswa_id !== $currentKetua->id) {
            // Jika ada perbedaan, sesuaikan dengan ketua aktif terkini
            $activeKetuaPengurus->update([
                'status' => Pengurus::STATUS_SELESAI,
                'catatan' => 'Digantikan sinkronisasi sistem',
            ]);
            Pengurus::create([
                'ekskul_id' => $ekskul->id,
                'siswa_id' => $currentKetua->id,
                'jabatan' => Pengurus::JABATAN_KETUA,
                'status' => Pengurus::STATUS_AKTIF,
                'diusulkan_oleh' => 'pembina',
                'catatan' => 'Sinkronisasi ketua aktif sistem',
            ]);
        }
    }

    /**
     * Tampilan kelola pengurus untuk Pembina.
     */
    public function indexPembina(Request $request)
    {
        $ekskuls = $this->getPembinaEkskuls();
        $ekskulIds = $ekskuls->pluck('id');
        $selectedEkskulId = $request->input('ekskul');

        // Jika hanya membina 1 ekskul, otomatis pilih ekskul tersebut
        if (! $selectedEkskulId && $ekskuls->count() === 1) {
            $selectedEkskulId = $ekskuls->first()?->id;
        }

        // TAMPILKAN KATALOG KARTU EKSKUL JIKA BELUM MEMILIH EKSKUL (PEMBINA MULTI-EKSKUL)
        if (! $selectedEkskulId || ! $ekskulIds->contains((int) $selectedEkskulId)) {
            $ekskulCards = $ekskuls->map(function (Ekskul $e) {
                $anggotaCount = Pendaftaran::where('ekskul_id', $e->id)
                    ->where('status', Pendaftaran::STATUS_DITERIMA)
                    ->count();
                $ketua = $e->ketua();
                $pengurusAktifCount = Pengurus::where('ekskul_id', $e->id)
                    ->where('status', Pengurus::STATUS_AKTIF)
                    ->count();
                $usulanMenungguCount = Pengurus::where('ekskul_id', $e->id)
                    ->where('status', Pengurus::STATUS_MENUNGGU)
                    ->count();

                return (object) [
                    'ekskul' => $e,
                    'totalAnggota' => $anggotaCount,
                    'ketua' => $ketua,
                    'pengurusCount' => $pengurusAktifCount,
                    'usulanCount' => $usulanMenungguCount,
                ];
            });

            return view('pembina.pengurus', [
                'ekskuls' => $ekskuls,
                'ekskul' => null,
                'ekskulCards' => $ekskulCards,
                'isCatalog' => true,
            ]);
        }

        $ekskul = $ekskuls->firstWhere('id', (int) $selectedEkskulId);

        if ($ekskul) {
            $this->syncExistingKetua($ekskul);

            // Pengurus aktif
            $semuaPengurusAktif = Pengurus::where('ekskul_id', $ekskul->id)
                ->where('status', Pengurus::STATUS_AKTIF)
                ->with(['siswa.kelas', 'siswa.pendaftarans', 'subdivisi'])
                ->get();

            $pengurusAktif = $semuaPengurusAktif->keyBy('jabatan');
            $wakilKetuas = $semuaPengurusAktif->where('jabatan', Pengurus::JABATAN_WAKIL_KETUA);

            // Sub divisi ekskul
            $subdivisis = $ekskul->subdivisis()->orderBy('nama_ekskul')->get();

            // Usulan dari ketua yang masih menunggu
            $usulanMenunggu = Pengurus::where('ekskul_id', $ekskul->id)
                ->where('status', Pengurus::STATUS_MENUNGGU)
                ->with(['siswa.kelas', 'subdivisi'])
                ->latest()
                ->get();

            // Riwayat pengurus (selesai atau ditolak)
            $riwayatPengurus = Pengurus::where('ekskul_id', $ekskul->id)
                ->whereIn('status', [Pengurus::STATUS_SELESAI, Pengurus::STATUS_DITOLAK])
                ->with(['siswa.kelas', 'subdivisi'])
                ->latest()
                ->limit(20)
                ->get();

            // Anggota ekskul berstatus 'diterima' yang belum memegang jabatan aktif di ekskul ini
            $currentKetuaId = $ekskul->ketua()?->id;
            $occupiedInThisEkskul = Pengurus::where('ekskul_id', $ekskul->id)
                ->whereIn('status', [Pengurus::STATUS_AKTIF, Pengurus::STATUS_MENUNGGU])
                ->pluck('siswa_id')
                ->when($currentKetuaId, fn ($col) => $col->push($currentKetuaId))
                ->unique();

            $anggotaEligible = Pendaftaran::whereIn('ekskul_id', array_merge([$ekskul->id], $ekskul->subdivisis()->pluck('id')->all()))
                ->where('status', Pendaftaran::STATUS_DITERIMA)
                ->whereNotIn('siswa_id', $occupiedInThisEkskul)
                ->with('siswa.kelas')
                ->get()
                ->sortBy(fn ($item) => strtolower($item->siswa?->nama ?? ''))
                ->values();
        } else {
            $pengurusAktif = collect();
            $wakilKetuas = collect();
            $subdivisis = collect();
            $usulanMenunggu = collect();
            $riwayatPengurus = collect();
            $anggotaEligible = collect();
        }

        $daftarJabatan = Pengurus::daftarJabatan();

        return view('pembina.pengurus', compact(
            'ekskuls',
            'ekskul',
            'pengurusAktif',
            'wakilKetuas',
            'subdivisis',
            'usulanMenunggu',
            'riwayatPengurus',
            'anggotaEligible',
            'daftarJabatan'
        ));
    }

    /**
     * Pembina menetapkan pengurus langsung dengan status aktif (atau mengganti pengurus aktif).
     */
    public function tetapkanPembina(Request $request, Ekskul $ekskul)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);
        $pembina = $user?->pembina;
        abort_unless($isKesiswaan || ($pembina && $ekskul->pembina_id === $pembina->id), 403, 'Akses tidak diizinkan.');

        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswas,id'],
            'jabatan' => ['required', 'in:ketua,wakil_ketua,sekretaris,bendahara'],
            'subdivisi_id' => ['nullable', 'exists:ekskuls,id'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'siswa_id.required' => 'Pilih salah satu anggota ekskul.',
            'siswa_id.exists' => 'Data siswa tidak ditemukan.',
            'jabatan.required' => 'Pilih jabatan pengurus.',
            'jabatan.in' => 'Jabatan tidak valid.',
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        $jabatan = $validated['jabatan'];
        $subdivisiId = ($jabatan === Pengurus::JABATAN_WAKIL_KETUA) ? ($validated['subdivisi_id'] ?? null) : null;
        if ($subdivisiId) {
            $subdivisi = Ekskul::findOrFail($subdivisiId);
            abort_unless($subdivisi->parent_id === $ekskul->id, 422, 'Sub divisi tidak sesuai dengan ekskul ini.');
        }

        // Ketua dapat dipilih dari master siswa oleh Pembina. Keanggotaannya
        // dibuat bersamaan dengan penetapan agar tidak tersimpan setengah.
        $pendaftaran = $subdivisiId
            ? Pendaftaran::where('ekskul_id', $subdivisiId)->where('siswa_id', $siswa->id)->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->latest('id')->first()
            : null;
        $pendaftaran ??= Pendaftaran::where('ekskul_id', $ekskul->id)->where('siswa_id', $siswa->id)->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->latest('id')->first();
        $pendaftaran ??= Pendaftaran::whereIn('ekskul_id', $ekskul->subdivisis()->pluck('id'))->where('siswa_id', $siswa->id)->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])->latest('id')->first();
        $pendaftaran ??= Pendaftaran::whereIn('ekskul_id', array_filter([$subdivisiId, $ekskul->id]))->where('siswa_id', $siswa->id)->latest('id')->first();

        $perluTambahAnggota = ! $pendaftaran?->isActive();
        if ($perluTambahAnggota) {
            if ($jabatan !== Pengurus::JABATAN_KETUA) {
                return back()->with('error', "{$siswa->nama} belum menjadi anggota aktif ekskul {$ekskul->nama_ekskul}.");
            }

            if ($ekskul->hasSubdivisi()) {
                return back()->with('error', 'Ketua pusat harus dipilih dari siswa yang sudah menjadi anggota aktif salah satu subdivisi.');
            }

            $siswa->loadMissing('user');
            if ($siswa->status !== 'aktif' || ! $siswa->user?->is_active) {
                return back()->with('error', 'Akun siswa yang dipilih tidak aktif.');
            }

            $candidate = $pendaftaran ?? new Pendaftaran;
            $candidate->ekskul_id = $ekskul->id;
            $candidate->setRelation('ekskul', $ekskul);
            if ($alasan = $siswa->alasanTidakDapatMenerima($candidate)) {
                return back()->with('error', $alasan);
            }

        }

        // 2. Jika jabatan yang dipilih adalah ketua, pastikan siswa belum menjadi ketua di ekskul lain
        if ($jabatan === Pengurus::JABATAN_KETUA) {
            $ekskulDipimpin = $siswa->ekskulDipimpin();
            if ($ekskulDipimpin && $ekskulDipimpin->id !== $ekskul->id) {
                return back()->with('error', "{$siswa->nama} saat ini sudah menjabat sebagai Ketua di ekskul {$ekskulDipimpin->nama_ekskul}. Satu siswa hanya dapat menjadi ketua pada 1 ekskul.");
            }
        }

        // 3. Pastikan siswa belum memegang jabatan aktif lain di ekskul ini (1 siswa 1 jabatan per ekskul)
        $jabatanAktifDiEkskulIni = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->first();

        if ($jabatanAktifDiEkskulIni) {
            $isSamePosition = ($jabatanAktifDiEkskulIni->jabatan === $jabatan && (int) $jabatanAktifDiEkskulIni->subdivisi_id === (int) $subdivisiId);
            if ($isSamePosition) {
                return back()->with('info', "{$siswa->nama} saat ini sudah aktif menjabat sebagai " . Pengurus::labelJabatan($jabatan) . " di ekskul ini.");
            }

            return back()->with('error', "{$siswa->nama} saat ini sudah menjabat sebagai " . Pengurus::labelJabatan($jabatanAktifDiEkskulIni->jabatan) . " di ekskul ini. Satu siswa hanya dapat memegang satu jabatan pada ekskul yang sama.");
        }

        if ($ekskul->ketua()?->id === $siswa->id && $jabatan !== Pengurus::JABATAN_KETUA) {
            return back()->with('error', "{$siswa->nama} adalah Ketua di ekskul ini dan tidak dapat merangkap jabatan lain.");
        }

        DB::transaction(function () use ($ekskul, $siswa, $jabatan, $subdivisiId, $validated, $perluTambahAnggota, &$pendaftaran) {
            $arsip = app(ArsipEkskulService::class);

            if ($perluTambahAnggota) {
                if ($pendaftaran) {
                    $pendaftaran->update([
                        'tanggal_daftar' => now()->toDateString(),
                        'status' => Pendaftaran::STATUS_DITERIMA,
                        'alasan' => 'Ditetapkan sebagai Ketua oleh Pembina',
                    ]);
                    $pendaftaran = $pendaftaran->fresh();
                } else {
                    $pendaftaran = Pendaftaran::create([
                        'siswa_id' => $siswa->id,
                        'ekskul_id' => $ekskul->id,
                        'tanggal_daftar' => now()->toDateString(),
                        'status' => Pendaftaran::STATUS_DITERIMA,
                        'alasan' => 'Ditetapkan sebagai Ketua oleh Pembina',
                    ]);
                }
            }

            // Jika jabatan ini sebelumnya sudah memiliki pengurus aktif, copot/ganti pengurus lama
            $queryLama = Pengurus::where('ekskul_id', $ekskul->id)
                ->where('jabatan', $jabatan)
                ->where('status', Pengurus::STATUS_AKTIF);

            if ($jabatan === Pengurus::JABATAN_WAKIL_KETUA) {
                $queryLama->where('subdivisi_id', $subdivisiId);
            }

            $pengurusLama = $queryLama->first();

            if ($pengurusLama) {
                $pengurusLama->update([
                    'status' => Pengurus::STATUS_SELESAI,
                    'catatan' => 'Digantikan oleh ' . $siswa->nama . ' pada ' . now()->translatedFormat('d M Y'),
                ]);

                // Jika jabatan yang diganti adalah ketua, turunkan jabatan di Siswa & riwayat arsip
                if ($jabatan === Pengurus::JABATAN_KETUA && $pengurusLama->siswa) {
                    $pengurusLama->siswa->update(['jabatan' => 'siswa']);
                    $arsip->akhiriPeriodeKetua($ekskul, $pengurusLama->siswa, null, RiwayatJabatan::ALASAN_DIGANTI);
                }
            }

            // Batalkan/selesaikan usulan pending untuk jabatan ini bila ada
            $queryPending = Pengurus::where('ekskul_id', $ekskul->id)
                ->where('jabatan', $jabatan)
                ->where('status', Pengurus::STATUS_MENUNGGU);

            if ($jabatan === Pengurus::JABATAN_WAKIL_KETUA) {
                $queryPending->where('subdivisi_id', $subdivisiId);
            }

            $queryPending->update([
                'status' => Pengurus::STATUS_DITOLAK,
                'catatan' => 'Dibatalkan karena pembina langsung menetapkan pengurus baru.',
            ]);

            // Tetapkan pengurus baru dengan status aktif
            Pengurus::create([
                'ekskul_id' => $ekskul->id,
                'subdivisi_id' => $subdivisiId,
                'siswa_id' => $siswa->id,
                'jabatan' => $jabatan,
                'status' => Pengurus::STATUS_AKTIF,
                'diusulkan_oleh' => 'pembina',
                'catatan' => $validated['catatan'] ?: 'Ditetapkan langsung oleh pembina',
            ]);

            if ($jabatan === Pengurus::JABATAN_WAKIL_KETUA && $subdivisiId) {
                $this->pindahkanKeanggotaanKetuaSubdivisi($ekskul, $ekskul->subdivisis()->findOrFail($subdivisiId), $siswa);
            }

            // Bila jabatan adalah ketua, update kolom jabatan siswa dan arsip riwayat
            if ($jabatan === Pengurus::JABATAN_KETUA) {
                $siswa->update(['jabatan' => 'ketua']);
                $arsip->mulaiPeriodeKetua($ekskul, $siswa);
            }
        });

        // Notifikasi ke siswa
        Notifikasi::create([
            'siswa_id' => $siswa->id,
            'judul' => 'Penetapan Pengurus Ekskul',
            'pesan' => "Selamat! Anda telah ditetapkan sebagai " . Pengurus::labelJabatan($jabatan) . " di ekskul {$ekskul->nama_ekskul} oleh Pembina.",
            'tipe' => 'info',
        ]);

        return back()->with('success', "Berhasil menetapkan {$siswa->nama} sebagai " . Pengurus::labelJabatan($jabatan) . " di ekskul {$ekskul->nama_ekskul}.");
    }

    /**
     * Pembina menyetujui usulan pengurus dari ketua (status berubah menjadi aktif).
     */
    public function approve(Request $request, Pengurus $pengurus)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);
        $pembina = $user?->pembina;
        $ekskul = $pengurus->ekskul;
        abort_unless($isKesiswaan || ($pembina && $ekskul && $ekskul->pembina_id === $pembina->id), 403, 'Akses tidak diizinkan.');

        if (! $pengurus->isMenunggu()) {
            return back()->with('error', 'Usulan ini sudah tidak dalam status menunggu.');
        }

        // Pastikan siswa masih berstatus diterima
        $pendaftaran = $pengurus->subdivisi_id
            ? Pendaftaran::where('ekskul_id', $pengurus->subdivisi_id)->where('siswa_id', $pengurus->siswa_id)->where('status', Pendaftaran::STATUS_DITERIMA)->first()
            : null;
        $pendaftaran ??= Pendaftaran::where('ekskul_id', $ekskul->id)->where('siswa_id', $pengurus->siswa_id)->where('status', Pendaftaran::STATUS_DITERIMA)->first();
        $pendaftaran ??= Pendaftaran::whereIn('ekskul_id', $ekskul->subdivisis()->pluck('id'))->where('siswa_id', $pengurus->siswa_id)->where('status', Pendaftaran::STATUS_DITERIMA)->first();

        if (! $pendaftaran) {
            $pengurus->update([
                'status' => Pengurus::STATUS_DITOLAK,
                'catatan' => 'Ditolak otomatis karena siswa bukan lagi anggota aktif.',
            ]);
            return back()->with('error', 'Siswa bukan anggota aktif berstatus diterima pada ekskul ini.');
        }

        // Pastikan siswa belum memegang jabatan aktif lain di ekskul ini
        $jabatanAktifDiEkskulIni = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $pengurus->siswa_id)
            ->where('id', '!=', $pengurus->id)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->first();

        if ($jabatanAktifDiEkskulIni) {
            return back()->with('error', "Tidak dapat menyetujui usulan: " . ($pengurus->siswa?->nama ?? 'Siswa') . " sudah menjabat sebagai " . Pengurus::labelJabatan($jabatanAktifDiEkskulIni->jabatan) . " di ekskul ini.");
        }

        if ($pengurus->jabatan === Pengurus::JABATAN_KETUA) {
            $ekskulDipimpin = $pengurus->siswa?->ekskulDipimpin();
            if ($ekskulDipimpin && $ekskulDipimpin->id !== $ekskul->id) {
                return back()->with('error', "Tidak dapat menyetujui usulan: " . ($pengurus->siswa?->nama ?? 'Siswa') . " sudah menjabat sebagai Ketua di ekskul {$ekskulDipimpin->nama_ekskul}.");
            }
        }

        DB::transaction(function () use ($ekskul, $pengurus, $request) {
            // Selesaikan pengurus aktif sebelumnya pada jabatan ini (jika ada)
            $queryLama = Pengurus::where('ekskul_id', $ekskul->id)
                ->where('jabatan', $pengurus->jabatan)
                ->where('status', Pengurus::STATUS_AKTIF);

            if ($pengurus->jabatan === Pengurus::JABATAN_WAKIL_KETUA) {
                $queryLama->where('subdivisi_id', $pengurus->subdivisi_id);
            }

            $pengurusLama = $queryLama->first();

            if ($pengurusLama) {
                $pengurusLama->update([
                    'status' => Pengurus::STATUS_SELESAI,
                    'catatan' => 'Digantikan melalui persetujuan usulan ketua atas nama ' . ($pengurus->siswa?->nama ?? '-'),
                ]);
            }

            // Setujui usulan ini menjadi aktif
            $pengurus->update([
                'status' => Pengurus::STATUS_AKTIF,
                'catatan' => $request->input('catatan') ?: 'Disetujui oleh pembina pada ' . now()->translatedFormat('d M Y H:i'),
            ]);

            if ($pengurus->jabatan === Pengurus::JABATAN_WAKIL_KETUA && $pengurus->subdivisi_id) {
                $this->pindahkanKeanggotaanKetuaSubdivisi($ekskul, $ekskul->subdivisis()->findOrFail($pengurus->subdivisi_id), $pengurus->siswa);
            }
        });

        // Notifikasi ke siswa
        if ($pengurus->siswa) {
            Notifikasi::create([
                'siswa_id' => $pengurus->siswa_id,
                'judul' => 'Usulan Pengurus Disetujui',
                'pesan' => "Selamat! Usulan Anda sebagai " . Pengurus::labelJabatan($pengurus->jabatan) . " di ekskul {$ekskul->nama_ekskul} telah disetujui Pembina.",
                'tipe' => 'info',
            ]);
        }

        // Notifikasi ke ketua
        $ketua = $ekskul->ketua();
        if ($ketua && $ketua->id !== $pengurus->siswa_id) {
            Notifikasi::create([
                'siswa_id' => $ketua->id,
                'judul' => 'Usulan Pengurus Disetujui',
                'pesan' => "Usulan Anda untuk mengangkat " . ($pengurus->siswa?->nama ?? 'siswa') . " sebagai " . Pengurus::labelJabatan($pengurus->jabatan) . " telah disetujui oleh Pembina.",
                'tipe' => 'info',
            ]);
        }

        return back()->with('success', "Usulan pengurus untuk " . ($pengurus->siswa?->nama ?? 'siswa') . " sebagai " . Pengurus::labelJabatan($pengurus->jabatan) . " berhasil disetujui.");
    }

    /**
     * Pembina menolak usulan pengurus dari ketua.
     */
    public function reject(Request $request, Pengurus $pengurus)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);
        $pembina = $user?->pembina;
        $ekskul = $pengurus->ekskul;
        abort_unless($isKesiswaan || ($pembina && $ekskul && $ekskul->pembina_id === $pembina->id), 403, 'Akses tidak diizinkan.');

        if (! $pengurus->isMenunggu()) {
            return back()->with('error', 'Usulan ini sudah tidak dalam status menunggu.');
        }

        $alasan = $request->input('catatan') ?: 'Usulan ditolak oleh pembina.';

        $pengurus->update([
            'status' => Pengurus::STATUS_DITOLAK,
            'catatan' => $alasan,
        ]);

        // Notifikasi ke ketua
        $ketua = $ekskul->ketua();
        if ($ketua) {
            Notifikasi::create([
                'siswa_id' => $ketua->id,
                'judul' => 'Usulan Pengurus Ditolak',
                'pesan' => "Usulan untuk " . ($pengurus->siswa?->nama ?? 'siswa') . " sebagai " . Pengurus::labelJabatan($pengurus->jabatan) . " ditolak oleh Pembina. Alasan: {$alasan}",
                'tipe' => 'info',
            ]);
        }

        return back()->with('success', "Usulan " . Pengurus::labelJabatan($pengurus->jabatan) . " telah ditolak.");
    }

    /**
     * Hanya pembina yang bisa mencopot pengurus yang sudah aktif.
     */
    public function copot(Request $request, Pengurus $pengurus)
    {
        $user = auth()->user();
        $isKesiswaan = $user && in_array($user->role, ['kesiswaan', 'admin'], true);
        $pembina = $user?->pembina;
        $ekskul = $pengurus->ekskul;
        abort_unless($isKesiswaan || ($pembina && $ekskul && $ekskul->pembina_id === $pembina->id), 403, 'Akses tidak diizinkan.');

        if (! $pengurus->isAktif()) {
            return back()->with('error', 'Pengurus ini tidak dalam status aktif.');
        }

        $alasan = $request->input('catatan') ?: 'Dicopot oleh pembina pada ' . now()->translatedFormat('d M Y');

        DB::transaction(function () use ($ekskul, $pengurus, $alasan) {
            $pengurus->update([
                'status' => Pengurus::STATUS_SELESAI,
                'catatan' => $alasan,
            ]);

            // Jika yang dicopot adalah ketua, turunkan role jabatan dan catat arsip
            if ($pengurus->jabatan === Pengurus::JABATAN_KETUA && $pengurus->siswa) {
                $pengurus->siswa->update(['jabatan' => 'siswa']);
                app(ArsipEkskulService::class)->akhiriPeriodeKetua($ekskul, $pengurus->siswa, null, RiwayatJabatan::ALASAN_DICOPOT);
            }
        });

        // Notifikasi ke siswa
        if ($pengurus->siswa) {
            Notifikasi::create([
                'siswa_id' => $pengurus->siswa_id,
                'judul' => 'Pencopotan Jabatan Pengurus',
                'pesan' => "Masa jabatan Anda sebagai " . Pengurus::labelJabatan($pengurus->jabatan) . " di ekskul {$ekskul->nama_ekskul} telah dicopot/diselesaikan oleh Pembina.",
                'tipe' => 'info',
            ]);
        }

        return back()->with('success', "Jabatan " . Pengurus::labelJabatan($pengurus->jabatan) . " untuk " . ($pengurus->siswa?->nama ?? 'siswa') . " berhasil dicopot.");
    }

    /* =========================================================================
     | KETUA ACTIONS
     | ========================================================================= */

    /**
     * Tampilan pengurus ekskul untuk Ketua.
     */
    public function indexKetua(Request $request)
    {
        $ekskul = $this->ekskul();
        $this->syncExistingKetua($ekskul);

        // Pengurus aktif
        $semuaPengurusAktif = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->with(['siswa.kelas', 'subdivisi'])
            ->get();

        $pengurusAktif = $semuaPengurusAktif->keyBy('jabatan');
        $wakilKetuas = $semuaPengurusAktif->where('jabatan', Pengurus::JABATAN_WAKIL_KETUA);
        $subdivisis = $ekskul->subdivisis()->orderBy('nama_ekskul')->get();

        // Usulan yang masih menunggu persetujuan pembina
        $usulanMenunggu = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('status', Pengurus::STATUS_MENUNGGU)
            ->with(['siswa.kelas', 'subdivisi'])
            ->latest()
            ->get();

        // Riwayat usulan pengurus yang pernah diajukan ketua
        $riwayatUsulan = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('diusulkan_oleh', 'ketua')
            ->whereIn('status', [Pengurus::STATUS_AKTIF, Pengurus::STATUS_DITOLAK, Pengurus::STATUS_SELESAI])
            ->with(['siswa.kelas', 'subdivisi'])
            ->latest()
            ->limit(15)
            ->get();

        // Anggota berstatus diterima yang belum memegang jabatan aktif di ekskul ini
        $currentKetuaId = $ekskul->ketua()?->id;
        $occupiedInThisEkskul = Pengurus::where('ekskul_id', $ekskul->id)
            ->whereIn('status', [Pengurus::STATUS_AKTIF, Pengurus::STATUS_MENUNGGU])
            ->pluck('siswa_id')
            ->when($currentKetuaId, fn ($col) => $col->push($currentKetuaId))
            ->unique();

        $anggotaEligible = Pendaftaran::whereIn('ekskul_id', array_merge([$ekskul->id], $subdivisis->pluck('id')->all()))
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->whereNotIn('siswa_id', $occupiedInThisEkskul)
            ->with('siswa.kelas')
            ->get()
            ->sortBy(fn ($item) => strtolower($item->siswa?->nama ?? ''))
            ->values();

        $daftarJabatanUsulan = Pengurus::daftarJabatanUsulanKetua();
        $daftarSemuaJabatan = Pengurus::daftarJabatan();

        return view('ketua.pengurus', compact(
            'ekskul',
            'pengurusAktif',
            'wakilKetuas',
            'subdivisis',
            'usulanMenunggu',
            'riwayatUsulan',
            'anggotaEligible',
            'daftarJabatanUsulan',
            'daftarSemuaJabatan'
        ));
    }

    /**
     * Ketua mengusulkan wakil ketua, sekretaris, atau bendahara (status menunggu).
     * Ketua TIDAK BISA mengusulkan ketua, dan TIDAK BISA mengganti/mencopot pengurus aktif.
     */
    public function usulkanKetua(Request $request)
    {
        $ekskul = $this->ekskul();

        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswas,id'],
            'jabatan' => ['required', 'in:wakil_ketua,sekretaris,bendahara'],
            'subdivisi_id' => ['nullable', 'exists:ekskuls,id'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'siswa_id.required' => 'Pilih salah satu anggota ekskul.',
            'siswa_id.exists' => 'Data siswa tidak ditemukan.',
            'jabatan.required' => 'Pilih jabatan yang diusulkan.',
            'jabatan.in' => 'Ketua hanya dapat mengusulkan Wakil Ketua, Sekretaris, dan Bendahara.',
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        $jabatan = $validated['jabatan'];
        $subdivisiId = ($jabatan === Pengurus::JABATAN_WAKIL_KETUA) ? ($validated['subdivisi_id'] ?? null) : null;
        if ($subdivisiId) {
            $subdivisi = Ekskul::findOrFail($subdivisiId);
            abort_unless($subdivisi->parent_id === $ekskul->id, 422, 'Sub divisi tidak sesuai dengan ekskul ini.');
        }

        // 1. Pastikan siswa adalah anggota ekskul ini dengan status diterima
        $pendaftaran = $subdivisiId
            ? Pendaftaran::where('ekskul_id', $subdivisiId)->where('siswa_id', $siswa->id)->where('status', Pendaftaran::STATUS_DITERIMA)->first()
            : null;
        $pendaftaran ??= Pendaftaran::where('ekskul_id', $ekskul->id)->where('siswa_id', $siswa->id)->where('status', Pendaftaran::STATUS_DITERIMA)->first();
        $pendaftaran ??= Pendaftaran::whereIn('ekskul_id', $ekskul->subdivisis()->pluck('id'))->where('siswa_id', $siswa->id)->where('status', Pendaftaran::STATUS_DITERIMA)->first();

        if (! $pendaftaran) {
            return back()->with('error', "{$siswa->nama} bukan anggota aktif berstatus diterima di ekskul ini.");
        }

        // 2. Pastikan siswa belum memegang jabatan aktif di ekskul ini (1 siswa 1 jabatan per ekskul)
        $jabatanAktifDiEkskulIni = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->first();

        if ($jabatanAktifDiEkskulIni) {
            return back()->with('error', "{$siswa->nama} saat ini sudah memegang jabatan " . Pengurus::labelJabatan($jabatanAktifDiEkskulIni->jabatan) . " di ekskul ini. Satu siswa hanya dapat memegang satu jabatan pada ekskul yang sama.");
        }

        if ($ekskul->ketua()?->id === $siswa->id) {
            return back()->with('error', "{$siswa->nama} adalah Ketua di ekskul ini dan tidak dapat diusulkan untuk jabatan lain.");
        }

        // 3. Pastikan tidak ada usulan yang sedang menunggu untuk jabatan yang sama
        $queryPending = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('jabatan', $jabatan)
            ->where('status', Pengurus::STATUS_MENUNGGU);

        if ($jabatan === Pengurus::JABATAN_WAKIL_KETUA) {
            $queryPending->where('subdivisi_id', $subdivisiId);
        }

        $pendingJabatan = $queryPending->first();

        if ($pendingJabatan) {
            $label = $jabatan === Pengurus::JABATAN_WAKIL_KETUA && $pendingJabatan->subdivisi
                ? "Wakil Ketua ({$pendingJabatan->subdivisi->nama_ekskul})"
                : Pengurus::labelJabatan($jabatan);
            return back()->with('error', "Sudah ada usulan {$label} atas nama {$pendingJabatan->siswa?->nama} yang sedang menunggu persetujuan Pembina.");
        }

        // 4. Pastikan siswa tersebut tidak sedang memiliki usulan pending di ekskul ini
        $pendingSiswa = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', Pengurus::STATUS_MENUNGGU)
            ->first();

        if ($pendingSiswa) {
            return back()->with('error', "{$siswa->nama} sudah diusulkan untuk jabatan " . Pengurus::labelJabatan($pendingSiswa->jabatan) . " di ekskul ini dan sedang menunggu persetujuan Pembina.");
        }

        $usulan = Pengurus::create([
            'ekskul_id' => $ekskul->id,
            'subdivisi_id' => $subdivisiId,
            'siswa_id' => $siswa->id,
            'jabatan' => $jabatan,
            'status' => Pengurus::STATUS_MENUNGGU,
            'diusulkan_oleh' => 'ketua',
            'catatan' => $validated['catatan'] ?: 'Diusulkan oleh Ketua Ekskul',
        ]);

        // Notifikasi ke pembina
        if ($ekskul->pembina) {
            Notifikasi::create([
                'pembina_id' => $ekskul->pembina->id,
                'judul' => 'Usulan Pengurus Ekskul Baru',
                'pesan' => "Ketua ekskul {$ekskul->nama_ekskul} mengusulkan {$siswa->nama} sebagai " . Pengurus::labelJabatan($jabatan) . ". Menunggu persetujuan Anda.",
                'tipe' => 'info',
            ]);
        }

        return back()->with('success', "Usulan pengurus untuk posisi " . Pengurus::labelJabatan($jabatan) . " atas nama {$siswa->nama} berhasil diajukan dan sedang menunggu persetujuan Pembina.");
    }

    private function pindahkanKeanggotaanKetuaSubdivisi(Ekskul $ekskulPusat, Ekskul $subdivisi, Siswa $siswa): void
    {
        $target = Pendaftaran::query()
            ->where('siswa_id', $siswa->id)
            ->where('ekskul_id', $subdivisi->id)
            ->lockForUpdate()
            ->first();
        $source = Pendaftaran::query()
            ->where('siswa_id', $siswa->id)
            ->whereIn('ekskul_id', array_merge([$ekskulPusat->id], $ekskulPusat->subdivisis()->pluck('id')->all()))
            ->where('ekskul_id', '!=', $subdivisi->id)
            ->whereIn('status', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->lockForUpdate()
            ->orderByRaw('CASE WHEN status IN (?, ?) THEN 0 ELSE 1 END', [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN])
            ->latest('id')
            ->first();

        if ($target) {
            if ($source) {
                RiwayatPendaftaran::create([
                    'pendaftaran_id' => $source->id,
                    'siswa_id' => $source->siswa_id,
                    'ekskul_id' => $source->ekskul_id,
                    'tanggal_daftar' => $source->tanggal_daftar,
                    'status' => $source->status,
                    'alasan' => 'Keanggotaan ketua subdivisi dialihkan ke '.$subdivisi->nama_ekskul.'.',
                    'dicatat_pada' => now(),
                ]);
                $source->update(['status' => Pendaftaran::STATUS_KELUAR, 'alasan' => 'Dipindahkan sebagai ketua subdivisi '.$subdivisi->nama_ekskul.'.']);
            }

            if (! in_array($target->status, [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN], true)) {
                RiwayatPendaftaran::create([
                    'pendaftaran_id' => $target->id,
                    'siswa_id' => $target->siswa_id,
                    'ekskul_id' => $target->ekskul_id,
                    'tanggal_daftar' => $target->tanggal_daftar,
                    'status' => $target->status,
                    'alasan' => 'Keanggotaan diaktifkan sebagai ketua subdivisi.',
                    'dicatat_pada' => now(),
                ]);
                $target->update(['status' => Pendaftaran::STATUS_DITERIMA, 'alasan' => 'Aktif sebagai ketua subdivisi.']);
            }

            return;
        }

        if (! $source) {
            return;
        }

        RiwayatPendaftaran::create([
            'pendaftaran_id' => $source->id,
            'siswa_id' => $source->siswa_id,
            'ekskul_id' => $source->ekskul_id,
            'tanggal_daftar' => $source->tanggal_daftar,
            'status' => $source->status,
            'alasan' => 'Dipindahkan otomatis karena ditetapkan sebagai ketua subdivisi '.$subdivisi->nama_ekskul.'.',
            'dicatat_pada' => now(),
        ]);
        $source->update([
            'ekskul_id' => $subdivisi->id,
            'status' => in_array($source->status, [Pendaftaran::STATUS_DITERIMA, Pendaftaran::STATUS_PERINGATAN], true)
                ? $source->status
                : Pendaftaran::STATUS_DITERIMA,
            'alasan' => 'Dipindahkan otomatis sebagai ketua subdivisi.',
        ]);
    }
}
