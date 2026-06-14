<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\NutritionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    public function home()
    {
        $recipes = Recipe::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('home', compact('recipes'));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $recipes = Recipe::with('user')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return view('recipes.index', compact('recipes', 'search'));
    }

    public function create()
    {
        $nutritionItems = NutritionItem::orderBy('name')->get();

        return view('recipes.create', compact('nutritionItems'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRecipe($request);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('recipes', 'public');
        }

        $ingredients = $validated['ingredients'] ?? [];

        $missingNutritionItems = $this->findMissingNutritionItems($ingredients);

        if ($missingNutritionItems->isNotEmpty()) {
            session([
                'pending_recipe' => $validated,
                'pending_recipe_ingredients' => $ingredients,
                'missing_nutrition_items' => $missingNutritionItems->toArray(),
            ]);

            return redirect()->route('nutrition.missing');
        }

        $nutrition = $this->calculateNutritionFromIngredients($ingredients);

        $validated['kcal'] = $nutrition['kcal'];
        $validated['total_kcal'] = $nutrition['kcal'];
        $validated['total_protein'] = $nutrition['protein'];
        $validated['total_carbs'] = $nutrition['carbs'];
        $validated['total_fat'] = $nutrition['fat'];

        unset($validated['photo'], $validated['ingredients']);

        $recipe = auth()->user()->recipes()->create($validated);

        $this->syncIngredients($recipe, $ingredients);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', 'Rezept wurde gespeichert.');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load(['ingredients', 'user']);

        return view('recipes.show', compact('recipe'));
    }

    public function edit(Recipe $recipe)
    {
        abort_unless(
            $recipe->user_id === auth()->id() || auth()->user()->is_admin,
            403
        );

        $recipe->load('ingredients');

        $nutritionItems = NutritionItem::orderBy('name')->get();

        return view('recipes.edit', compact('recipe', 'nutritionItems'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        abort_unless(
            $recipe->user_id === auth()->id() || auth()->user()->is_admin,
            403
        );

        $validated = $this->validateRecipe($request);

        if ($request->hasFile('photo')) {
            if ($recipe->photo_path) {
                Storage::disk('public')->delete($recipe->photo_path);
            }

            $validated['photo_path'] = $request->file('photo')->store('recipes', 'public');
        }

        $ingredients = $validated['ingredients'] ?? [];

        $missingNutritionItems = $this->findMissingNutritionItems($ingredients);

        if ($missingNutritionItems->isNotEmpty()) {
            session([
                'pending_update_recipe_id' => $recipe->id,
                'pending_recipe' => $validated,
                'pending_recipe_ingredients' => $ingredients,
                'missing_nutrition_items' => $missingNutritionItems->toArray(),
            ]);

            return redirect()->route('nutrition.missing');
        }

        $nutrition = $this->calculateNutritionFromIngredients($ingredients);

        $validated['kcal'] = $nutrition['kcal'];
        $validated['total_kcal'] = $nutrition['kcal'];
        $validated['total_protein'] = $nutrition['protein'];
        $validated['total_carbs'] = $nutrition['carbs'];
        $validated['total_fat'] = $nutrition['fat'];

        unset($validated['photo'], $validated['ingredients']);

        $recipe->update($validated);

        $this->syncIngredients($recipe, $ingredients);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', 'Rezept wurde aktualisiert.');
    }

    public function destroy(Recipe $recipe)
    {
        abort_unless(
            $recipe->user_id === auth()->id() || auth()->user()->is_admin,
            403
        );

        if ($recipe->photo_path) {
            Storage::disk('public')->delete($recipe->photo_path);
        }

        $recipe->delete();

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Rezept wurde gelöscht.');
    }

    private function validateRecipe(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'difficulty' => ['nullable', 'string'],
            'price_level' => ['nullable', 'string'],
            'servings' => ['nullable', 'integer', 'min:1'],
            'kcal' => ['nullable', 'integer', 'min:0'],
            'diet_type' => ['required', 'string'],
            'meal_type' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'source_url' => ['nullable', 'url'],
            'photo' => ['nullable', 'image', 'max:4096'],

            'ingredients' => ['nullable', 'array'],
            'ingredients.*.amount' => ['nullable', 'string', 'max:50'],
            'ingredients.*.unit' => ['nullable', 'string', 'max:50'],
            'ingredients.*.name' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function findMissingNutritionItems(array $ingredients)
    {
        return collect($ingredients)
            ->pluck('name')
            ->filter()
            ->map(fn ($name) => trim($name))
            ->unique()
            ->reject(fn ($name) => NutritionItem::where('name', $name)->exists())
            ->values();
    }

    private function calculateNutritionFromIngredients(array $ingredients): array
    {
        $totals = [
            'kcal' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
        ];

        foreach ($ingredients as $ingredient) {
            if (empty($ingredient['name']) || empty($ingredient['amount']) || empty($ingredient['unit'])) {
                continue;
            }

            $nutritionItem = NutritionItem::where('name', trim($ingredient['name']))->first();

            if (! $nutritionItem) {
                continue;
            }

            if ($nutritionItem->reference_unit !== $ingredient['unit']) {
                continue;
            }

            $factor = (float) $ingredient['amount'] / (float) $nutritionItem->reference_amount;

            $totals['kcal'] += $factor * (float) $nutritionItem->kcal;
            $totals['protein'] += $factor * (float) $nutritionItem->protein;
            $totals['carbs'] += $factor * (float) $nutritionItem->carbs;
            $totals['fat'] += $factor * (float) $nutritionItem->fat;
        }

        return [
            'kcal' => round($totals['kcal']),
            'protein' => round($totals['protein'], 1),
            'carbs' => round($totals['carbs'], 1),
            'fat' => round($totals['fat'], 1),
        ];
    }

    private function syncIngredients(Recipe $recipe, array $ingredients): void
    {
        $recipe->ingredients()->delete();

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
    }
}