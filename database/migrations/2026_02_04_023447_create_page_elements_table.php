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
        // Update pages table to make slug nullable
        Schema::table('pages', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
            // Drop content column if we are moving to elements?
            // keeping it for now or maybe deprecating.
        });

        // Create page_elements table
        Schema::create('page_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->string('type'); // title, content, carousel
            $table->json('content')->nullable(); // Stores text, html, or list of image URLs
            $table->integer('order')->default(0);
            $table->json('settings')->nullable(); // For styles or other config
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_elements');

        Schema::table('pages', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }
};
