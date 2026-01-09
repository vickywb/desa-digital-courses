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
        Schema::create('profile_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('profile_id'); // Relasi ke table profiles
            $table->uuid('file_id'); // Relasi ke table files

            $table->foreignUuid('profile_id')->constrained('profiles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('file_id')->constrained('files')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_files');
    }
};
