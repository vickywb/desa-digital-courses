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
        Schema::create('developments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('file_id')->nullable()->constrained('files')->onUpdate('cascade')->onDelete('set null');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('person_in_charge');
            $table->decimal('amount', 16, 2)->default(0);
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->enum('status', ['ongoing', 'completed', 'planned'])->default('planned');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developments');
    }
};
