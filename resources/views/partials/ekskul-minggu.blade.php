@php
    /**
     * Sumbu minggu: kapan ekskul bertemu.
     *
     * Ini data, bukan hiasan — jadwallah yang menentukan seorang siswa bisa
     * bergabung atau tidak, jadi ia digambar, bukan ditulis sebagai teks abu.
     * Kalau format jadwal tidak dikenali, teks aslinya ditampilkan apa adanya.
     *
     * @var \App\Models\Ekskul $ekskul
     * @var bool $besar
     * @var string $kelas
     */
    $jadwal = \App\Support\EkskulInfo::jadwal($ekskul->jadwal);
    $besar = $besar ?? false;
    $kelas = $kelas ?? '';
@endphp

<div class="soul-jadwal {{ $kelas }} @if ($besar) soul-minggu--besar @endif">
    @if ($jadwal['hari'])
        <div
            class="soul-minggu"
            role="img"
            aria-label="Bertemu setiap hari {{ \App\Support\EkskulInfo::hariTerbaca($jadwal['hari']) }}"
        >
            @foreach (\App\Support\EkskulInfo::HARI as $angka => $label)
                @php $aktif = in_array($angka, $jadwal['hari'], true); @endphp
                <div class="soul-minggu-kolom">
                    <span @class(['soul-minggu-batang', 'soul-minggu-batang--aktif' => $aktif])></span>
                    <span class="soul-minggu-sumbu"></span>
                    <span @class(['soul-minggu-label', 'soul-minggu-label--aktif' => $aktif])>{{ $label }}</span>
                </div>
            @endforeach
        </div>

        <p class="mt-2.5 text-[13px] font-semibold tabular-nums text-slate-800">
            {{ $jadwal['waktu'] ?? $jadwal['mentah'] }}
        </p>
    @else
        <p class="text-[13px] leading-relaxed text-slate-500">
            {{ $jadwal['mentah'] ?? 'Jadwal belum diatur' }}
        </p>
    @endif
</div>
