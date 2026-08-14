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
        Schema::create('ekskuls', function (Blueprint $table) {
            $table->id_ekskul();
            $table->foreignId('nip')->constrained('pembinas')->onDelete('cascade'); // Foreign Key
            $table->foreignId('id_pelatih')->constrained('pelatihs')->onDelete('cascade'); // Foreign Key
            $table->string('nama_ekskul');
            $table->text('deskripsi')->nullable();
            $table->text('jadwal')->nullable();
            $table->boolean('is_open_recruitment')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekskuls');
    }
};
