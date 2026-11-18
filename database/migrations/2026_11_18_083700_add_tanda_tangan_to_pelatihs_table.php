<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelatihs', function (Blueprint $table) {
            $table->string('tanda_tangan')->nullable()->after('sertifikat');
        });
    }

    public function down(): void
    {
        Schema::table('pelatihs', function (Blueprint $table) {
            $table->dropColumn('tanda_tangan');
        });
    }
};
