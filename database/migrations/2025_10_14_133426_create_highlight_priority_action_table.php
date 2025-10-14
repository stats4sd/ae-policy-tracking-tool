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
        Schema::create('highlight_priority_action', function (Blueprint $table) {
            $table->id();
            $table->foreignId('highlight_id');
            $table->string('priority_action_id')->constrained('priority_actions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('highlight_priority_action');
    }
};
