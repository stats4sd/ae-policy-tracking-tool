<?php

use App\Enums\PageType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('policy_document_pages', function (Blueprint $table) {
            $table->string('page_type')->default(PageType::Unknown->value)->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('policy_document_pages', function (Blueprint $table) {
            $table->dropColumn('page_type');
        });
    }
};
