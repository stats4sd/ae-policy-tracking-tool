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
        Schema::rename('highlights', 'extracts');
        Schema::rename('highlight_search_term', 'extract_search_term');
        Schema::rename('highlight_priority_action', 'extract_priority_action');
        Schema::rename('highlight_statement', 'extract_statement');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extracts', function (Blueprint $table) {
            //
        });
    }
};
