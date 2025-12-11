<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug', 50)->unique();
            $table->text('content');  // Markdown or HTML
            $table->dateTime('published_at')->nullable();  // For scheduling
            $table->timestamps();

            $table->index('slug');  // For fast lookups
            $table->index('published_at');  // For sorting
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
