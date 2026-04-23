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
        Schema::table('extracts', function (Blueprint $table) {
            $table->dropForeign('highlights_type_id_foreign');
            $table->renameColumn('type_id', 'score_id');
            $table->foreign('score_id')->references('id')->on('scores');
        });
    }

    public function down(): void
    {
        Schema::table('extracts', function (Blueprint $table) {
            $table->dropForeign(['score_id']);
            $table->renameColumn('score_id', 'type_id');
            $table->foreign('type_id')->references('id')->on('scores');
        });
    }
};
