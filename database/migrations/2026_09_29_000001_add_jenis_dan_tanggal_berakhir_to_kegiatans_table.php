<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->string('jenis_kegiatan', 20)->nullable()->index()->after('materi');
            $table->date('tanggal_berakhir')->nullable()->after('tanggal_kegiatan');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropIndex(['jenis_kegiatan']);
            $table->dropColumn(['jenis_kegiatan', 'tanggal_berakhir']);
        });
    }
};