<?php

namespace Database\Seeders;

use App\Models\NutritionItem;
use Illuminate\Database\Seeder;

class NutritionItemSeeder extends Seeder
{
    /**
     * Naehrwerte je Referenzmenge (nicht pro 100 g – die Referenz steht in
     * reference_amount / reference_unit).
     *
     * Wichtig: die reference_unit muss der Einheit entsprechen, die im Rezept
     * bei der Zutat eingetragen wird. Weicht sie ab, zaehlt die Zutat in
     * RecipeController::calculateNutritionFromIngredients() als 0.
     */
    public function run(): void
    {
        $items = [
            // [name, reference_amount, reference_unit, kcal, protein, carbs, fat]

            // Grundnahrungsmittel, je 100 g
            ['Nudeln', 100, 'g', 360, 12, 72, 2],
            ['Reis', 100, 'g', 350, 7, 78, 1],
            ['Mehl', 100, 'g', 340, 10, 72, 1],
            ['Zucker', 100, 'g', 400, 0, 100, 0],
            ['Kartoffeln', 100, 'g', 77, 2, 17, 0.1],

            // Fleisch und Fisch, je 100 g
            ['Hähnchenbrust', 100, 'g', 110, 23, 0, 1.5],
            ['Gyrosfleisch', 100, 'g', 190, 20, 2, 11],
            ['Hackfleisch gemischt', 100, 'g', 250, 18, 0, 20],
            ['Lachsfilet', 100, 'g', 200, 20, 0, 13],

            // Milchprodukte und Fette, je 100 g
            ['Gouda', 100, 'g', 356, 25, 0, 28],
            ['Feta', 100, 'g', 264, 14, 4, 21],
            ['Schmand', 100, 'g', 240, 2.5, 3, 24],
            ['Butter', 100, 'g', 740, 0.7, 0.6, 82],

            // Fluessiges, je 100 ml
            ['Milch', 100, 'ml', 64, 3.4, 4.8, 3.6],
            ['Sahne', 100, 'ml', 300, 2.4, 3.3, 32],

            // Gemuese und Obst, je Stueck
            ['Ei', 1, 'Stück', 78, 6.3, 0.6, 5.3],
            ['Zwiebel', 1, 'Stück', 40, 1.1, 9, 0.1],
            ['Paprika', 1, 'Stück', 46, 1.5, 9, 0.5],
            ['Apfel', 1, 'Stück', 65, 0.3, 15, 0.2],
            ['Gurke', 1, 'Stück', 45, 2, 7, 0.4],
            ['Knoblauchzehe', 1, 'Stück', 5, 0.2, 1, 0],

            // Gemuese, je 100 g
            ['Tomaten passiert', 100, 'g', 35, 1.5, 6, 0.2],
            ['Blattspinat', 100, 'g', 23, 2.9, 3.6, 0.4],

            // Sonstiges mit eigener Referenzeinheit
            ['Olivenöl', 1, 'EL', 90, 0, 0, 10],
            ['Sauce Hollandaise', 1, 'Packung', 250, 2, 4, 25],
            ['Gewürze', 1, 'Prise', 0, 0, 0, 0],
        ];

        foreach ($items as [$name, $refAmount, $refUnit, $kcal, $protein, $carbs, $fat]) {
            NutritionItem::updateOrCreate(
                ['name' => $name],
                [
                    'reference_amount' => $refAmount,
                    'reference_unit' => $refUnit,
                    'kcal' => $kcal,
                    'protein' => $protein,
                    'carbs' => $carbs,
                    'fat' => $fat,
                ]
            );
        }
    }
}
