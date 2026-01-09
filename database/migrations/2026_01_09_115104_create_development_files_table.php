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
        Schema::create('development_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('development_id'); // Relasi ke table developments
            $table->uuid('file_id'); // Relasi ke table files

            $table->foreignUuid('development_id')->constrained('developments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('file_id')->constrained('files')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('development_files');
    }
};
