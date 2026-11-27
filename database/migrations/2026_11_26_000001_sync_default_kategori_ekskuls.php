<?php

use App\Support\AturanEkskul;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kategori_ekskuls')) {
            return;
        }

        foreach (AturanEkskul::DEFAULT_KATEGORI as $nama) {
            DB::table('kategori_ekskuls')->insertOrIgnore([
                'nama' => $nama,
                'deskripsi' => AturanEkskul::isFallback($nama)
                    ? 'Kategori umum untuk ekskul yang tidak termasuk kategori lain.'
                    : 'Kategori bawaan sistem.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $kategoriLama = DB::table('kategori_ekskuls')->where('nama', 'Lainnya')->first();
        $kategoriFallback = DB::table('kategori_ekskuls')
            ->where('nama', AturanEkskul::KATEGORI_FALLBACK)
            ->first();

        if ($kategoriLama && $kategoriFallback) {
            DB::table('ekskuls')
                ->where('kategori', 'Lainnya')
                ->update(['kategori' => AturanEkskul::KATEGORI_FALLBACK]);

            DB::table('kategori_ekskuls')->where('id', $kategoriLama->id)->delete();
        }
    }

    public function down(): void {}
};
