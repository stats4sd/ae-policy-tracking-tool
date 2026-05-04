<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $isMysql = DB::connection()->getDriverName() === 'mysql';

        // Standard constraint names — use column array form (works on SQLite + MySQL)
        Schema::table('default_search_terms', function (Blueprint $table) {
            $table->dropForeign(['priority_action_id']);
            $table->foreign('priority_action_id')->references('id')->on('priority_actions')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('search_terms', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->foreign('assessment_id')->references('id')->on('assessments')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('statements', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->dropForeign(['theme_id']);
            $table->foreign('assessment_id')->references('id')->on('assessments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('theme_id')->references('id')->on('themes')->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->foreign('assessment_id')->references('id')->on('assessments')->cascadeOnDelete()->cascadeOnUpdate();
        });

        // Non-standard constraint names — MySQL only (SQLite ignores FKs anyway)
        Schema::table('extract_search_term', function (Blueprint $table) use ($isMysql) {
            if ($isMysql) {
                $table->dropForeign('highlight_search_term_highlight_id_foreign');
                $table->dropForeign('highlight_search_term_search_term_id_foreign');
            }
            $table->foreign('extract_id')->references('id')->on('extracts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('search_term_id')->references('id')->on('search_terms')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('extract_statement', function (Blueprint $table) use ($isMysql) {
            if ($isMysql) {
                $table->dropForeign('highlight_statement_statement_id_foreign');
                $table->dropForeign('highlight_statement_highlight_id_foreign');
            }
            $table->foreign('statement_id')->references('id')->on('statements')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('extract_id')->references('id')->on('extracts')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('extracts', function (Blueprint $table) use ($isMysql) {
            if ($isMysql) {
                $table->dropForeign('extracts_score_id_foreign');
                $table->dropForeign('highlights_type_id_foreign'); // duplicate of extracts_score_id_foreign — drop only
                $table->dropForeign('highlights_theme_id_foreign');
            }
            $table->foreign('score_id')->references('id')->on('scores')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('theme_id')->references('id')->on('themes')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        $isMysql = DB::connection()->getDriverName() === 'mysql';

        Schema::table('default_search_terms', function (Blueprint $table) {
            $table->dropForeign(['priority_action_id']);
            $table->foreign('priority_action_id')->references('id')->on('priority_actions');
        });

        Schema::table('extract_search_term', function (Blueprint $table) {
            $table->dropForeign(['extract_id']);
            $table->dropForeign(['search_term_id']);
            if (DB::connection()->getDriverName() === 'mysql') {
                $table->foreign('extract_id')->references('id')->on('extracts');
                $table->foreign('search_term_id')->references('id')->on('search_terms');
            }
        });

        Schema::table('extract_statement', function (Blueprint $table) {
            $table->dropForeign(['statement_id']);
            $table->dropForeign(['extract_id']);
            if (DB::connection()->getDriverName() === 'mysql') {
                $table->foreign('statement_id')->references('id')->on('statements');
                $table->foreign('extract_id')->references('id')->on('extracts');
            }
        });

        Schema::table('extracts', function (Blueprint $table) {
            $table->dropForeign(['score_id']);
            $table->dropForeign(['theme_id']);
            if (DB::connection()->getDriverName() === 'mysql') {
                $table->foreign('score_id')->references('id')->on('scores');
                $table->foreign('theme_id')->references('id')->on('themes');
            }
        });

        Schema::table('search_terms', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->foreign('assessment_id')->references('id')->on('assessments');
        });

        Schema::table('statements', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->dropForeign(['theme_id']);
            $table->foreign('assessment_id')->references('id')->on('assessments');
            $table->foreign('theme_id')->references('id')->on('themes');
        });

        Schema::table('themes', function (Blueprint $table) {
            $table->dropForeign(['assessment_id']);
            $table->foreign('assessment_id')->references('id')->on('assessments');
        });
    }
};
