<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Nährwerte ergänzen</title>

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-create.css') }}">
</head>
<body>
<main class="container">
    <a class="back-link" href="{{ route('recipes.create') }}">
        ← Zurück zum Rezept
    </a>

    <form class="card form-card"
          method="POST"
          action="{{ route('nutrition.storeMissing') }}">
        @csrf

        <span class="header">Nährwerte ergänzen</span>
        <p>
            Für diese Zutaten fehlen noch Nährwerte.
            Gib an, worauf sich die Werte beziehen.
        </p>

        <div class="form-grid">
            @foreach($items as $index => $name)
                <div class="form-group full">
                    <h3>{{ $name }}</h3>

                    <input type="hidden"
                           name="items[{{ $index }}][name]"
                           value="{{ $name }}">

                    <div class="ingredients-row nutrition-row">
                        <input class="input"
                               type="number"
                               step="0.01"
                               name="items[{{ $index }}][reference_amount]"
                               placeholder="Menge z.B. 100 oder 1"
                               required>

                        <select class="input"
                                name="items[{{ $index }}][reference_unit]"
                                required>
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

                        <input class="input"
                               type="number"
                               step="0.01"
                               name="items[{{ $index }}][kcal]"
                               placeholder="kcal"
                               required>
                    </div>

                    <div class="ingredients-row nutrition-row">
                        <input class="input"
                               type="number"
                               step="0.01"
                               name="items[{{ $index }}][protein]"
                               placeholder="Protein"
                               required>

                        <input class="input"
                               type="number"
                               step="0.01"
                               name="items[{{ $index }}][carbs]"
                               placeholder="Kohlenhydrate"
                               required>

                        <input class="input"
                               type="number"
                               step="0.01"
                               name="items[{{ $index }}][fat]"
                               placeholder="Fett"
                               required>
                    </div>

                    <p style="margin-top: 8px; color: var(--muted); font-size: 14px;">
                        Beispiel: Ei = 1 Stück = 78 kcal oder Reis = 100 g = 350 kcal
                    </p>
                </div>
            @endforeach
        </div>

        <div class="form-actions">
            <a href="{{ route('recipes.create') }}" class="btn btn-secondary">
                Abbrechen
            </a>

            <button type="submit" class="btn">
                Nährwerte speichern
            </button>
        </div>
    </form>
</main>
</body>
</html>