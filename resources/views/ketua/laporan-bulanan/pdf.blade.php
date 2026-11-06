<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 10mm;
        }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11pt; 
            color: #000; 
            margin: 0; 
            padding: 0; 
            line-height: 1.35;
        }

        /* HEADER / KOP SURAT */
        .header-table { 
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .header-table .logo-cell {
            width: 80px;
            text-align: center;
        }
        .header-table .logo-cell img {
            width: 100px;
            height: auto;
        }
        .header-table .text-cell { 
            text-align: center;
        }
        .header-table p { font-size: 9.5pt; margin: 0.3px 0; }
        .header-table .school-name { font-size: 13pt; font-weight: bold; margin: 4px 0 2px; text-transform: uppercase; }
        .header-border { border-bottom: 3px double #000; margin-top: 4px; margin-bottom: 12px; }

        /* JUDUL */
        .title { 
            text-align: center; 
            font-size: 12pt; 
            font-weight: normal; 
            margin: 6px 0 3px; 
            text-transform: uppercase; 
        }
        .subtitle {
            text-align: center;
            font-size: 11pt;
            font-weight: normal;
            margin: 0 0 12px;
            text-transform: uppercase;
        }

        /* META TABLE (Nama Ekskul, Pelatih) */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 11pt;
        }
        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
            font-weight: normal;
        }
        .meta-table td.meta-label {
            width: 120px;
        }
        .meta-table td.meta-colon {
            width: 15px;
        }
        .meta-table td.meta-value {
        }

        /* TABEL KEGIATAN PER BARIS */
        .kegiatan-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
            table-layout: fixed;
        }
        .kegiatan-table tr {
            page-break-inside: avoid;
        }
        .penjelasan-col {
            width: 50%;
            border: 1px solid #000;
            padding: 10px 12px;
            vertical-align: top;
            font-size: 10pt;
            line-height: 1.4;
            text-align: justify;
        }
        .kegiatan-nama {
            font-size: 10pt;
            font-weight: normal;
            margin-bottom: 3px;
        }
        .kegiatan-tgl {
            font-size: 9.5pt;
            font-weight: normal;
            color: #222;
            margin-bottom: 8px;
        }
        .kegiatan-desc {
            font-size: 9.5pt;
            font-weight: normal;
            color: #000;
            text-align: justify;
        }

        .foto-col {
            width: 50%;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            background: #fff;
            height: 150px;
        }
        .foto-col img {
            max-width: 95%;
            max-height: 145px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .no-foto {
            color: #777;
            font-size: 9pt;
            font-style: italic;
        }

        /* TANDA TANGAN (PALING BAWAH SEBELAH KANAN) */
        .ttd-container { 
            width: 100%; 
            margin-top: 30px; 
            page-break-inside: avoid;
        }
        .ttd { 
            float: right; 
            width: 250px; 
            text-align: left; 
        }
        .ttd p { 
            font-size: 11pt; 
            margin: 2px 0; 
            font-weight: normal;
        }
        .ttd .space { 
            height: 55px; 
        }
        .ttd .nama-pelatih {
            font-weight: normal;
            text-decoration: underline;
        }

        /* PAGE BREAK */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    @php
        \Carbon\Carbon::setLocale('id');

        $logoPath = public_path('images/logo-kop.webp');
        $logoSrc = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoMime = mime_content_type($logoPath);
            $logoSrc = 'data:' . $logoMime . ';base64,' . $logoData;
        }

        $pelatihModel = $laporan->ekskul->pelatih ?? null;
        $namaPelatih = ($pelatihModel && filled($pelatihModel->nama)) ? $pelatihModel->nama : '-';

        $tanggalSurat = $laporan->tanggal_surat 
            ?? \Carbon\Carbon::parse($laporan->bulan)->endOfMonth()->translatedFormat('d F Y');

        // Query atau siapkan kegiatan bulanan
        $semuaKegiatans = collect($rutinKegiatans ?? [])
            ->merge($eventKegiatans ?? []);

        if ($semuaKegiatans->isEmpty()) {
            $tahun = substr($laporan->bulan, 0, 4);
            $bulanNum = substr($laporan->bulan, 5, 2);
            $semuaKegiatans = \App\Models\Kegiatan::where('ekskul_id', $laporan->ekskul_id)
                ->whereYear('tanggal_kegiatan', $tahun)
                ->whereMonth('tanggal_kegiatan', $bulanNum)
                ->orderBy('tanggal_kegiatan')
                ->get();
        } else {
            $semuaKegiatans = $semuaKegiatans->sortBy('tanggal_kegiatan')->values();
        }
    @endphp

    <!-- HEADER / KOP SURAT -->
    <table class="header-table" cellspacing="0">
        <tr>
            <td class="logo-cell">
                @if(!empty($logoSrc))
                    <img src="{{ $logoSrc }}" alt="Logo">
                @endif
            </td>
            <td class="text-cell">
                <p>PEMERINTAH DAERAH PROVINSI JAWA BARAT</p>
                <p>DINAS PENDIDIKAN</p>
                <p>CABANG DINAS PENDIDIKAN WILAYAH VII</p>
                <div class="school-name">SMK NEGERI 11 BANDUNG</div>
                <p><strong>Bisnis dan Manajemen &ndash; Teknologi Informasi &ndash; Seni dan Ekonomi Kreatif</strong></p>
                <p>Jl. Budi Cilember Sukaraja Cicendo (022) 6652442 Fax. (022) 6613508 Bandung 40175</p>
                <p>http://smkn11bdg.sch.id &bull; E-mail: smkn11bdg@gmail.com NPSN: 20219175 NSS: 34.1.02.60.03.001</p>
            </td>
        </tr>
    </table>
    <div class="header-border"></div>

    <!-- JUDUL LAPORAN -->
    <div class="title">LAPORAN KEGIATAN</div>
    <div class="subtitle">BULAN : {{ \Carbon\Carbon::parse($laporan->bulan)->translatedFormat('F Y') }}</div>

    <!-- META INFORMATION -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Nama Ekskul</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $laporan->ekskul->nama_ekskul ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Pelatih</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $namaPelatih }}</td>
        </tr>
    </table>

    <!-- TABEL KONTEN KEGIATAN (KIRI: PENJELASAN, KANAN: FOTO DOKUMENTASI) -->
    <table class="kegiatan-table">
        @forelse($semuaKegiatans as $kegiatan)
            @php
                $imgSrc = null;
                if ($kegiatan->dokumentasi) {
                    $p = storage_path('app/public/' . $kegiatan->dokumentasi);
                    if (file_exists($p)) {
                        $imgData = base64_encode(file_get_contents($p));
                        $mime = mime_content_type($p);
                        $imgSrc = 'data:' . $mime . ';base64,' . $imgData;
                    }
                }
            @endphp
            <tr>
                {{-- Kolom Kiri: Penjelasan Kegiatan --}}
                <td class="penjelasan-col">
                    <div class="kegiatan-nama">Kegiatan: {{ $kegiatan->materi }}</div>
                    <div class="kegiatan-tgl">{{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('l, d M Y') : '-' }}</div>
                    <div class="kegiatan-desc">
                        {{ $kegiatan->deskripsi ?: 'Tidak ada deskripsi kegiatan.' }}
                    </div>
                </td>

                {{-- Kolom Kanan: Foto Dokumentasi --}}
                <td class="foto-col">
                    @if($imgSrc)
                        <img src="{{ $imgSrc }}" alt="Dokumentasi">
                    @else
                        <div class="no-foto">(Tidak ada dokumentasi)</div>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2" style="border: 1px dashed #999; padding: 25px; text-align: center; color: #555;">
                    Belum ada kegiatan yang tercatat untuk bulan ini.
                </td>
            </tr>
        @endforelse
    </table>

    <!-- TANDA TANGAN (PALING BAWAH SEBELAH KANAN) -->
    <div class="ttd-container">
        <div class="ttd">
            <p>Bandung, {{ $tanggalSurat }}</p>
            <p>Pelatih Ekstrakurikuler {{ $laporan->ekskul->nama_ekskul ?? '' }}</p>
            <div class="space"></div>
            <p class="nama-pelatih">{{ $namaPelatih !== '-' ? $namaPelatih : '..................................................' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>
