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
        Schema::create('event_participants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_id')->constrained('events')->onUpdate('cascade')->onDelete('cascade'); //Relasi ke table events
            $table->foreignUuid('head_of_family_id')->constrained('head_of_families')->onUpdate('cascade')->onDelete('cascade'); //Relasi ke table head of families
            $table->integer('quantity')->default(0);
            $table->decimal('total_price', 16, 2)->default(0);
            $table->string('payment_status')->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_participants');
    }
};
