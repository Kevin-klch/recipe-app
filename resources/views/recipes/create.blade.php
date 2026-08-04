<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezept hinzufügen | Meine Rezepte</title>

    @include('partials.theme-init')

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-form.css') }}">
</head>

<body>

    <x-page-hero :back-url="route('home')" back-label="Zurück zur Startseite" symbol="🥣" />

    <main class="container page-main">

        <x-page-card title="Neues Rezept" subtitle="Trage hier alle Informationen zu deinem Rezept ein." />

        <form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="content-grid">

                <section class="card panel">
                    <h2>Grunddaten</h2>

                    <div class="form-group">
                        <label for="title">Titel</label>
                        <input class="input" type="text" id="title" name="title" value="{{ old('title') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Beschreibung</label>
                        <textarea class="input" id="description" name="description"
                            rows="3">{{ old('description') }}</textarea>
                    </div>
                </section>

                <section class="card panel">
                    <h2>Angaben</h2>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="duration_minutes">Dauer in Minuten</label>
                            <input class="input" type="number" id="duration_minutes" name="duration_minutes" min="0"
                                value="{{ old('duration_minutes') }}">
                        </div>

                        <div class="form-group">
                            <label for="servings">Portionen</label>
                            <input class="input" type="number" id="servings" name="servings" min="1"
                                value="{{ old('servings') }}">
                        </div>

                        <div class="form-group">
                            <label for="difficulty">Aufwand</label>
                            <select class="input" id="difficulty" name="difficulty">
                                <option value="">Bitte auswählen</option>
                                <option value="easy" @selected(old('difficulty') === 'easy')>Einfach</option>
                                <option value="medium" @selected(old('difficulty') === 'medium')>Mittel</option>
                                <option value="hard" @selected(old('difficulty') === 'hard')>Aufwendig</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price_level">Preisschätzung</label>
                            <input class="input" type="text" id="price_level" name="price_level"
                                placeholder="z.B. ca. 8 € oder günstig" value="{{ old('price_level') }}">
                        </div>

                        <div class="form-group">
                            <label for="diet_type">Ernährungsart</label>
                            <select class="input" id="diet_type" name="diet_type">
                                <option value="none" @selected(old('diet_type', 'none') === 'none')>Normal</option>
                                <option value="vegetarian" @selected(old('diet_type') === 'vegetarian')>Vegetarisch</option>
                                <option value="vegan" @selected(old('diet_type') === 'vegan')>Vegan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="meal_type">Kategorie</label>
                            <select class="input" id="meal_type" name="meal_type">
                                <option value="">Bitte auswählen</option>
                                @foreach(['Frühstück', 'Mittagessen', 'Abendessen', 'Snack', 'Dip', 'Dessert', 'Getränk', 'Beilage', 'Sonstiges'] as $option)
                                    <option value="{{ $option }}" @selected(old('meal_type') === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group full">
                            <label for="kcal">Kalorien optional</label>
                            <input class="input" type="number" id="kcal" name="kcal" min="0" value="{{ old('kcal') }}">
                            <p class="field-hint">
                                Wird normalerweise aus den Zutaten berechnet und muss nicht ausgefüllt werden.
                            </p>
                        </div>
                    </div>
                </section>

                <section class="card panel">
                    <h2>Zutaten</h2>

                    <p class="field-hint">
                        Eine neue Zeile erscheint automatisch, sobald du eine Zutat einträgst.
                    </p>

                    <div id="ingredients-wrapper" class="ingredients-wrapper">
                        <div class="ingredients-row">
                            <input class="input ingredient-name" type="text" name="ingredients[0][name]"
                                placeholder="Zutat" list="nutrition-items-list">

                            <input class="input" type="text" name="ingredients[0][amount]" placeholder="Menge">

                            <select class="input" name="ingredients[0][unit]">
                                <option value="">Einheit</option>
                                @foreach(['g', 'kg', 'ml', 'l', 'TL', 'EL', 'Prise', 'Stück', 'Dose', 'Packung'] as $unit)
                                    <option value="{{ $unit }}">{{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Vorschlagsliste für die Zutatennamen. Fehlte bisher, obwohl
                         die Felder per list="…" darauf verweisen. --}}
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
                            rows="8">{{ old('instructions') }}</textarea>
                        <p class="field-hint">
                            Eine Leerzeile zwischen den Schritten – daraus werden später die
                            einzelnen Schritt-Karten.
                        </p>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notizen</label>
                        <textarea class="input" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="source_url">Quelle / Link optional</label>
                            <input class="input" type="url" id="source_url" name="source_url"
                                value="{{ old('source_url') }}">
                        </div>

                        <div class="form-group">
                            <label for="photo">Foto</label>
                            <input class="input input-file" type="file" id="photo" name="photo" accept="image/*">
                        </div>
                    </div>
                </section>
            </div>

            <div class="footer-actions">
                <button type="submit" class="btn">Rezept speichern</button>

                <a class="btn btn-secondary" href="{{ route('home') }}">Abbrechen</a>
            </div>
        </form>
    </main>

    <script>
        const wrapper = document.getElementById('ingredients-wrapper');

        const units = ['', 'g', 'kg', 'ml', 'l', 'TL', 'EL', 'Prise', 'Stück', 'Dose', 'Packung'];

        function createIngredientRow(index) {
            const row = document.createElement('div');
            row.classList.add('ingredients-row');

            row.innerHTML = `
                <input class="input ingredient-name"
                       type="text"
                       name="ingredients[${index}][name]"
                       placeholder="Zutat"
                       list="nutrition-items-list">

                <input class="input"
                       type="text"
                       name="ingredients[${index}][amount]"
                       placeholder="Menge">

                <select class="input" name="ingredients[${index}][unit]">
                    ${units.map(unit => `
                        <option value="${unit}">${unit === '' ? 'Einheit' : unit}</option>
                    `).join('')}
                </select>
            `;

            return row;
        }

        wrapper.addEventListener('input', function () {
            const rows = wrapper.querySelectorAll('.ingredients-row');
            const lastRow = rows[rows.length - 1];
            const lastNameInput = lastRow.querySelector('.ingredient-name');

            if (lastNameInput.value.trim() !== '') {
                wrapper.appendChild(createIngredientRow(rows.length));
            }
        });
    </script>
</body>

</html>
