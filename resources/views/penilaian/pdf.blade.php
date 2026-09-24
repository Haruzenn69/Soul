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
            width: 110px;
            text-align: center;
        }
        .header-table .logo-cell img {
            width: 110px;
            height: auto;
        }
        .header-table .spacer-cell {
            width: 110px;
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
            margin: 4px 0;
            text-transform: uppercase;
        }

        /* INFO TABEL */
        .info-table {
            width: 100%;
            margin: 15px auto 10px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 1.5px 0;
            font-size: 11pt;
            vertical-align: top;
        }
        .info-table td.label {
            width: 190px;
        }

        /* TABEL NILAI */
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }
        .nilai-table th {
            background: #2563eb;
            color: #fff;
            font-size: 8pt;
            padding: 4px 3px;
            border: 1px solid #000;
            text-align: center;
        }
        .nilai-table td {
            border: 1px solid #999;
            padding: 3px 4px;
            font-size: 8pt;
            vertical-align: middle;
        }
        .nilai-table td.c { text-align: center; }
        .nilai-table td.b { font-weight: bold; }
        .nilai-table tr:nth-child(even) td { background: #f6f8fb; }
        .pred {
            display: inline-block;
            min-width: 16px;
            text-align: center;
            font-weight: 800;
            padding: 1px 6px;
            border-radius: 5px;
            border: 1px solid #999;
        }
        .pred-A { color: #059669; }
        .pred-B { color: #0284c7; }
        .pred-C { color: #d97706; }
        .pred-D { color: #ea580c; }
        .pred-E { color: #dc2626; }

        /* CATATAN */
        .note {
            font-size: 9pt;
            color: #333;
            margin: 8px 0;
            text-align: justify;
        }

        /* TANDA TANGAN */
        .ttd-container {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .ttd {
            float: right;
            width: 280px;
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
            <td class="spacer-cell">&nbsp;</td>
        </tr>
    </table>
    <div class="header-border"></div>

    <!-- JUDUL LAPORAN -->
    <div class="title">LAPORAN PENILAIAN EKSTRAKURIKULER</div>

    <!-- INFORMATION TABLE -->
    <table class="info-table">
        <tr>
            <td class="label">Ekskul</td>
            <td>: {{ $ekskul->nama_ekskul }}</td>
        </tr>
        <tr>
            <td class="label">Pelatih</td>
            <td>: {{ $pembina?->nama ?? '-' }}{{ $pembina?->nip ? ' ('.$pembina->nip.')' : '' }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td>: {{ $periode }}</td>
        </tr>
    </table>

    <!-- TABEL PENILAIAN -->
    <table class="nilai-table">
        <thead>
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Pertemuan</th>
                <th>Hadir</th>
                <th>Izin</th>
                <th>Sakit</th>
                <th>Alpha</th>
                <th>% Hadir</th>
                <th>Sikap</th>
                <th>Keaktifan</th>
                <th>Keterampilan</th>
                <th>Nilai Akhir</th>
                <th>Predikat</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td class="c">{{ $loop->iteration }}</td>
                    <td class="c">{{ $row->siswa?->nis ?? '-' }}</td>
                    <td class="b">{{ $row->siswa?->nama ?? '-' }}</td>
                    <td class="c">{{ $row->kelas }}</td>
                    <td class="c">{{ $row->total_pertemuan }}</td>
                    <td class="c">{{ $row->total_hadir }}</td>
                    <td class="c">{{ $row->total_izin }}</td>
                    <td class="c">{{ $row->total_sakit }}</td>
                    <td class="c">{{ $row->total_alpha }}</td>
                    <td class="c"><b>{{ number_format((float) $row->persentase_kehadiran, 1) }}%</b></td>
                    <td class="c">{{ $row->penilaian ? number_format((float) $row->nilai_sikap, 1) : '-' }}</td>
                    <td class="c">{{ $row->penilaian ? number_format((float) $row->nilai_keaktifan, 1) : '-' }}</td>
                    <td class="c">{{ $row->penilaian ? number_format((float) $row->nilai_keterampilan, 1) : '-' }}</td>
                    <td class="c"><b>{{ $row->penilaian ? number_format((float) $row->nilai_akhir, 2) : '-' }}</b></td>
                    <td class="c">
                        @if ($row->penilaian)
                            <span class="pred pred-{{ $row->predikat }}">{{ $row->predikat }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $row->penilaian?->catatan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" class="c">Belum ada data penilaian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="note">
        Catatan : Nilai Akhir = (Persentase Kehadiran x 30%) + (Sikap x 25%) + (Keaktifan x 25%) + (Keterampilan x 20%).
        Predikat : A (90-100), B (80-89), C (70-79), D (60-69), E (&lt;60).
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd-container">
        <div class="ttd">
            <p>Bandung, {{ now()->translatedFormat('d F Y') }}</p>
            <p>{{ auth()->check() && auth()->user()->role === 'kesiswaan' ? 'Mengetahui, Kesiswaan' : 'Pelatih Ekskul '.$ekskul->nama_ekskul }}</p>
            <div class="space"></div>
            <p><strong>{{ $pembina?->nama ?? '-' }}</strong></p>
            <p>{{ $pembina?->nip ?? '' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>