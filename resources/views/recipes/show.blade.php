@php
    $canManage = auth()->id() === $recipe->user_id || auth()->user()->is_admin;

    $servings = $recipe->servings ?: 1;

    // Zubereitung an Leerzeilen in einzelne Schritte trennen.
    $steps = $recipe->instructions
        ? array_values(array_filter(
            array_map('trim', preg_split('/\R\s*\R/u', trim($recipe->instructions))),
            fn ($step) => $step !== ''
        ))
        : [];

    // Emoji je Zutat. Nach Schluessellaenge absteigend geprueft, damit
    // "Fleisch" nicht am kuerzeren "ei" haengen bleibt.
    $iconMap = [
        'nudel' => '🍝', 'pasta' => '🍝', 'spaghetti' => '🍝',
        'reis' => '🍚', 'mehl' => '🌾', 'zucker' => '🍬',
        'kartoffel' => '🥔', 'pommes' => '🍟',
        'hähnchen' => '🍗', 'huhn' => '🍗', 'pute' => '🍗',
        'gyros' => '🥩', 'hack' => '🥩', 'fleisch' => '🥩', 'rind' => '🥩', 'schwein' => '🥩',
        'lachs' => '🐟', 'fisch' => '🐟', 'garnele' => '🦐',
        'gouda' => '🧀', 'käse' => '🧀', 'feta' => '🧀', 'parmesan' => '🧀',
        'sahne' => '🥛', 'milch' => '🥛', 'schmand' => '🥛', 'joghurt' => '🥛', 'quark' => '🥛',
        'butter' => '🧈', 'hollandaise' => '🥣', 'sauce' => '🥣', 'soße' => '🥣',
        'zwiebel' => '🧅', 'knoblauch' => '🧄', 'paprika' => '🫑', 'tomate' => '🍅',
        'gurke' => '🥒', 'spinat' => '🥬', 'salat' => '🥬', 'kohl' => '🥬',
        'möhre' => '🥕', 'karotte' => '🥕', 'mais' => '🌽', 'brokkoli' => '🥦',
        'champignon' => '🍄', 'pilz' => '🍄',
        'apfel' => '🍎', 'banane' => '🍌', 'zitrone' => '🍋', 'beere' => '🫐',
        'brot' => '🍞', 'baguette' => '🥖', 'schokolade' => '🍫', 'honig' => '🍯',
        'gewürz' => '🧂', 'salz' => '🧂', 'pfeffer' => '🧂',
        'olive' => '🫒', 'öl' => '🫒', 'ei' => '🥚',
    ];

    uksort($iconMap, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));

    $iconFor = function (string $name) use ($iconMap) {
        $needle = mb_strtolower($name);

        foreach ($iconMap as $key => $icon) {
            if (str_contains($needle, $key)) {
                return $icon;
            }
        }

        return '🥘';
    };
@endphp
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $recipe->title }} | Meine Rezepte</title>

    @include('partials.theme-init')

    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/recipes-show.css') }}">
</head>

<body>

    <x-page-hero :back-url="route('recipes.index')" back-label="Zurück zu allen Rezepten"
        :photo="$recipe->photo_path ? asset('storage/'.$recipe->photo_path) : null" :alt="$recipe->title">

        @if($canManage)
            <x-slot:actions>
                <a class="hero-btn" href="{{ route('recipes.edit', $recipe) }}" aria-label="Rezept bearbeiten">
                    @include('partials.icons.pencil')
                </a>
            </x-slot:actions>
        @endif
    </x-page-hero>

    <main class="container page-main">

        <x-page-card :title="$recipe->title" :badge="$recipe->meal_type"
            :subtitle="$recipe->ingredients->count().' '.($recipe->ingredients->count() === 1 ? 'Zutat' : 'Zutaten').' · von '.($recipe->user->name ?? 'Unbekannt')">

            <div class="stat-row">
                <x-stat icon="clock" :value="$recipe->duration_minutes ? $recipe->duration_minutes.' Min' : '–'"
                    label="Dauer" />

                <x-stat icon="flame" :value="round($recipe->total_kcal / $servings)" label="kcal / Portion" />

                <x-stat icon="servings" :value="$recipe->servings ?? '–'"
                    :label="$recipe->servings == 1 ? 'Portion' : 'Portionen'" />
            </div>

            @if($recipe->description)
                <p class="page-lead">{{ $recipe->description }}</p>
            @endif

            @if($recipe->difficulty || $recipe->price_level || $recipe->diet_type)
                <div class="page-tags">
                    @if($recipe->difficulty)
                        <span class="badge">Aufwand <strong>{{ $recipe->difficulty_label }}</strong></span>
                    @endif

                    @if($recipe->price_level)
                        <span class="badge">Preis <strong>{{ $recipe->price_level }}</strong></span>
                    @endif

                    @if($recipe->diet_type)
                        <span class="badge">Ernährung <strong>{{ $recipe->diet_label }}</strong></span>
                    @endif
                </div>
            @endif
        </x-page-card>

        <div class="content-grid content-grid-split">

            {{-- Mobil zuerst (wie im Entwurf), auf dem Desktop rechts --}}
            <div class="content-col content-col-side">

                <section class="card panel">
                    <h2>Zutaten</h2>

                    @if($recipe->ingredients->isEmpty())
                        <p class="panel-text">Keine Zutaten hinterlegt.</p>
                    @else
                        <ul class="ingredients">
                            @foreach($recipe->ingredients as $ingredient)
                                <li class="ingredient">
                                    <span class="ingredient-thumb"
                                        style="--tile: var(--tile-{{ $loop->iteration % 6 ?: 6 }})"
                                        aria-hidden="true">{{ $iconFor($ingredient->name) }}</span>

                                    <span class="ingredient-title">{{ $ingredient->name }}</span>

                                    <span class="ingredient-amount">
                                        {{ trim($ingredient->amount.' '.$ingredient->unit) ?: 'nach Geschmack' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <section class="card panel">
                    <h2>Nährwerte pro Portion</h2>

                    <div class="nutrition-grid">
                        <div class="nutrition-item">
                            <span class="nutrition-label">🔥 Kalorien</span>
                            <strong>{{ round($recipe->total_kcal / $servings) }} kcal</strong>
                        </div>

                        <div class="nutrition-item">
                            <span class="nutrition-label">💪 Protein</span>
                            <strong>{{ round($recipe->total_protein / $servings, 1) }} g</strong>
                        </div>

                        <div class="nutrition-item">
                            <span class="nutrition-label">🍞 Kohlenhydrate</span>
                            <strong>{{ round($recipe->total_carbs / $servings, 1) }} g</strong>
                        </div>

                        <div class="nutrition-item">
                            <span class="nutrition-label">🥑 Fett</span>
                            <strong>{{ round($recipe->total_fat / $servings, 1) }} g</strong>
                        </div>
                    </div>

                    @if($recipe->servings)
                        <p class="nutrition-note">
                            Gesamt {{ round($recipe->total_kcal) }} kcal, berechnet für
                            {{ $recipe->servings }} {{ $recipe->servings == 1 ? 'Portion' : 'Portionen' }}.
                        </p>
                    @endif
                </section>
            </div>

            <div class="content-col content-col-main">

                @if(! empty($steps))
                    <section class="card panel">
                        <h2>Zubereitung</h2>

                        <ol class="steps">
                            @foreach($steps as $step)
                                <li class="step">
                                    <span class="step-label">Schritt {{ $loop->iteration }}</span>
                                    <p>{{ $step }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </section>
                @endif

                @if($recipe->notes)
                    <section class="card panel">
                        <h2>Notizen</h2>
                        <p class="panel-text">{{ $recipe->notes }}</p>
                    </section>
                @endif

                @if($recipe->source_url)
                    <section class="card panel">
                        <h2>Quelle</h2>
                        <a class="panel-link" href="{{ $recipe->source_url }}" target="_blank"
                            rel="noopener noreferrer">{{ $recipe->source_url }}</a>
                    </section>
                @endif
            </div>
        </div>

        @if($canManage)
            <div class="footer-actions">
                <a href="{{ route('recipes.edit', $recipe) }}" class="btn">Rezept bearbeiten</a>

                <form action="{{ route('recipes.destroy', $recipe) }}" method="POST"
                    onsubmit="return confirm('Möchtest du dieses Rezept wirklich löschen?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">Rezept löschen</button>
                </form>
            </div>
        @endif
    </main>
</body>

</html>
