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
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->string('email');

            $table->foreignId(config('filament-team-management.column_names.teams_foreign_key'))
                ->nullable()->constrained()->onDelete('cascade');
            $table->foreignId(config('permission.column_names.role_pivot_key') ?? 'role_id')
                ->nullable()->constrained()->onDelete('cascade');

            $table->foreignId('inviter_id');
            $table->string('token');
            $table->tinyInteger('is_confirmed')->default(0);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('latest_assessment_id', 'latest_team_id');
        });

        Schema::table('assessment_user', function (Blueprint $table) {
           $table->boolean('is_admin')->default(false)->after('user_id');
        });

        Schema::dropIfExists('role_invites');
        Schema::dropIfExists('team_invites');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_management_4', function (Blueprint $table) {
            //
        });
    }
};
