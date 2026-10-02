<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi_pelatihs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained()->onDelete('cascade');
            $table->foreignId('pelatih_id')->constrained('pelatihs')->onDelete('cascade');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])->default('hadir');
            $table->timestamps();

            $table->unique(['kegiatan_id', 'pelatih_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_pelatihs');
    }
};