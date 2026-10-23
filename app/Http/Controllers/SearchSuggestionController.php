<?php

namespace App\Http\Controllers;

use App\Models\Ekskul;
use App\Models\Kelas;
use App\Models\Pembina;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchSuggestionController extends Controller
{
    /**
     * Pencarian global dengan suggestion seperti Google.
     *
     * Mengembalikan maksimal 12 hasil, sudah Includes URL tujuan.
     */
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['items' => [], 'query' => $q]);
        }

        $role = Auth::user()?->role ?? 'guest';
        $limit = 6;
        $items = [];

        if (in_array($role, ['kesiswaan', 'admin'], true)) {
            $items = $this->kesiswaanSuggestions($q, $limit);
        } elseif ($role === 'pembina') {
            $items = $this->pembinaSuggestions($q, $limit);
        } elseif ($role === 'siswa') {
            $items = $this->siswaSuggestions($q, $limit);
        }

        return response()->json([
            'items' => array_slice($items, 0, 12),
            'query' => $q,
        ]);
    }

    /**
     * @return array<int, array{label:string, meta:string, url:string, type:string}>
     */
    private function kesiswaanSuggestions(string $q, int $limit): array
    {
        $items = [];
        $like = "%{$q}%";

        $siswa = Siswa::query()
            ->where(fn ($query) => $query->where('nama', 'like', $like)->orWhere('nis', 'like', $like))
            ->orderBy('nama')
            ->limit($limit)
            ->get();

        foreach ($siswa as $s) {
            $items[] = [
                'label' => $s->nama,
                'meta' => 'Siswa · '.$s->nis,
                'url' => route('kesiswaan.siswa.index', ['q' => $s->nis]),
                'type' => 'Siswa',
            ];
        }

        $pembina = Pembina::query()
            ->where(fn ($query) => $query->where('nama', 'like', $like)->orWhere('nip', 'like', $like))
            ->orderBy('nama')
            ->limit($limit)
            ->get();

        foreach ($pembina as $p) {
            $items[] = [
                'label' => $p->nama,
                'meta' => 'Pembina · '.$p->nip,
                'url' => route('kesiswaan.pembina.index', ['q' => $p->nip]),
                'type' => 'Pembina',
            ];
        }

        $kelas = Kelas::query()
            ->with('tahunAjaran')
            ->where('nama', 'like', $like)
            ->orderBy('nama')
            ->limit($limit)
            ->get();

        foreach ($kelas as $k) {
            $items[] = [
                'label' => $k->nama,
                'meta' => 'Kelas · '.($k->tahunAjaran?->nama ?? '-'),
                'url' => route('kesiswaan.kelas.show', $k),
                'type' => 'Kelas',
            ];
        }

        $ekskul = Ekskul::query()
            ->where('nama_ekskul', 'like', $like)
            ->orderBy('nama_ekskul')
            ->limit($limit)
            ->get();

        foreach ($ekskul as $e) {
            $items[] = [
                'label' => $e->nama_ekskul,
                'meta' => 'Ekskul',
                'url' => route('kesiswaan.ekskuls.show', $e),
                'type' => 'Ekskul',
            ];
        }

        return $items;
    }

    /**
     * @return array<int, array{label:string, meta:string, url:string, type:string}>
     */
    private function pembinaSuggestions(string $q, int $limit): array
    {
        $items = [];
        $like = "%{$q}%";

        $siswa = Siswa::query()
            ->where(fn ($query) => $query->where('nama', 'like', $like)->orWhere('nis', 'like', $like))
            ->orderBy('nama')
            ->limit($limit)
            ->get();

        foreach ($siswa as $s) {
            $items[] = [
                'label' => $s->nama,
                'meta' => 'Siswa · '.$s->nis,
                'url' => route('pembina.anggota', ['q' => $s->nis]),
                'type' => 'Siswa',
            ];
        }

        $ekskul = Ekskul::query()
            ->where('nama_ekskul', 'like', $like)
            ->orderBy('nama_ekskul')
            ->limit($limit)
            ->get();

        foreach ($ekskul as $e) {
            $items[] = [
                'label' => $e->nama_ekskul,
                'meta' => 'Ekskul',
                'url' => route('pembina.ekskul.pilih-ketua-page', ['ekskul' => $e->id]),
                'type' => 'Ekskul',
            ];
        }

        return $items;
    }

    /**
     * @return array<int, array{label:string, meta:string, url:string, type:string}>
     */
    private function siswaSuggestions(string $q, int $limit): array
    {
        $items = [];

        $ekskul = Ekskul::query()
            ->where('nama_ekskul', 'like', "%{$q}%")
            ->orderBy('nama_ekskul')
            ->limit($limit)
            ->get();

        foreach ($ekskul as $e) {
            $items[] = [
                'label' => $e->nama_ekskul,
                'meta' => 'Ekskul',
                'url' => route('siswa.katalog', ['q' => $e->nama_ekskul]),
                'type' => 'Ekskul',
            ];
        }

        return $items;
    }
}
