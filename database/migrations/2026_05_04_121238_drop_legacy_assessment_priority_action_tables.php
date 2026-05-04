<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('assessment_priority_action_policy');
        Schema::dropIfExists('assessment_priority_action');
    }

    public function down(): void
    {
        Schema::create('assessment_priority_action', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->string('priority_action_id');
            $table->timestamps();
        });

        Schema::create('assessment_priority_action_policy', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id');
            $table->string('assessment_priority_action_id');
            $table->timestamps();
        });
    }
};
