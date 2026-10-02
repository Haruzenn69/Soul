<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelatihs', function (Blueprint $table) {
            $table->foreignId('pembina_id')->nullable()->after('id')->constrained('pembinas')->nullOnDelete();
            $table->foreignId('ekskul_id')->nullable()->after('pembina_id')->constrained('ekskuls')->nullOnDelete();
            $table->string('email')->nullable()->after('no_hp');
            $table->string('sosmed')->nullable()->after('email');
            $table->text('alamat')->nullable()->after('sosmed');
            $table->string('cv')->nullable()->after('alamat');
            $table->string('sertifikat')->nullable()->after('cv');
            $table->enum('status_verifikasi', ['pending', 'terverifikasi', 'ditolak'])->default('pending')->after('status');
            $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
            $table->timestamp('verified_at')->nullable()->after('catatan_verifikasi');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
        });

        // Data awal yang sudah ada di database diset status_verifikasi = terverifikasi
        DB::table('pelatihs')->whereNull('status_verifikasi')->orWhere('status_verifikasi', 'pending')->update([
            'status_verifikasi' => 'terverifikasi',
        ]);
    }

    public function down(): void
    {
        Schema::table('pelatihs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropConstrainedForeignId('ekskul_id');
            $table->dropConstrainedForeignId('pembina_id');
            $table->dropColumn([
                'email',
                'sosmed',
                'alamat',
                'cv',
                'sertifikat',
                'status_verifikasi',
                'catatan_verifikasi',
                'verified_at',
            ]);
        });
    }
};
