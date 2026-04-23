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
        Schema::disableForeignKeyConstraints();

        Schema::table('extracts', function (Blueprint $table) {
            $table->renameColumn('type_id', 'score_id');
        });

        Schema::table('extracts', function (Blueprint $table) {
            $table->foreign('score_id')->references('id')->on('scores');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('extracts', function (Blueprint $table) {
            $table->renameColumn('score_id', 'type_id');
        });

        Schema::table('extracts', function (Blueprint $table) {
            $table->foreign('type_id')->references('id')->on('scores');
        });

        Schema::enableForeignKeyConstraints();
    }
};
