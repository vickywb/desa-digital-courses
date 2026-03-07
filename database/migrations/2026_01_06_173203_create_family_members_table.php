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
        Schema::create('family_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('head_of_family_id')->constrained('head_of_families')->onUpdate('cascade')->onDelete('cascade'); //Relasi ke table head of families
            $table->foreignUuid('file_id')->nullable()->constrained('files')->onUpdate('cascade')->onDelete('set null');
            $table->string('full_name');
            $table->string('email')->unique()->nullable();
            $table->string('identity_number')->unique();
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth');
            $table->string('phone_number')->nullable();
            $table->string('occupation');
            $table->enum('marital_status', ['single', 'married']);
            $table->enum('relation', ['child', 'wife']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
