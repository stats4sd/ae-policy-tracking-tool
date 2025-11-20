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
        Schema::create('highlight_search_term', function (Blueprint $table) {
            $table->id();
            $table->foreignId('highlight_id')->constrained('highlights');
            $table->foreignId('search_term_id')->constrained('search_terms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('highlight_search_term');
    }
};
