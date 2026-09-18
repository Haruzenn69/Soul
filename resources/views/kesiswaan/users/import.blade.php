@extends('layouts.kesiswaan')

@section('title', 'Import Akun')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-theme-dark">Import Akun Excel</h1>
            <p class="text-xs text-gray-400 mt-1">Import massal akun siswa / pembina dari file Excel sesuai kolom database.</p>
        </div>
        <a href="{{ route('kesiswaan.users.index') }}"
           class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-xs rounded-full transition flex items-center gap-2 shrink-0">
            ← Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 rounded-2xl text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-xs font-semibold">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-xs font-semibold">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('importErrors'))
        <div class="p-4 bg-amber-50 border border-amber-200 text-amber-700 rounded-2xl text-xs font-semibold">
            <p class="mb-2 font-bold">⚠ Beberapa baris gagal diimpor:</p>
            <ul class="list-disc list-inside space-y-1 max-h-48 overflow-y-auto">
                @foreach (session('importErrors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Template -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <h2 class="text-sm font-extrabold text-theme-dark mb-1">1. Download Template</h2>
        <p class="text-xs text-gray-400 mb-4">Pilih role akun, lalu download template berisi contoh data yang bisa diikuti.</p>
        <div class="flex flex-wrap gap-3 items-center">
            <select id="template-role" class="px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-xs focus:outline-none focus:border-theme-blue transition">
                <option value="siswa">Template Siswa</option>
                <option value="pembina">Template Pembina</option>
            </select>
            <a href="#" id="download-template"
               class="px-5 py-2.5 bg-theme-dark hover:bg-black text-white font-bold text-xs rounded-full transition">
                Download Template
            </a>
        </div>
    </div>

    <!-- Upload -->
    <form action="{{ route('kesiswaan.users.import.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm" id="import-form">
        @csrf
        <h2 class="text-sm font-extrabold text-theme-dark mb-1">2. Upload File Excel</h2>
        <p class="text-xs text-gray-400 mb-4">File berformat <span class="font-bold">.xlsx, .xls, .csv, .ods</span> maksimal 4 MB.</p>

        <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-theme-blue transition" id="drop-zone">
            <input type="file" name="file" id="file-input" accept=".xlsx,.xls,.csv,.ods" class="hidden" required>
            <div id="upload-prompt" class="cursor-pointer">
                <div class="text-3xl mb-2">📄</div>
                <p class="text-xs font-bold text-theme-dark">Klik atau seret file Excel ke sini</p>
                <p class="text-[11px] text-gray-400 mt-1">Sistem otomatis mendeteksi kolom NIS (siswa) atau NIP (pembina)</p>
            </div>
            <div id="file-info" class="hidden cursor-pointer">
                <p id="file-name" class="text-xs font-bold text-theme-blue"></p>
                <p class="text-[11px] text-gray-400 mt-1">Klik untuk mengganti file</p>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button type="submit" class="px-6 py-3 bg-theme-blue hover:bg-theme-darkBlue text-white font-bold text-xs rounded-full shadow-lg shadow-blue-500/20 transition">
                Import Akun
            </button>
        </div>
    </form>

    <!-- Panduan Kolom -->
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
        <h2 class="text-sm font-extrabold text-theme-dark mb-3">📋 Panduan Kolom Excel</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-xs font-extrabold text-emerald-600 mb-2">Row Siswa</h3>
                <table class="card-table w-full text-left text-xs">
                    <thead>
                        <tr class="text-[11px] font-bold text-gray-400 uppercase border-b border-gray-100">
                            <th class="py-2">Kolom</th>
                            <th class="py-2">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr><td class="py-2 font-mono">email</td><td class="text-gray-500">Opsional, kosongkan agar otomatis = nis@soul.test</td></tr>
                        <tr><td class="py-2 font-mono">nis</td><td class="text-gray-500 font-bold">Wajib, unik</td></tr>
                        <tr><td class="py-2 font-mono">nama</td><td class="text-gray-500 font-bold">Wajib</td></tr>
                        <tr><td class="py-2 font-mono">kelas</td><td class="text-gray-500 font-bold">Wajib — pilih dari dropdown template (nilai diambil dari data kelas yang sudah dibuat)</td></tr>
                        <tr><td class="py-2 font-mono">jenis_kelamin</td><td class="text-gray-500 font-bold">Wajib — dropdown: laki-laki / perempuan</td></tr>
                        <tr><td class="py-2 font-mono">jabatan</td><td class="text-gray-500">Dropdown: siswa / anggota / ketua</td></tr>
                        <tr><td class="py-2 font-mono">password</td><td class="text-gray-500">Opsional, default: password</td></tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="py-2 text-amber-600 font-semibold">Usernamenya diisi sendiri oleh siswa saat pertama kali login (harus unik).</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div>
                <h3 class="text-xs font-extrabold text-blue-600 mb-2">Row Pembina</h3>
                <table class="card-table w-full text-left text-xs">
                    <thead>
                        <tr class="text-[11px] font-bold text-gray-400 uppercase border-b border-gray-100">
                            <th class="py-2">Kolom</th>
                            <th class="py-2">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr><td class="py-2 font-mono">email</td><td class="text-gray-500">Opsional, kosongkan agar otomatis = nip@soul.test</td></tr>
                        <tr><td class="py-2 font-mono">nip</td><td class="text-gray-500 font-bold">Wajib, unik</td></tr>
                        <tr><td class="py-2 font-mono">nama</td><td class="text-gray-500 font-bold">Wajib</td></tr>
                        <tr><td class="py-2 font-mono">jenis_kelamin</td><td class="text-gray-500 font-bold">Wajib — dropdown: laki-laki / perempuan</td></tr>
                        <tr><td class="py-2 font-mono">password</td><td class="text-gray-500">Opsional, default: password</td></tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="py-2 text-amber-600 font-semibold">Usernamenya diisi sendiri oleh pembina saat pertama kali login (harus unik).</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const templateRole = document.getElementById('template-role');
            const downloadLink = document.getElementById('download-template');
            downloadLink.href = '{{ route('kesiswaan.users.import.template') }}?role=' + templateRole.value;
            templateRole.addEventListener('change', () => {
                downloadLink.href = '{{ route('kesiswaan.users.import.template') }}?role=' + templateRole.value;
            });

            const dropZone = document.getElementById('drop-zone');
            const fileInput = document.getElementById('file-input');
            const uploadPrompt = document.getElementById('upload-prompt');
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('file-name');

            dropZone.addEventListener('click', () => fileInput.click());
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    fileName.textContent = '✅ ' + fileInput.files[0].name;
                    uploadPrompt.classList.add('hidden');
                    fileInfo.classList.remove('hidden');
                }
            });
            ['dragover', 'dragenter'].forEach((e) => dropZone.addEventListener(e, (ev) => { ev.preventDefault(); dropZone.classList.add('border-theme-blue'); }));
            ['dragleave', 'drop'].forEach((e) => dropZone.addEventListener(e, (ev) => { ev.preventDefault(); dropZone.classList.remove('border-theme-blue'); }));
            dropZone.addEventListener('drop', (ev) => {
                if (ev.dataTransfer.files.length > 0) {
                    fileInput.files = ev.dataTransfer.files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            });
        });
    </script>
@endsection