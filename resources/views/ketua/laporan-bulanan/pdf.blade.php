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
            font-size: 12pt; 
            color: #000; 
            margin: 0; 
            padding: 0; 
            line-height: 1.4;
        }

        /* HEADER / KOP SURAT */
        .header-table { 
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
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
            width: 110px;
            height: auto;
        }
        .header-table .text-cell { 
            text-align: center;
        }
        .header-table p { font-size: 10pt; margin: 0.5px 0; }
        .header-table .school-name { font-size: 14pt; font-weight: bold; margin: 5px 0 2px; text-transform: uppercase; }
        .header-border { border-bottom: 3px double #000; margin-top: 5px; margin-bottom: 20px; }

        /* JUDUL */
        .title { 
            text-align: center; 
            font-size: 12pt; 
            font-weight: bold; 
            margin: 15px 0 20px; 
            text-transform: uppercase; 
        }

        /* INFO TABEL */
        .info-table { 
            width: 85%; 
            margin: 0 auto 20px; 
            border-collapse: collapse;
        }
        .info-table td { 
            padding: 2px 0; 
            font-size: 12pt; 
            vertical-align: top; 
        }
        .info-table td.label { 
            width: 180px; 
        }

        /* BAGIAN / SECTION */
        .section { 
            margin-bottom: 15px; 
        }
        .section-title { 
            font-size: 12pt; 
            font-weight: bold; 
            margin-bottom: 5px; 
            padding-left: 70px;
        }
        .section p, .section ol, .section ul { 
            font-size: 12pt; 
            margin: 3px 0; 
            text-align: justify;
            padding-left: 90px;
        }
        .section ol, .section ul { 
            padding-left: 107.5px; 
        }

        /* EVALUASI ITEM */
        .evaluasi-item { 
            margin-bottom: 5px; 
            text-align: justify;
            padding-left: 90px;
        }
        .evaluasi-item .label { 
            font-weight: bold; 
        }

        /* DOKUMENTASI */
        .dokumentasi { 
            text-align: center; 
            margin: 15px 0; 
        }
        .dokumentasi img { 
            max-width: 100%; 
            height: auto;
            border: 1px solid #ccc; 
        }

        /* TANDA TANGAN */
        .ttd-container { 
            width: 100%; 
            margin-top: 40px; 
            page-break-inside: avoid;
        }
        .ttd { 
            float: right; 
            width: 250px; 
            text-align: left; 
        }
        .ttd p { 
            font-size: 12pt; 
            margin: 2px 0; 
        }
        .ttd .space { 
            height: 60px; 
        }
    </style>
</head>
<body>

    <!-- HEADER / KOP SURAT -->
    @php
        $logoPath = public_path('images/logo-smkn11.png');
    @endphp
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                @if(file_exists($logoPath))
                    @php
                        $logoData = base64_encode(file_get_contents($logoPath));
                        $logoMime = mime_content_type($logoPath);
                        $logoSrc = 'data:' . $logoMime . ';base64,' . $logoData;
                    @endphp
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
    <div class="title">Bulan : {{ \Carbon\Carbon::parse($laporan->bulan)->translatedFormat('F Y') }}</div>

    <!-- INFORMATION TABLE -->
    <table class="info-table">
        <tr>
            <td class="label">Pelatih</td>
            <td>: {{ $laporan->ekskul->pembina->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td>: {{ $kelas }}</td>
        </tr>
        <tr>
            <td class="label">Hari/Tanggal Kegiatan</td>
            <td>: {{ $laporan->ekskul->jadwal ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tempat</td>
            <td>: {{ $laporan->tempat ?? '-' }}</td>
        </tr>
    </table>

    <!-- A. TUJUAN KEGIATAN -->
    @if($laporan->tujuan)
    <div class="section">
        <div class="section-title">A. Tujuan Kegiatan</div>
        <ol>
            @foreach(explode("\n", $laporan->tujuan) as $line)
                @if(trim($line) !== '')
                    <li>{{ trim($line) }}</li>
                @endif
            @endforeach
        </ol>
    </div>
    @endif

    <!-- B. MATERI DAN KEGIATAN YANG DILAKSANAKAN -->
    @if($laporan->materi_kegiatan)
    <div class="section">
        <div class="section-title">B. Materi dan Kegiatan yang Dilaksanakan</div>
        <ol>
            @foreach(explode("\n", $laporan->materi_kegiatan) as $line)
                @if(trim($line) !== '')
                    <li>{{ trim($line) }}</li>
                @endif
            @endforeach
        </ol>
    </div>
    @endif

    <!-- C. KEHADIRAN PESERTA -->
    @if($laporan->kehadiran)
    <div class="section">
        <div class="section-title">C. Kehadiran Peserta</div>
        <p>{!! nl2br(e($laporan->kehadiran)) !!}</p>
    </div>
    @endif

    <!-- D. EVALUASI KEGIATAN -->
    @if($laporan->evaluasi_keberhasilan || $laporan->evaluasi_kendala || $laporan->evaluasi_solusi)
    <div class="section">
        <div class="section-title">D. Evaluasi Kegiatan</div>
        @if($laporan->evaluasi_keberhasilan)
            <div class="evaluasi-item">
                <span class="label">Keberhasilan:</span> {!! nl2br(e($laporan->evaluasi_keberhasilan)) !!}
            </div>
        @endif
        @if($laporan->evaluasi_kendala)
            <div class="evaluasi-item">
                <span class="label">Kendala:</span> {!! nl2br(e($laporan->evaluasi_kendala)) !!}
            </div>
        @endif
        @if($laporan->evaluasi_solusi)
            <div class="evaluasi-item">
                <span class="label">Solusi/Tindak Lanjut:</span> {!! nl2br(e($laporan->evaluasi_solusi)) !!}
            </div>
        @endif
    </div>
    @endif

    <!-- DOKUMENTASI (JIKA ADA) -->
    @if($laporan->dokumentasi)
    <div class="section">
        <div class="section-title">Dokumentasi</div>
        <div class="dokumentasi">
            @php
                $imgPath = public_path('storage/' . $laporan->dokumentasi);
                $imgExists = file_exists($imgPath);
            @endphp
            @if($imgExists)
                @php
                    $exif = @exif_read_data($imgPath);
                    $orientation = $exif['COMPUTED']['Orientation'] ?? $exif['Orientation'] ?? null;
                    $img = @imagecreatefromstring(file_get_contents($imgPath));

                    if ($img && $orientation) {
                        $flip = [2, 4, 5, 7];
                        $rotate = [3 => 180, 6 => 270, 8 => 90];
                        if (in_array($orientation, $flip)) {
                            $img = $orientation <= 2 ? $img : imagerotate($img, $rotate[$orientation] ?? 0, 0);
                        } elseif (isset($rotate[$orientation])) {
                            $img = imagerotate($img, $rotate[$orientation], 0);
                        }
                    }

                    if ($img) {
                        ob_start();
                        imagejpeg($img, null, 90);
                        $imageData = base64_encode(ob_get_clean());
                        imagedestroy($img);
                        $src = 'data:image/jpeg;base64,' . $imageData;
                    } else {
                        $imageData = base64_encode(file_get_contents($imgPath));
                        $mime = mime_content_type($imgPath);
                        $src = 'data:' . $mime . ';base64,' . $imageData;
                    }
                @endphp
                <img src="{{ $src }}" alt="Dokumentasi">
            @else
                <p>{{ $laporan->dokumentasi }}</p>
            @endif
        </div>
    </div>
    @endif

    <!-- TANDA TANGAN -->
    <div class="ttd-container">
        <div class="ttd">
            <p>Bandung, {{ $laporan->tanggal_surat ?? now()->translatedFormat('d F Y') }}</p>
            <p>Pelatih Ekskul {{ $laporan->ekskul->nama_ekskul ?? '' }}</p>
            <div class="space"></div>
            <p><strong>{{ $laporan->ekskul->pembina->nama ?? '-' }}</strong></p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>