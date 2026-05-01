<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the FK and column from jurisdictions before changing countries PK
        Schema::table('jurisdictions', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });

        Schema::drop('countries');

        Schema::create('countries', function (Blueprint $table) {
            $table->char('id', 3)->primary()->comment('ISO 3166-1 alpha-3');
            $table->string('name', 400);
            $table->char('iso2', 2)->nullable()->comment('ISO 3166-1 alpha-2');
            $table->char('un_code', 3)->nullable()->comment('M49 standard');
            $table->timestamps();
        });

        Schema::table('jurisdictions', function (Blueprint $table) {
            $table->char('country_id', 3)->nullable()->after('type');
            $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('jurisdictions', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });

        Schema::drop('countries');

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 400);
            $table->timestamps();
        });

        Schema::table('jurisdictions', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('type')->constrained()->nullOnDelete();
        });
    }
};
