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
        Schema::create('siswas', function (Blueprint $table) {
            $table->string('nis')->primary(); // Primary Key
            $table->foreign('username')->references('username')->on('users')->onDelete('cascade'); // Foreign Key
            $table->string('nama');
            $table->string('kelas');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->enum('jabatan', ['siswa', 'anggota', 'ketua'])->default('siswa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
