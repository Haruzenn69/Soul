@extends('layouts.kesiswaan')
@section('title', 'Profile Kesiswaan')

@section('content')
    <div class="animate-fade-up">
        <h1 class="text-xl font-extrabold text-slate-900">Profile Saya</h1>
        <p class="text-xs text-slate-400 mt-0.5">Informasi akun Staff Kesiswaan</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold rounded-xl animate-fade-up">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- KOLOM KIRI: Foto & Info Singkat -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 text-center animate-fade-up" style="animation-delay: .1s">
                <div class="w-28 h-28 mx-auto rounded-full bg-gradient-to-br from-sky-100 to-blue-100 border-4 border-sky-300 flex items-center justify-center shadow-lg shadow-sky-200">
                    <span class="text-3xl font-extrabold text-sky-600 uppercase">{{ strtoupper(substr($user->username ?? 'K', 0, 1)) }}</span>
                </div>
                <h3 class="text-sm font-extrabold text-slate-900 mt-4">{{ $user->username ?? '-' }}</h3>
                <p class="text-xs text-slate-400">{{ $user->role === 'admin' ? 'Admin' : 'Staf Kesiswaan' }}</p>

                <div class="mt-4 pt-4 border-t border-sky-50">
                    <p class="text-[10px] text-slate-400">Bergabung sejak</p>
                    <p class="text-xs font-semibold text-slate-700 mt-0.5">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->isoFormat('D MMMM Y') : '-' }}</p>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Diri -->
            <div class="bg-white p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .15s">
                <h2 class="text-sm font-extrabold text-slate-900 mb-4">Data Diri</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-xl border border-sky-100">
                        <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Username</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">{{ $user->username ?? '-' }}</p>
                    </div>
                    <div class="p-3.5 bg-gradient-to-r from-amber-50 to-white rounded-xl border border-amber-100">
                        <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Role</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">{{ $user->role === 'admin' ? 'Admin' : 'Staf Kesiswaan' }}</p>
                    </div>
                    <div class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-xl border border-sky-100">
                        <p class="text-[10px] font-semibold text-slate-400 tracking-wider uppercase">Email</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">{{ $user->email ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection