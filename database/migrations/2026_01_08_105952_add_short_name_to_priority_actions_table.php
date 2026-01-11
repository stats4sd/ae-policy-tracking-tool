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
        Schema::table('priority_actions', function (Blueprint $table) {
            $table->string('short_name')->after('name');
            $table->string('code_and_short_name')
                ->virtualAs("CONCAT(id, ' - ', short_name)")->nullable()
                ->after('code_and_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('priority_actions', function (Blueprint $table) {
            //
        });
    }
};
