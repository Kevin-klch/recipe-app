<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezept bearbeiten</title>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-create.css') }}">
</head>

<body>
    <main class="container">
        <a class="back-link" href="{{ route('recipes.show', $recipe) }}">
            ← Zurück zum Rezept
        </a>

        <form class="card form-card" action="{{ route('recipes.update', $recipe) }}" method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <h1>Rezept bearbeiten</h1>
            <p>Ändere die Daten deines Rezepts.</p>

            <div class="form-grid">
                <div class="form-group full">
                    <label for="title">Titel</label>
                    <input class="input" type="text" id="title" name="title" value="{{ old('title', $recipe->title) }}"
                        required>
                </div>

                <div class="form-group full">
                    <label for="description">Beschreibung</label>
                    <textarea class="input" id="description" name="description"
                        rows="4">{{ old('description', $recipe->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="duration_minutes">Dauer in Minuten</label>
                    <input class="input" type="number" id="duration_minutes" name="duration_minutes"
                        value="{{ old('duration_minutes', $recipe->duration_minutes) }}" min="0">
                </div>

                <div class="form-group">
                    <label for="difficulty">Aufwand</label>
                    <select id="difficulty" name="difficulty">
                        <option value="">Bitte auswählen</option>
                        <option value="easy" @selected(old('difficulty', $recipe->difficulty) === 'easy')>Einfach</option>
                        <option value="medium" @selected(old('difficulty', $recipe->difficulty) === 'medium')>Mittel
                        </option>
                        <option value="hard" @selected(old('difficulty', $recipe->difficulty) === 'hard')>Aufwendig
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price_level">Preisschätzung</label>
                    <input class="input" type="text" id="price_level" name="price_level"
                        placeholder="z.B. ca. 8 € oder günstig" value="{{ old('price_level', $recipe->price_level) }}">
                </div>

                <div class="form-group">
                    <label for="servings">Portionen</label>
                    <input class="input" type="number" id="servings" name="servings"
                        value="{{ old('servings', $recipe->servings) }}" min="1">
                </div>

                <div class="form-group">
                    <label for="kcal">Kcal optional</label>
                    <input class="input" type="number" id="kcal" name="kcal" value="{{ old('kcal', $recipe->kcal) }}"
                        min="0">
                </div>

                <div class="form-group">
                    <label for="diet_type">Ernährungsart</label>
                    <select id="diet_type" name="diet_type">
                        <option value="none" @selected(old('diet_type', $recipe->diet_type) === 'none')>Normal</option>
                        <option value="vegetarian" @selected(old('diet_type', $recipe->diet_type) === 'vegetarian')>
                            Vegetarisch</option>
                        <option value="vegan" @selected(old('diet_type', $recipe->diet_type) === 'vegan')>Vegan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="meal_type">Kategorie</label>
                    <select id="meal_type" name="meal_type">
                        <option value="">Bitte auswählen</option>
                        <option value="breakfast" @selected(old('meal_type', $recipe->meal_type) === 'breakfast')>
                            Frühstück</option>
                        <option value="lunch" @selected(old('meal_type', $recipe->meal_type) === 'lunch')>Mittagessen
                        </option>
                        <option value="dinner" @selected(old('meal_type', $recipe->meal_type) === 'dinner')>Abendessen
                        </option>
                        <option value="snack" @selected(old('meal_type', $recipe->meal_type) === 'snack')>Snack</option>
                        <option value="dip" @selected(old('meal_type', $recipe->meal_type) === 'dip')>Dip</option>
                        <option value="dessert" @selected(old('meal_type', $recipe->meal_type) === 'dessert')>Dessert
                        </option>
                        <option value="drink" @selected(old('meal_type', $recipe->meal_type) === 'drink')>Getränk</option>
                        <option value="side" @selected(old('meal_type', $recipe->meal_type) === 'side')>Beilage</option>
                        <option value="other" @selected(old('meal_type', $recipe->meal_type) === 'other')>Sonstiges
                        </option>
                    </select>
                </div>

                <div class="form-group full">
                    <label for="instructions">Zubereitung</label>
                    <textarea class="input" id="instructions" name="instructions"
                        rows="8">{{ old('instructions', $recipe->instructions) }}</textarea>
                </div>

                <div class="form-group full">
                    <label for="notes">Notizen</label>
                    <textarea class="input" id="notes" name="notes"
                        rows="4">{{ old('notes', $recipe->notes) }}</textarea>
                </div>

                <div class="form-group full">
                    <label for="source_url">Quelle / Link optional</label>
                    <input class="input" type="url" id="source_url" name="source_url"
                        value="{{ old('source_url', $recipe->source_url) }}">
                </div>

                <div class="form-group full">
                    <label for="photo">Foto ändern</label>
                    <input class="input" type="file" id="photo" name="photo" accept="image/*">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('recipes.show', $recipe) }}" class="btn btn-secondary">
                    Abbrechen
                </a>

                <button type="submit" class="btn">
                    Änderungen speichern
                </button>
            </div>
        </form>
    </main>
</body>

</html>