<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_keluars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained()->onDelete('cascade');
            $table->foreignId('ekskul_id')->constrained()->onDelete('cascade');
            $table->text('alasan');
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->date('tanggal_pengajuan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_keluars');
    }
};
