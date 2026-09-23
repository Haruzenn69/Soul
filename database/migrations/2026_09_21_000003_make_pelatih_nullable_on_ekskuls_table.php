<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekskuls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pelatih_id');
            $table->foreignId('pelatih_id')->nullable()->after('pembina_id')->constrained('pelatihs')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ekskuls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pelatih_id');
            $table->foreignId('pelatih_id')->after('pembina_id')->constrained('pelatihs')->onDelete('cascade');
        });
    }
};
