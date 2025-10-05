<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('statements', function (Blueprint $table) {
            $table->string('priority_action_id', 400)->constrained();
        });

        // update statements - copy over priority_action_id from assessment_priority_actions table
        \Illuminate\Support\Facades\DB::statement('
            UPDATE statements
            SET priority_action_id = (
                SELECT assessment_priority_action.priority_action_id
                FROM assessment_priority_action
                WHERE assessment_priority_action.id = statements.assessment_priority_action_id
            );');



        Schema::table('statements', function (Blueprint $table) {
            $table->foreignId('assessment_id')->constrained();
            $table->dropColumn('assessment_priority_action_id');
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
