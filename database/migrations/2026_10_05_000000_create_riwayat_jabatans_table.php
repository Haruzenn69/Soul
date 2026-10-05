<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ekskul_id')->constrained()->cascadeOnDelete();
            $table->string('jabatan', 30)->default('ketua');
            $table->date('mulai');
            $table->date('selesai')->nullable();
            $table->string('alasan_selesai')->nullable();
            $table->timestamps();

            $table->index(['ekskul_id', 'jabatan', 'selesai'], 'riwayat_jabatans_arsip_index');
            $table->index(['siswa_id', 'ekskul_id'], 'riwayat_jabatans_siswa_ekskul_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_jabatans');
    }
};
