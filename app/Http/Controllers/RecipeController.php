<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $recipes = Recipe::query()
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return view('recipes.index', [
            'recipes' => $recipes,
            'search' => $search,
        ]);
    }
    public function create()
    {
        return view('recipes.create');
    }

    public function home()
    {
        $recipes = Recipe::latest()->take(10)->get();

        return view('home', compact('recipes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('recipes', 'public');
        }

        $ingredients = $validated['ingredients'] ?? [];

        unset($validated['photo'], $validated['ingredients']);

        $recipe = Recipe::create($validated);

        foreach ($ingredients as $ingredient) {
            if (! empty($ingredient['name'])) {
                $recipe->ingredients()->create([
                    'amount' => $ingredient['amount'] ?? null,
                    'unit' => $ingredient['unit'] ?? null,
                    'name' => $ingredient['name'],
                ]);
            }
        }

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', 'Rezept wurde gespeichert.');
    }

    public function show(Recipe $recipe)
    {
        $recipe->load('ingredients');

        return view('recipes.show', compact('recipe'));
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()
            ->route('recipes.index')
            ->with('success', 'Rezept wurde gelöscht.');
    }

    public function edit(Recipe $recipe)
    {
        return view('recipes.edit', compact('recipe'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validated = $request->validate([
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
        ]);

        if ($request->hasFile('photo')) {
            if ($recipe->photo_path) {
                Storage::disk('public')->delete($recipe->photo_path);
            }

            $validated['photo_path'] = $request
                ->file('photo')
                ->store('recipes', 'public');
        }

        unset($validated['photo']);

        $recipe->update($validated);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('success', 'Rezept wurde aktualisiert.');
    }
}