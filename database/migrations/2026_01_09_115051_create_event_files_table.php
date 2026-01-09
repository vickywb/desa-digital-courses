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
        Schema::create('event_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_id'); // Relasi ke table events
            $table->uuid('file_id'); // Relasi ke table files

            $table->foreignUuid('event_id')->constrained('events')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('file_id')->constrained('files')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_files');
    }
};
