<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participant_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('event_participant_id')->constrained('event_participants')->cascadeOnDelete();
            $table->string('member_type'); // 'head_of_family' or 'family_member'
            $table->foreignUuid('family_member_id')->nullable()->constrained('family_members')->nullOnDelete();
            $table->timestamps();

            $table->index('event_participant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participant_members');
    }
};
