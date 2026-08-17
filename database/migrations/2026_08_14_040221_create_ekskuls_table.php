<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekskuls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembina_id')->constrained('pembinas')->onDelete('cascade');
            $table->foreignId('pelatih_id')->constrained()->onDelete('cascade');
            $table->string('nama_ekskul');
            $table->text('deskripsi')->nullable();
            $table->text('jadwal')->nullable();
            $table->boolean('is_open_recruitment')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekskuls');
    }
};
