<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezept bearbeiten | Meine Rezepte</title>

    @include('partials.theme-init')

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-form.css') }}">
</head>

<body>

    <x-page-hero :back-url="route('recipes.show', $recipe)" back-label="Zurück zum Rezept"
        :photo="$recipe->photo_path ? asset('storage/'.$recipe->photo_path) : null" :alt="$recipe->title" symbol="✏️" />

    <main class="container page-main">

        <x-page-card title="Rezept bearbeiten" :subtitle="$recipe->title" />

        <form action="{{ route('recipes.update', $recipe) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="content-grid">

                <section class="card panel">
                    <h2>Grunddaten</h2>

                    <div class="form-group">
                        <label for="title">Titel</label>
                        <input class="input" type="text" id="title" name="title"
                            value="{{ old('title', $recipe->title) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Beschreibung</label>
                        <textarea class="input" id="description" name="description"
                            rows="3">{{ old('description', $recipe->description) }}</textarea>
                    </div>
                </section>

                <section class="card panel">
                    <h2>Angaben</h2>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="duration_minutes">Dauer in Minuten</label>
                            <input class="input" type="number" id="duration_minutes" name="duration_minutes" min="0"
                                value="{{ old('duration_minutes', $recipe->duration_minutes) }}">
                        </div>

                        <div class="form-group">
                            <label for="servings">Portionen</label>
                            <input class="input" type="number" id="servings" name="servings" min="1"
                                value="{{ old('servings', $recipe->servings) }}">
                        </div>

                        <div class="form-group">
                            <label for="difficulty">Aufwand</label>
                            <select class="input" id="difficulty" name="difficulty">
                                <option value="">Bitte auswählen</option>
                                <option value="easy" @selected(old('difficulty', $recipe->difficulty) === 'easy')>Einfach</option>
                                <option value="medium" @selected(old('difficulty', $recipe->difficulty) === 'medium')>Mittel</option>
                                <option value="hard" @selected(old('difficulty', $recipe->difficulty) === 'hard')>Aufwendig</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price_level">Preisschätzung</label>
                            <input class="input" type="text" id="price_level" name="price_level"
                                placeholder="z.B. ca. 8 € oder günstig"
                                value="{{ old('price_level', $recipe->price_level) }}">
                        </div>

                        <div class="form-group">
                            <label for="diet_type">Ernährungsart</label>
                            <select class="input" id="diet_type" name="diet_type">
                                <option value="none" @selected(old('diet_type', $recipe->diet_type) === 'none')>Normal</option>
                                <option value="vegetarian" @selected(old('diet_type', $recipe->diet_type) === 'vegetarian')>Vegetarisch</option>
                                <option value="vegan" @selected(old('diet_type', $recipe->diet_type) === 'vegan')>Vegan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="meal_type">Kategorie</label>
                            {{-- Diese Liste stand vorher auf englischen Werten (lunch, dinner …),
                                 während das Anlegen-Formular deutsche schreibt. Dadurch war nie
                                 etwas vorausgewählt und Speichern hat die Kategorie geleert. --}}
                            <select class="input" id="meal_type" name="meal_type">
                                <option value="">Bitte auswählen</option>
                                @foreach(['Frühstück', 'Mittagessen', 'Abendessen', 'Snack', 'Dip', 'Dessert', 'Getränk', 'Beilage', 'Sonstiges'] as $option)
                                    <option value="{{ $option }}" @selected(old('meal_type', $recipe->meal_type) === $option)>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group full">
                            <label for="kcal">Kalorien optional</label>
                            <input class="input" type="number" id="kcal" name="kcal" min="0"
                                value="{{ old('kcal', $recipe->kcal) }}">
                            <p class="field-hint">
                                Wird beim Speichern aus den Zutaten neu berechnet.
                            </p>
                        </div>
                    </div>
                </section>

                <section class="card panel">
                    <h2>Zutaten</h2>

                    <p class="field-hint">
                        Die letzte Zeile ist frei für eine weitere Zutat. Leere Zeilen werden ignoriert.
                    </p>

                    <div id="ingredients-wrapper" class="ingredients-wrapper">
                        @foreach($recipe->ingredients as $index => $ingredient)
                            <div class="ingredients-row">
                                <input class="input ingredient-name" type="text" name="ingredients[{{ $index }}][name]"
                                    value="{{ old("ingredients.$index.name", $ingredient->name) }}" placeholder="Zutat"
                                    list="nutrition-items-list">

                                <input class="input" type="text" name="ingredients[{{ $index }}][amount]"
                                    value="{{ old("ingredients.$index.amount", $ingredient->amount) }}"
                                    placeholder="Menge">

                                <select class="input" name="ingredients[{{ $index }}][unit]">
                                    <option value="">Einheit</option>
                                    @foreach(['g', 'kg', 'ml', 'l', 'TL', 'EL', 'Prise', 'Stück', 'Dose', 'Packung'] as $unit)
                                        <option value="{{ $unit }}" @selected(old("ingredients.$index.unit", $ingredient->unit) === $unit)>
                                            {{ $unit }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach

                        {{-- Leere Zeile für eine neue Zutat --}}
                        <div class="ingredients-row">
                            <input class="input ingredient-name" type="text"
                                name="ingredients[{{ $recipe->ingredients->count() }}][name]" placeholder="Zutat"
                                list="nutrition-items-list">

                            <input class="input" type="text"
                                name="ingredients[{{ $recipe->ingredients->count() }}][amount]" placeholder="Menge">

                            <select class="input" name="ingredients[{{ $recipe->ingredients->count() }}][unit]">
                                <option value="">Einheit</option>
                                @foreach(['g', 'kg', 'ml', 'l', 'TL', 'EL', 'Prise', 'Stück', 'Dose', 'Packung'] as $unit)
                                    <option value="{{ $unit }}">{{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <datalist id="nutrition-items-list">
                        @foreach($nutritionItems as $item)
                            <option value="{{ $item->name }}"></option>
                        @endforeach
                    </datalist>
                </section>

                <section class="card panel">
                    <h2>Zubereitung</h2>

                    <div class="form-group">
                        <label for="instructions">Schritte</label>
                        <textarea class="input" id="instructions" name="instructions"
                            rows="8">{{ old('instructions', $recipe->instructions) }}</textarea>
                        <p class="field-hint">
                            Eine Leerzeile zwischen den Schritten – daraus werden die
                            einzelnen Schritt-Karten.
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notizen</label>
                        <textarea class="input" id="notes" name="notes"
                            rows="3">{{ old('notes', $recipe->notes) }}</textarea>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="source_url">Quelle / Link optional</label>
                            <input class="input" type="url" id="source_url" name="source_url"
                                value="{{ old('source_url', $recipe->source_url) }}">
                        </div>

                        <div class="form-group">
                            <label for="photo">Foto ändern</label>
                            <input class="input input-file" type="file" id="photo" name="photo" accept="image/*">
                            <p class="field-hint">
                                Leer lassen, um das bisherige Foto zu behalten.
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            <div class="footer-actions">
                <button type="submit" class="btn">Änderungen speichern</button>

                <a class="btn btn-secondary" href="{{ route('recipes.show', $recipe) }}">Abbrechen</a>
            </div>
        </form>
    </main>
</body>

</html>
