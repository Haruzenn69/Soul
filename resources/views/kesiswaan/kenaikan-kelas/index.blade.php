@extends('layouts.kesiswaan')
@section('title', 'Kenaikan Kelas Siswa')

@section('content')
<div class="mx-auto max-w-6xl space-y-5">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Kenaikan Kelas</h1>
        <p class="mt-1 text-xs text-slate-500">Tahun ajaran baru diaktifkan terlebih dahulu. Setelah itu, pilih siswa yang naik atau lulus.</p>
    </div>
    @if(!$activeYear || !$sourceYear)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">Aktifkan tahun ajaran baru terlebih dahulu untuk memproses pilihan kenaikan kelas.</div>
    @else
        <div class="rounded-2xl border border-sky-100 bg-sky-50 p-4 text-sm text-sky-800">Pilihan dari {{ $sourceYear->nama }} akan diterapkan ke tahun ajaran aktif {{ $activeYear->nama }}. Siswa yang tidak dicentang tetap pada tingkat kelasnya.</div>
        @if($errors->any())<div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-700">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <form method="POST" action="{{ route('kesiswaan.kenaikan-kelas.process') }}" class="space-y-4">
            @csrf
            @foreach(['x' => ['Kelas 10', 'naik_xi', 'Centang siswa yang naik ke kelas 11, lalu pilih rombel tujuan'], 'xi' => ['Kelas 11', 'naik_xii', 'Centang siswa yang naik ke kelas 12, lalu pilih rombel tujuan'], 'xii' => ['Kelas 12', 'lulus', 'Centang siswa yang lulus dan menjadi alumni']] as $tier => [$label, $key, $action])
                @php($tierClasses = $classes->where('tingkat', $tier))
                @if($tierClasses->isNotEmpty())
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50 px-5 py-3">
                        <h2 class="text-sm font-extrabold text-slate-800">{{ $label }}</h2><span class="text-xs text-slate-500">{{ $action }}</span>
                    </div>
                    @foreach($tierClasses as $class)
                        @if($class->siswas->isNotEmpty())
                        @php
                            $targetTier = $tier === 'x' ? 'xi' : 'xii';
                            $availableTargets = $tier === 'xii' ? collect() : ($targetClasses->get($targetTier, collect())->filter(function ($target) use ($class) {
                                return $target->jurusan === $class->jurusan || ($class->jurusan === 'mp' && $target->jurusan === 'mlog');
                            })->values());
                            $defaultTarget = $availableTargets->first(fn ($target) => $target->jurusan === $class->jurusan && (string) $target->rombel === (string) $class->rombel);
                        @endphp
                        <div class="border-b border-slate-100 last:border-0">
                            <h3 class="px-5 pt-3 text-xs font-bold text-slate-600">{{ $class->nama }}</h3>
                            <div class="grid gap-1 px-4 py-3 sm:grid-cols-2">
                                @foreach($class->siswas as $siswa)
                                <div class="rounded-lg px-2 py-2 hover:bg-sky-50">
                                    <label class="flex cursor-pointer items-center gap-3">
                                        <input type="checkbox" name="{{ $key }}[]" value="{{ $siswa->id }}" data-promotion-checkbox="{{ $siswa->id }}" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                        <span class="text-sm text-slate-700">{{ $siswa->nama }} <span class="text-slate-400">({{ $siswa->nis }})</span></span>
                                    </label>
                                    @if($tier !== 'xii')
                                    <select name="kelas_tujuan[{{ $siswa->id }}]" data-target-select="{{ $siswa->id }}" disabled required class="ml-7 mt-2 w-[calc(100%-1.75rem)] rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-xs text-slate-600 disabled:bg-slate-100">
                                        <option value="">Pilih kelas {{ $targetTier === 'xi' ? 11 : 12 }} tujuan</option>
                                        @foreach($availableTargets as $target)
                                            <option value="{{ $target->id }}" {{ $defaultTarget?->id === $target->id ? 'selected' : '' }}>{{ $target->nama }}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                </section>
                @endif
            @endforeach
            <div class="flex justify-end rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
                <button type="submit" onclick="return confirm('Simpan pilihan kenaikan dan kelulusan siswa?')" class="rounded-xl bg-sky-600 px-5 py-3 text-sm font-bold text-white hover:bg-sky-700">Simpan Pilihan</button>
            </div>
        </form>
        <script>
            document.querySelectorAll('[data-promotion-checkbox]').forEach((checkbox) => {
                const target = document.querySelector(`[data-target-select="${checkbox.dataset.promotionCheckbox}"]`);
                if (!target) return;
                const sync = () => { target.disabled = !checkbox.checked; };
                checkbox.addEventListener('change', sync);
                sync();
            });
        </script>
    @endif
</div>
@endsection
