<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->foreignId('siswa_id')->nullable()->change();
            $table->foreignId('pembina_id')->nullable()->after('siswa_id')->constrained('pembinas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pembina_id');
            $table->foreignId('siswa_id')->nullable(false)->change();
        });
    }
};