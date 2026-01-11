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
        Schema::table('policy_documents', function (Blueprint $table) {
            $table->year('year')->nullable()->after('short_title');
            $table->year('end_year')->nullable()->after('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policy_documents_tabgle', function (Blueprint $table) {
            //
        });
    }
};
