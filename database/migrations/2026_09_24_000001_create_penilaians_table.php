<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained()->onDelete('cascade');
            $table->string('periode', 20);
            $table->unsignedInteger('total_pertemuan')->default(0);
            $table->unsignedInteger('total_hadir')->default(0);
            $table->unsignedInteger('total_izin')->default(0);
            $table->unsignedInteger('total_sakit')->default(0);
            $table->unsignedInteger('total_alpha')->default(0);
            $table->decimal('persentase_kehadiran', 5, 2)->default(0);
            $table->decimal('nilai_sikap', 5, 2)->default(0);
            $table->decimal('nilai_keaktifan', 5, 2)->default(0);
            $table->decimal('nilai_keterampilan', 5, 2)->default(0);
            $table->decimal('nilai_akhir', 5, 2)->default(0);
            $table->enum('predikat', ['A', 'B', 'C', 'D', 'E'])->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'terkirim'])->default('draft');
            $table->timestamp('dikirim_at')->nullable();
            $table->unsignedBigInteger('dinilai_oleh')->nullable();
            $table->timestamps();

            $table->index(['pendaftaran_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
