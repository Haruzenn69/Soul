<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\KetuaEkskul;
use App\Models\Ekskul;
use App\Models\Notifikasi;
use App\Models\Pendaftaran;
use App\Models\Pengurus;
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
        $pembina = auth()->user()?->pembina;

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
        $ekskul = $this->resolvePembinaEkskul($request);

        if ($ekskul) {
            $this->syncExistingKetua($ekskul);

            // 4 Jabatan Pokok
            $pengurusAktif = Pengurus::where('ekskul_id', $ekskul->id)
                ->where('status', Pengurus::STATUS_AKTIF)
                ->with(['siswa.kelas', 'siswa.pendaftarans'])
                ->get()
                ->keyBy('jabatan');

            // Usulan dari ketua yang masih menunggu
            $usulanMenunggu = Pengurus::where('ekskul_id', $ekskul->id)
                ->where('status', Pengurus::STATUS_MENUNGGU)
                ->with(['siswa.kelas'])
                ->latest()
                ->get();

            // Riwayat pengurus (selesai atau ditolak)
            $riwayatPengurus = Pengurus::where('ekskul_id', $ekskul->id)
                ->whereIn('status', [Pengurus::STATUS_SELESAI, Pengurus::STATUS_DITOLAK])
                ->with(['siswa.kelas'])
                ->latest()
                ->limit(20)
                ->get();

            // Anggota ekskul berstatus 'diterima' yang berhak dipilih menjadi pengurus
            $anggotaEligible = Pendaftaran::where('ekskul_id', $ekskul->id)
                ->where('status', Pendaftaran::STATUS_DITERIMA)
                ->with('siswa.kelas')
                ->get()
                ->sortBy(fn ($item) => strtolower($item->siswa?->nama ?? ''))
                ->values();
        } else {
            $pengurusAktif = collect();
            $usulanMenunggu = collect();
            $riwayatPengurus = collect();
            $anggotaEligible = collect();
        }

        $daftarJabatan = Pengurus::daftarJabatan();

        return view('pembina.pengurus', compact(
            'ekskuls',
            'ekskul',
            'pengurusAktif',
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
        $pembina = auth()->user()?->pembina;
        abort_unless($pembina && $ekskul->pembina_id === $pembina->id, 403, 'Akses tidak diizinkan.');

        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswas,id'],
            'jabatan' => ['required', 'in:ketua,wakil_ketua,sekretaris,bendahara'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'siswa_id.required' => 'Pilih salah satu anggota ekskul.',
            'siswa_id.exists' => 'Data siswa tidak ditemukan.',
            'jabatan.required' => 'Pilih jabatan pengurus.',
            'jabatan.in' => 'Jabatan tidak valid.',
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        $jabatan = $validated['jabatan'];

        // 1. Pastikan siswa adalah anggota ekskul ini dengan status diterima
        $pendaftaran = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->first();

        if (! $pendaftaran) {
            return back()->with('error', "{$siswa->nama} bukan anggota aktif berstatus diterima pada ekskul {$ekskul->nama_ekskul}.");
        }

        // 2. Pastikan siswa belum memegang jabatan aktif lain di ekskul ini (1 orang per jabatan)
        $jabatanAktifLain = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->first();

        if ($jabatanAktifLain) {
            if ($jabatanAktifLain->jabatan === $jabatan) {
                return back()->with('info', "{$siswa->nama} saat ini sudah aktif menjabat sebagai " . Pengurus::labelJabatan($jabatan) . ".");
            }
            return back()->with('error', "{$siswa->nama} saat ini sudah menjabat sebagai " . Pengurus::labelJabatan($jabatanAktifLain->jabatan) . " di ekskul ini. Satu siswa hanya dapat memegang satu jabatan.");
        }

        // 3. Jika jabatan yang dipilih adalah ketua, validasi aturan ketua ekskul sistem
        if ($jabatan === Pengurus::JABATAN_KETUA) {
            $ekskulDipimpin = $siswa->ekskulDipimpin();
            if ($ekskulDipimpin && $ekskulDipimpin->id !== $ekskul->id) {
                return back()->with('error', "{$siswa->nama} saat ini sudah menjabat sebagai Ketua di ekskul {$ekskulDipimpin->nama_ekskul}. Satu siswa hanya dapat menjadi ketua pada 1 ekskul.");
            }
        }

        DB::transaction(function () use ($ekskul, $siswa, $jabatan, $validated) {
            $arsip = app(ArsipEkskulService::class);

            // Jika jabatan ini sebelumnya sudah memiliki pengurus aktif, copot/ganti pengurus lama
            $pengurusLama = Pengurus::where('ekskul_id', $ekskul->id)
                ->where('jabatan', $jabatan)
                ->where('status', Pengurus::STATUS_AKTIF)
                ->first();

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
            Pengurus::where('ekskul_id', $ekskul->id)
                ->where('jabatan', $jabatan)
                ->where('status', Pengurus::STATUS_MENUNGGU)
                ->update([
                    'status' => Pengurus::STATUS_DITOLAK,
                    'catatan' => 'Dibatalkan karena pembina langsung menetapkan pengurus baru.',
                ]);

            // Tetapkan pengurus baru dengan status aktif
            Pengurus::create([
                'ekskul_id' => $ekskul->id,
                'siswa_id' => $siswa->id,
                'jabatan' => $jabatan,
                'status' => Pengurus::STATUS_AKTIF,
                'diusulkan_oleh' => 'pembina',
                'catatan' => $validated['catatan'] ?: 'Ditetapkan langsung oleh pembina',
            ]);

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
        $pembina = auth()->user()?->pembina;
        $ekskul = $pengurus->ekskul;
        abort_unless($pembina && $ekskul && $ekskul->pembina_id === $pembina->id, 403, 'Akses tidak diizinkan.');

        if (! $pengurus->isMenunggu()) {
            return back()->with('error', 'Usulan ini sudah tidak dalam status menunggu.');
        }

        // Pastikan siswa masih berstatus diterima
        $pendaftaran = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $pengurus->siswa_id)
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->first();

        if (! $pendaftaran) {
            $pengurus->update([
                'status' => Pengurus::STATUS_DITOLAK,
                'catatan' => 'Ditolak otomatis karena siswa bukan lagi anggota aktif.',
            ]);
            return back()->with('error', 'Siswa bukan anggota aktif berstatus diterima pada ekskul ini.');
        }

        DB::transaction(function () use ($ekskul, $pengurus, $request) {
            // Selesaikan pengurus aktif sebelumnya pada jabatan ini (jika ada)
            $pengurusLama = Pengurus::where('ekskul_id', $ekskul->id)
                ->where('jabatan', $pengurus->jabatan)
                ->where('status', Pengurus::STATUS_AKTIF)
                ->first();

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
        $pembina = auth()->user()?->pembina;
        $ekskul = $pengurus->ekskul;
        abort_unless($pembina && $ekskul && $ekskul->pembina_id === $pembina->id, 403, 'Akses tidak diizinkan.');

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
        $pembina = auth()->user()?->pembina;
        $ekskul = $pengurus->ekskul;
        abort_unless($pembina && $ekskul && $ekskul->pembina_id === $pembina->id, 403, 'Akses tidak diizinkan.');

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

        // 4 Jabatan Pokok
        $pengurusAktif = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->with(['siswa.kelas'])
            ->get()
            ->keyBy('jabatan');

        // Usulan yang masih menunggu persetujuan pembina
        $usulanMenunggu = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('status', Pengurus::STATUS_MENUNGGU)
            ->with(['siswa.kelas'])
            ->latest()
            ->get();

        // Riwayat usulan pengurus yang pernah diajukan ketua
        $riwayatUsulan = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('diusulkan_oleh', 'ketua')
            ->whereIn('status', [Pengurus::STATUS_AKTIF, Pengurus::STATUS_DITOLAK, Pengurus::STATUS_SELESAI])
            ->with(['siswa.kelas'])
            ->latest()
            ->limit(15)
            ->get();

        // Anggota berstatus diterima yang dapat diusulkan
        $anggotaEligible = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->with('siswa.kelas')
            ->get()
            ->sortBy(fn ($item) => strtolower($item->siswa?->nama ?? ''))
            ->values();

        $daftarJabatanUsulan = Pengurus::daftarJabatanUsulanKetua();
        $daftarSemuaJabatan = Pengurus::daftarJabatan();

        return view('ketua.pengurus', compact(
            'ekskul',
            'pengurusAktif',
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
            'catatan' => ['nullable', 'string', 'max:500'],
        ], [
            'siswa_id.required' => 'Pilih salah satu anggota ekskul.',
            'siswa_id.exists' => 'Data siswa tidak ditemukan.',
            'jabatan.required' => 'Pilih jabatan yang diusulkan.',
            'jabatan.in' => 'Ketua hanya dapat mengusulkan Wakil Ketua, Sekretaris, dan Bendahara.',
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        $jabatan = $validated['jabatan'];

        // 1. Pastikan siswa adalah anggota ekskul ini dengan status diterima
        $pendaftaran = Pendaftaran::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', Pendaftaran::STATUS_DITERIMA)
            ->first();

        if (! $pendaftaran) {
            return back()->with('error', "{$siswa->nama} bukan anggota aktif berstatus diterima di ekskul ini.");
        }

        // 2. Pastikan siswa belum memegang jabatan aktif di ekskul ini
        $jabatanAktif = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', Pengurus::STATUS_AKTIF)
            ->first();

        if ($jabatanAktif) {
            return back()->with('error', "{$siswa->nama} sudah memegang jabatan " . Pengurus::labelJabatan($jabatanAktif->jabatan) . " di ekskul ini.");
        }

        // 3. Pastikan tidak ada usulan yang sedang menunggu untuk jabatan yang sama
        $pendingJabatan = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('jabatan', $jabatan)
            ->where('status', Pengurus::STATUS_MENUNGGU)
            ->first();

        if ($pendingJabatan) {
            return back()->with('error', "Sudah ada usulan " . Pengurus::labelJabatan($jabatan) . " atas nama {$pendingJabatan->siswa?->nama} yang sedang menunggu persetujuan Pembina.");
        }

        // 4. Pastikan siswa tersebut tidak sedang memiliki usulan pending untuk jabatan lain
        $pendingSiswa = Pengurus::where('ekskul_id', $ekskul->id)
            ->where('siswa_id', $siswa->id)
            ->where('status', Pengurus::STATUS_MENUNGGU)
            ->first();

        if ($pendingSiswa) {
            return back()->with('error', "{$siswa->nama} sudah diusulkan untuk jabatan " . Pengurus::labelJabatan($pendingSiswa->jabatan) . " dan sedang menunggu persetujuan Pembina.");
        }

        $usulan = Pengurus::create([
            'ekskul_id' => $ekskul->id,
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
}
