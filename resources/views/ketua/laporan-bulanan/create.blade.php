@extends('ketua.layout')
@section('title', 'Buat Laporan Bulanan')

@section('content')
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm max-w-2xl">
        <form action="{{ route('ketua.laporan-bulanan.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Bulan (YYYY-MM)</label>
                <input type="month" name="bulan" value="{{ old('bulan') }}" required
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">
                @error('bulan') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4 p-3 bg-blue-50 rounded-2xl border border-blue-100">
                <p class="text-[11px] text-blue-600 font-medium">📋 Materi kegiatan akan digenerate otomatis berdasarkan data kegiatan yang tercatat di bulan tersebut.</p>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Tujuan Kegiatan</label>
                <textarea name="tujuan" rows="4" placeholder="Contoh: Mengembangkan kemampuan bermain alat musik..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">{{ old('tujuan') }}</textarea>
            </div>

            <div class="mb-4 p-3 bg-blue-50 rounded-2xl border border-blue-100">
                <p class="text-[11px] text-blue-600 font-medium">📊 Kehadiran peserta akan digenerate otomatis berdasarkan data presensi kegiatan bulan tersebut.</p>
            </div>

            <div class="mb-4 p-3 bg-blue-50 rounded-2xl border border-blue-100">
                <p class="text-[11px] text-blue-600 font-medium">📸 Dokumentasi akan diambil otomatis dari dokumentasi kegiatan di bulan tersebut.</p>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Evaluasi - Keberhasilan</label>
                <textarea name="evaluasi_keberhasilan" rows="3" placeholder="Contoh: Siswa telah mampu bermain bersama dengan koordinasi yang baik..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">{{ old('evaluasi_keberhasilan') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Evaluasi - Kendala</label>
                <textarea name="evaluasi_kendala" rows="3" placeholder="Contoh: Masih terdapat permasalahan dalam pengaturan sound..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">{{ old('evaluasi_kendala') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 mb-1">Evaluasi - Solusi / Tindak Lanjut</label>
                <textarea name="evaluasi_solusi" rows="3" placeholder="Contoh: Akan dilaksanakan pelatihan dasar mengenai pengaturan sound..."
                    class="w-full px-4 py-2.5 rounded-2xl bg-gray-50 border border-gray-200 text-xs focus:outline-none focus:border-theme-blue transition">{{ old('evaluasi_solusi') }}</textarea>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit" class="px-5 py-2 bg-theme-blue hover:bg-theme-darkBlue text-white text-xs font-semibold rounded-full transition">Simpan</button>
                <a href="{{ route('ketua.laporan-bulanan.index') }}" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-full transition">Batal</a>
            </div>
        </form>
    </div>
@endsection
