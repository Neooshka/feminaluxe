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
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('seo_title', 60)->nullable()->change();
            $table->string('seo_description', 160)->nullable()->change();
            $table->string('seo_keywords', 300)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->change();
            $table->text('seo_description')->nullable()->change();
            $table->string('seo_keywords')->nullable()->change();
        });
    }
};
