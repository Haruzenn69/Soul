<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->string('jurusan')->nullable()->after('nama');
            $table->string('rombel')->nullable()->after('jurusan');
        });

        $jurusanMap = config('kelas.jurusan', []);
        $rows = DB::table('kelas')->select('id', 'nama', 'tingkat')->get();

        foreach ($rows as $row) {
            $parts = preg_split('/\s+/', trim((string) $row->nama));
            if (count($parts) < 3) {
                continue;
            }

            $rombel = end($parts);
            $label = $parts[count($parts) - 2];
            $kode = null;

            foreach ($jurusanMap as $code => $labels) {
                if (isset($labels[$row->tingkat]) && $labels[$row->tingkat] === $label) {
                    $kode = $code;
                    break;
                }
            }

            if ($kode === null || ! ctype_digit($rombel)) {
                continue;
            }

            DB::table('kelas')->where('id', $row->id)->update([
                'jurusan' => $kode,
                'rombel' => $rombel,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn(['jurusan', 'rombel']);
        });
    }
};
