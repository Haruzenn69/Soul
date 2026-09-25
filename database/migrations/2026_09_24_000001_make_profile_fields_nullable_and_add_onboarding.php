<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->timestamp('onboarding_completed_at')->nullable()->after('role');
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nama')->nullable()->change();
            $table->foreignId('kelas_id')->nullable()->change();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable()->change();
        });

        Schema::table('pembinas', function (Blueprint $table) {
            $table->string('nama')->nullable()->change();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pembinas', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable(false)->change();
            $table->string('nama')->nullable(false)->change();
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable(false)->change();
            $table->foreignId('kelas_id')->nullable(false)->change();
            $table->string('nama')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('onboarding_completed_at');
            $table->string('email')->nullable(false)->change();
        });
    }
};
