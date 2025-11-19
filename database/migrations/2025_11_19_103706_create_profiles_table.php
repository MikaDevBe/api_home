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
        Schema::create('profiles', function (Blueprint $table) {
          $table->id('profile_id');
          $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');
          $table->string('mail')->unique()->nullable();
          $table->string('phone', 20)->nullable();
          $table->text('address')->nullable();
          $table->string('image')->nullable();
          $table->string('town')->nullable();
          $table->string('postal_code', 10)->nullable();
          $table->string('country')->nullable();
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
