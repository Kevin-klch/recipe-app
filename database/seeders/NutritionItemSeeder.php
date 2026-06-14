<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NutritionItem;

class NutritionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NutritionItem::upsert([
            [
                'name' => 'Reis',
                'kcal' => 350,
                'protein' => 7,
                'carbs' => 78,
                'fat' => 1,
            ],
            [
                'name' => 'Nudeln',
                'kcal' => 360,
                'protein' => 12,
                'carbs' => 72,
                'fat' => 2,
            ],
            [
                'name' => 'Hähnchenbrust',
                'kcal' => 110,
                'protein' => 23,
                'carbs' => 0,
                'fat' => 1.5,
            ],
            [
                'name' => 'Paprika',
                'kcal' => 31,
                'protein' => 1,
                'carbs' => 6,
                'fat' => 0.3,
            ],
        ], ['name'], ['kcal', 'protein', 'carbs', 'fat']);
    }
}
