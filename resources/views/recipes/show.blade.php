@php
    $canManage = auth()->id() === $recipe->user_id || auth()->user()->is_admin;

    $servings = $recipe->servings ?: 1;

    $difficultyLabels = ['easy' => 'Einfach', 'medium' => 'Mittel', 'hard' => 'Aufwendig'];
    $dietLabels = ['none' => 'Normal', 'vegetarian' => 'Vegetarisch', 'vegan' => 'Vegan'];

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
    <link rel="stylesheet" href="{{ asset('css/recipes-show.css') }}">
</head>

<body>

    <header class="detail-hero{{ $recipe->photo_path ? '' : ' detail-hero-no-photo' }}">
        @if($recipe->photo_path)
            <img src="{{ asset('storage/'.$recipe->photo_path) }}" alt="{{ $recipe->title }}">
        @else
            <div class="detail-hero-empty" aria-hidden="true">🍽️</div>
        @endif

        <div class="hero-bar">
            <a class="hero-btn" href="{{ route('recipes.index') }}" aria-label="Zurück zu allen Rezepten">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 5l-7 7 7 7" />
                </svg>
            </a>

            <div class="hero-bar-right">
                @include('partials.theme-toggle')

                @if($canManage)
                    <a class="hero-btn" href="{{ route('recipes.edit', $recipe) }}" aria-label="Rezept bearbeiten">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16v4Z" />
                            <path d="M14.5 5.5 18.5 9.5" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <main class="container detail-main">

        <section class="card detail-card">

            @if(session('success'))
                <div class="success-message">{{ session('success') }}</div>
            @endif

            @if($recipe->meal_type)
                <span class="badge detail-badge">{{ $recipe->meal_type }}</span>
            @endif

            <h1 class="detail-title">{{ $recipe->title }}</h1>

            <p class="detail-subtitle">
                {{ $recipe->ingredients->count() }}
                {{ $recipe->ingredients->count() === 1 ? 'Zutat' : 'Zutaten' }}
                · von {{ $recipe->user->name ?? 'Unbekannt' }}
            </p>

            <div class="stat-row">
                <div class="stat">
                    <span class="stat-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3.5 2" />
                        </svg>
                    </span>
                    <span class="stat-text">
                        <span class="stat-value">
                            {{ $recipe->duration_minutes ? $recipe->duration_minutes.' Min' : '–' }}
                        </span>
                        <span class="stat-label">Dauer</span>
                    </span>
                </div>

                <div class="stat">
                    <span class="stat-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13 2c.4 3.4 3.8 4.9 3.8 8.6A4.8 4.8 0 0 1 7.2 11c0-1.5.6-2.8 1.5-3.8.4 1.6 1.4 2.2 2 1.8.9-.7 1.9-3 2.3-7Z" />
                            <path d="M12 21a3 3 0 0 0 3-3c0-1.9-3-3.6-3-3.6S9 16.1 9 18a3 3 0 0 0 3 3Z" />
                        </svg>
                    </span>
                    <span class="stat-text">
                        <span class="stat-value">{{ round($recipe->total_kcal / $servings) }}</span>
                        <span class="stat-label">kcal / Portion</span>
                    </span>
                </div>

                <div class="stat">
                    <span class="stat-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="3.2" />
                            <path d="M5.5 20c0-3.6 2.9-6.5 6.5-6.5s6.5 2.9 6.5 6.5" />
                        </svg>
                    </span>
                    <span class="stat-text">
                        <span class="stat-value">{{ $recipe->servings ?? '–' }}</span>
                        <span class="stat-label">
                            {{ $recipe->servings == 1 ? 'Portion' : 'Portionen' }}
                        </span>
                    </span>
                </div>
            </div>

            @if($recipe->description)
                <p class="detail-description">{{ $recipe->description }}</p>
            @endif

            @if($recipe->difficulty || $recipe->price_level || $recipe->diet_type)
                <div class="detail-tags">
                    @if($recipe->difficulty)
                        <span class="badge">
                            Aufwand
                            <strong>{{ $difficultyLabels[$recipe->difficulty] ?? $recipe->difficulty }}</strong>
                        </span>
                    @endif

                    @if($recipe->price_level)
                        <span class="badge">Preis <strong>{{ $recipe->price_level }}</strong></span>
                    @endif

                    @if($recipe->diet_type)
                        <span class="badge">
                            Ernährung
                            <strong>{{ $dietLabels[$recipe->diet_type] ?? $recipe->diet_type }}</strong>
                        </span>
                    @endif
                </div>
            @endif
        </section>

        <div class="detail-grid">

            {{-- Mobil zuerst (wie im Entwurf), auf dem Desktop rechts --}}
            <div class="detail-col detail-col-side">

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

            <div class="detail-col detail-col-main">

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
                        <a class="source-link" href="{{ $recipe->source_url }}" target="_blank"
                            rel="noopener noreferrer">{{ $recipe->source_url }}</a>
                    </section>
                @endif
            </div>
        </div>

        @if($canManage)
            <div class="recipe-actions">
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
