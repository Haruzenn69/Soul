@extends('ketua.layout')
@section('title', 'Edit Laporan Bulanan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Edit Laporan Bulanan</h1>
            <p class="text-xs text-slate-400 mt-1">Perbarui isi laporan sebelum dikirim ke pembina.</p>
        </div>
        <a href="{{ route('ketua.laporan-bulanan.show', $laporan) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition text-center">Kembali ke detail</a>
    </div>
    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 max-w-2xl">
        <form action="{{ route('ketua.laporan-bulanan.update', $laporan) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4 p-4 bg-sky-50/70 rounded-2xl border border-sky-100">
                <p class="text-xs text-sky-700 font-semibold">Periode laporan</p>
                <p class="text-[11px] text-sky-600 mt-1"><strong>{{ \Carbon\Carbon::createFromFormat('Y-m', $laporan->bulan)->translatedFormat('F Y') }}</strong></p>
            </div>

            <div class="mb-4 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <p class="text-[11px] text-blue-600 font-medium flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/>
                </svg>
                Materi kegiatan akan digenerate otomatis berdasarkan data kegiatan yang tercatat di bulan tersebut.
            </p>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tujuan kegiatan</label>
                <textarea name="tujuan" rows="4" placeholder="Contoh: Mengembangkan kemampuan bermain alat musik..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('tujuan', $laporan->tujuan) }}</textarea>
            </div>

            <div class="mb-4 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <p class="text-[11px] text-blue-600 font-medium flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Kehadiran peserta akan digenerate otomatis berdasarkan data presensi kegiatan bulan tersebut.
            </p>
            </div>

            <div class="mb-4 p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <p class="text-[11px] text-blue-600 font-medium flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Dokumentasi akan diambil otomatis dari dokumentasi kegiatan di bulan tersebut.
            </p>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Evaluasi - keberhasilan</label>
                <textarea name="evaluasi_keberhasilan" rows="3" placeholder="Contoh: Siswa telah mampu bermain bersama dengan koordinasi yang baik..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('evaluasi_keberhasilan', $laporan->evaluasi_keberhasilan) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Evaluasi - kendala</label>
                <textarea name="evaluasi_kendala" rows="3" placeholder="Contoh: Masih terdapat permasalahan dalam pengaturan sound..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('evaluasi_kendala', $laporan->evaluasi_kendala) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Evaluasi - solusi / tindak lanjut</label>
                <textarea name="evaluasi_solusi" rows="3" placeholder="Contoh: Akan dilaksanakan pelatihan dasar mengenai pengaturan sound..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-sky-50/50 border border-sky-100 text-xs focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">{{ old('evaluasi_solusi', $laporan->evaluasi_solusi) }}</textarea>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-sky-200 transition">Simpan perubahan</button>
                <a href="{{ route('ketua.laporan-bulanan.show', $laporan) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection