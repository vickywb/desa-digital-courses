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
        Schema::create('social_assistance_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('social_assistance_id')->constrained('social_assistances')->onUpdate('cascade')->onDelete('cascade'); //Relasi ke table social assistances
            $table->foreignUuid('head_of_family_id')->constrained('head_of_families')->onUpdate('cascade')->onDelete('cascade'); //Relasi ke table head of families
            $table->enum('bank', ['BCA', 'Mandiri', 'BNI', 'BRI']);
            $table->decimal('amount', 16, 2);
            $table->string('account_number')->unique();
            $table->longText('reason')->nullable();
            $table->string('proof');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_assistance_recipients');
    }
};
