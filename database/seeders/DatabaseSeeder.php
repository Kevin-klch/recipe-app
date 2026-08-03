<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Reihenfolge ist wichtig: RecipeSeeder braucht die Nutzer und liest
     * beim Berechnen der Naehrwerte die NutritionItems.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            NutritionItemSeeder::class,
            RecipeSeeder::class,
        ]);
    }
}
