<?php

use App\Services\PendaftaranDedupService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        (new PendaftaranDedupService())->run();

        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->unique(['siswa_id', 'ekskul_id'], 'pendaftarans_siswa_ekskul_unique');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->dropUnique('pendaftarans_siswa_ekskul_unique');
        });
    }
};
