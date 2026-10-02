@extends("pembina.layout")
@section("title", "Detail Anggota")
@section("content")
<div class="space-y-6 animate-fade-up">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">Detail Anggota</h1>
            <p class="text-xs text-slate-400 mt-0.5">Data lengkap anggota ekskul.</p>
        </div>
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">Kembali</a>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center overflow-hidden">
                @if($pendaftaran->siswa?->foto_url)
                    <img src="{{ $pendaftaran->siswa->foto_url }}" alt="{{ $pendaftaran->siswa->nama }}" class="w-full h-full object-cover">
                @else
                    <span class="text-lg font-bold">{{ strtoupper(substr($pendaftaran->siswa->nama ?? "S", 0, 2)) }}</span>
                @endif
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">{{ $pendaftaran->siswa->nama ?? "-" }}</h2>
                <p class="text-xs text-slate-500">NIS: {{ $pendaftaran->siswa->nis ?? "-" }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
