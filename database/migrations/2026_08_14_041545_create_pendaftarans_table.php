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
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id_pendaftaran();
            $table->foreignId('nis')->constrained('siswas')->onDelete('cascade'); // Foreign Key    
            $table->foreignId('id_ekskul')->constrained('ekskuls')->onDelete('cascade'); // Foreign Key
            $table->timestamps('tanggal_daftar');
            $table->enum('status', ['pending', 'diterima'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
