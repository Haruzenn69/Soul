<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiswaController extends Controller
{
    private const SORTABLE = ['nama', 'nis', 'angkatan', 'kelas', 'created_at'];

    public function index(Request $request): View
    {
        $sort = in_array($request->input('sort'), self::SORTABLE, true) ? $request->input('sort') : 'nama';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $siswas = Siswa::with('kelas')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = '%'.$request->input('q').'%';
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama', 'like', $q)
                        ->orWhere('nis', 'like', $q)
                        ->orWhere('email', 'like', $q);
                });
            })
            ->when($request->filled('kelas_id'), fn ($query) => $query->where('kelas_id', $request->input('kelas_id')))
            ->when($request->filled('angkatan'), fn ($query) => $query->where('angkatan', $request->input('angkatan')))
            ->when($request->filled('jenis_kelamin'), fn ($query) => $query->where('jenis_kelamin', $request->input('jenis_kelamin')))
            ->when($request->filled('jabatan'), fn ($query) => $query->where('jabatan', $request->input('jabatan')))
            ->when($sort === 'kelas', fn ($query) => $query->leftJoin('kelas', 'kelas.id', '=', 'siswas.kelas_id')->select('siswas.*')->orderBy('kelas.nama', $direction)->orderBy('siswas.nama'))
            ->when($sort !== 'kelas', fn ($query) => $query->orderBy($sort, $direction))
            ->paginate(15)
            ->withQueryString();

        return view('kesiswaan.siswa.index', [
            'siswas' => $siswas,
            'kelasList' => Kelas::orderBy('nama')->get(),
            'angkatanList' => Siswa::whereNotNull('angkatan')->where('angkatan', '!=', '')
                ->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan'),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
}
