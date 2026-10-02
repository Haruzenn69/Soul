@php
    /**
     * Field pencarian ekskul, dipakai di navbar dan di mobile.
     *
     * @var string $aksi
     * @var string $idCari
     * @var string $kelas  Kelas visibilitas dari pemanggil.
     */
    $aksi = $aksi ?? route('siswa.katalog');
    $idCari = $idCari ?? 'cari-ekskul';
    $kelas = $kelas ?? '';
    $status = request('status');
@endphp

<form method="GET" action="{{ $aksi }}" role="search" class="relative {{ $kelas }}">
    <label for="{{ $idCari }}" class="sr-only">Cari ekskul</label>

    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </span>

    <input
        type="text"
        id="{{ $idCari }}"
        name="cari"
        value="{{ request('cari') }}"
        placeholder="Cari nama ekskul atau pembina"
        autocomplete="off"
        @class([
            'w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 text-[13px] text-slate-800',
            'placeholder:text-slate-400 focus:border-[#2563eb] focus:outline-none focus:ring-4 focus:ring-sky-100',
            'pr-9' => request('cari'),
            'pr-3' => ! request('cari'),
        ])
    >

    @if ($status)
        <input type="hidden" name="status" value="{{ $status }}">
    @endif

    @if (request('cari'))
        <a
            href="{{ $status ? $aksi.'?status='.$status : $aksi }}"
            class="absolute right-2 top-1/2 flex h-6 w-6 -translate-y-1/2 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2563eb]"
            aria-label="Hapus kata kunci pencarian"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
    @endif
</form>
