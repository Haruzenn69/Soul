@extends('pembina.layout')
@section('title', 'Presensi Kegiatan')

@section('content')
    <div>
        <h1 class="text-xl font-bold text-slate-900">Presensi Kegiatan</h1>
        <p class="text-xs text-slate-400 mt-0.5">Riwayat kehadiran anggota pada setiap kegiatan ekskul yang anda bina</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
        @if(count($kegiatans) > 0)
            <div class="space-y-6">
                @foreach($kegiatans as $kegiatan)
                    <div class="bg-slate-50 rounded-2xl border border-slate-200/60 overflow-hidden">
                        <div class="p-4 bg-white border-b border-slate-200/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">{{ $kegiatan->materi }}</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('dddd, DD MMM Y') }}</p>
                            </div>
                            <span class="text-[10px] font-medium text-slate-500">{{ $kegiatan->presensis->count() }} anggota tercatat</span>
                        </div>

                        @if($kegiatan->presensis->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead class="bg-slate-100/70">
                                        <tr>
                                            <th class="text-left p-3 font-semibold text-slate-500">Nama</th>
                                            <th class="text-left p-3 font-semibold text-slate-500">Kelas</th>
                                            <th class="text-left p-3 font-semibold text-slate-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kegiatan->presensis as $presensi)
                                        <tr class="border-t border-slate-200/50 bg-white">
                                            <td class="p-3 font-medium">{{ $presensi->pendaftaran->siswa->nama ?? '-' }}</td>
                                            <td class="p-3">{{ $presensi->pendaftaran->siswa->kelas->nama ?? '-' }}</td>
                                            <td class="p-3">
                                                <span class="px-2 py-1 rounded-full text-[10px] font-semibold
                                                    @if($presensi->status == 'hadir') bg-emerald-100 text-emerald-700
                                                    @elseif($presensi->status == 'izin') bg-blue-100 text-blue-700
                                                    @elseif($presensi->status == 'sakit') bg-amber-100 text-amber-700
                                                    @else bg-red-100 text-red-700 @endif">
                                                    {{ ucfirst($presensi->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-6 text-center text-slate-400">
                                <p class="text-xs">Belum ada presensi dicatat untuk kegiatan ini.</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-slate-400">
                <p class="text-sm">Belum ada kegiatan yang tercatat.</p>
            </div>
        @endif
    </div>
@endsection
