<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->enum('status', ['nonaktif', 'aktif', 'historis'])->default('nonaktif')->after('nama');
        });

        // Migrate existing data
        DB::table('tahun_ajarans')->where('is_active', true)->update(['status' => 'aktif']);
        DB::table('tahun_ajarans')->where('is_active', false)->update(['status' => 'nonaktif']);

        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('nama');
        });

        DB::table('tahun_ajarans')->where('status', 'aktif')->update(['is_active' => true]);

        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
