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
        Schema::table('policy_statement', function (Blueprint $table) {
            $table->dropForeign(['policy_id']);
            $table->foreign('policy_id')->references('id')->on('policies')->cascadeOnDelete();

            $table->dropForeign(['statement_id']);
            $table->foreign('statement_id')->references('id')->on('statements')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
