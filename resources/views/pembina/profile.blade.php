@extends('pembina.layout')
@section('title', 'Profile Pembina')

@section('content')
    <div>
        <h1 class="text-xl font-bold text-slate-900">Profile Saya</h1>
        <p class="text-xs text-slate-400 mt-0.5">Informasi akun dan ekskul yang anda bina</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- KOLOM KIRI: Foto & Info Singkat -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm text-center">
                <div class="w-28 h-28 mx-auto rounded-full bg-blue-100 border-4 border-blue-600 flex items-center justify-center">
                    <span class="text-3xl font-bold text-blue-600 uppercase">{{ strtoupper(substr($pembina->nama ?? 'P', 0, 2)) }}</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mt-4">{{ $pembina->nama ?? '-' }}</h3>
                <p class="text-xs text-slate-400">Pembina Ekskul</p>

                <div class="mt-4 pt-4 border-t border-slate-200/60">
                    <p class="text-[10px] text-slate-400">Bergabung sejak</p>
                    <p class="text-xs font-medium text-slate-700">{{ $pembina->created_at ? \Carbon\Carbon::parse($pembina->created_at)->isoFormat('D MMMM Y') : '-' }}</p>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Diri -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 mb-4">Data Diri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Nama Lengkap</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">{{ $pembina->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">NIP</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">{{ $pembina->nip ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Jenis Kelamin</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">{{ ucfirst($pembina->jenis_kelamin ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Username</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">{{ auth()->user()->username ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Email</p>
                        <p class="text-sm font-medium text-slate-800 mt-1">{{ auth()->user()->email ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Ekskul yang Dibina -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/70 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 mb-4">Ekskul yang Dibina</h2>
                @php
                    $ekskuls = $pembina ? $pembina->ekskuls()->get() : collect();
                @endphp
                @if($ekskuls->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($ekskuls as $ekskul)
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/60">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-slate-900">{{ $ekskul->nama_ekskul }}</h4>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $ekskul->is_open_recruitment ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $ekskul->is_open_recruitment ? 'Open' : 'Tutup' }}
                                    </span>
                                </div>
                                @if($ekskul->jadwal)
                                    <p class="text-[11px] text-slate-500 mt-2">Jadwal: {{ $ekskul->jadwal }}</p>
                                @endif
                                <p class="text-[10px] text-slate-400 mt-1">Anggota aktif: {{ $ekskul->pendaftarans()->where('status', 'diterima')->count() }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-slate-400">
                        <p class="text-sm">Belum ada ekskul yang anda bina.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
