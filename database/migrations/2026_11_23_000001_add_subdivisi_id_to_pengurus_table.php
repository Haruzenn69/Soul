<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengurus', function (Blueprint $table) {
            $table->foreignId('subdivisi_id')
                ->nullable()
                ->after('ekskul_id')
                ->constrained('ekskuls')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengurus', function (Blueprint $table) {
            $table->dropForeign(['subdivisi_id']);
            $table->dropColumn('subdivisi_id');
        });
    }
};
