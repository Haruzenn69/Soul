@extends('layouts.kesiswaan')

@section('title', 'Semua Riwayat Profil Siswa')

@section('content')
<div class="space-y-5 animate-fade-up">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Riwayat Perubahan Profil Semua Siswa</h1>
            <p class="text-xs text-slate-500 mt-1">Pantau seluruh pembaruan foto profil, username, nomor telepon, dan email yang dilakukan siswa</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('kesiswaan.siswa.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 shadow-sm transition">
                Data Siswa
            </a>
            <a href="{{ route('kesiswaan.notifikasi') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-sky-50 border border-sky-200 text-sky-700 text-xs font-bold hover:bg-sky-100 shadow-sm transition">
                Notifikasi
            </a>
        </div>
    </div>

    {{-- Content Table --}}
    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm overflow-hidden">
        @if ($riwayat->isEmpty())
            <div class="px-6 py-14 text-center">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-400 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-bold text-slate-700">Belum ada riwayat perubahan profil</p>
                <p class="text-xs text-slate-400 mt-1">Saat siswa memperbarui foto profil atau informasi profil lainnya, perubahannya akan otomatis tercatat di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                            <th class="px-5 py-3.5">Waktu</th>
                            <th class="px-5 py-3.5">Siswa</th>
                            <th class="px-5 py-3.5">Data Diubah</th>
                            <th class="px-5 py-3.5">Sebelumnya</th>
                            <th class="px-5 py-3.5">Perubahan Baru</th>
                            <th class="px-5 py-3.5">Diubah Oleh</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach ($riwayat as $item)
                            @php
                                $label = [
                                    'foto' => 'Foto profil',
                                    'no_telp' => 'Nomor telepon',
                                    'email' => 'Email',
                                    'username' => 'Username login'
                                ][$item->field] ?? ucfirst($item->field);
                            @endphp
                            <tr class="align-top hover:bg-sky-50/20 transition">
                                <td class="px-5 py-4 whitespace-nowrap text-slate-500 font-medium">
                                    {{ $item->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                                    <div class="text-[10px] text-slate-400">{{ $item->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    @if($item->siswa)
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 font-bold flex items-center justify-center text-xs shrink-0 overflow-hidden">
                                                @if($item->siswa->foto_url)
                                                    <img src="{{ $item->siswa->foto_url }}" alt="{{ $item->siswa->nama }}" class="w-full h-full object-cover">
                                                @else
                                                    {{ strtoupper(substr($item->siswa->nama, 0, 2)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 leading-snug">{{ $item->siswa->nama }}</div>
                                                <div class="text-[11px] text-slate-400">{{ $item->siswa->kelas?->nama ?? 'Siswa' }} · NIS: {{ $item->siswa->nis }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-slate-400">Siswa (Data telah dihapus)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-800">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-semibold {{ $item->field === 'foto' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-sky-50 text-sky-700 border border-sky-100' }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    @if ($item->field === 'foto')
                                        @if ($item->old_value)
                                            <div class="flex items-center gap-2">
                                                <img src="{{ asset('storage/'.$item->old_value) }}" alt="Foto sebelumnya" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm">
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Belum ada foto</span>
                                        @endif
                                    @else
                                        <span class="font-mono text-slate-600">{{ $item->old_value ?: '—' }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    @if ($item->field === 'foto')
                                        @if ($item->new_value)
                                            <div class="flex items-center gap-2">
                                                <img src="{{ asset('storage/'.$item->new_value) }}" alt="Foto baru" class="w-12 h-12 rounded-xl object-cover border border-sky-300 shadow-sm ring-2 ring-sky-100">
                                            </div>
                                        @else
                                            <span class="text-rose-500 font-semibold italic">Foto dihapus</span>
                                        @endif
                                    @else
                                        <span class="font-mono text-slate-800 font-semibold">{{ $item->new_value ?: '—' }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                                    <span class="text-slate-700 font-semibold">{{ $item->changedBy?->siswa?->nama ?? $item->changedBy?->username ?? 'Pengguna' }}</span>
                                    <div class="text-[10px] text-slate-400">{{ ucfirst($item->changedBy?->role ?? '') }}</div>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    @if($item->siswa)
                                        <a href="{{ route('kesiswaan.siswa.riwayat-profil', $item->siswa_id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold rounded-xl transition text-[11px]">
                                            Detail Siswa
                                        </a>
                                    @endif
                                </td>
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
