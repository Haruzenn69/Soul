<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('ekskuls', 'is_open_recruitment')) {
            Schema::table('ekskuls', function (Blueprint $table) {
                $table->dropColumn('is_open_recruitment');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ekskuls', function (Blueprint $table) {
            $table->boolean('is_open_recruitment')->default(true);
        });
    }
};
