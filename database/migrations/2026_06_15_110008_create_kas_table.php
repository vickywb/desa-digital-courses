<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('village_profile_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_balance', 16, 2)->default(0);
            $table->decimal('monthly_expense', 16, 2)->default(0);
            $table->decimal('iuran_kebersihan', 16, 2)->default(0);
            $table->decimal('iuran_keamanan', 16, 2)->default(0);
            $table->decimal('iuran_makam', 16, 2)->default(0);
            $table->decimal('expense_keamanan', 16, 2)->default(0);
            $table->decimal('expense_kebersihan', 16, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas');
    }
};
