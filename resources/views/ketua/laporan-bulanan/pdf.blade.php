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
            font-size: 10.5pt;
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

        /* TABEL KEGIATAN PER BARIS (LAPORAN BULANAN) */
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

        /* INFO PERTEMUAN (LEMBAR HISTORY) */
        .info-pertemuan {
            width: 100%;
            margin: 0 0 10px;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .info-pertemuan td {
            padding: 2px 0;
            vertical-align: top;
        }

        /* TABEL MATRIX / DAFTAR HADIR (HISTORY & PELATIH) */
        .matrix { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 12px;
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

        /* TANDA TANGAN SINGLE (LAPORAN KEGIATAN - PALING BAWAH SEBELAH KANAN) */
        .ttd-single-container { 
            width: 100%; 
            margin-top: 30px; 
            page-break-inside: avoid;
        }
        .ttd-single { 
            float: right; 
            width: 250px; 
            text-align: left; 
        }
        .ttd-single p { 
            font-size: 11pt; 
            margin: 2px 0; 
            font-weight: normal;
        }
        .ttd-single .space { 
            height: 55px; 
        }
        .ttd-single .nama-pelatih {
            font-weight: normal;
            text-decoration: underline;
        }

        /* TANDA TANGAN DUAL (LEMBAR HISTORY SISWA & PELATIH) */
        .ttd-dual-container { 
            width: 100%; 
            margin-top: 25px; 
            page-break-inside: avoid;
        }
        .ttd-dual-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .ttd-dual-container td {
            vertical-align: top;
            font-size: 11pt;
        }
        .ttd-dual-container p {
            margin: 2px 0;
            font-weight: normal;
        }
        .ttd-dual-container .space {
            height: 50px;
        }
        .ttd-dual-container .nama-ttd {
            text-decoration: underline;
            font-weight: normal;
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

        $ekskul = $laporan->ekskul;
        $pelatihModel = $ekskul->pelatih ?? null;
        $namaPelatih = ($pelatihModel && filled($pelatihModel->nama)) ? $pelatihModel->nama : '-';
        $namaKetua = $ekskul->ketua()?->nama ?? 'Ketua Ekskul';

        $tanggalSurat = $laporan->tanggal_surat 
            ?? \Carbon\Carbon::parse($laporan->bulan)->endOfMonth()->translatedFormat('d F Y');

        $waktuJadwal = null;
        if (!empty($ekskul->jadwal) && preg_match('/(\d{1,2}[:.]\d{2}\s*-\s*\d{1,2}[:.]\d{2})/', $ekskul->jadwal, $m)) {
            $waktuJadwal = str_replace('.', ':', $m[1]) . ' WIB';
        }

        // Ambil data rekap absensi jika belum tersedia
        if (!isset($rows) || !isset($presensis)) {
            $rekapData = app(\App\Services\RekapAbsensiService::class)->rekap($ekskul, $laporan->bulan);
            $rows = $rekapData['rows'];
            $presensis = $rekapData['presensis'];
            $presensiPelatih = $rekapData['presensiPelatih'];
            $kegiatans = $rekapData['kegiatans'];
            $eventKegiatans = $rekapData['eventKegiatans'];
        }

        // Query atau gabungkan semua kegiatan bulan ini untuk laporan bulanan & absen pelatih
        $semuaKegiatansList = collect($kegiatans ?? [])
            ->merge($eventKegiatans ?? [])
            ->sortBy('tanggal_kegiatan')
            ->values();

        if ($semuaKegiatansList->isEmpty()) {
            $tahun = substr($laporan->bulan, 0, 4);
            $bulanNum = substr($laporan->bulan, 5, 2);
            $semuaKegiatansList = \App\Models\Kegiatan::where('ekskul_id', $ekskul->id)
                ->whereYear('tanggal_kegiatan', $tahun)
                ->whereMonth('tanggal_kegiatan', $bulanNum)
                ->orderBy('tanggal_kegiatan')
                ->get();
        }

        $kegiatanRutinList = collect($kegiatans ?? [])->reject(fn ($k) => $k->isEvent())->sortBy('tanggal_kegiatan')->values();
        if ($kegiatanRutinList->isEmpty() && $semuaKegiatansList->isNotEmpty()) {
            $kegiatanRutinList = $semuaKegiatansList->reject(fn ($k) => $k->isEvent())->values();
        }
    @endphp

    {{-- =================================================================== --}}
    {{-- LEMBAR 1: LAPORAN KEGIATAN BULANAN                                 --}}
    {{-- =================================================================== --}}

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
            <td class="meta-value">{{ $ekskul->nama_ekskul ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Pelatih</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $namaPelatih }}</td>
        </tr>
    </table>

    <!-- TABEL KONTEN KEGIATAN (KIRI: PENJELASAN, KANAN: FOTO DOKUMENTASI) -->
    <table class="kegiatan-table">
        @forelse($semuaKegiatansList as $kegiatan)
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
    <div class="ttd-single-container">
        <div class="ttd-single">
            <p>Bandung, {{ $tanggalSurat }}</p>
            <p>Pelatih Ekstrakurikuler {{ $ekskul->nama_ekskul ?? '' }}</p>
            <div class="space"></div>
            <p class="nama-pelatih">{{ $namaPelatih !== '-' ? $namaPelatih : '..................................................' }}</p>
        </div>
        <div style="clear: both;"></div>
    </div>

    {{-- =================================================================== --}}
    {{-- LEMBAR 2+: HISTORY PRESENSI SISWA PER 1 PERTEMUAN                   --}}
    {{-- =================================================================== --}}
    @foreach ($kegiatanRutinList as $kegiatanIndex => $kegiatan)
        <div class="page-break"></div>

        <!-- HEADER / KOP SURAT PERTEMUAN -->
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

        <!-- JUDUL PERTEMUAN -->
        <div class="title" style="margin-bottom: 12px;">DAFTAR HADIR PERTEMUAN EKSTRAKURIKULER {{ strtoupper($ekskul->nama_ekskul) }}</div>

        @php
            $waktuKegiatanIni = $waktuJadwal ?: '15:30 - 17:30 WIB';
        @endphp

        <!-- INFO PERTEMUAN -->
        <table class="info-pertemuan">
            <tr>
                <td style="width: 120px;">Hari, Tanggal</td>
                <td style="width: 250px;">: {{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('l, d F Y') : '-' }}</td>
                <td style="width: 80px;">Pelatih</td>
                <td>: {{ $namaPelatih }}</td>
            </tr>
            <tr>
                <td>Waktu Kegiatan</td>
                <td colspan="3">: {{ $waktuKegiatanIni }}</td>
            </tr>
        </table>

        <!-- TABEL HISTORY PRESENSI SISWA -->
        <table class="matrix">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 16%;">NIS</th>
                    <th style="width: 38%; text-align: center;">Nama Siswa</th>
                    <th style="width: 14%;">Tanggal</th>
                    <th style="width: 13%;">Waktu</th>
                    <th style="width: 13%;">Keterangan</th>
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

                        $waktuItem = ($presensiItem && $presensiItem->created_at)
                            ? $presensiItem->created_at->format('H:i') . ' WIB'
                            : '-';

                        if ($status === 'hadir') {
                            $countHadir++;
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Belum ada anggota pada pertemuan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- TANDA TANGAN PERTEMUAN -->
        <div class="ttd-dual-container">
            <table>
                <tr>
                    <td style="width: 50%; text-align: left;">
                        <p>Mengetahui,</p>
                        <p>Ketua Ekskul {{ $ekskul->nama_ekskul }}</p>
                        <div class="space"></div>
                        <p class="nama-ttd">{{ $namaKetua }}</p>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <p>Bandung, {{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('d F Y') : $tanggalSurat }}</p>
                        <p>Pelatih / Pembina Ekskul</p>
                        <div class="space"></div>
                        <p class="nama-ttd">{{ $namaPelatih !== '-' ? $namaPelatih : ($ekskul->pembina->nama ?? '..................................................') }}</p>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

    {{-- =================================================================== --}}
    {{-- LEMBAR TERAKHIR: DAFTAR HADIR PELATIH (SEMUA PERTEMUAN DALAM 1 LEMBAR)--}}
    {{-- =================================================================== --}}
    <div class="page-break"></div>

    <!-- HEADER / KOP SURAT PELATIH -->
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

    <!-- JUDUL DAFTAR HADIR PELATIH -->
    <div class="title" style="margin-bottom: 2px;">DAFTAR HADIR PELATIH EKSTRAKURIKULER</div>
    <div class="subtitle">Bulan : {{ \Carbon\Carbon::parse($laporan->bulan)->translatedFormat('F Y') }} &bull; {{ $ekskul->nama_ekskul }}</div>

    <!-- INFO PELATIH -->
    <table class="info-pertemuan" style="margin-bottom: 12px;">
        <tr>
            <td style="width: 140px;">Nama Pelatih</td>
            <td style="width: 250px;">: {{ $namaPelatih }}</td>
            <td style="width: 130px;">Jumlah Pertemuan</td>
            <td>: {{ $semuaKegiatansList->count() }} Pertemuan</td>
        </tr>
        <tr>
            <td>Ekstrakurikuler</td>
            <td>: {{ $ekskul->nama_ekskul }}</td>
            <td>Jadwal Kegiatan</td>
            <td>: {{ $ekskul->jadwal ?? '-' }}</td>
        </tr>
    </table>

    <!-- TABEL HISTORY PRESENSI PELATIH (SEMUA PERTEMUAN) -->
    <table class="matrix">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 18%;">Pertemuan</th>
                <th style="width: 18%;">Hari, Tanggal</th>
                <th style="width: 14%;">Waktu</th>
                <th style="width: 25%; text-align: center;">Materi Kegiatan</th>
                <th style="width: 10%;">Keterangan</th>
                <th style="width: 15%;">TTD</th>
            </tr>
        </thead>
        <tbody>
            @php
                $countPelatihHadir = 0;
                $countPelatihSakit = 0;
                $countPelatihIzin = 0;
                $countPelatihAlpa = 0;
            @endphp
            @forelse ($semuaKegiatansList as $kIndex => $kegiatan)
                @php
                    $statusPelatih = $presensiPelatih[$kegiatan->id] ?? null;
                    $waktuItem = $waktuJadwal ?: '15:30 - 17:30 WIB';
                    if ($statusPelatih === 'hadir') {
                        $countPelatihHadir++;
                    } elseif ($statusPelatih === 'sakit') {
                        $countPelatihSakit++;
                    } elseif ($statusPelatih === 'izin') {
                        $countPelatihIzin++;
                    } elseif ($statusPelatih === 'alpha') {
                        $countPelatihAlpa++;
                    }
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($kegiatan->isEvent())
                            Kegiatan Event
                        @else
                            Pertemuan Ke-{{ $kIndex + 1 }}
                        @endif
                    </td>
                    <td>{{ $kegiatan->tanggal_kegiatan ? $kegiatan->tanggal_kegiatan->translatedFormat('l, d/m/Y') : '-' }}</td>
                    <td>{{ $waktuItem }}</td>
                    <td class="name" style="text-align: left;">{{ $kegiatan->materi ?: '-' }}</td>
                    <td>
                        @if($statusPelatih === 'hadir')
                            <strong style="color: #047857;">Hadir</strong>
                        @elseif($statusPelatih === 'sakit')
                            <strong style="color: #6d28d9;">Sakit</strong>
                        @elseif($statusPelatih === 'izin')
                            <strong style="color: #b45309;">Izin</strong>
                        @elseif($statusPelatih === 'alpha')
                            <strong style="color: #b91c1c;">Alpa</strong>
                        @else
                            <span style="color: #6b7280;">&ndash;</span>
                        @endif
                    </td>
                    <td style="text-align: {{ $loop->iteration % 2 === 1 ? 'left' : 'right' }}; padding-left: 6px; padding-right: 6px; font-size: 8.5pt;">
                        @if($statusPelatih === 'hadir' || ($namaPelatih !== '-' && empty($statusPelatih)))
                            {{ $loop->iteration }}. ....................
                        @else
                            <span style="color: #999; font-style: italic;">&ndash;</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Belum ada pertemuan pada bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN LEMBAR PELATIH -->
    <div class="ttd-dual-container">
        <table>
            <tr>
                <td style="width: 50%; text-align: left;">
                    <p>Mengetahui,</p>
                    <p>Ketua Ekskul {{ $ekskul->nama_ekskul }}</p>
                    <div class="space"></div>
                    <p class="nama-ttd">{{ $namaKetua }}</p>
                </td>
                <td style="width: 50%; text-align: right;">
                    <p>Bandung, {{ $tanggalSurat }}</p>
                    <p>Pelatih Ekstrakurikuler</p>
                    <div class="space"></div>
                    <p class="nama-ttd">{{ $namaPelatih !== '-' ? $namaPelatih : '..................................................' }}</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
