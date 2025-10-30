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
        Schema::rename('policies', 'policy_documents');

        Schema::table('policy_documents', function (Blueprint $table) {
            $table->string('text_direction')->nullable()->default('left_to_right')->after('comments');
            $table->longText('content')->nullable()->after('text_direction');
        });

        // update policy_statement table foreign key
        Schema::table('policy_statement', function (Blueprint $table) {

            $table->dropForeign('policy_statement_policy_id_foreign');
            $table->renameColumn('policy_id', 'policy_document_id');

        });

        Schema::table('policy_statement', function (Blueprint $table) {
            $table->foreign('policy_document_id')->references('id')->on('policy_documents')->cascadeOnDelete();
        });

        // rename to match Laravel defaults
        Schema::rename('policy_statement', 'policy_document_statement');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('policy_documents', 'policies');

        Schema::table('policies', function (Blueprint $table) {
            $table->dropColumn('text_direction');
            $table->dropColumn('content');
        });

        // revert policy_statement table foreign key
        Schema::table('policy_statement', function (Blueprint $table) {
            $table->dropForeign('policy_statement_policy_document_id_foreign');
            $table->foreign('policy_id')->references('id')->on('policies')->cascadeOnDelete();
        });
    }
};
