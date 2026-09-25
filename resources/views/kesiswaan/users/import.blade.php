@extends('layouts.kesiswaan')

@section('title', 'Import Akun Excel')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-theme-dark">Import Akun Excel</h1>
            <p class="text-xs text-gray-400 mt-1">Buat banyak akun sekaligus dari file excel.</p>
        </div>
        <a href="{{ route('kesiswaan.users.index') }}"
           class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-full transition">
            ← Kembali ke Akun
        </a>
    </div>

    <!-- Informasi Template -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4">
        <div>
            <h2 class="text-sm font-extrabold text-theme-dark mb-1">Langkah Import</h2>
            <ol class="list-decimal list-inside space-y-1 text-xs text-gray-500 font-medium leading-relaxed">
                <li>Download template (Siswa / Pembina) sesuai jenis akun.</li>
                <li>Untuk siswa: pilih kelas target di form bawah, isi excel hanya NIS + Nama + Jabatan, lalu upload.</li>
                <li>Semua siswa dalam file otomatis masuk ke kelas yang sudah ditentukan.</li>
                <li>Akun dibuat otomatis; user <b>melengkapi profil sendiri</b> setelah login pertama.</li>
            </ol>
        </div>

        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Template Siswa -->
            <div class="flex-1 p-5 rounded-2xl border border-emerald-100 bg-emerald-50/50">
                <div class="flex items-center justify-between gap-3 mb-3 flex-wrap">
                    <h3 class="text-xs font-extrabold text-emerald-700">Template Akun Siswa</h3>
                    <a href="{{ route('kesiswaan.users.template-siswa') }}"
                       class="px-4 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs rounded-full shadow shadow-emerald-500/20 transition flex items-center gap-1.5 shrink-0">
                        <span>⬇</span> Template Siswa
                    </a>
                </div>
                <ul class="space-y-1.5 text-[11px] text-emerald-900/80 font-medium">
                    <li><b>NIS</b> — nomor induk siswa (unik, tidak boleh sama dengan yang sudah ada).</li>
                    <li><b>Nama</b> — nama lengkap siswa sesuai data resmi.</li>
                    <li><b>Jabatan</b> — isi <i>siswa</i> atau <i>ketua</i>.</li>
                    <li>Kelas ditentukan di sini (tidak ditulis di excel).</li>
                </ul>
                <div class="mt-4" id="kelas-wrapper">
                    <label class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider block mb-1.5">Kelas Tujuan</label>
                    <select name="kelas_id" id="kelas-id" form="form-import"
                            class="w-full px-3 py-2.5 bg-white border border-emerald-200 rounded-2xl text-xs focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 transition">
                        <option value="">Pilih kelas...</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }} ({{ config("kelas.tingkat.{$k->tingkat}") }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Template Pembina -->
            <div class="flex-1 p-5 rounded-2xl border border-blue-100 bg-blue-50/50">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 class="text-xs font-extrabold text-theme-blue">Template Akun Pembina</h3>
                    <a href="{{ route('kesiswaan.users.template-pembina') }}"
                       class="px-4 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full shadow shadow-blue-500/20 transition flex items-center gap-1.5 shrink-0">
                        <span>⬇</span> Template Pembina
                    </a>
                </div>
                <ul class="space-y-1.5 text-[11px] text-blue-900/80 font-medium">
                    <li><b>NIP</b> — nomor induk pegawai (unik, tidak boleh sama dengan yang sudah ada).</li>
                    <li><b>Nama</b> — nama lengkap pembina sesuai data resmi.</li>
                    <li>Jenis kelamin diisi sendiri oleh pembina setelah login pertama.</li>
                </ul>
            </div>
        </div>

        <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50/60">
            <p class="text-[11px] font-bold text-amber-700 mb-2">⚠️ Password semua akun hasil import otomatis: <span class="bg-white px-2 py-0.5 rounded-lg font-mono font-extrabold">password</span></p>
            <p class="text-[11px] text-amber-700/80">Kesiswaan bisa mereset password lewat tombol <b>Reset PW</b> di halaman daftar akun.</p>
        </div>
    </div>

    <!-- Form Upload -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <h2 class="text-sm font-extrabold text-theme-dark mb-4">Upload File Excel</h2>
        <form method="POST" action="{{ route('kesiswaan.users.import') }}" enctype="multipart/form-data" id="form-import"
              class="flex flex-wrap items-end gap-3">
            @csrf
            <div>
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Jenis Akun</label>
                <select name="jenis" id="jenis-akun" required
                        class="px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                    <option value="siswa" {{ old('jenis') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="pembina" {{ old('jenis') === 'pembina' ? 'selected' : '' }}>Pembina</option>
                </select>
            </div>
            <div class="flex-1 min-w-[240px]">
                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">File Excel</label>
                <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs file:mr-3 file:px-4 file:py-1.5 file:rounded-full file:border-0 file:bg-theme-blue file:text-white file:font-bold hover:file:bg-theme-darkBlue transition">
            </div>
            <button type="submit"
                    class="px-6 py-2.5 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-2xl shadow-lg shadow-blue-500/20 transition shrink-0">
                Import Akun
            </button>
        </form>

        <script>
            (function () {
                var jenis = document.getElementById('jenis-akun');
                var wrap = document.getElementById('kelas-wrapper');
                var kelas = document.getElementById('kelas-id');

                function sync() {
                    var isSiswa = jenis.value === 'siswa';
                    wrap.classList.toggle('hidden', !isSiswa);
                    if (isSiswa) {
                        kelas.setAttribute('required', 'required');
                    } else {
                        kelas.removeAttribute('required');
                    }
                }

                jenis.addEventListener('change', sync);
                sync();
            })();
        </script>

        @if(isset($errors) && $errors->any())
            <div class="mt-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-xs font-semibold">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @php $importErrors = $importErrors ?? session('import_errors', []); @endphp
        @if(!empty($importErrors))
            <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                <p class="text-[11px] font-bold text-amber-700 mb-2">Baris yang dilewati (akun {{ session('import_jenis') ?? '' }}):</p>
                <ul class="list-disc list-inside space-y-1 text-[11px] text-amber-700/90 font-medium max-h-40 overflow-y-auto">
                    @foreach($importErrors as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endsection