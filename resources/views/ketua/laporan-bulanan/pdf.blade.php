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
        .header-table p { font-size: 10pt; margin: 0.3px 0; }
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
            max-width: 45%; 
            height: auto;
            border: 1px solid #ccc; 
            margin: 5px 2%;
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
        $logoPath = public_path('images/logo-kop.webp');
    @endphp
    <table class="header-table" cellspacing="0">
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

    <!-- B. KEGIATAN RUTIN -->
    <div class="section">
        <div class="section-title">B. Materi Kegiatan Rutin yang Dilaksanakan</div>
        <ol>
            @forelse ($rutinKegiatans ?? collect() as $kegiatan)
                <li>Pada tanggal {{ $kegiatan->tanggalText() }}, kegiatan yang dilaksanakan berupa {{ $kegiatan->materi }}.@if($kegiatan->deskripsi) {{ $kegiatan->deskripsi }}@endif</li>
            @empty
                <li>Tidak ada kegiatan rutin pada bulan ini.</li>
            @endforelse
        </ol>
    </div>

    <!-- C. KEGIATAN EVENT -->
    <div class="section">
        <div class="section-title">C. Kegiatan Event yang Dilaksanakan (Diklat, Lomba, dll.)</div>
        <ol>
            @forelse ($eventKegiatans ?? collect() as $kegiatan)
                <li>Pada tanggal {{ $kegiatan->tanggalText() }}, kegiatan yang dilaksanakan berupa {{ $kegiatan->materi }}.@if($kegiatan->deskripsi) {{ $kegiatan->deskripsi }}@endif</li>
            @empty
                <li>Tidak ada kegiatan event pada bulan ini.</li>
            @endforelse
        </ol>
    </div>

    <!-- D. KEHADIRAN PESERTA -->
    @if($laporan->kehadiran)
    <div class="section">
        <div class="section-title">D. Kehadiran Peserta</div>
        <p>{!! nl2br(e($laporan->kehadiran)) !!}</p>
    </div>
    @endif

    <!-- E. EVALUASI KEGIATAN -->
    @if($laporan->evaluasi_keberhasilan || $laporan->evaluasi_kendala || $laporan->evaluasi_solusi)
    <div class="section">
        <div class="section-title">E. Evaluasi Kegiatan</div>
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

    <!-- F. DOKUMENTASI KEGIATAN RUTIN -->
    @if(collect($dokumentasiRutin ?? [])->isNotEmpty())
    <div class="section">
        <div class="section-title">F. Dokumentasi Kegiatan Rutin</div>
        @include('partials.pdf-dokumentasi', ['paths' => $dokumentasiRutin ?? []])
    </div>
    @endif

    <!-- G. DOKUMENTASI KEGIATAN EVENT -->
    @if(collect($dokumentasiEvent ?? [])->isNotEmpty())
    <div class="section">
        <div class="section-title">G. Dokumentasi Kegiatan Event</div>
        @include('partials.pdf-dokumentasi', ['paths' => $dokumentasiEvent ?? []])
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