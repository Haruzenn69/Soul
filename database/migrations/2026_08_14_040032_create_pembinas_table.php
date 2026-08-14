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
        Schema::create('pembinas', function (Blueprint $table) {
            $table->string('nip')->primary(); // Primary Key
            $table->foreign('username')->references('username')->on('users')->onDelete('cascade'); // Foreign Key
            $table->string('nama');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembinas');
    }
};
