# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Layout

This repository is nested one level down: the working directory usually opened is `Rezept App/`, while the Laravel app and the git repo root are `Rezept App/recipe-app/`. **Run every command below from `recipe-app/`.**

A self-hosted recipe manager ("Meine Rezepte"): Laravel 13 + Blade + SQLite, German UI, per-user recipes with a simple admin role. Target deployment is a Raspberry Pi.

## Commands

```bash
composer dev                      # server + queue listener + pail logs + vite, concurrently
php artisan serve                 # server only (usually enough — see "Frontend" below)
npm run dev / npm run build       # Vite; only affects Breeze/Tailwind assets

php artisan test                  # full suite (also what CI runs)
php artisan test --filter=ProfileTest
php artisan test tests/Feature/Auth/AuthenticationTest.php
vendor/bin/pint                   # formatter (Laravel Pint)

php artisan migrate
php artisan db:seed --class=NutritionItemSeeder
php artisan storage:link          # required for recipe photos to be visible
php artisan tinker                # the only way to grant admin (see "Authorization")
```

`.env` and `database/database.sqlite` are both gitignored, so a fresh clone needs:
`cp .env.example .env && php artisan key:generate && touch database/database.sqlite && php artisan migrate && php artisan db:seed --class=NutritionItemSeeder && php artisan storage:link`.

The local `database/database.sqlite` holds real development data (recipes, user accounts) and is never pushed — prefer `migrate` over `migrate:fresh`, and don't assume a teammate's database looks like yours. Tests run against in-memory SQLite (`phpunit.xml`).

## Architecture

### Two parallel frontends — know which one you are in

- **Breeze/Tailwind stack**: `layouts/app.blade.php`, `layouts/guest.blade.php`, `x-app-layout`, the `components/` directory, Tailwind, Alpine, Vite. Used *only* by `auth/*` (except `login`), `profile/*`, `dashboard`, `welcome`.
- **The actual app**: `home`, `recipes/*`, `nutrition/missing`, `admin/users/index`, `auth/login` are **standalone HTML documents** — each has its own `<!DOCTYPE>`, links `public/css/base.css` plus a per-page stylesheet (`home.css`, `recipes-create.css`, `recipes-index.css`, `recipes-show.css`) via `asset()`, and carries its own inline `<script>`.

So: styling a recipe page means editing hand-written CSS in `public/css/`, **not** Tailwind classes, and no build step is involved. `resources/js/app.js` contains modal helpers that are duplicated inline in `home.blade.php` — the Vite bundle is not loaded by any app page.

Dynamic ingredient rows are built by inline JS in `recipes/create.blade.php` and `recipes/edit.blade.php`, which appends `ingredients[N][name|amount|unit]` inputs and offers a fixed German unit list (`g, kg, ml, l, TL, EL, Prise, Stück, Dose, Packung`). Ingredient names autocomplete from a `<datalist>` of `NutritionItem` names.

All user-facing strings, flash messages and validation output are **German**, even though `APP_LOCALE=en`.

### Domain model

- `Recipe` belongsTo `User`, hasMany `Ingredient` (cascade delete).
- `Ingredient` stores free text: `amount`, `unit`, `name` are strings, matched to nutrition data by name.
- `NutritionItem` is a global lookup table with a **unique `name`** and a reference basis (`reference_amount`, `reference_unit`, default `100 g`) plus `kcal/protein/carbs/fat` for that basis. Shared across all users.

### The nutrition-gap flow (spans RecipeController ↔ session ↔ NutritionController)

This is the least obvious part of the codebase. On `store`/`update`, `RecipeController`:

1. Validates, saves the photo, then calls `findMissingNutritionItems()` — ingredient names with **no exact (trimmed) `NutritionItem` name match**.
2. If any are missing, it does *not* save the recipe. It stashes `pending_recipe`, `pending_recipe_ingredients`, `missing_nutrition_items` (and `pending_update_recipe_id` on update) in the **session** and redirects to `nutrition.missing`.
3. `nutrition/missing.blade.php` asks the user for the nutrition values; `NutritionController@storeMissing` upserts the `NutritionItem`s, then creates the recipe and its ingredients from the session data and clears those keys.
4. If nothing is missing, `calculateNutritionFromIngredients()` scales each item by `amount / reference_amount` and writes **denormalized totals** onto the recipe (`kcal`, `total_kcal`, `total_protein`, `total_carbs`, `total_fat`).

Consequences to keep in mind: an ingredient whose `unit` does not string-match the item's `reference_unit` contributes **zero, silently**; totals are snapshots and are never recomputed when a `NutritionItem` later changes.

### Authorization

There are no policies, gates, or admin middleware. Every protected action repeats an inline check in the controller:

```php
abort_unless($recipe->user_id === auth()->id() || auth()->user()->is_admin, 403);
abort_unless(auth()->user()->is_admin, 403);   // Admin\UserController, per method
```

The `admin.` route group only applies `auth`, so the `is_admin` check must be repeated in each new admin method. `is_admin` is a plain boolean column with no UI for granting it — set it in tinker. `/admin/users` is also not linked from anywhere in the UI; you have to know the URL. Note `Admin\UserController@store` creates users without `is_admin`, and self-deletion is blocked.

Recipe **viewing** is intentionally open to any authenticated user (`show`, `index`, `home` have no ownership check); only edit/update/destroy are restricted. Nothing is public — every route sits behind `auth`.

### Photos

Uploaded via `$request->file('photo')->store('recipes', 'public')` — the `public` disk is named explicitly, so `FILESYSTEM_DISK=local` in `.env` is irrelevant. Old files are deleted on replace and on recipe delete. Views fall back to `storage/recipes/nA.png` when `photo_path` is null.

## Known rough edges

Verified by reading the code; don't treat these as intended behavior:

- `Recipe::getProteinPerServingAttribute()` / `carbs` / `fat` read `$this->protein|carbs|fat`, which are not columns (the columns are `total_protein`, …). Only `kcal_per_serving` works; the views compute per-serving values from `total_*` inline instead.
- `NutritionController@storeMissing` ignores `pending_update_recipe_id`: editing a recipe to add an unknown ingredient **creates a new recipe** instead of updating the existing one. It also never calls the nutrition calculation, so recipes created through this path are saved with zero/null totals.
- `RecipeController@index` search combines `where(...)->orWhere(...)` without grouping — harmless today since there is no other constraint, but it will leak results if a scope is ever added.
- Tests cover only the Breeze-generated auth/profile flows. There is no coverage for recipes, ingredients, nutrition, or admin.
