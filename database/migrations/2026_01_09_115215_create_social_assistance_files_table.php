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
        Schema::create('social_assistance_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('social_assistance_id')->constrained('social_assistances')->onUpdate('cascade')->onDelete('cascade'); //Relasi ke table social assistances
            $table->foreignUuid('file_id')->constrained('files')->onUpdate('cascade')->onDelete('cascade'); //Relasi ke table files
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_assistance_files');
    }
};
