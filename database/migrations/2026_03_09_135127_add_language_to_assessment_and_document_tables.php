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

        Schema::create('languages', function (Blueprint $table) {
            $table->string('id')->primary()->comment('ISO 639-1');
            $table->json('name'); // translatable
            $table->timestamps();
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->string('language_id')->nullable();
        });

        Schema::table('policy_documents', function (Blueprint $table) {
            $table->string('language_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('assessments', 'language_id');
        Schema::dropColumns('policy_documents', 'language_id');
    }
};
