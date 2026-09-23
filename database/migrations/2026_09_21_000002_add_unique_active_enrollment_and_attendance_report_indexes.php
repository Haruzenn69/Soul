<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->unique(['kegiatan_id', 'pendaftaran_id'], 'presensis_kegiatan_pendaftaran_unique');
        });

        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->unique(['ekskul_id', 'bulan'], 'laporan_bulanans_ekskul_bulan_unique');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->dropUnique('laporan_bulanans_ekskul_bulan_unique');
        });

        Schema::table('presensis', function (Blueprint $table) {
            $table->dropUnique('presensis_kegiatan_pendaftaran_unique');
        });

    }
};
