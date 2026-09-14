@extends('ketua.layout')
@section('title', 'Buat Laporan Bulanan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Buat Laporan Bulanan</h1>
            <p class="text-xs text-slate-400 mt-1">Isi laporan kegiatan ekskul untuk satu bulan</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl space-y-5">
        <form action="{{ route('ketua.laporan-bulanan.store') }}" method="POST">
            @csrf

            <div class="mb-4 p-3 bg-blue-50 rounded-2xl border border-blue-100">
                <p class="text-[11px] text-blue-600 font-medium">📅 Laporan dibuat otomatis untuk <strong>{{ now()->translatedFormat('F Y') }}</strong> (bulan berjalan).</p>
            </div>

            <div class="p-3 bg-sky-50 rounded-2xl border border-sky-100">
                <p class="text-[11px] text-sky-600 font-medium">Materi kegiatan akan digenerate otomatis berdasarkan data kegiatan yang tercatat di bulan tersebut.</p>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tujuan Kegiatan</label>
                <textarea name="tujuan" rows="4" placeholder="Contoh: Mengembangkan kemampuan bermain alat musik..."
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('tujuan') }}</textarea>
            </div>

            <div class="p-3 bg-sky-50 rounded-2xl border border-sky-100">
                <p class="text-[11px] text-sky-600 font-medium">Kehadiran peserta akan digenerate otomatis berdasarkan data presensi kegiatan bulan tersebut.</p>
            </div>

            <div class="p-3 bg-sky-50 rounded-2xl border border-sky-100">
                <p class="text-[11px] text-sky-600 font-medium">Dokumentasi akan diambil otomatis dari dokumentasi kegiatan di bulan tersebut.</p>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Evaluasi - Keberhasilan</label>
                <textarea name="evaluasi_keberhasilan" rows="3" placeholder="Contoh: Siswa telah mampu bermain bersama dengan koordinasi yang baik..."
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('evaluasi_keberhasilan') }}</textarea>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Evaluasi - Kendala</label>
                <textarea name="evaluasi_kendala" rows="3" placeholder="Contoh: Masih terdapat permasalahan dalam pengaturan sound..."
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('evaluasi_kendala') }}</textarea>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Evaluasi - Solusi / Tindak Lanjut</label>
                <textarea name="evaluasi_solusi" rows="3" placeholder="Contoh: Akan dilaksanakan pelatihan dasar mengenai pengaturan sound..."
                    class="w-full px-4 py-2.5 bg-sky-50/50 border border-sky-100 rounded-2xl text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('evaluasi_solusi') }}</textarea>
            </div>

            <div class="flex gap-2 pt-2 flex-wrap">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-bold text-xs rounded-xl shadow-lg shadow-sky-200 transition w-full sm:w-auto">Simpan</button>
                <a href="{{ route('ketua.laporan-bulanan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition w-full sm:w-auto text-center">Batal</a>
            </div>
        </form>
    </div>
@endsection