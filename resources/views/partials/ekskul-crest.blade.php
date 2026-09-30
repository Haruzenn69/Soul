@php
    /**
     * Logo ekskul, atau monogram kalau logonya belum diunggah.
     *
     * Bingkainya sengaja netral: warna di halaman ini sudah dipakai untuk
     * status pendaftaran, jadi crest tidak boleh merebutkannya.
     *
     * @var \App\Models\Ekskul $ekskul
     * @var string $ukuran  sm | md | lg
     */
    $ukuran = $ukuran ?? 'sm';
    $logo = \App\Support\EkskulInfo::logo($ekskul->logo);
    $monogram = \Illuminate\Support\Str::of($ekskul->nama_ekskul)->upper()->substr(0, 2)->value();
@endphp

<span @class([
    'soul-crest',
    'soul-crest--md' => $ukuran === 'md',
    'soul-crest--lg' => $ukuran === 'lg',
])>
    @if ($logo)
        <img
            src="{{ $logo }}"
            alt="Logo {{ $ekskul->nama_ekskul }}"
            class="h-full w-full object-contain p-1"
            loading="lazy"
        >
    @else
        <span @class(['soul-crest-mono', 'soul-crest-mono--lg' => $ukuran === 'lg'])>{{ $monogram }}</span>
    @endif
</span>
