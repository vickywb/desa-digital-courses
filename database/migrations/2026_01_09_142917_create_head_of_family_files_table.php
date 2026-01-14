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
        Schema::create('head_of_family_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('head_of_family_id')->constrained('head_of_families')->onUpdate('cascade')->onDelete('cascade'); //Relasi ke table head_of_families
            $table->foreignUuid('file_id')->constrained('files')->onUpdate('cascade')->onDelete('cascade'); //Relasi ke table files
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('head_of_family_files');
    }
};
