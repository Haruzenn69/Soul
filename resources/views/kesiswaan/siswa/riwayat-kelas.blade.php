@extends('layouts.kesiswaan')

@section('title', 'Riwayat Kelas Siswa')

@section('content')
    <div class="mx-auto max-w-5xl space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Riwayat Kelas</h1>
                <p class="mt-1 text-xs text-slate-500">{{ $siswa->nama }} · NIS {{ $siswa->nis }}</p>
            </div>
            <a href="{{ route('kesiswaan.siswa.index') }}" class="rounded-full bg-slate-100 px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-200">Kembali</a>
        </div>

        <section class="overflow-hidden rounded-3xl border border-sky-100 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[620px] text-left text-xs">
                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Tahun Ajaran</th><th class="px-5 py-3">Kelas Asal</th><th class="px-5 py-3">Kelas Tujuan</th><th class="px-5 py-3">Keterangan</th><th class="px-5 py-3">Tanggal</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($riwayatKelas as $history)
                            <tr><td class="px-5 py-3">{{ $history->tahun_ajaran }}</td><td class="px-5 py-3">{{ $history->kelas_asal }}</td><td class="px-5 py-3">{{ $history->kelas_tujuan ?? 'Lulus / Alumni' }}</td><td class="px-5 py-3">{{ ['kenaikan' => 'Naik kelas otomatis', 'penempatan' => 'Penempatan oleh kesiswaan', 'kelulusan' => 'Lulus dan diarsipkan'][$history->jenis] ?? $history->jenis }}</td><td class="px-5 py-3">{{ $history->diproses_pada?->format('d-m-Y H:i') }}</td></tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">Belum ada riwayat perpindahan atau kelulusan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($riwayatKelas->hasPages())<div class="border-t border-slate-100 p-4">{{ $riwayatKelas->links() }}</div>@endif
        </section>
    </div>
@endsection
