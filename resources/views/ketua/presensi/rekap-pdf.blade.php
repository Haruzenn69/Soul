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
        .header-border { border-bottom: 3px double #000; margin-top: 4px; margin-bottom: 14px; }

        /* JUDUL */
        .title { 
            text-align: center; 
            font-size: 12pt; 
            font-weight: bold; 
            margin: 10px 0 5px; 
            text-transform: uppercase; 
        }
        .subtitle {
            text-align: center;
            font-size: 10.5pt;
            font-weight: bold;
            margin: 0 0 14px;
            text-transform: uppercase;
        }

        /* INFO TABEL (REKAP BULANAN) */
        .info-table { 
            width: 85%; 
            margin: 0 auto 15px; 
            border-collapse: collapse;
        }
        .info-table td { 
            padding: 2px 0; 
            font-size: 11pt; 
            vertical-align: top; 
        }
        .info-table td.label { 
            width: 180px; 
        }

        /* INFO PERTEMUAN (LEMBAR HISTORY) */
        .info-pertemuan {
            width: 100%;
            margin: 0 0 12px;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .info-pertemuan td {
            padding: 2.5px 0;
            vertical-align: top;
        }

        /* MATRIKS & TABEL REKAP */
        .matrix { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 15px;
        }
        .matrix th, .matrix td { 
            border: 1px solid #000; 
            padding: 4.5px 5px; 
            text-align: center; 
            font-size: 9.5pt; 
            vertical-align: middle;
        }
        .matrix th { 
            background: #f1f5f9; 
            font-weight: bold; 
        }
        .matrix td.name { 
            text-align: left; 
            padding-left: 6px;
        }
        .matrix td.name small { 
            display: block; 
            font-size: 7.5pt; 
            color: #444; 
        }

        /* TANDA TANGAN */
        .ttd-container { 
            width: 100%; 
            margin-top: 25px; 
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
        }
        .ttd .space { 
            height: 50px; 
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

        $waktuJadwal = null;
        if (!empty($ekskul->jadwal) && preg_match('/(\d{1,2}[:.]\d{2}\s*-\s*\d{1,2}[:.]\d{2})/', $ekskul->jadwal, $m)) {
            $waktuJadwal = str_replace('.', ':', $m[1]) . ' WIB';
        }

        $pelatihData = $pelatih ?? null;
        $namaPelatih = $pelatihData->nama ?? $ekskul->pembina->nama ?? '-';
        $namaKetua = $ekskul->ketua()?->nama ?? 'Ketua Ekskul';
    @endphp

    {{-- ======================================================== --}}
    {{-- HALAMAN 1: REKAP ABSENSI BULANAN (RINGKASAN TOTAL)      --}}
    {{-- ======================================================== --}}

    <!-- HEADER / KOP SURAT -->
    <table class="header-table" cellspacing="0">
        <tr>
            <td class="logo-cell">
                @if($logoSrc)
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
    <div class="subtitle">Bulan : {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</div>

    <!-- INFORMATION TABLE -->
    <table class="info-table">
        <tr>
            <td class="label">Nama Ekskul</td>
            <td>: {{ $ekskul->nama_ekskul }}</td>
        </tr>
        <tr>
            <td class="label">Pelatih</td>
            <td>: {{ $namaPelatih }}</td>
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

    <!-- REKAP KEHADIRAN (RUTIN) -->
    <table class="matrix">
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">No</th>
                <th rowspan="2" style="text-align: center;">Nama Siswa</th>
                <th colspan="4" style="text-align: center;">Jumlah Kehadiran</th>
                <th rowspan="2" style="width: 14%; text-align: center;">% Kehadiran</th>
            </tr>
            <tr>
                <th style="width: 10%;">Hadir</th>
                <th style="width: 10%;">Sakit</th>
                <th style="width: 10%;">Izin</th>
                <th style="width: 10%;">Alpa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                @php
                    $anggota = $row->pendaftaran->siswa;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="name">
                        {{ $anggota->nama ?? '-' }}
                        <small>{{ $anggota->kelas?->nama ?? 'Tanpa kelas' }}</small>
                    </td>
                    <td>{{ $row->hadir }}</td>
                    <td>{{ $row->sakit }}</td>
                    <td>{{ $row->izin }}</td>
                    <td>{{ $row->alpha }}</td>
                    <td>{{ $row->persentaseKehadiran }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Belum ada anggota aktif untuk direkap.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @php
        $letterMap = [
            'hadir' => 'H',
            'sakit' => 'S',
            'izin' => 'I',
            'alpha' => 'A',
            null => '&ndash;',
        ];
    @endphp

    @if(($eventKegiatans ?? collect())->isNotEmpty())
        <div class="title" style="font-size: 11pt; margin-top: 15px;">Kegiatan Event (Diklat, Lomba, dll.)</div>
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
        $perKegiatanPdf = $presensiPelatih ?? [];
        $pelatihLetterMap = [
            'hadir' => 'H',
            'sakit' => 'S',
            'izin' => 'I',
            'alpha' => 'A',
        ];
    @endphp
    @if($pelatihData && $semuaKegiatanPdf->isNotEmpty())
        <div class="title" style="font-size: 11pt; margin-top: 15px;">Kehadiran Pelatih</div>
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

    <!-- TANDA TANGAN REKAP BULANAN -->
    <div class="ttd-container">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; text-align: left; font-size: 11pt;">
                    <p style="margin: 2px 0;">Mengetahui,</p>
                    <p style="margin: 2px 0;">Ketua Ekskul {{ $ekskul->nama_ekskul }}</p>
                    <div class="space" style="height: 50px;"></div>
                    <p style="margin: 2px 0;"><strong>{{ $namaKetua }}</strong></p>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right; font-size: 11pt;">
                    <p style="margin: 2px 0;">Bandung, {{ now()->translatedFormat('d F Y') }}</p>
                    <p style="margin: 2px 0;">Pelatih / Pembina Ekskul</p>
                    <div class="space" style="height: 50px;"></div>
                    <p style="margin: 2px 0;"><strong>{{ $namaPelatih }}</strong></p>
                </td>
            </tr>
        </table>
    </div>

    {{-- ======================================================== --}}
    {{-- LEMBAR HISTORY PER PERTEMUAN (1 LEMBAR PER 1 PERTEMUAN) --}}
    {{-- ======================================================== --}}
    @foreach ($kegiatans as $kegiatanIndex => $kegiatan)
        <div class="page-break"></div>

        <!-- HEADER / KOP SURAT PERTEMUAN -->
        <table class="header-table" cellspacing="0">
            <tr>
                <td class="logo-cell">
                    @if($logoSrc)
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

        <!-- JUDUL PERTEMUAN -->
        <div class="title" style="margin-bottom: 2px;">DAFTAR HADIR PERTEMUAN EKSTRAKURIKULER</div>
        <div class="subtitle">Pertemuan Ke-{{ $kegiatanIndex + 1 }} &bull; {{ $ekskul->nama_ekskul }}</div>

        @php
            $waktuKegiatanIni = $waktuJadwal ?: '15:30 - 17:30 WIB';
        @endphp

        <!-- INFO PERTEMUAN -->
        <table class="info-pertemuan">
            <tr>
                <td style="width: 120px; font-weight: bold;">Hari, Tanggal</td>
                <td style="width: 250px;">: {{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('l, d F Y') : '-' }}</td>
                <td style="width: 80px; font-weight: bold;">Waktu</td>
                <td>: {{ $waktuKegiatanIni }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Materi Kegiatan</td>
                <td>: {{ $kegiatan->materi ?: '-' }}</td>
                <td style="font-weight: bold;">Pelatih</td>
                <td>: {{ $namaPelatih }}</td>
            </tr>
            @if($kegiatan->deskripsi)
                <tr>
                    <td style="font-weight: bold;">Deskripsi</td>
                    <td colspan="3">: {{ $kegiatan->deskripsi }}</td>
                </tr>
            @endif
        </table>

        <!-- TABEL HISTORY PRESENSI -->
        <table class="matrix">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 14%;">NIS</th>
                    <th style="width: 29%; text-align: center;">Nama Siswa</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 12%;">Waktu</th>
                    <th style="width: 13%;">Keterangan</th>
                    <th style="width: 15%;">TTD</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $countHadir = 0;
                    $countSakit = 0;
                    $countIzin = 0;
                    $countAlpa = 0;
                @endphp
                @forelse ($rows as $row)
                    @php
                        $anggota = $row->pendaftaran->siswa;
                        $status = $row->sel[$kegiatan->id] ?? null;
                        $presensiItem = isset($presensis) ? $presensis->get($row->pendaftaran->id . '-' . $kegiatan->id) : null;

                        $waktuItem = '-';
                        if ($status === 'hadir') {
                            $countHadir++;
                            $waktuItem = ($presensiItem && $presensiItem->created_at)
                                ? $presensiItem->created_at->format('H:i') . ' WIB'
                                : ($waktuJadwal ?: '15:30 WIB');
                        } elseif ($status === 'sakit') {
                            $countSakit++;
                        } elseif ($status === 'izin') {
                            $countIzin++;
                        } elseif ($status === 'alpha') {
                            $countAlpa++;
                        }
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $anggota->nis ?? '-' }}</td>
                        <td class="name">
                            {{ $anggota->nama ?? '-' }}
                            <small>{{ $anggota->kelas?->nama ?? 'Tanpa kelas' }}</small>
                        </td>
                        <td>{{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('d/m/Y') : '-' }}</td>
                        <td>{{ $waktuItem }}</td>
                        <td>
                            @if($status === 'hadir')
                                <strong style="color: #047857;">Hadir</strong>
                            @elseif($status === 'sakit')
                                <strong style="color: #6d28d9;">Sakit</strong>
                            @elseif($status === 'izin')
                                <strong style="color: #b45309;">Izin</strong>
                            @elseif($status === 'alpha')
                                <strong style="color: #b91c1c;">Alpa</strong>
                            @else
                                <span style="color: #6b7280;">&ndash;</span>
                            @endif
                        </td>
                        <td style="text-align: {{ $loop->iteration % 2 === 1 ? 'left' : 'right' }}; padding-left: 6px; padding-right: 6px; font-size: 8.5pt;">
                            @if($status === 'hadir')
                                {{ $loop->iteration }}. ....................
                            @else
                                <span style="color: #999; font-style: italic;">&ndash;</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">Belum ada anggota pada pertemuan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- RINGKASAN KEHADIRAN PERTEMUAN -->
        <table style="width: 100%; margin-top: 6px; font-size: 9pt; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: top;">
                    <strong>Rekap Kehadiran:</strong>
                    Hadir: <strong>{{ $countHadir }}</strong> &bull;
                    Sakit: <strong>{{ $countSakit }}</strong> &bull;
                    Izin: <strong>{{ $countIzin }}</strong> &bull;
                    Alpa: <strong>{{ $countAlpa }}</strong> &bull;
                    Total: <strong>{{ $rows->count() }} Anggota</strong>
                </td>
            </tr>
        </table>

        <!-- TANDA TANGAN PERTEMUAN -->
        <div class="ttd-container" style="margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; vertical-align: top; text-align: left; font-size: 11pt;">
                        <p style="margin: 2px 0;">Mengetahui,</p>
                        <p style="margin: 2px 0;">Ketua Ekskul {{ $ekskul->nama_ekskul }}</p>
                        <div class="space" style="height: 50px;"></div>
                        <p style="margin: 2px 0;"><strong>{{ $namaKetua }}</strong></p>
                    </td>
                    <td style="width: 50%; vertical-align: top; text-align: right; font-size: 11pt;">
                        <p style="margin: 2px 0;">Bandung, {{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</p>
                        <p style="margin: 2px 0;">Pelatih / Pembina Ekskul</p>
                        <div class="space" style="height: 50px;"></div>
                        <p style="margin: 2px 0;"><strong>{{ $namaPelatih }}</strong></p>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

</body>
</html>
