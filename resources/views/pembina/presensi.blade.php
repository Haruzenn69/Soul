@extends('pembina.layout')
@section('title', 'Presensi Kegiatan')

@section('content')
    <div class="animate-fade-up">
        <h1 class="text-xl font-extrabold text-slate-900">Presensi Kegiatan</h1>
        <p class="text-xs text-slate-400 mt-0.5">Riwayat kehadiran anggota pada setiap kegiatan ekskul yang anda bina</p>
    </div>

    <div class="bg-white p-5 md:p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .1s">
        @if(count($kegiatans) > 0)
            <div class="space-y-6">
                @foreach($kegiatans as $kegiatan)
                    <div class="bg-gradient-to-br from-sky-50 via-white to-amber-50 rounded-2xl border border-sky-100 overflow-hidden shadow-sm">
                        <div class="p-4 bg-white/80 backdrop-blur border-b border-sky-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="text-sm font-extrabold text-slate-900 truncate">{{ $kegiatan->materi }}</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('dddd, DD MMM Y') }}</p>
                            </div>
                            <span class="text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-100 px-2.5 py-1 rounded-lg shrink-0">{{ $kegiatan->presensis->count() }} anggota tercatat</span>
                        </div>

                        @if($kegiatan->presensis->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="card-table w-full text-xs">
                                    <thead class="bg-sky-50">
                                        <tr>
                                            <th class="text-left p-3 font-semibold text-slate-500">Nama</th>
                                            <th class="text-left p-3 font-semibold text-slate-500">Kelas</th>
                                            <th class="text-left p-3 font-semibold text-slate-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kegiatan->presensis as $presensi)
                                        <tr class="border-t border-sky-50 bg-white">
                                            <td class="p-3 font-medium">{{ $presensi->pendaftaran->siswa->nama ?? '-' }}</td>
                                            <td class="p-3">{{ $presensi->pendaftaran->siswa->kelas->nama ?? '-' }}</td>
                                            <td class="p-3">
                                                <span class="px-2 py-1 rounded-full text-[10px] font-semibold border
                                                    @if($presensi->status == 'hadir') bg-emerald-100 text-emerald-700 border-emerald-200
                                                    @elseif($presensi->status == 'izin') bg-sky-100 text-sky-700 border-sky-200
                                                    @elseif($presensi->status == 'sakit') bg-amber-100 text-amber-700 border-amber-200
                                                    @else bg-red-100 text-red-700 border-red-200 @endif">
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