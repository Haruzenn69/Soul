<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->foreignId('pengajuan_keluar_id')->nullable()->after('pendaftaran_id')->constrained('pengajuan_keluars')->onDelete('cascade');
            $table->foreignId('laporan_bulanan_id')->nullable()->after('pengajuan_keluar_id')->constrained('laporan_bulanans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pengajuan_keluar_id');
            $table->dropConstrainedForeignId('laporan_bulanan_id');
        });
    }
};