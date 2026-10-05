@extends('layouts.kesiswaan')

@section('title', 'Kenaikan Kelas Siswa')

@section('content')
    <div class="mx-auto max-w-6xl space-y-5">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Kenaikan Kelas</h1>
            <p class="mt-1 text-xs text-slate-500">Pilih siswa dari kelas 10 untuk ditempatkan ke kelas 11 yang sesuai jurusannya.</p>
        </div>


        @if($errors->any())<div role="alert" class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-700"><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <section class="rounded-3xl border border-sky-100 bg-white p-5 shadow-sm">
            <label for="kelas-asal" class="mb-2 block text-xs font-bold text-slate-600">Pilih rombongan kelas 10 asal</label>
            <form method="GET" action="{{ route('kesiswaan.kenaikan-kelas.index') }}" class="flex flex-col gap-3 sm:flex-row">
                <select id="kelas-asal" name="kelas_asal_id" required class="min-w-0 flex-1 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs">
                    <option value="">Pilih kelas asal</option>
                    @foreach($sourceClasses as $source)
                        <option value="{{ $source->id }}" {{ $sourceClass?->id === $source->id ? 'selected' : '' }}>{{ $source->nama }} · TA {{ $source->tahunAjaran?->nama }} · {{ $source->pending_siswas_count }} siswa menunggu</option>
                    @endforeach
                </select>
                <button class="rounded-2xl bg-sky-600 px-5 py-3 text-xs font-bold text-white hover:bg-sky-700">Tampilkan Siswa</button>
            </form>
        </section>

        @if($sourceClass)
            <section class="overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-sm">
                @if($siswas->isEmpty())
                    <div class="p-8 text-center text-xs text-slate-500">Tidak ada siswa yang menunggu penempatan dari {{ $sourceClass->nama }}.</div>
                @elseif($targetClasses->isEmpty())
                    <div class="p-5 text-xs text-amber-800 bg-amber-50">Belum ada kelas 11 untuk jurusan kelas asal di tahun ajaran aktif {{ $activeYear?->nama ?? '-' }}. Pastikan proses kenaikan tahun ajaran berhasil.</div>
                @else
                    <form method="POST" action="{{ route('kesiswaan.kenaikan-kelas.assign') }}">
                        @csrf
                        <input type="hidden" name="kelas_asal_id" value="{{ $sourceClass->id }}">
                        <div class="flex flex-col gap-3 border-b border-slate-100 p-5 md:flex-row md:items-end md:justify-between">
                            <div>
                                <h2 class="text-sm font-extrabold text-slate-800">Siswa dari {{ $sourceClass->nama }}</h2>
                                <p class="mt-1 text-xs text-slate-500">Pilih siswa yang akan dipindahkan bersama-sama ke satu kelas 11.</p>
                            </div>
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <select name="kelas_tujuan_id" required class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs">
                                    <option value="">Pilih kelas 11 tujuan</option>
                                    @foreach($targetClasses as $target)
                                        <option value="{{ $target->id }}">{{ $target->nama }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="rounded-xl bg-sky-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-sky-700">Tempatkan Siswa Terpilih</button>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[600px] text-left text-xs">
                                <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500"><tr><th class="w-12 px-5 py-3"><input aria-label="Pilih semua siswa" type="checkbox" id="pilih-semua" class="rounded border-slate-300"></th><th class="px-5 py-3">Nama</th><th class="px-5 py-3">NIS</th><th class="px-5 py-3">Kelas Asal</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($siswas as $siswa)
                                        <tr class="hover:bg-sky-50/40"><td class="px-5 py-3"><input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" class="siswa-pilihan rounded border-slate-300"></td><td class="px-5 py-3 font-semibold text-slate-800">{{ $siswa->nama }}</td><td class="px-5 py-3 text-slate-600">{{ $siswa->nis }}</td><td class="px-5 py-3 text-slate-600">{{ $siswa->kelas?->nama }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <script>
                        document.getElementById('pilih-semua')?.addEventListener('change', event => {
                            document.querySelectorAll('.siswa-pilihan').forEach(input => input.checked = event.target.checked);
                        });
                    </script>
                @endif
            </section>
        @endif
    </div>
@endsection
