<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kategori_ekskuls', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Seed default & existing categories from ekskuls table
        $defaults = ['Wajib', 'Olahraga', 'Seni', 'Bahasa', 'Bela Diri', 'Lainnya'];
        $existing = [];

        if (Schema::hasTable('ekskuls') && Schema::hasColumn('ekskuls', 'kategori')) {
            $existing = DB::table('ekskuls')
                ->whereNotNull('kategori')
                ->where('kategori', '!=', '')
                ->distinct()
                ->pluck('kategori')
                ->all();
        }

        $all = array_values(array_unique(array_filter(array_map('trim', array_merge($defaults, $existing)))));

        foreach ($all as $k) {
            if ($k !== '') {
                DB::table('kategori_ekskuls')->insertOrIgnore([
                    'nama' => $k,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_ekskuls');
    }
};
