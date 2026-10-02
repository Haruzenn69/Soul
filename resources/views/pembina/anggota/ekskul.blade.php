@extends("pembina.layout")
@section("title", "Data Anggota")
@section("content")
<div class="space-y-6 animate-fade-up">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900 tracking-tight">Data Anggota</h1>
            <p class="text-xs text-slate-400 mt-0.5">Kelola anggota aktif ekskul {{ $ekskul->nama_ekskul ?? "" }}.</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-3xl border border-sky-100 shadow-sm">
        <p class="text-xs text-slate-500">Data anggota per ekskul</p>
    </div>
</div>
@endsection
