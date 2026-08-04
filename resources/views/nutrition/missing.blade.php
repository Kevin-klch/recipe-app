<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nährwerte ergänzen | Meine Rezepte</title>

    @include('partials.theme-init')

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-form.css') }}">
</head>

<body>

    <x-page-hero :back-url="route('recipes.create')" back-label="Zurück zum Rezept" symbol="🧾" />

    <main class="container page-main">

        <x-page-card title="Nährwerte ergänzen"
            :subtitle="count($items).' '.(count($items) === 1 ? 'Zutat ist' : 'Zutaten sind').' noch unbekannt'">

            <p class="page-lead">
                Damit die Nährwerte des Rezepts berechnet werden können, fehlen noch diese
                Angaben. Gib jeweils an, worauf sich die Werte beziehen.
            </p>
        </x-page-card>

        <form method="POST" action="{{ route('nutrition.storeMissing') }}">
            @csrf

            <div class="content-grid">
                <section class="card panel">
                    <h2>Fehlende Angaben</h2>

                    <p class="field-hint">
                        Beispiel: Ei = 1 Stück = 78 kcal, oder Reis = 100 g = 350 kcal.
                        Die Einheit sollte zu der passen, die du im Rezept verwendest.
                    </p>

                    @foreach($items as $index => $name)
                        <div class="nutrition-item-card">
                            <h3>{{ $name }}</h3>

                            <input type="hidden" name="items[{{ $index }}][name]" value="{{ $name }}">

                            <div class="ingredients-row">
                                <input class="input" type="number" step="0.01"
                                    name="items[{{ $index }}][reference_amount]"
                                    value="{{ old("items.$index.reference_amount") }}"
                                    placeholder="Menge z.B. 100 oder 1" required>

                                <select class="input" name="items[{{ $index }}][reference_unit]" required>
                                    <option value="">Einheit</option>
                                    @foreach(['g', 'kg', 'ml', 'l', 'TL', 'EL', 'Prise', 'Stück', 'Dose', 'Packung'] as $unit)
                                        <option value="{{ $unit }}" @selected(old("items.$index.reference_unit") === $unit)>
                                            {{ $unit }}
                                        </option>
                                    @endforeach
                                </select>

                                <input class="input" type="number" step="0.01" name="items[{{ $index }}][kcal]"
                                    value="{{ old("items.$index.kcal") }}" placeholder="kcal" required>
                            </div>

                            <div class="ingredients-row">
                                <input class="input" type="number" step="0.01" name="items[{{ $index }}][protein]"
                                    value="{{ old("items.$index.protein") }}" placeholder="Protein in g" required>

                                <input class="input" type="number" step="0.01" name="items[{{ $index }}][carbs]"
                                    value="{{ old("items.$index.carbs") }}" placeholder="Kohlenhydrate in g" required>

                                <input class="input" type="number" step="0.01" name="items[{{ $index }}][fat]"
                                    value="{{ old("items.$index.fat") }}" placeholder="Fett in g" required>
                            </div>
                        </div>
                    @endforeach
                </section>
            </div>

            <div class="footer-actions">
                <button type="submit" class="btn">Nährwerte speichern</button>

                <a class="btn btn-secondary" href="{{ route('recipes.create') }}">Abbrechen</a>
            </div>
        </form>
    </main>
</body>

</html>
