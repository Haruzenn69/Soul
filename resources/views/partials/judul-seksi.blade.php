@php
    /**
     * Judul seksi di halaman profil ekskul.
     *
     * Angka di sebelah kanan bukan hiasan: setiap seksi hanya dirender kalau
     * isinya ada, jadi jumlahnya adalah informasi, bukan label.
     *
     * @var string $judul
     * @var int|null $jumlah
     */
    $jumlah = $jumlah ?? null;
@endphp

<div class="flex items-baseline gap-3 border-b border-slate-200 pb-3">
    <h2 class="text-[22px] font-extrabold tracking-[-0.02em] text-slate-900">{{ $judul }}</h2>
    @if ($jumlah !== null)
        <span class="text-[13px] font-semibold tabular-nums text-slate-500">{{ $jumlah }}</span>
    @endif
</div>
