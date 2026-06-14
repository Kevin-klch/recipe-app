<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezept hinzufügen</title>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-create.css') }}">
</head>

<body>
    <main class="container">
        <a class="back-link" href="{{ route('home') }}">
            ← Zurück zur Startseite
        </a>

        <form class="card form-card" action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data">

            @csrf

            <span class="header">Neues Rezept hinzufügen</span>
            <p>Trage hier alle Informationen zu deinem Rezept ein.</p>

            <div>
                <label for="title">Titel</label><br>
                <input class="input" type="text" id="title" name="title" required>
            </div>

            <br>

            <div>
                <label for="description">Beschreibung</label><br>
                <textarea class="input" id="description" name="description" rows="4"></textarea>
            </div>

            <br>

            <div>
                <label for="duration_minutes">Dauer in Minuten</label><br>
                <input class="input" type="number" id="duration_minutes" name="duration_minutes" min="0">
            </div>

            <br>

            <div>
                <label for="difficulty">Aufwand / Schwierigkeit</label><br>
                <select class="input" id="difficulty" name="difficulty">
                    <option value="">Bitte auswählen</option>
                    <option value="easy">Einfach</option>
                    <option value="medium">Mittel</option>
                    <option value="hard">Aufwendig</option>
                </select>
            </div>

            <br>

            <div>
                <label for="price_level">Preisschätzung</label><br>
                <input class="input" type="text" id="price_level" name="price_level"
                    placeholder="z.B. ca. 8 € oder günstig" value="{{ old('price_level') }}">
            </div>

            <br>

            <div>
                <label for="servings">Menge / Portionen</label><br>
                <input class="input" type="number" id="servings" name="servings" min="1">
            </div>

            <br>

            <div>
                <label for="kcal">Kcal optional</label><br>
                <input class="input" type="number" id="kcal" name="kcal" min="0">
            </div>

            <br>

            <div>
                <label for="diet_type">Ernährungsart</label><br>
                <select class="input" id="diet_type" name="diet_type">
                    <option value="none">Normal</option>
                    <option value="vegetarian">Vegetarisch</option>
                    <option value="vegan">Vegan</option>
                </select>
            </div>

            <br>

            <div>
                <label for="meal_type">Kategorie</label><br>
                <select class="input" id="meal_type" name="meal_type">
                    <option value="">Bitte auswählen</option>
                    <option value="Frühstück">Frühstück</option>
                    <option value="Mittagessen">Mittagessen</option>
                    <option value="Abendessen">Abendessen</option>
                    <option value="Snack">Snack</option>
                    <option value="Dip">Dip</option>
                    <option value="Dessert">Dessert</option>
                    <option value="Getränk">Getränk</option>
                    <option value="Beilage">Beilage</option>
                    <option value="Sonstiges">Sonstiges</option>
                </select>
            </div>

            <br>

            <div class="form-group full">
                <label>Zutaten</label>

                <div id="ingredients-wrapper">
                    <div class="ingredients-row">
                        <input class="input ingredient-name" type="text" name="ingredients[0][name]"
                            placeholder="Zutat">

                        <input class="input" type="text" name="ingredients[0][amount]" placeholder="Menge">

                        <select class="input" name="ingredients[0][unit]">
                            <option value="">Einheit</option>
                            <option value="g">g</option>
                            <option value="kg">kg</option>
                            <option value="ml">ml</option>
                            <option value="l">l</option>
                            <option value="TL">TL</option>
                            <option value="EL">EL</option>
                            <option value="Prise">Prise</option>
                            <option value="Stück">Stück</option>
                            <option value="Dose">Dose</option>
                            <option value="Packung">Packung</option>
                        </select>
                    </div>
                </div>
            </div>

            <br>

            <div>
                <label for="instructions">Zubereitung</label><br>
                <textarea id="instructions" name="instructions" rows="8"></textarea>
            </div>

            <br>

            <div>
                <label for="notes">Notizen</label><br>
                <textarea id="notes" name="notes" rows="4"></textarea>
            </div>

            <br>

            <div>
                <label for="source_url">Quelle / Link optional</label><br>
                <input type="url" id="source_url" name="source_url">
            </div>

            <br>

            <div>
                <label for="photo">Foto hinzufügen</label><br>
                <input type="file" id="photo" name="photo" accept="image/*">
            </div>

            <br>

            <button type="submit">Rezept speichern</button>
        </form>

        <br>
    </main>

    <script>
        const wrapper = document.getElementById('ingredients-wrapper');

        const units = [
            '',
            'g',
            'kg',
            'ml',
            'l',
            'TL',
            'EL',
            'Prise',
            'Stück',
            'Dose',
            'Packung'
        ];

        function createIngredientRow(index) {
            const row = document.createElement('div');
            row.classList.add('ingredients-row');

            row.innerHTML = `
                <input class="input ingredient-name"
                       type="text"
                       name="ingredients[${index}][name]"
                       placeholder="Zutat">
    
                <input class="input"
                       type="text"
                       name="ingredients[${index}][amount]"
                       placeholder="Menge">
    
                <select class="input" name="ingredients[${index}][unit]">
                    ${units.map(unit => `
                        <option value="${unit}">
                            ${unit === '' ? 'Einheit' : unit}
                        </option>
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