<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama');
            $table->foreignId('kelas_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->enum('jabatan', ['siswa', 'anggota', 'ketua'])->default('siswa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
