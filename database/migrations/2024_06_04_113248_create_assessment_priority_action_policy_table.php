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
        Schema::create('assessment_priority_action_policy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('policy_id');
            $table->string('assessment_priority_action_id')->constrained('assessment_priority_action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_priority_action_policy');
    }
};
