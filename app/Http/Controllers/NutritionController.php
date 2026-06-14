<?php

namespace App\Http\Controllers;

use App\Models\NutritionItem;
use App\Models\Recipe;
use Illuminate\Http\Request;

class NutritionController extends Controller
{
    public function missing()
    {
        $items = session('missing_nutrition_items', []);

        if (empty($items)) {
            return redirect()->route('recipes.create');
        }

        return view('nutrition.missing', compact('items'));
    }

    public function storeMissing(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],

            'items.*.name' => ['required', 'string', 'max:255'],

            'items.*.reference_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'items.*.reference_unit' => [
                'required',
                'string',
                'max:50',
            ],

            'items.*.kcal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.protein' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.carbs' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.fat' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        foreach ($validated['items'] as $item) {

            NutritionItem::updateOrCreate(
                [
                    'name' => trim($item['name']),
                ],
                [
                    'reference_amount' => $item['reference_amount'],
                    'reference_unit' => $item['reference_unit'],

                    'kcal' => $item['kcal'],
                    'protein' => $item['protein'],
                    'carbs' => $item['carbs'],
                    'fat' => $item['fat'],
                ]
            );
        }

        $recipeData = session('pending_recipe');
        $ingredients = session('pending_recipe_ingredients', []);

        if (!$recipeData) {
            return redirect()->route('recipes.create');
        }

        unset(
            $recipeData['photo'],
            $recipeData['ingredients']
        );

        $recipe = auth()->user()
            ->recipes()
            ->create($recipeData);

        foreach ($ingredients as $ingredient) {

            if (empty($ingredient['name'])) {
                continue;
            }

            $recipe->ingredients()->create([
                'amount' => $ingredient['amount'] ?? null,
                'unit' => $ingredient['unit'] ?? null,
                'name' => trim($ingredient['name']),
            ]);
        }

        session()->forget([
            'pending_recipe',
            'pending_recipe_ingredients',
            'missing_nutrition_items',
        ]);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with(
                'success',
                'Nährwerte gespeichert und Rezept angelegt.'
            );
    }
}