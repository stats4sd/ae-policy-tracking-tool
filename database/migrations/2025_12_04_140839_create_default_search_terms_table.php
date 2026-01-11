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
        Schema::create('default_search_terms', function (Blueprint $table) {
            $table->id();
            $table->string('priority_action_id');
            // TODO: add _and_ support
            $table->string('phrase');
            $table->timestamps();

            $table->foreign('priority_action_id')->references('id')->on('priority_actions');
        });

        Schema::table('search_terms', function (Blueprint $table) {
            $table->foreignId('assessment_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_search_terms');
    }
};
