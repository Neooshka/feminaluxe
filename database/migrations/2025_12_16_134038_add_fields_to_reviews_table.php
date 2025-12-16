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
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('email', 50)->nullable()->after('rating');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('ip_address', 20)->nullable()->after('phone');
            $table->string('user_agent')->nullable()->after('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('email', 'phone', 'ip_address', 'user_agent');
        });
    }
};
