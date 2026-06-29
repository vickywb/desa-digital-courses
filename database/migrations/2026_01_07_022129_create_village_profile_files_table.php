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
        Schema::create('village_profile_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('village_profile_id')->constrained('village_profiles')->onUpdate('cascade')->onDelete('cascade'); // Relasi ke table profiles
            $table->foreignUuid('file_id')->constrained('files')->onUpdate('cascade')->onDelete('cascade'); // Relasi ke table files
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('village_profile_files');
    }
};
