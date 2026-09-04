<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_bulanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekskul_id')->constrained()->onDelete('cascade');
            $table->string('bulan', 7);
            $table->text('materi_kegiatan')->nullable();
            $table->text('tujuan')->nullable();
            $table->text('kehadiran')->nullable();
            $table->text('evaluasi_keberhasilan')->nullable();
            $table->text('evaluasi_kendala')->nullable();
            $table->text('evaluasi_solusi')->nullable();
            $table->text('ringkasan')->nullable();
            $table->string('dokumentasi')->nullable();
            $table->json('dokumentasi_kegiatan')->nullable();
            $table->enum('status', ['draft', 'disetujui', 'ditolak'])->default('draft');
            $table->text('catatan_pembina')->nullable();
            $table->string('file_laporan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_bulanans');
    }
};
