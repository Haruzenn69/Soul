<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'nonaktif', 'peringatan', 'keluar'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftarans', function (Blueprint $table) {
            $table->enum('status', ['pending', 'diterima', 'ditolak', 'nonaktif', 'peringatan'])->default('pending')->change();
        });
    }
};