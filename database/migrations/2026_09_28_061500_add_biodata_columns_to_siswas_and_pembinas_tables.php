<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('tempat_lahir', 100)->nullable()->after('nama');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama', 50)->nullable()->after('tanggal_lahir');
            $table->string('angkatan', 20)->nullable()->after('kelas_id');
            $table->string('email')->nullable()->after('jenis_kelamin');
            $table->string('no_telp', 25)->nullable()->after('email');
            $table->text('alamat')->nullable()->after('no_telp');
            $table->string('medsos')->nullable()->after('alamat');
        });

        Schema::table('pembinas', function (Blueprint $table) {
            $table->string('tempat_lahir', 100)->nullable()->after('nama');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->string('agama', 50)->nullable()->after('tanggal_lahir');
            $table->string('email')->nullable()->after('jenis_kelamin');
            $table->string('no_telp', 25)->nullable()->after('email');
            $table->text('alamat')->nullable()->after('no_telp');
            $table->string('medsos')->nullable()->after('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembinas', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir',
                'tanggal_lahir',
                'agama',
                'email',
                'no_telp',
                'alamat',
                'medsos',
            ]);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir',
                'tanggal_lahir',
                'agama',
                'angkatan',
                'email',
                'no_telp',
                'alamat',
                'medsos',
            ]);
        });
    }
};
