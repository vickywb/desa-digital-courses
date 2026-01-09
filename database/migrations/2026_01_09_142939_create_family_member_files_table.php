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
        Schema::create('family_member_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('family_member_id'); // Relasi ke table family_members
            $table->uuid('file_id'); // Relasi ke table files

            $table->foreignUuid('family_member_id')->constrained('family_members')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignUuid('file_id')->constrained('files')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_member_files');
    }
};
