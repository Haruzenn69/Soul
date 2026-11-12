<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('siswa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ekskul_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal_daftar');
            $table->string('status', 30);
            $table->text('alasan')->nullable();
            $table->timestamp('dicatat_pada');
            $table->timestamps();

            $table->index(['siswa_id', 'ekskul_id', 'dicatat_pada'], 'riwayat_pendaftaran_siswa_ekskul_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_pendaftarans');
    }
};
