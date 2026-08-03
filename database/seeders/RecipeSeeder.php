<?php

namespace Database\Seeders;

use App\Models\NutritionItem;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RecipeSeeder extends Seeder
{
    /**
     * Legt 10 Rezepte mit Zutaten an und berechnet die Naehrwert-Summen
     * nach derselben Formel wie RecipeController.
     *
     * Voraussetzung: NutritionItemSeeder lief vorher. Jeder hier verwendete
     * Zutatenname existiert dort als NutritionItem – sonst wuerde das
     * Bearbeiten des Rezepts in der Oberflaeche den Nachfrage-Flow ausloesen.
     */
    public function run(): void
    {
        $users = User::pluck('id', 'email');

        foreach ($this->recipes() as $data) {
            $ingredients = $data['ingredients'];
            unset($data['ingredients']);

            $owner = $users[$data['owner']] ?? $users->first();
            unset($data['owner']);

            $nutrition = $this->calculateNutrition($ingredients);

            $recipe = Recipe::create($data + [
                'kcal' => $nutrition['kcal'],
                'total_kcal' => $nutrition['kcal'],
                'total_protein' => $nutrition['protein'],
                'total_carbs' => $nutrition['carbs'],
                'total_fat' => $nutrition['fat'],
            ]);

            $recipe->user_id = $owner;
            $recipe->save();

            foreach ($ingredients as [$amount, $unit, $name]) {
                $recipe->ingredients()->create([
                    'amount' => $amount,
                    'unit' => $unit,
                    'name' => $name,
                ]);
            }
        }
    }

    /**
     * Spiegelt RecipeController::calculateNutritionFromIngredients().
     * Zutaten ohne Menge, Einheit oder passendes NutritionItem zaehlen als 0.
     */
    private function calculateNutrition(array $ingredients): array
    {
        $totals = ['kcal' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0];

        foreach ($ingredients as [$amount, $unit, $name]) {
            if (empty($name) || empty($amount) || empty($unit)) {
                continue;
            }

            $item = NutritionItem::where('name', trim($name))->first();

            if (! $item || $item->reference_unit !== $unit) {
                continue;
            }

            $factor = (float) $amount / (float) $item->reference_amount;

            $totals['kcal'] += $factor * (float) $item->kcal;
            $totals['protein'] += $factor * (float) $item->protein;
            $totals['carbs'] += $factor * (float) $item->carbs;
            $totals['fat'] += $factor * (float) $item->fat;
        }

        return [
            'kcal' => round($totals['kcal']),
            'protein' => round($totals['protein'], 1),
            'carbs' => round($totals['carbs'], 1),
            'fat' => round($totals['fat'], 1),
        ];
    }

    /**
     * Referenziert ein Foto nur, wenn die Datei wirklich auf der public-Disk
     * liegt. Sonst bleibt photo_path null und die Views zeigen den Platzhalter.
     */
    private function photoIfExists(string $path): ?string
    {
        return Storage::disk('public')->exists($path) ? $path : null;
    }

    /**
     * Zutaten jeweils als [Menge, Einheit, Name].
     */
    private function recipes(): array
    {
        return [
            [
                'owner' => 'admin@admin.de',
                'title' => 'Nudelauflauf',
                'description' => 'Bester Nudelauflauf wo gibt',
                'duration_minutes' => 45,
                'difficulty' => 'easy',
                'price_level' => '10€',
                'servings' => 4,
                'diet_type' => 'none',
                'meal_type' => 'Mittagessen',
                'instructions' => "Nudeln gar kochen.\n\nZwiebel, Knoblauch, Gehacktes anbraten mit Öl. Mit Tomaten löschen, nach Geschmack würzen und 10 Minuten weiterkochen.\n\nAlles mischen, in eine Auflaufform geben, mit Sauce Hollandaise bestreichen, Käse drauf reiben, in den vorgeheizten 200° Ofen geben und 30 Minuten überbacken.",
                'notes' => null,
                'source_url' => null,
                'photo_path' => $this->photoIfExists('recipes/6CVuslDI0lMgfYKmOHW5t0dwlIk0c3UWKpl2a8Nm.jpg'),
                'ingredients' => [
                    ['500', 'g', 'Nudeln'],
                    ['500', 'g', 'Hackfleisch gemischt'],
                    ['500', 'g', 'Tomaten passiert'],
                    ['1', 'Stück', 'Zwiebel'],
                    ['1', 'Stück', 'Knoblauchzehe'],
                    ['200', 'g', 'Gouda'],
                    ['1', 'Packung', 'Sauce Hollandaise'],
                    [null, null, 'Gewürze'],
                ],
            ],
            [
                'owner' => 'admin@admin.de',
                'title' => 'Gyrosauflauf',
                'description' => 'Herzhafter Gyrosauflauf mit Schweinegeschnetzeltem, Paprika, Zwiebeln, Sahne und Käse überbacken.',
                'duration_minutes' => 60,
                'difficulty' => 'medium',
                'price_level' => '15€',
                'servings' => 4,
                'diet_type' => 'none',
                'meal_type' => 'Abendessen',
                'instructions' => "Backofen auf 180 °C Ober-/Unterhitze vorheizen.\n\nPaprika und Zwiebeln in Streifen schneiden.\n\nGyrosfleisch in einer Pfanne kräftig anbraten.\n\nPaprika und Zwiebeln hinzufügen und einige Minuten mitbraten.\n\nSahne und Schmand verrühren und über die Fleischmischung geben.\n\nAlles in eine Auflaufform geben.\n\nMit geriebenem Gouda bestreuen.\n\nCa. 30 Minuten backen bis der Käse goldbraun ist.",
                'notes' => 'Schmeckt besonders gut mit Reis oder Baguette.',
                'source_url' => null,
                'photo_path' => null,
                'ingredients' => [
                    ['800', 'g', 'Gyrosfleisch'],
                    ['2', 'Stück', 'Paprika'],
                    ['2', 'Stück', 'Zwiebel'],
                    ['200', 'ml', 'Sahne'],
                    ['200', 'g', 'Schmand'],
                    ['200', 'g', 'Gouda'],
                ],
            ],
            [
                'owner' => 'anna@example.de',
                'title' => 'Hähnchen-Reispfanne',
                'description' => 'Schnelle Pfanne mit Hähnchenbrust, Reis und Paprika.',
                'duration_minutes' => 30,
                'difficulty' => 'easy',
                'price_level' => '8€',
                'servings' => 4,
                'diet_type' => 'none',
                'meal_type' => 'Mittagessen',
                'instructions' => "Reis nach Packungsangabe garen.\n\nHähnchenbrust in Streifen schneiden und in Olivenöl anbraten.\n\nPaprika und Zwiebel dazugeben und mitbraten.\n\nReis unterrühren und kräftig würzen.",
                'notes' => 'Reste eignen sich gut als Meal Prep.',
                'source_url' => null,
                'photo_path' => null,
                'ingredients' => [
                    ['500', 'g', 'Hähnchenbrust'],
                    ['250', 'g', 'Reis'],
                    ['2', 'Stück', 'Paprika'],
                    ['1', 'Stück', 'Zwiebel'],
                    ['2', 'EL', 'Olivenöl'],
                    [null, null, 'Gewürze'],
                ],
            ],
            [
                'owner' => 'lukas@example.de',
                'title' => 'Lachs mit Ofenkartoffeln',
                'description' => 'Lachsfilet aus dem Ofen mit Kartoffelspalten.',
                'duration_minutes' => 50,
                'difficulty' => 'medium',
                'price_level' => '18€',
                'servings' => 4,
                'diet_type' => 'none',
                'meal_type' => 'Abendessen',
                'instructions' => "Kartoffeln in Spalten schneiden, mit Olivenöl mischen und bei 200 °C 30 Minuten backen.\n\nLachsfilet salzen, mit Butter belegen und die letzten 15 Minuten mitbacken.",
                'notes' => null,
                'source_url' => null,
                'photo_path' => null,
                'ingredients' => [
                    ['600', 'g', 'Lachsfilet'],
                    ['800', 'g', 'Kartoffeln'],
                    ['30', 'g', 'Butter'],
                    ['2', 'EL', 'Olivenöl'],
                    [null, null, 'Gewürze'],
                ],
            ],
            [
                'owner' => 'mia@example.de',
                'title' => 'Gemüse-Omelett',
                'description' => 'Fluffiges Omelett mit Paprika und Gouda.',
                'duration_minutes' => 15,
                'difficulty' => 'easy',
                'price_level' => '4€',
                'servings' => 2,
                'diet_type' => 'vegetarian',
                'meal_type' => 'Frühstück',
                'instructions' => "Eier mit Milch verquirlen und würzen.\n\nPaprika und Zwiebel klein schneiden und kurz anbraten.\n\nEimasse dazugeben, stocken lassen, Gouda darüber streuen und zusammenklappen.",
                'notes' => null,
                'source_url' => null,
                'photo_path' => null,
                'ingredients' => [
                    ['6', 'Stück', 'Ei'],
                    ['100', 'ml', 'Milch'],
                    ['1', 'Stück', 'Paprika'],
                    ['1', 'Stück', 'Zwiebel'],
                    ['100', 'g', 'Gouda'],
                    [null, null, 'Gewürze'],
                ],
            ],
            [
                'owner' => 'jonas@example.de',
                'title' => 'Tomatensuppe',
                'description' => 'Einfache Tomatensuppe aus passierten Tomaten.',
                'duration_minutes' => 25,
                'difficulty' => 'easy',
                'price_level' => '3€',
                'servings' => 4,
                'diet_type' => 'vegan',
                'meal_type' => 'Mittagessen',
                'instructions' => "Zwiebel und Knoblauch in Olivenöl anschwitzen.\n\nPassierte Tomaten dazugeben und 15 Minuten köcheln lassen.\n\nKräftig würzen und pürieren.",
                'notes' => 'Mit einem Schuss Sahne wird sie milder, ist dann aber nicht mehr vegan.',
                'source_url' => null,
                'photo_path' => null,
                'ingredients' => [
                    ['800', 'g', 'Tomaten passiert'],
                    ['2', 'Stück', 'Zwiebel'],
                    ['2', 'Stück', 'Knoblauchzehe'],
                    ['2', 'EL', 'Olivenöl'],
                    [null, null, 'Gewürze'],
                ],
            ],
            [
                'owner' => 'lena@example.de',
                'title' => 'Spinat-Feta-Auflauf',
                'description' => 'Cremiger Auflauf mit Blattspinat und Feta.',
                'duration_minutes' => 40,
                'difficulty' => 'easy',
                'price_level' => '9€',
                'servings' => 4,
                'diet_type' => 'vegetarian',
                'meal_type' => 'Abendessen',
                'instructions' => "Blattspinat auftauen und gut ausdrücken.\n\nMit Sahne und Eiern verrühren, würzen.\n\nIn eine Auflaufform geben, Feta darüber bröseln und bei 180 °C 25 Minuten backen.",
                'notes' => null,
                'source_url' => null,
                'photo_path' => null,
                'ingredients' => [
                    ['600', 'g', 'Blattspinat'],
                    ['200', 'g', 'Feta'],
                    ['200', 'ml', 'Sahne'],
                    ['2', 'Stück', 'Ei'],
                    [null, null, 'Gewürze'],
                ],
            ],
            [
                'owner' => 'tim@example.de',
                'title' => 'Milchreis',
                'description' => 'Klassischer Milchreis, warm oder kalt.',
                'duration_minutes' => 35,
                'difficulty' => 'easy',
                'price_level' => '3€',
                'servings' => 4,
                'diet_type' => 'vegetarian',
                'meal_type' => 'Dessert',
                'instructions' => "Milch aufkochen, Reis einrühren und bei kleiner Hitze 30 Minuten quellen lassen.\n\nRegelmäßig umrühren, am Ende mit Zucker süßen.",
                'notes' => 'Mit Zimt und Zucker servieren.',
                'source_url' => null,
                'photo_path' => null,
                'ingredients' => [
                    ['200', 'g', 'Reis'],
                    ['1000', 'ml', 'Milch'],
                    ['40', 'g', 'Zucker'],
                ],
            ],
            [
                'owner' => 'sarah@example.de',
                'title' => 'Apfelpfannkuchen',
                'description' => 'Pfannkuchen mit Apfelscheiben im Teig.',
                'duration_minutes' => 30,
                'difficulty' => 'medium',
                'price_level' => '5€',
                'servings' => 4,
                'diet_type' => 'vegetarian',
                'meal_type' => 'Dessert',
                'instructions' => "Mehl, Milch, Eier und Zucker zu einem glatten Teig verrühren.\n\nÄpfel in dünne Scheiben schneiden und unterheben.\n\nIn Butter portionsweise goldbraun ausbacken.",
                'notes' => null,
                'source_url' => null,
                'photo_path' => null,
                'ingredients' => [
                    ['200', 'g', 'Mehl'],
                    ['300', 'ml', 'Milch'],
                    ['3', 'Stück', 'Ei'],
                    ['2', 'Stück', 'Apfel'],
                    ['20', 'g', 'Butter'],
                    ['30', 'g', 'Zucker'],
                ],
            ],
            [
                'owner' => 'laura@example.de',
                'title' => 'Gurkensalat',
                'description' => 'Frischer Gurkensalat als Beilage.',
                'duration_minutes' => 10,
                'difficulty' => 'easy',
                'price_level' => '2€',
                'servings' => 2,
                'diet_type' => 'vegan',
                'meal_type' => 'Snack',
                'instructions' => "Gurke in feine Scheiben hobeln.\n\nMit Olivenöl, Salz und Pfeffer anmachen und kurz ziehen lassen.",
                'notes' => null,
                'source_url' => null,
                'photo_path' => null,
                'ingredients' => [
                    ['1', 'Stück', 'Gurke'],
                    ['2', 'EL', 'Olivenöl'],
                    [null, null, 'Gewürze'],
                ],
            ],
        ];
    }
}
