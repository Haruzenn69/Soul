<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->text('tujuan')->nullable()->after('dokumentasi');
            $table->text('kehadiran')->nullable()->after('tujuan');
            $table->text('evaluasi_keberhasilan')->nullable()->after('kehadiran');
            $table->text('evaluasi_kendala')->nullable()->after('evaluasi_keberhasilan');
            $table->text('evaluasi_solusi')->nullable()->after('evaluasi_kendala');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->dropColumn(['tujuan', 'kehadiran', 'evaluasi_keberhasilan', 'evaluasi_kendala', 'evaluasi_solusi']);
        });
    }
};
