<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_keluars', function (Blueprint $table) {
            $table->id_pengajuan_keluar();
            $table->foreignId('nis')->constrained('siswas')->onDelete('cascade'); // Foreign Key
            $table->foreignId('id_ekskul')->constrained('ekskuls')->onDelete('cascade'); // Foreign Key
            $table->text('alasan');
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->timestamps('tanggal_pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_keluars');
    }
};
