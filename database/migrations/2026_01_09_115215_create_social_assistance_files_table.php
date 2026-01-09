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
            $table->uuid('social_assistance_id'); // Relasi ke table social_assistances
            $table->uuid('file_id'); // Relasi ke table files

            $table->foreignUuid('social_assistance_id')->constrained('social_assistances')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('file_id')->constrained('files')->onUpdate('cascade')->onDelete('cascade');
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
