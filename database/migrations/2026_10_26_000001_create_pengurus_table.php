<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekskul_id')->constrained('ekskuls')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('jabatan', ['ketua', 'wakil_ketua', 'sekretaris', 'bendahara']);
            $table->enum('status', ['aktif', 'menunggu', 'ditolak', 'selesai'])->default('menunggu');
            $table->string('diusulkan_oleh', 20)->default('pembina'); // 'pembina' atau 'ketua'
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['ekskul_id', 'jabatan', 'status'], 'pengurus_ekskul_jabatan_status_idx');
            $table->index(['siswa_id', 'ekskul_id'], 'pengurus_siswa_ekskul_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengurus');
    }
};
