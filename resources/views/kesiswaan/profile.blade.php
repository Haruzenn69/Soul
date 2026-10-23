@extends('layouts.kesiswaan')
@section('title', 'Profile Kesiswaan')

@section('content')
    <div class="animate-fade-up">
        <h1 class="text-xl md:text-2xl font-extrabold text-slate-900">Profile Saya</h1>
        <p class="text-xs text-slate-400 mt-0.5">Informasi akun Staff Kesiswaan</p>
    </div>



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
                    <p class="text-xs text-slate-400">Bergabung sejak</p>
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
                        <p class="text-xs font-semibold text-slate-400 tracking-wider uppercase">Username</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">{{ $user->username ?? '-' }}</p>
                    </div>
                    <div class="p-3.5 bg-gradient-to-r from-amber-50 to-white rounded-xl border border-amber-100">
                        <p class="text-xs font-semibold text-slate-400 tracking-wider uppercase">Role</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">{{ $user->role === 'admin' ? 'Admin' : 'Staf Kesiswaan' }}</p>
                    </div>
                    <div class="p-3.5 bg-gradient-to-r from-sky-50 to-white rounded-xl border border-sky-100">
                        <p class="text-xs font-semibold text-slate-400 tracking-wider uppercase">Email</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">{{ $user->email ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- UBAH PASSWORD -->
            <div class="bg-white p-6 rounded-2xl border border-sky-100 shadow-lg shadow-sky-100/60 animate-fade-up" style="animation-delay: .2s">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Ubah Password</h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Perbarui password akun Anda untuk menjaga keamanan</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="kesiswaan_current_password" class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="kesiswaan_current_password" name="current_password" required autocomplete="current-password" placeholder="Masukkan password saat ini"
                               class="w-full px-4 py-2.5 bg-sky-50/60 border {{ $errors->updatePassword->has('current_password') ? 'border-rose-300' : 'border-sky-100' }} rounded-xl text-xs font-semibold focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                        @if($errors->updatePassword->has('current_password'))
                            <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $errors->updatePassword->first('current_password') }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="kesiswaan_password" class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">
                                Password Baru <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" id="kesiswaan_password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter"
                                   class="w-full px-4 py-2.5 bg-sky-50/60 border {{ $errors->updatePassword->has('password') ? 'border-rose-300' : 'border-sky-100' }} rounded-xl text-xs font-semibold focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                            @if($errors->updatePassword->has('password'))
                                <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $errors->updatePassword->first('password') }}</p>
                            @else
                                <p class="text-[10px] text-slate-400 mt-1">Minimal 8 karakter.</p>
                            @endif
                        </div>

                        <div>
                            <label for="kesiswaan_password_confirmation" class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1.5">
                                Konfirmasi Password Baru <span class="text-rose-500">*</span>
                            </label>
                            <input type="password" id="kesiswaan_password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password baru"
                                   class="w-full px-4 py-2.5 bg-sky-50/60 border {{ $errors->updatePassword->has('password_confirmation') ? 'border-rose-300' : 'border-sky-100' }} rounded-xl text-xs font-semibold focus:outline-none focus:bg-white focus:border-sky-400 focus:ring-4 focus:ring-sky-100 transition">
                            @if($errors->updatePassword->has('password_confirmation'))
                                <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white text-xs font-bold rounded-xl shadow-md shadow-sky-200 transition hover:-translate-y-0.5">
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
