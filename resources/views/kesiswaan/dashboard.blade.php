@extends('layouts.kesiswaan')

@section('title', 'Dashboard Kesiswaan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <h1 class="text-xl font-bold text-slate-900">
                Dashboard Kesiswaan
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                Selamat datang, <span class="font-semibold text-slate-700">{{ auth()->user()->username }}</span>. Kelola akun, ekskul, dan kelas dalam satu panel.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('kesiswaan.users.create') }}"
               class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Akun Baru
            </a>
        </div>
    </div>

    <!-- METRICS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Akun -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Akun</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalUsers }}</h3>
                <p class="text-[11px] font-medium text-blue-600 mt-1">Semua role</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>

        <!-- Card 2: Total Siswa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Total Siswa</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalSiswa }}</h3>
                <p class="text-[11px] font-medium text-emerald-600 mt-1">Termasuk ketua</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <!-- Card 3: Ekskul Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Ekskul Aktif</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalEkskul }}</h3>
                <p class="text-[11px] font-medium text-blue-600 mt-1">{{ $ekskulBuka }} Buka Pendaftaran</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>

        <!-- Card 4: Kelas Terdaftar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase">Kelas Terdaftar</p>
                <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalKelas }}</h3>
                <p class="text-[11px] font-medium text-slate-500 mt-1">Semua tingkat</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- NAVIGATION MENU -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-2">
        <a href="{{ route('kesiswaan.users.index') }}" 
           class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm hover:border-blue-500 hover:shadow-md transition-all block group">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition">Kelola Akun Pengguna</h2>
                <span class="text-xs font-semibold text-blue-600">Akses →</span>
            </div>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                Buat akun, atur peranan, dan reset kata sandi ke default "password".
            </p>
        </a>

        <a href="{{ route('kesiswaan.ekskuls.index') }}" 
           class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm hover:border-blue-500 hover:shadow-md transition-all block group">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition">Kelola Ekskul</h2>
                <span class="text-xs font-semibold text-blue-600">Akses →</span>
            </div>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                Pengaturan data ekstrakurikuler beserta penetapan pembina dan pelatih.
            </p>
        </a>

        <a href="{{ route('kesiswaan.kelas.index') }}" 
           class="bg-white p-5 rounded-2xl border border-slate-200/70 shadow-sm hover:border-blue-500 hover:shadow-md transition-all block group">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition">Kelola Kelas</h2>
                <span class="text-xs font-semibold text-blue-600">Akses →</span>
            </div>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                Manajemen daftar kelas berdasarkan tingkat dan periode tahun ajaran.
            </p>
        </a>
    </div>
</div>
@endsection