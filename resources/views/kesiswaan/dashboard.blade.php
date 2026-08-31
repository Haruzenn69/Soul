@extends('layouts.kesiswaan')

@section('title', 'Dashboard Kesiswaan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold text-theme-dark tracking-tight">
                Dashboard Kesiswaan
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Selamat datang, <span class="font-semibold text-gray-700">{{ auth()->user()->username }}</span>. Kelola akun, ekskul, dan kelas dalam satu panel.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('kesiswaan.users.create') }}"
               class="inline-flex items-center justify-center px-4 py-2.5 bg-theme-blue hover:bg-theme-darkBlue text-white font-semibold text-xs rounded-lg shadow-sm transition">
                + Buat Akun Baru
            </a>
            <!-- ❌ TOMBOL LOGOUT DI SINI SUDAH DIHAPUS -->
        </div>
    </div>

    <!-- METRICS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Akun</p>

            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-3xl font-bold text-theme-dark">{{ $totalUsers }}</span>
                <span class="text-xs font-medium text-gray-400">Semua role</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Siswa</p>

            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-3xl font-bold text-theme-dark">{{ $totalSiswa }}</span>
                <span class="text-xs font-medium text-emerald-600">Termasuk ketua</span>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ekskul Aktif</p>

            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-3xl font-bold text-theme-dark">{{ $totalEkskul }}</span>
                <span class="text-xs font-medium text-theme-blue">{{ $ekskulBuka }} Buka Pendaftaran</span>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas Terdaftar</p>

            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-3xl font-bold text-theme-dark">{{ $totalKelas }}</span>
                <span class="text-xs font-medium text-gray-400">Semua tingkat</span>
            </div>
        </div>
    </div>

    <!-- NAVIGATION MENU -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
        <a href="{{ route('kesiswaan.users.index') }}" 
           class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-theme-blue transition block group">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-theme-dark group-hover:text-theme-blue transition">Kelola Akun Pengguna</h2>
                <span class="text-xs font-semibold text-theme-blue">Akses &rarr;</span>
            </div>
            <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                Buat akun, atur peranan, dan reset kata sandi ke default "password".
            </p>
        </a>

        <a href="{{ route('kesiswaan.ekskuls.index') }}" 
           class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-theme-blue transition block group">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-theme-dark group-hover:text-theme-blue transition">Kelola Ekskul</h2>
                <span class="text-xs font-semibold text-theme-blue">Akses &rarr;</span>
            </div>
            <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                Pengaturan data ekstrakurikuler beserta penetapan pembina dan pelatih.
            </p>
        </a>

        <a href="{{ route('kesiswaan.kelas.index') }}" 
           class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm hover:border-theme-blue transition block group">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-theme-dark group-hover:text-theme-blue transition">Kelola Kelas</h2>
                <span class="text-xs font-semibold text-theme-blue">Akses &rarr;</span>
            </div>
            <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                Manajemen daftar kelas berdasarkan tingkat dan periode tahun ajaran.
            </p>
        </a>
    </div>
</div>
@endsection