@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

@section('search')
    <form method="GET" action="{{ route('siswa.katalog') }}" class="relative w-full max-w-md hidden sm:block">
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </span>
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari ekskul, siswa, kegiatan..." class="w-full pl-10 pr-4 py-2 bg-sky-50/70 border border-sky-100 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition-all">
    </form>
@endsection

@section('content')

            <!-- NOTIFIKASI STATUS PENDAFTARAN -->
            @php
                $pendaftaranStatus = null;
                $pendaftaranMessage = '';
                $pendaftaranColor = '';
                $pendaftaranIcon = '';

                if ($siswa) {
                    $pending = $siswa->pendaftarans()->where('status', 'pending')->first();
                    $diterima = $siswa->pendaftarans()->whereIn('status', ['diterima', 'peringatan'])->first();
                    $ditolak = $siswa->pendaftarans()->where('status', 'ditolak')->first();

                    if ($diterima) {
                        $pendaftaranStatus = 'diterima';
                        $pendaftaranMessage = 'Kamu sudah terdaftar di ekskul ' . $diterima->ekskul->nama_ekskul . '.';
                        $pendaftaranColor = 'from-emerald-500 to-teal-600';
                        $pendaftaranSoft = 'bg-emerald-50 border-emerald-200 text-emerald-800';
                        $pendaftaranIcon = '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    } elseif ($pending) {
                        $pendaftaranStatus = 'pending';
                        $pendaftaranMessage = 'Kamu sudah mengajukan pendaftaran ke ekskul ' . $pending->ekskul->nama_ekskul . '.';
                        $pendaftaranColor = 'from-amber-400 to-yellow-500';
                        $pendaftaranSoft = 'bg-amber-50 border-amber-200 text-amber-800';
                        $pendaftaranIcon = '<svg class="w-5 h-5 text-amber-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                    } elseif ($ditolak) {
                        $pendaftaranStatus = 'ditolak';
                        $pendaftaranMessage = 'Pendaftaran kamu ke ekskul ' . $ditolak->ekskul->nama_ekskul . ' ditolak oleh ketua ekskul.';
                        $pendaftaranColor = 'from-rose-500 to-red-600';
                        $pendaftaranSoft = 'bg-rose-50 border-rose-200 text-rose-800';
                        $pendaftaranIcon = '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
                    }
                }
            @endphp

            

        
    });
</script>
@endpush
