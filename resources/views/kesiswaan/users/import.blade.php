@extends('layouts.kesiswaan')

@section('title', 'Import Akun')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-theme-dark">Import Akun Excel</h1>
                <p class="text-xs text-gray-400 mt-1">Unduh template, isi data akun, lalu upload file untuk membuat banyak akun sekaligus.</p>
            </div>
            <a href="{{ route('kesiswaan.users.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-full transition">
                <svg aria-hidden="true" class="w-4 h-4" viewBox="0 0 20 20" fill="none"><path d="M12.5 4.5 7 10l5.5 5.5M7.5 10H17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Daftar akun
            </a>
        </div>

        @if(isset($errors) && $errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-semibold text-red-600" role="alert">
                <p class="font-bold">File belum dapat diproses:</p>
                <ul class="mt-1 list-disc pl-5 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        @php $importErrors = $importErrors ?? session('import_errors', []); @endphp
        @if(!empty($importErrors))
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4" role="status">
                <p class="text-[11px] font-bold text-amber-700">{{ session('success') ? 'Beberapa baris dilewati:' : 'Periksa baris berikut:' }} {{ ucfirst(session('import_jenis') ?? '') }}</p>
                <p class="mt-1 text-[11px] text-amber-700/90">Pastikan jenis akun sama dengan template yang digunakan. Template siswa memakai kolom NIS, Nama, Jabatan; template pembina memakai kolom NIP, Username, Nama.</p>
                <ul class="mt-2 list-disc list-inside space-y-1 text-[11px] text-amber-700/90 max-h-40 overflow-y-auto">
                    @foreach($importErrors as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        <section class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-4" aria-labelledby="template-heading">
            <div class="flex gap-3 items-start">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-theme-blue">
                    <svg aria-hidden="true" class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M7 3.75h7l5 5v11.5H7a2 2 0 0 1-2-2v-12.5a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.6"/><path d="M14 4v5h5M8.5 13h7M8.5 16.5h7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </span>
                <div>
                    <h2 id="template-heading" class="text-sm font-extrabold text-theme-dark">1. Pilih Template</h2>
                    <p class="mt-1 text-xs text-gray-500">Gunakan template yang sesuai agar kolom data terbaca dengan benar.</p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <article class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-xs font-extrabold text-emerald-700">Template Akun Siswa</h3>
                            <p class="mt-1 text-[11px] text-gray-600">Kolom NIS, nama, dan jabatan siswa.</p>
                        </div>
                        <span class="rounded-xl bg-emerald-100 p-2 text-emerald-700" aria-hidden="true">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M16 20v-1.5a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4V20m5.5-9a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm8-6.5a3.5 3.5 0 0 1 0 6.8m2.5 4.2a4 4 0 0 1 3 3.9V20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        </span>
                    </div>
                    <a href="{{ route('kesiswaan.users.template-siswa') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs rounded-full shadow shadow-emerald-500/20 transition">
                        <svg aria-hidden="true" class="w-4 h-4" viewBox="0 0 20 20" fill="none"><path d="M10 3v9m0 0 3.5-3.5M10 12 6.5 8.5M4 13.5v2A1.5 1.5 0 0 0 5.5 17h9a1.5 1.5 0 0 0 1.5-1.5v-2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Unduh template siswa
                    </a>
                    <p class="mt-3 text-[11px] leading-relaxed text-emerald-900/80">NIS hanya boleh berisi angka. Kelas dipilih pada formulir di bawah. Jabatan diisi <strong>siswa</strong> atau <strong>ketua</strong>.</p>
                </article>

                <article class="rounded-2xl border border-blue-100 bg-blue-50/50 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-xs font-extrabold text-theme-blue">Template Akun Pembina</h3>
                            <p class="mt-1 text-[11px] text-gray-600">Kolom NIP dan nama lengkap pembina.</p>
                        </div>
                        <span class="rounded-xl bg-blue-100 p-2 text-theme-blue" aria-hidden="true">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M12 3.5 19 6v5.5c0 4.2-2.8 7.3-7 9-4.2-1.7-7-4.8-7-9V6l7-2.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 10.5h6M9 14h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        </span>
                    </div>
                    <a href="{{ route('kesiswaan.users.template-pembina') }}" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full shadow shadow-blue-500/20 transition">
                        <svg aria-hidden="true" class="w-4 h-4" viewBox="0 0 20 20" fill="none"><path d="M10 3v9m0 0 3.5-3.5M10 12 6.5 8.5M4 13.5v2A1.5 1.5 0 0 0 5.5 17h9a1.5 1.5 0 0 0 1.5-1.5v-2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Unduh template pembina
                    </a>
                    <p class="mt-3 text-[11px] leading-relaxed text-blue-900/80">Isi NIP dengan angka saja. Username dipakai untuk login dan harus berbeda dari NIP. Jenis kelamin dapat dilengkapi setelah login.</p>
                </article>
            </div>
        </section>

        <section class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm" aria-labelledby="upload-heading">
            <div class="mb-5">
                <h2 id="upload-heading" class="text-sm font-extrabold text-theme-dark">2. Upload File Excel</h2>
                <p class="mt-1 text-xs text-gray-400">Pilih jenis akun dan file dengan format .xlsx, .xls, atau .csv.</p>
            </div>
            <form method="POST" action="{{ route('kesiswaan.users.import') }}" enctype="multipart/form-data" id="form-import" class="space-y-5">
                @csrf
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="jenis-akun" class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Jenis Akun</label>
                        <select name="jenis" id="jenis-akun" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                            <option value="siswa" {{ old('jenis', 'siswa') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                            <option value="pembina" {{ old('jenis') === 'pembina' ? 'selected' : '' }}>Pembina</option>
                        </select>
                    </div>
                    <div id="kelas-wrapper">
                        <label for="kelas-id" class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Kelas Tujuan <span class="text-red-500">*</span></label>
                        <select name="kelas_id" id="kelas-id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-theme-blue transition">
                            <option value="">Pilih kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }} ({{ config("kelas.tingkat.{$k->tingkat}") }})</option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-[11px] text-gray-400">Semua siswa dari file akan dimasukkan ke kelas ini.</p>
                    </div>
                </div>

                <div>
                    <label for="file-import" class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">File Excel atau CSV</label>
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        <input id="file-import" type="file" name="file" accept=".xlsx,.xls,.csv" required class="block w-full text-xs text-gray-600 file:mr-3 file:px-4 file:py-1.5 file:rounded-full file:border-0 file:bg-theme-blue file:text-white file:font-bold hover:file:bg-theme-darkBlue transition">
                        <p id="file-name" class="mt-2 text-[11px] text-gray-400" aria-live="polite">Belum ada file dipilih.</p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50/60">
                    <p class="text-[11px] font-bold text-amber-700">Password semua akun hasil import otomatis: <code class="bg-white px-2 py-0.5 rounded-lg font-mono font-extrabold">password</code></p>
                    <p class="mt-1 text-[11px] text-amber-700/80">Pengguna dapat melengkapi profil setelah login. Password bisa direset dari halaman daftar akun.</p>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                    <p class="text-[11px] text-gray-400">Pastikan jenis akun dan kelas sesuai dengan isi file.</p>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full shadow-lg shadow-blue-500/20 transition">
                        Import Akun
                        <svg aria-hidden="true" class="w-4 h-4" viewBox="0 0 20 20" fill="none"><path d="M10 13V4m0 0L6.5 7.5M10 4l3.5 3.5M4 12.5v2A1.5 1.5 0 0 0 5.5 16h9a1.5 1.5 0 0 0 1.5-1.5v-2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        (() => {
            const jenis = document.getElementById('jenis-akun');
            const wrap = document.getElementById('kelas-wrapper');
            const kelas = document.getElementById('kelas-id');
            const file = document.getElementById('file-import');
            const fileName = document.getElementById('file-name');

            function syncJenis() {
                const isSiswa = jenis.value === 'siswa';
                wrap.classList.toggle('hidden', !isSiswa);
                kelas.required = isSiswa;
            }

            jenis.addEventListener('change', syncJenis);
            file.addEventListener('change', () => {
                fileName.textContent = file.files.length ? `File dipilih: ${file.files[0].name}` : 'Belum ada file dipilih.';
            });
            syncJenis();
        })();
    </script>
@endsection
