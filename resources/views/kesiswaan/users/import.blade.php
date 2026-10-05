@extends('layouts.kesiswaan')

@section('title', 'Import Akun '.($jenis === 'siswa' ? 'Siswa' : 'Guru'))

@section('content')
    @php
        $isSiswa = $jenis === 'siswa';
        $templateRoute = $isSiswa ? 'kesiswaan.users.template-siswa' : 'kesiswaan.users.template-pembina';
        $columns = $isSiswa ? [
            ['NIS', 'Wajib, tepat 10 angka.'], ['Nama', 'Wajib.'], ['Email', 'Opsional; jika diisi harus berformat email dan unik.'],
            ['Jenis Kelamin', 'Wajib: L / Laki-laki atau P / Perempuan.'], ['Tempat Lahir', 'Wajib.'],
            ['Tanggal Lahir', 'Wajib; contoh 26-06-2008 atau 26,06,2008.'], ['Agama', 'Wajib.'],
            ['No. Telp', 'Opsional, boleh kosong.'], ['Alamat', 'Wajib.'],
            ['Media Sosial', 'Opsional, boleh kosong.'],
        ] : [
            ['NIP', 'Wajib, tepat 18 angka.'], ['Username', 'Opsional; jika diisi harus unik dan berbeda dari NIP.'],
            ['Nama Lengkap', 'Wajib.'], ['Email', 'Opsional; jika diisi harus berformat email dan unik.'],
            ['Jenis Kelamin', 'Wajib: L / Laki-laki atau P / Perempuan.'], ['Tempat Lahir', 'Wajib.'],
            ['Tanggal Lahir', 'Wajib; contoh 26-06-2008 atau 26,06,2008.'], ['Agama', 'Wajib.'],
            ['No. Telp', 'Opsional, boleh kosong.'], ['Alamat', 'Wajib.'], ['Media Sosial', 'Opsional, boleh kosong.'],
        ];
    @endphp

    <div class="mx-auto max-w-5xl space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-theme-dark">Import Akun {{ $isSiswa ? 'Siswa' : 'Guru / Pembina' }}</h1>
                <p class="mt-1 text-xs text-gray-500">Unduh template, lengkapi kolom sesuai keterangannya, lalu unggah file Excel atau CSV.</p>
            </div>
            <a href="{{ route('kesiswaan.users.create') }}" class="rounded-full bg-gray-100 px-5 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-200">Kembali ke Form Pembuatan Akun</a>
        </div>

        @if(session('error'))
            <div role="alert" class="rounded-2xl border p-4 text-xs font-semibold border-red-200 bg-red-50 text-red-700">
                {{ session('error') }}
                @foreach(session('import_errors', []) as $importError)<p class="mt-1">{{ $importError }}</p>@endforeach
            </div>
        @endif
        @if($errors->any())
            <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-semibold text-red-700">
                <p>File belum dapat diproses:</p>
                <ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <section class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-extrabold text-theme-dark">1. Unduh template {{ $isSiswa ? 'siswa' : 'guru' }}</h2>
                    <p class="mt-1 text-xs text-gray-500">Gunakan nama kolom pada template dan jangan mengubah baris judul.</p>
                </div>
                <a href="{{ route($templateRoute) }}" class="rounded-full {{ $isSiswa ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700' }} px-5 py-3 text-xs font-bold text-white">Unduh Template Excel</a>
            </div>

            <h3 class="mt-6 text-xs font-extrabold text-gray-700">Keterangan kolom</h3>
            <div class="mt-2 overflow-x-auto rounded-2xl border border-gray-100">
                <table class="w-full min-w-[520px] text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500"><tr><th class="px-4 py-3 font-bold">Kolom</th><th class="px-4 py-3 font-bold">Aturan pengisian</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($columns as [$column, $description])
                            <tr><td class="px-4 py-3 font-semibold text-gray-700">{{ $column }}</td><td class="px-4 py-3 text-gray-600">{{ $description }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 rounded-2xl bg-amber-50 p-4 text-xs leading-relaxed text-amber-800">
                Email, No. Telp, dan Media Sosial boleh dikosongkan. Foto profil tidak ada di kolom Excel dan dapat ditambahkan setelah import. Jenis kelamin menerima <strong>L</strong>, <strong>Laki-laki</strong>, <strong>P</strong>, atau <strong>Perempuan</strong>; sistem menyimpannya sebagai <code>laki-laki</code> atau <code>perempuan</code>. Tanggal dapat berupa sel tanggal Excel atau teks dengan format <strong>26-06-2008</strong>, <strong>26/06/2008</strong>, <strong>26.06.2008</strong>, atau <strong>26,06,2008</strong>. Format penyimpanan menjadi <code>2008-06-26</code>.
            </div>
        </section>

        <section class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-extrabold text-theme-dark">2. Unggah file</h2>
            <p class="mt-1 text-xs text-gray-500">Password akun hasil import adalah <code class="rounded bg-gray-100 px-1.5 py-0.5">password</code>.</p>
            <form method="POST" action="{{ route('kesiswaan.users.import') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="jenis" value="{{ $jenis }}">
                @if($isSiswa)
                    <div>
                        <label for="kelas-id" class="mb-1.5 block text-xs font-bold text-gray-500">Kelas tujuan siswa <span class="text-red-500">*</span></label>
                        <select id="kelas-id" name="kelas_id" required class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-700">
                            <option value="">Pilih kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }} ({{ config("kelas.tingkat.{$k->tingkat}") }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[11px] text-gray-400">Semua akun siswa dalam file akan dimasukkan ke kelas yang dipilih.</p>
                    </div>
                @endif
                <div>
                    <label for="file-import" class="mb-1.5 block text-xs font-bold text-gray-500">File Excel atau CSV <span class="text-red-500">*</span></label>
                    <input id="file-import" type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full rounded-2xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-600">
                </div>
                <button type="submit" class="rounded-full {{ $isSiswa ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700' }} px-6 py-3 text-xs font-bold text-white">Import Akun {{ $isSiswa ? 'Siswa' : 'Guru' }}</button>
            </form>
        </section>
    </div>
@endsection
