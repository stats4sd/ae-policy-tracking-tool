<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ae_principle_recommendation', function (Blueprint $table) {
            $table->foreign('ae_principle_id')->references('id')->on('ae_principles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('recommendation_id')->references('id')->on('recommendations')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('ae_principle_statement', function (Blueprint $table) {
            $table->foreign('ae_principle_id')->references('id')->on('ae_principles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('statement_id')->references('id')->on('statements')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('extract_priority_action', function (Blueprint $table) {
            $table->foreign('extract_id')->references('id')->on('extracts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('priority_action_id')->references('id')->on('priority_actions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('priority_actions', function (Blueprint $table) {
            $table->foreign('recommendation_id')->references('id')->on('recommendations')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('ae_principle_recommendation', function (Blueprint $table) {
            $table->dropForeign(['ae_principle_id']);
            $table->dropForeign(['recommendation_id']);
        });

        Schema::table('ae_principle_statement', function (Blueprint $table) {
            $table->dropForeign(['ae_principle_id']);
            $table->dropForeign(['statement_id']);
        });

        Schema::table('extract_priority_action', function (Blueprint $table) {
            $table->dropForeign(['extract_id']);
            $table->dropForeign(['priority_action_id']);
        });

        Schema::table('priority_actions', function (Blueprint $table) {
            $table->dropForeign(['recommendation_id']);
        });
    }
};
