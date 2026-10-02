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

        /* LEGEND */
        .legend {
            margin: 0 auto 12px;
            text-align: center;
            font-size: 10pt;
        }
        .legend span { margin: 0 8px; }

        /* MATRIKS TABEL */
        .matrix { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
        }
        .matrix th, .matrix td { 
            border: 1px solid #000; 
            padding: 3px 4px; 
            text-align: center; 
            font-size: 8pt; 
            vertical-align: middle;
        }
        .matrix th { 
            background: #f1f5f9; 
            font-weight: bold; 
        }
        .matrix td.name { 
            text-align: left; 
        }
        .matrix td.name small { 
            display: block; 
            font-size: 7pt; 
            color: #333; 
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

    <!-- JUDUL -->
    <div class="title">REKAP ABSENSI EKSTRAKURIKULER</div>
    <div class="title">Bulan : {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</div>

    <!-- INFORMATION TABLE -->
    <table class="info-table">
        <tr>
            <td class="label">Nama Ekskul</td>
            <td>: {{ $ekskul->nama_ekskul }}</td>
        </tr>
        <tr>
            <td class="label">Pelatih</td>
            <td>: {{ $ekskul->pembina->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Hari/Tanggal Kegiatan</td>
            <td>: {{ $ekskul->jadwal ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jumlah Anggota Aktif</td>
            <td>: {{ $rows->count() }} orang</td>
        </tr>
    </table>

    <!-- LEGEND -->
    <div class="legend">
        <span>H = Hadir</span>
        <span>S = Sakit</span>
        <span>I = Izin</span>
        <span>A = Alpha</span>
        <span>&ndash; = Belum diabsen</span>
        <span>(Kegiatan event tidak dihitung ke persentase kehadiran)</span>
    </div>

    <!-- MATRIKS KE HADIRAN (RUTIN) -->
    <table class="matrix">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2" style="text-align: center;">Nama</th>
                <th colspan="{{ max($kegiatans->count(), 1) }}" style="text-align: center;">Pertemuan Rutin (tanggal)</th>
                <th colspan="4">Total</th>
                <th rowspan="2">% Kehadiran</th>
            </tr>
            <tr>
                @forelse ($kegiatans as $kegiatan)
                    <th>{{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('d/m') : 'Keg. #'.$kegiatan->id }}</th>
                @empty
                    <th>Belum ada kegiatan</th>
                @endforelse
                <th>H</th>
                <th>I</th>
                <th>S</th>
                <th>A</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                @php
                    $anggota = $row->pendaftaran->siswa;
                    $letterMap = [
                        'hadir' => 'H',
                        'sakit' => 'S',
                        'izin' => 'I',
                        'alpha' => 'A',
                        null => '&ndash;',
                    ];
                    $totalKegiatanAbsen = $row->total > 0 ? $row->total : 1;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="name">
                        {{ $anggota->nama ?? '-' }}
                        <small>{{ $anggota->kelas?->nama ?? 'Tanpa kelas' }}</small>
                    </td>
                    @forelse ($kegiatans as $kegiatan)
                        <td>{!! $letterMap[$row->sel[$kegiatan->id] ?? null] !!}</td>
                    @empty
                        <td>&ndash;</td>
                    @endforelse
                    <td>{{ $row->hadir }}</td>
                    <td>{{ $row->izin }}</td>
                    <td>{{ $row->sakit }}</td>
                    <td>{{ $row->alpha }}</td>
                    <td>{{ $row->persentaseKehadiran }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $kegiatans->count() + 7 }}" style="text-align: center;">Belum ada anggota aktif untuk direkap.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(($eventKegiatans ?? collect())->isNotEmpty())
        <div class="title" style="font-size: 11pt; margin-top: 20px;">Kegiatan Event (Diklat, Lomba, dll.)</div>
        <table class="matrix">
            <thead>
                <tr>
                    <th>No</th>
                    <th style="text-align: center;">Nama</th>
                    @foreach ($eventKegiatans as $kegiatan)
                        <th>{{ $kegiatan->tanggalText() }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    @php
                        $anggota = $row->pendaftaran->siswa;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="name">
                            {{ $anggota->nama ?? '-' }}
                            <small>{{ $anggota->kelas?->nama ?? 'Tanpa kelas' }}</small>
                        </td>
                        @foreach ($eventKegiatans as $kegiatan)
                            <td>{!! $letterMap[$row->sel[$kegiatan->id] ?? null] ?? '&ndash;' !!}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @php
        $semuaKegiatanPdf = collect($kegiatans ?? [])->merge($eventKegiatans ?? []);
        $pelatihData = $pelatih ?? null;
        $perKegiatanPdf = $presensiPelatih ?? [];
        $pelatihLetterMap = [
            'hadir' => 'H',
            'sakit' => 'S',
            'izin' => 'I',
            'alpha' => 'A',
        ];
    @endphp
    @if($pelatihData && $semuaKegiatanPdf->isNotEmpty())
        <div class="title" style="font-size: 11pt; margin-top: 20px;">Kehadiran Pelatih</div>
        <table class="matrix">
            <thead>
                <tr>
                    <th style="text-align: center;">Nama</th>
                    @foreach ($semuaKegiatanPdf as $kegiatan)
                        <th>
                            @if($kegiatan->isEvent() && $kegiatan->tanggal_berakhir && $kegiatan->tanggal_berakhir->ne($kegiatan->tanggal_kegiatan))
                                {{ $kegiatan->tanggal_kegiatan->translatedFormat('d/m') }}&ndash;{{ $kegiatan->tanggal_berakhir->translatedFormat('d/m') }}
                                (E)
                            @else
                                {{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('d/m') : 'Keg. #'.$kegiatan->id }}@if($kegiatan->isEvent()) (E)@endif
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="name">{{ $pelatihData->nama }}</td>
                    @foreach ($semuaKegiatanPdf as $kegiatan)
                        <td>{!! $pelatihLetterMap[$perKegiatanPdf[$kegiatan->id] ?? null] ?? '&ndash;' !!}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endif

    <!-- TANDA TANGAN -->
    <div class="ttd-container">
        <div class="ttd">
            <p>Bandung, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Pelatih Ekskul {{ $ekskul->nama_ekskul }}</p>
            <div class="space"></div>
            <p><strong>{{ $ekskul->pembina->nama ?? '-' }}</strong></p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>