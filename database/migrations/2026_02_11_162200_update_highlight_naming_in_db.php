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
        Schema::table('extract_priority_action', function (Blueprint $table) {
            $table->renameColumn('highlight_id', 'extract_id');
        });

        Schema::table('extract_search_term', function (Blueprint $table) {
            $table->renameColumn('highlight_id', 'extract_id');
        });

        Schema::table('extract_statement', function (Blueprint $table) {
            $table->renameColumn('highlight_id', 'extract_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('db', function (Blueprint $table) {
            //
        });
    }
};
