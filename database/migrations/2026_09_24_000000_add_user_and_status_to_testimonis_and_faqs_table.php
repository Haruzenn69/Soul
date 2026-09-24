<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonis', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('ekskul_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('quote');
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('ekskul_id');
            $table->enum('status', ['pending', 'answered'])->default('pending')->after('jawaban');
            $table->text('jawaban')->nullable()->change();
        });

        DB::table('testimonis')->update(['status' => 'approved']);
        DB::table('faqs')->update(['status' => 'answered']);
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'status']);
        });

        Schema::table('testimonis', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'status']);
        });
    }
};