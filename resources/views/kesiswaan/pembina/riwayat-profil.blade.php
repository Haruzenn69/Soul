@extends('layouts.kesiswaan')

@section('title', 'Riwayat Profil Pembina')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Riwayat Perubahan Profil</h1>
            <p class="text-xs text-slate-500 mt-1">{{ $pembina->nama }} · perubahan foto profil, nomor telepon, dan email</p>
        </div>
        <a href="{{ route('kesiswaan.pembina.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50">
            Kembali ke Data Pembina
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        @if ($riwayat->isEmpty())
            <div class="px-6 py-14 text-center">
                <p class="text-sm font-bold text-slate-700">Belum ada perubahan profil</p>
                <p class="text-xs text-slate-400 mt-1">Perubahan yang dilakukan pembina akan tercatat di halaman ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-3.5 font-bold">Waktu</th>
                            <th class="px-5 py-3.5 font-bold">Data</th>
                            <th class="px-5 py-3.5 font-bold">Sebelumnya</th>
                            <th class="px-5 py-3.5 font-bold">Perubahan</th>
                            <th class="px-5 py-3.5 font-bold">Diubah oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach ($riwayat as $item)
                            @php
                                $label = ['foto' => 'Foto profil', 'no_telp' => 'Nomor telepon', 'email' => 'Email'][$item->field] ?? ucfirst($item->field);
                            @endphp
                            <tr class="align-top">
                                <td class="px-5 py-4 whitespace-nowrap text-slate-600">{{ $item->created_at->isoFormat('D MMM YYYY, HH:mm') }}</td>
                                <td class="px-5 py-4 font-bold text-slate-800">{{ $label }}</td>
                                <td class="px-5 py-4 text-slate-600">
                                    @if ($item->field === 'foto')
                                        @if ($item->old_value)
                                            <img src="{{ asset('storage/'.$item->old_value) }}" alt="Foto profil sebelumnya" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                                        @else
                                            Belum ada foto
                                        @endif
                                    @else
                                        {{ $item->old_value ?: '—' }}
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    @if ($item->field === 'foto')
                                        @if ($item->new_value)
                                            <img src="{{ asset('storage/'.$item->new_value) }}" alt="Foto profil baru" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                                        @else
                                            Foto dihapus
                                        @endif
                                    @else
                                        {{ $item->new_value ?: '—' }}
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $item->changedBy?->pembina?->nama ?? $item->changedBy?->username ?? 'Pengguna' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100">{{ $riwayat->links() }}</div>
        @endif
    </div>
</div>
@endsection
