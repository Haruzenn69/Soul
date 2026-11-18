@extends('pembina.layout')

@section('title', 'Edit Data Pelatih')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-up">

    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('pembina.pelatih.index') }}"
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-sky-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Data Pelatih
        </a>
    </div>

    {{-- Hero Card --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-500 via-blue-600 to-indigo-700 p-6 md:p-8 text-white shadow-xl shadow-sky-200">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -bottom-24 -left-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -top-24 -right-10 w-72 h-72 rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur border border-white/20 text-[11px] font-bold uppercase tracking-wider text-white mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Data Pelatih
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-white">
                Edit Pelatih: {{ $pelatih->nama }}
            </h1>
            <p class="text-xs text-white/80 mt-1 max-w-xl leading-relaxed">
                Perbarui biodata, penugasan ekskul, berkas CV, sertifikat, serta tanda tangan resmi pelatih. Berkas lama tetap tersimpan jika tidak diunggah ulang.
            </p>
        </div>
    </div>

    {{-- Alert Error --}}
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs animate-shake">
            <div class="font-bold flex items-center gap-2 mb-1.5">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Mohon periksa kembali formulir berikut:
            </div>
            <ul class="list-disc list-inside space-y-1 text-[11px]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <form action="{{ route('pembina.pelatih.update', $pelatih) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-sky-100 p-6 md:p-8 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        {{-- Section 1: Ekskul & Identitas Utama --}}
        <div>
            <h2 class="text-sm font-extrabold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 font-black text-xs flex items-center justify-center">1</span>
                Informasi Ekskul & Identitas
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Ekskul Binaan --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Ekskul Binaan <span class="text-rose-500">*</span>
                    </label>
                    <select name="ekskul_id" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                        <option value="">-- Pilih Ekskul yang Dilatih --</option>
                        @foreach ($ekskuls as $ekskul)
                            <option value="{{ $ekskul->id }}" {{ old('ekskul_id', $pelatih->ekskul_id) == $ekskul->id ? 'selected' : '' }}>
                                {{ $ekskul->nama_ekskul }} (Bidang: {{ $ekskul->bidang }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Pilih ekskul binaan yang dilatih oleh pelatih ini.</p>
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nama Lengkap Pelatih <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama', $pelatih->nama) }}" required placeholder="Contoh: Coach Hendra Setiawan, S.Pd."
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>

                {{-- Jenis Kelamin --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 cursor-pointer hover:bg-sky-50 transition">
                            <input type="radio" name="jenis_kelamin" value="laki-laki" {{ old('jenis_kelamin', $pelatih->jenis_kelamin) === 'laki-laki' ? 'checked' : '' }} class="text-sky-600 focus:ring-sky-500">
                            <span>Laki-laki</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 cursor-pointer hover:bg-sky-50 transition">
                            <input type="radio" name="jenis_kelamin" value="perempuan" {{ old('jenis_kelamin', $pelatih->jenis_kelamin) === 'perempuan' ? 'checked' : '' }} class="text-sky-600 focus:ring-sky-500">
                            <span>Perempuan</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Kontak & Alamat Domisili --}}
        <div>
            <h2 class="text-sm font-extrabold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 font-black text-xs flex items-center justify-center">2</span>
                Kontak & Alamat Domisili
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- No HP --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nomor HP / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $pelatih->no_hp) }}" required placeholder="Contoh: 081234567890"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $pelatih->email) }}" required placeholder="Contoh: pelatih@gmail.com"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>

                {{-- Domisili --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Kota Domisili <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="domisili" value="{{ old('domisili', $pelatih->domisili) }}" required placeholder="Contoh: Jakarta Selatan / Bandung / Surabaya"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>

                {{-- Media Sosial --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Media Sosial <span class="text-slate-400 font-normal">(opsional, IG / LinkedIn)</span>
                    </label>
                    <input type="text" name="sosmed" value="{{ old('sosmed', $pelatih->sosmed) }}" placeholder="Contoh: @coach.hendra atau linkedin.com/in/coach-hendra"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">
                </div>

                {{-- Alamat Lengkap --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Alamat Lengkap Tempat Tinggal <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="alamat" rows="2.5" required placeholder="Tuliskan jalan, nomor rumah, RT/RW, kelurahan, dan kecamatan..."
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition">{{ old('alamat', $pelatih->alamat) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Section 3: Berkas CV, Sertifikat & Tanda Tangan --}}
        <div x-data="{ ttdPreview: '{{ $pelatih->tanda_tangan ? route('storage.public', ['path' => $pelatih->tanda_tangan]) : '' }}' }">
            <h2 class="text-sm font-extrabold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 font-black text-xs flex items-center justify-center">3</span>
                Dokumen Berkas & Tanda Tangan Resmi
            </h2>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Curriculum Vitae (CV) --}}
                    <div class="p-4 rounded-2xl bg-sky-50/60 border-2 border-dashed border-sky-300">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-extrabold text-slate-800">
                                Curriculum Vitae / CV (PDF)
                            </label>
                            @if($pelatih->cv)
                                <a href="{{ route('storage.public', ['path' => $pelatih->cv]) }}" target="_blank"
                                   class="text-[11px] font-bold text-sky-600 hover:underline inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat CV Saat Ini
                                </a>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3 leading-relaxed">
                            Kosongkan jika tidak ingin mengganti file CV (format PDF, maks 5MB).
                        </p>
                        <input type="file" name="cv" accept="application/pdf"
                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-600 file:text-white hover:file:bg-sky-700 file:cursor-pointer transition">
                    </div>

                    {{-- Sertifikat Pelatih --}}
                    <div class="p-4 rounded-2xl bg-amber-50/60 border-2 border-dashed border-amber-300">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-extrabold text-slate-800">
                                Sertifikat Pelatih (PDF)
                            </label>
                            @if($pelatih->sertifikat)
                                <a href="{{ route('storage.public', ['path' => $pelatih->sertifikat]) }}" target="_blank"
                                   class="text-[11px] font-bold text-amber-700 hover:underline inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Lihat Sertifikat Saat Ini
                                </a>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3 leading-relaxed">
                            Kosongkan jika tidak ingin mengganti file sertifikat (format PDF, maks 5MB).
                        </p>
                        <input type="file" name="sertifikat" accept="application/pdf"
                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-white hover:file:bg-amber-600 file:cursor-pointer transition">
                    </div>
                </div>

                {{-- Tanda Tangan Pelatih --}}
                <div class="p-4 rounded-2xl bg-emerald-50/60 border-2 border-dashed border-emerald-300">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <label class="text-xs font-extrabold text-slate-800">
                                Tanda Tangan Pelatih (PNG / JPG)
                            </label>
                        </div>
                        <span class="text-[10px] text-emerald-700 font-semibold">Digunakan khusus untuk absensi pelatih di laporan bulanan</span>
                    </div>
                    <p class="text-[11px] text-slate-500 mb-3 leading-relaxed">
                        Kosongkan jika tidak ingin mengganti tanda tangan. Unggah berkas baru (PNG/JPG latar transparan atau putih, maks 2MB) untuk memperbarui.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                        <div class="flex-1 w-full">
                            <input type="file" name="tanda_tangan" accept="image/png,image/jpeg,image/jpg"
                                   @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { ttdPreview = e.target.result; }; reader.readAsDataURL(file); }"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 file:cursor-pointer transition">
                        </div>

                        {{-- Preview Box --}}
                        <div x-show="ttdPreview" x-cloak class="shrink-0 flex items-center gap-2 p-2 bg-white rounded-xl border border-emerald-200 shadow-sm">
                            <span class="text-[10px] text-slate-400 font-bold px-1">Pratinjau:</span>
                            <img :src="ttdPreview" alt="Pratinjau TTD" class="h-10 max-w-[120px] object-contain">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('pembina.pelatih.index') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 text-white font-bold text-xs shadow-lg shadow-sky-500/25 hover:from-sky-600 hover:to-blue-700 transition transform hover:-translate-y-0.5">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
