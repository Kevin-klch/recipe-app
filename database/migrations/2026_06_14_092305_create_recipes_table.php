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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('difficulty')->nullable();
            $table->string('price_level')->nullable();
            $table->unsignedInteger('servings')->nullable();
            $table->unsignedInteger('kcal')->nullable();
            $table->string('diet_type')->default('none');
            $table->string('meal_type')->nullable();
            $table->text('instructions')->nullable();
            $table->text('notes')->nullable();
            $table->string('source_url')->nullable();
            $table->string('photo_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
