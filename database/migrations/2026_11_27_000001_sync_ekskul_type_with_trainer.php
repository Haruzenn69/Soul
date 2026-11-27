<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('ekskuls')
            ->whereNull('parent_id')
            ->update([
                'tipe' => DB::raw("CASE WHEN pelatih_id IS NULL THEN 'komunitas' ELSE 'ekskul' END"),
            ]);

        DB::table('ekskuls')
            ->whereNotNull('parent_id')
            ->orderBy('id')
            ->get(['id', 'parent_id'])
            ->each(function (object $subdivisi): void {
                $tipeInduk = DB::table('ekskuls')->where('id', $subdivisi->parent_id)->value('tipe');
                if ($tipeInduk !== null) {
                    DB::table('ekskuls')->where('id', $subdivisi->id)->update(['tipe' => $tipeInduk]);
                }
            });
    }

    public function down(): void
    {
        // Tipe sebelumnya tidak dapat dipulihkan karena nilainya akan mengikuti pelatih.
    }
};
