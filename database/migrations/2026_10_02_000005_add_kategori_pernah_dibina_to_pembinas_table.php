<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembinas', function (Blueprint $table) {
            $table->string('kategori_pernah_dibina')->nullable()->after('foto');
        });
    }

    public function down(): void
    {
        Schema::table('pembinas', function (Blueprint $table) {
            $table->dropColumn('kategori_pernah_dibina');
        });
    }
};
