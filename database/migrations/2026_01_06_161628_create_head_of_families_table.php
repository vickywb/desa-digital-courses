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
        Schema::create('head_of_families', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique(); //Relasi ke table users dengan menggunakan user id
            $table->string('profile_picture')->nullable();
            $table->string('identity_number')->unique();
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->string('phone_number')->unique();
            $table->string('occupation');
            $table->enum('marital_status', ['single', 'married', 'widower', 'widow']);
            $table->softDeletes();
            $table->timestamps();

            $table->foreignUuid('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head_of_families');
    }
};
