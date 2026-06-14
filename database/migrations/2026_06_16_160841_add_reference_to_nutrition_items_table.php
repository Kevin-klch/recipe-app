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
        Schema::table('nutrition_items', function (Blueprint $table) {
            $table->decimal('reference_amount', 8, 2)->default(100)->after('name');
            $table->string('reference_unit')->default('g')->after('reference_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nutrition_items', function (Blueprint $table) {
            //
        });
    }
};
