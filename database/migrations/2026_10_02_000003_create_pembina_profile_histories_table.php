<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembina_profile_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembina_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('field', 50);
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();

            $table->index(['pembina_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembina_profile_histories');
    }
};
