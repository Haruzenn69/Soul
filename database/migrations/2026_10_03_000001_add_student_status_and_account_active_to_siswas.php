<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'menunggu_penempatan', 'nonaktif'])->default('aktif')->after('angkatan');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role');
        });

        Schema::create('siswa_riwayat_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('tahun_ajaran', 20);
            $table->string('kelas_asal', 100);
            $table->string('kelas_tujuan', 100)->nullable();
            $table->enum('jenis', ['kenaikan', 'penempatan', 'kelulusan']);
            $table->timestamp('diproses_pada');
            $table->timestamps();

            $table->index(['siswa_id', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_riwayat_kelas');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
